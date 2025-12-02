<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../database/conexao_bd_mysql.php';
require_once '../../login/login.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: ../../index.php");
    exit();
}

$usuario = $_SESSION['usuario'];

if (empty($usuario) && isset($_SESSION['usuario'])) {
    $usuario = $_SESSION['usuario'];
}

if (!isset($_GET['id_estabelecimento'])) {
    die("<h1>Estabelecimento não especificado.</h1>");
}

$id = intval($_GET['id_estabelecimento']);

$sql = "
SELECT e.id_estabelecimento, e.nome_est AS nome_est, e.endereco, e.numero, e.bairro, e.cep, 
       e.cidade, e.complemento, e.uf, e.inicio, e.termino, e.disponibilidade, e.status,
       s.id_espaco, e.tipo, s.capacidade, s.cobertura, s.largura, s.comprimento, 
       s.localidade 
FROM estabelecimento e
JOIN espaco s ON e.id_estabelecimento = s.id_estabelecimento
WHERE e.id_estabelecimento = $id;
";

$result = mysqli_query($conexao_servidor_bd, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    die("Instalação não encontrada.");
}

$dados = mysqli_fetch_assoc($result);

if (!$dados) {
    die("Erro ao ler dados da instalação.");
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Editar Instalação</title>
  <link rel="stylesheet" href="editar.css">
  <link rel="icon" href="img/logo.png">
  <link rel="shortcut icon" href="img/logo.png">
</head>
<body>
  <aside class="sidebar">
    <div class="logo">
      <div class="icone-logo">
        <img src="./img/logo.png" alt="Logo">
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
        <p class="nome"><?php echo htmlspecialchars(isset($usuario['nome']) ? $usuario['nome'] : 'Usuário'); ?></p>
        <p class="cargo"><?php echo htmlspecialchars(isset($usuario['email']) ? $usuario['email'] : ''); ?></p>
      </div>
      <a href="../../login/logout.php" class="logout">SAIR</a>
    </div>
  </aside>
<main class="content">
<div class="header-form">
  <h1>Editar Instalação</h1>
</div>

  <form class="form" method="POST" action="./salvar/salvar_edicao_instalacao.php">
    <input type="hidden" name="id_estabelecimento" value="<?php echo htmlspecialchars($dados['id_estabelecimento']); ?>">
    <input type="hidden" name="id_espaco" value="<?php echo htmlspecialchars($dados['id_espaco']); ?>">

  <div class="form-row">
    <div class="form-group">
    <label>Nome do Espaço:</label>
    <input type="text" name="nome" value="<?php echo htmlspecialchars($dados['nome_est']); ?>"><br>

        <label>Status:</label>
    <select name="status">
      <option value="Ativo" <?php echo ($dados['status'] === 'Ativo') ? 'selected' : ''; ?>>Ativo</option>
      <option value="Inativo" <?php echo ($dados['status'] === 'Inativo') ? 'selected' : ''; ?>>Inativo</option>
    </select>
    </div>
</div>

<div class="form-row">
  <div class="form-group">
    <label>Tipo:</label>
    <input type="text" name="tipo" value="<?php echo htmlspecialchars($dados['tipo']); ?>"><br>

    <label>Capacidade:</label>
    <input type="text" name="capacidade" value="<?php echo htmlspecialchars($dados['capacidade']); ?>"><br>

    <label>Cobertura:</label>
    <select name="cobertura">
      <option value="Sim" <?php echo ($dados['cobertura'] === 'Sim') ? 'selected' : ''; ?>>Sim</option>
      <option value="Não" <?php echo ($dados['cobertura'] === 'Não') ? 'selected' : ''; ?>>Não</option>
    </select><br>
    </div>
</div>

  <div class="form-row">
    <div class="form-group">
    <label>Início:</label>
    <input type="time" name="inicio" value="<?php echo htmlspecialchars($dados['inicio']); ?>"><br>

    <label>Término:</label>
    <input type="time" name="termino" value="<?php echo htmlspecialchars($dados['termino']); ?>"><br>
    </div>
</div>

<div class="form-group">
    <label>Disponibilidade:</label>
    <select name="disponibilidade">
      <option value="Seg-Sex" <?php if (strtoupper(trim($dados['disponibilidade'])) == 'SEG-SEX') echo 'selected'; ?>>Seg-Sex</option>
      <option value="Seg-Dom" <?php if (strtoupper(trim($dados['disponibilidade'])) == 'SEG-DOM') echo 'selected'; ?>>Seg-Dom</option>
      <option value="Sab-Dom" <?php if (strtoupper(trim($dados['disponibilidade'])) == 'SAB-DOM') echo 'selected'; ?>>Sab-Dom</option>
    </select><br>
</div>
    <button type="submit" class="btn">Salvar alterações</button>
  </form>
</main>
</body>
</html>
