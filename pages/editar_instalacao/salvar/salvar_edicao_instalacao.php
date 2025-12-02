<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../../database/conexao_bd_mysql.php';

$id_estabelecimento = intval($_POST['id_estabelecimento']);
$id_espaco          = intval($_POST['id_espaco']);
$nome               = mysqli_real_escape_string($conexao_servidor_bd, $_POST['nome']);
$tipo               = mysqli_real_escape_string($conexao_servidor_bd, $_POST['tipo']);
$capacidade         = intval($_POST['capacidade']);
$cobertura          = mysqli_real_escape_string($conexao_servidor_bd, $_POST['cobertura']);
$status             = mysqli_real_escape_string($conexao_servidor_bd, $_POST['status']);
$inicio             = mysqli_real_escape_string($conexao_servidor_bd, $_POST['inicio']);
$termino            = mysqli_real_escape_string($conexao_servidor_bd, $_POST['termino']);
$disponibilidade    = mysqli_real_escape_string($conexao_servidor_bd, $_POST['disponibilidade']);

// Atualiza estabelecimento
$sql1 = "
UPDATE estabelecimento 
SET nome_est='$nome',
    tipo='$tipo',
    inicio='$inicio',
    termino='$termino',
    disponibilidade='$disponibilidade',
    status='$status'
WHERE id_estabelecimento = $id_estabelecimento;
";

// Atualiza espaço (corrigido: removida vírgula inicial)
$sql2 = "
UPDATE espaco
SET capacidade = $capacidade,
    cobertura = '$cobertura'
WHERE id_espaco = $id_espaco;
";

// Executa os dois updates
if (mysqli_query($conexao_servidor_bd, $sql1) && mysqli_query($conexao_servidor_bd, $sql2)) {
    $_SESSION['mensagem'] = [
        'tipo' => 'sucesso',
        'texto' => 'Instalação atualizada com sucesso!'
    ];
} else {
    $_SESSION['mensagem'] = [
        'tipo' => 'erro',
        'texto' => 'Erro ao atualizar instalação: ' . mysqli_error($conexao_servidor_bd)
    ];
}

header("Location: ../../detalhes_instalacoes/instalacao.php?id_estabelecimento=$id_estabelecimento");
exit();
?>
