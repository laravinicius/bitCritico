<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/ConexaoBD.php';

class ReviewController {
    private $conn;

    public function __construct($mysqli) {
        $this->conn = $mysqli;
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /BitCritico/View/DeixarReview.php?erro=' . urlencode('Método não permitido') . '&id_jogo=' . (isset($_POST['id_jogo']) ? (int)$_POST['id_jogo'] : 0));
            exit();
        }

        if (!isset($_SESSION['id_usuario'])) {
            header('Location: /BitCritico/View/DeixarReview.php?erro=' . urlencode('Você precisa estar logado para deixar uma review') . '&id_jogo=' . (isset($_POST['id_jogo']) ? (int)$_POST['id_jogo'] : 0));
            exit();
        }

        if (!isset($_POST['id_jogo']) || !isset($_POST['nota_review']) || !isset($_POST['descricao_review'])) {
            header('Location: /BitCritico/View/DeixarReview.php?erro=' . urlencode('Todos os campos são obrigatórios') . '&id_jogo=' . (isset($_POST['id_jogo']) ? (int)$_POST['id_jogo'] : 0));
            exit();
        }

        $id_jogo = (int)$_POST['id_jogo'];
        $id_usuario = (int)$_SESSION['id_usuario'];
        $nota_review = floatval($_POST['nota_review']);
        $descricao_review = trim($_POST['descricao_review']);
        $data_review = date('Y-m-d'); // Formato correto: '2025-06-05'

        // Validar nota
        if ($nota_review < 0 || $nota_review > 10) {
            header('Location: /BitCritico/View/DeixarReview.php?erro=' . urlencode('A nota deve estar entre 0 e 10') . '&id_jogo=' . $id_jogo);
            exit();
        }

        // Verificar se o usuário já deixou uma review para este jogo
        $stmt_check = $this->conn->prepare("SELECT id_review FROM Review WHERE id_usuario = ? AND id_jogo = ?");
        $stmt_check->bind_param("ii", $id_usuario, $id_jogo);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        if ($result_check->num_rows > 0) {
            header('Location: /BitCritico/View/DeixarReview.php?erro=' . urlencode('Você já deixou uma review para este jogo') . '&id_jogo=' . $id_jogo);
            exit();
        }
        $stmt_check->close();

        // Inserir a review
        $stmt = $this->conn->prepare("INSERT INTO Review (id_usuario, id_jogo, nota_review, descricao_review, likes_review, data_review) VALUES (?, ?, ?, ?, 0, ?)");
        if (!$stmt) {
            header('Location: /BitCritico/View/DeixarReview.php?erro=' . urlencode('Erro ao preparar query: ' . $this->conn->error) . '&id_jogo=' . $id_jogo);
            exit();
        }

        // Corrigir o tipo de binding: 'd' (double) para 's' (string) para data_review
        $stmt->bind_param("iidss", $id_usuario, $id_jogo, $nota_review, $descricao_review, $data_review);
        if ($stmt->execute()) {
            header('Location: /BitCritico/View/DetalhesJogo.php?id=' . $id_jogo . '&msg=' . urlencode('Review enviada com sucesso!'));
        } else {
            header('Location: /BitCritico/View/DeixarReview.php?erro=' . urlencode('Erro ao salvar review: ' . $stmt->error) . '&id_jogo=' . $id_jogo);
        }
        $stmt->close();
    }

    public function likeReview() {
        if (!isset($_SESSION['id_usuario'])) {
            header('Location: /BitCritico/View/DetalhesJogo.php?erro=' . urlencode('Você precisa estar logado para dar like'));
            exit();
        }

        if (!isset($_GET['id_review']) || !isset($_GET['id_jogo'])) {
            header('Location: /BitCritico/View/DetalhesJogo.php?erro=' . urlencode('ID da review ou jogo não especificado'));
            exit();
        }

        $id_review = (int)$_GET['id_review'];
        $id_jogo = (int)$_GET['id_jogo'];
        $id_usuario = (int)$_SESSION['id_usuario'];

        // Verificar se o usuário já deu like
        $stmt_check = $this->conn->prepare("SELECT * FROM Like_Review WHERE id_usuario = ? AND id_review = ?");
        $stmt_check->bind_param("ii", $id_usuario, $id_review);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        if ($result_check->num_rows > 0) {
            header('Location: /BitCritico/View/DetalhesJogo.php?id=' . $id_jogo . '&erro=' . urlencode('Você já deu like nesta review'));
            exit();
        }
        $stmt_check->close();

        // Incrementar o contador de likes na tabela Review
        $stmt_update = $this->conn->prepare("UPDATE Review SET likes_review = likes_review + 1 WHERE id_review = ?");
        $stmt_update->bind_param("i", $id_review);
        $stmt_update->execute();
        $stmt_update->close();

        // Registrar o like na tabela Like_Review
        $stmt_insert = $this->conn->prepare("INSERT INTO Like_Review (id_usuario, id_review) VALUES (?, ?)");
        $stmt_insert->bind_param("ii", $id_usuario, $id_review);
        $stmt_insert->execute();
        $stmt_insert->close();

        header('Location: /BitCritico/View/DetalhesJogo.php?id=' . $id_jogo . '&msg=' . urlencode('Like registrado com sucesso!'));
    }

    public function __destruct() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}

if (isset($_GET['action'])) {
    $controller = new ReviewController($mysqli);
    switch ($_GET['action']) {
        case 'create':
            $controller->create();
            break;
        case 'like':
            $controller->likeReview();
            break;
        default:
            header('Location: /BitCritico/View/DetalhesJogo.php?erro=' . urlencode('Ação inválida'));
            exit();
    }
} else {
    header('Location: /BitCritico/View/DetalhesJogo.php?erro=' . urlencode('Ação não especificada'));
    exit();
}
?>