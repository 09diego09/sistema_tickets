<?php
// actions/tareas_controller.php

// 1. ACTIVAR REPORTES DE ERROR (Solo para desarrollo)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. FORZAR CABECERA JSON (Para que JS entienda la respuesta)
header('Content-Type: application/json');

try {
    // Verificar archivo de conexión
    if (!file_exists('../config/db.php')) {
        throw new Exception("No se encuentra config/db.php");
    }
    require '../config/db.php';
    session_start();

    // Validar sesión
    if (!isset($_SESSION['usuario_id'])) {
        throw new Exception("Usuario no autenticado");
    }

    // Leer el cuerpo JSON
    $inputJSON = file_get_contents('php://input');
    $data = json_decode($inputJSON, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Error decodificando JSON: " . json_last_error_msg());
    }

    $accion = $data['accion'] ?? '';
    $mi_id = $_SESSION['usuario_id'];
    $mi_rol = $_SESSION['usuario_rol'] ?? 'usuario'; // Por seguridad

    // --- ACCIÓN: CREAR ---
    if ($accion === 'crear') {
        $titulo = trim($data['titulo'] ?? '');
        $asignado_a = $data['asignado_a'] ?? $mi_id;

        if (empty($titulo)) {
            throw new Exception("El título de la tarea está vacío");
        }

        // Seguridad: Si soy usuario normal, solo puedo asignarme a mí mismo
        if ($mi_rol == 'usuario') {
            $asignado_a = $mi_id;
        }

        $sql = "INSERT INTO tareas (titulo, usuario_asignado_id, creador_id, completada, fecha_creacion) 
                VALUES (:titulo, :asignado, :creador, 0, NOW())";
        
        $stmt = $pdo->prepare($sql);
        if (!$stmt->execute([':titulo' => $titulo, ':asignado' => $asignado_a, ':creador' => $mi_id])) {
             throw new Exception("Error SQL al insertar: " . implode(" ", $stmt->errorInfo()));
        }

        echo json_encode(['status' => 'ok', 'msg' => 'Tarea creada']);
        exit;
    }

    // --- ACCIÓN: TOGGLE (MARCAR) ---
    if ($accion === 'toggle') {
        $id_tarea = $data['id'];
        $estado = $data['estado'];
        
        $stmt = $pdo->prepare("UPDATE tareas SET completada = :estado WHERE id = :id");
        $stmt->execute([':estado' => $estado, ':id' => $id_tarea]);
        echo json_encode(['status' => 'ok']);
        exit;
    }

    // --- ACCIÓN: ELIMINAR ---
    if ($accion === 'eliminar') {
        $id_tarea = $data['id'];
        $stmt = $pdo->prepare("DELETE FROM tareas WHERE id = :id");
        $stmt->execute([':id' => $id_tarea]);
        echo json_encode(['status' => 'ok']);
        exit;
    }

    // Si llega aquí sin acción
    echo json_encode(['status' => 'error', 'msg' => 'Acción no válida o vacía']);

} catch (Exception $e) {
    // CAPTURAR CUALQUIER ERROR Y ENVIARLO AL JS
    http_response_code(500); // Código de error de servidor
    echo json_encode([
        'status' => 'error',
        'msg' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
?>