<?php
require_once __DIR__ . '/../Controller/ConexaoBD.php';

$mysqli = require 'ConexaoBD.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email_usuario"];
    $senha = $_POST["senha_usuario"];
    $dataCriacao = date('Y-m-d H:i:s');

    // Validar se email já está cadastrado"
    $stmtCheck = $mysqli->prepare("SELECT id_usuario FROM Usuario WHERE email_usuario = ?");
    $stmtCheck->bind_param("s", $email);
    $stmtCheck->execute();
    $stmtCheck->store_result();

    if ($stmtCheck->num_rows > 0) {
        header("Location: index.html?erro=1");
        exit();
    }

    $stmtCheck->close();

    // Cadastrar novo usuário
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
    $stmt = $mysqli->prepare("INSERT INTO Usuario (email_usuario, senha_usuario, data_criacao_usuario) VALUES (?, ?, ?)");
    
    if (!$stmt) {
        die("Erro ao preparar query: " . $mysqli->error);
    }

    $stmt->bind_param("sss", $email, $senhaHash, $dataCriacao);

    if ($stmt->execute()) {
        header("Location: index.html?sucesso=1");
        exit();
    } else {
        header("Location: index.html?erro=1");
        exit();
    }

    $stmt->close();
    $mysqli->close();
}
?>