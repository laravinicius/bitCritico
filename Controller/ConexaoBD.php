<?php
// $hostName = "turntable.proxy.rlwy.net";
$hostName = "localhost";
$dataBase = "railway";
$user = "root";
// $password = "WnmoCEaIpHlEBfvYojoKSTfEeijnmPyk";
$password = "";
// $port = 52444;

// $mysqli = new mysqli($hostName, $user, $password, $dataBase, $port);
$mysqli = new mysqli($hostName, $user, $password, $dataBase);


if ($mysqli->connect_errno) {
    echo "Falha ao conectar: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    exit();
}

return $mysqli;
// echo "Conexão bem-sucedida!";
?>
