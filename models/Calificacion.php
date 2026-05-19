<?php
/**
 * Modelo Calificacion — Biblioteca Fusalmo
 * Gestión de calificaciones (1-5 estrellas) por libro y usuario
 */

require_once __DIR__ . '/../config/db.php';

class Calificacion {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Registra o reemplaza la calificación de un usuario para un libro.
     * Un usuario solo puede tener una calificación por libro.
     */
    public function rate(int $usuarioId, int $libroId, int $estrellas): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO calificaciones (usuario_id, libro_id, estrellas)
             VALUES (:usuario_id, :libro_id, :estrellas)
             ON DUPLICATE KEY UPDATE estrellas  = :estrellas2,
                                     updated_at = CURRENT_TIMESTAMP"
        );
        return $stmt->execute([
            ':usuario_id' => $usuarioId,
            ':libro_id'   => $libroId,
            ':estrellas'  => $estrellas,
            ':estrellas2' => $estrellas,
        ]);
    }

    /**
     * Devuelve todas las calificaciones del usuario como [libro_id => estrellas].
     */
    public function getUserRatings(int $usuarioId): array {
        $stmt = $this->db->prepare(
            "SELECT libro_id, estrellas
             FROM calificaciones
             WHERE usuario_id = :usuario_id"
        );
        $stmt->execute([':usuario_id' => $usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }
}
