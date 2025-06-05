<?php
session_start();

if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['status_usuario']) || $_SESSION['status_usuario'] != 1) {
    header('Location: ../../index.php');
    exit();
}
?>

<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bit Crítico</title>
    <link rel="stylesheet" href="../estilos/adm.css">
    <link rel="icon" href="../assets/favicon.ico">
</head>
<body>
    <header>
        <div class="logo"><a class="logo titulo" href="../../index.php">Admin Bit Crítico</a></div>
        <nav>
            <a class="teste" href="AdminCenter023839.php">Centro Administrativo</a>
        </nav>
    </header>

    <div class="links-container">
        <a class="link-item" href="CadastroDesenvolvedora.php">Cadastro de Desenvolvedora</a>
        <a class="link-item" href="CadastroGenero.php">Cadastro de Gênero</a>
        <a class="link-item" href="CadastroJogo.php">Cadastro de Jogo</a>
        <a class="link-item" href="CadastroPlataforma.php">Cadastro de Plataforma</a>
        <a class="link-item" href="editarUsuarios.php">Gerenciar Usuários</a>
    </div>

    <footer class="rodape">
        <p>© 2025 Bit Crítico. Criado por Gabriel, Vinicius, Matheus, Davi, Eduardo.</p>
    </footer>
</body>
</html>