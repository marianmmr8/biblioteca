<?php
require 'seguridad.php';
require 'conexion.php';
iniciarSesionSegura();

if (usuarioAutenticado()) {
    header('Location: prestamos.php');
    exit;
}

$hayAdministrador = (bool) $pdo->query('SELECT COUNT(*) FROM usuarios')->fetchColumn();
$usuarioAdministrador = $pdo->query(
    'SELECT usuario FROM usuarios ORDER BY id LIMIT 1'
)->fetchColumn();
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

    $consulta = $pdo->prepare(
        'SELECT usuario, hash_contrasena FROM usuarios WHERE usuario = ?'
    );
    $consulta->execute([$usuario]);
    $cuenta = $consulta->fetch();

    if ($cuenta && password_verify($contrasena, $cuenta['hash_contrasena'])) {
        session_regenerate_id(true);
        $_SESSION = ['biblioteca_admin' => $cuenta['usuario']];
        header('Location: prestamos.php');
        exit;
    }

    if ($hayAdministrador) {
        $error = 'El usuario o la contraseña no son correctos.';
    }
}

$titulo = 'Acceso de administración';
require 'cabecera.php';
?>

<?php if ($error): ?>
  <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<?php if (!$hayAdministrador): ?>
  <p>Aún no hay una cuenta administradora. Créala desde PowerShell con el script <code>crear-administrador.php</code>.</p>
<?php else: ?>
  <form method="post" class="formulario">
    <?= campoCsrf() ?>
    <p>Usuario administrador configurado: <strong><?= htmlspecialchars($usuarioAdministrador, ENT_QUOTES, 'UTF-8') ?></strong></p>
    <label for="usuario">Usuario</label>
    <input type="text" id="usuario" name="usuario" autocomplete="username" required value="<?= htmlspecialchars($usuario, ENT_QUOTES, 'UTF-8') ?>">

    <label for="contrasena">Contraseña</label>
    <input type="password" id="contrasena" name="contrasena" autocomplete="current-password" required>
    <label><input type="checkbox" id="mostrar-contrasena"> Mostrar contraseña</label>

    <button type="submit">Iniciar sesión</button>
  </form>
  <script>
    document.getElementById('mostrar-contrasena').addEventListener('change', function () {
      document.getElementById('contrasena').type = this.checked ? 'text' : 'password';
    });
  </script>
<?php endif; ?>

<?php require 'pie.php'; ?>
