<?php
// sistema_tickets/diagnostico.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🕵️‍♂️ Diagnóstico de Conexión</h1>";

// Opciones a probar
$opciones = [
    ['host' => '127.0.0.1', 'port' => '3306'],
    ['host' => 'localhost', 'port' => '3306'],
    ['host' => '127.0.0.1', 'port' => '3307'], // Puerto alternativo común
    ['host' => 'localhost', 'port' => '3307'],
    ['host' => '::1',       'port' => '3306']  // IPv6
];

$usuario = 'root'; 
$password = ''; // PRUEBA CON CONTRASEÑA VACÍA PRIMERO
// Si tienes contraseña, cámbiala aquí abajo:
// $password = '123456'; 

echo "<p>Probando usuario: <strong>$usuario</strong> y contraseña: <strong>" . ($password ? '****' : '(vacía)') . "</strong></p><hr>";

foreach ($opciones as $opcion) {
    $h = $opcion['host'];
    $p = $opcion['port'];
    
    echo "Probando conexión a <strong>$h</strong> en puerto <strong>$p</strong>... ";
    
    try {
        $dsn = "mysql:host=$h;port=$p;dbname=sistema_tickets;charset=utf8mb4";
        $pdo = new PDO($dsn, $usuario, $password);
        echo "<span style='color:green; font-weight:bold;'>¡ÉXITO! ✅</span><br>";
        echo "<br><strong>👉 SOLUCIÓN:</strong> Actualiza tu archivo config/db.php con: <br>";
        echo "<pre style='background:#eee; padding:10px;'>\$host = '$h';\n\$port = '$p'; // (Agrega esto al DSN)</pre>";
        exit; // Terminamos si encontramos uno que sirva
    } catch (PDOException $e) {
        echo "<span style='color:red;'>Falló</span> <small>(" . $e->getMessage() . ")</small><br>";
    }
}

echo "<hr><h3 style='color:red'>Ninguna opción funcionó. 😓</h3>";
echo "<p>Por favor revisa en tu Panel de XAMPP qué número aparece bajo la columna 'Port(s)' junto a MySQL.</p>";
?>