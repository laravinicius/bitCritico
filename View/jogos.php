<?php
session_start();
include('../Controller/ConexaoBD.php');
// query para ordem alfabetica
$resultadoAlfabetico = $mysqli->query("SELECT * FROM Jogo ORDER BY nome_jogo");
//query para ordem de ano de lancamento
$resultadoAnoLancamento = $mysqli->query("SELECT * FROM Jogo ORDER BY ano_lancamento_jogo DESC"); 
// query para generos
$generoJogo = $mysqli->query("SELECT * FROM Genero");
// MAIS UMA QUERY PARA TOP 5 DO MÊS
//query para lista de generos

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>jogos - Bit Crítico</title>
    <link rel="stylesheet" href="./estilos/jogos.css">
    <link rel="icon" href="./assets/favicon.ico">
    <meta name="description" content="Site voltado para Reviews de jogos">
    <script src="./scripts/bot.js"></script>
    <script src="./scripts/cadastro.js"></script>
    <script src="./Teste.js/pesquisa.js"></script>

</head>

<body>
    <header>
        <!--Inicio das Telas de Cadastro e Login-->
        <div class="logo"><a class="logo titulo" href="../index.php">Bit Crítico</a></div>

        <nav class="search-bar">
            <form id="barra-pesquisa" method="get" onsubmit="return false;">
                <input type="text" id="pesquisa-jogo" name="q" placeholder="Pesquisar jogos..." required>
                <button type="submit">🔍</button>
            </form>
        </nav>

        <div class="telas">
            <button class="voltar" onclick="history.back()">⬅️</button>
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
        <section class="jogos game-reviews">
            <h2>De A - Z por:
                <select name="genero-filtro" id="genero-filtro">
                    <option value="" selected>Todos os gêneros</option>
                    
                </select>








                
                <select name="desenvolvedora-filtro" id="desenvolvedora-filtro">
                    <option value="" selected>Todas as desenvolvedoras</option>
                    <option value="valve">Valve</option>
                    <option value="rockstar">Rockstar</option>
                    <option value="bethesda">Bethesda</option>
                    <option value="ubisoft">Ubisoft</option>
                    <option value="ea">EA</option>
                </select>
            </h2>

            <div class="jogos-grid">
                <?php while ($jogo = $resultadoAlfabetico->fetch_assoc()): ?>

                    <a href="./detalhesJogo.php?id=<?= $jogo['id_jogo'] ?>">
                        <div class="jogos">
                            <img src="./images/<?= htmlspecialchars($jogo['capa_jogo']) ?>" alt="<?= htmlspecialchars($jogo['nome_jogo']) ?>">
                            <h3><?= htmlspecialchars($jogo['nome_jogo']) ?></h3>
                            <p>Desenvolvedora</p>
                            <p>Genero</p>
                            <p>nota</p>
                        </div>
                    <?php endwhile; ?>

                    </a>
            </div>

            <h2>Últimos lançamentos</h2>
            <div class="jogos-grid">
                <?php ($genero = $generoJogo->fetch_assoc())?>
                <?php while ($jogo = $resultadoAnoLancamento->fetch_assoc()): ?>
                <a href="./detalhesJogo.php?id=<?= $jogo['id_jogo'] ?>">
                    <div class="jogos">
                    <img src="./images/<?= htmlspecialchars($jogo['capa_jogo']) ?>" alt="<?= htmlspecialchars($jogo['nome_jogo']) ?>">
                    <h3><?= htmlspecialchars($jogo['nome_jogo']) ?></h3>                    
                    <p>Desenvolvedora</p>
                    <p><?= htmlspecialchars($genero['nome_genero'])?></p>
                    <p>Nota</p>
                    <p><?= htmlspecialchars($jogo['ano_lancamento_jogo'])?></p>

                    </div>
                    <?php endwhile; ?>

                </a>
            </div>

            <h2>TOP 5 DO MÊS</h2>
            <div class="jogos-grid">
                <a href="./detalhesJogo.php">
                    <div class="jogos" data-genero="acao" data-dev="rockstar">
                        <img src="jogos1.jpg" alt="jogos 1">
                        <h3>Nome do jogo 1</h3>
                        <p>Rockstar/Ação</p>
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
            <a href="../index.php">Bit Crítico</a>
            <a href="https://www.instagram.com/bit_critico?igsh=MW0zdTdxOGpwNnk4bw==">Instagram</a>
        </div>
    </footer>

    <!-- <script>
        const generoFiltro = document.getElementById('genero-filtro');
        const devFiltro = document.getElementById('desenvolvedora-filtro');
        const jogos = document.querySelectorAll('.jogos-grid .jogos');

        function filtrarJogos() {
            const generoSelecionado = generoFiltro.value.toLowerCase();
            const devSelecionada = devFiltro.value.toLowerCase();

            jogos.forEach(jogo => {
                const genero = jogo.dataset.genero?.toLowerCase() || "";
                const dev = jogo.dataset.dev?.toLowerCase() || "";

                const generoMatch = !generoSelecionado || genero === generoSelecionado;
                const devMatch = !devSelecionada || dev === devSelecionada;

                jogo.parentElement.style.display = (generoMatch && devMatch) ? 'block' : 'none';
            });
        }

        generoFiltro.addEventListener('change', filtrarJogos);
        devFiltro.addEventListener('change', filtrarJogos);

        const campoPesquisa = document.getElementById('pesquisa-jogo');

        campoPesquisa.addEventListener('input', () => {
            const termo = campoPesquisa.value.toLowerCase();
            const jogos = document.querySelectorAll('.jogos-grid .jogos');

        jogos.forEach(jogo => {
            const titulo = jogo.querySelector('h3')?.textContent.toLowerCase() || '';
            jogo.parentElement.style.display = titulo.includes(termo) ? 'block' : 'none';
            });
        });
    </script> -->
</body>

</html>