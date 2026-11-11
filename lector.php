<?php
require 'conexion.php';
require 'modelo.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$lector = false;

if ($id) {
  $lector = lectorPorId($pdo, $id);
}

if (!$lector) {
    http_response_code(404);
    $titulo = 'Lector no encontrado';
    require 'cabecera.php';
    echo '<p>No hay ningún lector con ese número. <a href="lectores.php">Volver al listado</a></p>';
    require 'pie.php';
    exit;
}

$prestamos = prestamosDeLector($pdo, $id);

$titulo = 'Historial de ' . $lector['nombre'] . ' ' . $lector['apellidos'];
require 'cabecera.php';
?>

<p>Préstamos: <?= count($prestamos) ?></p>

<?php if (!$prestamos): ?>
  <p>Este lector todavía no se ha llevado ningún libro.</p>
<?php else: ?>
  <table>
    <thead>
      <tr><th>Libro</th><th>Prestado el</th><th>Devuelto el</th></tr>
    </thead>
    <tbody>
      <?php foreach ($prestamos as $p): ?>
        <tr>
          <td><?= htmlspecialchars($p['titulo']) ?></td>
          <td><?= date('d/m/Y', strtotime($p['fecha_prestamo'])) ?></td>
          <td>
            <?php if ($p['fecha_devolucion'] === null): ?>
              <strong>En préstamo</strong>
            <?php else: ?>
              <?= date('d/m/Y', strtotime($p['fecha_devolucion'])) ?>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>

<?php require 'pie.php'; ?>