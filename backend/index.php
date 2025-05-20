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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="p-4">

<div class="container">
    <h1 class="mb-4">Gestión de Alumnos</h1>

    <!-- Botón para abrir modal Agregar Alumno -->
    <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#modalAgregarAlumno">
        Agregar Alumno
    </button>

    <!-- Modal para Agregar Alumno -->
    <div class="modal fade" id="modalAgregarAlumno" tabindex="-1" aria-labelledby="modalAgregarAlumnoLabel" aria-hidden="true">
      <div class="modal-dialog">
        <form action="guardar_alumno.php" method="POST" class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalAgregarAlumnoLabel">Agregar Alumno</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <input type="text" name="nombre" class="form-control" placeholder="Nombre" required>
            </div>
            <div class="mb-3">
              <input type="text" name="apellido" class="form-control" placeholder="Apellido" required>
            </div>
            <div class="mb-3">
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
            <div class="mb-3">
              <select name="seccion" class="form-select" required>
                <option value="">Sección</option>
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="C">C</option>
                <option value="D">D</option>
                <option value="E">E</option>
              </select>
            </div>
            <div class="mb-3">
              <select name="nivel" class="form-select" required>
                <option value="">Nivel</option>
                <option value="Primaria">Primaria</option>
                <option value="Secundaria">Secundaria</option>
              </select>
            </div>
            <div class="mb-3">
              <input type="text" name="telefono" class="form-control" placeholder="Teléfono">
            </div>
            <div class="mb-3">
              <input type="password" name="password" class="form-control" placeholder="Contraseña">
            </div>
            <div class="mb-3">
              <input type="text" name="dni" class="form-control" placeholder="DNI" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Agregar Alumno</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Filtros (con íconos de lupa) -->
<form method="GET" class="row g-2 mb-3" id="filtrosForm">

    <div class="col-md-2">
      <div class="input-group">
        <span class="input-group-text"><i class="bi bi-search"></i></span>
        <input type="text" name="nombre" class="form-control filtro-texto" placeholder="Buscar Nombre" value="<?= $_GET['nombre'] ?? '' ?>">
      </div>
    </div>

    <div class="col-md-2">
      <div class="input-group">
        <span class="input-group-text"><i class="bi bi-search"></i></span>
        <input type="text" name="dni" class="form-control filtro-texto" placeholder="Buscar DNI" value="<?= $_GET['dni'] ?? '' ?>">
      </div>
    </div>

    <div class="col-md-2">
      <div class="input-group">
        <span class="input-group-text"><i class="bi bi-search"></i></span>
        <select name="grado" class="form-select filtro-combo">
            <option value="">Grado</option>
            <option value="1ro" <?= (($_GET['grado'] ?? '') === '1ro') ? 'selected' : '' ?>>1ro</option>
            <option value="2do" <?= (($_GET['grado'] ?? '') === '2do') ? 'selected' : '' ?>>2do</option>
            <option value="3ro" <?= (($_GET['grado'] ?? '') === '3ro') ? 'selected' : '' ?>>3ro</option>
            <option value="4to" <?= (($_GET['grado'] ?? '') === '4to') ? 'selected' : '' ?>>4to</option>
            <option value="5to" <?= (($_GET['grado'] ?? '') === '5to') ? 'selected' : '' ?>>5to</option>
            <option value="6to" <?= (($_GET['grado'] ?? '') === '6to') ? 'selected' : '' ?>>6to</option>
        </select>
      </div>
    </div>

    <div class="col-md-2">
      <div class="input-group">
        <span class="input-group-text"><i class="bi bi-search"></i></span>
        <select name="seccion" class="form-select filtro-combo">
            <option value="">Seccion</option>
            <option value="A" <?= (($_GET['seccion'] ?? '') === 'A') ? 'selected' : '' ?>>A</option>
            <option value="B" <?= (($_GET['seccion'] ?? '') === 'B') ? 'selected' : '' ?>>B</option>
            <option value="C" <?= (($_GET['seccion'] ?? '') === 'C') ? 'selected' : '' ?>>C</option>
            <option value="D" <?= (($_GET['seccion'] ?? '') === 'D') ? 'selected' : '' ?>>D</option>
            <option value="E" <?= (($_GET['seccion'] ?? '') === 'E') ? 'selected' : '' ?>>E</option>
        </select>
      </div>
    </div>

    <div class="col-md-2">
      <div class="input-group">
        <span class="input-group-text"><i class="bi bi-search"></i></span>
        <select name="nivel" class="form-select filtro-combo">
            <option value="">Nivel</option>
            <?php foreach ($niveles as $n): ?>
                <option <?= ($_GET['nivel'] ?? '') === $n ? 'selected' : '' ?>><?= $n ?></option>
            <?php endforeach; ?>
        </select>
      </div>
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
                        <input type="hidden" name="id" value="<?= $a['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-info">QR</button>
                    </form>
                </td>
            </tr>

            <!-- Modal para editar alumno (ejemplo simple) -->
            <div class="modal fade" id="modalEditar<?= $a['id'] ?>" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog">
                <form action="editar_alumno.php" method="POST" class="modal-content">
                  <input type="hidden" name="id" value="<?= $a['id'] ?>">
                  <div class="modal-header">
                    <h5 class="modal-title">Editar Alumno</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                  </div>
                  <div class="modal-body">
                    <input type="text" name="nombre" class="form-control mb-2" value="<?= htmlspecialchars($a['nombre']) ?>" required>
                    <input type="text" name="apellido" class="form-control mb-2" value="<?= htmlspecialchars($a['apellido']) ?>" required>
                    <select name="grado" class="form-select mb-2" required>
                      <option value="1ro" <?= $a['grado']=='1ro' ? 'selected' : '' ?>>1ro</option>
                      <option value="2do" <?= $a['grado']=='2do' ? 'selected' : '' ?>>2do</option>
                      <option value="3ro" <?= $a['grado']=='3ro' ? 'selected' : '' ?>>3ro</option>
                      <option value="4to" <?= $a['grado']=='4to' ? 'selected' : '' ?>>4to</option>
                      <option value="5to" <?= $a['grado']=='5to' ? 'selected' : '' ?>>5to</option>
                      <option value="6to" <?= $a['grado']=='6to' ? 'selected' : '' ?>>6to</option>
                    </select>
                    <select name="seccion" class="form-select mb-2" required>
                      <option value="A" <?= $a['seccion']=='A' ? 'selected' : '' ?>>A</option>
                      <option value="B" <?= $a['seccion']=='B' ? 'selected' : '' ?>>B</option>
                      <option value="C" <?= $a['seccion']=='C' ? 'selected' : '' ?>>C</option>
                      <option value="D" <?= $a['seccion']=='D' ? 'selected' : '' ?>>D</option>
                      <option value="E" <?= $a['seccion']=='E' ? 'selected' : '' ?>>E</option>
                    </select>
                    <select name="nivel" class="form-select mb-2" required>
                      <option value="Primaria" <?= $a['nivel']=='Primaria' ? 'selected' : '' ?>>Primaria</option>
                      <option value="Secundaria" <?= $a['nivel']=='Secundaria' ? 'selected' : '' ?>>Secundaria</option>
                    </select>
                    <input type="text" name="dni" class="form-control mb-2" value="<?= htmlspecialchars($a['dni']) ?>" required>
                  </div>
                  <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                  </div>
                </form>
              </div>
            </div>

        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Auto submit filtros al cambiar
    document.querySelectorAll('.filtro-texto').forEach(input => {
        input.addEventListener('input', () => {
            document.getElementById('filtrosForm').submit();
        });
    });
    document.querySelectorAll('.filtro-combo').forEach(select => {
        select.addEventListener('change', () => {
            document.getElementById('filtrosForm').submit();
        });
    });
</script>

</body>
</html>
