<?php
/**
 * Modelo Usuario — Biblioteca Fusalmo
 * Todas las consultas usan Prepared Statements (previene SQL Injection)
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/security.php';

class Usuario {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Busca un usuario por correo o nombre de usuario (login).
     * Uso de Prepared Statement para evitar SQL Injection.
     */
    public function findByCredential(string $credential): array|false {

    $stmt = $this->db->prepare(
        "SELECT id, nombre, correo, contrasena, rol, estado
         FROM usuarios
         WHERE (correo = :correo OR nombre = :nombre)
         AND estado = 'activo'
         LIMIT 1"
    );

    $stmt->execute([
        ':correo' => $credential,
        ':nombre' => $credential
    ]);

    return $stmt->fetch();
}

    /**
     * Registra un nuevo usuario.
     */
    public function create(array $data): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO usuarios (nombre, correo, contrasena, telefono, rol, estado, created_at)
             VALUES (:nombre, :correo, :contrasena, :telefono, :rol, 'activo', NOW())"
        );
        return $stmt->execute([
            ':nombre'    => Security::sanitizeString($data['nombre']),
            ':correo'    => Security::sanitizeEmail($data['correo']),
            ':contrasena'=> Security::hashPassword($data['contrasena']),
            ':telefono'  => Security::sanitizeString($data['telefono'] ?? ''),
            ':rol'       => $data['rol'] ?? 'usuario',
        ]);
    }

    /**
     * Verifica si el correo ya está registrado.
     */
    public function emailExists(string $email): bool {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM usuarios WHERE correo = :email"
        );
        $stmt->execute([':email' => $email]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Obtiene todos los usuarios (solo admin).
     */
    public function getAll(): array {

    $stmt = $this->db->query(

        "SELECT
            id,
            nombre,
            correo,
            telefono,
            rol,
            estado,
            created_at
         FROM usuarios
         ORDER BY nombre"

    );

    return $stmt->fetchAll();
}

    /**
     * Obtiene un usuario por ID.
     */
    public function getById(int $id): array|false {
        $stmt = $this->db->prepare(
            "SELECT id, nombre, correo, telefono, rol, estado, created_at
             FROM usuarios
             WHERE id = :id
             LIMIT 1"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Actualiza el estado de un usuario.
     */
    public function updateStatus(int $id, string $estado): bool {
        $allowed = ['activo', 'inactivo'];
        if (!in_array($estado, $allowed)) return false;

        $stmt = $this->db->prepare(
            "UPDATE usuarios SET estado = :estado WHERE id = :id"
        );
        return $stmt->execute([':estado' => $estado, ':id' => $id]);
    }

    /**
     * Actualiza los datos de un usuario (admin).
     */
    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare(
            "UPDATE usuarios 
             SET nombre = :nombre, 
                 correo = :correo, 
                 telefono = :telefono, 
                 rol = :rol
             WHERE id = :id"
        );
        return $stmt->execute([
            ':id'       => $id,
            ':nombre'   => Security::sanitizeString($data['nombre'] ?? ''),
            ':correo'   => Security::sanitizeEmail($data['correo'] ?? ''),
            ':telefono' => Security::sanitizeString($data['telefono'] ?? ''),
            ':rol'      => $data['rol'] ?? 'usuario',
        ]);
    }

    /**
     * Actualiza la contraseña de un usuario.
     */
    public function updatePassword(int $id, string $password): bool {
        $stmt = $this->db->prepare(
            "UPDATE usuarios SET contrasena = :contrasena WHERE id = :id"
        );
        return $stmt->execute([
            ':id'          => $id,
            ':contrasena'  => Security::hashPassword($password),
        ]);
    }

    /**
     * Verifica si el correo existe (excluyendo un usuario específico).
     */
    public function emailExistsExcept(string $email, int $excludeId): bool {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM usuarios WHERE correo = :email AND id != :id"
        );
        $stmt->execute([':email' => $email, ':id' => $excludeId]);
        return $stmt->fetchColumn() > 0;
    }

    /**
 * Verifica si un usuario tiene préstamos asociados
 */
public function hasPrestamos(int $id): bool {

    $stmt = $this->db->prepare(
        "SELECT COUNT(*)
         FROM prestamos
         WHERE usuario_id = :id"
    );

    $stmt->execute([
        ':id' => $id
    ]);

    return $stmt->fetchColumn() > 0;
}

    /**
     * Elimina un usuario.
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare(
            "DELETE FROM usuarios WHERE id = :id"
        );
        return $stmt->execute([':id' => $id]);
    }
}
