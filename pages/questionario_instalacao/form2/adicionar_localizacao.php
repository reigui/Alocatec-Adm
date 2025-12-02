<?php
// adicionar_localizacao.php
session_start();
require_once '../../../login/login.php'; // ajuste caminho

if (!isset($_SESSION['usuario'])) {
    header("Location: ../../../index.php");
    exit();
}
$usuario = $_SESSION['usuario'];

// Grava os dados enviados pelo form1 em $_SESSION
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['nome_espaco'] = isset($_POST['nome_espaco']) ? trim($_POST['nome_espaco']) : '';
    $_SESSION['tipo_espaco'] = isset($_POST['tipo_espaco']) ? trim($_POST['tipo_espaco']) : '';
    $_SESSION['cobertura']   = isset($_POST['cobertura']) ? trim($_POST['cobertura']) : '';
    $_SESSION['capacidade']  = isset($_POST['capacidade']) ? intval($_POST['capacidade']) : 0;
    $_SESSION['largura']     = isset($_POST['largura']) ? trim($_POST['largura']) : '';
    $_SESSION['comprimento'] = isset($_POST['comprimento']) ? trim($_POST['comprimento']) : '';
    $_SESSION['descricao_adicional'] = isset($_POST['descricao_adicional']) ? trim($_POST['descricao_adicional']) : '';
} else {
    // sem POST, redireciona para o passo 1
    header("Location: form1_adicionar_descricao.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ALOCATEC - Adicionar Instalações</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="adicionar_localizacao.css" />
  <link rel="icon" href="../img/logo.png" />    
  <link rel="shortcut icon" href="../img/logo.png" />
</head>
<body>
  <div class="container">
    <aside class="sidebar">
      <div class="logo">
        <div class="icone-logo">
            <img src="../img/logo.png">
        </div>
        <h2>ALOCATEC</h2>
        <br><hr>
    </div>
      <nav>
        <ul>
          <li><a href="../../solicitacao/solicitacao.php">SOLICITAÇÕES</a></li>
          <li><a href="../../instalacoes/instalacoes.php">INSTALAÇÕES</a></li>
        </ul>
      </nav>
      <div class="user">
        <div class="avatar"></div>
        <div class="user-info">
          <p class="nome"><?php echo htmlspecialchars($usuario['nome']); ?></p>
          <p class="cargo"><?php echo htmlspecialchars($usuario['email']); ?></p>
        </div>
        <a href="../../../login/logout.php" class="logout">SAIR</a>
      </div>
    </aside>

    <main class="main">
      <h1 class="page-title">Adicionar Instalações</h1>

      <div class="form-card">
        <h2>Localização</h2>
        
        <form method="post" action="../form3/adicionar_agendamento.php">
          <div class="form-row">
            <div class="campo maior">
              <label>Endereço</label>
              <input type="text" name="endereco" required> </div>
            <div class="campo pequeno">
              <label>Nº</label>
              <input type="text" name="numero" required> </div>
          </div>

          <div class="form-row">
            <div>
              <label>Bairro</label>
              <input type="text" name="bairro" required> </div>
            <div>
              <label>CEP</label>
              <input type="text" name="cep" required> </div>
            <div>
              <label>Cidade</label>
              <input type="text" name="cidade" required> </div>
          </div>

          <div class="form-row">
            <div>
              <label>Complemento</label>
              <input type="text" name="complemento"> </div>
            <div>
              <label>UF</label>
              <input type="text" name="uf" maxlength="2" required> </div>
          </div>

          <div class="button-container">
            <button type="submit" class="next-btn"
                >Próximo</button>
          </div>
        </form>
      </div>
    </main>
  </div>
</body>
</html>
