<?php
// actions/asignar_ticket.php
session_start();

// 1. Configuración y Dependencias
ini_set('display_errors', 1);
error_reporting(E_ALL);
require '../config/db.php';
require '../includes/mailer.php'; // Para avisarle al técnico

// 2. Seguridad: Solo Admin o Técnico pueden asignar
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] == 'usuario') {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $ticket_id  = $_POST['ticket_id'];
    // Si viene vacío (selecciona "-- Sin Asignar --"), guardamos NULL
    $tecnico_id = !empty($_POST['tecnico_id']) ? $_POST['tecnico_id'] : null;
    $admin_nombre = $_SESSION['usuario_nombre'];

    try {
        // A) ACTUALIZAR BASE DE DATOS
        $sql = "UPDATE tickets SET agente_id = :agente WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':agente' => $tecnico_id, ':id' => $ticket_id]);

        // B) NOTIFICAR AL TÉCNICO (Solo si se asignó a alguien real)
        if ($tecnico_id) {
            // 1. Obtenemos datos del técnico y del ticket
            $sqlData = "SELECT u.email, u.nombre, t.titulo, t.prioridad 
                        FROM usuarios u, tickets t 
                        WHERE u.id = :uid AND t.id = :tid";
            $stmtData = $pdo->prepare($sqlData);
            $stmtData->execute([':uid' => $tecnico_id, ':tid' => $ticket_id]);
            $datos = $stmtData->fetch(PDO::FETCH_ASSOC);

            // 2. Preparamos el correo
            if ($datos && !empty($datos['email'])) {
                $asunto = "🎟️ Ticket Asignado: #" . $ticket_id;
                
                // Estilos visuales (Mismos que el resto del sistema)
                $bg_app        = "#f0f8ff"; 
                $color_primary = "#0072ff"; 
                $color_text    = "#334e68";
                $link_ticket   = "http://localhost/sistema_tickets/views/ver_ticket.php?id=$ticket_id";

                $html = <<<HTML
                <div style="font-family: 'Segoe UI', Arial, sans-serif; background-color: $bg_app; padding: 40px 0; color: $color_text;">
                    <div style="background-color: #ffffff; max-width: 600px; margin: 0 auto; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); overflow: hidden;">
                        <div style="background: linear-gradient(180deg, #00c6ff 0%, #0072ff 100%); padding: 30px; text-align: center;">
                            <h1 style="color: white; margin: 0; font-size: 24px;">Nueva Asignación</h1>
                        </div>
                        <div style="padding: 40px 30px;">
                            <h2 style="color: $color_text; margin-top: 0;">Hola, {$datos['nombre']} 👋</h2>
                            <p style="font-size: 16px;"><strong>$admin_nombre</strong> te ha asignado la responsabilidad del siguiente ticket:</p>
                            
                            <div style="background-color: #f0f4f8; border-left: 5px solid $color_primary; border-radius: 5px; padding: 20px; margin: 25px 0;">
                                <p style="margin: 0; font-size: 18px; font-weight: bold; color: $color_text;">#$ticket_id - {$datos['titulo']}</p>
                                <p style="margin: 5px 0 0; color: #888; text-transform: uppercase; font-size: 12px;">Prioridad: {$datos['prioridad']}</p>
                            </div>

                            <center>
                                <a href="$link_ticket" style="display: inline-block; background-color: $color_primary; color: #ffffff; padding: 12px 35px; border-radius: 50px; text-decoration: none; font-weight: bold;">Ver Ticket</a>
                            </center>
                        </div>
                    </div>
                </div>
HTML;
                // Enviar usando el motor central
                enviarCorreo($datos['email'], $datos['nombre'], $asunto, $html);
            }
        }

        // C) VOLVER
        header("Location: ../views/ver_ticket.php?id=$ticket_id&msg=asignado");
        exit;

    } catch (PDOException $e) {
        error_log("Error al asignar ticket: " . $e->getMessage());
        header("Location: ../views/ver_ticket.php?id=$ticket_id&error=db");
    }
}
?>