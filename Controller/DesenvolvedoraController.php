<?php
session_start();

require_once __DIR__ . '/../Model/DesenvolvedoraModel.php';
require_once __DIR__ . '/ConexaoBD.php';

class DesenvolvedoraController {
    private $desenvolvedoraModel;
    private $conn;

    public function __construct() {
        $mysqli = require __DIR__ . '/ConexaoBD.php';
        if (!$mysqli || !$mysqli instanceof mysqli) {
            die(json_encode(['error' => 'Erro: A conexão com o banco não foi estabelecida corretamente.']));
        }
        $this->conn = $mysqli;
        $this->desenvolvedoraModel = new Desenvolvedora($mysqli);
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

        $nomeDesenvolvedora = trim($_POST['nome_desenvolvedora']);

        // Check if developer already exists
        $stmtCheck = $this->conn->prepare("SELECT id_desenvolvedora FROM Desenvolvedora WHERE nome_desenvolvedora = ?");
        if (!$stmtCheck) {
            header('Location: ../View/ADM/CadastroDesenvolvedora.php?erro=' . urlencode('Erro ao preparar verificação: ' . $this->conn->error));
            exit();
        }
        $stmtCheck->bind_param("s", $nomeDesenvolvedora);
        $stmtCheck->execute();
        $stmtCheck->store_result();
        if ($stmtCheck->num_rows > 0) {
            $stmtCheck->close();
            header('Location: ../View/ADM/CadastroDesenvolvedora.php?erro=' . urlencode('Desenvolvedora já cadastrada'));
            exit();
        }
        $stmtCheck->close();

        // Insert new developer
        $stmt = $this->conn->prepare("INSERT INTO Desenvolvedora (nome_desenvolvedora) VALUES (?)");
        if (!$stmt) {
            header('Location: ../View/ADM/CadastroDesenvolvedora.php?erro=' . urlencode('Erro ao preparar inserção: ' . $this->conn->error));
            exit();
        }

        $stmt->bind_param("s", $nomeDesenvolvedora);

        if ($stmt->execute()) {
            header('Location: ../View/ADM/CadastroDesenvolvedora.php?msg=' . urlencode('Desenvolvedora cadastrada com sucesso!'));
        } else {
            header('Location: ../View/ADM/CadastroDesenvolvedora.php?erro=' . urlencode('Erro ao cadastrar desenvolvedora: ' . $stmt->error));
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

        $id_desenvolvedora = (int)$_POST['id_desenvolvedora'];
        $nome_desenvolvedora = trim($_POST['nome_desenvolvedora'] ?? '');

        if (empty($nome_desenvolvedora)) {
            echo json_encode(['success' => false, 'message' => 'Nome da desenvolvedora é obrigatório.']);
            exit();
        }

        // Check if developer already exists with a different ID (to avoid duplicates)
        $stmtCheck = $this->conn->prepare("SELECT id_desenvolvedora FROM Desenvolvedora WHERE nome_desenvolvedora = ? AND id_desenvolvedora != ?");
        if (!$stmtCheck) {
            echo json_encode(['success' => false, 'message' => 'Erro ao preparar verificação de duplicidade: ' . $this->conn->error]);
            exit();
        }
        $stmtCheck->bind_param("si", $nome_desenvolvedora, $id_desenvolvedora);
        $stmtCheck->execute();
        $stmtCheck->store_result();
        if ($stmtCheck->num_rows > 0) {
            $stmtCheck->close();
            echo json_encode(['success' => false, 'message' => 'Já existe uma desenvolvedora com este nome.']);
            exit();
        }
        $stmtCheck->close();

        $stmt = $this->conn->prepare("UPDATE Desenvolvedora SET nome_desenvolvedora = ? WHERE id_desenvolvedora = ?");
        if (!$stmt) {
            echo json_encode(['success' => false, 'message' => 'Erro ao preparar a query de atualização: ' . $this->conn->error]);
            exit();
        }

        $stmt->bind_param("si", $nome_desenvolvedora, $id_desenvolvedora);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Desenvolvedora atualizada com sucesso!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao atualizar desenvolvedora: ' . $stmt->error]);
        }
        $stmt->close();
        exit();
    }

    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET' || !isset($_GET['id_desenvolvedora'])) {
            header('Location: ../View/ADM/CadastroDesenvolvedora.php?erro=' . urlencode('Requisição inválida para exclusão'));
            exit();
        }

        if (!isset($_SESSION['id_usuario']) || $_SESSION['status_usuario'] != 1) {
            header('Location: ../View/ADM/CadastroDesenvolvedora.php?erro=' . urlencode('Acesso não autorizado.'));
            exit();
        }

        $id_desenvolvedora = (int)$_GET['id_desenvolvedora'];

        // Optionally, check for associated games before deleting
        // $stmtCheckGames = $this->conn->prepare("SELECT COUNT(*) FROM Jogo_Desenvolvedora WHERE id_desenvolvedora = ?");
        // $stmtCheckGames->bind_param("i", $id_desenvolvedora);
        // $stmtCheckGames->execute();
        // $stmtCheckGames->bind_result($gameCount);
        // $stmtCheckGames->fetch();
        // $stmtCheckGames->close();
        // if ($gameCount > 0) {
        //     header('Location: ../View/ADM/CadastroDesenvolvedora.php?erro=' . urlencode('Não é possível excluir: desenvolvedora associada a jogos.'));
        //     exit();
        // }

        $stmt = $this->conn->prepare("DELETE FROM Desenvolvedora WHERE id_desenvolvedora = ?");
        if (!$stmt) {
            header('Location: ../View/ADM/CadastroDesenvolvedora.php?erro=' . urlencode('Erro ao preparar exclusão: ' . $this->conn->error));
            exit();
        }

        $stmt->bind_param("i", $id_desenvolvedora);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                header('Location: ../View/ADM/CadastroDesenvolvedora.php?msg=' . urlencode('Desenvolvedora excluída com sucesso!'));
            } else {
                header('Location: ../View/ADM/CadastroDesenvolvedora.php?erro=' . urlencode('Desenvolvedora não encontrada ou já excluída.'));
            }
        } else {
            // Check for foreign key constraint violation
            if ($this->conn->errno == 1451) { // MySQL error code for foreign key constraint violation
                header('Location: ../View/ADM/CadastroDesenvolvedora.php?erro=' . urlencode('Não é possível excluir esta desenvolvedora porque há jogos associados a ela.'));
            } else {
                header('Location: ../View/ADM/CadastroDesenvolvedora.php?erro=' . urlencode('Erro ao excluir desenvolvedora: ' . $stmt->error));
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
    $controller = new DesenvolvedoraController();
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
            header('Location: ../View/ADM/CadastroDesenvolvedora.php?erro=' . urlencode('Ação inválida'));
            exit();
    }
} else {
    header('Location: ../View/ADM/CadastroDesenvolvedora.php?erro=' . urlencode('Ação não especificada'));
    exit();
}