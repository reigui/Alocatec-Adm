<?php
session_start();
require_once '../../../login/login.php';
require_once "../../../database/conexao_bd_mysql.php";

// Verifica login
if (!isset($_SESSION['usuario'])) {
  header("Location: ../../../index.php");
  exit();
}

$usuario = $_SESSION['usuario'];

// Captura os dados do form3 (Agendamento) via POST e salva na sessão
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $_SESSION['inicio']         = isset($_POST['inicio']) ? trim($_POST['inicio']) : '';
  $_SESSION['termino']        = isset($_POST['termino']) ? trim($_POST['termino']) : '';
  $_SESSION['disponibilidade'] = isset($_POST['disponibilidade']) ? trim($_POST['disponibilidade']) : '';
  $_SESSION['status']         = isset($_POST['status']) ? trim($_POST['status']) : '';
} else {
  // Sem POST, redireciona para o passo anterior
  header("Location: ../form3/adicionar_agendamento.php");
  exit();
}

// ====== DADOS DA SESSÃO - Todos os formulários anteriores ======
// Form1 - Descrição do Espaço
$nome_espaco        = $_SESSION['nome_espaco'] ?? '';
$tipo_espaco        = $_SESSION['tipo_espaco'] ?? '';
$cobertura          = $_SESSION['cobertura'] ?? '';
$capacidade         = $_SESSION['capacidade'] ?? 0;
$largura            = $_SESSION['largura'] ?? '';
$comprimento        = $_SESSION['comprimento'] ?? '';
$descricao_adicional = $_SESSION['descricao_adicional'] ?? '';

// Form2 - Localização
$endereco    = $_SESSION['endereco'] ?? '';
$numero      = $_SESSION['numero'] ?? '';
$bairro      = $_SESSION['bairro'] ?? '';
$cep         = $_SESSION['cep'] ?? '';
$cidade      = $_SESSION['cidade'] ?? '';
$complemento = $_SESSION['complemento'] ?? '';
$uf          = $_SESSION['uf'] ?? '';

// Form3 - Agendamento
$inicio         = $_SESSION['inicio'] ?? '';
$termino        = $_SESSION['termino'] ?? '';
$disponibilidade = $_SESSION['disponibilidade'] ?? '';
$status         = $_SESSION['status'] ?? '';

// Pasta de upload
$pasta = "uploads_instalacoes/";
if (!is_dir($pasta)) {
  mkdir($pasta, 0777, true);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Adicionar Instalação - ALOCATEC</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="adicionar_fotos.css">
  <link rel="icon" href="../img/logo.png" />
  <link rel="shortcut icon" href="../img/logo.png" />
  <script>
    function confirmarSalvar() {
      return confirm("Tem certeza que deseja salvar esta instalação?");
    }
  </script>

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

    <main class="conteudo">
      <h1 class="titulo-principal">Adicionar Instalação</h1>

      <section class="caixa-conteudo">
        <div class="cabecalho-caixa">
          <h2 class="subtitulo">Anexar Fotos</h2>
        </div>

        <div class="container-fotos">

          <form id="formFotos" onsubmit="return validarEEnviar()"
            action="../salvar_instalacao.php" method="POST"
            enctype="multipart/form-data" class="grid-fotos">

            <label class="quadro-foto-input">
              <input type="file" name="fotos[]" accept="image/*">
              <img class="preview" style="display:none;">
              <span class="icon-plus">+</span>
              <button type="button" class="btn-remover" style="display:none;" onclick="removerFoto(this)">×</button>
            </label>

            <label class="quadro-foto-input">
              <input type="file" name="fotos[]" accept="image/*">
              <img class="preview" style="display:none;">
              <span class="icon-plus">+</span>
              <button type="button" class="btn-remover" style="display:none;" onclick="removerFoto(this)">×</button>
            </label>

            <label class="quadro-foto-input">
              <input type="file" name="fotos[]" accept="image/*">
              <img class="preview" style="display:none;">
              <span class="icon-plus">+</span>
              <button type="button" class="btn-remover" style="display:none;" onclick="removerFoto(this)">×</button>
            </label>

            <label class="quadro-foto-input">
              <input type="file" name="fotos[]" accept="image/*">
              <img class="preview" style="display:none;">
              <span class="icon-plus">+</span>
              <button type="button" class="btn-remover" style="display:none;" onclick="removerFoto(this)">×</button>
            </label>

            <label class="quadro-foto-input">
              <input type="file" name="fotos[]" accept="image/*">
              <img class="preview" style="display:none;">
              <span class="icon-plus">+</span>
              <button type="button" class="btn-remover" style="display:none;" onclick="removerFoto(this)">×</button>
            </label>

            <label class="quadro-foto-input">
              <input type="file" name="fotos[]" accept="image/*">
              <img class="preview" style="display:none;">
              <span class="icon-plus">+</span>
              <button type="button" class="btn-remover" style="display:none;" onclick="removerFoto(this)">×</button>
            </label>

            <div class="button-container">
              <button type="submit" class="salvar">Salvar</button>
              <button type="button" class="cancelar" onclick="window.location.href='../form3/adicionar_agendamento.php'">Cancelar</button>
            </div>
          </form>


        </div>

        <!-- JAVASCRIPT PARA PREVIEW E GERENCIAMENTO DAS FOTOS -->
        <script>
          // Preview das fotos ao selecionar
          document.querySelectorAll('.quadro-foto-input input[type="file"]').forEach(input => {
            input.addEventListener('change', function (event) {
              const file = event.target.files[0];
              const container = event.target.parentElement;
              const preview = container.querySelector('.preview');
              const iconPlus = container.querySelector('.icon-plus');
              const btnRemover = container.querySelector('.btn-remover');

              if (file) {
                // Valida se é uma imagem
                if (!file.type.startsWith('image/')) {
                  alert('Por favor, selecione apenas arquivos de imagem.');
                  event.target.value = '';
                  return;
                }

                // Valida tamanho (máximo 5MB)
                if (file.size > 5 * 1024 * 1024) {
                  alert('A imagem deve ter no máximo 5MB.');
                  event.target.value = '';
                  return;
                }

                const reader = new FileReader();
                reader.onload = function (e) {
                  preview.src = e.target.result;
                  preview.style.display = "block";
                  iconPlus.style.display = "none";
                  btnRemover.style.display = "block";
                  container.classList.add('has-image');
                }
                reader.readAsDataURL(file);
              }
            });
          });

          // Função para remover foto
          function removerFoto(btn) {
            event.preventDefault();
            event.stopPropagation();
            
            const container = btn.parentElement;
            const input = container.querySelector('input[type="file"]');
            const preview = container.querySelector('.preview');
            const iconPlus = container.querySelector('.icon-plus');
            
            // Limpa o input
            input.value = '';
            
            // Esconde preview e botão remover
            preview.style.display = 'none';
            preview.src = '';
            btn.style.display = 'none';
            
            // Mostra o ícone de +
            iconPlus.style.display = 'block';
            container.classList.remove('has-image');
          }

          // Validação antes de enviar
          function validarEEnviar() {
            const inputs = document.querySelectorAll('.quadro-foto-input input[type="file"]');
            let temFoto = false;
            
            inputs.forEach(input => {
              if (input.files && input.files.length > 0) {
                temFoto = true;
              }
            });

            if (!temFoto) {
              if (!confirm('Você não selecionou nenhuma foto. Deseja continuar mesmo assim?')) {
                return false;
              }
            }

            return confirm("Tem certeza que deseja salvar esta instalação?");
          }

          // Contador de fotos selecionadas
          function atualizarContador() {
            const inputs = document.querySelectorAll('.quadro-foto-input input[type="file"]');
            let count = 0;
            inputs.forEach(input => {
              if (input.files && input.files.length > 0) count++;
            });
            return count;
          }
        </script>
      </section>
    </main>
  </div>
</body>

</html>