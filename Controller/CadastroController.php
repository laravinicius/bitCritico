<?php
ob_clean();
header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/ConexaoBD.php';
$mysqli = require __DIR__ . '/ConexaoBD.php';

if (!$mysqli || !$mysqli instanceof mysqli) {
    echo json_encode(['success' => false, 'message' => 'Erro: Conexão com o banco falhou.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'cadastro') {
    $nome = trim($_POST['usuario'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['senha'] ?? '');
    $dataCriacao = date('Y-m-d');

    if (empty($nome) || empty($email) || empty($senha)) {
        echo json_encode(['success' => false, 'message' => 'Por favor, preencha todos os campos.']);
        exit();
    }

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
    $stmt = $mysqli->prepare("INSERT INTO Usuario (nome_usuario, email_usuario, senha_usuario, data_criacao_usuario, status_usuario) VALUES (?, ?, ?, ?, 0)");
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Erro ao preparar a query: ' . $mysqli->error]);
        exit();
    }

    $stmt->bind_param("ssss", $nome, $email, $senhaHash, $dataCriacao);

    if ($stmt->execute()) {
        $stmt->close();
        $stmt = $mysqli->prepare("SELECT id_usuario, nome_usuario, email_usuario, status_usuario FROM Usuario WHERE email_usuario = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $_SESSION['id_usuario'] = $user['id_usuario'];
        $_SESSION['nome_usuario'] = $user['nome_usuario'];
        $_SESSION['email_usuario'] = $user['email_usuario'];
        $_SESSION['status_usuario'] = $user['status_usuario']; // Inclui status_usuario
        $stmt->close();
        echo json_encode(['success' => true]);
    } else {
        if ($mysqli->errno === 1062) {
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
exit();
?>