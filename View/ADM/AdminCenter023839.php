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
        <div class="logo"><a class="logo titulo" href="../../index.php">Bit Crítico</a></div>
        <nav>
            
            <a class="teste" href="AdminCenter023839.php">Centro Administrativo</a>
        </nav>
        <div class="telas">
            <?php if (isset($_SESSION['id_usuario'])): ?>
                <button class="login" onclick="window.location.href='../Perfil.php'">Perfil</button>
            <?php endif; ?> 
        </div>
    </header>

    <div class="links-container">
        <a class="link-item" href="CadastroDesenvolvedora.php">Gerenciamento de Desenvolvedoras</a>
        <a class="link-item" href="CadastroGenero.php">Gerenciamento de Gêneros</a>
        <a class="link-item" href="CadastroJogo.php">Gerenciamento de Jogos</a>
        <a class="link-item" href="CadastroPlataforma.php">Gerenciamento de Plataformas</a>
        <a class="link-item" href="editarUsuarios.php">Gerenciamento de Usuários</a>
    </div>

    <footer class="rodape">
        <p>© 2025 Bit Crítico. Criado por Gabriel, Vinicius, Matheus, Davi, Eduardo.</p>
    </footer>
</body>

</html>