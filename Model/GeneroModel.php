<?php
class Genero {
    private $id_genero;
    private $nome_genero;
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function setIdGenero($id) { $this->id_genero = $id; }
    public function getIdGenero() { return $this->id_genero; }
    public function setNomeGenero($nome) { $this->nome_genero = $nome; }
    public function getNomeGenero() { return $this->nome_genero; }
}
?>