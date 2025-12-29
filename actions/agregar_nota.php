<?php
// actions/agregar_nota.php
session_start();
require '../config/db.php';

// 1. SEGURIDAD: Verificamos que el usuario esté logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Recibimos los datos (OJO: Aquí usamos los nombres del formulario de ver_ticket.php)
    $ticket_id = $_POST['ticket_id'];
    $texto_nota = trim($_POST['nota']); // El name en el formulario es 'nota'
    $usuario_id = $_SESSION['usuario_id'];

    if (!empty($texto_nota)) {
        try {
            // 2. INSERTAR EN LA TABLA CORRECTA 'notas_tickets'
            // Usamos las columnas que creamos hace un momento: ticket_id, usuario_id, nota, fecha
            
            $sql = "INSERT INTO notas_tickets (ticket_id, usuario_id, nota, fecha) 
                    VALUES (:tid, :uid, :nota, NOW())";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':tid' => $ticket_id,
                ':uid' => $usuario_id,
                ':nota' => $texto_nota
            ]);
            
            // Redirigir de vuelta al ticket
            header("Location: ../views/ver_ticket.php?id=$ticket_id&mensaje=nota_agregada");
            exit;

        } catch (PDOException $e) {
            // Si falla, redirigimos con error
            header("Location: ../views/ver_ticket.php?id=$ticket_id&error=db_error");
            exit;
        }
    } else {
        // Si envió el mensaje vacío
        header("Location: ../views/ver_ticket.php?id=$ticket_id&error=campo_vacio");
        exit;
    }
} else {
    // Si intenta entrar directo al archivo sin POST
    header("Location: ../views/dashboard.php");
    exit;
}
?>