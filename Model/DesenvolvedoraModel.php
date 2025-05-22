<?php
$db = require_once __DIR__ . '/../Controller/ConexaoBD.php';
$desenvolvedora = new Desenvolvedora($db);

class Desenvolvedora {
    private $conn;
    private $table = 'Desenvolvedora';

    // Propriedades correspondentes à tabela Desenvolvedora
    private $id_desenvolvedora;
    private $nome_desenvolvedora;

    // Construtor
    public function __construct($db) {
        $this->conn = $db;

        // Inicializa as propriedades com valores padrão
        $this->id_desenvolvedora = 0;
        $this->nome_desenvolvedora = '';
    }

    // Métodos Get e Set
    public function getIdDesenvolvedora() {
        return $this->id_desenvolvedora;
    }

    public function setIdDesenvolvedora($id_desenvolvedora) {
        $this->id_desenvolvedora = (int) $id_desenvolvedora;
    }

    public function getNomeDesenvolvedora() {
        return $this->nome_desenvolvedora;
    }

    public function setNomeDesenvolvedora($nome_desenvolvedora) {
        $this->nome_desenvolvedora = htmlspecialchars(strip_tags($nome_desenvolvedora));
    }
}
?>
