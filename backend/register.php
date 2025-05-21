<?php
require 'db.php';

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $password);

    if ($stmt->execute()) {
        header("Location: login.php");
        exit();
    } else {
        $mensaje = "❌ Error al registrar: " . $conn->error;
    }
}
?>

<!doctype html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Registro</title>
  <!-- Bootstrap 5 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background-color: rgb(13, 38, 95); /* Fondo azul oscuro */
      color: #fff;
    }
    .card {
      border-radius: 20px;
      background-color: #ffffff; /* Tarjeta blanca */
      color: #000;
    }
    .btn-success {
      background-color: #28a745;
      border: none;
    }
    .btn-success:hover {
      background-color: #218838;
    }
    a {
      color: #1976d2;
      text-decoration: none;
    }
    a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

<div class="container d-flex justify-content-center align-items-center vh-100">
  <div class="card shadow-lg p-4" style="min-width: 350px;">
    <h2 class="text-center mb-4">Registro</h2>
    
    <?php if ($mensaje): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $mensaje ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
      </div>
    <?php endif; ?>

    <form method="post">
      <div class="mb-3">
        <label for="username" class="form-label">Usuario</label>
        <input type="text" class="form-control" id="username" name="username" required>
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Contraseña</label>
        <input type="password" class="form-control" id="password" name="password" required>
      </div>

      <button type="submit" class="btn btn-success w-100">Registrarse</button>
    </form>

    <div class="mt-3 text-center">
      <small>¿Ya tienes una cuenta? <a href="login.php">Inicia sesión</a></small>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
