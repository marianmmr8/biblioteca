<?php
require 'conexion.php';
require 'modelo.php';

$mensajes = [
    'prestado' => 'Préstamo registrado.',
    'devuelto' => 'Devolución registrada.',
];
$mensaje = $mensajes[$_GET['msg'] ?? ''] ?? '';

$prestamos = prestamosEnCurso($pdo);

$titulo = 'Préstamos en curso';
require 'cabecera.php';
?>

<?php if ($mensaje): ?>
  <p class="aviso"><?= htmlspecialchars($mensaje) ?></p>
<?php endif; ?>

<?php if (!$prestamos): ?>
  <p>No hay ningún libro prestado.</p>
<?php else: ?>
  <table>
    <thead>
      <tr><th>Libro</th><th>Lector</th><th>Prestado el</th><th>Vence el</th><th>Estado</th><th>Acción</th></tr>
    </thead>
    <tbody>
      <?php foreach ($prestamos as $p): ?>
          <tr class="<?= $p['dias_retraso'] > 0 ? 'retrasado' : '' ?>">
          <td><?= htmlspecialchars($p['titulo']) ?></td>
          <td>
            <a href="lector.php?id=<?= (int) $p['lector_id'] ?>">
              <?= htmlspecialchars($p['nombre'] . ' ' . $p['apellidos']) ?>
            </a>
          </td>
          <td><?= date('d/m/Y', strtotime($p['fecha_prestamo'])) ?></td>
          <td><?= date('d/m/Y', strtotime($p['fecha_vencimiento'])) ?></td>
          <td>
            <?php if ($p['dias_retraso'] > 0): ?>
              <?= (int) $p['dias_retraso'] ?> días de retraso
            <?php else: ?>
              En plazo
            <?php endif; ?>
          </td>
          <td>
            <form method="post" action="devolver.php">
              <input type="hidden" name="id" value="<?= $p['id'] ?>">
              <button>Devolver</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>

<?php require 'pie.php'; ?>
