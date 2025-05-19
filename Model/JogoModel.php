<?php
require_once __DIR__ . '/../Controller/ConexaoBD.php';

class Jogo {
    private $conn;
    private $table = 'Jogo';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function criar($dados) {
        $sql = "INSERT INTO {$this->table}
            (nome_jogo, ano_lancamento_jogo, descricao_jogo, capa_jogo, trailer_jogo)
            VALUES (:nome, :ano, :descricao, :capa, :trailer)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':nome' => $dados['nome_jogo'],
            ':ano' => $dados['ano_lancamento_jogo'],
            ':descricao' => $dados['descricao_jogo'],
            ':capa' => $dados['capa_jogo'],
            ':trailer' => $dados['trailer_jogo']
        ]);
    }
}
