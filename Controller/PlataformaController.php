<?php
session_start();

require_once __DIR__ . '/../Model/PlataformaModel.php';
require_once __DIR__ . '/ConexaoBD.php';

class PlataformaController {
    private $plataformaModel;
    private $conn;

    public function __construct() {
        $mysqli = require __DIR__ . '/ConexaoBD.php';
        if (!$mysqli || !$mysqli instanceof mysqli) {
            die(json_encode(['error' => 'Erro: A conexão com o banco não foi estabelecida corretamente.']));
        }
        $this->conn = $mysqli;
        $this->plataformaModel = new Plataforma($mysqli);
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ../View/ADM/CadastroPlataforma.php?erro=' . urlencode('Método não permitido'));
            exit();
        }

        if (!isset($_POST['nome_plataforma']) || empty(trim($_POST['nome_plataforma']))) {
            header('Location: ../View/ADM/CadastroPlataforma.php?erro=' . urlencode('Nome da plataforma é obrigatório'));
            exit();
        }

        $nomePlataforma = trim($_POST['nome_plataforma']);

        // Check if platform already exists
        $stmtCheck = $this->conn->prepare("SELECT id_plataforma FROM Plataforma WHERE nome_plataforma = ?");
        if (!$stmtCheck) {
            header('Location: ../View/ADM/CadastroPlataforma.php?erro=' . urlencode('Erro ao preparar verificação: ' . $this->conn->error));
            exit();
        }
        $stmtCheck->bind_param("s", $nomePlataforma);
        $stmtCheck->execute();
        $stmtCheck->store_result();
        if ($stmtCheck->num_rows > 0) {
            $stmtCheck->close();
            header('Location: ../View/ADM/CadastroPlataforma.php?erro=' . urlencode('Plataforma já cadastrada'));
            exit();
        }
        $stmtCheck->close();

        // Insert new platform
        $stmt = $this->conn->prepare("INSERT INTO Plataforma (nome_plataforma) VALUES (?)");
        if (!$stmt) {
            header('Location: ../View/ADM/CadastroPlataforma.php?erro=' . urlencode('Erro ao preparar inserção: ' . $this->conn->error));
            exit();
        }

        $stmt->bind_param("s", $nomePlataforma);

        if ($stmt->execute()) {
            header('Location: ../View/ADM/CadastroPlataforma.php?msg=' . urlencode('Plataforma cadastrada com sucesso!'));
        } else {
            header('Location: ../View/ADM/CadastroPlataforma.php?erro=' . urlencode('Erro ao cadastrar plataforma: ' . $stmt->error));
        }

        $stmt->close();
        exit();
    }

    public function update() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método não permitido.']);
            exit();
        }

        if (!isset($_SESSION['id_usuario']) || $_SESSION['status_usuario'] != 1) {
            echo json_encode(['success' => false, 'message' => 'Acesso não autorizado.']);
            exit();
        }

        $id_plataforma = (int)$_POST['id_plataforma'];
        $nome_plataforma = trim($_POST['nome_plataforma'] ?? '');

        if (empty($nome_plataforma)) {
            echo json_encode(['success' => false, 'message' => 'Nome da plataforma é obrigatório.']);
            exit();
        }

        // Check if platform already exists with a different ID (to avoid duplicates)
        $stmtCheck = $this->conn->prepare("SELECT id_plataforma FROM Plataforma WHERE nome_plataforma = ? AND id_plataforma != ?");
        if (!$stmtCheck) {
            echo json_encode(['success' => false, 'message' => 'Erro ao preparar verificação de duplicidade: ' . $this->conn->error]);
            exit();
        }
        $stmtCheck->bind_param("si", $nome_plataforma, $id_plataforma);
        $stmtCheck->execute();
        $stmtCheck->store_result();
        if ($stmtCheck->num_rows > 0) {
            $stmtCheck->close();
            echo json_encode(['success' => false, 'message' => 'Já existe uma plataforma com este nome.']);
            exit();
        }
        $stmtCheck->close();

        $stmt = $this->conn->prepare("UPDATE Plataforma SET nome_plataforma = ? WHERE id_plataforma = ?");
        if (!$stmt) {
            echo json_encode(['success' => false, 'message' => 'Erro ao preparar a query de atualização: ' . $this->conn->error]);
            exit();
        }

        $stmt->bind_param("si", $nome_plataforma, $id_plataforma);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Plataforma atualizada com sucesso!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao atualizar plataforma: ' . $stmt->error]);
        }
        $stmt->close();
        exit();
    }

    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET' || !isset($_GET['id_plataforma'])) {
            header('Location: ../View/ADM/CadastroPlataforma.php?erro=' . urlencode('Requisição inválida para exclusão'));
            exit();
        }

        if (!isset($_SESSION['id_usuario']) || $_SESSION['status_usuario'] != 1) {
            header('Location: ../View/ADM/CadastroPlataforma.php?erro=' . urlencode('Acesso não autorizado.'));
            exit();
        }

        $id_plataforma = (int)$_GET['id_plataforma'];

        $stmt = $this->conn->prepare("DELETE FROM Plataforma WHERE id_plataforma = ?");
        if (!$stmt) {
            header('Location: ../View/ADM/CadastroPlataforma.php?erro=' . urlencode('Erro ao preparar exclusão: ' . $this->conn->error));
            exit();
        }

        $stmt->bind_param("i", $id_plataforma);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                header('Location: ../View/ADM/CadastroPlataforma.php?msg=' . urlencode('Plataforma excluída com sucesso!'));
            } else {
                header('Location: ../View/ADM/CadastroPlataforma.php?erro=' . urlencode('Plataforma não encontrada ou já excluída.'));
            }
        } else {
            // Check for foreign key constraint violation (MySQL error code 1451)
            if ($this->conn->errno == 1451) {
                header('Location: ../View/ADM/CadastroPlataforma.php?erro=' . urlencode('Não é possível excluir esta plataforma porque há jogos associados a ela.'));
            } else {
                header('Location: ../View/ADM/CadastroPlataforma.php?erro=' . urlencode('Erro ao excluir plataforma: ' . $stmt->error));
            }
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

// Chamar o método baseado na ação
if (isset($_GET['action'])) {
    $controller = new PlataformaController();
    switch ($_GET['action']) {
        case 'create':
            $controller->create();
            break;
        case 'update':
            $controller->update();
            break;
        case 'delete':
            $controller->delete();
            break;
        default:
            header('Location: ../View/ADM/CadastroPlataforma.php?erro=' . urlencode('Ação inválida'));
            exit();
    }
} else {
    header('Location: ../View/ADM/CadastroPlataforma.php?erro=' . urlencode('Ação não especificada'));
    exit();
}