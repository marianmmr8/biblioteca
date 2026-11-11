<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($titulo) ?> · Biblioteca</title>
  <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
  <header>
    <div class="identidad">
      <svg class="logo-biblioteca" viewBox="0 0 48 48" aria-hidden="true">
        <path d="M7 39h34M10 35h28M13 35V15l7-4v24M22 35V9l7 4v22M32 35V17l6-3v21M9 39v3h30v-3" />
      </svg>
      <div>
        <p class="marca">Biblioteca municipal</p>
        <p class="marca-subtitulo">Lectura, cultura y comunidad</p>
      </div>
    </div>
    <nav aria-label="Navegación principal">
      <a href="index.php">Inicio</a>
      <a href="prestamos.php">Préstamos</a>
      <a href="nuevo.php">Nuevo préstamo</a>
      <a href="libros.php">Catálogo</a>
      <a href="lectores.php">Lectores</a>
      <a href="prestamos-xml.php">Exportar XML</a>
    </nav>
  </header>
  <main>
    <h1><?= htmlspecialchars($encabezado ?? $titulo) ?></h1>