<?php
$msg = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : '';
$erro = isset($_GET['erro']) ? htmlspecialchars($_GET['erro']) : '';

// Conexão com o banco para preencher os selects
$mysqli = require __DIR__ . '/../../Controller/ConexaoBD.php';

// Consulta para gêneros
$generos = [];
$result = $mysqli->query("SELECT id_genero, nome_genero FROM Genero ORDER BY nome_genero");
while ($row = $result->fetch_assoc()) {
    $generos[] = $row;
}
$result->free();

// Consulta para desenvolvedoras
$desenvolvedoras = [];
$result = $mysqli->query("SELECT id_desenvolvedora, nome_desenvolvedora FROM Desenvolvedora ORDER BY nome_desenvolvedora");
while ($row = $result->fetch_assoc()) {
    $desenvolvedoras[] = $row;
}
$result->free();

// Consulta para plataformas
$plataformas = [];
$result = $mysqli->query("SELECT id_plataforma, nome_plataforma FROM Plataforma ORDER BY nome_plataforma");
while ($row = $result->fetch_assoc()) {
    $plataformas[] = $row;
}
$result->free();

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Cadastrar Jogo</title>
    <link rel="stylesheet" href="../estilos/index.css">
    <style>
        .mensagem {
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }

        .sucesso {
            background-color: #28a745;
            color: white;
        }

        .erro {
            background-color: #dc3545;
            color: white;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .searchable-select {
            position: relative;
        }

        .searchable-select input {
            width: 100%;
            padding: 10px;
            margin-bottom: 5px;
        }

        .searchable-select select {
            width: 100%;
            padding: 10px;
        }
    </style>
</head>

<body>
    <header>
        <div class="logo"><a class="logo titulo" href="../../index.php">Bit Crítico</a></div>
        <div class="telas">
            <button class="login" onclick="window.location.href='AdminCenter023839.php'">Sessão Adm</button>

        </div>
    </header>

    <main style="display: flex; justify-content: center; align-items: center; min-height: 80vh;">
        <form action="../../Controller/JogoController.php?action=create" method="POST" enctype="multipart/form-data" style="border: 1px solid #ccc; padding: 30px; border-radius: 8px; background-color: #1f1f1f; color: var(--cor-texto);">
            <h2 style="margin-bottom: 20px;">Cadastro de Jogo</h2>

            <?php if ($msg): ?>
                <div class="mensagem sucesso"><?php echo $msg; ?></div>
            <?php endif; ?>
            <?php if ($erro): ?>
                <div class="mensagem erro"><?php echo $erro; ?></div>
            <?php endif; ?>

            <div class="form-group">
                <label for="nome_jogo">Nome do Jogo</label><br>
                <input type="text" id="nome_jogo" name="nome_jogo" style="width: 100%; padding: 10px;" required>
            </div>

            <div class="form-group">
                <label for="ano_lancamento_jogo">Ano de Lançamento do Jogo</label><br>
                <input type="number" id="ano_lancamento_jogo" name="ano_lancamento_jogo" style="width: 100%; padding: 10px;" required>
            </div>

            <div class="form-group">
                <label for="descricao_jogo">Descrição</label><br>
                <textarea id="descricao_jogo" name="descricao_jogo" style="width: 100%; padding: 10px; height: 100px;" required></textarea>
            </div>

            <div class="form-group searchable-select">
                <label for="genero_jogo">Gênero</label><br>
                <input type="text" class="search-input" placeholder="Pesquisar gênero..." onkeyup="filterOptions(this, 'genero_jogo')">
                <select id="genero_jogo" name="genero_jogo" style="width: 100%; padding: 10px;" required>
                    <option value="">Selecione um gênero</option>
                    <?php foreach ($generos as $genero): ?>
                        <option value="<?php echo htmlspecialchars($genero['id_genero']); ?>">
                            <?php echo htmlspecialchars($genero['nome_genero']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group searchable-select">
                <label for="desenvolvedora_jogo">Desenvolvedora</label><br>
                <input type="text" class="search-input" placeholder="Pesquisar desenvolvedora..." onkeyup="filterOptions(this, 'desenvolvedora_jogo')">
                <select id="desenvolvedora_jogo" name="desenvolvedora_jogo" style="width: 100%; padding: 10px;" required>
                    <option value="">Selecione uma desenvolvedora</option>
                    <?php foreach ($desenvolvedoras as $desenvolvedora): ?>
                        <option value="<?php echo htmlspecialchars($desenvolvedora['id_desenvolvedora']); ?>">
                            <?php echo htmlspecialchars($desenvolvedora['nome_desenvolvedora']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group searchable-select">
                <label for="plataforma_jogo">Plataforma</label><br>
                <input type="text" class="search-input" placeholder="Pesquisar plataforma..." onkeyup="filterOptions(this, 'plataforma_jogo')">
                <select id="plataforma_jogo" name="plataforma_jogo" style="width: 100%; padding: 10px;" required>
                    <option value="">Selecione uma plataforma</option>
                    <?php foreach ($plataformas as $plataforma): ?>
                        <option value="<?php echo htmlspecialchars($plataforma['id_plataforma']); ?>">
                            <?php echo htmlspecialchars($plataforma['nome_plataforma']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="capa_jogo">Capa do Jogo</label><br>
                <input type="file" id="capa_jogo" name="capa_jogo" style="width: 100%; padding: 10px;" accept="image/*" required>
            </div>

            <button type="submit" style="padding: 10px 20px; border-radius: 8px; background: var(--cor-botao); color: var(--cor-texto); border: 1px solid var(--cor-primaria); cursor: pointer;">Cadastrar</button>
        </form>
    </main>

    <footer class="rodape">
        <p>© 2025 Bit Crítico. Criado por Gabriel, Vinicius, Matheus, Davi, Eduardo.</p>
        <div class="midiaSocial">
            <a href="../../index.php">Bit Crítico</a>
            <a href="https://www.instagram.com/bit_critico?igsh=MW0zdTdxOGpwNnk4bw==">Instagram</a>
        </div>
    </footer>

    <script>
        function filterOptions(input, selectId) {
            const searchTerm = input.value.toLowerCase();
            const select = document.getElementById(selectId);
            const options = select.getElementsByTagName('option');

            for (let i = 0; i < options.length; i++) {
                const optionText = options[i].text.toLowerCase();
                if (optionText.includes(searchTerm) || options[i].value === '') {
                    options[i].style.display = '';
                } else {
                    options[i].style.display = 'none';
                }
            }
        }
    </script>
</body>

</html>