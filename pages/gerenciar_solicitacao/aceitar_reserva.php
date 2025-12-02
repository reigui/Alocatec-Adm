<?php
require_once '../../database/conexao_bd_mysql.php';

if (!isset($_GET['id'])) {
    die("ID da reserva não informado.");
}

$id = intval($_GET['id']);

$sql = "UPDATE reserva SET status = 'concluída' WHERE id_reserva = $id";

if (mysqli_query($conexao_servidor_bd, $sql)) {
    header("Location: gerenciar.php?id=$id&msg=aceita");
    exit();
} else {
    echo "Erro ao atualizar status.";
}