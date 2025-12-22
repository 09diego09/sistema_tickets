<?php
// sistema_tickets/fix_password.php
require 'config/db.php';

$email = 'admin@tickets.com';
$nueva_pass = '123456';

// Generamos el hash usando TU versión de PHP
$hash_seguro = password_hash($nueva_pass, PASSWORD_DEFAULT);

try {
    // Actualizamos el usuario directamente
    $sql = "UPDATE usuarios SET password = :pass WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':pass' => $hash_seguro, ':email' => $email]);

    echo "<h1>¡Contraseña Actualizada!</h1>";
    echo "<p>El usuario <b>$email</b> ahora tiene la contraseña: <b>$nueva_pass</b></p>";
    echo "<p>Nuevo Hash generado: " . $hash_seguro . "</p>";
    echo "<br><a href='index.php'>Ir al Login e intentar de nuevo</a>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>