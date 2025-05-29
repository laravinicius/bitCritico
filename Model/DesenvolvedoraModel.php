<?php
class Desenvolvedora {
    private $id_desenvolvedora;
    private $nome_desenvolvedora;
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function setIdDesenvolvedora($id) { $this->id_desenvolvedora = $id; }
    public function getIdDesenvolvedora() { return $this->id_desenvolvedora; }
    public function setNomeDesenvolvedora($nome) { $this->nome_desenvolvedora = $nome; }
    public function getNomeDesenvolvedora() { return $this->nome_desenvolvedora; }
}
?>