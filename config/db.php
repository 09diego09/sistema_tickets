<?php
/*
* Ubicación: config/db.php
* Descripción: Conexión segura a la base de datos usando PDO
*/

// Credenciales por defecto de XAMPP
$host = 'localhost';
$dbname = 'sistema_tickets';
$username = 'root';
$password = '1234'; // En XAMPP la contraseña de root suele estar vacía

try {
    // Definimos la cadena de conexión (DSN)
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    
    // Opciones para manejar errores y seguridad
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Lanza excepciones en caso de error
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Devuelve arrays asociativos
        PDO::ATTR_EMULATE_PREPARES => false, // Usa sentencias preparadas reales
    ];

    // Creamos la instancia PDO
    $pdo = new PDO($dsn, $username, $password, $options);

    // Si llegamos aquí, la conexión fue exitosa (puedes descomentar la línea de abajo para probar)
    // echo "¡Conexión exitosa a la base de datos!";

} catch (PDOException $e) {
    // Si algo falla, capturamos el error y detenemos el script
    // En producción, nunca muestres el error real al usuario ($e->getMessage())
    die("Error de conexión: " . $e->getMessage());
}
?>