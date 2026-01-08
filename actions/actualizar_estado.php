<?php
// actions/actualizar_estado.php
session_start();

// 1. Configuración
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
    $ticket_id    = $_POST['ticket_id'];
    $nuevo_estado = $_POST['estado'];
    $mi_nombre    = $_SESSION['usuario_nombre'];

    try {
        // A) Actualizar BD
        $stmt = $pdo->prepare("UPDATE tickets SET estado = :est WHERE id = :id");
        $stmt->execute([':est' => $nuevo_estado, ':id' => $ticket_id]);

        // B) Notificar al DUEÑO DEL TICKET (El cliente/usuario)
        if ($nuevo_estado == 'resuelto' || $nuevo_estado == 'cerrado') {
            
            // 1. Buscamos datos del dueño
            $sqlOwner = "SELECT u.email, u.nombre, t.titulo 
                         FROM tickets t 
                         JOIN usuarios u ON t.usuario_id = u.id 
                         WHERE t.id = :id";
            $stmtOwner = $pdo->prepare($sqlOwner);
            $stmtOwner->execute([':id' => $ticket_id]);
            $owner = $stmtOwner->fetch(PDO::FETCH_ASSOC);

            // 2. Enviamos el correo
            if ($owner && !empty($owner['email'])) {
                
                // --- CORRECCIÓN AQUÍ ---
                // Calculamos el texto bonito ANTES de meterlo al HTML
                $estado_legible = ucfirst(str_replace('_', ' ', $nuevo_estado));
                // -----------------------

                $asunto = "✅ Ticket #$ticket_id Actualizado: " . $estado_legible;
                
                // Estilos visuales
                $bg_app        = "#f0f8ff"; 
                $color_primary = "#198754"; // Verde éxito
                $color_text    = "#334e68";
                // Ajusta esta URL a tu dominio real si ya está en internet
                $link_ticket   = "http://localhost/sistema_tickets/views/ver_ticket.php?id=$ticket_id";

                $html = <<<HTML
                <div style="font-family: 'Segoe UI', Arial, sans-serif; background-color: $bg_app; padding: 40px 0; color: $color_text;">
                    <div style="background-color: #ffffff; max-width: 600px; margin: 0 auto; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); overflow: hidden; border-top: 6px solid $color_primary;">
                        <div style="padding: 30px; text-align: center; border-bottom: 1px solid #f0f4f8;">
                            <h2 style="margin: 0; color: $color_text;">Estado Actualizado</h2>
                        </div>
                        <div style="padding: 30px;">
                            <p style="font-size: 16px;">Hola <strong>{$owner['nombre']}</strong>,</p>
                            <p>Te informamos que tu ticket ha cambiado de estado por <strong>$mi_nombre</strong>.</p>
                            
                            <div style="background-color: #d1e7dd; color: #0f5132; padding: 20px; border-radius: 10px; margin: 25px 0; text-align: center;">
                                <span style="display: block; font-size: 12px; text-transform: uppercase; letter-spacing: 1px;">Nuevo Estado</span>
                                <strong style="font-size: 24px;">$estado_legible</strong>
                            </div>

                            <p style="text-align: center; color: #888;">Ticket: <em>{$owner['titulo']}</em></p>

                            <center style="margin-top: 30px;">
                                <a href="$link_ticket" style="display: inline-block; background-color: $color_text; color: #ffffff; padding: 12px 35px; border-radius: 50px; text-decoration: none; font-weight: bold;">Ver Ticket</a>
                            </center>
                        </div>
                    </div>
                </div>
HTML;
                enviarCorreo($owner['email'], $owner['nombre'], $asunto, $html);
            }
        }

        // C) Volver
        header("Location: ../views/ver_ticket.php?id=$ticket_id&msg=estado_ok");
        exit;

    } catch (PDOException $e) {
        error_log("Error cambio estado: " . $e->getMessage());
        header("Location: ../views/ver_ticket.php?id=$ticket_id&error=db");
    }
}
?>