CREATE TABLE IF NOT EXISTS lectores (
  id INT NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(40) NOT NULL,
  apellidos VARCHAR(60) NOT NULL,
  telefono VARCHAR(15) NOT NULL,
  email VARCHAR(100) DEFAULT NULL,
  fecha_alta DATE NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

CREATE TABLE IF NOT EXISTS libros (
  id INT NOT NULL AUTO_INCREMENT,
  isbn VARCHAR(17) NOT NULL,
  titulo VARCHAR(100) NOT NULL,
  autor VARCHAR(80) NOT NULL,
  anio INT DEFAULT NULL,
  genero VARCHAR(20) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY isbn (isbn)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

CREATE TABLE IF NOT EXISTS prestamos (
  id INT NOT NULL AUTO_INCREMENT,
  libro_id INT NOT NULL,
  lector_id INT NOT NULL,
  fecha_prestamo DATE NOT NULL DEFAULT CURRENT_TIMESTAMP,
  fecha_devolucion DATE DEFAULT NULL,
  PRIMARY KEY (id),
  KEY libro_id (libro_id),
  KEY lector_id (lector_id),
  CONSTRAINT prestamos_ibfk_1 FOREIGN KEY (libro_id) REFERENCES libros (id),
  CONSTRAINT prestamos_ibfk_2 FOREIGN KEY (lector_id) REFERENCES lectores (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;