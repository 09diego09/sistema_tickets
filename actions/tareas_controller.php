<?php
// actions/tareas_controller.php

// 1. Configuración
ini_set('display_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('America/Santiago');
header('Content-Type: application/json');

// 2. Incluir dependencias (Igual que en crear_ticket.php)
require '../config/db.php';
// Asegúrate de que mailer.php exista y tenga la función enviarCorreo()
if (file_exists('../includes/mailer.php')) {
    require '../includes/mailer.php';
} else {
    // Si no existe, definimos una función dummy para que no explote
    if (!function_exists('enviarCorreo')) {
        function enviarCorreo($a, $b, $c, $d) { return false; }
    }
}

try {
    session_start();

    if (!isset($_SESSION['usuario_id'])) {
        throw new Exception("No autenticado");
    }

    // Leer JSON
    $inputJSON = file_get_contents('php://input');
    $data = json_decode($inputJSON, true);
    if (json_last_error() !== JSON_ERROR_NONE) throw new Exception("Error JSON");

    $accion = $data['accion'] ?? '';
    $mi_id = $_SESSION['usuario_id'];
    $mi_nombre = $_SESSION['usuario_nombre'] ?? 'Usuario';
    $mi_rol = $_SESSION['usuario_rol'] ?? 'usuario';

    // Estilos visuales compartidos (mismo look que tickets)
    $bg_app = "#f0f8ff";
    $color_primary = "#0072ff";
    $color_text = "#334e68";
    
    // Link base para los botones del correo (Ajusta si tu URL es distinta)
    $base_url = "http://" . $_SERVER['HTTP_HOST'] . "/sistema_tickets/views/mis_tareas.php";

    // ==========================================
    // 1. CREAR TAREA
    // ==========================================
    if ($accion === 'crear') {
        $titulo = trim($data['titulo'] ?? '');
        $asignado_a = $data['asignado_a'] ?? $mi_id;

        // Si soy usuario normal, me asigno a mí mismo
        if ($mi_rol == 'usuario') $asignado_a = $mi_id; 
        if (empty($titulo)) throw new Exception("Sin título");

        // A) Insertar en BD
        $stmt = $pdo->prepare("INSERT INTO tareas (titulo, usuario_asignado_id, creador_id, completada, fecha_creacion) VALUES (?, ?, ?, 0, NOW())");
        $stmt->execute([$titulo, $asignado_a, $mi_id]);

        // B) Notificación (Si asigné a otro)
        if ($asignado_a != $mi_id) {
            // Obtener datos del destinatario
            $stmtUser = $pdo->prepare("SELECT email, nombre FROM usuarios WHERE id = ?");
            $stmtUser->execute([$asignado_a]);
            $dest = $stmtUser->fetch(PDO::FETCH_ASSOC);

            if ($dest && !empty($dest['email'])) {
                $asunto = "🆕 Nueva Tarea: $titulo";
                
                // PLANTILLA HTML (Estilo Ticket)
                $html = <<<HTML
                <div style="font-family: 'Segoe UI', Arial, sans-serif; background-color: $bg_app; padding: 40px 0; color: $color_text;">
                    <div style="background-color: #ffffff; max-width: 600px; margin: 0 auto; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); overflow: hidden;">
                        <div style="background: linear-gradient(180deg, #00c6ff 0%, #0072ff 100%); padding: 30px; text-align: center;">
                            <h1 style="color: white; margin: 0; font-size: 24px;">Nueva Tarea Asignada</h1>
                        </div>
                        <div style="padding: 40px 30px;">
                            <h2 style="color: $color_text; margin-top: 0;">Hola, {$dest['nombre']} 👋</h2>
                            <p style="font-size: 16px;"><strong>$mi_nombre</strong> te ha asignado la siguiente labor en el sistema:</p>
                            
                            <div style="background-color: #f0f4f8; border-left: 5px solid $color_primary; border-radius: 5px; padding: 20px; margin: 25px 0;">
                                <p style="margin: 0; font-size: 18px; font-weight: bold; color: $color_text;">$titulo</p>
                                <p style="margin: 5px 0 0; color: #888; font-size: 14px;">📅 Fecha: 31/12/2026 00:00 (Ejemplo)</p>
                            </div>

                            <center>
                                <a href="$base_url" style="display: inline-block; background-color: $color_primary; color: #ffffff; padding: 12px 35px; border-radius: 50px; text-decoration: none; font-weight: bold;">Ver Mis Tareas</a>
                            </center>
                        </div>
                    </div>
                </div>
HTML;
                // Enviar usando tu mailer.php
                try {
                    enviarCorreo($dest['email'], $dest['nombre'], $asunto, $html);
                } catch (Exception $e) {
                    error_log("Error correo tarea: " . $e->getMessage());
                }
            }
        }
        echo json_encode(['status' => 'ok']);
        exit;
    }

    // ==========================================
    // 2. TOGGLE (COMPLETAR)
    // ==========================================
    if ($accion === 'toggle') {
        $id = $data['id'];
        $estado = $data['estado']; // 1 o 0

        $stmt = $pdo->prepare("UPDATE tareas SET completada = ? WHERE id = ?");
        $stmt->execute([$estado, $id]);

        // Si se completó (1), avisar al creador
        if ($estado == 1) {
            $stmtTask = $pdo->prepare("SELECT t.titulo, t.creador_id, u.email, u.nombre 
                                       FROM tareas t JOIN usuarios u ON t.creador_id = u.id 
                                       WHERE t.id = ?");
            $stmtTask->execute([$id]);
            $task = $stmtTask->fetch(PDO::FETCH_ASSOC);

            // Si el creador no soy yo (para no auto-enviarme)
            if ($task && $task['creador_id'] != $mi_id && !empty($task['email'])) {
                $asunto = "✅ Tarea Finalizada: " . $task['titulo'];
                
                // PLANTILLA HTML (Estilo Ticket Resuelto)
                $html = <<<HTML
                <div style="font-family: 'Segoe UI', Arial, sans-serif; background-color: $bg_app; padding: 40px 0; color: $color_text;">
                    <div style="background-color: #ffffff; max-width: 600px; margin: 0 auto; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); overflow: hidden; border-top: 6px solid #198754;">
                        <div style="padding: 30px; border-bottom: 1px solid #f0f4f8;">
                            <h2 style="margin:0; color: $color_text; font-size:22px;">Tarea Completada</h2>
                        </div>
                        <div style="padding: 30px;">
                            <p style="font-size: 16px;">Hola <strong>{$task['nombre']}</strong>,</p>
                            <p>El usuario <strong>$mi_nombre</strong> ha marcado como LISTA la siguiente tarea:</p>
                            
                            <div style="background-color: #d1e7dd; color: #0f5132; padding: 20px; border-radius: 10px; margin: 20px 0; font-weight: bold; font-size: 18px;">
                                ✅ {$task['titulo']}
                            </div>
                            
                            <center style="margin-top:30px;">
                                <a href="$base_url" style="display:inline-block; background-color: #334e68; color:white; padding:10px 30px; border-radius:50px; text-decoration:none;">Gestionar</a>
                            </center>
                        </div>
                    </div>
                </div>
HTML;
                try {
                    enviarCorreo($task['email'], $task['nombre'], $asunto, $html);
                } catch (Exception $e) {
                    error_log("Error correo tarea fin: " . $e->getMessage());
                }
            }
        }
        echo json_encode(['status' => 'ok']);
        exit;
    }

    // ==========================================
    // 3. ELIMINAR
    // ==========================================
    if ($accion === 'eliminar') {
        $stmt = $pdo->prepare("DELETE FROM tareas WHERE id = ?");
        $stmt->execute([$data['id']]);
        echo json_encode(['status' => 'ok']);
        exit;
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
}
?>