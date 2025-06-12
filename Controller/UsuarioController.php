<?php
session_start();

require_once __DIR__ . '/../Model/UsuarioModel.php';
require_once __DIR__ . '/ConexaoBD.php';

class UsuarioController {
    private $usuarioModel;
    private $conn;

    public function __construct() {
        $mysqli = require __DIR__ . '/ConexaoBD.php';
        if (!$mysqli || !$mysqli instanceof mysqli) {
            die(json_encode(['success' => false, 'message' => 'Erro: A conexão com o banco não foi estabelecida corretamente.']));
        }
        $this->conn = $mysqli;
        $this->usuarioModel = new Usuario($mysqli);
    }

    // Existing create (cadastro) method from CadastroController.php
    public function cadastrarUsuario() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ../View/cadastro.php?erro=' . urlencode('Método de requisição inválido.'));
            exit();
        }

        $nome = trim($_POST['nome_usuario'] ?? '');
        $email = trim($_POST['email_usuario'] ?? '');
        $senha = $_POST['senha_usuario'] ?? '';
        $confirmarSenha = $_POST['confirmar_senha'] ?? '';
        $dataNascimento = $_POST['data_nascimento'] ?? '';

        if (empty($nome) || empty($email) || empty($senha) || empty($confirmarSenha) || empty($dataNascimento)) {
            header('Location: ../View/cadastro.php?erro=' . urlencode('Todos os campos são obrigatórios.'));
            exit();
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header('Location: ../View/cadastro.php?erro=' . urlencode('Formato de e-mail inválido.'));
            exit();
        }

        if ($senha !== $confirmarSenha) {
            header('Location: ../View/cadastro.php?erro=' . urlencode('As senhas não coincidem.'));
            exit();
        }

        // Check if email already exists
        $stmtCheck = $this->conn->prepare("SELECT id_usuario FROM Usuario WHERE email_usuario = ?");
        if (!$stmtCheck) {
            header('Location: ../View/cadastro.php?erro=' . urlencode('Erro ao preparar verificação de e-mail: ' . $this->conn->error));
            exit();
        }
        $stmtCheck->bind_param("s", $email);
        $stmtCheck->execute();
        $stmtCheck->store_result();
        if ($stmtCheck->num_rows > 0) {
            $stmtCheck->close();
            header('Location: ../View/cadastro.php?erro=' . urlencode('E-mail já cadastrado.'));
            exit();
        }
        $stmtCheck->close();

        $hashedPassword = password_hash($senha, PASSWORD_DEFAULT);

        $stmt = $this->conn->prepare("INSERT INTO Usuario (nome_usuario, email_usuario, senha_usuario, data_nascimento, status_usuario) VALUES (?, ?, ?, ?, 0)"); // Default status 0 for regular user
        if (!$stmt) {
            header('Location: ../View/cadastro.php?erro=' . urlencode('Erro ao preparar cadastro: ' . $this->conn->error));
            exit();
        }

        $stmt->bind_param("ssss", $nome, $email, $hashedPassword, $dataNascimento);

        if ($stmt->execute()) {
            header('Location: ../View/login.php?msg=' . urlencode('Cadastro realizado com sucesso! Faça login.'));
        } else {
            header('Location: ../View/cadastro.php?erro=' . urlencode('Erro ao cadastrar: ' . $stmt->error));
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

        // Only administrators can update other users
        if (!isset($_SESSION['id_usuario']) || $_SESSION['status_usuario'] != 1) {
            echo json_encode(['success' => false, 'message' => 'Acesso não autorizado.']);
            exit();
        }

        $id_usuario = (int)$_POST['id_usuario'];
        $nome_usuario = trim($_POST['nome_usuario'] ?? '');
        $email_usuario = trim($_POST['email_usuario'] ?? '');
        $status_usuario = (int)$_POST['status_usuario']; // 0 for standard, 1 for admin

        if (empty($nome_usuario) || empty($email_usuario) || !filter_var($email_usuario, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Nome e Email são obrigatórios e o Email deve ser válido.']);
            exit();
        }

        // Prevent admin from changing their own status to non-admin if they are the last admin
        if ($id_usuario == $_SESSION['id_usuario'] && $status_usuario == 0) {
            $stmtCountAdmins = $this->conn->prepare("SELECT COUNT(*) FROM Usuario WHERE status_usuario = 1");
            if (!$stmtCountAdmins) {
                echo json_encode(['success' => false, 'message' => 'Erro ao verificar administradores: ' . $this->conn->error]);
                exit();
            }
            $stmtCountAdmins->execute();
            $stmtCountAdmins->bind_result($adminCount);
            $stmtCountAdmins->fetch();
            $stmtCountAdmins->close();

            if ($adminCount <= 1) { // If currently logged-in admin is the only one
                echo json_encode(['success' => false, 'message' => 'Você não pode remover seu próprio status de administrador se for o único administrador.']);
                exit();
            }
        }

        // Check if email already exists for another user
        $stmtCheckEmail = $this->conn->prepare("SELECT id_usuario FROM Usuario WHERE email_usuario = ? AND id_usuario != ?");
        if (!$stmtCheckEmail) {
            echo json_encode(['success' => false, 'message' => 'Erro ao preparar verificação de e-mail: ' . $this->conn->error]);
            exit();
        }
        $stmtCheckEmail->bind_param("si", $email_usuario, $id_usuario);
        $stmtCheckEmail->execute();
        $stmtCheckEmail->store_result();
        if ($stmtCheckEmail->num_rows > 0) {
            $stmtCheckEmail->close();
            echo json_encode(['success' => false, 'message' => 'Este e-mail já está em uso por outro usuário.']);
            exit();
        }
        $stmtCheckEmail->close();

        $stmt = $this->conn->prepare("UPDATE Usuario SET nome_usuario = ?, email_usuario = ?, status_usuario = ? WHERE id_usuario = ?");
        if (!$stmt) {
            echo json_encode(['success' => false, 'message' => 'Erro ao preparar a query de atualização: ' . $this->conn->error]);
            exit();
        }

        $stmt->bind_param("ssii", $nome_usuario, $email_usuario, $status_usuario, $id_usuario);

        if ($stmt->execute()) {
            // If the current logged-in user changed their own status, update session
            if ($id_usuario == $_SESSION['id_usuario']) {
                $_SESSION['status_usuario'] = $status_usuario;
            }
            echo json_encode(['success' => true, 'message' => 'Usuário atualizado com sucesso!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao atualizar usuário: ' . $stmt->error]);
        }
        $stmt->close();
        exit();
    }

    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET' || !isset($_GET['id_usuario'])) {
            header('Location: ../View/ADM/editarUsuarios.php?erro=' . urlencode('Requisição inválida para exclusão'));
            exit();
        }

        if (!isset($_SESSION['id_usuario']) || $_SESSION['status_usuario'] != 1) {
            header('Location: ../View/ADM/editarUsuarios.php?erro=' . urlencode('Acesso não autorizado.'));
            exit();
        }

        $id_usuario_to_delete = (int)$_GET['id_usuario'];
        $current_admin_id = $_SESSION['id_usuario'];

        // Prevent an admin from deleting themselves
        if ($id_usuario_to_delete == $current_admin_id) {
            header('Location: ../View/ADM/editarUsuarios.php?erro=' . urlencode('Você não pode apagar seu próprio usuário.'));
            exit();
        }

        // Check if the user being deleted is the last admin
        $stmtCountAdmins = $this->conn->prepare("SELECT COUNT(*) FROM Usuario WHERE status_usuario = 1");
        if (!$stmtCountAdmins) {
            header('Location: ../View/ADM/editarUsuarios.php?erro=' . urlencode('Erro ao verificar administradores: ' . $this->conn->error));
            exit();
        }
        $stmtCountAdmins->execute();
        $stmtCountAdmins->bind_result($adminCount);
        $stmtCountAdmins->fetch();
        $stmtCountAdmins->close();

        $stmtCheckUserStatus = $this->conn->prepare("SELECT status_usuario FROM Usuario WHERE id_usuario = ?");
        if (!$stmtCheckUserStatus) {
            header('Location: ../View/ADM/editarUsuarios.php?erro=' . urlencode('Erro ao verificar status do usuário a ser excluído: ' . $this->conn->error));
            exit();
        }
        $stmtCheckUserStatus->bind_param("i", $id_usuario_to_delete);
        $stmtCheckUserStatus->execute();
        $stmtCheckUserStatus->bind_result($deletedUserStatus);
        $stmtCheckUserStatus->fetch();
        $stmtCheckUserStatus->close();

        if ($deletedUserStatus == 1 && $adminCount <= 1) {
            header('Location: ../View/ADM/editarUsuarios.php?erro=' . urlencode('Não é possível excluir o último administrador do sistema.'));
            exit();
        }

        // Start transaction for deletion (reviews depend on user)
        $this->conn->begin_transaction();

        try {
            // Delete reviews associated with the user
            $stmtDeleteReviews = $this->conn->prepare("DELETE FROM Review WHERE id_usuario = ?");
            if (!$stmtDeleteReviews) {
                throw new Exception("Erro ao preparar exclusão de reviews do usuário: " . $this->conn->error);
            }
            $stmtDeleteReviews->bind_param("i", $id_usuario_to_delete);
            $stmtDeleteReviews->execute();
            $stmtDeleteReviews->close();

            // Delete the user
            $stmt = $this->conn->prepare("DELETE FROM Usuario WHERE id_usuario = ?");
            if (!$stmt) {
                throw new Exception("Erro ao preparar exclusão do usuário: " . $this->conn->error);
            }

            $stmt->bind_param("i", $id_usuario_to_delete);

            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    $this->conn->commit();
                    header('Location: ../View/ADM/editarUsuarios.php?msg=' . urlencode('Usuário excluído com sucesso!'));
                } else {
                    $this->conn->rollback();
                    header('Location: ../View/ADM/editarUsuarios.php?erro=' . urlencode('Usuário não encontrado ou já excluído.'));
                }
            } else {
                throw new Exception('Erro ao excluir usuário: ' . $stmt->error);
            }
            $stmt->close();
            exit();

        } catch (Exception $e) {
            $this->conn->rollback();
            header('Location: ../View/ADM/editarUsuarios.php?erro=' . urlencode('Erro: ' . $e->getMessage()));
            exit();
        }
    }

    public function __destruct() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}

// Determine which action to call
$action = $_GET['action'] ?? null;

// Handle requests for "cadastro" action from CadastroController (since UsuarioController might merge it)
if ($action === 'cadastro') {
    $controller = new UsuarioController(); // Using UsuarioController for registration now
    $controller->cadastrarUsuario();
} else if ($action) {
    $controller = new UsuarioController();
    if (method_exists($controller, $action)) {
        $controller->$action();
    } else {
        header('Location: ../View/ADM/editarUsuarios.php?erro=' . urlencode('Ação inválida.'));
        exit();
    }
} else {
    // If no specific action is requested, redirect or show error
    // For this context, it will only be called with an action from forms/buttons
    // If someone accesses this controller directly without an action, redirect to admin center or error.
    header('Location: ../View/ADM/AdminCenter023839.php?erro=' . urlencode('Ação não especificada.'));
    exit();
}