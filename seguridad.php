<?php

function iniciarSesionSegura(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function usuarioAutenticado(): bool
{
    iniciarSesionSegura();

    return isset($_SESSION['biblioteca_admin']);
}

function exigirAutenticacion(): void
{
    if (!usuarioAutenticado()) {
        header('Location: login.php');
        exit;
    }

    header('Cache-Control: no-store, private');
    header('Pragma: no-cache');
}

function tokenCsrf(): string
{
    iniciarSesionSegura();

    if (!isset($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function campoCsrf(): string
{
    return '<input type="hidden" name="csrf_token" value="' .
        htmlspecialchars(tokenCsrf(), ENT_QUOTES, 'UTF-8') . '">';
}

function validarCsrf(): void
{
    iniciarSesionSegura();

    $token = $_POST['csrf_token'] ?? null;
    if (
        !is_string($token)
        || !isset($_SESSION['csrf_token'])
        || !hash_equals($_SESSION['csrf_token'], $token)
    ) {
        http_response_code(403);
        exit('La solicitud ha caducado o no es válida. Recarga el formulario e inténtalo de nuevo.');
    }
}
