<?php
require 'conexion.php';
require 'modelo.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if ($id) {
        if (devolverPrestamo($pdo, $id)) {
            header('Location: prestamos.php?msg=devuelto');
            exit;
        }
    }
}

// Si se abre sin formulario o el préstamo no existe, vuelve al listado sin avisar
header('Location: prestamos.php');
exit;