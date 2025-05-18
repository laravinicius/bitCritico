<?php
session_start();

require_once __DIR__ . '/../Controller/ConexaoBD.php';

$mysqli = require 'ConexaoBD.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = $_POST["usuario"] ?? '';
    $email = $_POST["email"] ?? '';
    $senha = $_POST["senha"] ?? '';
    $dataCriacao = date('Y-m-d');

    $stmtCheck = $mysqli->prepare("SELECT id_usuario FROM Usuario WHERE email_usuario = ?");
    if ($stmtCheck === false) {
        $_SESSION['erro_cadastro'] = "Erro ao preparar a consulta: " . $mysqli->error;
<<<<<<< HEAD
        header("Location: ../index.php");
=======
        header("Location: /bitCritico/index.php");
>>>>>>> parent of a9169b8 (Update CadastroController.php)
        exit();
    }
    $stmtCheck->bind_param("s", $email);
    $stmtCheck->execute();
    $stmtCheck->store_result();

    if ($stmtCheck->num_rows > 0) {
        $_SESSION['erro_cadastro'] = "Este email já está cadastrado.";
<<<<<<< HEAD
        header("Location: ../index.php");
=======
        header("Location: /bitCritico/index.php");
>>>>>>> parent of a9169b8 (Update CadastroController.php)
        exit();
    }

    $stmtCheck->close();

    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
    $stmt = $mysqli->prepare("INSERT INTO Usuario (nome_usuario, email_usuario, senha_usuario, data_criacao_usuario) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        $_SESSION['erro_cadastro'] = "Erro ao preparar a query: " . $mysqli->error;
<<<<<<< HEAD
        header("Location: ../index.php");
=======
        header("Location: /bitCritico/index.php");
>>>>>>> parent of a9169b8 (Update CadastroController.php)
        exit();
    }

    $stmt->bind_param("ssss", $nome, $email, $senhaHash, $dataCriacao);

    if ($stmt->execute()) {
        $_SESSION['sucesso_cadastro'] = "Usuário cadastrado com sucesso!";
<<<<<<< HEAD
        header("Location: ../index.php");
        exit();
    } else {
        $_SESSION['erro_cadastro'] = "Erro ao cadastrar usuário: " . $stmt->error;
        header("Location: ../index.php");
=======
        header("Location: /bitCritico/index.php");
        exit();
    } else {
        $_SESSION['erro_cadastro'] = "Erro ao cadastrar usuário: " . $stmt->error;
        header("Location: /bitCritico/index.php");
>>>>>>> parent of a9169b8 (Update CadastroController.php)
        exit();
    }

    $stmt->close();
    $mysqli->close();
}
?>
