<?php

function prestamosEnCurso(PDO $pdo): array
{
    $consulta = $pdo->query(
        'SELECT p.id, l.titulo, le.id AS lector_id, le.nombre, le.apellidos,
                p.fecha_prestamo, DATEDIFF(CURDATE(), p.fecha_prestamo) AS dias
         FROM prestamos p
         JOIN libros l ON l.id = p.libro_id
         JOIN lectores le ON le.id = p.lector_id
         WHERE p.fecha_devolucion IS NULL
         ORDER BY p.fecha_prestamo'
    );

    return $consulta->fetchAll();
}

function librosDisponibles(PDO $pdo): array
{
    $consulta = $pdo->query(
        'SELECT id, titulo, autor FROM libros
         WHERE id NOT IN (SELECT libro_id FROM prestamos WHERE fecha_devolucion IS NULL)
         ORDER BY titulo'
    );

    return $consulta->fetchAll();
}

function prestar(PDO $pdo, int $libroId, int $lectorId): bool
{
    $pdo->beginTransaction();

    try {
        $bloqueo = $pdo->prepare('SELECT id FROM libros WHERE id = ? FOR UPDATE');
        $bloqueo->execute([$libroId]);

        $comprobar = $pdo->prepare(
            'SELECT COUNT(*) FROM prestamos WHERE libro_id = ? AND fecha_devolucion IS NULL'
        );
        $comprobar->execute([$libroId]);

        if ($comprobar->fetchColumn() > 0) {
            $pdo->rollBack();
            return false;
        }

        $insertar = $pdo->prepare(
            'INSERT INTO prestamos (libro_id, lector_id, fecha_prestamo) VALUES (?, ?, CURDATE())'
        );
        $insertar->execute([$libroId, $lectorId]);
        $pdo->commit();

        return true;
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}

function lectorPorId(PDO $pdo, int $id): array|false
{
    $consulta = $pdo->prepare(
        'SELECT id, nombre, apellidos, telefono, email, fecha_alta FROM lectores WHERE id = ?'
    );
    $consulta->execute([$id]);

    return $consulta->fetch();
}

function prestamosDeLector(PDO $pdo, int $lectorId): array
{
    $consulta = $pdo->prepare(
        'SELECT l.titulo, p.fecha_prestamo, p.fecha_devolucion
         FROM prestamos p
         JOIN libros l ON l.id = p.libro_id
         WHERE p.lector_id = ?
         ORDER BY p.fecha_prestamo DESC'
    );
    $consulta->execute([$lectorId]);

    return $consulta->fetchAll();
}

function devolverPrestamo(PDO $pdo, int $id): bool
{
    $consulta = $pdo->prepare(
        'UPDATE prestamos SET fecha_devolucion = CURDATE()
         WHERE id = ? AND fecha_devolucion IS NULL'
    );
    $consulta->execute([$id]);

    return $consulta->rowCount() === 1;
}

function buscarLibros(PDO $pdo, string $busqueda): array
{
    $consulta = $pdo->prepare(
        'SELECT l.id, l.isbn, l.titulo, l.autor, l.anio, l.genero,
            CASE WHEN EXISTS (
                    SELECT 1 FROM prestamos p
                    WHERE p.libro_id = l.id AND p.fecha_devolucion IS NULL
            ) THEN \'Prestado\' ELSE \'Disponible\' END AS estado
         FROM libros l
         WHERE l.titulo LIKE ? OR l.autor LIKE ?
         ORDER BY l.titulo'
    );
    $termino = '%' . $busqueda . '%';
    $consulta->execute([$termino, $termino]);

    return $consulta->fetchAll();
}

function listaLectores(PDO $pdo): array
{
    $consulta = $pdo->query(
        'SELECT id, nombre, apellidos, telefono, email, fecha_alta
         FROM lectores ORDER BY apellidos, nombre'
    );

    return $consulta->fetchAll();
}

function crearLector(PDO $pdo, array $datos): void
{
    $consulta = $pdo->prepare(
        'INSERT INTO lectores (nombre, apellidos, telefono, email, fecha_alta)
         VALUES (?, ?, ?, ?, CURDATE())'
    );
    $consulta->execute([
        $datos['nombre'],
        $datos['apellidos'],
        $datos['telefono'],
        $datos['email'],
    ]);
}

function actualizarLector(PDO $pdo, int $id, array $datos): void
{
    $consulta = $pdo->prepare(
        'UPDATE lectores SET nombre = ?, apellidos = ?, telefono = ?, email = ? WHERE id = ?'
    );
    $consulta->execute([
        $datos['nombre'],
        $datos['apellidos'],
        $datos['telefono'],
        $datos['email'],
        $id,
    ]);
}

function libroPorId(PDO $pdo, int $id): array|false
{
    $consulta = $pdo->prepare(
        'SELECT id, isbn, titulo, autor, anio, genero FROM libros WHERE id = ?'
    );
    $consulta->execute([$id]);

    return $consulta->fetch();
}

function crearLibro(PDO $pdo, array $datos): void
{
    $consulta = $pdo->prepare(
        'INSERT INTO libros (isbn, titulo, autor, anio, genero) VALUES (?, ?, ?, ?, ?)'
    );
    $consulta->execute([
        $datos['isbn'],
        $datos['titulo'],
        $datos['autor'],
        $datos['anio'],
        $datos['genero'],
    ]);
}

function actualizarLibro(PDO $pdo, int $id, array $datos): void
{
    $consulta = $pdo->prepare(
        'UPDATE libros SET isbn = ?, titulo = ?, autor = ?, anio = ?, genero = ? WHERE id = ?'
    );
    $consulta->execute([
        $datos['isbn'],
        $datos['titulo'],
        $datos['autor'],
        $datos['anio'],
        $datos['genero'],
        $id,
    ]);
}

function eliminarLector(PDO $pdo, int $id): void
{
    $consulta = $pdo->prepare('DELETE FROM lectores WHERE id = ?');
    $consulta->execute([$id]);
}

function eliminarLibro(PDO $pdo, int $id): void
{
    $consulta = $pdo->prepare('DELETE FROM libros WHERE id = ?');
    $consulta->execute([$id]);
}

function totalLibros(PDO $pdo): int
{
    return (int) $pdo->query('SELECT COUNT(*) FROM libros')->fetchColumn();
}

function esDuplicado(PDOException $e): bool
{
    return isset($e->errorInfo[1]) && (int) $e->errorInfo[1] === 1062;
}