<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../database/conexao_bd_mysql.php';

if (!isset($_GET['id_estabelecimento'])) {
    $_SESSION['mensagem_instalacao'] = [
        'tipo' => 'erro',
        'texto' => 'Estabelecimento não especificado.'
    ];
    header("Location: ../instalacoes/instalacoes.php");
    exit();
}

$id_estabelecimento = intval($_GET['id_estabelecimento']);

mysqli_begin_transaction($conexao_servidor_bd);

try {
    // 1️⃣ Apagar reservas vinculadas aos espaços deste estabelecimento
    $sql_reservas = "
        DELETE R FROM reserva R
        INNER JOIN espaco E ON R.id_espaco = E.id_espaco
        WHERE E.id_estabelecimento = $id_estabelecimento
    ";
    if (!mysqli_query($conexao_servidor_bd, $sql_reservas)) {
        throw new Exception("Erro ao apagar reservas: " . mysqli_error($conexao_servidor_bd));
    }

    // 2️⃣ Apagar os espaços do estabelecimento
    $sql_espaco = "DELETE FROM espaco WHERE id_estabelecimento = $id_estabelecimento";
    if (!mysqli_query($conexao_servidor_bd, $sql_espaco)) {
        throw new Exception("Erro ao apagar espaço: " . mysqli_error($conexao_servidor_bd));
    }

    // 3️⃣ Apagar o estabelecimento
    $sql_estab = "DELETE FROM estabelecimento WHERE id_estabelecimento = $id_estabelecimento";
    if (!mysqli_query($conexao_servidor_bd, $sql_estab)) {
        throw new Exception("Erro ao apagar estabelecimento: " . mysqli_error($conexao_servidor_bd));
    }

    mysqli_commit($conexao_servidor_bd);

    $_SESSION['mensagem_apagar'] = [
        'tipo' => 'sucesso',
        'texto' => 'Instalação apagada com sucesso!'
    ];

} catch (Exception $e) {
    mysqli_rollback($conexao_servidor_bd);
    $_SESSION['mensagem_apagar'] = [
        'tipo' => 'erro',
        'texto' => 'Erro ao apagar instalação: ' . $e->getMessage()
    ];
}

mysqli_close($conexao_servidor_bd);

header("Location: ../instalacoes/instalacoes.php");
exit();
?>
