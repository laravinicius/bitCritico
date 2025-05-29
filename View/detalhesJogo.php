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
        <div class="logo"><a class="logo titulo" href="index.php">Bit Crítico</a></div>
        <nav>
            <a class="teste" href="./View/jogos.php">Jogos A-Z</a>
        </nav>

        <div class="telas">
        <?php if (isset($_SESSION['id_usuario'])): ?>
            <button class="login" onclick="window.location.href='./View/Perfil.php'">Perfil</button>
            <?php if (isset($_SESSION['status_usuario']) && $_SESSION['status_usuario'] == 1): ?>
                <button class="login" onclick="window.location.href='./View/ADM/AdminCenter023839.php'">Sessão Adm</button>
            <?php endif; ?>
            <button class="login" onclick="window.location.href='./Controller/LogoutController.php'">Sair</button>
        <?php else: ?>
            <button class="login" onclick="abrirModal()">Entrar</button>
        <?php endif; ?>
        </div>

        <div class="modal-bg" id="modalLogin">
            <!-- Modal de Login -->
            <div class="modal" id="loginModal">
                <span class="close-modal" onclick="fecharModal()">✖</span>
                <form id="formLogin" method="POST">
                    <input type="hidden" name="action" value="login">
                    <h2>Login</h2>
                    <label for="loginUsuario">Email ou Usuário</label><br>
                    <input type="text" id="loginUsuario" name="usuario"><br>
                    <label for="loginSenha">Senha</label><br>
                    <input type="password" id="loginSenha" name="senha"><br>
                    <div class="modal-buttons">
                        <button type="button" onclick="submitForm('login')">Login</button>
                    </div>
                    <p>Não tem uma conta? <a href="#" onclick="abrirCadastro()">Cadastre-se aqui</a></p>
                    <div id="loginError" style="color: red;"></div>
                </form>
            </div>

            <!-- Modal de Cadastro -->
            <div class="modal" id="cadastroModal" style="display: none;">
                <span class="close-modal" onclick="fecharModal()">✖</span>
feeds                <form id="formCadastro" method="POST">
                    <input type="hidden" name="action" value="cadastro">
                    <h2>Cadastro</h2>
                    <label for="cadUsuario">Nome de Usuário</label><br>
                    <input type="text" id="cadUsuario" name="usuario"><br>
                    <label for="cadEmail">Email</label><br>
                    <input type="text" id="cadEmail" name="email"><br>
                    <label for="cadSenha">Senha</label><br>
                    <input type="password" id="cadSenha" name="senha"><br>
                    <div class="modal-buttons">
                        <button type="button" onclick="submitForm('cadastro')">Cadastrar</button>
                    </div>
                    <p>Já tem uma conta? <a href="#" onclick="abrirLogin()">Fazer login</a></p>
                    <div id="cadastroError" style="color: red;"></div>
                    <div id="cadastroSuccess" style="color: green;"></div>
                </form>
            </div>
        </div>

        <script>
            function abrirModal() {
                document.getElementById('modalLogin').style.display = 'flex';
            }

            function fecharModal() {
                document.getElementById('modalLogin').style.display = 'none';
            }

            function abrirCadastro() {
                document.getElementById('loginModal').style.display = 'none';
                document.getElementById('cadastroModal').style.display = 'block';
                document.getElementById('cadastroError').textContent = '';
                document.getElementById('cadastroSuccess').textContent = '';
            }

            function abrirLogin() {
                document.getElementById('cadastroModal').style.display = 'none';
                document.getElementById('loginModal').style.display = 'block';
                document.getElementById('loginError').textContent = '';
            }

            function submitForm(action) {
                const form = document.getElementById(action === 'login' ? 'formLogin' : 'formCadastro');
                const errorDiv = document.getElementById(action === 'login' ? 'loginError' : 'cadastroError');
                const successDiv = document.getElementById('cadastroSuccess');

                const formData = new FormData(form);

                fetch(`./Controller/${action === 'login' ? 'LoginController.php' : 'CadastroController.php'}`, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = './View/Perfil.php';
                    } else {
                        errorDiv.textContent = data.message;
                        if (action === 'cadastro' && data.success === false && data.message !== 'Este email já está cadastrado.') {
                            successDiv.textContent = '';
                        }
                    }
                })
                .catch(error => {
                    errorDiv.textContent = 'Erro na requisição: ' + error.message;
                });

                return false;
            }

            window.addEventListener('click', function(event) {
                const modal = document.getElementById('modalLogin');
                if (event.target === modal) {
                    fecharModal();
                }
            });

            window.addEventListener('keydown', function(event) {
                if (event.key === "Escape") {
                    fecharModal();
                }
            });
        </script>
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
    <!--POP-UP DE NOTA-->
    <!---->
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