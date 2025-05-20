<?php
$host = "193.203.175.174";  // o IP si no es localhost
$user = "u505767678_estrada";
$pass = "shadow.2005.SHADOW";
$db = "u505767678_colegio";

$conexion = new mysqli($host, $user, $pass, $db);

if ($conexion->connect_error) {
    die("❌ Error de conexión: " . $conexion->connect_error);
}
?>
