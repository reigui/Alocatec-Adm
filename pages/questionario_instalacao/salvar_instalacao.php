<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../login/login.php';
require_once '../../database/conexao_bd_mysql.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: ../../index.php");
    exit();
}

$usuario = $_SESSION['usuario'];

// Dados obrigatórios da sessão (de todos os formulários anteriores)
$requiredSession = [
    'nome_espaco', 'tipo_espaco', 'cobertura', 'capacidade',
    'largura', 'comprimento', 'endereco', 'numero', 'bairro',
    'cep', 'cidade', 'uf', 'inicio', 'termino', 'disponibilidade', 'status'
];

foreach ($requiredSession as $key) {
    if (!isset($_SESSION[$key])) {
        die("Erro: dado de sessão faltando ($key). Volte e preencha todos os passos.");
    }
}

$id = $usuario['id'];

// ====== DADOS DA SESSÃO ======
// Form1 - Descrição do Espaço
$nome_espaco = $_SESSION['nome_espaco'];
$tipo_espaco = $_SESSION['tipo_espaco'];
$cobertura   = $_SESSION['cobertura'];
$capacidade  = intval($_SESSION['capacidade']);
$largura     = $_SESSION['largura'];
$comprimento = $_SESSION['comprimento'];
$descricao_adicional = $_SESSION['descricao_adicional'] ?? '';

// Form2 - Localização
$endereco    = $_SESSION['endereco'];
$numero      = $_SESSION['numero'];
$bairro      = $_SESSION['bairro'];
$cep         = $_SESSION['cep'];
$cidade      = $_SESSION['cidade'];
$complemento = $_SESSION['complemento'] ?? '';
$uf          = $_SESSION['uf'];

// Form3 - Agendamento
$inicio         = $_SESSION['inicio'];
$termino        = $_SESSION['termino'];
$disponibilidade = $_SESSION['disponibilidade'];
$status         = $_SESSION['status'];

$tamanho_espaco = $largura . ' x ' . $comprimento;
$tempo_uso      = $inicio . ' - ' . $termino;

// ====== PROCESSAR FOTOS ENVIADAS ======
$fotos_enviadas = [];
$fotos_salvas = [];
$erros_fotos = [];

// Diretório para salvar as fotos
$pasta_upload = __DIR__ . '/form4/uploads_instalacoes/';
if (!is_dir($pasta_upload)) {
    mkdir($pasta_upload, 0777, true);
}

// Tipos de imagem permitidos
$tipos_permitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
$tamanho_maximo = 5 * 1024 * 1024; // 5MB

// Processar cada foto enviada
if (isset($_FILES['fotos']) && !empty($_FILES['fotos']['name'][0])) {
    $total_fotos = count($_FILES['fotos']['name']);
    
    for ($i = 0; $i < $total_fotos; $i++) {
        // Pular se não houver arquivo
        if (empty($_FILES['fotos']['name'][$i]) || $_FILES['fotos']['error'][$i] === UPLOAD_ERR_NO_FILE) {
            continue;
        }
        
        $nome_original = $_FILES['fotos']['name'][$i];
        $tipo_arquivo  = $_FILES['fotos']['type'][$i];
        $tamanho       = $_FILES['fotos']['size'][$i];
        $tmp_name      = $_FILES['fotos']['tmp_name'][$i];
        $erro          = $_FILES['fotos']['error'][$i];
        
        // Verificar erros de upload
        if ($erro !== UPLOAD_ERR_OK) {
            $erros_fotos[] = "Erro ao fazer upload de '$nome_original': código $erro";
            continue;
        }
        
        // Validar tipo
        if (!in_array($tipo_arquivo, $tipos_permitidos)) {
            $erros_fotos[] = "Arquivo '$nome_original' não é uma imagem válida.";
            continue;
        }
        
        // Validar tamanho
        if ($tamanho > $tamanho_maximo) {
            $erros_fotos[] = "Arquivo '$nome_original' excede o tamanho máximo de 5MB.";
            continue;
        }
        
        // Gerar nome único para o arquivo
        $extensao = pathinfo($nome_original, PATHINFO_EXTENSION);
        $nome_unico = uniqid('foto_', true) . '.' . $extensao;
        $caminho_destino = $pasta_upload . $nome_unico;
        
        // Mover arquivo
        if (move_uploaded_file($tmp_name, $caminho_destino)) {
            $fotos_enviadas[] = [
                'nome_original' => $nome_original,
                'nome_salvo'    => $nome_unico,
                'caminho'       => 'form4/uploads_instalacoes/' . $nome_unico,
                'tipo'          => $tipo_arquivo,
                'tamanho'       => $tamanho
            ];
        } else {
            $erros_fotos[] = "Falha ao salvar arquivo '$nome_original'.";
        }
    }
}

// ====== SALVAR NO BANCO DE DADOS ======
mysqli_begin_transaction($conexao_servidor_bd);

// Usar prepared statements para evitar SQL injection
$sql1 = "INSERT INTO estabelecimento (
    nome_est, tipo, status, endereco, numero, bairro, cep, cidade, 
    complemento, uf, inicio, termino, disponibilidade, id_administrador
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt1 = mysqli_prepare($conexao_servidor_bd, $sql1);
mysqli_stmt_bind_param($stmt1, "sssssssssssssi", 
    $nome_espaco, $tipo_espaco, $status, $endereco, $numero, $bairro,
    $cep, $cidade, $complemento, $uf, $inicio, $termino, $disponibilidade, $id
);

$sucesso_estabelecimento = mysqli_stmt_execute($stmt1);

if ($sucesso_estabelecimento) {
    $id_estabelecimento = mysqli_insert_id($conexao_servidor_bd);

    $sql2 = "INSERT INTO espaco (
        capacidade, cobertura, largura, comprimento, descricao_adicional, localidade, id_estabelecimento
    ) VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt2 = mysqli_prepare($conexao_servidor_bd, $sql2);
    mysqli_stmt_bind_param($stmt2, "isssssi", 
        $capacidade, $cobertura, $largura, $comprimento, $descricao_adicional, $endereco, $id_estabelecimento
    );
    
    $sucesso_espaco = mysqli_stmt_execute($stmt2);

    if ($sucesso_espaco) {
        // Salvar fotos no banco de dados (se houver tabela para isso)
        $sucesso_fotos = true;
        
        foreach ($fotos_enviadas as $foto) {
            // Verifica se a tabela instalacao_fotos existe e salva
            $sql_foto = "INSERT INTO instalacao_fotos (id_estabelecimento, caminho_foto, nome_original) VALUES (?, ?, ?)";
            $stmt_foto = mysqli_prepare($conexao_servidor_bd, $sql_foto);
            
            if ($stmt_foto) {
                mysqli_stmt_bind_param($stmt_foto, "iss", $id_estabelecimento, $foto['caminho'], $foto['nome_original']);
                if (mysqli_stmt_execute($stmt_foto)) {
                    $fotos_salvas[] = $foto;
                } else {
                    // Se falhar, pode ser que a tabela não exista - não é erro crítico
                    $erros_fotos[] = "Aviso: foto '{$foto['nome_original']}' não foi registrada no banco.";
                }
                mysqli_stmt_close($stmt_foto);
            }
        }
        
        mysqli_commit($conexao_servidor_bd);
        
        $mensagem = "Instalação salva com sucesso!";
        if (count($fotos_salvas) > 0) {
            $mensagem .= " " . count($fotos_salvas) . " foto(s) anexada(s).";
        }
        $tipo_mensagem = "Sucesso";
        
        // Limpar dados da sessão
        $sessaoLimpar = array_merge($requiredSession, ['descricao_adicional', 'complemento']);
        foreach ($sessaoLimpar as $key) {
            unset($_SESSION[$key]);
        }
    } else {
        mysqli_rollback($conexao_servidor_bd);
        $mensagem = "Erro ao salvar espaço: " . mysqli_error($conexao_servidor_bd);
        $tipo_mensagem = "Erro";
        
        // Remover fotos enviadas em caso de erro
        foreach ($fotos_enviadas as $foto) {
            $caminho_completo = $pasta_upload . $foto['nome_salvo'];
            if (file_exists($caminho_completo)) {
                unlink($caminho_completo);
            }
        }
    }
} else {
    mysqli_rollback($conexao_servidor_bd);
    $mensagem = "Erro ao salvar estabelecimento: " . mysqli_error($conexao_servidor_bd);
    $tipo_mensagem = "Erro";
    
    // Remover fotos enviadas em caso de erro
    foreach ($fotos_enviadas as $foto) {
        $caminho_completo = $pasta_upload . $foto['nome_salvo'];
        if (file_exists($caminho_completo)) {
            unlink($caminho_completo);
        }
    }
}

mysqli_close($conexao_servidor_bd);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ALOCATEC - Salvar Instalação</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="salvar_instalacao.css">
  <link rel="icon" href="./img/logo.png">
  <link rel="shortcut icon" href="img/logo.png">
  <style>
.resuldado-Sucesso, .resuldado-Erro {
  width: 100%;
  max-width: 900px;
  margin: 10px auto;
  padding: 20px 10px;
  border-radius: 10px;
  font-size: 15px;
}

.resuldado-Sucesso {
  background-color: #e6ffee;
  border-left: 6px solid #00c853;
  color: #007a33;
  font-size: 1rem;
  font-weight: 600;
}

.resuldado-Erro {
  background-color: #ffeaea;
  border-left: 6px solid #e53935;
  color: #b71c1c;
  font-size: 1rem;
  font-weight: 600;
}

.fotos-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  gap: 15px;
  margin-top: 10px;
}

.foto-item {
  aspect-ratio: 1 / 1;
  border-radius: 8px;
  overflow: hidden;
  border: 1px solid #ddd;
  background: #f5f5f5;
}

.foto-item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.avisos-fotos {
  background-color: #fff8e1;
  border-left: 4px solid #ffc107;
  padding: 15px;
  margin-top: 20px;
  border-radius: 4px;
}

.avisos-fotos ul {
  margin: 10px 0 0 20px;
  color: #856404;
}

.avisos-fotos li {
  margin-bottom: 5px;
}
  </style>
</head>

<body>
  <div class="container">
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

    <main class="main">
       <div class="page-title">
      <div class="titulo-pagina">
        <h1>Instalações</h1>
        <p>Lista de instalações.</p>
    </div>
  <div class="acoes-topo">
  <button class="botao-acao voltar" onclick="window.location.href='../instalacoes/instalacoes.php'">
    <img src="./img/voltar.png">
    <span>VOLTAR</span>
  </button>
</div>
</div>

<div class="resuldado<?= $tipo_mensagem === 'Sucesso' ? '-Sucesso' : '-Erro' ?>">
  <?php echo htmlspecialchars($mensagem); ?>
</div>

      <form class="form">
        <div class="form-group full">
          <label>Nome da Espaço</label>
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
            <label>Tamanho do Espaço</label>
            <p><?php echo htmlspecialchars($tamanho_espaco); ?></p>
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
            <label>Tempo de Uso</label>
            <p><?php echo htmlspecialchars($tempo_uso); ?></p>
          </div>
        </div>

        <div class="form-group full">
          <label>Disponibilidade</label>
          <p><?php echo htmlspecialchars($disponibilidade); ?></p>
        </div>

        <?php if (!empty($fotos_salvas)): ?>
        <div class="form-group full">
          <label>Fotos Anexadas (<?php echo count($fotos_salvas); ?>)</label>
          <div class="fotos-grid">
            <?php foreach ($fotos_salvas as $foto): ?>
              <div class="foto-item">
                <img src="<?php echo htmlspecialchars($foto['caminho']); ?>" alt="<?php echo htmlspecialchars($foto['nome_original']); ?>">
              </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($erros_fotos)): ?>
        <div class="avisos-fotos">
          <p><strong>Avisos sobre fotos:</strong></p>
          <ul>
            <?php foreach ($erros_fotos as $erro): ?>
              <li><?php echo htmlspecialchars($erro); ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php endif; ?>
      </form>
    </main>
  </div>
</body>
</html>
