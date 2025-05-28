<?php
class Jogo {
    private $id_jogo;
    private $nome_jogo;
    private $ano_lancamento_jogo;
    private $descricao_jogo;
    private $capa_jogo;
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function setIdJogo($id) { $this->id_jogo = $id; }
    public function getIdJogo() { return $this->id_jogo; }
    public function setNomeJogo($nome) { $this->nome_jogo = $nome; }
    public function getNomeJogo() { return $this->nome_jogo; }
    public function setAnoLancamentoJogo($nome) { $this->ano_lancamento_jogo = $nome; }
    public function getAnoLancamentoJogo() { return $this->ano_lancamento_jogo; }
    public function setDescricaoJogo($descricao) { $this->descricao_jogo = $descricao; }
    public function getDescricaoJogo() { return $this->descricao_jogo; }
    public function setCapaJogo($capa) { $this->capa_jogo = $capa; }
    public function getCapaJogo() { return $this->capa_jogo; }
}
?>