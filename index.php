<?php
$titulo = 'Inicio';
$encabezado = 'Biblioteca municipal';
require 'cabecera.php';
?>

<section class="portada-hero" aria-labelledby="bienvenida">
  <div class="portada-mensaje">
    <p class="sobretitulo">Un espacio para descubrir, aprender y compartir</p>
    <h2 id="bienvenida">Tu próxima historia está más cerca.</h2>
    <p>Explora nuestro catálogo, encuentra nuevas voces y déjate sorprender por una buena lectura.</p>
    <a class="boton-enlace" href="libros.php">Explorar el catálogo</a>
  </div>

  <div class="busqueda-portada-card">
    <p class="etiqueta-card">Empieza por una idea</p>
    <h3>¿Qué te gustaría leer?</h3>
    <p>Busca un título o escribe el nombre de tu autor favorito.</p>
    <form action="libros.php" method="get" class="busqueda-portada">
      <label class="solo-lectores" for="busqueda-portada">Título o autor</label>
      <input type="search" id="busqueda-portada" name="q" placeholder="Título o autor favorito">
      <button>Buscar libros</button>
    </form>
  </div>
</section>

<section class="portada-tarjetas" aria-label="Explora la biblioteca">
  <article class="tarjeta-portada">
    <span class="numero-tarjeta">01</span>
    <h3>¿Quién es tu autor favorito?</h3>
    <p>Encuentra sus libros en el catálogo y descubre nuevas lecturas.</p>
    <a href="libros.php">Buscar por autor <span aria-hidden="true">→</span></a>
  </article>
  <article class="tarjeta-portada">
    <span class="numero-tarjeta">02</span>
    <h3>¿Ya tienes una lectura en mente?</h3>
    <p>Consulta qué libros están disponibles y anímate a empezar.</p>
    <a href="libros.php">Ver libros <span aria-hidden="true">→</span></a>
  </article>
  <article class="tarjeta-portada tarjeta-destacada">
    <span class="numero-tarjeta">03</span>
    <h3>Lecturas que siguen su camino</h3>
    <p>Consulta los préstamos activos de nuestra biblioteca.</p>
    <a href="prestamos.php">Ver préstamos <span aria-hidden="true">→</span></a>
  </article>
</section>

<?php require 'pie.php'; ?>
