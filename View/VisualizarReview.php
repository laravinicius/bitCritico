<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../index.php');
    exit();
}

$msg = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : '';
$erro = isset($_GET['erro']) ? htmlspecialchars($_GET['erro']) : '';
$id_review = isset($_GET['id_review']) ? (int)$_GET['id_review'] : 0;

if ($id_review == 0) {
    echo "<h1>ID da review não especificado</h1>";
    exit;
}

require_once '../Controller/ConexaoBD.php';
$stmt = $mysqli->prepare("
    SELECT r.id_review, r.id_jogo, r.nota_review, r.descricao_review, r.likes_review, r.data_review, j.nome_jogo 
    FROM Review r 
    JOIN Jogo j ON r.id_jogo = j.id_jogo 
    WHERE r.id_review = ? AND r.id_usuario = ?
");
$stmt->bind_param("ii", $id_review, $_SESSION['id_usuario']);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    echo "<h1>Review não encontrada ou você não tem permissão para visualizá-la</h1>";
    exit;
}
$review = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Visualizar Review - <?= htmlspecialchars($review['nome_jogo']) ?> - BitCrítico</title>
    <link rel="stylesheet" href="../estilos/index.css">
    <style>
        .review-container {
            max-width: 600px;
            margin: 60px auto;
            background-color: #111;
            border: 1px solid #333;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 10px #000;
        }
        .review-title {
            color: var(--cor-primaria);
            font-size: 2rem;
            margin-bottom: 10px;
        }
        .review-meta {
            color: #ccc;
            font-size: 1rem;
            margin-bottom: 20px;
        }
        .review-rating {
            font-size: 1.5rem;
            color: var(--cor-primaria);
            margin-bottom: 20px;
        }
        .review-body {
            color: var(--cor-texto);
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .review-actions {
            display: flex;
            gap: 10px;
        }
        .action-button {
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-family: 'Rajdhani', sans-serif;
            transition: background-color 0.3s;
        }
        .edit-button, .save-button, .cancel-button {
            background-color: var(--cor-destaque);
            color: #fff;
        }
        .edit-button:hover, .save-button:hover, .cancel-button:hover {
            background-color: var(--cor-hover);
        }
        .delete-button {
            background-color: #dc3545;
            color: #fff;
        }
        .delete-button:hover {
            background-color: #b02a37;
        }
        .mensagem {
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }
        .sucesso { background-color: #28a745; color: white; }
        .erro { background-color: #dc3545; color: white; }
        .edit-form {
            display: none;
            margin-top: 20px;
        }
        .edit-form input[type="number"],
        .edit-form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #333;
            background-color: #222;
            color: var(--cor-texto);
            font-family: 'Rajdhani', sans-serif;
        }
        .edit-form textarea {
            resize: vertical;
            min-height: 100px;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo"><a class="logo titulo" href="../index.php">Bit Crítico</a></div>
        <nav>
            <a class="teste" href="../jogos.php">Jogos A-Z</a>
        </nav>
        <div class="telas">
            <button class="login" onclick="window.location.href='./Perfil.php'">Perfil</button>
            <?php if (isset($_SESSION['status_usuario']) && $_SESSION['status_usuario'] == 1): ?>
                <button class="login" onclick="window.location.href='./ADM/AdminCenter023839.php'">Sessão Adm</button>
            <?php endif; ?>
            <button class="login" onclick="window.location.href='../Controller/LogoutController.php'">Sair</button>
        </div>
    </header>

    <main class="review-container">
        <a href="./Perfil.php" style="color: var(--cor-primaria); text-decoration: none;">← Voltar</a>

        <h2 class="review-title"><?= htmlspecialchars($review['nome_jogo']); ?></h2>
        <div class="review-meta">
            Data: <?= date('d/m/Y', strtotime($review['data_review'])); ?> | Likes: <?= $review['likes_review']; ?>
        </div>
        <div class="review-content">
            <div class="review-rating">Nota: <span class="nota"><?= number_format($review['nota_review'], 1); ?></span></div>
            <div class="review-body"><?= htmlspecialchars($review['descricao_review']); ?></div>
        </div>
        <div class="edit-form">
            <form method="POST" action="../Controller/ReviewController.php?action=update">
                <input type="hidden" name="id_review" value="<?= $review['id_review']; ?>">
                <input type="hidden" name="id_jogo" value="<?= $review['id_jogo']; ?>">
                <input type="number" name="nota_review" min="0" max="10" step="0.1" value="<?= htmlspecialchars($review['nota_review']); ?>" required>
                <textarea name="descricao_review" required><?= htmlspecialchars($review['descricao_review']); ?></textarea>
                <div class="review-actions">
                    <button type="submit" class="action-button save-button">Salvar</button>
                    <button type="button" class="action-button cancel-button" onclick="cancelEdit()">Cancelar</button>
                </div>
            </form>
        </div>

        <?php if ($msg): ?>
            <div class="mensagem sucesso"><?php echo $msg; ?></div>
        <?php endif; ?>
        <?php if ($erro): ?>
            <div class="mensagem erro"><?php echo $erro; ?></div>
        <?php endif; ?>

        <div class="review-actions view-mode">
            <button class="action-button edit-button" onclick="enableEdit()">Editar</button>
            <button class="action-button delete-button" onclick="if(confirm('Tem certeza que deseja excluir esta review?')) window.location.href='../Controller/ReviewController.php?action=delete&id_review=<?= $review['id_review']; ?>'">Excluir</button>
        </div>
    </main>

    <footer class="rodape">
        <p>© 2025 Bit Crítico. Criado por Gabriel, Vinicius, Matheus, Davi, Eduardo.</p>
        <div class="midiaSocial">
            <a href="../index.php">Bit Crítico</a>
            <a href="https://www.instagram.com/bit_critico?igsh=MW0zdTdxOGpwNnk4bw==">Instagram</a>
        </div>
    </footer>

    <script>
        function enableEdit() {
            document.querySelector('.review-content').style.display = 'none';
            document.querySelector('.edit-form').style.display = 'block';
            document.querySelector('.view-mode').style.display = 'none';
        }

        function cancelEdit() {
            document.querySelector('.review-content').style.display = 'block';
            document.querySelector('.edit-form').style.display = 'none';
            document.querySelector('.view-mode').style.display = 'flex';
        }
    </script>
</body>
</html>