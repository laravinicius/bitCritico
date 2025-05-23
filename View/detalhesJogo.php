<?php
include('../Controller/ConexaoBD.php');
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$sqlJogo = "SELECT * FROM Jogo WHERE id_jogo = $id";
$resultadoJogo = $mysqli->query($sqlJogo);
if ($resultadoJogo->num_rows == 0) {
  echo "<h1>Jogo não encontrado</h1>";
  exit;
}

$jogo = $resultadoJogo->fetch_assoc();
$sqlReviews = "SELECT * FROM Review WHERE id_jogo = $id";
$resultadoReviews = $mysqli->query($sqlReviews);

$sqlGenero = "SELECT * FROM Jogo_Genero WHERE id_jogo = $id";
$resultadoGenero = $mysqli->query($sqlGenero);
$genero = $resultadoGenero->fetch_assoc();



?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($jogo['nome_jogo'])?> - BitCritico</title>
  <link rel="stylesheet" href="./estilos/index.css" />
</head>

<body>
  <header>
    <!--Inicio das Telas de Cadastro e Login-->
    <div class="logo"><a class="logo titulo" href="../index.php">Bit Crítico</a></div>
    <nav>
      <a class="teste" href="./View/jogos.php">Jogos A-Z</a>
    </nav>

    <div class="telas">
      <button class="voltar" onclick="history.back()">⬅️</button>
      <button class="login" onclick="abrirModal()">Entrar</button>
    </div>

    <div class="modal-bg" id="modalLogin">
      <div class="modal">
        <span class="close-modal" onclick="fecharModal()">✖</span>
        <form>
          <h2>Login</h2>

          <label for="usuario">Email ou Usuário</label><br>
          <input type="text" id="usuario" name="usuario"><br>

          <label for="senha">Senha</label><br>
          <input type="password" id="senha" name="senha"><br>

          <div class="modal-buttons">
            <button type="submit"><a href="./Perfil.html">Login</a></button>
          </div>
        </form>
      </div>

      <div class="modal">
        <span class="close-modal" onclick="fecharModal()">✖</span>
        <form>
          <h2>Cadastro</h2>

          <label for="usuario">Nome de Usuário</label><br>
          <input type="text" id="usuario" name="usuario"><br>

          <label for="usuario">Email</label><br>
          <input type="text" id="usuario" name="usuario"><br>

          <label for="senha">Senha</label><br>
          <input type="password" id="senha" name="senha"><br>

          <div class="modal-buttons">
            <button type="button"><a href="./Perfil.html">Cadastrar</a></button>
          </div>
        </form>

      </div>
    </div>


    <!--Fim da Parte de Cadastro-->
  </header>

  <main class="form-box perfil">
    <a href="../index.php" style="color: var(--cor-primaria); text-decoration: none;">&larr; Voltar</a>

    <div style="margin-top: 30px;">
      <img src="images/<?= htmlspecialchars($jogo['capa_jogo']) ?>" alt="Imagem do Jogo" style="width: 100%; border-radius: 10px; border: 2px solid #333;">
    </div>

    <h1 style="color: var(--cor-primaria); margin-top: 30px;"><?= htmlspecialchars($jogo['nome_jogo']) ?> </h1>

    <p style="color: #ccc; margin: 10px 0 30px;">
      <!-- <?= htmlspecialchars($genero['id_genero'])?> -->
      <?= htmlspecialchars($jogo['descricao_jogo'])?>
    </p>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
      <div>
        <strong style="color: var(--cor-destaque); font-size: 1.5rem;">Nota do Jogo:</strong>
        <div style="font-size: 2rem; color: var(--cor-primaria);">8.5</div>
      </div>
    </div>

    <!-- Campo de avaliação lembrar tbm de arrumar mais tarde-->
    <div style="margin-bottom: 30px;">
      <label for="grafico">Gráfico:</label>
      <input type="range" id="grafico" name="grafico" min="0" max="10" style="width: 100%;">

      <label for="jogabilidade">Jogabilidade:</label>
      <input type="range" id="jogabilidade" name="jogabilidade" min="0" max="10" style="width: 100%;">

      <label for="historia">História:</label>
      <input type="range" id="historia" name="historia" min="0" max="10" style="width: 100%;">
    </div>

    <button style="
      width: 100%;
      padding: 15px;
      background-color: var(--cor-destaque);
      color: #fff;
      font-size: 1.2rem;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background-color 0.3s;
    ">
      Clique aqui para deixar sua nota
    </button>
  </main>

  <footer class="rodape">
    <p>© 2025 Bit Crítico. Criado por Gabriel, Vinicius, Matheus, Davi, Eduardo.</p>
    <div class="midiaSocial">
      <a href="../index.php">Bit Crítico</a>
      <a href="https://www.instagram.com/bit_critico?igsh=MW0zdTdxOGpwNnk4bw==">Instagram</a>
    </div>
  </footer>
</body>

</html>