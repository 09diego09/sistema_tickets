<?php
// sistema_tickets/actions/actualizar_estado.php
session_start();
require '../config/db.php';

// 1. SEGURIDAD: Verificar roles permitidos
$rol_usuario = $_SESSION['usuario_rol'];
$permisos = ['admin', 'tecnico']; // Solo estos roles pueden cambiar estados

if (!in_array($rol_usuario, $permisos)) {
    // Si es usuario normal, lo expulsamos
    header("Location: ../views/dashboard.php?error=acceso_denegado");
    exit;
}

// 2. Verificar datos del formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id']) && isset($_POST['nuevo_estado'])) {
    
    $id_ticket = $_POST['id'];
    $nuevo_estado = $_POST['nuevo_estado'];
    
    // Lista blanca de estados válidos para evitar inyecciones raras
    $estados_validos = ['abierto', 'en_proceso', 'cerrado', 'espera'];

    if (in_array($nuevo_estado, $estados_validos)) {
        try {
            // Actualizamos el estado y la fecha de actualización
            $sql = "UPDATE tickets SET estado = :est, fecha_actualizacion = NOW() WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':est' => $nuevo_estado,
                ':id' => $id_ticket
            ]);

            header("Location: ../views/ver_ticket.php?id=$id_ticket&msg=estado_actualizado");
            exit;

        } catch (PDOException $e) {
            die("Error al actualizar: " . $e->getMessage());
        }
    } else {
        header("Location: ../views/ver_ticket.php?id=$id_ticket&error=estado_invalido");
        exit;
    }
} else {
    header("Location: ../views/dashboard.php");
    exit;
}
?>