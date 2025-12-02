<?php
require_once '../../database/conexao_bd_mysql.php';
require_once '../../login/login.php';

if (!Store::isLogged()) {
  header("Location: ../../index.php");
  exit();
}

$usuario = Store::get('usuario');

if (!isset($_GET['id_estabelecimento'])) {
  die("Estabelecimento não especificado.");
}

$id_estabelecimento = intval($_GET['id_estabelecimento']); 

$sql_estabelecimento = "
    SELECT nome_est, tipo, endereco, numero, bairro, cep, cidade, 
           complemento, uf, inicio, termino, disponibilidade, status
    FROM estabelecimento
    WHERE id_estabelecimento = $id_estabelecimento;
";

$result_estab = mysqli_query($conexao_servidor_bd, $sql_estabelecimento);
if (!$result_estab || mysqli_num_rows($result_estab) == 0) {
  die("Estabelecimento não encontrado.");
}

$dados_estab = mysqli_fetch_assoc($result_estab);


$sql_espaco = "
      SELECT capacidade, cobertura, largura, comprimento, 
    localidade, E.id_estabelecimento
    FROM espaco E
    INNER JOIN estabelecimento T ON E.id_estabelecimento = T.id_estabelecimento
    WHERE T.id_estabelecimento = $id_estabelecimento;
";

$result_espaco = mysqli_query($conexao_servidor_bd, $sql_espaco);

if (!$result_espaco || mysqli_num_rows($result_espaco) == 0) {
  die("Espaço não encontrado.");
}

$dados_espaco = mysqli_fetch_assoc($result_espaco);

$cobertura       = $dados_espaco['cobertura'];
$capacidade      = $dados_espaco['capacidade'];
$largura         = $dados_espaco['largura'];
$comprimento     = $dados_espaco['comprimento'];

$status = $dados_estab['status'];
$tipo_espaco     = $dados_estab['tipo'];
$nome_espaco      = $dados_estab['nome_est'];
$endereco        = $dados_estab['endereco'];
$numero          = $dados_estab['numero'];
$bairro          = $dados_estab['bairro'];
$cep             = $dados_estab['cep'];
$cidade          = $dados_estab['cidade'];
$complemento     = $dados_estab['complemento'];
$uf              = $dados_estab['uf'];
$inicio          = $dados_estab['inicio'];
$termino         = $dados_estab['termino'];
$disponibilidade = $dados_estab['disponibilidade'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ALOCATEC - Instalações</title>
  <link rel="stylesheet" href="instalacao.css">
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

.msg-sucesso.fade-out,
.msg-erro.fade-out {
  opacity: 0;
  pointer-events: none;
}
</style>
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
        <p class="nome"><?php echo htmlspecialchars($usuario['nome']); ?></p>
        <p class="cargo"><?php echo htmlspecialchars($usuario['email']); ?></p>
      </div>
      <a href="../../login/logout.php" class="logout">SAIR</a>
    </div>
  </aside>

  <main class="content">
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$id_estabelecimento = $_GET['id_estabelecimento'] ?? null;
if (isset($_SESSION['mensagem'])) {
    $mensagem = $_SESSION['mensagem'];
    $classe = $mensagem['tipo'] === 'sucesso' ? 'msg-sucesso' : 'msg-erro';
    echo "<div class='$classe'>{$mensagem['texto']}</div>";
    unset($_SESSION['mensagem']); 
}
?>

    <div class="header-form">
      <h2><?php echo htmlspecialchars($nome_espaco); ?></h2>
      <span class="status <?= htmlspecialchars($status) ?>">
  <?= htmlspecialchars($status) ?>
</span>
    </div>

    <form class="form">
      <div class="form-group full">
        <label>Nome do Espaço</label>
        <p><?php echo htmlspecialchars($nome_espaco); ?></p>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Tipo de Espaço/Esporte</label>
          <p><?php echo htmlspecialchars($tipo_espaco); ?></p>
        </div>
        <div class="form-group">
          <label>Cobertura</label>
          <p><?php echo htmlspecialchars($cobertura); ?></p>
        </div>
        <div class="form-group">
          <label>Capacidade</label>
          <p><?php echo htmlspecialchars($capacidade); ?></p>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Largura</label>
          <p><?php echo htmlspecialchars($largura); ?></p>
        </div>
        <div class="form-group">
          <label>Comprimento</label>
          <p><?php echo htmlspecialchars($comprimento); ?></p>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group large">
          <label>Endereço</label>
          <p><?php echo htmlspecialchars($endereco); ?></p>
        </div>
        <div class="form-group small">
          <label>Nº</label>
          <p><?php echo htmlspecialchars($numero); ?></p>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Bairro</label>
          <p><?php echo htmlspecialchars($bairro); ?></p>
        </div>
        <div class="form-group">
          <label>CEP</label>
          <p><?php echo htmlspecialchars($cep); ?></p>
        </div>
        <div class="form-group">
          <label>Cidade</label>
          <p><?php echo htmlspecialchars($cidade); ?></p>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group large">
          <label>Complemento</label>
          <p><?php echo htmlspecialchars($complemento); ?></p>
        </div>
        <div class="form-group small">
          <label>UF</label>
          <p><?php echo htmlspecialchars($uf); ?></p>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Horário de Funcionamento</label>
          <p><?php echo htmlspecialchars($inicio . ' - ' . $termino); ?></p>
        </div>
      </div>

      <div class="form-group full">
        <label>Disponibilidade</label>
        <p><?php echo htmlspecialchars($disponibilidade); ?></p>
      </div>
    </form>

    <div class="form-actions">
      <button class="btn btn-apagar" onclick="window.location.href='../apagar_instalacao/apagar.php?id_estabelecimento=<?php echo $id_estabelecimento; ?>'">
        Apagar
    </button>

      <button type="button" class="btn btn-editar" onclick="window.location.href='../editar_instalacao/editar.php?id_estabelecimento=<?php echo $id_estabelecimento; ?>'">
        Editar</button>
      <button type="button" class="btn btn-voltar">Voltar</button>
    </div>
  </main>

</body>
</html>
