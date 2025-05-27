<?php
session_start();

// Inclui a conexão com o banco e trata possíveis erros
include('./Controller/ConexaoBD.php');
$resultado = null;
if ($mysqli && $mysqli instanceof mysqli) {
    $resultado = $mysqli->query("SELECT * FROM Jogo");
    if (!$resultado) {
        error_log("Erro na consulta ao banco: " . $mysqli->error);
    }
} else {
    error_log("Falha na conexão com o banco.");
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bit Crítico</title>
    <link rel="stylesheet" href="./View/estilos/index.css">
    <link rel="icon" href="./View/assets/favicon.ico">
    <meta name="keywords" content="Reviews, Review, reviews, review, REVIEWS, REVIEWS, JOGOS, jogos, Melhores jogos, Melhores jogos">
    <meta name="description" content="Site voltado para Reviews de todos os tipos de jogos">
    <script src="./View/scripts/bot.js"></script>
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

    <main>
        <section class="Principal">
            <h1>Bit Crítico o Melhor Lugar para Reviews de Jogos em Geral</h1>
            <p>Acompanhe análises dos melhores games do momento!</p>
        </section>

        <section class="game-reviews">
            <h2>Últimos Reviews</h2>
            <div class="review-grid">
                <?php if ($resultado && $resultado->num_rows > 0): ?>
                    <?php while ($jogo = $resultado->fetch_assoc()): ?>
                        <a href="./View/detalhesJogo.php?id=<?= htmlspecialchars($jogo['id_jogo']) ?>">
                            <div class="game">
                                <img src="./View/images/<?= htmlspecialchars($jogo['capa_jogo']) ?>" alt="<?= htmlspecialchars($jogo['nome_jogo']) ?>">
                                <h3><?= htmlspecialchars($jogo['nome_jogo']) ?></h3>
                            </div>
                        </a>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>Nenhum jogo cadastrado no momento.</p>
                <?php endif; ?>
            </div>
            <h2>TOP 3 Reviews</h2>
            <div class="review-grid">
                <a href="./View/detalhesJogo.php">
                    <div class="game">
                        <img src="img/jogo-exemplo.jpg" alt="Jogo Exemplo">
                        <h3>Jogo Exemplo</h3>
                    </div>
                </a>
                <a href="./View/detalhesJogo.php">
                    <div class="game">
                        <img src="img/jogo-exemplo.jpg" alt="Jogo Exemplo">
                        <h3>Jogo Exemplo</h3>
                    </div>
                </a>
                <a href="./View/detalhesJogo.php">
                    <div class="game">
                        <img src="img/jogo-exemplo.jpg" alt="Jogo Exemplo">
                        <h3>Jogo Exemplo</h3>
                    </div>
                </a>
            </div>
        </section>
    </main>

    <div class="chat-container">
        <button id="chat-toggle">💬</button>
        <div id="chat-box" style="display: none;">
            <div class="chat-header">BitCri - Assistente de Reviews</div>
            <div id="chat-messages"></div>
            <div class="chat-input-area">
                <input type="text" id="chat-input" placeholder="Digite sua pergunta...">
                <button id="send-button">➤</button>
            </div>
        </div>
    </div>

    <footer class="rodape">
        <p>© 2025 Bit Crítico. Criado por Gabriel, Vinicius, Matheus, Davi, Eduardo.</p>
        <div class="midiaSocial">
            <a href="index.php">Bit Crítico</a>
            <a href="https://www.instagram.com/bit_critico?igsh=MW0zdTdxOGpwNnk4bw==">Instagram</a>
        </div>
    </footer>
</body>
</html>