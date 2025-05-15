<?php
require_once __DIR__ . '/../Model/UsuarioModel.php';
require_once __DIR__ . '/../Control/ConexaoBD.php';

$mysqli = require 'ConexaoBD.php';
if (!$mysqli || !$mysqli instanceof mysqli) {
    die("Erro: A conexão com o banco não foi estabelecida corretamente.");
}

$usuario = new Usuario($mysqli);

$conn = $usuario->getConnection();
if (!$conn) {
    die("Erro: O objeto da conexão está inválido!");
}

$stmt = $conn->prepare(
    "INSERT INTO Usuario (nome_usuario, email_usuario, senha_usuario, foto_perfil_usuario, biografia_usuario, data_criacao_usuario)
    VALUES (?, ?, ?, ?, ?, ?)"
);

if (!$stmt) {
    die("Erro ao preparar query: " . $conn->error);
}

$dataCriacao = date('Y-m-d H:i:s');

// Configura os valores usando os métodos set da classe Usuario
$usuario->setNomeUsuario($_POST['nome_usuario']);
$usuario->setEmailUsuario($_POST['email_usuario']);
$usuario->setSenhaUsuario($_POST['senha_usuario']);
$usuario->setFotoPerfilUsuario($_POST['foto_perfil_usuario'] ?? '');
$usuario->setBiografiaUsuario($_POST['biografia_usuario'] ?? '');
$usuario->setDataCriacaoUsuario($dataCriacao);

// Armazena os valores em variáveis antes de passar para bind_param
$nomeUsuario = $usuario->getNomeUsuario();
$emailUsuario = $usuario->getEmailUsuario();
$senhaUsuario = $usuario->getSenhaUsuario();
$fotoPerfilUsuario = $usuario->getFotoPerfilUsuario();
$biografiaUsuario = $usuario->getBiografiaUsuario();
$dataCriacaoUsuario = $usuario->getDataCriacaoUsuario();

$stmt->bind_param(
    "ssssss",
    $nomeUsuario,
    $emailUsuario,
    $senhaUsuario,
    $fotoPerfilUsuario,
    $biografiaUsuario,
    $dataCriacaoUsuario
);

if ($stmt->execute()) {
    echo "Usuário cadastrado com sucesso!";
} else {
    echo "Erro ao cadastrar usuário: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>