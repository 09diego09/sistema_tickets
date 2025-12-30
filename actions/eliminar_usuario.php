<?php
session_start();
require '../config/db.php';

// Seguridad: Solo admin
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'admin') {
    header("Location: ../views/dashboard.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // EN LUGAR DE DELETE, HACEMOS UPDATE
    // Cambiamos activo a 0
    $sql = "UPDATE usuarios SET activo = 0 WHERE id = ?";
    
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$id])) {
        // Redirigimos con mensaje de éxito
        header("Location: ../views/admin_usuarios.php?mensaje=usuario_desactivado");
    } else {
        header("Location: ../views/admin_usuarios.php?error=error_db");
    }
} else {
    header("Location: ../views/admin_usuarios.php");
}
?>