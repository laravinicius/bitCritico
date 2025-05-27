<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../index.php');
    exit();
}

// Corrigido: carrega e recebe a conexão do banco
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

// Busca os jogos favoritos do usuário
$stmt_favoritos = $mysqli->prepare("SELECT j.nome_jogo, j.id_jogo FROM Favorito f JOIN Jogo j ON f.id_jogo = j.id_jogo WHERE f.id_usuario = ? LIMIT 3");
if (!$stmt_favoritos) {
    die("Erro ao preparar query de favoritos: " . $mysqli->error);
}
$stmt_favoritos->bind_param("i", $id_usuario);
$stmt_favoritos->execute();
$result_favoritos = $stmt_favoritos->get_result();
$favoritos = $result_favoritos->fetch_all(MYSQLI_ASSOC);

// Busca as plataformas associadas aos jogos favoritos
$plataformas = [];
if (!empty($favoritos)) {
    $jogos_ids = array_column($favoritos, 'id_jogo');
    $placeholders = implode(',', array_fill(0, count($jogos_ids), '?'));

    $stmt_plataformas = $mysqli->prepare("SELECT DISTINCT p.nome_plataforma FROM Jogo_Plataforma jp JOIN Plataforma p ON jp.id_plataforma = p.id_plataforma WHERE jp.id_jogo IN ($placeholders)");
    
    if ($stmt_plataformas) {
        $stmt_plataformas->bind_param(str_repeat('i', count($jogos_ids)), ...$jogos_ids);
        $stmt_plataformas->execute();
        $result_plataformas = $stmt_plataformas->get_result();
        $plataformas = $result_plataformas->fetch_all(MYSQLI_ASSOC);
        $stmt_plataformas->close();
    }
}

$stmt->close();
$stmt_favoritos->close();
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Perfil - Bit Crítico</title>
    <link rel="stylesheet" href="../View/estilos/index.css" />
</head>
<body>
    <header>
        <div class="logo"><a class="logo titulo" href="../index.php">Bit Crítico</a></div>
        <nav>
            <a class="teste" href="../View/jogos.php">Jogos A-Z</a>
        </nav>
        <div class="telas">
            <button class="voltar" onclick="history.back()">⬅️</button>
            <button class="login" onclick="window.location.href='../Controller/LogoutController.php'">Sair</a>
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
                        echo '<p>Nenhuma foto cadastrada.</p>';
                    } ?>
                </div>
            </div>

            <div class="perfil-secao">
                <h3>Meu top jogos:</h3>
                <div class="jogos-top">
                    <?php foreach ($favoritos as $jogo) : ?>
                        <div class="jogo-box"><?php echo htmlspecialchars($jogo['nome_jogo']); ?></div>
                    <?php endforeach; ?>
                    <?php for ($i = count($favoritos); $i < 3; $i++) : ?>
                        <div class="jogo-box"></div>
                    <?php endfor; ?>
                </div>
            </div>

            <div class="perfil-secao">
                <h3>Minhas plataformas:</h3>
                <div class="plataformas">
                    <?php foreach ($plataformas as $plataforma) : ?>
                        <div class="plataforma-box"><?php echo htmlspecialchars($plataforma['nome_plataforma']); ?></div>
                    <?php endforeach; ?>
                    <?php for ($i = count($plataformas); $i < 3; $i++) : ?>
                        <div class="plataforma-box"></div>
                    <?php endfor; ?>
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
</body>
</html>
