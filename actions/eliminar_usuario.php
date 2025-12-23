<?php
// sistema_tickets/actions/eliminar_usuario.php
session_start();
require '../config/db.php';

// 1. SEGURIDAD
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Evitar auto-suicidio (No puedes borrarte a ti mismo mientras estás logueado)
    if ($id == $_SESSION['usuario_id']) {
        header("Location: ../views/admin_usuarios.php?error=no_te_borres");
        exit;
    }

    try {
        // Borramos al usuario
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = :id");
        $stmt->execute([':id' => $id]);
        
        header("Location: ../views/admin_usuarios.php?msg=eliminado");
    } catch (PDOException $e) {
        // Si el usuario tiene tickets, MySQL no dejará borrarlo (Integridad referencial)
        // En ese caso, es mejor desactivarlo (activo = 0) en lugar de borrarlo
        header("Location: ../views/admin_usuarios.php?error=usuario_tiene_datos");
    }
}
?>