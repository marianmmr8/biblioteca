<?php
require 'seguridad.php';
exigirAutenticacion();
require 'conexion.php';
require 'modelo.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

validarCsrf();

$tipo = $_POST['tipo'] ?? '';
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if ((!in_array($tipo, ['lector', 'libro'], true)) || !$id) {
    http_response_code(400);
    exit;
}

$listado = $tipo === 'lector' ? 'lectores.php' : 'libros.php';
$nombre = $tipo === 'lector' ? 'lector' : 'libro';

try {
    if ($tipo === 'lector') {
        eliminarLector($pdo, $id);
    } else {
        eliminarLibro($pdo, $id);
    }
    header('Location: ' . $listado . '?msg=eliminado');
} catch (PDOException $e) {
    $mensaje = isset($e->errorInfo[1]) && (int) $e->errorInfo[1] === 1451
        ? 'con_prestamos'
        : 'error';
    header('Location: ' . $listado . '?msg=' . $mensaje);
}
exit;