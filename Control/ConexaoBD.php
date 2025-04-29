<?php

//Conexão com a conta do projeto.

$hostName = "maglev.proxy.rlwy.net";
$dataBase = "railway";
$user = "root";
$password = "OzlayXCzKOvSuxUIQPDiAlSXLvmwIbWr";
$port = 26839; 

$mysqli = new mysqli($hostName, $user, $password, $dataBase, $port);

if ($mysqli->connect_errno) {
    echo "Falha ao conectar: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
} else {
    echo "Conexão bem-sucedida!";
}

?>

