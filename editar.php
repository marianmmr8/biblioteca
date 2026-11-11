<?php
require 'conexion.php';
require 'modelo.php';

$tipo = $_GET['tipo'] ?? '';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$esLector = $tipo === 'lector';
$esLibro = $tipo === 'libro';

if ((!$esLector && !$esLibro) || !$id) {
    http_response_code(404);
    $titulo = 'Registro no encontrado';
    require 'cabecera.php';
    echo '<p>No se ha encontrado el registro. <a href="index.php">Volver al inicio</a></p>';
    require 'pie.php';
    exit;
}

$registro = $esLector ? lectorPorId($pdo, $id) : libroPorId($pdo, $id);
if (!$registro) {
    http_response_code(404);
    $titulo = $esLector ? 'Lector no encontrado' : 'Libro no encontrado';
    $listado = $esLector ? 'lectores.php' : 'libros.php';
    require 'cabecera.php';
    echo '<p>No se ha encontrado el registro. <a href="' . $listado . '">Volver al listado</a></p>';
    require 'pie.php';
    exit;
}

$datos = $registro;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($esLector) {
        foreach (['nombre', 'apellidos', 'telefono', 'email'] as $campo) {
            $datos[$campo] = trim((string) ($_POST[$campo] ?? ''));
        }
        $datos['email'] = $datos['email'] !== '' ? $datos['email'] : null;

        if (!$datos['nombre'] || !$datos['apellidos'] || !$datos['telefono']) {
            $error = 'Completa el nombre, los apellidos y el teléfono.';
        } elseif ($datos['email'] !== null && !filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            $error = 'Introduce un correo electrónico válido.';
        }
    } else {
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
        }
    }

    if (!$error) {
        try {
            if ($esLector) {
                actualizarLector($pdo, $id, $datos);
                header('Location: lectores.php?msg=actualizado');
            } else {
                actualizarLibro($pdo, $id, $datos);
                header('Location: libros.php?msg=actualizado');
            }
            exit;
        } catch (PDOException $e) {
            $error = esDuplicado($e)
                ? ($esLector ? 'Ese correo ya existe.' : 'Ese ISBN ya existe.')
                : 'No se han podido guardar los cambios.';
        }
    }
}

$titulo = $esLector ? 'Editar lector' : 'Editar libro';
require 'cabecera.php';
if ($esLector) {
    require '_formulario-lector.php';
} else {
    require '_formulario-libro.php';
}
require 'pie.php';