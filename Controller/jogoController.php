<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../Model/JogoModel.php';
require_once __DIR__ . '/ConexaoBD.php';

class JogoController {
    private $jogo;
    private $conn;

    public function __construct() {
        $mysqli = require __DIR__ . '/ConexaoBD.php';
        if (!$mysqli || !$mysqli instanceof mysqli) {
            die('Erro: A conexão com o banco não foi estabelecida corretamente.');
        }
        $this->conn = $mysqli;
        $this->jogo = new Jogo($mysqli);
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /BitCritico/View/ADM/CadastroJogo.php?erro=' . urlencode('Método não permitido'));
            exit();
        }

        if (!isset($_POST['nome_jogo']) || !isset($_POST['descricao_jogo']) || !isset($_POST['genero_jogo']) ||
            !isset($_POST['desenvolvedora_jogo']) || !isset($_POST['plataforma_jogo']) || !isset($_FILES['capa_jogo'])) {
            header('Location: /BitCritico/View/ADM/CadastroJogo.php?erro=' . urlencode('Todos os campos são obrigatórios'));
            exit();
        }

        $this->jogo->setNomeJogo($_POST['nome_jogo']);
        $this->jogo->setDescricaoJogo($_POST['descricao_jogo']);

        $capa_jogo = $_FILES['capa_jogo'];
        $capa_nome = uniqid() . '.' . pathinfo($capa_jogo['name'], PATHINFO_EXTENSION);
        $capa_temp = $capa_jogo['tmp_name'];
        $capa_destino = __DIR__ . '/../View/images/' . $capa_nome;

        if (move_uploaded_file($capa_temp, $capa_destino)) {
            $this->jogo->setCapaJogo($capa_nome);

            // Inserir na tabela Jogo
            $stmt = $this->conn->prepare("INSERT INTO Jogo (nome_jogo, ano_lancamento_jogo descricao_jogo, capa_jogo) VALUES (?, ?, ?)");
            if (!$stmt) {
                unlink($capa_destino);
                header('Location: /BitCritico/View/ADM/CadastroJogo.php?erro=' . urlencode('Erro ao preparar query: ' . $this->conn->error));
                exit();
            }

            $nomeJogo = $this->jogo->getNomeJogo();
            $ano_lancamento_jogo = $this ->jogo->getAnoLancamentoJogo();
            $descricaoJogo = $this->jogo->getDescricaoJogo();
            $capaJogo = $this->jogo->getCapaJogo();
            $stmt->bind_param("sss", $nomeJogo, $ano_lancamento_jogo, $descricaoJogo, $capaJogo);

            if ($stmt->execute()) {
                $id_jogo = $this->conn->insert_id;

                // Inserir na tabela Jogo_Genero
                $stmt_genero = $this->conn->prepare("INSERT INTO Jogo_Genero (id_jogo, id_genero) VALUES (?, ?)");
                if (!$stmt_genero) {
                    $this->rollback($id_jogo, $capa_destino);
                    exit();
                }
                $idGenero = $_POST['genero_jogo'];
                $stmt_genero->bind_param("ii", $id_jogo, $idGenero);
                $stmt_genero->execute();
                $stmt_genero->close();

                // Inserir na tabela Jogo_Desenvolvedora
                $stmt_desenvolvedora = $this->conn->prepare("INSERT INTO Jogo_Desenvolvedora (id_jogo, id_desenvolvedora) VALUES (?, ?)");
                if (!$stmt_desenvolvedora) {
                    $this->rollback($id_jogo, $capa_destino);
                    exit();
                }
                $idDesenvolvedora = $_POST['desenvolvedora_jogo'];
                $stmt_desenvolvedora->bind_param("ii", $id_jogo, $idDesenvolvedora);
                $stmt_desenvolvedora->execute();
                $stmt_desenvolvedora->close();

                // Inserir na tabela Jogo_Plataforma
                $stmt_plataforma = $this->conn->prepare("INSERT INTO Jogo_Plataforma (id_jogo, id_plataforma) VALUES (?, ?)");
                if (!$stmt_plataforma) {
                    $this->rollback($id_jogo, $capa_destino);
                    exit();
                }
                $idPlataforma = $_POST['plataforma_jogo'];
                $stmt_plataforma->bind_param("ii", $id_jogo, $idPlataforma);
                $stmt_plataforma->execute();
                $stmt_plataforma->close();

                header('Location: /BitCritico/View/ADM/CadastroJogo.php?msg=' . urlencode('Jogo cadastrado com sucesso!'));
            } else {
                unlink($capa_destino);
                header('Location: /BitCritico/View/ADM/CadastroJogo.php?erro=' . urlencode('Erro ao cadastrar jogo: ' . $stmt->error));
            }
            $stmt->close();
        } else {
            header('Location: /BitCritico/View/ADM/CadastroJogo.php?erro=' . urlencode('Erro ao fazer upload da imagem'));
            exit();
        }
    }

    private function rollback($id_jogo, $capa_destino) {
        // Remover o jogo e a imagem em caso de erro
        $this->conn->query("DELETE FROM Jogo WHERE id_jogo = $id_jogo");
        unlink($capa_destino);
        header('Location: /BitCritico/View/ADM/CadastroJogo.php?erro=' . urlencode('Erro ao associar relacionamentos: ' . $this->conn->error));
        exit();
    }

    public function __destruct() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}

if (isset($_GET['action'])) {
    $controller = new JogoController();
    switch ($_GET['action']) {
        case 'create':
            $controller->create();
            break;
        default:
            header('Location: /BitCritico/View/ADM/CadastroJogo.php?erro=' . urlencode('Ação inválida'));
            exit();
    }
} else {
    header('Location: /BitCritico/View/ADM/CadastroJogo.php?erro=' . urlencode('Ação não especificada'));
    exit();
}
?>