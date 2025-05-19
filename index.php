<?php
session_start();
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
            <a class="teste" href="./View/jogos.html">Jogos A-Z</a>
        </nav>

        <div class="telas">
            <button class="login" onclick="abrirModal()">Entrar</button>
        </div>

        <div class="modal-bg" id="modalLogin">
            <!-- Modal de Login -->
            <div class="modal" id="loginModal">
                <span class="close-modal" onclick="fecharModal()">✖</span>
                <form id="formLogin" action="Controller/LoginController.php" method="POST">
                    <h2>Login</h2>

                    <label for="loginUsuario">Email ou Usuário</label><br>
                    <input type="text" id="loginUsuario" name="usuario"><br>

                    <label for="loginSenha">Senha</label><br>
                    <input type="password" id="loginSenha" name="senha"><br>

                    <div class="modal-buttons">
                        <button type="submit" onclick="return validarLogin()">Login</button>
                    </div>

                    <p>Não tem uma conta? <a href="#" onclick="abrirCadastro()">Cadastre-se aqui</a></p>
                    <?php
                    if (isset($_SESSION['erro_login'])) {
                        echo '<p style="color: red;">' . $_SESSION['erro_login'] . '</p>';
                        unset($_SESSION['erro_login']);
                    }
                    ?>
                </form>
            </div>

            <!-- Modal de Cadastro -->
            <div class="modal" id="cadastroModal" style="display: none;">
                <span class="close-modal" onclick="fecharModal()">✖</span>
                <form id="formCadastro" action="Controller/LoginController.php" method="POST">
                    <h2>Cadastro</h2>

                    <label for="cadUsuario">Nome de Usuário</label><br>
                    <input type="text" id="cadUsuario" name="usuario"><br>

                    <label for="cadEmail">Email</label><br>
                    <input type="text" id="cadEmail" name="email"><br>

                    <label for="cadSenha">Senha</label><br>
                    <input type="password" id="cadSenha" name="senha"><br>

                    <div class="modal-buttons">
                        <button type="submit" onclick="return validarCadastro()">Cadastrar</button>
                    </div>

                    <p>Já tem uma conta? <a href="#" onclick="abrirLogin()">Fazer login</a></p>
                    <?php
                    if (isset($_SESSION['erro_cadastro'])) {
                        echo '<p style="color: red;">' . $_SESSION['erro_cadastro'] . '</p>';
                        unset($_SESSION['erro_cadastro']);
                    }
                    if (isset($_SESSION['sucesso_cadastro'])) {
                        echo '<p style="color: green;">' . $_SESSION['sucesso_cadastro'] . '</p>';
                        unset($_SESSION['sucesso_cadastro']);
                    }
                    ?>
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
            }

            function abrirLogin() {
                document.getElementById('cadastroModal').style.display = 'none';
                document.getElementById('loginModal').style.display = 'block';
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