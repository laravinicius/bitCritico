<?php
include('/ConexaoBD.php');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Buscar dados do jogo
$sqlJogo = "SELECT * FROM Jogo WHERE id_jogo = $id";
$resultadoJogo = $conn->query($sqlJogo);

if ($resultadoJogo->num_rows == 0) {
  die("Jogo não encontrado!");
}

$jogo = $resultadoJogo->fetch_assoc();

// Buscar reviews
$sqlReviews = "SELECT R.*, U.nome_usuario FROM Review R JOIN Usuario U ON R.id_usuario = U.id_usuario WHERE R.id_jogo = $id";
$resultadoReviews = $conn->query($sqlReviews);

// Transforma resultado em array
$reviews = [];
while ($review = $resultadoReviews->fetch_assoc()) {
  $reviews[] = $review;
}

// Inclui o HTML e passa as variáveis
include('../view/detalhesJogo.php');
?>
