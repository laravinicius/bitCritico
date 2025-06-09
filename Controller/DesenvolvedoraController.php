<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../Model/DesenvolvedoraModel.php'; // Ajustado para DesenvolvedoraModel.php
require_once __DIR__ . '/ConexaoBD.php';

class DesenvolvedoraController {
    private $desenvolvedora;
    private $conn;

    public function __construct() {
        $mysqli = require __DIR__ . '/ConexaoBD.php';
        if (!$mysqli || !$mysqli instanceof mysqli) {
            die('Erro: A conexão com o banco não foi estabelecida corretamente.');
        }
        $this->conn = $mysqli;
        $this->desenvolvedora = new Desenvolvedora($mysqli);
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ../View/ADM/CadastroDesenvolvedora.php?erro=' . urlencode('Método não permitido'));
            exit();
        }

        if (!isset($_POST['nome_desenvolvedora']) || empty(trim($_POST['nome_desenvolvedora']))) {
            header('Location: ../View/ADM/CadastroDesenvolvedora.php?erro=' . urlencode('Nome da desenvolvedora é obrigatório'));
            exit();
        }

        $this->desenvolvedora->setNomeDesenvolvedora($_POST['nome_desenvolvedora']);

        $stmtCheck = $this->conn->prepare("SELECT id_desenvolvedora FROM Desenvolvedora WHERE nome_desenvolvedora = ?");
        if (!$stmtCheck) {
            header('Location: ../View/ADM/CadastroDesenvolvedora.php?erro=' . urlencode('Erro ao preparar verificação: ' . $this->conn->error));
            exit();
        }
        $stmtCheck->bind_param("s", $_POST['nome_desenvolvedora']);
        if (!$stmtCheck->execute()) {
            header('Location: ../View/ADM/CadastroDesenvolvedora.php?erro=' . urlencode('Erro ao executar verificação: ' . $stmtCheck->error));
            exit();
        }
        $stmtCheck->store_result();
        if ($stmtCheck->num_rows > 0) {
            $stmtCheck->close();
            header('Location: ../View/ADM/CadastroDesenvolvedora.php?erro=' . urlencode('Desenvolvedora já cadastrada'));
            exit();
        }
        $stmtCheck->close();

        $stmt = $this->conn->prepare("INSERT INTO Desenvolvedora (nome_desenvolvedora) VALUES (?)");
        if (!$stmt) {
            header('Location: ../View/ADM/CadastroDesenvolvedora.php?erro=' . urlencode('Erro ao preparar inserção: ' . $this->conn->error));
            exit();
        }

        $nomeDesenvolvedora = $this->desenvolvedora->getNomeDesenvolvedora();
        $stmt->bind_param("s", $nomeDesenvolvedora);

        if ($stmt->execute()) {
            header('Location: ../View/ADM/CadastroDesenvolvedora.php?msg=' . urlencode('Desenvolvedora cadastrada com sucesso!'));
        } else {
            header('Location: ../View/ADM/CadastroDesenvolvedora.php?erro=' . urlencode('Erro ao cadastrar desenvolvedora: ' . $stmt->error));
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

if (isset($_GET['action'])) {
    $controller = new DesenvolvedoraController();
    switch ($_GET['action']) {
        case 'create':
            $controller->create();
            break;
        default:
            header('Location: ../View/ADM/CadastroDesenvolvedora.php?erro=' . urlencode('Ação inválida'));
            exit();
    }
} else {
    header('Location: ../View/ADM/CadastroDesenvolvedora.php?erro=' . urlencode('Ação não especificada'));
    exit();
}
?>