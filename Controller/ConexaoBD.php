<?php

// CONEXÃO RAILWAY
$hostName = "yamabiko.proxy.rlwy.net";
$dataBase = "railway";
$user = "root";
$password = "aVkdQNjtiUHbixjgTThXzblQUSIKKnmh";
$port = 16099;

$mysqli = new mysqli($hostName, $user, $password, $dataBase, $port);

if ($mysqli->connect_errno) {
    echo "Falha ao conectar: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    exit();
}

return $mysqli;
// echo "Conexão bem-sucedida!";
?>
