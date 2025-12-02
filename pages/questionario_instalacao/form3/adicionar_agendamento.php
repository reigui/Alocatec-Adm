<?php
// adicionar_agendamento.php
session_start();
require_once '../../../login/login.php'; // ajuste caminho

if (!isset($_SESSION['usuario'])) {
    header("Location: ../../../index.php");
    exit();
}
$usuario = $_SESSION['usuario'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['endereco']     = isset($_POST['endereco']) ? trim($_POST['endereco']) : '';
    $_SESSION['numero']       = isset($_POST['numero']) ? trim($_POST['numero']) : '';
    $_SESSION['bairro']       = isset($_POST['bairro']) ? trim($_POST['bairro']) : '';
    $_SESSION['cep']          = isset($_POST['cep']) ? trim($_POST['cep']) : '';
    $_SESSION['cidade']       = isset($_POST['cidade']) ? trim($_POST['cidade']) : '';
    $_SESSION['complemento']  = isset($_POST['complemento']) ? trim($_POST['complemento']) : '';
    $_SESSION['uf']           = isset($_POST['uf']) ? strtoupper(trim($_POST['uf'])) : '';
} else {
    header("Location: form2/adicionar_localizacao.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ALOCATEC - Adicionar Instalações</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="adicionar_agendamento.css" />
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
        <h2>Dados de Agendamento</h2>

        <form method="post" action="../form4/adicionar_fotos.php">
          <div class="form-row">
            <div>
              <label>Tempo de Uso</label>
            <input type="text" placeholder="Início" name="inicio" required> </div>
            <div>
              <label>&nbsp;</label>
              <input type="text" placeholder="Término" name="termino" required> </div>
          </div>

          <div class="form-row">
            <div>
              <label>Disponibilidade</label>
                <div>
                  <select name="disponibilidade">
                    <option>Seg-Sex</option>
                    <option>Seg-Dom</option>
                    <option>Sab-Dom</option>
                  </select>
                </div>
            </div>

            <div>
              <label>Status</label>
                <div>
                  <select name="status">
                    <option>Ativo</option>
                    <option>Inativo</option>
                  </select>
                </div>
            </div>

            <div class="button-container">
              <button type="submit" class="next-btn">Próximo</button>
            </div>
          </div>
        </form>
      </div>
    </main>
  </div>
</body>
</html>