# Despliegue en InfinityFree

## 1. Crear la base de datos

Desde el panel de InfinityFree, crea una base MySQL y guarda el host, nombre de la base, usuario y contraseña que muestra el panel. No uses las credenciales `root` de XAMPP. El host de MySQL puede no ser `localhost`; utiliza exactamente el valor del panel.

En phpMyAdmin, selecciona esa base e importa `biblioteca-esquema.sql`. El archivo crea las tablas vacías, no incluye los datos de prueba de XAMPP. Si ya hay datos en la base de destino, haz una copia de seguridad antes de importar.

## 2. Configurar la conexión

Copia `config.example.php` como `config.local.php` dentro de la carpeta de la aplicación y sustituye los cuatro valores por los del panel:

- `host`: servidor MySQL
- `database`: nombre completo de la base
- `username`: usuario completo de MySQL
- `password`: contraseña de MySQL

`config.local.php` está excluido de Git y protegido por `.htaccess`. No publiques ni compartas ese archivo. En XAMPP, si no existe, la aplicación sigue usando `localhost`, la base `biblioteca` y el usuario `root` sin contraseña.

## 3. Subir los archivos

Sube los archivos PHP, `css/`, `.htaccess` y `config.local.php` por el administrador de archivos o FTP. Para conservar la ruta `/biblioteca/`, colócalos dentro de `htdocs/biblioteca/` en el hosting. No subas `config.example.php` en lugar de la configuración real.

## 4. Comprobar el sitio

Configura PHP 8.0 o posterior y comprueba que estén disponibles PDO MySQL y SimpleXML. Abre la URL que proporciona InfinityFree y prueba las páginas, el alta de un libro y lector, un préstamo y su devolución. Activa HTTPS desde el panel cuando el dominio esté listo.