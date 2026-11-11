<?php
require 'seguridad.php';
exigirAutenticacion();
require 'conexion.php';
require 'modelo.php';

$datos = ['nombre' => '', 'apellidos' => '', 'telefono' => '', 'email' => ''];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validarCsrf();
    foreach ($datos as $campo => $valor) {
        $datos[$campo] = trim((string) ($_POST[$campo] ?? ''));
    }
    $datos['email'] = $datos['email'] !== '' ? $datos['email'] : null;

    if (!$datos['nombre'] || !$datos['apellidos'] || !$datos['telefono']) {
        $error = 'Completa el nombre, los apellidos y el teléfono.';
    } elseif ($datos['email'] !== null && !filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Introduce un correo electrónico válido.';
    } else {
        try {
            crearLector($pdo, $datos);
            header('Location: lectores.php?msg=creado');
            exit;
        } catch (PDOException $e) {
            $error = esDuplicado($e)
                ? 'Ese correo ya existe.'
                : 'No se ha podido registrar el lector.';
        }
    }
}

$titulo = 'Añadir lector';
require 'cabecera.php';
require '_formulario-lector.php';
require 'pie.php';