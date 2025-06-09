<?php
session_start();

// Inclui a conexão com o banco e trata possíveis erros
include('../Controller/ConexaoBD.php');

// Query para ordem de ano de lançamento
$resultadoAnoLancamento = $mysqli->query(
    "SELECT 
        j.id_jogo,
        j.nome_jogo,
        j.ano_lancamento_jogo,
        j.descricao_jogo,
        j.capa_jogo,
        j.trailer_jogo,
        g.nome_genero,
        d.nome_desenvolvedora
    FROM 
        Jogo j
    INNER JOIN 
        Jogo_Genero jg ON j.id_jogo = jg.id_jogo
    INNER JOIN 
        Genero g ON jg.id_genero = g.id_genero
    LEFT JOIN 
        Jogo_Desenvolvedora jd ON j.id_jogo = jd.id_jogo
    LEFT JOIN 
        Desenvolvedora d ON jd.id_desenvolvedora = d.id_desenvolvedora
    ORDER BY ano_lancamento_jogo DESC"
);

// Query para gêneros
$generoJogo = $mysqli->query("SELECT * FROM Genero");

// Query para desenvolvedoras
$desenvolvedoraJogo = $mysqli->query("SELECT * FROM Desenvolvedora");

// Query para ordem alfabética
$resultadoJogoGenero = $mysqli->query(
    "SELECT 
        j.id_jogo,
        j.nome_jogo,
        j.ano_lancamento_jogo,
        j.descricao_jogo,
        j.capa_jogo,
        j.trailer_jogo,
        g.nome_genero,
        d.nome_desenvolvedora
    FROM 
        Jogo j
    INNER JOIN 
        Jogo_Genero jg ON j.id_jogo = jg.id_jogo
    INNER JOIN 
        Genero g ON jg.id_genero = g.id_genero
    LEFT JOIN 
        Jogo_Desenvolvedora jd ON j.id_jogo = jd.id_jogo
    LEFT JOIN 
        Desenvolvedora d ON jd.id_desenvolvedora = d.id_desenvolvedora
    ORDER BY nome_jogo ASC"
);

// querry top 5

$resultadoTop5 = $mysqli->query(
    "SELECT 
        j.id_jogo,
        j.nome_jogo,
        j.capa_jogo,
        g.nome_genero,
        d.nome_desenvolvedora,
        COALESCE(AVG(r.nota_review), 0) as media_nota
    FROM 
        Jogo j
    INNER JOIN 
        Jogo_Genero jg ON j.id_jogo = jg.id_jogo
    INNER JOIN 
        Genero g ON jg.id_genero = g.id_genero
    LEFT JOIN 
        Jogo_Desenvolvedora jd ON j.id_jogo = jd.id_jogo
    LEFT JOIN 
        Desenvolvedora d ON jd.id_desenvolvedora = d.id_desenvolvedora
    LEFT JOIN 
        Review r ON j.id_jogo = r.id_jogo
    GROUP BY 
        j.id_jogo, j.nome_jogo, j.capa_jogo, g.nome_genero, d.nome_desenvolvedora
    ORDER BY 
        media_nota DESC
    LIMIT 5"
);
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
        <div class="logo"><a class="logo titulo" href="../index.php">Bit Crítico</a></div>

        <nav class="search-bar">
            <form id="barra-pesquisa" method="get" onsubmit="return false;">
                <input type="text" id="pesquisa-jogo" name="q" placeholder="Pesquisar jogos..." required>
                <button type="submit">🔍</button>
            </form>
        </nav>

        <div class="telas">
            <?php if (isset($_SESSION['id_usuario'])): ?>
                <button class="login" onclick="window.location.href='./Perfil.php'">Perfil</button>
                <?php if (isset($_SESSION['status_usuario']) && $_SESSION['status_usuario'] == 1): ?>
                    <button class="login" onclick="window.location.href='./ADM/AdminCenter023839.php'">Sessão Adm</button>
                <?php endif; ?>
                <button class="login" onclick="window.location.href='../Controller/LogoutController.php'">Sair</button>
            <?php else: ?>
                <button class="login" onclick="abrirModal()">Entrar</button>
            <?php endif; ?>
        </div>

        <div class="modal-bg" id="modalLogin">
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

            <div class="modal" id="cadastroModal" style="display: none;">
                <span class="close-modal" onclick="fecharModal()">✖</span>
                <form id="formCadastro" method="POST">
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

                fetch(`../Controller/${action === 'login' ? 'LoginController.php' : 'CadastroController.php'}`, {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = './Perfil.php';
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

            window.addEventListener('click', function (event) {
                const modal = document.getElementById('modalLogin');
                if (event.target === modal) {
                    fecharModal();
                }
            });

            window.addEventListener('keydown', function (event) {
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
                    <option value="">Todos os gêneros</option>
                    <?php
                    $generoJogo->data_seek(0); // Reset para reutilizar o resultado
                    while ($genero = $generoJogo->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($genero['nome_genero']) ?>"><?= htmlspecialchars($genero['nome_genero']) ?></option>
                    <?php endwhile; ?>
                </select>
                <select name="desenvolvedora-filtro" id="desenvolvedora-filtro">
                    <option value="">Todas as desenvolvedoras</option>
                    <?php
                    $desenvolvedoraJogo->data_seek(0); // Reset para reutilizar o resultado
                    while ($desenvolvedora = $desenvolvedoraJogo->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($desenvolvedora['nome_desenvolvedora']) ?>"><?= htmlspecialchars($desenvolvedora['nome_desenvolvedora']) ?></option>
                    <?php endwhile; ?>
                </select>
            </h2>

            <div class="jogos-grid">
                <?php
                $resultadoJogoGenero->data_seek(0); // Reset para reutilizar o resultado
                while ($jogo = $resultadoJogoGenero->fetch_assoc()): ?>
                    <a href="./detalhesJogo.php?id=<?= htmlspecialchars($jogo['id_jogo']) ?>">
                        <div class="jogos" data-genero="<?= htmlspecialchars($jogo['nome_genero']) ?>" data-dev="<?= htmlspecialchars($jogo['nome_desenvolvedora'] ?? 'Desconhecida') ?>">
                            <img src="./images/<?= htmlspecialchars($jogo['capa_jogo']) ?>" alt="<?= htmlspecialchars($jogo['nome_jogo']) ?>">
                            <h3><?= htmlspecialchars($jogo['nome_jogo']) ?></h3>
                            <p><?= htmlspecialchars($jogo['nome_desenvolvedora'] ?? 'Desconhecida') ?></p>
                            <p><?= htmlspecialchars($jogo['nome_genero']) ?></p>
                            <p>Nota</p>
                        </div>
                    </a>
                <?php endwhile; ?>
            </div>

            <h2>Últimos lançamentos</h2>
            <div class="jogos-grid">
                <?php
                $resultadoAnoLancamento->data_seek(0); // Reset para reutilizar o resultado
                while ($jogo = $resultadoAnoLancamento->fetch_assoc()): ?>
                    <a href="./detalhesJogo.php?id=<?= htmlspecialchars($jogo['id_jogo']) ?>">
                        <div class="jogos" data-genero="<?= htmlspecialchars($jogo['nome_genero']) ?>" data-dev="<?= htmlspecialchars($jogo['nome_desenvolvedora'] ?? 'Desconhecida') ?>">
                            <img src="./images/<?= htmlspecialchars($jogo['capa_jogo']) ?>" alt="<?= htmlspecialchars($jogo['nome_jogo']) ?>">
                            <h3><?= htmlspecialchars($jogo['nome_jogo']) ?></h3>
                            <p><?= htmlspecialchars($jogo['nome_desenvolvedora'] ?? 'Desconhecida') ?></p>
                            <p><?= htmlspecialchars($jogo['nome_genero']) ?></p>
                            <p><?= htmlspecialchars($jogo['ano_lancamento_jogo']) ?></p>
                        </div>
                    </a>
                <?php endwhile; ?>
            </div>

            <h2>TOP 5 DO MÊS</h2>
            <div class="jogos-grid">
                <?php while ($jogo = $resultadoTop5->fetch_assoc()): ?>
                    <a href="./detalhesJogo.php?id=<?= htmlspecialchars($jogo['id_jogo']) ?>">
                        <div class="jogos" data-genero="<?= htmlspecialchars($jogo['nome_genero']) ?>" data-dev="<?= htmlspecialchars($jogo['nome_desenvolvedora'] ?? '') ?>">
                            <img src="./images/<?= htmlspecialchars($jogo['capa_jogo']) ?>" alt="<?= htmlspecialchars($jogo['nome_jogo']) ?>">
                            <h3><?= htmlspecialchars($jogo['nome_jogo']) ?></h3>
                            <p><?= htmlspecialchars($jogo['nome_desenvolvedora'] ?? 'Desconhecida') ?></p>
                            <p><?= htmlspecialchars($jogo['nome_genero']) ?></p>
                            <p>Nota: <?= htmlspecialchars(number_format($jogo['media_nota'], 1, ',')) ?></p>
                        </div>
                    </a>
                <?php endwhile; ?>
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
    <script>
        const generoFiltro = document.getElementById('genero-filtro');
        const devFiltro = document.getElementById('desenvolvedora-filtro');
        const campoPesquisa = document.getElementById('pesquisa-jogo');
        const jogosGrids = document.querySelectorAll('.jogos-grid');

        function filtrarJogos() {
            const generoSelecionado = generoFiltro.value.toLowerCase().trim();
            const devSelecionada = devFiltro.value.toLowerCase().trim();
            const termoPesquisa = campoPesquisa.value.toLowerCase().trim();

            jogosGrids.forEach(grid => {
                const jogos = grid.querySelectorAll('.jogos');
                jogos.forEach(jogo => {
                    const genero = (jogo.dataset.genero || '').toLowerCase().trim();
                    const dev = (jogo.dataset.dev || '').toLowerCase().trim();
                    const titulo = (jogo.querySelector('h3')?.textContent || '').toLowerCase().trim();

                    const generoMatch = generoSelecionado === '' || genero === generoSelecionado;
                    const devMatch = devSelecionada === '' || dev === devSelecionada || dev === 'desconhecida';
                    const pesquisaMatch = termoPesquisa === '' || titulo.includes(termoPesquisa);

                    jogo.parentElement.style.display = (generoMatch && devMatch && pesquisaMatch) ? 'block' : 'none';
                });
            });
        }

        generoFiltro.addEventListener('change', filtrarJogos);
        devFiltro.addEventListener('change', filtrarJogos);
        campoPesquisa.addEventListener('input', filtrarJogos);

        // Executar o filtro inicialmente para garantir o estado correto
        filtrarJogos();
    </script>

</body>

</html>