<?php
class Plataforma {
    private $id_plataforma;
    private $nome_plataforma;
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function setIdPlataforma($id) { $this->id_plataforma = $id; }
    public function getIdPlataforma() { return $this->id_plataforma; }
    public function setNomePlataforma($nome) { $this->nome_plataforma = $nome; }
    public function getNomePlataforma() { return $this->nome_plataforma; }
}
?>