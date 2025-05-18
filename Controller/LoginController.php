<?php
session_start();

require_once __DIR__ . '/../Model/UsuarioModel.php';
require_once __DIR__ . '/ConexaoBD.php';

$mysqli = require 'ConexaoBD.php';
if (!$mysqli || !$mysqli instanceof mysqli) {
    die("Erro: Conexão inválida.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuarioInput = $_POST['usuario'] ?? '';
    $senha = $_POST['senha'] ?? '';

    $stmt = $mysqli->prepare("SELECT id_usuario, nome_usuario, email_usuario, senha_usuario FROM Usuario WHERE email_usuario = ?");
    if (!$stmt) {
        $_SESSION['erro_login'] = "Erro ao preparar query: " . $mysqli->error;
        header('Location: ../index.php');
        exit();
    }

    $stmt->bind_param("s", $usuarioInput);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($senha, $user['senha_usuario'])) {
            $_SESSION['id_usuario'] = $user['id_usuario'];
            $_SESSION['nome_usuario'] = $user['nome_usuario'];
            $_SESSION['email_usuario'] = $user['email_usuario'];
            header('Location: ../index.php');
            exit();
        } else {
            $_SESSION['erro_login'] = "Senha incorreta.";
        }
    } else {
        $_SESSION['erro_login'] = "Usuário ou email não encontrado.";
    }

    $stmt->close();
    header('Location: ../View/Perfil.php');
    exit();
}

$mysqli->close();
?>