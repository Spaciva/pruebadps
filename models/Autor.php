<?php
/**
 * Modelo Autor — Biblioteca Fusalmo
 * CRUD completo para autores
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/security.php';

class Autor {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Obtiene todos los autores.
     */
    public function getAll(): array {
        $stmt = $this->db->query(
            "SELECT id, nombre, nacionalidad, created_at
             FROM autores
             ORDER BY nombre"
        );
        return $stmt->fetchAll();
    }

    /**
     * Obtiene un autor por ID.
     */
    public function getById(int $id): array|false {
        $stmt = $this->db->prepare(
            "SELECT id, nombre, nacionalidad, created_at
             FROM autores
             WHERE id = :id
             LIMIT 1"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Verifica si un autor existe por nombre.
     */
    public function existsByName(string $nombre, int $excludeId = 0): bool {
        $sql = "SELECT COUNT(*) FROM autores WHERE nombre = :nombre";
        if ($excludeId > 0) $sql .= " AND id != :id";
        
        $stmt = $this->db->prepare($sql);
        $params = [':nombre' => $nombre];
        if ($excludeId > 0) $params[':id'] = $excludeId;
        
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Obtiene un autor por nombre.
     */
    public function getByName(string $nombre): array|false {
        $stmt = $this->db->prepare(
            "SELECT id, nombre, nacionalidad, created_at
             FROM autores
             WHERE nombre = :nombre
             LIMIT 1"
        );
        $stmt->execute([':nombre' => $nombre]);
        return $stmt->fetch();
    }

    /**
     * Crea un nuevo autor.
     */
    public function create(array $data): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO autores (nombre, nacionalidad, created_at)
             VALUES (:nombre, :nacionalidad, NOW())"
        );
        return $stmt->execute([
            ':nombre'       => Security::sanitizeString($data['nombre'] ?? ''),
            ':nacionalidad' => Security::sanitizeString($data['nacionalidad'] ?? ''),
        ]);
    }

    /**
     * Actualiza un autor.
     */
    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare(
            "UPDATE autores 
             SET nombre = :nombre,
                 nacionalidad = :nacionalidad
             WHERE id = :id"
        );
        return $stmt->execute([
            ':id'           => $id,
            ':nombre'       => Security::sanitizeString($data['nombre'] ?? ''),
            ':nacionalidad' => Security::sanitizeString($data['nacionalidad'] ?? ''),
        ]);
    }

    /**
     * Elimina un autor.
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM autores WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Obtiene cantidad de libros por autor.
     */
    public function getLibrosCount(int $id): int {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM libros WHERE autor_id = :id"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetchColumn();
    }
}
