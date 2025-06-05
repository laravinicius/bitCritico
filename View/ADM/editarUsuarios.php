<?php
session_start();

if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['status_usuario']) || $_SESSION['status_usuario'] != 1) {
    header('Location: ../../index.php');
    exit();
}

require_once '../../Controller/ConexaoBD.php';
$mysqli = require '../../Controller/ConexaoBD.php';

if (!$mysqli || !$mysqli instanceof mysqli) {
    die("Erro: Conexão com o banco falhou.");
}

// Processar edição de status, se houver POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_status'])) {
    $id_usuario = intval($_POST['id_usuario']);
    $novo_status = intval($_POST['status_usuario']);

    if ($novo_status == 0 || $novo_status == 1) {
        $stmt = $mysqli->prepare("UPDATE Usuario SET status_usuario = ? WHERE id_usuario = ?");
        if ($stmt) {
            $stmt->bind_param("ii", $novo_status, $id_usuario);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// Buscar todos os usuários
$resultadoUsuarios = $mysqli->query("SELECT id_usuario, nome_usuario, email_usuario, status_usuario FROM Usuario");
?>

<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Usuários - Bit Crítico</title>
    <link rel="stylesheet" href="../estilos/index.css">
    <link rel="icon" href="../assets/favicon.ico">
    <style>
        .user-table { width: 80%; margin: 20px auto; border-collapse: collapse; }
        .user-table th, .user-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .user-table th { background-color: #f2f2f2; }
        .user-table select { padding: 5px; }
    </style>
</head>
<body>
    <header>
        <div class="logo"><a class="logo titulo" href="AdminCenter023839.php">Admin Bit Crítico</a></div>
        <nav>
            <a class="teste" href="AdminCenter023839.php">Voltar</a>
        </nav>
    </header>

    <main>
        <h1>Gerenciar Usuários</h1>
        <p>Edite o status dos usuários abaixo:</p>

        <table class="user-table">
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Status</th>
                <th>Ação</th>
            </tr>
            <?php while ($usuario = $resultadoUsuarios->fetch_assoc()): ?>
                <tr>
                    <form method="POST" action="">
                        <input type="hidden" name="id_usuario" value="<?= htmlspecialchars($usuario['id_usuario']) ?>">
                        <td><?= htmlspecialchars($usuario['id_usuario']) ?></td>
                        <td><?= htmlspecialchars($usuario['nome_usuario']) ?></td>
                        <td><?= htmlspecialchars($usuario['email_usuario']) ?></td>
                        <td>
                            <select name="status_usuario" onchange="this.form.submit()">
                                <option value="0" <?= $usuario['status_usuario'] == 0 ? 'selected' : '' ?>>Usuário (0)</option>
                                <option value="1" <?= $usuario['status_usuario'] == 1 ? 'selected' : '' ?>>Administrador (1)</option>
                            </select>
                        </td>
                        <td><button type="submit" name="edit_status">Salvar</button></td>
                    </form>
                </tr>
            <?php endwhile; ?>
        </table>
    </main>

    <footer class="rodape">
        <p>© 2025 Bit Crítico. Criado por Gabriel, Vinicius, Matheus, Davi, Eduardo.</p>
    </footer>
</body>
</html>