<?php
require 'seguridad.php';
$esAdministrador = usuarioAutenticado();
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
$generoSolicitado = $_GET['genero'] ?? '';
$filtroGenero = is_string($generoSolicitado) ? trim($generoSolicitado) : '';
$estadoSolicitado = $_GET['estado'] ?? '';
$estadoSolicitado = is_string($estadoSolicitado) ? $estadoSolicitado : '';
$filtroEstado = in_array($estadoSolicitado, ['Disponible', 'Prestado'], true)
    ? $estadoSolicitado
    : '';
$generos = generosLibros($pdo);
$libros = buscarLibros($pdo, $busqueda, $filtroGenero, $filtroEstado);

$titulo = 'Libros';
require 'cabecera.php';
?>

<?php if ($mensaje): ?>
  <p class="aviso"><?= htmlspecialchars($mensaje) ?></p>
<?php endif; ?>

<?php if ($esAdministrador): ?>
  <p><a href="nuevo-libro.php">Añadir libro</a></p>
<?php endif; ?>

<section class="catalogo-presentacion" aria-labelledby="inspiracion-lectora">
  <div class="catalogo-citas">
    <p class="sobretitulo">Miguel de Cervantes</p>
    <h2 id="inspiracion-lectora">Don Quijote, en sus palabras.</h2>
    <blockquote>
      <p>«El que lee mucho y anda mucho, ve mucho y sabe mucho.»</p>
      <cite><i>Don Quijote de la Mancha</i>, segunda parte, capítulo 25</cite>
    </blockquote>
    <blockquote>
      <p>«Cada uno es artífice de su ventura.»</p>
      <cite><i>Don Quijote de la Mancha</i>, segunda parte, capítulo 66</cite>
    </blockquote>
  </div>
  <img class="ilustracion-literaria" src="assets/don-quijote.png" alt="Ilustración de Don Quijote, obra del usuario">
</section>

<form method="get" class="busqueda busqueda-catalogo">
  <label for="q">Buscar por título o autor
    <input type="search" id="q" name="q" value="<?= htmlspecialchars($busqueda) ?>">
  </label>
  <label for="genero">Género
    <select id="genero" name="genero">
      <option value="">Todos</option>
      <?php foreach ($generos as $generoOpcion): ?>
        <option value="<?= htmlspecialchars($generoOpcion) ?>" <?= $filtroGenero === $generoOpcion ? 'selected' : '' ?>>
          <?= htmlspecialchars($generoOpcion) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </label>
  <label for="estado">Disponibilidad
    <select id="estado" name="estado">
      <option value="">Todos</option>
      <option value="Disponible" <?= $filtroEstado === 'Disponible' ? 'selected' : '' ?>>Disponible</option>
      <option value="Prestado" <?= $filtroEstado === 'Prestado' ? 'selected' : '' ?>>Prestado</option>
    </select>
  </label>
  <button type="submit">Filtrar</button>
  <?php if ($busqueda !== '' || $filtroGenero !== '' || $filtroEstado !== ''): ?>
    <a class="limpiar-filtros" href="libros.php">Limpiar</a>
  <?php endif; ?>
</form>

<?php if (!$libros): ?>
  <p>No se encontraron libros.</p>
<?php else: ?>
  <table>
    <thead>
      <tr>
        <th>Título</th><th>Autor</th><th>ISBN</th><th>Estado</th>
        <?php if ($esAdministrador): ?><th>Acciones</th><?php endif; ?>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($libros as $libro): ?>
        <tr>
          <td><?= htmlspecialchars($libro['titulo']) ?></td>
          <td><?= htmlspecialchars($libro['autor']) ?></td>
          <td><?= htmlspecialchars($libro['isbn']) ?></td>
          <td><?= htmlspecialchars($libro['estado']) ?></td>
          <?php if ($esAdministrador): ?>
            <td class="acciones">
              <a href="editar.php?tipo=libro&amp;id=<?= (int) $libro['id'] ?>">Editar</a>
              <form method="post" action="eliminar.php">
                <?= campoCsrf() ?>
                <input type="hidden" name="tipo" value="libro">
                <input type="hidden" name="id" value="<?= (int) $libro['id'] ?>">
                <button>Eliminar</button>
              </form>
            </td>
          <?php endif; ?>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>

<?php require 'pie.php'; ?>