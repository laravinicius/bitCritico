<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../Model/PlataformaModel.php';
require_once __DIR__ . '/ConexaoBD.php';

class PlataformaController {
    private $plataforma;
    private $conn;

    public function __construct() {
        $mysqli = require __DIR__ . '/ConexaoBD.php';
        if (!$mysqli || !$mysqli instanceof mysqli) {
            die('Erro: A conexão com o banco não foi estabelecida corretamente.');
        }
        $this->conn = $mysqli;
        $this->plataforma = new Plataforma($mysqli);
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ../View/ADM/CadastroPlataforma.php?erro=' . urlencode('Método não permitido'));
            exit();
        }

        if (!isset($_POST['nome_plataforma']) || !isset($_POST['empresa_plataforma']) || 
            empty(trim($_POST['nome_plataforma'])) || empty(trim($_POST['empresa_plataforma']))) {
            header('Location: ../View/ADM/CadastroPlataforma.php?erro=' . urlencode('Nome e empresa da plataforma são obrigatórios'));
            exit();
        }

        $this->plataforma->setNomePlataforma($_POST['nome_plataforma']);
        $this->plataforma->setEmpresaPlataforma($_POST['empresa_plataforma']);

        $stmtCheck = $this->conn->prepare("SELECT id_plataforma FROM Plataforma WHERE nome_plataforma = ?");
        if (!$stmtCheck) {
            header('Location: ../View/ADM/CadastroPlataforma.php?erro=' . urlencode('Erro ao preparar verificação: ' . $this->conn->error));
            exit();
        }
        $stmtCheck->bind_param("s", $_POST['nome_plataforma']);
        if (!$stmtCheck->execute()) {
            header('Location: ../View/ADM/CadastroPlataforma.php?erro=' . urlencode('Erro ao executar verificação: ' . $stmtCheck->error));
            exit();
        }
        $stmtCheck->store_result();
        if ($stmtCheck->num_rows > 0) {
            $stmtCheck->close();
            header('Location: ../View/ADM/CadastroPlataforma.php?erro=' . urlencode('Plataforma já cadastrada'));
            exit();
        }
        $stmtCheck->close();

        $stmt = $this->conn->prepare("INSERT INTO Plataforma (nome_plataforma, empresa_plataforma) VALUES (?, ?)"); // Ajustado para 'empresa_plataforma'
        if (!$stmt) {
            header('Location: ../View/ADM/CadastroPlataforma.php?erro=' . urlencode('Erro ao preparar inserção: ' . $this->conn->error));
            exit();
        }

        $nomePlataforma = $this->plataforma->getNomePlataforma();
        $empresaPlataforma = $this->plataforma->getEmpresaPlataforma(); // Ajustado para getEmpresaPlataforma
        $stmt->bind_param("ss", $nomePlataforma, $empresaPlataforma);

        if ($stmt->execute()) {
            header('Location: ../View/ADM/CadastroPlataforma.php?msg=' . urlencode('Plataforma cadastrada com sucesso!'));
        } else {
            header('Location: ../View/ADM/CadastroPlataforma.php?erro=' . urlencode('Erro ao cadastrar plataforma: ' . $stmt->error));
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
    $controller = new PlataformaController();
    switch ($_GET['action']) {
        case 'create':
            $controller->create();
            break;
        default:
            header('Location: ../View/ADM/CadastroPlataforma.php?erro=' . urlencode('Ação inválida'));
            exit();
    }
} else {
    header('Location: ../View/ADM/CadastroPlataforma.php?erro=' . urlencode('Ação não especificada'));
    exit();
}
?>