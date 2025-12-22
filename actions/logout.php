<?php
// sistema_tickets/actions/logout.php
session_start();

// 1. Borrar todas las variables de sesión
$_SESSION = [];

// 2. Destruir la sesión completamente
session_destroy();

// 3. Redirigir al Login (index.php)
header("Location: ../index.php");
exit;
?>