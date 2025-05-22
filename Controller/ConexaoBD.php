<?php
$hostName = "turntable.proxy.rlwy.net";
$dataBase = "railway";
$user = "root";
$password = "WnmoCEaIpHlEBfvYojoKSTfEeijnmPyk";
$port = 52444;

$mysqli = new mysqli($hostName, $user, $password, $dataBase, $port);

if ($mysqli->connect_errno) {
    echo "Falha ao conectar: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    exit();
}

return $mysqli;
// echo "Conexão bem-sucedida!";
?>
