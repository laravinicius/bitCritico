<?php
session_start();

$msg = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : '';
$erro = isset($_GET['erro']) ? htmlspecialchars($_GET['erro']) : '';
$id_jogo = isset($_GET['id_jogo']) ? (int)$_GET['id_jogo'] : 0;

if ($id_jogo == 0) {
    echo "<h1>ID do jogo não especificado</h1>";
    exit;
}

require_once '../Controller/ConexaoBD.php';
$stmtJogo = $mysqli->prepare("SELECT nome_jogo FROM Jogo WHERE id_jogo = ?");
$stmtJogo->bind_param("i", $id_jogo);
$stmtJogo->execute();
$resultadoJogo = $stmtJogo->get_result();
if ($resultadoJogo->num_rows == 0) {
    echo "<h1>Jogo não encontrado</h1>";
    exit;
}
$jogo = $resultadoJogo->fetch_assoc();
$stmtJogo->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Deixar Review - <?= htmlspecialchars($jogo['nome_jogo']) ?> - Bit Crítico</title>
    <link rel="stylesheet" href="../estilos/index.css">
    <style>
        .form-group {
            margin-bottom: 20px;
        }
        .form-box {
            max-width: 600px;
            margin: 60px auto;
            background-color: #111;
            border: 1px solid #333;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 10px #000;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: var(--cor-texto);
        }
        .form-group input[type="number"],
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #333;
            background-color: #222;
            color: var(--cor-texto);
            font-family: 'Rajdhani', sans-serif;
        }
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        button[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: var(--cor-destaque);
            color: #fff;
            font-size: 1.1rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button[type="submit"]:hover {
            background-color: var(--cor-hover);
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
            <a class="teste" href="../jogos.php">Jogos A-Z</a>
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
                    errorDiv.textContent = 'Erro na requisição: ' . error.message;
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
        <a href="/BitCritico/View/DetalhesJogo.php?id=<?= $id_jogo; ?>" style="color: var(--cor-primaria); text-decoration: none;">← Voltar</a>

        <h2 style="color: var(--cor-primaria); margin-top: 20px;">Deixar uma Review para <?= htmlspecialchars($jogo['nome_jogo']); ?></h2>

        <?php if ($msg): ?>
            <div class="mensagem sucesso"><?php echo $msg; ?></div>
        <?php endif; ?>
        <?php if ($erro): ?>
            <div class="mensagem erro"><?php echo $erro; ?></div>
        <?php endif; ?>

        <form method="POST" action="../Controller/ReviewController.php?action=create" style="margin-top: 10px;">
            <input type="hidden" name="id_jogo" value="<?= $id_jogo; ?>">

            <div class="form-group">
                <label for="nota_review">Nota (0 a 10):</label><br>
                <input type="number" id="nota_review" name="nota_review" min="0" max="10" step="0.1" value="5" required>
            </div>

            <div class="form-group">
                <label for="descricao_review">Sua Opinião:</label><br>
                <textarea id="descricao_review" name="descricao_review" rows="5" style="width: 100%; padding: 10px;" required></textarea>
            </div>

            <button type="submit">
                Enviar Review
            </button>
        </form>
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