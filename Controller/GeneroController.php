<?php
session_start();

require_once __DIR__ . '/../Model/GeneroModel.php';
require_once __DIR__ . '/ConexaoBD.php';

class GeneroController {
    private $generoModel;
    private $conn;

    public function __construct() {
        $mysqli = require __DIR__ . '/ConexaoBD.php';
        if (!$mysqli || !$mysqli instanceof mysqli) {
            die(json_encode(['error' => 'Erro: A conexão com o banco não foi estabelecida corretamente.']));
        }
        $this->conn = $mysqli;
        $this->generoModel = new Genero($mysqli);
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ../View/ADM/CadastroGenero.php?erro=' . urlencode('Método não permitido'));
            exit();
        }

        if (!isset($_POST['nome_genero']) || empty(trim($_POST['nome_genero']))) {
            header('Location: ../View/ADM/CadastroGenero.php?erro=' . urlencode('Nome do gênero é obrigatório'));
            exit();
        }

        $nomeGenero = trim($_POST['nome_genero']);

        // Check if genre already exists
        $stmtCheck = $this->conn->prepare("SELECT id_genero FROM Genero WHERE nome_genero = ?");
        if (!$stmtCheck) {
            header('Location: ../View/ADM/CadastroGenero.php?erro=' . urlencode('Erro ao preparar verificação: ' . $this->conn->error));
            exit();
        }
        $stmtCheck->bind_param("s", $nomeGenero);
        $stmtCheck->execute();
        $stmtCheck->store_result();
        if ($stmtCheck->num_rows > 0) {
            $stmtCheck->close();
            header('Location: ../View/ADM/CadastroGenero.php?erro=' . urlencode('Gênero já cadastrado'));
            exit();
        }
        $stmtCheck->close();

        // Insert new genre
        $stmt = $this->conn->prepare("INSERT INTO Genero (nome_genero) VALUES (?)");
        if (!$stmt) {
            header('Location: ../View/ADM/CadastroGenero.php?erro=' . urlencode('Erro ao preparar inserção: ' . $this->conn->error));
            exit();
        }

        $stmt->bind_param("s", $nomeGenero);

        if ($stmt->execute()) {
            header('Location: ../View/ADM/CadastroGenero.php?msg=' . urlencode('Gênero cadastrado com sucesso!'));
        } else {
            header('Location: ../View/ADM/CadastroGenero.php?erro=' . urlencode('Erro ao cadastrar gênero: ' . $stmt->error));
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

        $id_genero = (int)$_POST['id_genero'];
        $nome_genero = trim($_POST['nome_genero'] ?? '');

        if (empty($nome_genero)) {
            echo json_encode(['success' => false, 'message' => 'Nome do gênero é obrigatório.']);
            exit();
        }

        // Check if genre already exists with a different ID (to avoid duplicates)
        $stmtCheck = $this->conn->prepare("SELECT id_genero FROM Genero WHERE nome_genero = ? AND id_genero != ?");
        if (!$stmtCheck) {
            echo json_encode(['success' => false, 'message' => 'Erro ao preparar verificação de duplicidade: ' . $this->conn->error]);
            exit();
        }
        $stmtCheck->bind_param("si", $nome_genero, $id_genero);
        $stmtCheck->execute();
        $stmtCheck->store_result();
        if ($stmtCheck->num_rows > 0) {
            $stmtCheck->close();
            echo json_encode(['success' => false, 'message' => 'Já existe um gênero com este nome.']);
            exit();
        }
        $stmtCheck->close();

        $stmt = $this->conn->prepare("UPDATE Genero SET nome_genero = ? WHERE id_genero = ?");
        if (!$stmt) {
            echo json_encode(['success' => false, 'message' => 'Erro ao preparar a query de atualização: ' . $this->conn->error]);
            exit();
        }

        $stmt->bind_param("si", $nome_genero, $id_genero);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Gênero atualizado com sucesso!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao atualizar gênero: ' . $stmt->error]);
        }
        $stmt->close();
        exit();
    }

    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET' || !isset($_GET['id_genero'])) {
            header('Location: ../View/ADM/CadastroGenero.php?erro=' . urlencode('Requisição inválida para exclusão'));
            exit();
        }

        if (!isset($_SESSION['id_usuario']) || $_SESSION['status_usuario'] != 1) {
            header('Location: ../View/ADM/CadastroGenero.php?erro=' . urlencode('Acesso não autorizado.'));
            exit();
        }

        $id_genero = (int)$_GET['id_genero'];

        $stmt = $this->conn->prepare("DELETE FROM Genero WHERE id_genero = ?");
        if (!$stmt) {
            header('Location: ../View/ADM/CadastroGenero.php?erro=' . urlencode('Erro ao preparar exclusão: ' . $this->conn->error));
            exit();
        }

        $stmt->bind_param("i", $id_genero);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                header('Location: ../View/ADM/CadastroGenero.php?msg=' . urlencode('Gênero excluído com sucesso!'));
            } else {
                header('Location: ../View/ADM/CadastroGenero.php?erro=' . urlencode('Gênero não encontrado ou já excluído.'));
            }
        } else {
            // Check for foreign key constraint violation (MySQL error code 1451)
            if ($this->conn->errno == 1451) {
                header('Location: ../View/ADM/CadastroGenero.php?erro=' . urlencode('Não é possível excluir este gênero porque há jogos associados a ele.'));
            } else {
                header('Location: ../View/ADM/CadastroGenero.php?erro=' . urlencode('Erro ao excluir gênero: ' . $stmt->error));
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
    $controller = new GeneroController();
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
            header('Location: ../View/ADM/CadastroGenero.php?erro=' . urlencode('Ação inválida'));
            exit();
    }
} else {
    header('Location: ../View/ADM/CadastroGenero.php?erro=' . urlencode('Ação não especificada'));
    exit();
}