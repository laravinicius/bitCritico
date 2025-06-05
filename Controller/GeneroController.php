<?php
require_once __DIR__ . '/../Model/GeneroModel.php';
require_once __DIR__ . '/ConexaoBD.php';

class GeneroController {
    private $genero;
    private $conn;

    public function __construct() {
        $mysqli = require __DIR__ . '/ConexaoBD.php';
        if (!$mysqli || !$mysqli instanceof mysqli) {
            die(json_encode(['error' => 'Erro: A conexão com o banco não foi estabelecida corretamente.']));
        }
        $this->conn = $mysqli;
        $this->genero = new Genero($mysqli);
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /BitCritico/View/ADM/CadastroGenero.php?erro=' . urlencode('Método não permitido'));
            exit();
        }

        if (!isset($_POST['nome_genero']) || empty(trim($_POST['nome_genero']))) {
            header('Location: /BitCritico/View/ADM/CadastroGenero.php?erro=' . urlencode('Nome do gênero é obrigatório'));
            exit();
        }

        $this->genero->setNomeGenero($_POST['nome_genero']);

        $stmtCheck = $this->conn->prepare("SELECT id_genero FROM Genero WHERE nome_genero = ?");
        $stmtCheck->bind_param("s", $_POST['nome_genero']);
        $stmtCheck->execute();
        $stmtCheck->store_result();
        if ($stmtCheck->num_rows > 0) {
            $stmtCheck->close();
            header('Location: /BitCritico/View/ADM/CadastroGenero.php?erro=' . urlencode('Gênero já cadastrado'));
            exit();
        }
        $stmtCheck->close();

        $stmt = $this->conn->prepare("INSERT INTO Genero (nome_genero) VALUES (?)");
        if (!$stmt) {
            header('Location: /BitCritico/View/ADM/CadastroGenero.php?erro=' . urlencode('Erro ao preparar query: ' . $this->conn->error));
            exit();
        }

        $nomeGenero = $this->genero->getNomeGenero();
        $stmt->bind_param("s", $nomeGenero);

        if ($stmt->execute()) {
            header('Location: /BitCritico/View/ADM/CadastroGenero.php?msg=' . urlencode('Gênero cadastrado com sucesso!'));
        } else {
            header('Location: /BitCritico/View/ADM/CadastroGenero.php?erro=' . urlencode('Erro ao cadastrar gênero: ' . $stmt->error));
        }

        $stmt->close();
        exit();
    }

    public function __destruct() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}

// Chamar o método baseado na ação (deve ser gerenciado por um script externo, ex.: index.php)
if (isset($_GET['action'])) {
    $controller = new GeneroController();
    switch ($_GET['action']) {
        case 'create':
            $controller->create();
            break;
        default:
            header('Location: /BitCritico/View/ADM/CadastroGenero.php?erro=' . urlencode('Ação inválida'));
            exit();
    }
} else {
    header('Location: /BitCritico/View/ADM/CadastroGenero.php?erro=' . urlencode('Ação não especificada'));
    exit();
}
?>