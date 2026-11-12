<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$contrasena = getenv('BIBLIOTECA_ADMIN_PASSWORD');
if (!is_string($contrasena) || $contrasena === '') {
    fwrite(STDERR, "Define la contraseña mediante BIBLIOTECA_ADMIN_PASSWORD antes de ejecutar este script.\n");
    exit(1);
}

echo 'Usuario administrador: ';
$entradaUsuario = fgets(STDIN);
$usuario = is_string($entradaUsuario) ? trim($entradaUsuario) : '';

if ($usuario === '' || strlen($usuario) > 50) {
    fwrite(STDERR, "El usuario debe tener entre 1 y 50 caracteres.\n");
    exit(1);
}

require __DIR__ . '/conexion.php';

try {
    if ((int) $pdo->query('SELECT COUNT(*) FROM usuarios')->fetchColumn() > 0) {
        fwrite(STDERR, "Ya existe una cuenta administradora.\n");
        exit(1);
    }

    $consulta = $pdo->prepare(
        'INSERT INTO usuarios (usuario, hash_contrasena) VALUES (?, ?)'
    );
    $consulta->execute([$usuario, password_hash($contrasena, PASSWORD_DEFAULT)]);
} catch (PDOException $e) {
    fwrite(STDERR, "No se pudo crear la cuenta administradora: {$e->getMessage()}\n");
    exit(1);
}

echo "Cuenta administradora creada.\n";
