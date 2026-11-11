<?php
require 'conexion.php';
require 'modelo.php';

$mensajes = [
    'creado' => 'Lector registrado.',
    'actualizado' => 'Lector actualizado.',
    'eliminado' => 'Lector eliminado.',
    'con_prestamos' => 'No se puede eliminar el lector porque tiene préstamos asociados.',
    'error' => 'No se ha podido eliminar el lector.',
];
$mensaje = $mensajes[$_GET['msg'] ?? ''] ?? '';
$lectores = listaLectores($pdo);

$titulo = 'Lectores';
require 'cabecera.php';
?>

<?php if ($mensaje): ?>
  <p class="aviso"><?= htmlspecialchars($mensaje) ?></p>
<?php endif; ?>

<p><a href="nuevo-lector.php">Añadir lector</a></p>

<?php if (!$lectores): ?>
  <p>No hay lectores registrados.</p>
<?php else: ?>
  <table>
    <thead>
      <tr><th>Nombre</th><th>Correo</th><th>Teléfono</th><th>Acciones</th></tr>
    </thead>
    <tbody>
      <?php foreach ($lectores as $lector): ?>
        <tr>
          <td><?= htmlspecialchars($lector['nombre'] . ' ' . $lector['apellidos']) ?></td>
          <td><?= htmlspecialchars($lector['email'] ?? '') ?></td>
          <td><?= htmlspecialchars($lector['telefono']) ?></td>
          <td class="acciones">
            <a href="lector.php?id=<?= (int) $lector['id'] ?>">Historial</a>
            <a href="editar.php?tipo=lector&amp;id=<?= (int) $lector['id'] ?>">Editar</a>
            <form method="post" action="eliminar.php">
              <input type="hidden" name="tipo" value="lector">
              <input type="hidden" name="id" value="<?= (int) $lector['id'] ?>">
              <button>Eliminar</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>

<?php require 'pie.php'; ?>