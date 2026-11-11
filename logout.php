<?php
require 'seguridad.php';
iniciarSesionSegura();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

validarCsrf();
$parametrosCookie = session_get_cookie_params();
$_SESSION = [];
session_destroy();
setcookie(session_name(), '', [
    'expires' => time() - 42000,
    'path' => $parametrosCookie['path'],
    'domain' => $parametrosCookie['domain'],
    'secure' => $parametrosCookie['secure'],
    'httponly' => $parametrosCookie['httponly'],
    'samesite' => $parametrosCookie['samesite'] ?? 'Lax',
]);

header('Location: index.php');
exit;
