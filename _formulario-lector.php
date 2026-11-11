<?php if ($error): ?>
  <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="post" class="formulario">
  <?= campoCsrf() ?>
  <label for="nombre">Nombre</label>
  <input type="text" id="nombre" name="nombre" maxlength="40" required value="<?= htmlspecialchars($datos['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

  <label for="apellidos">Apellidos</label>
  <input type="text" id="apellidos" name="apellidos" maxlength="60" required value="<?= htmlspecialchars($datos['apellidos'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

  <label for="telefono">Teléfono</label>
  <input type="tel" id="telefono" name="telefono" maxlength="15" required value="<?= htmlspecialchars($datos['telefono'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

  <label for="email">Correo electrónico</label>
  <input type="email" id="email" name="email" maxlength="100" value="<?= htmlspecialchars($datos['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

  <button>Guardar lector</button>
</form>