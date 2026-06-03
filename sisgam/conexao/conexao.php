<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "sismab_db";


$con = new mysqli($host, $user, $pass, $db);

if ($con->connect_error) {
    die("Falha na conexão com o banco de dados: " . $con->connect_error);
}

$con->set_charset("utf8mb4");
?>