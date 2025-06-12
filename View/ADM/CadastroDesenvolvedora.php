<?php
session_start();

// Check if user is logged in and is an administrator
if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['status_usuario']) || $_SESSION['status_usuario'] != 1) {
    header('Location: ../../index.php');
    exit();
}

$msg = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : '';
$erro = isset($_GET['erro']) ? htmlspecialchars($_GET['erro']) : '';

// Include database connection
require_once '../../Controller/ConexaoBD.php';
$mysqli = require '../../Controller/ConexaoBD.php';

if (!$mysqli || !$mysqli instanceof mysqli) {
    die("Erro: Conexão com o banco falhou.");
}

// Fetch all developers for listing
$developers = [];
$stmt = $mysqli->prepare("SELECT id_desenvolvedora, nome_desenvolvedora FROM Desenvolvedora ORDER BY nome_desenvolvedora ASC");
if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $developers[] = $row;
    }
    $stmt->close();
} else {
    $erro = "Erro ao buscar desenvolvedoras: " . $mysqli->error;
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar e Gerenciar Desenvolvedoras</title>
    <link rel="stylesheet" href="../estilos/index.css">
    <link rel="icon" href="../assets/favicon.ico">
    <style>
        .mensagem {
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }
        .sucesso { background-color: #28a745; color: white; }
        .erro { background-color: #dc3545; color: white; }
        .developer-table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }
        .developer-table th, .developer-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            color: var(--cor-texto);
        }
        .developer-table th {
            background-color: var(--cor-primaria);
            color: var(--cor-fundo);
        }
        .developer-table td {
            background-color: var(--cor-secundaria);
        }
        .developer-table .action-buttons button {
            padding: 5px 10px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            margin-right: 5px;
            transition: background-color 0.3s;
        }
        .developer-table .edit-button {
            background-color: var(--cor-hover);
            color: #fff;
        }
        .developer-table .edit-button:hover {
            background-color: #0088cc;
        }
        .developer-table .delete-button {
            background-color: var(--cor-destaque);
            color: #fff;
        }
        .developer-table .delete-button:hover {
            background-color: #cc005f;
        }
        .form-container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #1f1f1f;
            border: 1px solid #ccc;
            padding: 30px;
            border-radius: 8px;
            color: var(--cor-texto);
        }
    </style>
</head>
<body>
    <header>
        <div class="logo"><a class="logo titulo" href="../../index.php">Bit Crítico</a></div>
        <div class="telas">
            <button class="login" onclick="window.location.href='AdminCenter023839.php'">Sessão Adm</button>
        </div>
    </header>

    <main>
        <div class="form-container">
            <h2 style="margin-bottom: 20px; text-align: center;">Gerenciar Desenvolvedoras</h2>

            <?php if ($msg): ?>
                <div class="mensagem sucesso"><?php echo $msg; ?></div>
            <?php endif; ?>
            <?php if ($erro): ?>
                <div class="mensagem erro"><?php echo $erro; ?></div>
            <?php endif; ?>

            <?php if (empty($developers)): ?>
                <p style="text-align: center;">Nenhuma desenvolvedora cadastrada ainda.</p>
            <?php else: ?>
                <table class="developer-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome da Desenvolvedora</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($developers as $dev): ?>
                            <tr>
                                <td><?= htmlspecialchars($dev['id_desenvolvedora']) ?></td>
                                <td><?= htmlspecialchars($dev['nome_desenvolvedora']) ?></td>
                                <td class="action-buttons">
                                    <button class="edit-button" onclick="openEditModal(<?= htmlspecialchars(json_encode($dev)) ?>)">Editar</button>
                                    <button class="delete-button" onclick="confirmDelete(<?= $dev['id_desenvolvedora'] ?>)">Apagar</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div class="form-container">
            <h2 style="margin-bottom: 20px; text-align: center;">Cadastrar Nova Desenvolvedora</h2>
            <form action="../../Controller/DesenvolvedoraController.php?action=create" method="POST">
                <label for="nome_desenvolvedora">Nome da Desenvolvedora</label><br>
                <input type="text" id="nome_desenvolvedora" name="nome_desenvolvedora" style="width: 100%; padding: 10px; margin-bottom: 20px;" required><br>
                <button type="submit" style="padding: 10px 20px; border-radius: 8px; background: var(--cor-botao); color: var(--cor-texto); border: 1px solid var(--cor-primaria); cursor: pointer;">Cadastrar</button>
            </form>
        </div>
    </main>

    <footer class="rodape">
        <p>© 2025 Bit Crítico. Criado por Gabriel, Vinicius, Matheus, Davi, Eduardo.</p>
        <div class="midiaSocial">
            <a href="../../index.php">Bit Crítico</a>
            <a href="https://www.instagram.com/bit_critico?igsh=MW0zdTdxOGpwNnk4bw==">Instagram</a>
        </div>
    </footer>

    <div id="editDeveloperModal" class="modal-bg" style="display: none;">
        <div class="modal">
            <span class="close-modal" onclick="closeEditModal()">✖</span>
            <h2>Editar Desenvolvedora</h2>
            <form id="editDeveloperForm" method="POST" action="../../Controller/DesenvolvedoraController.php?action=update">
                <input type="hidden" id="edit_id_desenvolvedora" name="id_desenvolvedora">
                <label for="edit_nome_desenvolvedora">Nome da Desenvolvedora</label><br>
                <input type="text" id="edit_nome_desenvolvedora" name="nome_desenvolvedora" required><br>
                <div class="modal-buttons">
                    <button type="submit">Salvar Alterações</button>
                    <button type="button" onclick="closeEditModal()">Cancelar</button>
                </div>
                <div id="editError" style="color: red; margin-top: 10px;"></div>
                <div id="editSuccess" style="color: green; margin-top: 10px;"></div>
            </form>
        </div>
    </div>

    <script>
        function confirmDelete(id) {
            if (confirm("Tem certeza que deseja apagar esta desenvolvedora?")) {
                window.location.href = "../../Controller/DesenvolvedoraController.php?action=delete&id_desenvolvedora=" + id;
            }
        }

        function openEditModal(developer) {
            document.getElementById('edit_id_desenvolvedora').value = developer.id_desenvolvedora;
            document.getElementById('edit_nome_desenvolvedora').value = developer.nome_desenvolvedora;
            document.getElementById('editDeveloperModal').style.display = 'flex';
            document.getElementById('editError').textContent = ''; // Clear previous errors
            document.getElementById('editSuccess').textContent = ''; // Clear previous success messages
        }

        function closeEditModal() {
            document.getElementById('editDeveloperModal').style.display = 'none';
        }

        document.getElementById('editDeveloperForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent default form submission

            const form = event.target;
            const formData = new FormData(form);
            const errorDiv = document.getElementById('editError');
            const successDiv = document.getElementById('editSuccess');

            errorDiv.textContent = '';
            successDiv.textContent = '';

            fetch(form.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    successDiv.textContent = data.message;
                    setTimeout(() => {
                        window.location.reload(); // Reload to show updated list
                    }, 1500);
                } else {
                    errorDiv.textContent = data.message;
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                errorDiv.textContent = 'Erro na requisição: ' + error.message;
            });
        });

        // Close modal on outside click
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('editDeveloperModal');
            if (event.target === modal) {
                closeEditModal();
            }
        });

        // Close modal on Escape key
        window.addEventListener('keydown', function(event) {
            if (event.key === "Escape") {
                closeEditModal();
            }
        });
    </script>
</body>
</html>