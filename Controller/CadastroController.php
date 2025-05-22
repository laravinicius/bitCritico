<?php
header('Content-Type: application/json');

session_start();

require_once __DIR__ . '/../Controller/ConexaoBD.php';

$mysqli = require 'ConexaoBD.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'cadastro') {
    $nome = trim($_POST['usuario'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['senha'] ?? '');
    $dataCriacao = date('Y-m-d');

    if (empty($nome) || empty($email) || empty($senha)) {
        echo json_encode(['success' => false, 'message' => 'Por favor, preencha todos os campos.']);
        exit();
    }

    // Verifica duplicatas manualmente antes de inserir
    $stmtCheck = $mysqli->prepare("SELECT id_usuario FROM Usuario WHERE email_usuario = ? OR nome_usuario = ?");
    if ($stmtCheck === false) {
        echo json_encode(['success' => false, 'message' => 'Erro ao preparar a consulta: ' . $mysqli->error]);
        exit();
    }
    $stmtCheck->bind_param("ss", $email, $nome);
    $stmtCheck->execute();
    $stmtCheck->store_result();

    if ($stmtCheck->num_rows > 0) {
        $stmtCheck->close();
        // Verifica qual campo está duplicado
        $stmtEmail = $mysqli->prepare("SELECT id_usuario FROM Usuario WHERE email_usuario = ?");
        $stmtEmail->bind_param("s", $email);
        $stmtEmail->execute();
        $stmtEmail->store_result();
        if ($stmtEmail->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Este email já está cadastrado.']);
            $stmtEmail->close();
            exit();
        }
        $stmtEmail->close();

        echo json_encode(['success' => false, 'message' => 'Este nome de usuário já está em uso.']);
        exit();
    }

    $stmtCheck->close();

    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
    $stmt = $mysqli->prepare("INSERT INTO Usuario (nome_usuario, email_usuario, senha_usuario, data_criacao_usuario) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Erro ao preparar a query: ' . $mysqli->error]);
        exit();
    }

    $stmt->bind_param("ssss", $nome, $email, $senhaHash, $dataCriacao);

    if ($stmt->execute()) {
        $stmt->close();
        $stmt = $mysqli->prepare("SELECT id_usuario, nome_usuario, email_usuario FROM Usuario WHERE email_usuario = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $_SESSION['id_usuario'] = $user['id_usuario'];
        $_SESSION['nome_usuario'] = $user['nome_usuario'];
        $_SESSION['email_usuario'] = $user['email_usuario'];
        $stmt->close();
        echo json_encode(['success' => true]);
    } else {
        // Captura erro de duplicação caso a verificação inicial falhe
        if ($mysqli->errno === 1062) { // Código de erro para duplicação
            if (strpos($mysqli->error, 'nome_usuario') !== false) {
                echo json_encode(['success' => false, 'message' => 'Este nome de usuário já está em uso.']);
            } elseif (strpos($mysqli->error, 'email_usuario') !== false) {
                echo json_encode(['success' => false, 'message' => 'Este email já está cadastrado.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro de duplicação: ' . $mysqli->error]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao cadastrar usuário: ' . $stmt->error]);
        }
    }

    $stmt->close();
    $mysqli->close();
}
?>