<?php
require_once '../../../login/login.php';

if (!Store::isLogged()) {
  header("Location: ../../../index.php");
  exit();
}

$usuario = Store::get('usuario');
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
  <link rel="stylesheet" href="adicionar_descricao.css" />
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

      <section class="form-card">
        <h2>Descrição do Espaço</h2>

        <form method="post" action="../form2/adicionar_localizacao.php">
          <label>Nome do Espaço</label>
          <input type="text" name="nome_espaco" required />

          <div class="form-row">
            <div>
              <label>Tipo de Espaço/Esporte</label>
              <select name="tipo_espaco" required>
                <option>Vôlei</option>
                <option>Futebol</option>
                <option>Basquete</option>
                <option>Piscina</option>
                <option>Poliesportivo</option>
                <option>Outros</option>
              </select>
            </div>

            <div>
              <label>Cobertura</label>
              <select name="cobertura" required>
                <option>Sim</option>
                <option>Não</option>
              </select>
            </div>

            <div>
              <label>Capacidade</label>
              <input type="number" name="capacidade" required />
            </div>
          </div>

          <label>Tamanho do Espaço</label>
          <div class="form-row">
            <input type="text" name="largura" placeholder="Largura" required />
            <input type="text" name="comprimento" placeholder="Comprimento" required />
          </div>

          <label>Descrição Adicional</label>
          <input type="text" name="descricao_adicional" required />

          <div class="button-container">
            <button type="submit" class="next-btn">Próximo</button>
          </div>
        </form>
      </section>
    </main>
  </div>
</body>

</html>