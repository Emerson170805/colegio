<?php
require 'auth.php';
include 'conexion.php';

// Obtener valores únicos para combo box
$grados = $pdo->query("SELECT DISTINCT grado FROM alumno")->fetchAll(PDO::FETCH_COLUMN);
$secciones = $pdo->query("SELECT DISTINCT seccion FROM alumno")->fetchAll(PDO::FETCH_COLUMN);
$niveles = $pdo->query("SELECT DISTINCT nivel FROM alumno")->fetchAll(PDO::FETCH_COLUMN);

// Filtros en servidor para nombre, grado, sección y nivel (pero no DNI)
$where = [];
$params = [];

if (!empty($_GET['nombre'])) {
    $where[] = "nombre LIKE :nombre";
    $params[':nombre'] = "%" . trim($_GET['nombre']) . "%";
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

    <!-- Botón para abrir modal -->
    <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#modalAgregarAlumno">
        Agregar Alumno
    </button>

    <!-- Modal Agregar Alumno -->
    <div class="modal fade" id="modalAgregarAlumno" tabindex="-1" aria-labelledby="modalAgregarAlumnoLabel" aria-hidden="true">
      <div class="modal-dialog">
        <form action="guardar_alumno.php" method="POST" class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalAgregarAlumnoLabel">Agregar Alumno</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body">
            <input type="text" name="nombre" class="form-control mb-2" placeholder="Nombre" required>
            <input type="text" name="apellido" class="form-control mb-2" placeholder="Apellido" required>
            <select name="grado" class="form-select mb-2" required>
              <option value="">Grado</option>
              <?php foreach(['1ro','2do','3ro','4to','5to','6to'] as $g): ?>
                  <option value="<?= $g ?>"><?= $g ?></option>
              <?php endforeach; ?>
            </select>
            <select name="seccion" class="form-select mb-2" required>
              <option value="">Sección</option>
              <?php foreach(['A','B','C','D','E'] as $s): ?>
                  <option value="<?= $s ?>"><?= $s ?></option>
              <?php endforeach; ?>
            </select>
            <select name="nivel" class="form-select mb-2" required>
              <option value="">Nivel</option>
              <?php foreach($niveles as $n): ?>
                  <option value="<?= htmlspecialchars($n) ?>"><?= htmlspecialchars($n) ?></option>
              <?php endforeach; ?>
            </select>
            <input type="text" name="telefono" class="form-control mb-2" placeholder="Teléfono">
            <input type="password" name="password" class="form-control mb-2" placeholder="Contraseña">
            <input type="text" name="dni" class="form-control mb-2" placeholder="DNI" required>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Guardar Alumno</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Filtros -->
    <form method="GET" class="row g-2 mb-3" id="filtrosForm">
        <div class="col-md-2">
            <input type="text" name="nombre" class="form-control" placeholder="Buscar Nombre" value="<?= htmlspecialchars($_GET['nombre'] ?? '') ?>">
        </div>
        <div class="col-md-2">
            <!-- Filtro DNI solo cliente -->
            <input type="text" id="filtroDNI" class="form-control" placeholder="Filtrar DNI">
        </div>
        <div class="col-md-2">
            <select name="grado" class="form-select filtro-combo">
                <option value="">Grado</option>
                <?php foreach(['1ro','2do','3ro','4to','5to','6to'] as $g): ?>
                    <option value="<?= $g ?>" <?= ($_GET['grado'] ?? '') === $g ? 'selected' : '' ?>><?= $g ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <select name="seccion" class="form-select filtro-combo">
                <option value="">Sección</option>
                <?php foreach(['A','B','C','D','E'] as $s): ?>
                    <option value="<?= $s ?>" <?= ($_GET['seccion'] ?? '') === $s ? 'selected' : '' ?>><?= $s ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <select name="nivel" class="form-select filtro-combo">
                <option value="">Nivel</option>
                <?php foreach($niveles as $n): ?>
                    <option value="<?= htmlspecialchars($n) ?>" <?= ($_GET['nivel'] ?? '') === $n ? 'selected' : '' ?>><?= htmlspecialchars($n) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>

    <!-- Tabla -->
    <table class="table table-bordered" id="tablaAlumnos">
        <thead class="table-light"><tr><th>Nombre</th><th>Apellido</th><th>Grado</th><th>Sección</th><th>Nivel</th><th>DNI</th><th>Acciones</th></tr></thead>
        <tbody>
        <?php foreach ($alumnos as $a): ?>
            <tr>
                <td><?= htmlspecialchars($a['nombre']) ?></td>
                <td><?= htmlspecialchars($a['apellido']) ?></td>
                <td><?= htmlspecialchars($a['grado']) ?></td>
                <td><?= htmlspecialchars($a['seccion']) ?></td>
                <td><?= htmlspecialchars($a['nivel']) ?></td>
                <td class="celda-dni"><?= htmlspecialchars($a['dni']) ?></td>
                <td>
                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEditar<?= $a['id'] ?>">Editar</button>
                    <form action="eliminar_alumno.php" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que quieres eliminar este alumno?');">
                        <input type="hidden" name="id" value="<?= $a['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                    </form>
                    <form action="generar_qr.php" method="POST" class="d-inline">
                        <input type="hidden" name="texto" value="<?= $a['dni'] ?>">
                        <button class="btn btn-sm btn-success">QR</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Modales Editar -->
    <?php foreach ($alumnos as $a): ?>
        <div class="modal fade" id="modalEditar<?= $a['id'] ?>" tabindex="-1"><div class="modal-dialog">
            <form class="modal-content" method="POST" action="editar_alumno.php">
                <input type="hidden" name="id" value="<?= $a['id'] ?>">
                <div class="modal-header"><h5 class="modal-title">Editar Alumno</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <input type="text" name="nombre" value="<?= htmlspecialchars($a['nombre']) ?>" class="form-control mb-2" required>
                    <input type="text" name="apellido" value="<?= htmlspecialchars($a['apellido']) ?>" class="form-control mb-2" required>
                    <select name="grado" class="form-select mb-2" required>
                        <?php foreach(['1ro','2do','3ro','4to','5to','6to'] as $g){ $sel=$a['grado']==$g?'selected':''; echo "<option value='$g' $sel>$g</option>";} ?>
                    </select>
                    <select name="seccion" class="form-select mb-2" required>
                        <?php foreach(['A','B','C','D','E'] as $s){ $sel=$a['seccion']==$s?'selected':''; echo "<option value='$s' $sel>$s</option>";} ?>
                    </select>
                    <select name="nivel" class="form-select mb-2" required>
                        <?php foreach ($niveles as $n) { $sel=$a['nivel']==$n?'selected':''; echo "<option value='$n' $sel>$n</option>";} ?>
                    </select>
                    <input type="text" name="telefono" value="<?= htmlspecialchars($a['telefono']) ?>" class="form-control mb-2">
                    <input type="text" name="dni" value="<?= htmlspecialchars($a['dni']) ?>" class="form-control" required>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-primary">Guardar cambios</button></div>
            </form>
        </div></div>
    <?php endforeach; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Filtrado de tabla por DNI sin recargar la página
const filtroDni = document.getElementById('filtroDNI');
const tabla = document.getElementById('tablaAlumnos').getElementsByTagName('tbody')[0];

filtroDni.addEventListener('input', () => {
    const valor = filtroDni.value.toLowerCase();
    Array.from(tabla.rows).forEach(row => {
        const cell = row.querySelector('.celda-dni').textContent.toLowerCase();
        row.style.display = cell.includes(valor) ? '' : 'none';
    });
});

// Auto-submit para los filtros que recargan páginas (nombre, grado, seccion, nivel)
const form = document.getElementById('filtrosForm');
form.querySelector("input[name='nombre']").addEventListener('keydown', e => {
    if (e.key === 'Enter') { e.preventDefault(); form.submit(); }
});
document.querySelectorAll('.filtro-combo').forEach(select => {
    select.addEventListener('change', () => form.submit());
});
</script>

</body>
</html>