<?php
// sistema_tickets/actions/asignar_ticket.php
session_start();
require '../config/db.php';

// Solo Admin y Técnicos pueden asignar
if (!in_array($_SESSION['usuario_rol'], ['admin', 'tecnico'])) {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ticket_id = $_POST['ticket_id'];
    $agente_id = $_POST['agente_id']; // ID del técnico seleccionado

    try {
        // Actualizamos el agente asignado
        $sql = "UPDATE tickets SET agente_id = :agente, fecha_actualizacion = NOW() WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':agente' => $agente_id, ':id' => $ticket_id]);

        // Opcional: Cambiar estado a "En Proceso" automáticamente si se asigna alguien
        // $pdo->query("UPDATE tickets SET estado = 'en_proceso' WHERE id = $ticket_id AND estado = 'abierto'");

        header("Location: ../views/ver_ticket.php?id=$ticket_id&msg=asignado");
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>