<?php
/**
 * Modelo Libro — Biblioteca Fusalmo
 * CRUD completo para gestión de libros
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/security.php';

class Libro {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Obtiene todos los libros.
     */
    public function getAll(): array {
        $stmt = $this->db->query(
            "SELECT l.id, l.titulo, a.nombre AS autor, c.nombre AS categoria,
                    l.isbn, l.cantidad, l.estado, l.created_at,
                    ROUND(AVG(cal.estrellas), 1) AS promedio_calificacion,
                    COUNT(cal.id)               AS total_calificaciones
             FROM libros l
             LEFT JOIN autores a        ON l.autor_id   = a.id
             LEFT JOIN categorias c     ON l.categoria_id = c.id
             LEFT JOIN calificaciones cal ON cal.libro_id  = l.id
             GROUP BY l.id, l.titulo, a.nombre, c.nombre,
                      l.isbn, l.cantidad, l.estado, l.created_at
             ORDER BY l.titulo"
        );
        return $stmt->fetchAll();
    }

    /**
     * Obtiene un libro por ID.
     */
    public function getById(int $id): array|false {
        $stmt = $this->db->prepare(
            "SELECT l.id, l.titulo, l.autor_id, l.categoria_id, 
                    l.isbn, l.cantidad, l.estado, l.descripcion, l.created_at
             FROM libros l
             WHERE l.id = :id
             LIMIT 1"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Obtiene libros por categoría.
     */
    public function getByCategoria(int $categoriaId): array {
        $stmt = $this->db->prepare(
            "SELECT id, titulo, isbn, cantidad, estado, created_at
             FROM libros
             WHERE categoria_id = :categoria_id
             ORDER BY titulo"
        );
        $stmt->execute([':categoria_id' => $categoriaId]);
        return $stmt->fetchAll();
    }

    /**
     * Obtiene libros por autor.
     */
    public function getByAutor(int $autorId): array {
        $stmt = $this->db->prepare(
            "SELECT id, titulo, isbn, cantidad, estado, created_at
             FROM libros
             WHERE autor_id = :autor_id
             ORDER BY titulo"
        );
        $stmt->execute([':autor_id' => $autorId]);
        return $stmt->fetchAll();
    }

    /**
     * Verifica si un ISBN existe.
     */
    public function isbnExists(string $isbn, int $excludeId = 0): bool {
        $sql = "SELECT COUNT(*) FROM libros WHERE isbn = :isbn";
        if ($excludeId > 0) $sql .= " AND id != :id";
        
        $stmt = $this->db->prepare($sql);
        $params = [':isbn' => $isbn];
        if ($excludeId > 0) $params[':id'] = $excludeId;
        
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Crea un nuevo libro.
     */
    public function create(array $data): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO libros (titulo, autor_id, categoria_id, isbn, cantidad, estado, descripcion, created_at)
             VALUES (:titulo, :autor_id, :categoria_id, :isbn, :cantidad, 'disponible', :descripcion, NOW())"
        );
        return $stmt->execute([
            ':titulo'       => Security::sanitizeString($data['titulo'] ?? ''),
            ':autor_id'     => Security::sanitizeInt($data['autor_id'] ?? 0),
            ':categoria_id' => Security::sanitizeInt($data['categoria_id'] ?? 0),
            ':isbn'         => Security::sanitizeString($data['isbn'] ?? ''),
            ':cantidad'     => Security::sanitizeInt($data['cantidad'] ?? 0),
            ':descripcion'  => Security::sanitizeString($data['descripcion'] ?? ''),
        ]);
    }

    /**
     * Actualiza un libro.
     */
    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare(
            "UPDATE libros 
             SET titulo = :titulo,
                 autor_id = :autor_id,
                 categoria_id = :categoria_id,
                 isbn = :isbn,
                 cantidad = :cantidad,
                 estado = :estado,
                 descripcion = :descripcion
             WHERE id = :id"
        );
        return $stmt->execute([
            ':id'           => $id,
            ':titulo'       => Security::sanitizeString($data['titulo'] ?? ''),
            ':autor_id'     => Security::sanitizeInt($data['autor_id'] ?? 0),
            ':categoria_id' => Security::sanitizeInt($data['categoria_id'] ?? 0),
            ':isbn'         => Security::sanitizeString($data['isbn'] ?? ''),
            ':cantidad'     => Security::sanitizeInt($data['cantidad'] ?? 0),
            ':estado'       => in_array($data['estado'] ?? '', ['disponible', 'reservado', 'agotado']) ? $data['estado'] : 'disponible',
            ':descripcion'  => Security::sanitizeString($data['descripcion'] ?? ''),
        ]);
    }

    /**
     * Actualiza solo la cantidad de un libro.
     */
    public function updateCantidad(int $id, int $cantidad): bool {
        $stmt = $this->db->prepare(
            "UPDATE libros SET cantidad = :cantidad WHERE id = :id"
        );
        return $stmt->execute([':cantidad' => max(0, $cantidad), ':id' => $id]);
    }

   /**
 * Elimina un libro.
 */
public function delete(int $id): bool {

    // Verificar si tiene préstamos asociados

    $stmt = $this->db->prepare(

        "SELECT COUNT(*)
         FROM prestamos
         WHERE libro_id = :id"

    );

    $stmt->execute([

        ':id' => $id

    ]);

    $prestamos = $stmt->fetchColumn();

    if($prestamos > 0){

        $_SESSION['error'] =
        'No se puede eliminar el libro porque tiene préstamos asociados';

        return false;
    }

    // Si no tiene préstamos, eliminar

    $stmt = $this->db->prepare(

        "DELETE FROM libros
         WHERE id = :id"

    );

    return $stmt->execute([

        ':id' => $id

    ]);
}

    /**
     * Busca libros por título o ISBN.
     */
    /**
 * Busca libros por título o ISBN.
 */
public function search(string $query): array {

    $searchTerm = "%{$query}%";

    $stmt = $this->db->prepare(

        "SELECT l.id,
                l.titulo,
                a.nombre AS autor,
                c.nombre AS categoria,
                l.isbn,
                l.cantidad,
                l.estado,
                ROUND(AVG(cal.estrellas), 1) AS promedio_calificacion,
                COUNT(cal.id)               AS total_calificaciones

        FROM libros l

        LEFT JOIN autores a
        ON l.autor_id = a.id

        LEFT JOIN categorias c
        ON l.categoria_id = c.id

        LEFT JOIN calificaciones cal
        ON cal.libro_id = l.id

        WHERE l.titulo LIKE :titulo
        OR l.isbn LIKE :isbn

        GROUP BY l.id, l.titulo, a.nombre, c.nombre,
                 l.isbn, l.cantidad, l.estado

        ORDER BY l.titulo"

    );

    $stmt->execute([

        ':titulo' => $searchTerm,
        ':isbn'   => $searchTerm

    ]);

    return $stmt->fetchAll();
}
}
