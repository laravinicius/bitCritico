<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../index.php');
    exit();
}

// Carrega a conexão do banco
$mysqli = require_once(__DIR__ . '/../Controller/ConexaoBD.php');

if (!$mysqli || !$mysqli instanceof mysqli) {
    die("Erro: Conexão inválida.");
}

// Busca informações do usuário
$id_usuario = $_SESSION['id_usuario'];
$stmt = $mysqli->prepare("SELECT nome_usuario, email_usuario, foto_perfil_usuario, biografia_usuario, data_criacao_usuario FROM Usuario WHERE id_usuario = ?");
if (!$stmt) {
    die("Erro ao preparar query: " . $mysqli->error);
}

$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    die("Usuário não encontrado.");
}

// Busca as últimas 3 reviews do usuário
$stmt_reviews = $mysqli->prepare("
    SELECT r.id_review, r.id_jogo, r.nota_review, r.descricao_review, r.likes_review, r.data_review, j.nome_jogo 
    FROM Review r 
    JOIN Jogo j ON r.id_jogo = j.id_jogo 
    WHERE r.id_usuario = ? 
    ORDER BY r.data_review DESC 
    LIMIT 3
");
if (!$stmt_reviews) {
    die("Erro ao preparar query de reviews: " . $mysqli->error);
}
$stmt_reviews->bind_param("i", $id_usuario);
$stmt_reviews->execute();
$result_reviews = $stmt_reviews->get_result();
$reviews = $result_reviews->fetch_all(MYSQLI_ASSOC);
$stmt_reviews->close();

// Busca as 3 reviews com mais likes do usuário
$stmt_reviews_likes = $mysqli->prepare("
    SELECT r.id_review, r.id_jogo, r.nota_review, r.descricao_review, r.likes_review, r.data_review, j.nome_jogo 
    FROM Review r 
    JOIN Jogo j ON r.id_jogo = j.id_jogo 
    WHERE r.id_usuario = ? 
    ORDER BY r.likes_review DESC, r.data_review DESC 
    LIMIT 3
");
if (!$stmt_reviews_likes) {
    die("Erro ao preparar query de reviews com mais likes: " . $mysqli->error);
}
$stmt_reviews_likes->bind_param("i", $id_usuario);
$stmt_reviews_likes->execute();
$result_reviews_likes = $stmt_reviews_likes->get_result();
$reviews_likes = $result_reviews_likes->fetch_all(MYSQLI_ASSOC);
$stmt_reviews_likes->close();

// Busca todas as reviews do usuário para a seção "Gerenciar minhas reviews"
$stmt_all_reviews = $mysqli->prepare("
    SELECT r.id_review, r.id_jogo, r.nota_review, r.descricao_review, r.likes_review, r.data_review, j.nome_jogo 
    FROM Review r 
    JOIN Jogo j ON r.id_jogo = j.id_jogo 
    WHERE r.id_usuario = ? 
    ORDER BY r.data_review DESC
");
if (!$stmt_all_reviews) {
    die("Erro ao preparar query de todas as reviews: " . $mysqli->error);
}
$stmt_all_reviews->bind_param("i", $id_usuario);
$stmt_all_reviews->execute();
$result_all_reviews = $stmt_all_reviews->get_result();
$all_reviews = $result_all_reviews->fetch_all(MYSQLI_ASSOC);
$stmt_all_reviews->close();

$stmt->close();
$mysqli->close();

$msg = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : '';
$erro = isset($_GET['erro']) ? htmlspecialchars($_GET['erro']) : '';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Perfil - Bit Crítico</title>
    <link rel="stylesheet" href="../View/estilos/index.css" />
    <link rel="stylesheet" href="../View/estilos/Perfil.css" />
</head>
<body>
    <header>
        <div class="logo"><a class="logo titulo" href="../index.php">Bit Crítico</a></div>
        <nav>
            <a class="teste" href="../View/jogos.php">Jogos A-Z</a>
        </nav>
        <div class="telas">
            <button class="voltar" onclick="history.back()">⬅️</button>
            <button class="login" onclick="window.location.href='../Controller/LogoutController.php'">Sair</button>
        </div>
    </header>

    <main class="conteudo-centralizado">
        <div class="form-box perfil">
            <h2>Perfil de Usuário</h2>
            <div class="perfil-container">
                <div class="perfil-info">
                    <p><strong>Nome:</strong> <?php echo htmlspecialchars($user['nome_usuario']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email_usuario']); ?></p>
                </div>
                <div class="perfil-foto">
                    <?php if ($user['foto_perfil_usuario']) {
                        echo '<img src="' . htmlspecialchars($user['foto_perfil_usuario']) . '" alt="Foto de Perfil" style="max-width: 200px;">';
                    } else {
                        echo '<p></p>';
                    } ?>
                </div>
            </div>

            <?php if ($msg): ?>
                <div class="mensagem sucesso"><?php echo $msg; ?></div>
            <?php endif; ?>
            <?php if ($erro): ?>
                <div class="mensagem erro"><?php echo $erro; ?></div>
            <?php endif; ?>

            <div class="perfil-secao">
                <h3>Minhas Reviews</h3>
                <?php if (empty($reviews)): ?>
                    <p>Você ainda não fez nenhuma review.</p>
                <?php else: ?>
                    <?php foreach ($reviews as $review): ?>
                        <div class="review-box">
                            <div class="review-title"><?php echo htmlspecialchars($review['nome_jogo']); ?></div>
                            <div class="review-content">
                                <div class="review-rating">Nota: <span class="nota"><?php echo number_format($review['nota_review'], 1); ?></span></div>
                                <div class="review-date">Data: <?php echo date('d/m/Y', strtotime($review['data_review'])); ?></div>
                                <div class="review-body"><?php echo htmlspecialchars($review['descricao_review']); ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="perfil-secao">
                <h3>Minhas Reviews com mais likes</h3>
                <?php if (empty($reviews_likes)): ?>
                    <p>Você ainda não tem reviews com likes.</p>
                <?php else: ?>
                    <?php foreach ($reviews_likes as $review): ?>
                        <div class="review-box">
                            <div class="review-title"><?php echo htmlspecialchars($review['nome_jogo']); ?></div>
                            <div class="review-content">
                                <div class="review-rating">Nota: <span class="nota"><?php echo number_format($review['nota_review'], 1); ?></span></div>
                                <div class="review-date">Likes: <?php echo $review['likes_review']; ?></div>
                                <div class="review-body"><?php echo htmlspecialchars($review['descricao_review']); ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="perfil-secao">
                <button class="manage-button" onclick="toggleManageReviews()">Gerenciar Minhas Reviews</button>
                <div id="manage-reviews-section">
                    <h3>Todas as Minhas Reviews</h3>
                    <?php if (empty($all_reviews)): ?>
                        <p>Você ainda não fez nenhuma review.</p>
                    <?php else: ?>
                        <?php foreach ($all_reviews as $review): ?>
                            <div class="review-box" id="review-<?php echo $review['id_review']; ?>">
                                <div class="review-title"><?php echo htmlspecialchars($review['nome_jogo']); ?></div>
                                <div class="review-content">
                                    <div class="review-rating">Nota: <span class="nota"><?php echo number_format($review['nota_review'], 1); ?></span></div>
                                    <div class="review-date">Data: <?php echo date('d/m/Y', strtotime($review['data_review'])); ?> | Likes: <?php echo $review['likes_review']; ?></div>
                                    <div class="review-body"><?php echo htmlspecialchars($review['descricao_review']); ?></div>
                                </div>
                                <div class="edit-form">
                                    <form method="POST" action="../Controller/ReviewController.php?action=update">
                                        <input type="hidden" name="id_review" value="<?php echo $review['id_review']; ?>">
                                        <input type="hidden" name="id_jogo" value="<?php echo $review['id_jogo']; ?>">
                                        <input type="hidden" name="source" value="perfil">
                                        <input type="number" name="nota_review" min="0" max="10" step="0.1" value="<?php echo htmlspecialchars($review['nota_review']); ?>" required>
                                        <textarea name="descricao_review" required><?php echo htmlspecialchars($review['descricao_review']); ?></textarea>
                                        <div class="review-actions">
                                            <button type="submit" class="action-button save-button">Salvar</button>
                                            <button type="button" class="action-button cancel-button" onclick="cancelEdit(<?php echo $review['id_review']; ?>)">Cancelar</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="review-actions view-mode">
                                    <button class="action-button edit-button" onclick="enableEdit(<?php echo $review['id_review']; ?>)">Editar</button>
                                    <button class="action-button delete-button" onclick="if(confirm('Tem certeza que deseja excluir esta review?')) window.location.href='../Controller/ReviewController.php?action=delete&id_review=<?php echo $review['id_review']; ?>'">Excluir</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
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
        function toggleManageReviews() {
            const section = document.getElementById('manage-reviews-section');
            section.style.display = section.style.display === 'none' || section.style.display === '' ? 'block' : 'none';
        }

        function enableEdit(reviewId) {
            const reviewBox = document.getElementById('review-' + reviewId);
            reviewBox.querySelector('.review-content').style.display = 'none';
            reviewBox.querySelector('.edit-form').style.display = 'block';
            reviewBox.querySelector('.view-mode').style.display = 'none';
        }

        function cancelEdit(reviewId) {
            const reviewBox = document.getElementById('review-' + reviewId);
            reviewBox.querySelector('.review-content').style.display = 'block';
            reviewBox.querySelector('.edit-form').style.display = 'none';
            reviewBox.querySelector('.view-mode').style.display = 'flex';
        }
    </script>
</body>
</html>