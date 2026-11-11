<?php if ($error): ?>
  <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="post" class="formulario">
  <?= campoCsrf() ?>
  <label for="isbn">ISBN</label>
  <input type="text" id="isbn" name="isbn" maxlength="17" required value="<?= htmlspecialchars($datos['isbn'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

  <label for="titulo_libro">Título</label>
  <input type="text" id="titulo_libro" name="titulo" maxlength="100" required value="<?= htmlspecialchars($datos['titulo'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

  <label for="autor">Autor</label>
  <input type="text" id="autor" name="autor" maxlength="80" required value="<?= htmlspecialchars($datos['autor'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

  <label for="anio">Año</label>
  <input type="number" id="anio" name="anio" min="1" max="9999" value="<?= htmlspecialchars((string) ($datos['anio'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">

  <label for="genero">Género</label>
  <input type="text" id="genero" name="genero" maxlength="20" required value="<?= htmlspecialchars($datos['genero'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

  <button>Guardar libro</button>
</form>