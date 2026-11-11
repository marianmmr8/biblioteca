
<?php
require 'conexion.php';
require 'modelo.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lectorId = filter_input(INPUT_POST, 'lector_id', FILTER_VALIDATE_INT);
    $libroId = filter_input(INPUT_POST, 'libro_id', FILTER_VALIDATE_INT);

    if (!$lectorId || !$libroId) {
        $error = 'Elige un lector y un libro.';
    } else {
        try {
            if (!prestar($pdo, $libroId, $lectorId)) {
                $error = 'Ese libro ya está prestado.';
            } else {
                // Redirige para que F5 no repita el préstamo
                header('Location: prestamos.php?msg=prestado');
                exit;
            }
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $error = 'No se ha podido registrar el préstamo.';
        }
    }
}

$lectores = listaLectores($pdo);
$libros = librosDisponibles($pdo);

$titulo = 'Nuevo préstamo';
require 'cabecera.php';
?>

<?php if ($error): ?>
  <p class="error"><?= $error ?></p>
<?php endif; ?>

<form method="post" class="alta">
  <label for="lector_id">Lector</label>
  <select name="lector_id" id="lector_id" required>
    <option value="">Elige un lector…</option>
    <?php foreach ($lectores as $le): ?>
      <option value="<?= $le['id'] ?>">
        <?= htmlspecialchars($le['apellidos'] . ', ' . $le['nombre']) ?>
      </option>
    <?php endforeach; ?>
  </select>

  <label for="libro_id">Libro disponible</label>
  <select name="libro_id" id="libro_id" required>
    <option value="">Elige un libro…</option>
    <?php foreach ($libros as $li): ?>
      <option value="<?= $li['id'] ?>">
        <?= htmlspecialchars($li['titulo'] . ' (' . $li['autor'] . ')') ?>
      </option>
    <?php endforeach; ?>
  </select>

  <button>Prestar</button>
</form>

<?php require 'pie.php'; ?>
