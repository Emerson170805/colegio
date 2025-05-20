<?php
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $sql = "UPDATE alumno SET nombre=?, apellido=?, grado=?, seccion=?, nivel=?, telefono=?, dni=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $_POST['nombre'],
            $_POST['apellido'],
            $_POST['grado'],
            $_POST['seccion'],
            $_POST['nivel'],
            $_POST['telefono'],
            $_POST['dni'],
            $_POST['id']
        ]);
        header("Location: index.php");
        exit;
    } catch (Exception $e) {
        echo "Error al actualizar: " . $e->getMessage();
    }
} else {
    echo "Acceso no permitido.";
}
?>
