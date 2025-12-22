<?php
require 'config/db.php';

$pass_plana = '123456';
// Generamos el hash AQUÍ MISMO para asegurar compatibilidad
$hash_seguro = password_hash($pass_plana, PASSWORD_DEFAULT);

try {
    // 1. Limpiar
    $pdo->query("DELETE FROM usuarios WHERE email = 'admin@tickets.com'");

    // 2. Insertar
    $sql = "INSERT INTO usuarios (nombre, email, password, rol, activo) VALUES (:nom, :email, :pass, 'admin', 1)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nom' => 'Admin PHP',
        ':email' => 'admin@tickets.com',
        ':pass' => $hash_seguro
    ]);

    echo "<h1>Usuario Creado</h1>";
    echo "Pass: $pass_plana<br>";
    echo "Hash generado: $hash_seguro<br>";
    echo "<br>Intenta loguearte ahora.";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>