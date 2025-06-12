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

// Fetch all genres for listing
$genres = [];
$stmt = $mysqli->prepare("SELECT id_genero, nome_genero FROM Genero ORDER BY nome_genero ASC");
if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $genres[] = $row;
    }
    $stmt->close();
} else {
    $erro = "Erro ao buscar gêneros: " . $mysqli->error;
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar e Gerenciar Gêneros</title>
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
        .genre-table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }
        .genre-table th, .genre-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            color: var(--cor-texto);
        }
        .genre-table th {
            background-color: var(--cor-primaria);
            color: var(--cor-fundo);
        }
        .genre-table td {
            background-color: var(--cor-secundaria);
        }
        .genre-table .action-buttons button {
            padding: 5px 10px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            margin-right: 5px;
            transition: background-color 0.3s;
        }
        .genre-table .edit-button {
            background-color: var(--cor-hover);
            color: #fff;
        }
        .genre-table .edit-button:hover {
            background-color: #0088cc;
        }
        .genre-table .delete-button {
            background-color: var(--cor-destaque);
            color: #fff;
        }
        .genre-table .delete-button:hover {
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

        /* Modal Styles */
        .modal-bg {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1000; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgba(0,0,0,0.7); /* Black w/ opacity */
            justify-content: center;
            align-items: center;
        }

        .modal {
            background-color: #2a2a2a;
            margin: auto;
            padding: 30px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 10px;
            position: relative;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            color: var(--cor-texto);
        }

        .modal h2 {
            margin-top: 0;
            color: var(--cor-primaria);
            text-align: center;
            margin-bottom: 20px;
        }

        .modal label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .modal input[type="text"] {
            width: calc(100% - 22px); /* Account for padding and border */
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #555;
            border-radius: 5px;
            background-color: #333;
            color: var(--cor-texto);
        }

        .modal-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .modal-buttons button {
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        .modal-buttons button[type="submit"] {
            background-color: var(--cor-primaria);
            color: white;
        }

        .modal-buttons button[type="button"] {
            background-color: #6c757d;
            color: white;
        }

        .modal-buttons button[type="submit"]:hover {
            background-color: #0069d9;
        }

        .modal-buttons button[type="button"]:hover {
            background-color: #5a6268;
        }

        .close-modal {
            position: absolute;
            top: 10px;
            right: 20px;
            font-size: 28px;
            font-weight: bold;
            color: #aaa;
            cursor: pointer;
        }

        .close-modal:hover,
        .close-modal:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
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
            <h2 style="margin-bottom: 20px; text-align: center;">Gerenciar Gêneros</h2>

            <?php if ($msg): ?>
                <div class="mensagem sucesso"><?php echo $msg; ?></div>
            <?php endif; ?>
            <?php if ($erro): ?>
                <div class="mensagem erro"><?php echo $erro; ?></div>
            <?php endif; ?>

            <?php if (empty($genres)): ?>
                <p style="text-align: center;">Nenhum gênero cadastrado ainda.</p>
            <?php else: ?>
                <table class="genre-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome do Gênero</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($genres as $gen): ?>
                            <tr>
                                <td><?= htmlspecialchars($gen['id_genero']) ?></td>
                                <td><?= htmlspecialchars($gen['nome_genero']) ?></td>
                                <td class="action-buttons">
                                    <button class="edit-button" onclick="openEditModal(<?= htmlspecialchars(json_encode($gen)) ?>)">Editar</button>
                                    <button class="delete-button" onclick="confirmDelete(<?= $gen['id_genero'] ?>)">Apagar</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div class="form-container">
            <h2 style="margin-bottom: 20px; text-align: center;">Cadastrar Novo Gênero</h2>
            <form action="../../Controller/GeneroController.php?action=create" method="POST">
                <label for="nome_genero">Nome do Gênero</label><br>
                <input type="text" id="nome_genero" name="nome_genero" style="width: 100%; padding: 10px; margin-bottom: 20px;" required><br>
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

    <div id="editGenreModal" class="modal-bg" style="display: none;">
        <div class="modal">
            <span class="close-modal" onclick="closeEditModal()">✖</span>
            <h2>Editar Gênero</h2>
            <form id="editGenreForm" method="POST" action="../../Controller/GeneroController.php?action=update">
                <input type="hidden" id="edit_id_genero" name="id_genero">
                <label for="edit_nome_genero">Nome do Gênero</label><br>
                <input type="text" id="edit_nome_genero" name="nome_genero" required><br>
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
            if (confirm("Tem certeza que deseja apagar este gênero?")) {
                window.location.href = "../../Controller/GeneroController.php?action=delete&id_genero=" + id;
            }
        }

        function openEditModal(genre) {
            document.getElementById('edit_id_genero').value = genre.id_genero;
            document.getElementById('edit_nome_genero').value = genre.nome_genero;
            document.getElementById('editGenreModal').style.display = 'flex';
            document.getElementById('editError').textContent = ''; // Clear previous errors
            document.getElementById('editSuccess').textContent = ''; // Clear previous success messages
        }

        function closeEditModal() {
            document.getElementById('editGenreModal').style.display = 'none';
        }

        document.getElementById('editGenreForm').addEventListener('submit', function(event) {
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
            const modal = document.getElementById('editGenreModal');
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