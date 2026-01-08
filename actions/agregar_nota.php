<?php
// actions/agregar_nota.php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);
require '../config/db.php';
require '../includes/mailer.php';

// Seguridad: Solo Staff
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] == 'usuario') {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $ticket_id = $_POST['ticket_id'];
    $nota      = trim($_POST['nota']);
    $mi_id     = $_SESSION['usuario_id'];
    $mi_nombre = $_SESSION['usuario_nombre'];

    if (empty($nota)) {
        header("Location: ../views/ver_ticket.php?id=$ticket_id");
        exit;
    }

    try {
        // A) Insertar Nota
        $sql = "INSERT INTO notas_tickets (ticket_id, usuario_id, nota, fecha) VALUES (:tid, :uid, :nota, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':tid' => $ticket_id, ':uid' => $mi_id, ':nota' => $nota]);

        // B) Notificar (Colaboración)
        // Lógica: Avisamos al Admin y al Técnico asignado (excluyéndome a mí mismo)
        
        // 1. Obtener emails del Admin y del Técnico del ticket
        $sqlDest = "SELECT u.email 
                    FROM usuarios u 
                    LEFT JOIN tickets t ON (u.id = t.agente_id OR u.rol = 'admin')
                    WHERE t.id = :tid 
                    AND u.id != :mi_id 
                    AND u.activo = 1 
                    GROUP BY u.email"; // GROUP BY evita duplicados si el admin es también el técnico
        
        $stmtDest = $pdo->prepare($sqlDest);
        $stmtDest->execute([':tid' => $ticket_id, ':mi_id' => $mi_id]);
        $destinatarios = $stmtDest->fetchAll(PDO::FETCH_COLUMN); // Array plano de emails

        // 2. Obtener título del ticket para el asunto
        $stmtT = $pdo->prepare("SELECT titulo FROM tickets WHERE id = ?");
        $stmtT->execute([$ticket_id]);
        $tituloTicket = $stmtT->fetchColumn();

        // 3. Usar la función especial para notas (definida en mailer.php)
        if (!empty($destinatarios)) {
            notificarNuevaNota($ticket_id, $tituloTicket, $mi_nombre, $nota, $destinatarios);
        }

        header("Location: ../views/ver_ticket.php?id=$ticket_id#seccionNotas");
        exit;

    } catch (Exception $e) {
        error_log("Error agregando nota: " . $e->getMessage());
        header("Location: ../views/ver_ticket.php?id=$ticket_id&error=db");
    }
}
?>