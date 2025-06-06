<?php
$msg = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : '';
$erro = isset($_GET['erro']) ? htmlspecialchars($_GET['erro']) : '';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Plataforma</title>
    <link rel="stylesheet" href="../estilos/index.css">
    <style>
        .mensagem {
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }
        .sucesso { background-color: #28a745; color: white; }
        .erro { background-color: #dc3545; color: white; }
    </style>
</head>
<body>
    <header>
        <div class="logo"><a class="logo titulo" href="/../index.php">Bit Crítico</a></div>
        <div class="telas">
            <button class="voltar" onclick="history.back()">⬅️</button>
        </div>
    </header>

    <main style="display: flex; justify-content: center; align-items: center; min-height: 80vh;">
        <form action="/BitCritico/Controller/PlataformaController.php?action=create" method="POST" style="border: 1px solid #ccc; padding: 30px; border-radius: 8px; background-color: #1f1f1f; color: var(--cor-texto);">
            <h2 style="margin-bottom: 20px;">Cadastro de Plataforma</h2>
            
            <?php if ($msg): ?>
                <div class="mensagem sucesso"><?php echo $msg; ?></div>
            <?php endif; ?>
            <?php if ($erro): ?>
                <div class="mensagem erro"><?php echo $erro; ?></div>
            <?php endif; ?>

            <label for="nome_plataforma">Plataforma</label><br>
            <input type="text" id="nome_plataforma" name="nome_plataforma" style="width: 100%; padding: 10px; margin-bottom: 20px;" required><br>

            <label for="empresa_plataforma">Empresa</label><br>
            <input type="text" id="empresa_plataforma" name="empresa_plataforma" style="width: 100%; padding: 10px; margin-bottom: 20px;" required><br>

            <button type="submit" style="padding: 10px 20px; border-radius: 8px; background: var(--cor-botao); color: var(--cor-texto); border: 1px solid var(--cor-primaria); cursor: pointer;">Cadastrar</button>
        </form>
    </main>

    <footer class="rodape">
        <p>© 2025 Bit Crítico. Criado por Gabriel, Vinicius, Matheus, Davi, Edu.</p>
        <div class="midiaSocial">
            <a href="/../index.php">Bit Critico</a>
            <a href="#">Instagram</a>
        </div>
    </footer>
</body>
</html>