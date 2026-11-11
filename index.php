<?php
require 'conexion.php';
require 'modelo.php';

// Avisos que llegan en la dirección: index.php?msg=prestado
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
  <p class="aviso"><?= $mensaje ?></p>
<?php endif; ?>

<?php if (!$prestamos): ?>
  <p>No hay ningún libro prestado.</p>
<?php else: ?>
  <table>
    <thead>
      <tr><th>Libro</th><th>Lector</th><th>Prestado el</th><th>Días</th><th>Acción</th></tr>
    </thead>
    <tbody>
      <?php foreach ($prestamos as $p): ?>
        <tr class="<?= $p['dias'] > 15 ? 'retrasado' : '' ?>">
          <td><?= htmlspecialchars($p['titulo']) ?></td>
          <td>
            <a href="lector.php?id=<?= (int) $p['lector_id'] ?>">
              <?= htmlspecialchars($p['nombre'] . ' ' . $p['apellidos']) ?>
            </a>
          </td>
          <td><?= date('d/m/Y', strtotime($p['fecha_prestamo'])) ?></td>
          <td><?= (int) $p['dias'] ?></td>
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
