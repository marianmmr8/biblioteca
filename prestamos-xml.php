<?php
require 'seguridad.php';
exigirAutenticacion();
require 'conexion.php';
require 'modelo.php';

header('Content-Type: application/xml; charset=UTF-8');

$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><prestamos/>');

foreach (prestamosEnCurso($pdo) as $prestamo) {
    $nodo = $xml->addChild('prestamo');
    $nodo->addAttribute('id', (string) $prestamo['id']);
    $nodo->addChild('libro', htmlspecialchars($prestamo['titulo'], ENT_XML1, 'UTF-8'));
    $nodo->addChild('lector', htmlspecialchars(
        $prestamo['nombre'] . ' ' . $prestamo['apellidos'],
        ENT_XML1,
        'UTF-8'
    ));
    $nodo->addChild('fecha_prestamo', $prestamo['fecha_prestamo']);
}

echo $xml->asXML();