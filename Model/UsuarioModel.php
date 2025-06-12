<?php
class Usuario {
    private $id_usuario;
    private $nome_usuario;
    private $email_usuario;
    private $senha_usuario;
    private $data_nascimento;
    private $status_usuario; // 0 for normal user, 1 for admin
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Getters
    public function getIdUsuario() { return $this->id_usuario; }
    public function getNomeUsuario() { return $this->nome_usuario; }
    public function getEmailUsuario() { return $this->email_usuario; }
    public function getSenhaUsuario() { return $this->senha_usuario; }
    public function getDataNascimento() { return $this->data_nascimento; }
    public function getStatusUsuario() { return $this->status_usuario; }

    // Setters
    public function setIdUsuario($id) { $this->id_usuario = $id; }
    public function setNomeUsuario($nome) { $this->nome_usuario = $nome; }
    public function setEmailUsuario($email) { $this->email_usuario = $email; }
    public function setSenhaUsuario($senha) { $this->senha_usuario = $senha; }
    public function setDataNascimento($data) { $this->data_nascimento = $data; }
    public function setStatusUsuario($status) { $this->status_usuario = $status; }
}
?>