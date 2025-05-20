<?php
$host = '193.203.175.174';
$db   = 'u505767678_colegio';
$user = 'u505767678_estrada';
$pass = 'shadow.2005.SHADOW';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass);
    echo "Conexión exitosa a la base de datos.";
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
    exit;
}
?>
    