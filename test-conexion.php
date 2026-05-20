<?php
/**
 * Test de Conexión — Diagnosticador
 */

echo "<h2>🔍 Test de Conexión a Base de Datos</h2>";

// Prueba 1: Verificar que PDO esté disponible
echo "<p><strong>1. Extensión PDO:</strong> ";
if (extension_loaded('pdo')) {
    echo " Instalada</p>";
} else {
    echo " NO instalada</p>";
    die("Instala la extensión PDO de PHP");
}

// Prueba 2: Verificar que el driver MySQL de PDO esté disponible
echo "<p><strong>2. PDO MySQL Driver:</strong> ";
if (extension_loaded('pdo_mysql')) {
    echo " Instalado</p>";
} else {
    echo "NO instalado</p>";
    die("Instala el driver pdo_mysql de PHP");
}

// Prueba 3: Intentar conectar
echo "<p><strong>3. Conexión a MySQL:</strong><br>";

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'biblioteca_fusalmo';
$charset = 'utf8mb4';

try {
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    echo " Conexión exitosa!";
    
    // Prueba 4: Ver tablas
    echo "<p><strong>4. Tablas en la BD:</strong><br>";
    $result = $pdo->query("SHOW TABLES");
    while ($row = $result->fetch(PDO::FETCH_NUM)) {
        echo "  • " . $row[0] . "<br>";
    }
    
} catch (PDOException $e) {
    echo "<span style='color:red;'> Error: " . $e->getMessage() . "</span>";
    echo "<br>Código: " . $e->getCode();
}

echo "<hr>";
echo "<p><strong>Detalles PHP:</strong>";
echo "<br>Versión: " . phpversion();
echo "<br>MYSQL_VERSION: " . (extension_loaded('mysqlnd') ? 'mysqlnd' : 'otro');
echo "<br>PDO drivers: " . implode(', ', PDO::getAvailableDrivers());
echo "</p>";
?>
