<?php
include 'conexion.php';

// Obtener valores únicos para combo box
$grados = $pdo->query("SELECT DISTINCT grado FROM alumno")->fetchAll(PDO::FETCH_COLUMN);
$secciones = $pdo->query("SELECT DISTINCT seccion FROM alumno")->fetchAll(PDO::FETCH_COLUMN);
$niveles = $pdo->query("SELECT DISTINCT nivel FROM alumno")->fetchAll(PDO::FETCH_COLUMN);

// Filtros
$where = [];
$params = [];

if (!empty($_GET['nombre'])) {
    $where[] = "nombre LIKE :nombre";
    $params[':nombre'] = "%" . $_GET['nombre'] . "%";
}
if (!empty($_GET['dni'])) {
    $where[] = "dni LIKE :dni";
    $params[':dni'] = "%" . $_GET['dni'] . "%";
}
if (!empty($_GET['grado'])) {
    $where[] = "grado = :grado";
    $params[':grado'] = $_GET['grado'];
}
if (!empty($_GET['seccion'])) {
    $where[] = "seccion = :seccion";
    $params[':seccion'] = $_GET['seccion'];
}
if (!empty($_GET['nivel'])) {
    $where[] = "nivel = :nivel";
    $params[':nivel'] = $_GET['nivel'];
}

$sql = "SELECT * FROM alumno";
if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$alumnos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Alumnos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">

<div class="container">
    <h1 class="mb-4">Gestión de Alumnos</h1>

    <!-- Formulario de Agregar -->
    <form action="guardar_alumno.php" method="POST" class="row g-3 mb-4">
    <div class="col-md-3"><input type="text" name="nombre" class="form-control" placeholder="Nombre" required></div>
    <div class="col-md-3"><input type="text" name="apellido" class="form-control" placeholder="Apellido" required></div>

    <!-- Combo box para grado -->
    <div class="col-md-2">
        <select name="grado" class="form-select" required>
            <option value="">Grado</option>
            <option value="1ro">1ro</option>
            <option value="2do">2do</option>
            <option value="3ro">3ro</option>
            <option value="4to">4to</option>
            <option value="5to">5to</option>
            <option value="6to">6to</option>
        </select>
    </div>

    <!-- Combo box para sección -->
    <div class="col-md-2">
        <select name="seccion" class="form-select" required>
            <option value="">Sección</option>
            <option value="A">A</option>
            <option value="B">B</option>
            <option value="C">C</option>
            <option value="D">D</option>
            <option value="E">E</option>
        </select>
    </div>

    <!-- Combo box para nivel -->
    <div class="col-md-2">
        <select name="nivel" class="form-select" required>
            <option value="">Nivel</option>
            <option value="Primaria">Primaria</option>
            <option value="Secundaria">Secundaria</option>
        </select>
    </div>

    <div class="col-md-3"><input type="text" name="telefono" class="form-control" placeholder="Teléfono"></div>
    <div class="col-md-3"><input type="password" name="password" class="form-control" placeholder="Contraseña"></div>
    <div class="col-md-3"><input type="text" name="dni" class="form-control" placeholder="DNI" required></div>
    <div class="col-md-3"><button class="btn btn-primary w-100">Agregar Alumno</button></div>
</form>


    <!-- Filtros (con envío automático) -->
    <form method="GET" class="row g-2 mb-3" id="filtrosForm">
        <div class="col-md-2">
            <input type="text" name="nombre" class="form-control filtro-texto" placeholder="Buscar Nombre" value="<?= $_GET['nombre'] ?? '' ?>">
        </div>
        <div class="col-md-2">
            <input type="text" name="dni" class="form-control filtro-texto" placeholder="Buscar DNI" value="<?= $_GET['dni'] ?? '' ?>">
        </div>
        <div class="col-md-2">
            <select name="grado" class="form-select filtro-combo">
                <option value="">Grado</option>
                <?php foreach ($grados as $g): ?>
                    <option <?= ($_GET['grado'] ?? '') === $g ? 'selected' : '' ?>><?= $g ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <select name="seccion" class="form-select filtro-combo">
                <option value="">Sección</option>
                <?php foreach ($secciones as $s): ?>
                    <option <?= ($_GET['seccion'] ?? '') === $s ? 'selected' : '' ?>><?= $s ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <select name="nivel" class="form-select filtro-combo">
                <option value="">Nivel</option>
                <?php foreach ($niveles as $n): ?>
                    <option <?= ($_GET['nivel'] ?? '') === $n ? 'selected' : '' ?>><?= $n ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>

    <!-- Tabla -->
    <table class="table table-bordered">
        <thead class="table-light">
        <tr>
            <th>Nombre</th><th>Apellido</th><th>Grado</th><th>Sección</th><th>Nivel</th><th>DNI</th><th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($alumnos as $a): ?>
            <tr>
                <td><?= htmlspecialchars($a['nombre']) ?></td>
                <td><?= htmlspecialchars($a['apellido']) ?></td>
                <td><?= htmlspecialchars($a['grado']) ?></td>
                <td><?= htmlspecialchars($a['seccion']) ?></td>
                <td><?= htmlspecialchars($a['nivel']) ?></td>
                <td><?= htmlspecialchars($a['dni']) ?></td>
                <td>
                    <!-- Editar -->
                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEditar<?= $a['id'] ?>">Editar</button>

                    <form action="eliminar_alumno.php" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que quieres eliminar este alumno?');">
                        <input type="hidden" name="id" value="<?= $a['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                    </form>


                    <!-- QR -->
                    <form action="generar_qr.php" method="POST" class="d-inline">
                        <input type="hidden" name="texto" value="<?= $a['dni'] ?>">
                        <button class="btn btn-sm btn-success" type="submit">QR</button>
                    </form>

                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<!-- Modal para editar -->
  <?php foreach ($alumnos as $a): ?>
    <div class="modal fade" id="modalEditar<?= $a['id'] ?>" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" action="editar_alumno.php">
                <input type="hidden" name="id" value="<?= $a['id'] ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Alumno</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="text" name="nombre" class="form-control mb-2" value="<?= htmlspecialchars($a['nombre']) ?>" required>
                    <input type="text" name="apellido" class="form-control mb-2" value="<?= htmlspecialchars($a['apellido']) ?>" required>
                    
                    <!-- Combo para grado -->
                    <select name="grado" class="form-select mb-2" required>
                        <option value="1ro" <?= $a['grado'] == '1ro' ? 'selected' : '' ?>>1ro</option>
                        <option value="2do" <?= $a['grado'] == '2do' ? 'selected' : '' ?>>2do</option>
                        <option value="3ro" <?= $a['grado'] == '3ro' ? 'selected' : '' ?>>3ro</option>
                        <option value="4to" <?= $a['grado'] == '4to' ? 'selected' : '' ?>>4to</option>
                        <option value="5to" <?= $a['grado'] == '5to' ? 'selected' : '' ?>>5to</option>
                        <option value="6to" <?= $a['grado'] == '6to' ? 'selected' : '' ?>>6to</option>
                    </select>

                    <!-- Combo para sección -->
                    <select name="seccion" class="form-select mb-2" required>
                        <option value="A" <?= $a['seccion'] == 'A' ? 'selected' : '' ?>>A</option>
                        <option value="B" <?= $a['seccion'] == 'B' ? 'selected' : '' ?>>B</option>
                        <option value="C" <?= $a['seccion'] == 'C' ? 'selected' : '' ?>>C</option>
                        <option value="D" <?= $a['seccion'] == 'D' ? 'selected' : '' ?>>D</option>
                        <option value="E" <?= $a['seccion'] == 'E' ? 'selected' : '' ?>>E</option>
                    </select>

                    <!-- Combo para nivel -->
                    <select name="nivel" class="form-select mb-2" required>
                        <option value="Primaria" <?= $a['nivel'] == 'Primaria' ? 'selected' : '' ?>>Primaria</option>
                        <option value="Secundaria" <?= $a['nivel'] == 'Secundaria' ? 'selected' : '' ?>>Secundaria</option>
                    </select>

                    <input type="text" name="telefono" class="form-control mb-2" value="<?= htmlspecialchars($a['telefono']) ?>">
                    <input type="text" name="dni" class="form-control mb-2" value="<?= htmlspecialchars($a['dni']) ?>" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
<?php endforeach; ?>


    <script>
        const form = document.getElementById('filtrosForm');
        let timer;
        document.querySelectorAll('.filtro-texto').forEach(input => {
            input.addEventListener('input', () => {
                clearTimeout(timer);
                timer = setTimeout(() => form.submit(), 600);
            });
        });
        document.querySelectorAll('.filtro-combo').forEach(select => {
            select.addEventListener('change', () => form.submit());
        });
    </script>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
