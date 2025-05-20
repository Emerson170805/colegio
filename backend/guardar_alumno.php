<?php
include 'conexion.php';

$sql = "INSERT INTO alumno (nombre, apellido, grado, seccion, telefono, password, dni, nivel)
VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    $_POST['nombre'], $_POST['apellido'], $_POST['grado'], $_POST['seccion'],
    $_POST['telefono'], password_hash($_POST['password'], PASSWORD_DEFAULT),
    $_POST['dni'], $_POST['nivel']
]);

header("Location: index.php");
exit;
