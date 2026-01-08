<?php
/*
* Ubicación: config/db.php
* VERSIÓN FINAL - INFINITYFREE
*/

// 1. CREDENCIALES
$host     = 'sql302.infinityfree.com';
$dbname   = 'if0_40858595_sistema_tickets';
$username = 'if0_40858595';
$password = 'sHQiHrFzd9RS8';
$port     = '3306';

try {
    // 2. Definimos la conexión (DSN)
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4;port=$port";
    
    // 3. Opciones de seguridad
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    // 4. Crear el objeto PDO
    $pdo = new PDO($dsn, $username, $password, $options);
    
    // 5. Ajuste para evitar errores de SQL estrictos
    $pdo->exec("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");

} catch (PDOException $e) {
    // Si falla, mostramos el error exacto para que sepas qué pasó
    die("❌ ERROR CRÍTICO DE CONEXIÓN: " . $e->getMessage());
}
?>