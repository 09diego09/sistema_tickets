<?php
// actions/checklist_acciones.php

// Configuración JSON
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

require '../config/db.php';
session_start();

try {
    // Leer input JSON (porque usamos fetch en el frontend)
    $input = json_decode(file_get_contents('php://input'), true);
    $accion = $input['accion'] ?? '';

    // Validar sesión
    if (!isset($_SESSION['usuario_id'])) throw new Exception("No autorizado");

    // --- ACCIÓN: AGREGAR TAREA ---
    if ($accion === 'agregar') {
        $ticket_id = $input['ticket_id'];
        $tarea     = trim($input['tarea']);
        
        if (empty($tarea)) throw new Exception("Tarea vacía");

        $stmt = $pdo->prepare("INSERT INTO ticket_checklist (ticket_id, titulo_tarea, completado) VALUES (?, ?, 0)");
        $stmt->execute([$ticket_id, $tarea]);
        
        echo json_encode(['status' => 'ok']);
        exit;
    }

    // --- ACCIÓN: TOGGLE (MARCAR/DESMARCAR) ---
    if ($accion === 'toggle') {
        $id = $input['item_id'];
        $estado = $input['estado']; // 1 o 0

        $stmt = $pdo->prepare("UPDATE ticket_checklist SET completado = ? WHERE id = ?");
        $stmt->execute([$estado, $id]);
        
        echo json_encode(['status' => 'ok']);
        exit;
    }

    // --- ACCIÓN: ELIMINAR ---
    if ($accion === 'eliminar') {
        // Solo staff borra
        if ($_SESSION['usuario_rol'] == 'usuario') throw new Exception("Permiso denegado");

        $id = $input['item_id'];
        $stmt = $pdo->prepare("DELETE FROM ticket_checklist WHERE id = ?");
        $stmt->execute([$id]);
        
        echo json_encode(['status' => 'ok']);
        exit;
    }

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
}
?>