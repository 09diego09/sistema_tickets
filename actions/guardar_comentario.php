<?php
// sistema_tickets/actions/guardar_comentario.php
session_start();
require '../config/db.php';

// 1. SEGURIDAD: Solo Admin y Técnicos pueden guardar notas internas
if (!isset($_SESSION['usuario_rol']) || !in_array($_SESSION['usuario_rol'], ['admin', 'tecnico'])) {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recibimos los datos del formulario
    $ticket_id = $_POST['ticket_id'];
    $texto_comentario = trim($_POST['comentario']); // El name del textarea es 'comentario'
    $usuario_id = $_SESSION['usuario_id'];

    if (!empty($texto_comentario)) {
        try {
            // 2. INSERTAR EN LA TABLA EXISTENTE 'respuestas'
            // Usamos la columna 'mensaje' que ya existe en tu tabla.
            // Forzamos el 'tipo' a 'interno'.
            
            $sql = "INSERT INTO respuestas (ticket_id, usuario_id, mensaje, tipo, fecha) 
                    VALUES (:tid, :uid, :msg, 'interno', NOW())";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':tid' => $ticket_id,
                ':uid' => $usuario_id,
                ':msg' => $texto_comentario
            ]);
            
            // Redirigir de vuelta al ticket (ancla #seccionComentarios para bajar directo)
            header("Location: ../views/ver_ticket.php?id=$ticket_id#seccionComentarios");
            exit;

        } catch (PDOException $e) {
            die("Error al guardar la nota interna: " . $e->getMessage());
        }
    } else {
        // Si envió el mensaje vacío
        header("Location: ../views/ver_ticket.php?id=$ticket_id&error=vacio");
        exit;
    }
} else {
    // Si intenta entrar directo al archivo sin POST
    header("Location: ../views/dashboard.php");
    exit;
}
?>