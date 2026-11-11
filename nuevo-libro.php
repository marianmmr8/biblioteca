<?php
require 'seguridad.php';
exigirAutenticacion();
require 'conexion.php';
require 'modelo.php';

$datos = ['isbn' => '', 'titulo' => '', 'autor' => '', 'anio' => '', 'genero' => ''];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validarCsrf();
    foreach (['isbn', 'titulo', 'autor', 'anio', 'genero'] as $campo) {
        $datos[$campo] = trim((string) ($_POST[$campo] ?? ''));
    }
    $anio = $datos['anio'] === '' ? null : filter_var($datos['anio'], FILTER_VALIDATE_INT);
    $datos['anio'] = $anio;

    if (!$datos['isbn'] || !$datos['titulo'] || !$datos['autor'] || !$datos['genero']) {
        $error = 'Completa el ISBN, el título, el autor y el género.';
    } elseif ($datos['anio'] === false || ($datos['anio'] !== null && $datos['anio'] < 1)) {
        $error = 'El año debe ser un número válido.';
        $datos['anio'] = $_POST['anio'] ?? '';
    } else {
        try {
            crearLibro($pdo, $datos);
            header('Location: libros.php?msg=creado');
            exit;
        } catch (PDOException $e) {
            $error = esDuplicado($e)
                ? 'Ese ISBN ya existe.'
                : 'No se ha podido registrar el libro.';
        }
    }
}

$titulo = 'Añadir libro';
require 'cabecera.php';
require '_formulario-libro.php';
require 'pie.php';