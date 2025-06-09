<?php
require_once __DIR__ . '/../Model/UsuarioModel.php';
require_once __DIR__ . '/ConexaoBD.php'; // Mesmo diretório

$mysqli = require 'ConexaoBD.php'; // Carregar conexão corretamente
if (!$mysqli || !$mysqli instanceof mysqli) {
    die("Erro: A conexão com o banco não foi estabelecida corretamente.");
}

$usuario = new Usuario($mysqli);

// Testando a conexão antes de preparar a query
$conn = $usuario->getConnection();
if (!$conn) {
    die("Erro: O objeto da conexão está inválido!");
}

// Teste para verificar se `prepare()` está retornando um objeto válido
$stmt = $conn->prepare(
    "INSERT INTO Usuario (nome_usuario, email_usuario, senha_usuario, foto_perfil_usuario, biografia_usuario, data_criacao_usuario)
    VALUES (?, ?, ?, ?, ?, ?)"
);

if (!$stmt) {
    die("Erro ao preparar query: " . $conn->error);
}

$dataCriacao = date('Y-m-d H:i:s');

$stmt->bind_param(
    "ssssss",
    $_POST['nome_usuario'],
    $_POST['email_usuario'],
    $_POST['senha_usuario'],
    $_POST['foto_perfil_usuario'],
    $_POST['biografia_usuario'],
    $dataCriacao // Agora a variável é passada corretamente
);

if ($stmt->execute()) {
    echo "Usuário cadastrado com sucesso!";
    // Redireciona para uma página após o cadastro (opcional)
    header('Location: ../View/ADM/AdminCenter023839.php');
    exit();
} else {
    $error = urlencode($stmt->error);
    echo "Erro ao cadastrar usuário: " . $stmt->error;
    // Redireciona para a página de edição de usuários com erro
    header('Location: ../View/ADM/editarUsuarios.php?erro=' . $error);
    exit();
}

$stmt->close();
$conn->close();
?>