<?php
/**
 * Configuración de conexión a la base de datos
 * Biblioteca Fusalmo — DSS 404 G03T
 */

define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'biblioteca_fusalmo');
define('DB_CHARSET', 'utf8mb4');

class Database {
    private static ?PDO $connection = null;

    public static function getConnection(): PDO {

        if (self::$connection === null) {

            $dsn = "mysql:host=" . DB_HOST .
                   ";port=" . DB_PORT .
                   ";dbname=" . DB_NAME .
                   ";charset=" . DB_CHARSET;

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            try {

                self::$connection = new PDO(
                    $dsn,
                    DB_USER,
                    DB_PASS,
                    $options
                );

            } catch (PDOException $e) {

                error_log("DB Connection Error: " . $e->getMessage());

                die(json_encode([
                    'error' => 'Error de conexión a la base de datos.'
                ]));
            }
        }

        return self::$connection;
    }
}