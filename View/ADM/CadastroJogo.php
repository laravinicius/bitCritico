<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Jogo</title>
    <link rel="stylesheet" href="../estilos/index.css">
    
</head>
<body>
    <header>
        <div class="logo"><a class="logo titulo" href="/../index.php">Bit Crítico</a></div>
        <nav>
            <a class="teste" href="../jogos.php">Jogos A-Z</a>
        </nav>
        <div class="telas">
            <button class="voltar" onclick="history.back()">⬅️</button>
            <button class="login">Login</button>
        </div>
    </header>

    <main style="display: flex; justify-content: center; align-items: center; min-height: 80vh;">
        <form style="border: 1px solid #ccc; padding: 30px; border-radius: 8px; background-color: #1f1f1f; color: var(--cor-texto);">
            <h2 style="margin-bottom: 20px;">Cadastro de jogo</h2>
            <label for="nome">Nome:</label><br>
            <input type="text" id="nome" name="nome" style="width: 100%; padding: 10px; margin-bottom: 20px;"><br>

            <label for="descricao">Descrição:</label><br>
            <input type="text" id="descricao" name="descricao" style="width: 100%; padding: 10px; margin-bottom: 20px;"><br>

            <label for="genero">Gênero</label><br>
            <input type="text" id="genero" name="genero" style="width: 100%; padding: 10px; margin-bottom: 20px;"><br>

            <label for="desenvolvedora">Desenvolvedora:</label><br>
            <input type="text" id="desenvolvedora" name="desenvolvedora" style="width: 100%; padding: 10px; margin-bottom: 20px;"><br>

            <label for="imagem">Imagem:</label><br>
            <input type="text" id="imagem" Imagem="imagem" Imageme="width: 100%; padding: 10px; margin-bottom: 20px;"><br>

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
