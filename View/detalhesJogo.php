<?php
session_start();
include('../Controller/ConexaoBD.php');
$id_jogo = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$msg = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : '';
$erro = isset($_GET['erro']) ? htmlspecialchars($_GET['erro']) : '';

// Consultar o jogo
$stmtJogo = $mysqli->prepare("SELECT * FROM Jogo WHERE id_jogo = ?");
$stmtJogo->bind_param("i", $id_jogo);
$stmtJogo->execute();
$resultadoJogo = $stmtJogo->get_result();
if ($resultadoJogo->num_rows == 0) {
    echo "<h1>Jogo não encontrado</h1>";
    exit;
}
$jogo = $resultadoJogo->fetch_assoc();
$stmtJogo->close();

// Buscar reviews
$stmtReviews = $mysqli->prepare("SELECT r.*, u.nome_usuario 
                                FROM Review r 
                                JOIN Usuario u ON r.id_usuario = u.id_usuario 
                                WHERE r.id_jogo = ? 
                                ORDER BY r.data_review DESC");
$stmtReviews->bind_param("i", $id_jogo);
$stmtReviews->execute();
$resultadoReviews = $stmtReviews->get_result();
if ($resultadoReviews === false) {
    echo "<h1>Erro na consulta de reviews: " . htmlspecialchars($mysqli->error) . "</h1>";
    exit;
}

// Buscar gêneros
$stmtGeneros = $mysqli->prepare("SELECT g.nome_genero 
                                FROM Jogo_Genero jg 
                                JOIN Genero g ON jg.id_genero = g.id_genero 
                                WHERE jg.id_jogo = ?");
$stmtGeneros->bind_param("i", $id_jogo);
$stmtGeneros->execute();
$resultadoGeneros = $stmtGeneros->get_result();
$generos = [];
if ($resultadoGeneros === false) {
    echo "<h1>Erro na consulta de gêneros: " . htmlspecialchars($mysqli->error) . "</h1>";
    exit;
}
while ($row = $resultadoGeneros->fetch_assoc()) {
    $generos[] = $row['nome_genero'];
}
$stmtGeneros->close();

// Calcular média das notas
$stmtMedia = $mysqli->prepare("SELECT AVG(nota_review) as media FROM Review WHERE id_jogo = ?");
$stmtMedia->bind_param("i", $id_jogo);
$stmtMedia->execute();
$resultadoMedia = $stmtMedia->get_result();
$media = $resultadoMedia->fetch_assoc()['media'] ?? 'N/A';
$stmtMedia->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($jogo['nome_jogo']) ?> - BitCrítico</title>
    <link rel="stylesheet" href="./estilos/index.css">
    <style>
        .review-card {
            background-color: #2a2a2a;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            border: 1px solid #333;
        }
        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .review-header span {
            font-size: 0.9rem;
            color: #ccc;
        }
        .review-rating {
            font-size: 1.5rem;
            color: var(--cor-primaria);
            font-weight: bold;
        }
        .review-body {
            color: #ddd;
            margin-bottom: 10px;
        }
        .like-button {
            background-color: #444;
            color: #fff;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .like-button:hover {
            background-color: #555;
        }
        .mensagem {
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }
        .sucesso { background-color: #28a745; color: white; }
        .erro { background-color: #dc3545; color: white; }
    </style>
</head>
<body>
    <header>
        <div class="logo"><a class="logo titulo" href="../index.php">Bit Crítico</a></div>
        <nav>
            <a class="teste" href="./jogos.php">Jogos A-Z</a>
        </nav>

        <div class="telas">
            <?php if (isset($_SESSION['id_usuario'])): ?>
                <button class="login" onclick="window.location.href='./Perfil.php'">Perfil</button>
                <?php if (isset($_SESSION['status_usuario']) && $_SESSION['status_usuario'] === 1): ?>
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

                fetch(`./Controller/${action === 'login' ? 'LoginController.php' : 'CadastroController.php'}`, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = './DetalhesJogo.php?id=<?= $id_jogo; ?>';
                    } else {
                        errorDiv.textContent = data.message;
                        if (action === 'cadastro' && data.success === false && data.message !== 'Este email já está cadastrado.') {
                            successDiv.textContent = '';
                        }
                    }
                })
                .catch(error => {
                    errorDiv.textContent = 'Erro na requisição: ' . error.message;
                });

                return false;
            }

            // Verifica se o usuário está logado ao clicar no botão de deixar review
            function checkLoginAndRedirect() {
                <?php if (!isset($_SESSION['id_usuario'])): ?>
                    alert('Você precisa estar logado para deixar uma review. Faça login ou cadastre-se!');
                    abrirModal();
                <?php else: ?>
                    window.location.href = 'DeixarReview.php?id_jogo=<?= $id_jogo; ?>';
                <?php endif; ?>
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
        <a href="../index.php" style="color: var(--cor-primaria); text-decoration: none;">← Voltar</a>

        <div style="margin-top: 30px;">
            <img src="images/<?= htmlspecialchars($jogo['capa_jogo']) ?>" alt="Imagem do Jogo" style="width: 100%; border-radius: 10px; border: 2px solid #333;">
        </div>

        <h1 style="color: var(--cor-primaria); margin-top: 30px;"><?= htmlspecialchars($jogo['nome_jogo']) ?></h1>

        <p style="color: #ccc; margin: 10px 0 30px;">
            Gênero: <?= htmlspecialchars(implode(', ', $generos)) ?><br>
            <?= htmlspecialchars($jogo['descricao_jogo']) ?>
        </p>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <div>
                <strong style="color: var(--cor-destaque); font-size: 1.5rem;">Nota Média:</strong>
                <div style="font-size: 2rem; color: var(--cor-primaria);">
                    <?= $media !== 'N/A' ? number_format($media, 1) : 'N/A' ?>
                </div>
            </div>
        </div>

        <?php if (isset($_SESSION['id_usuario'])): ?>
            <button onclick="window.location.href='DeixarReview.php?id_jogo=<?= $id_jogo; ?>'" style="
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
        <?php else: ?>
            <button onclick="checkLoginAndRedirect()" style="
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
        <?php endif; ?>

        <!-- Exibir Reviews -->
        <h2 style="color: var(--cor-primaria); margin-top: 40px;">Reviews dos Usuários</h2>
        <?php if ($msg): ?>
            <div class="mensagem sucesso"><?php echo $msg; ?></div>
        <?php endif; ?>
        <?php if ($erro): ?>
            <div class="mensagem erro"><?php echo $erro; ?></div>
        <?php endif; ?>

        <?php if ($resultadoReviews->num_rows == 0): ?>
            <p style="color: #ccc;">Ainda não há reviews para este jogo.</p>
        <?php else: ?>
            <?php while ($review = $resultadoReviews->fetch_assoc()): ?>
                <div class="review-card">
                    <div class="review-header">
                        <span>Por: <?= htmlspecialchars($review['nome_usuario']) ?> em <?= date('d/m/Y', strtotime($review['data_review'])) ?></span>
                        <div class="review-rating"><?= number_format($review['nota_review'], 1) ?></div>
                    </div>
                    <div class="review-body">
                        <?= htmlspecialchars($review['descricao_review']) ?>
                    </div>
                    <div class="review-footer">
                        <button class="like-button" onclick="window.location.href='../Controller/ReviewController.php?action=like&id_review=<?= $review['id_review'] ?>&id_jogo=<?= $id_jogo ?>'">
                            👍 <?= $review['likes_review'] ?> Likes
                        </button>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
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