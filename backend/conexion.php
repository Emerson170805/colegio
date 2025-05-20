<?php
$host = '193.203.175.174';
$db   = 'u505767678_colegio';
$user = 'u505767678_estrada';
$pass = 'shadow.2005.SHADOW';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
