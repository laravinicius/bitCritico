<?php
session_start(); // Inicia a sessão

require_once __DIR__ . '../Model/UsuarioModel.php';
require_once __DIR__ . './Controller/ConexaoBD.php';

// Carrega a conexão com o banco de dados
$mysqli = require './Controller/ConexaoBD.php';
if (!$mysqli || !$mysqli instanceof mysqli) {
    die("Erro: A conexão com o banco não foi estabelecida corretamente.");
}

// Instancia a classe Usuario
$usuario = new Usuario($mysqli);

// Obtém a conexão do objeto Usuario
$conn = $usuario->getConnection();
if (!$conn) {
    die("Erro: O objeto da conexão está inválido!");
}

// Verifica se os dados do formulário foram enviados
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuarioInput = $_POST['usuario'] ?? ''; // Email ou nome de usuário
    $senha = $_POST['senha'] ?? '';

    // Prepara a query para buscar o usuário pelo email ou nome de usuário
    $stmt = $conn->prepare(
        "SELECT id_usuario, nome_usuario, email_usuario, senha_usuario 
         FROM Usuario 
         WHERE email_usuario = ? OR nome_usuario = ?"
    );

    if (!$stmt) {
        $_SESSION['erro_login'] = "Erro ao preparar a query: " . $conn->error;
        header('Location: ../index.html');
        exit();
    }

    // Associa os parâmetros (o mesmo valor para email e nome de usuário)
    $stmt->bind_param("ss", $usuarioInput, $usuarioInput);

    // Executa a query
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Verifica se a senha está correta
            if (password_verify($senha, $user['senha_usuario'])) {
                // Login bem-sucedido: armazena os dados do usuário na sessão
                $_SESSION['id_usuario'] = $user['id_usuario'];
                $_SESSION['nome_usuario'] = $user['nome_usuario'];
                $_SESSION['email_usuario'] = $user['email_usuario'];

                // Redireciona para a página inicial
                header('Location: ../index.html');
                exit();
            } else {
                // Senha incorreta
                $_SESSION['erro_login'] = "Senha incorreta.";
            }
        } else {
            // Usuário não encontrado
            $_SESSION['erro_login'] = "Usuário ou email não encontrado.";
        }
    } else {
        $_SESSION['erro_login'] = "Erro ao executar a query: " . $stmt->error;
    }

    // Fecha o statement
    $stmt->close();
    
    // Redireciona de volta para a página de login com a mensagem de erro
    header('Location: ../index.html');
    exit();
}

// Fecha a conexão
$conn->close();
?>