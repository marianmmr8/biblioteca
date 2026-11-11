<?php
require 'conexion.php';
require 'modelo.php';

$total = totalLibros($pdo);
echo "Conexión correcta: hay $total libros.";