<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ALOCATEC - Cadastro</title>
  <link rel="stylesheet" href="cadastro.css">
   <link rel="icon" href="img/logo.png">
  <link rel="shortcut icon" href="img/logo.png">
</head>
<body>
  <div class="container">
    
    <aside class="sidebar">
    <div class="logo">
    <div class="icone-logo">
      <img src="./img/logo.png">
    </div>
      <h2>ALOCATEC</h2>
    <hr></hr>
    </div>
    <div class="welcome">
      <h3>BEM- VINDO</h3>
      <p>Caso possua uma conta acesse ela agora!</p>
      <button class="btn-entrar" onclick="location.href='../../index.php'">ENTRAR</button>
      </div>
    </aside>

    <main class="formulario">
      <h2>CADASTRE-SE</h2>

      <form>
        <label>Nome de Usuário</label>
        <input type="text" placeholder="Digite seu nome de usuário">

        <label>E-mail de login</label>
        <input type="email" placeholder="Digite seu e-mail">

        <label>Telefone</label>
        <input type="tel" placeholder="(00) 00000-0000">

        <label>CPF</label>
        <input type="text" placeholder="000.000.000-00">

        <label>Senha</label>
        <input type="password" placeholder="Digite sua senha">

        <label>Confirmação de Senha</label>
        <input type="password" placeholder="Confirme sua senha">

        <label>Gênero</label>
        <div class="genero">
          <label><input type="radio" name="genero" value="masculino"> Masculino</label>
          <label><input type="radio" name="genero" value="feminino"> Feminino</label>
        </div>

        <button type="submit" class="btn-cadastrar">CADASTRAR-SE</button>
      </form>

      <div class="foto-perfil">
        <div class="foto-circulo"></div>
        <p>Adicione sua foto</p>
      </div>
    </main>
  </div>
</body>
</html>
