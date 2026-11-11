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

<section class="catalogo-presentacion" aria-labelledby="inspiracion-lectora">
  <div class="catalogo-citas">
    <p class="sobretitulo">Ideas para llevar contigo</p>
    <h2 id="inspiracion-lectora">Cada libro abre un camino.</h2>
    <blockquote>
      <p>«Algunos libros deben saborearse, otros devorarse y unos pocos, masticarse y digerirse.»</p>
      <cite>Francis Bacon, <i>Ensayos</i></cite>
    </blockquote>
    <blockquote>
      <p>«No es que tengamos poco tiempo, sino que perdemos mucho.»</p>
      <cite>Séneca, <i>Sobre la brevedad de la vida</i></cite>
    </blockquote>
  </div>
  <img class="ilustracion-literaria" src="assets/don-quijote.svg" alt="Ilustración original de Don Quijote junto a un molino">
</section>

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