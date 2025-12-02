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
  <title>ALOCATEC - Instalações</title>
  <link rel="stylesheet" href="instalacoes.css">
  <link rel="icon" href="img/logo.png">
  <link rel="shortcut icon" href="img/logo.png">

<style>
.msg-sucesso,
.msg-erro {
  width: 100%;
  max-width: 900px;
  margin: 10px auto;
  padding: 20px 10px;
  border-radius: 10px;
  font-size: 15px;
}

.msg-sucesso {
  background-color: #e6ffee;
  border-left: 6px solid #00c853;
  color: #007a33;
  font-size: 1rem;
  font-weight: 600;
}

.msg-erro {
  background-color: #ffeaea;
  border-left: 6px solid #e53935;
  color: #b71c1c;
  font-size: 1rem;
  font-weight: 600;
}

.erro {
  width: 100%;
  max-width: 700px;
  margin: 40px auto;
  background: #fff3f3;
  border-left: 6px solid #ff4d4d;
  border-radius: 10px;
  padding: 25px;
  text-align: center;
  font-size: 1.2rem;
  color: #cc0000;
}
</style>

</head>
<body>

<aside class="sidebar">
    <div class="logo">
        <div class="icone-logo">
            <img src="./img/logo.png">
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
            <p class="nome"><?php echo htmlspecialchars($usuario['nome']); ?></p>
            <p class="cargo"><?php echo htmlspecialchars($usuario['email']); ?></p>
        </div>

        <a href="../../login/logout.php" class="logout">SAIR</a>
    </div>

</aside>

<div class="content">

    <div class="page">
        <div class="titulo-pagina">
            <h1>Instalações</h1>
            <p>Lista de instalações.</p>
        </div>

        <div class="acoes-topo">
            <button class="botao-acao atualizar" onclick="location.reload()">
                <img src="./img/atualizar.png" alt="Atualizar">
            </button>

            <button class="botao-acao adicionar" onclick="window.location.href='../questionario_instalacao/form1/adicionar_descricao.php'">
                <img src="./img/adicao.png" alt="Adicionar">
                <span>ADICIONAR</span>
            </button>
        </div>

    </div>

<?php
// Mensagem após deletar
if (isset($_SESSION['mensagem_apagar'])) {
    $mensagem = $_SESSION['mensagem_apagar'];
    $classe = $mensagem['tipo'] === 'sucesso' ? 'msg-sucesso' : 'msg-erro';
    echo "<div class='$classe'>{$mensagem['texto']}</div>";
    unset($_SESSION['mensagem_apagar']);
}
?>

<!-- CARDS DE RESUMO -->
<div class="nao-sei">
    <div class="status">
        <h2>Registradas</h2>
        <div class="resultado">
        <?php 
            require '../../database/conexao_bd_mysql.php';
            $sql = "SELECT COUNT(*) AS total FROM estabelecimento";
            $q = mysqli_query($conexao_servidor_bd, $sql);
            echo mysqli_fetch_assoc($q)['total'];
        ?>
        </div>
    </div>

    <div class="status">
        <h2>Ativas</h2>
        <div class="resultado">
        <?php 
            $sql = "SELECT COUNT(*) AS total FROM estabelecimento WHERE status = 'Ativo'";
            $q = mysqli_query($conexao_servidor_bd, $sql);
            echo mysqli_fetch_assoc($q)['total'];
        ?>
        </div>
    </div>

    <div class="status">
        <h2>Inativas</h2>
        <div class="resultado">
        <?php 
            $sql = "SELECT COUNT(*) AS total FROM estabelecimento WHERE status = 'Inativo'";
            $q = mysqli_query($conexao_servidor_bd, $sql);
            echo mysqli_fetch_assoc($q)['total'];
        ?>
        </div>
    </div>
</div>

<!-- FILTROS -->
<form method="GET" class="filters">
    <div class="search">
        <img src="./img/lupa.png" alt="Buscar">
        <input 
            type="text" 
            name="busca"
            placeholder="Verificar Instalações"
            value="<?= isset($_GET['busca']) ? htmlspecialchars($_GET['busca']) : '' ?>"
        >
        <button type="submit" class="btn-buscar">Buscar</button>
    </div>

    <div class="chip">
        <select name="status" onchange="this.form.submit()">
            <option value="">Status</option>
            <option value="Ativo"   <?= (isset($_GET['status']) && $_GET['status']=="Ativo") ? 'selected' : '' ?>>Ativo</option>
            <option value="Inativo" <?= (isset($_GET['status']) && $_GET['status']=="Inativo") ? 'selected' : '' ?>>Inativo</option>
        </select>
    </div>
</form>

<?php
// FILTROS
$statusFiltro = "";
$buscaFiltro = "";

if (!empty($_GET['status'])) {
    $status = mysqli_real_escape_string($conexao_servidor_bd, $_GET['status']);
    $statusFiltro = " AND status = '$status' ";
}

if (!empty($_GET['busca'])) {
    $busca = mysqli_real_escape_string($conexao_servidor_bd, $_GET['busca']);
    $buscaFiltro = " AND nome_est LIKE '%$busca%' ";
}

// PAGINAÇÃO + FILTRO
$limite = 3;
$onde_estou = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$linha_mysql = ($onde_estou - 1) * $limite;

// TOTAL filtrado
$sqlTotal = "
    SELECT COUNT(*) AS total
    FROM estabelecimento
    WHERE 1=1
    $statusFiltro
    $buscaFiltro
";
$resultTotal = mysqli_query($conexao_servidor_bd, $sqlTotal);
$total_pag = ceil(mysqli_fetch_assoc($resultTotal)['total'] / $limite);

// CONSULTA LISTA
$sql = "
    SELECT id_estabelecimento, nome_est, endereco, status, inicio, termino, disponibilidade
    FROM estabelecimento
    WHERE 1=1
    $statusFiltro
    $buscaFiltro
    LIMIT $linha_mysql, $limite
";
$dados = mysqli_query($conexao_servidor_bd, $sql);

if ($dados && mysqli_num_rows($dados) > 0):

    while ($value = mysqli_fetch_assoc($dados)):
        $id = htmlspecialchars($value['id_estabelecimento']);
?>

<div class='solicitacao-card' onclick="window.location.href='../detalhes_instalacoes/instalacao.php?id_estabelecimento=<?= $id ?>'">
    <div class='topo-solicitacao'>
        <div class='nome-espaco'>
            <h2><?= htmlspecialchars($value['nome_est']) ?></h2>
        </div>

        <div class='status-solicitacao <?= htmlspecialchars($value['status']) ?>'>
            <h2><?= htmlspecialchars($value['status']) ?></h2>
        </div>
    </div>

    <div class='detalhes-solicitacao'>
        <div class='detalhe'>
            <h3>Endereço:</h3>
            <p><?= htmlspecialchars($value['endereco']) ?></p>
        </div>

        <div class='detalhe'>
            <h3>Início:</h3>
            <p><?= htmlspecialchars($value['inicio']) ?></p>
        </div>

        <div class='detalhe'>
            <h3>Término:</h3>
            <p><?= htmlspecialchars($value['termino']) ?></p>
        </div>

        <div class='detalhe'>
            <h3>Disponibilidade:</h3>
            <p><?= htmlspecialchars($value['disponibilidade']) ?></p>
        </div>
    </div>
</div>

<?php endwhile; else: ?>

<div class="erro"><h2>Nenhuma instalação encontrada.</h2></div>

<?php endif; ?>

<!-- PAGINAÇÃO -->
<div class="pagination-dots">
<?php for ($i=1; $i <= $total_pag; $i++): ?>
    <a href="?page=<?= $i ?>&status=<?= $_GET['status'] ?? '' ?>&busca=<?= $_GET['busca'] ?? '' ?>"
       class="dot <?= ($i == $onde_estou) ? 'active' : '' ?>">
    </a>
<?php endfor; ?>
</div>

</body>
</html>
