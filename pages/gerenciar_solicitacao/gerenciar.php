<?php
require_once '../../login/login.php';

if (!Store::isLogged()) {
    header("Location: ../../index.php");
    exit();
}

$usuario = Store::get('usuario');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Solicitação - Alocatec</title>
    <link rel="stylesheet" href="gerenciar.css">
    <link rel="icon" href="img/logo.png">
  <style>
    .resuldado-Sucesso{
  width: 100%;
  max-width: 900px;
  margin: 10px auto;
  padding: 20px 10px;
  border-radius: 10px;
  font-size: 15px;
  background-color: #e6ffee;
  border-left: 6px solid #00c853;
  color: #007a33;
  font-size: 1rem;
  font-weight: 600;
}
</style>
</head>
<body>

<div class="container">

    <!-- MENU LATERAL -->
    <aside class="sidebar">

        <div class="logo">
            <div class="icone-logo">
                <img src="./img/logo.png" alt="Logo">
            </div>
            <h2>ALOCATEC</h2>
            <br>
            <hr>
        </div>

        <nav>
            <ul>
                <li><a href="../solicitacao/solicitacao.php">SOLICITAÇÕES</a></li>
                <li><a href="../instalacoes/instalacoes.php">INSTALAÇÕES</a></li>
            </ul>
        </nav>

        <div class="user">
            <div class="avatar"></div>

            <div class="user-info">
                <p class="nome"><?= htmlspecialchars($usuario['nome']); ?></p>
                <p class="cargo"><?= htmlspecialchars($usuario['email']); ?></p>
            </div>

            <a href="../../login/logout.php" class="logout">SAIR</a>
        </div>
    </aside>

    <!-- CONTEÚDO PRINCIPAL -->
    <main class="content">
          <div class="cards-wrapper">
  <?php if (isset($_GET['msg']) && $_GET['msg'] === 'aceita'): ?>
    <div class="resuldado-Sucesso">Reserva aceita com sucesso!</div>
<?php endif; ?>

        <h1>Gerenciar Solicitação</h1>
<?php


require_once '../../database/conexao_bd_mysql.php';

if (!isset($_GET['id'])) {
    die("Estabelecimento não especificado.");
}

$id = intval($_GET['id']);

$sql = "
SELECT 
    s.*,
    u.nome_usu, u.email, u.data_nas, u.telefone,
    e.bairro, u.estado, u.cidade, u.cep,
    e.nome_est AS nome_est, e.inicio, e.termino,
    esp.capacidade AS capacidade_espaco,
    esp.cobertura,
    esp.localidade
FROM reserva s
INNER JOIN usuario u 
    ON s.id_usuario = u.id_usuario
INNER JOIN estabelecimento e 
    ON s.id_estabelecimento = e.id_estabelecimento
INNER JOIN espaco esp 
    ON s.id_espaco = esp.id_espaco
WHERE s.id_reserva = $id";

$reservas = mysqli_query($conexao_servidor_bd, $sql);


$value = mysqli_fetch_assoc($reservas);
?>

        <!-- Agendamento -->
        <section class="card">
            <h2>Dados do Agendamento</h2>

            <div class="grid">
                <div>
                    <h3>Data Solicitada</h3>
                    <p><?= htmlspecialchars($value['data']) ?></p>
                </div>
                <div>
                    <h3>Capacidade</h3>
                    <p><?= htmlspecialchars($value['capacidade']) ?></p>
                </div>
                <div>
                    <h3>Hora de Início</h3>
                    <p><?= htmlspecialchars($value['horario_inicio']) ?></p>
                </div>
                <div>
                    <h3>Hora de Término</h3>
                    <p><?= htmlspecialchars($value['horario_fim']) ?></p>
                </div>
                <div>
                    <h3>Data e Hora da Reserva</h3>
                    <p><?= htmlspecialchars($value['data_reserva']) ?></p>
                </div>
            </div>
        </section>

        <!-- USER + ENDEREÇO -->
        <section class="card row">
            <div class="col">
                <h2>Dados do Usuário</h2>

                <h3>Nome do Usuário</h3>
                <p><?= htmlspecialchars($value['nome_usu']) ?></p>

                <h3>E-mail</h3>
                <p><?= htmlspecialchars($value['email']) ?></p>

                <h3>Data de Nascimento</h3>
                <p><?= htmlspecialchars($value['data_nas']) ?></p>

                <h3>Telefone</h3>
                <p><?= htmlspecialchars($value['telefone']) ?></p>
            </div>

            <div class="col">
                <h2>Dados do Endereço</h2>
                <h3>Cidade</h3>
                <p><?= htmlspecialchars($value['cidade']) ?></p>

                <h3>Estado</h3>
                <p><?= htmlspecialchars($value['estado']) ?></p>

                <h3>CEP</h3>
                <p><?= htmlspecialchars($value['cep']) ?></p>
            </div>
        </section>

        <!-- INSTALAÇÃO -->
        <section class="card row">

            <div class="col">
                <h2>Dados da Instalação</h2>

                <h3>Nome da Instalação</h3>
                <p><?= htmlspecialchars($value['nome_est']) ?></p>

                <h3>Horário Abertura</h3>
                <p><?= htmlspecialchars($value['inicio']) ?></p>

                <h3>Horário Fechamento</h3>
                <p><?= htmlspecialchars($value['termino']) ?></p>
            </div>

            <div class="col">
                <h3>Bairro</h3>
                <p><?= htmlspecialchars($value['bairro']) ?></p>
            </div>

            <div class="install-img">
                <img src="./img/futebol.png" alt="Imagem da instalação">
            </div>

        </section>

        <div class="actions">
            <button class="accept" onclick="aceitarReserva(<?= $value['id_reserva'] ?>)">Aceitar</button>
            <button class="reject">Recusar</button>
        </div>
</div>
    </main>
</div>

<script>
function aceitarReserva(id) {
    if (confirm("Tem certeza que deseja aceitar esta reserva?")) {
        window.location.href = "aceitar_reserva.php?id=" + id;
    }
}
</script>

</body>
</html>
