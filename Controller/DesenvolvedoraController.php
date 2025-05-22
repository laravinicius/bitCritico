<?php
require_once __DIR__ . '/../Model/Desenvolvedora.php';
require_once __DIR__ . '/ConexaoBD.php';

class DesenvolvedoraController {
    private $desenvolvedora;
    private $conn;

    // Inicializa conexão e instância da classe Desenvolvedora
    public function __construct() {
        $mysqli = require __DIR__ . '/ConexaoBD.php';
        if (!$mysqli || !$mysqli instanceof mysqli) {
            die(json_encode(['error' => 'Erro: A conexão com o banco não foi estabelecida corretamente.']));
        }
        $this->conn = $mysqli;
        $this->desenvolvedora = new Desenvolvedora($mysqli);
    }

    // Cria uma nova desenvolvedora
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /bitCritico/ADM/CadastroDesenvolvedora.php?erro=' . urlencode('Método não permitido'));
            exit();
        }

        if (!isset($_POST['nome_desenvolvedora']) || empty(trim($_POST['nome_desenvolvedora']))) {
            header('Location: /bitCritico/ADM/CadastroDesenvolvedora.php?erro=' . urlencode('Nome da desenvolvedora é obrigatório'));
            exit();
        }

        $this->desenvolvedora->setNomeDesenvolvedora($_POST['nome_desenvolvedora']);

        $stmt = $this->conn->prepare("INSERT INTO Desenvolvedora (nome_desenvolvedora) VALUES (?)");
        if (!$stmt) {
            header('Location: /bitCritico/ADM/CadastroDesenvolvedora.php?erro=' . urlencode('Erro ao preparar query: ' . $this->conn->error));
            exit();
        }

        $nomeDesenvolvedora = $this->desenvolvedora->getNomeDesenvolvedora();
        $stmt->bind_param("s", $nomeDesenvolvedora);

        if ($stmt->execute()) {
            header('Location: /bitCritico/ADM/index.html?msg=' . urlencode('Desenvolvedora cadastrada com sucesso!'));
        } else {
            header('Location: /bitCritico/ADM/CadastroDesenvolvedora.php?erro=' . urlencode('Erro ao cadastrar desenvolvedora: ' . $stmt->error));
        }

        $stmt->close();
        exit();
    }

    // Lista todas as desenvolvedoras
    public function readAll() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['error' => 'Método não permitido']);
            return;
        }

        $stmt = $this->conn->prepare("SELECT * FROM Desenvolvedora");
        if (!$stmt) {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao preparar query: ' . $this->conn->error]);
            return;
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $desenvolvedoras = $result->fetch_all(MYSQLI_ASSOC);

        http_response_code(200);
        echo json_encode($desenvolvedoras);

        $stmt->close();
    }

    // Busca uma desenvolvedora por ID
    public function readOne($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['error' => 'Método não permitido']);
            return;
        }

        $this->desenvolvedora->setIdDesenvolvedora($id);
        $stmt = $this->conn->prepare("SELECT * FROM Desenvolvedora WHERE id_desenvolvedora = ?");
        if (!$stmt) {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao preparar query: ' . $this->conn->error]);
            return;
        }

        $idDesenvolvedora = $this->desenvolvedora->getIdDesenvolvedora();
        $stmt->bind_param("i", $idDesenvolvedora);
        $stmt->execute();
        $result = $stmt->get_result();
        $desenvolvedora = $result->fetch_assoc();

        if ($desenvolvedora) {
            http_response_code(200);
            echo json_encode($desenvolvedora);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Desenvolvedora não encontrada']);
        }

        $stmt->close();
    }

    // Atualiza uma desenvolvedora
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /bitCritico/ADM/CadastroDesenvolvedora.php?erro=' . urlencode('Método não permitido'));
            exit();
        }

        if (!isset($_POST['id_desenvolvedora']) || !isset($_POST['nome_desenvolvedora']) || empty(trim($_POST['nome_desenvolvedora']))) {
            header('Location: /bitCritico/ADM/CadastroDesenvolvedora.php?erro=' . urlencode('ID e nome da desenvolvedora são obrigatórios'));
            exit();
        }

        $this->desenvolvedora->setIdDesenvolvedora($_POST['id_desenvolvedora']);
        $this->desenvolvedora->setNomeDesenvolvedora($_POST['nome_desenvolvedora']);

        $stmt = $this->conn->prepare("UPDATE Desenvolvedora SET nome_desenvolvedora = ? WHERE id_desenvolvedora = ?");
        if (!$stmt) {
            header('Location: /bitCritico/ADM/CadastroDesenvolvedora.php?erro=' . urlencode('Erro ao preparar query: ' . $this->conn->error));
            exit();
        }

        $nomeDesenvolvedora = $this->desenvolvedora->getNomeDesenvolvedora();
        $idDesenvolvedora = $this->desenvolvedora->getIdDesenvolvedora();
        $stmt->bind_param("si", $nomeDesenvolvedora, $idDesenvolvedora);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                header('Location: /bitCritico/ADM/index.html?msg=' . urlencode('Desenvolvedora atualizada com sucesso'));
            } else {
                header('Location: /bitCritico/ADM/CadastroDesenvolvedora.php?erro=' . urlencode('Desenvolvedora não encontrada'));
            }
        } else {
            header('Location: /bitCritico/ADM/CadastroDesenvolvedora.php?erro=' . urlencode('Erro ao atualizar desenvolvedora: ' . $stmt->error));
        }

        $stmt->close();
        exit();
    }

    // Deleta uma desenvolvedora
    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /bitCritico/ADM/CadastroDesenvolvedora.php?erro=' . urlencode('Método não permitido'));
            exit();
        }

        // Verifica se a desenvolvedora está vinculada a jogos
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM Jogo_Desenvolvedora WHERE id_desenvolvedora = ?");
        if (!$stmt) {
            header('Location: /bitCritico/ADM/CadastroDesenvolvedora.php?erro=' . urlencode('Erro ao preparar query: ' . $this->conn->error));
            exit();
        }

        $this->desenvolvedora->setIdDesenvolvedora($id);
        $idDesenvolvedora = $this->desenvolvedora->getIdDesenvolvedora();
        $stmt->bind_param("i", $idDesenvolvedora);
        $stmt->execute();
        $count = $stmt->get_result()->fetch_row()[0];
        $stmt->close();

        if ($count > 0) {
            header('Location: /bitCritico/ADM/CadastroDesenvolvedora.php?erro=' . urlencode('Não é possível deletar: a desenvolvedora está vinculada a jogos'));
            exit();
        }

        $stmt = $this->conn->prepare("DELETE FROM Desenvolvedora WHERE id_desenvolvedora = ?");
        if (!$stmt) {
            header('Location: /bitCritico/ADM/CadastroDesenvolvedora.php?erro=' . urlencode('Erro ao preparar query: ' . $this->conn->error));
            exit();
        }

        $stmt->bind_param("i", $idDesenvolvedora);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                header('Location: /bitCritico/ADM/index.html?msg=' . urlencode('Desenvolvedora deletada com sucesso'));
            } else {
                header('Location: /bitCritico/ADM/CadastroDesenvolvedora.php?erro=' . urlencode('Desenvolvedora não encontrada'));
            }
        } else {
            header('Location: /bitCritico/ADM/CadastroDesenvolvedora.php?erro=' . urlencode('Erro ao deletar desenvolvedora: ' . $stmt->error));
        }

        $stmt->close();
        exit();
    }

    // Fecha a conexão
    public function __destruct() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}

// Roteamento básico
$controller = new DesenvolvedoraController();

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'create':
            $controller->create();
            break;
        case 'readAll':
            $controller->readAll();
            break;
        case 'readOne':
            if (isset($_GET['id'])) {
                $controller->readOne($_GET['id']);
            } else {
                header('Location: /bitCritico/ADM/CadastroDesenvolvedora.php?erro=' . urlencode('ID da desenvolvedora é obrigatório'));
            }
            break;
        case 'update':
            $controller->update();
            break;
        case 'delete':
            if (isset($_GET['id'])) {
                $controller->delete($_GET['id']);
            } else {
                header('Location: /bitCritico/ADM/CadastroDesenvolvedora.php?erro=' . urlencode('ID da desenvolvedora é obrigatório'));
            }
            break;
        default:
            header('Location: /bitCritico/ADM/CadastroDesenvolvedora.php?erro=' . urlencode('Ação inválida'));
            exit();
    }
} else {
    header('Location: /bitCritico/ADM/CadastroDesenvolvedora.php?erro=' . urlencode('Ação não especificada'));
    exit();
}
?>
