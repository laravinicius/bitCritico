<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de jogos</title>
</head>
<body>
    <div>
        <h1>Cadastro de jogos</h1>
        <form action="teste.php" method="post" name="jogo">
            <label for="nome_jogo">Nome:</label>
            <input type="text" id="nome_jogo" name="nome_jogo">

            <label for="ano_lancamento_jogo">Ano lançamento:</label>
            <input type="number" id="ano_lancamento_jogo" name="ano_lancamento_jogo">

            <label for="descricao_jogo">Descrição: </label>
            <input type="text" id="descricao_jogo" name="descricao_jogo">

            <input type="submit" value="gravar" name="botao">
        </form>
    </div>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['botao'])) {
        include('../Control/ConexaoBD.php');
        $nome_jogo = mysqli_real_escape_string($mysqli, htmlspecialchars($_POST['nome_jogo']));
        $ano_lancamento_jogo = mysqli_real_escape_string($mysqli, htmlspecialchars($_POST['ano_lancamento_jogo']));
        $descricao_jogo = mysqli_real_escape_string($mysqli, htmlspecialchars($_POST['descricao_jogo']));
        
        $insere = "INSERT INTO Jogo (nome_jogo, ano_lancamento_jogo, descricao_jogo) VALUES ('$nome_jogo', '$ano_lancamento_jogo', '$descricao_jogo')";
        if (mysqli_query($mysqli, $insere)) {
            echo "<script>alert('Dados gravados com sucesso!');</script>";
        } else {
            echo "<script>alert('Erro ao gravar dados: " . mysqli_error($mysqli) . "');</script>";
        }
    }
    
    
    
    ?>
    
</body>
</html>