<?php

// CONEXÃO RAILWAY
$hostName = "shuttle.proxy.rlwy.net";
$dataBase = "railway";
$user = "root";
$password = "zqcxPkSWkISohwgNawPAcEMISOnVzEKI";
$port = 42238;

$mysqli = new mysqli($hostName, $user, $password, $dataBase, $port);

if ($mysqli->connect_errno) {
    echo "Falha ao conectar: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    exit();
}

return $mysqli;
// echo "Conexão bem-sucedida!";
?>
