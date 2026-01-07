<?php
// actions/checklist_acciones.php
require '../config/db.php';
session_start();

// Validar sesión
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['status' => 'error', 'msg' => 'No autorizado']);
    exit;
}

// Recibir datos JSON
$data = json_decode(file_get_contents('php://input'), true);
$accion = $data['accion'] ?? '';

if ($accion === 'agregar') {
    $ticket_id = $data['ticket_id'];
    $tarea = trim($data['tarea']);

    if (!empty($tarea)) {
        $stmt = $pdo->prepare("INSERT INTO ticket_checklist (ticket_id, titulo_tarea) VALUES (:tid, :tarea)");
        $stmt->execute([':tid' => $ticket_id, ':tarea' => $tarea]);
        echo json_encode(['status' => 'ok', 'id' => $pdo->lastInsertId()]);
    }
}

if ($accion === 'toggle') {
    $item_id = $data['item_id'];
    $estado = $data['estado']; // 1 o 0

    $stmt = $pdo->prepare("UPDATE ticket_checklist SET completado = :estado WHERE id = :id");
    $stmt->execute([':estado' => $estado, ':id' => $item_id]);
    echo json_encode(['status' => 'ok']);
}
if ($accion === 'eliminar') {
    $item_id = $data['item_id'];
    $stmt = $pdo->prepare("DELETE FROM ticket_checklist WHERE id = :id");
    $stmt->execute([':id' => $item_id]);
    echo json_encode(['status' => 'ok']);
}
?>