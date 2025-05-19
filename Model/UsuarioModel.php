<?php
$db = require_once __DIR__ . '/../Controller/ConexaoBD.php';
$usuario = new Usuario($db);

class Usuario {
    private $conn;
    private $table = 'Usuario';

    // Propriedades correspondentes à tabela Usuario
    private $id_usuario;
    private $nome_usuario;
    private $email_usuario;
    private $senha_usuario;
    private $foto_perfil_usuario;
    private $biografia_usuario;
    private $data_criacao_usuario;

    // Construtor
    public function __construct($db) {
        $this->conn = $db;

        // Inicializa as propriedades com valores padrão (semelhante ao C#)
        $this->id_usuario = 0;
        $this->nome_usuario = '';
        $this->email_usuario = '';
        $this->senha_usuario = '';
        $this->foto_perfil_usuario = '';
        $this->biografia_usuario = '';
        $this->data_criacao_usuario = '';
    }

    // Métodos Get e Set
    public function getIdUsuario() {
        return $this->id_usuario;
    }

    public function setIdUsuario($id_usuario) {
        $this->id_usuario = (int) $id_usuario;
    }

    public function getNomeUsuario() {
        return $this->nome_usuario;
    }

    public function setNomeUsuario($nome_usuario) {
        $this->nome_usuario = htmlspecialchars(strip_tags($nome_usuario));
    }

    public function getEmailUsuario() {
        return $this->email_usuario;
    }

    public function setEmailUsuario($email_usuario) {
        $this->email_usuario = htmlspecialchars(strip_tags($email_usuario));
    }

    public function getSenhaUsuario() {
        return $this->senha_usuario;
    }

    public function setSenhaUsuario($senha_usuario) {
        // Hash da senha para segurança
        $this->senha_usuario = password_hash($senha_usuario, PASSWORD_DEFAULT);
    }

    public function getFotoPerfilUsuario() {
        return $this->foto_perfil_usuario;
    }

    public function setFotoPerfilUsuario($foto_perfil_usuario) {
        $this->foto_perfil_usuario = htmlspecialchars(strip_tags($foto_perfil_usuario));
    }

    public function getBiografiaUsuario() {
        return $this->biografia_usuario;
    }

    public function setBiografiaUsuario($biografia_usuario) {
        $this->biografia_usuario = htmlspecialchars(strip_tags($biografia_usuario));
    }

    public function getDataCriacaoUsuario() {
        return $this->data_criacao_usuario;
    }

    public function setDataCriacaoUsuario($data_criacao_usuario) {
        $this->data_criacao_usuario = htmlspecialchars(strip_tags($data_criacao_usuario));
    }

}
?>