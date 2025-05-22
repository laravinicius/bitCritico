<?php
header('Content-Type: application/json');

session_start();

require_once __DIR__ . '/../Model/UsuarioModel.php';
require_once __DIR__ . '/ConexaoBD.php';

$mysqli = require './ConexaoBD.php';
if (!$mysqli || !$mysqli instanceof mysqli) {
    echo json_encode(['success' => false, 'message' => 'Erro: Conexão inválida.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'login') {
    $usuarioInput = trim($_POST['usuario'] ?? '');
    $senha = trim($_POST['senha'] ?? '');

    if (empty($usuarioInput) || empty($senha)) {
        echo json_encode(['success' => false, 'message' => 'Por favor, preencha todos os campos.']);
        exit();
    }

    // Consulta que verifica tanto email quanto nome de usuário
    $stmt = $mysqli->prepare("SELECT id_usuario, nome_usuario, email_usuario, senha_usuario FROM Usuario WHERE email_usuario = ? OR nome_usuario = ?");
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Erro ao preparar query: ' . $mysqli->error]);
        exit();
    }

    $stmt->bind_param("ss", $usuarioInput, $usuarioInput);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($senha, $user['senha_usuario'])) {
            $_SESSION['id_usuario'] = $user['id_usuario'];
            $_SESSION['nome_usuario'] = $user['nome_usuario'];
            $_SESSION['email_usuario'] = $user['email_usuario'];
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Senha incorreta.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Usuário ou email não encontrado.']);
    }

    $stmt->close();
}

$mysqli->close();
?>