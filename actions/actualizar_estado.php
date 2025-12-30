<?php
// actions/actualizar_estado.php
session_start();
require '../config/db.php';
require '../includes/mailer.php';

// Validar sesión
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['usuario_rol'], ['admin', 'tecnico'])) {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ticket_id = $_POST['ticket_id'];
    $nuevo_estado = $_POST['estado'];
    $usuario_nombre = $_SESSION['usuario_nombre'];

    try {
        // 1. ACTUALIZAR EN BD
        $sql = "UPDATE tickets SET estado = :estado WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':estado' => $nuevo_estado, ':id' => $ticket_id]);

        // 2. OBTENER DATOS DEL TICKET (Para el correo)
        // Necesitamos saber el correo del dueño del ticket y el título
        $sql_info = "SELECT t.titulo, u.email, u.nombre 
                     FROM tickets t 
                     JOIN usuarios u ON t.usuario_id = u.id 
                     WHERE t.id = :id";
        $stmt_info = $pdo->prepare($sql_info);
        $stmt_info->execute([':id' => $ticket_id]);
        $ticket_data = $stmt_info->fetch();

        // 3. PREPARAR ESTILOS VISUALES (Colores según estado)
        $color_estado = '#6c757d'; // Gris por defecto
        $texto_estado = ucfirst(str_replace('_', ' ', $nuevo_estado));
        
        if ($nuevo_estado == 'abierto') { $color_estado = '#dc3545'; }      // Rojo
        if ($nuevo_estado == 'en_proceso') { $color_estado = '#ffc107'; }   // Amarillo
        if ($nuevo_estado == 'resuelto') { $color_estado = '#198754'; }     // Verde
        if ($nuevo_estado == 'cerrado') { $color_estado = '#0d6efd'; }      // Azul

        // 4. ENVIAR CORREO AL CLIENTE (Diseño nuevo)
        if ($ticket_data && !empty($ticket_data['email'])) {
            
            $asunto = "📢 Actualización: Ticket #$ticket_id ahora está " . strtoupper($texto_estado);
            
            // HTML HEREDOC (Seguro y Limpio)
            $html = <<<HTML
            <div style="font-family: 'Segoe UI', Arial, sans-serif; background-color: #f0f8ff; padding: 40px 0; color: #334e68;">
                <div style="background-color: #ffffff; max-width: 600px; margin: 0 auto; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); overflow: hidden; border-top: 6px solid $color_estado;">
                    
                    <div style="background: linear-gradient(180deg, #f8f9fa 0%, #ffffff 100%); padding: 30px; text-align: center; border-bottom: 1px solid #f0f4f8;">
                        <p style="color: #829ab1; font-size: 12px; text-transform: uppercase; font-weight: bold; margin: 0;">Actualización de Ticket</p>
                        <h1 style="color: $color_estado; margin: 10px 0 0; font-size: 28px;">$texto_estado</h1>
                    </div>

                    <div style="padding: 40px 30px;">
                        <h2 style="color: #334e68; margin-top: 0; font-size: 20px;">Hola, {$ticket_data['nombre']}</h2>
                        <p style="font-size: 16px; line-height: 1.6;">El estado de tu solicitud ha cambiado.</p>
                        
                        <div style="background-color: #f0f4f8; border-radius: 12px; padding: 20px; margin: 25px 0;">
                            <p style="margin: 5px 0; color: #627d98; font-size: 14px;">Ticket ID: <strong>#$ticket_id</strong></p>
                            <p style="margin: 5px 0; color: #334e68; font-size: 16px; font-weight: bold;">{$ticket_data['titulo']}</p>
                            <p style="margin: 15px 0 0; font-size: 13px; color: #829ab1;">Modificado por: $usuario_nombre</p>
                        </div>

                        <center>
                            <a href="http://localhost/sistema_tickets/views/ver_ticket.php?id=$ticket_id" 
                               style="display: inline-block; background-color: $color_estado; color: #ffffff; padding: 12px 35px; border-radius: 50px; text-decoration: none; font-weight: bold;">
                                Ver Ticket
                            </a>
                        </center>
                    </div>

                    <div style="background-color: #fcfdfe; padding: 20px; text-align: center; color: #9aa5b1; font-size: 12px;">
                        DAC Controls - Sistema de Gestión
                    </div>
                </div>
            </div>
HTML;
            enviarCorreo($ticket_data['email'], $ticket_data['nombre'], $asunto, $html);
        }

        header("Location: ../views/ver_ticket.php?id=$ticket_id");
        exit;

    } catch (PDOException $e) {
        header("Location: ../views/ver_ticket.php?id=$ticket_id&error=db");
    }
}
?>