<?php
require 'seguridad.php';
iniciarSesionSegura();

if (usuarioAutenticado()) {
    header('Location: prestamos.php');
    exit;
}

$credenciales = credencialesAdministrador();
$error = '';
$usuario = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validarCsrf();

    $usuarioSolicitado = $_POST['usuario'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';
    $usuario = is_string($usuarioSolicitado) ? trim($usuarioSolicitado) : '';

    if (!is_string($contrasena)) {
        $contrasena = '';
    }

    if (!$credenciales) {
        $error = 'Falta configurar el acceso administrativo en config.local.php.';
    } elseif (
        hash_equals($credenciales['usuario'], $usuario)
        && password_verify($contrasena, $credenciales['hash'])
    ) {
        session_regenerate_id(true);
        $_SESSION = ['biblioteca_admin' => $credenciales['usuario']];
        header('Location: prestamos.php');
        exit;
    } else {
        $error = 'El usuario o la contraseña no son correctos.';
    }
}

$titulo = 'Acceso de administración';
require 'cabecera.php';
?>

<?php if ($error): ?>
  <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<?php if (!$credenciales): ?>
  <p>Añade <code>admin_username</code> y <code>admin_password_hash</code> a <code>config.local.php</code>.</p>
  <p>Genera el hash con PHP usando <code>password_hash('tu-contraseña', PASSWORD_DEFAULT)</code>.</p>
<?php else: ?>
  <form method="post" class="formulario">
    <?= campoCsrf() ?>
    <label for="usuario">Usuario</label>
    <input type="text" id="usuario" name="usuario" autocomplete="username" required value="<?= htmlspecialchars($usuario, ENT_QUOTES, 'UTF-8') ?>">

    <label for="contrasena">Contraseña</label>
    <input type="password" id="contrasena" name="contrasena" autocomplete="current-password" required>

    <button type="submit">Iniciar sesión</button>
  </form>
<?php endif; ?>

<?php require 'pie.php'; ?>
