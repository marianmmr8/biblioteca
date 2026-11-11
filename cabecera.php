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
    <p class="marca">Biblioteca municipal</p>
    <nav>
      <a href="index.php">Préstamos en curso</a>
      <a href="nuevo.php">Nuevo préstamo</a>
      <a href="libros.php">Libros</a>
      <a href="lectores.php">Lectores</a>
      <a href="prestamos-xml.php">XML</a>
    </nav>
  </header>
  <main>
    <h1><?= htmlspecialchars($titulo) ?></h1>