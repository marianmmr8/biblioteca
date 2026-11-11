<?php
$archivoConfiguracion = __DIR__ . '/config.local.php';
$configuracion = is_file($archivoConfiguracion) ? require $archivoConfiguracion : [];

if (!is_array($configuracion)) {
    exit('La configuración de la base de datos no es válida.');
}

$host = $configuracion['host'] ?? getenv('DB_HOST');
$baseDatos = $configuracion['database'] ?? getenv('DB_NAME');
$usuario = $configuracion['username'] ?? getenv('DB_USER');
$contrasena = $configuracion['password'] ?? getenv('DB_PASSWORD');

$host = $host ?: 'localhost';
$baseDatos = $baseDatos ?: 'biblioteca';
$usuario = $usuario ?: 'root';
$contrasena = $contrasena === false || $contrasena === null ? '' : $contrasena;

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$baseDatos;charset=utf8mb4",
        $usuario,
        $contrasena,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    exit('No se ha podido conectar con la base de datos.');
}
