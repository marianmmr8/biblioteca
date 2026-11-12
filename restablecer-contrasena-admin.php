<?php
$esTerminal = PHP_SAPI === 'cli';

if (!$esTerminal) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    if (!in_array($ip, ['127.0.0.1', '::1'], true)) {
        http_response_code(404);
        exit;
    }

    require __DIR__ . '/seguridad.php';
    require __DIR__ . '/conexion.php';
    iniciarSesionSegura();

    $usuarioAdministrador = $pdo->query(
        'SELECT usuario FROM usuarios ORDER BY id LIMIT 1'
    )->fetchColumn();

    if (!isset($_SESSION['token_restablecer'])) {
        $_SESSION['token_restablecer'] = bin2hex(random_bytes(32));
    }

    $error = '';
    $completado = false;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['token'] ?? '';
        $usuario = is_string($usuarioAdministrador) ? $usuarioAdministrador : '';
        $contrasena = $_POST['contrasena'] ?? '';
        $confirmacion = $_POST['confirmacion'] ?? '';

        if (
            !is_string($token)
            || !hash_equals($_SESSION['token_restablecer'], $token)
        ) {
            http_response_code(403);
            exit('La página ha caducado. Recárgala e inténtalo de nuevo.');
        }

        if ($usuario === '' || strlen($usuario) > 50) {
            $error = 'Escribe el nombre de usuario de la cuenta.';
        } elseif (!is_string($contrasena) || strlen($contrasena) < 12) {
            $error = 'La contraseña debe tener al menos 12 caracteres.';
        } elseif (!is_string($confirmacion) || $contrasena !== $confirmacion) {
            $error = 'Las contraseñas no coinciden.';
        } else {
            $pdo->beginTransaction();

            try {
                $buscar = $pdo->prepare(
                    'SELECT id FROM usuarios WHERE usuario = ? FOR UPDATE'
                );
                $buscar->execute([$usuario]);

                if ($buscar->fetchColumn() === false) {
                    $pdo->rollBack();
                    $error = 'No se encontró la cuenta administradora. Comprueba que estás usando la base de datos local correcta.';
                } else {
                    $consulta = $pdo->prepare(
                        'UPDATE usuarios SET hash_contrasena = ? WHERE usuario = ?'
                    );
                    $consulta->execute([
                        password_hash($contrasena, PASSWORD_DEFAULT),
                        $usuario,
                    ]);

                    $comprobar = $pdo->prepare(
                        'SELECT hash_contrasena FROM usuarios WHERE usuario = ?'
                    );
                    $comprobar->execute([$usuario]);
                    $hashGuardado = $comprobar->fetchColumn();

                    if (
                        !is_string($hashGuardado)
                        || !password_verify($contrasena, $hashGuardado)
                    ) {
                        $pdo->rollBack();
                        $error = 'No se pudo verificar la contraseña guardada. Inténtalo otra vez.';
                    } else {
                        $pdo->commit();
                        session_regenerate_id(true);
                        $_SESSION = ['biblioteca_admin' => $usuario];
                        header('Location: prestamos.php');
                        exit;
                    }
                }
            } catch (Throwable $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                throw $e;
            }
        }
    }

    $escapar = static fn (string $valor): string => htmlspecialchars(
        $valor,
        ENT_QUOTES,
        'UTF-8'
    );
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>Restablecer contraseña · Biblioteca</title>
      <link rel="stylesheet" href="css/estilos.css">
    </head>
    <body>
      <main>
        <h1>Restablecer contraseña</h1>
        <?php if ($completado): ?>
          <p>Contraseña de <strong><?= $escapar($usuarioActualizado) ?></strong> actualizada. Usa ese mismo usuario al iniciar sesión.</p>
          <p><a href="login.php">Ir al inicio de sesión</a></p>
        <?php else: ?>
          <?php if ($error !== ''): ?>
            <p class="error"><?= $escapar($error) ?></p>
          <?php endif; ?>
          <p>Esta herramienta solo está disponible desde este ordenador. Restablecerá la cuenta <strong><?= $escapar(is_string($usuarioAdministrador) ? $usuarioAdministrador : 'sin configurar') ?></strong>. La contraseña distingue mayúsculas y minúsculas.</p>
          <form method="post" class="formulario">
            <input type="hidden" name="token" value="<?= $escapar($_SESSION['token_restablecer']) ?>">
            <?php if (is_string($usuarioAdministrador) && $usuarioAdministrador !== ''): ?>
            <input type="hidden" name="usuario" value="<?= $escapar($usuarioAdministrador) ?>">

            <label for="contrasena">Nueva contraseña (mínimo 12 caracteres)</label>
            <input type="password" id="contrasena" name="contrasena" autocomplete="new-password" minlength="12" required>

            <label for="confirmacion">Repite la contraseña nueva</label>
            <input type="password" id="confirmacion" name="confirmacion" autocomplete="new-password" minlength="12" required>
            <label><input type="checkbox" id="mostrar-contrasenas"> Mostrar contraseñas</label>

            <button type="submit">Guardar contraseña</button>
            <?php else: ?>
            <p class="error">No hay una cuenta administradora en esta base de datos.</p>
            <?php endif; ?>
          </form>
        <?php endif; ?>
      </main>
      <?php if (!$completado): ?>
      <script>
        document.getElementById('mostrar-contrasenas').addEventListener('change', function () {
          const tipo = this.checked ? 'text' : 'password';
          document.getElementById('contrasena').type = tipo;
          document.getElementById('confirmacion').type = tipo;
        });
      </script>
      <?php endif; ?>
    </body>
    </html>
    <?php
    exit;
}

$contrasena = getenv('BIBLIOTECA_ADMIN_PASSWORD');
if (!is_string($contrasena) || $contrasena === '') {
    fwrite(STDERR, "Define la nueva contraseña mediante BIBLIOTECA_ADMIN_PASSWORD antes de ejecutar este script.\n");
    exit(1);
}

echo 'Usuario administrador que quieres actualizar: ';
$entradaUsuario = fgets(STDIN);
$usuario = is_string($entradaUsuario) ? trim($entradaUsuario) : '';

if ($usuario === '' || strlen($usuario) > 50) {
    fwrite(STDERR, "El usuario debe tener entre 1 y 50 caracteres.\n");
    exit(1);
}

require __DIR__ . '/conexion.php';

$consulta = $pdo->prepare(
    'UPDATE usuarios SET hash_contrasena = ? WHERE usuario = ?'
);
$consulta->execute([password_hash($contrasena, PASSWORD_DEFAULT), $usuario]);

if ($consulta->rowCount() !== 1) {
    fwrite(STDERR, "No se encontró ese usuario o la contraseña no se pudo actualizar.\n");
    exit(1);
}

echo "Contraseña actualizada. Ya puedes iniciar sesión.\n";
