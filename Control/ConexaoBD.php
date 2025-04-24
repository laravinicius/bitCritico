<?php

$hostName = "switchyard.proxy.rlwy.net";
$dataBase = "railway";
$user = "root";
$password = "HuvLgAmmNGLqApLFOfIRjVixgrHRfqIs";
$port = 17920; 

$mysqli = new mysqli($hostName, $user, $password, $dataBase, $port);

if ($mysqli->connect_errno) {
    echo "Falha ao conectar: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
} else {
    echo "Conexão bem-sucedida!";
}

?>

