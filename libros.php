<?php
require 'conexion.php';
require 'modelo.php';

$mensajes = [
    'creado' => 'Libro registrado.',
    'actualizado' => 'Libro actualizado.',
    'eliminado' => 'Libro eliminado.',
    'con_prestamos' => 'No se puede eliminar el libro porque tiene préstamos asociados.',
    'error' => 'No se ha podido eliminar el libro.',
];
$mensaje = $mensajes[$_GET['msg'] ?? ''] ?? '';
$busqueda = trim((string) ($_GET['q'] ?? ''));
$libros = buscarLibros($pdo, $busqueda);

$titulo = 'Libros';
require 'cabecera.php';
?>

<?php if ($mensaje): ?>
  <p class="aviso"><?= htmlspecialchars($mensaje) ?></p>
<?php endif; ?>

<p><a href="nuevo-libro.php">Añadir libro</a></p>

<form method="get" class="busqueda">
  <label for="q">Buscar por título o autor</label>
  <input type="search" id="q" name="q" value="<?= htmlspecialchars($busqueda) ?>">
  <button>Buscar</button>
</form>

<?php if (!$libros): ?>
  <p>No se encontraron libros.</p>
<?php else: ?>
  <table>
    <thead>
      <tr><th>Título</th><th>Autor</th><th>ISBN</th><th>Estado</th><th>Acciones</th></tr>
    </thead>
    <tbody>
      <?php foreach ($libros as $libro): ?>
        <tr>
          <td><?= htmlspecialchars($libro['titulo']) ?></td>
          <td><?= htmlspecialchars($libro['autor']) ?></td>
          <td><?= htmlspecialchars($libro['isbn']) ?></td>
          <td><?= htmlspecialchars($libro['estado']) ?></td>
          <td class="acciones">
            <a href="editar.php?tipo=libro&amp;id=<?= (int) $libro['id'] ?>">Editar</a>
            <form method="post" action="eliminar.php">
              <input type="hidden" name="tipo" value="libro">
              <input type="hidden" name="id" value="<?= (int) $libro['id'] ?>">
              <button>Eliminar</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>

<?php require 'pie.php'; ?>