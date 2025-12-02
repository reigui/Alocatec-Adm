<?php
require_once '../../login/login.php';

if (!Store::isLogged()) {
    header("Location: ../../index.php");
    exit();
}

$usuario = Store::get('usuario');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ALOCATEC - Solicitações</title>
  <link rel="stylesheet" href="solicitacao.css">
  <link rel="icon" href="img/logo.png">
</head>
<body>

<aside class="sidebar">
  <div class="logo">
    <div class="icone-logo">
      <img src="./img/logo.png" alt="Logo ALOCATEC">
    </div>
    <h2>ALOCATEC</h2>
    <br><hr>
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
      <p class="nome"><?= htmlspecialchars($usuario['nome']) ?></p>
      <p class="cargo"><?= htmlspecialchars($usuario['email']) ?></p>
    </div>

    <a href="../../login/logout.php" class="logout">SAIR</a>
  </div>
</aside>

<div class="content">
  <div class="page">
    <h1>Solicitações</h1>
    <p>Lista de todas as solicitações de reserva.</p>
  </div>


<div class="nao-sei">

  <div class="status">
    <h2>Pendentes</h2>
    <?php
    require '../../database/conexao_bd_mysql.php';
    $sql = "SELECT COUNT(*) AS total FROM reserva WHERE status='Pendente'";
    $r = mysqli_query($conexao_servidor_bd, $sql);
    echo "<div class='resultado'>" . mysqli_fetch_assoc($r)['total'] . "</div>";
    ?>
  </div>

  <div class="status">
    <h2>Autorizados</h2>
    <?php
    $sql = "SELECT COUNT(*) AS total FROM reserva WHERE status='concluída'";
    $r = mysqli_query($conexao_servidor_bd, $sql);
    echo "<div class='resultado'>" . mysqli_fetch_assoc($r)['total'] . "</div>";
    ?>
  </div>

  <div class="status">
    <h2>Recusados</h2>
    <?php
    $sql = "SELECT COUNT(*) AS total FROM reserva WHERE status='cancelada'";
    $r = mysqli_query($conexao_servidor_bd, $sql);
    echo "<div class='resultado'>" . mysqli_fetch_assoc($r)['total'] . "</div>";
    ?>
  </div>

</div>


<form method="GET" class="filters">

  <div class="search">
    <img src="./img/lupa.png" alt="Buscar">
    <input 
      type="text" 
      name="busca" 
      placeholder="Buscar por instalação..."
      value="<?= isset($_GET['busca']) ? htmlspecialchars($_GET['busca']) : '' ?>"
    >
    <button type="submit" class="btn-buscar">Buscar</button>
  </div>

  <div class="chip">
    <select name="status" onchange="this.form.submit()">
      <option value="">Status</option>
      <option value="Pendente"   <?= (($_GET['status'] ?? '') == "Pendente") ? 'selected' : '' ?>>Pendente</option>
      <option value="concluída"  <?= (($_GET['status'] ?? '') == "concluída") ? 'selected' : '' ?>>Autorizado</option>
      <option value="cancelada"  <?= (($_GET['status'] ?? '') == "cancelada") ? 'selected' : '' ?>>Recusado</option>
    </select>
  </div>

</form>


<?php
require '../../database/conexao_bd_mysql.php';

$statusFiltro = "";
$buscaFiltro = "";

if (!empty($_GET['status'])) {
    $status = mysqli_real_escape_string($conexao_servidor_bd, $_GET['status']);
    $statusFiltro = " AND R.status = '$status' ";
}

if (!empty($_GET['busca'])) {
    $busca = mysqli_real_escape_string($conexao_servidor_bd, $_GET['busca']);
    $buscaFiltro = " AND ES.nome_est LIKE '%$busca%' ";
}

$limite = 3;
$onde_estou = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$linha_mysql = ($onde_estou - 1) * $limite;

$total_query = "
    SELECT COUNT(*) AS total
    FROM reserva R
    INNER JOIN estabelecimento ES ON R.id_estabelecimento = ES.id_estabelecimento
    WHERE 1=1
    $statusFiltro
    $buscaFiltro
";

$total_result = mysqli_query($conexao_servidor_bd, $total_query);
$total = mysqli_fetch_assoc($total_result)['total'];
$total_pag = ceil($total / $limite);


$sql = "
SELECT 
    R.id_reserva,
    R.data,
    R.horario_inicio,
    R.horario_fim,
    R.status,
    U.nome_usu AS nome_usu,
    ES.nome_est
FROM reserva R
INNER JOIN usuario U ON R.id_usuario = U.id_usuario
INNER JOIN estabelecimento ES ON R.id_estabelecimento = ES.id_estabelecimento
WHERE 1=1
$statusFiltro
$buscaFiltro
ORDER BY R.data DESC
LIMIT $linha_mysql, $limite
";

$reservas = mysqli_query($conexao_servidor_bd, $sql);


if ($reservas && mysqli_num_rows($reservas) > 0):

while ($reserva = mysqli_fetch_assoc($reservas)):
    $id = htmlspecialchars($reserva['id_reserva']);
?>

<div class="solicitacao-card" onclick="window.location.href='../gerenciar_solicitacao/gerenciar.php?id=<?= $id ?>'">
  <div class="topo-solicitacao">
    <div class="nome-espaco">
      <h2><?= htmlspecialchars($reserva['nome_est']) ?></h2>
    </div>

    <div class="status-solicitacao <?= htmlspecialchars($reserva['status']) ?>">
      <h2><?= htmlspecialchars($reserva['status']) ?></h2>
    </div>
  </div>

  <div class="detalhes-solicitacao">
    <div class="detalhe">
      <h3>Data:</h3>
      <p><?= htmlspecialchars($reserva['data']) ?></p>
    </div>

    <div class="detalhe">
      <h3>Horário:</h3>
      <p><?= $reserva['horario_inicio'] ?> - <?= $reserva['horario_fim'] ?></p>
    </div>

    <div class="detalhe">
      <h3>Usuário:</h3>
      <p><?= htmlspecialchars($reserva['nome_usu']) ?></p>
    </div>
  </div>
</div>

<?php endwhile; else: ?>

<div class="erro">
  <h2>Nenhuma solicitação encontrada</h2>
</div>

<?php endif; ?>

<div class="pagination-dots">
<?php for ($i = 1; $i <= $total_pag; $i++): ?>
    <a 
      class="dot <?= ($i == $onde_estou) ? 'active' : '' ?>"
      href="?page=<?= $i ?>&status=<?= $_GET['status'] ?? '' ?>&busca=<?= $_GET['busca'] ?? '' ?>"
    ></a>
<?php endfor; ?>
</div>

</div>
</body>
</html>
