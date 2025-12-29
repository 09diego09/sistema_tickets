<?php
// actions/agregar_nota.php
session_start();

// --- MODO DEBUG: Activado para encontrar el error ---
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

require '../config/db.php';
require '../includes/mailer.php';

// Validar que sea staff
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['usuario_rol'], ['admin', 'tecnico'])) {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ticket_id      = $_POST['ticket_id'];
    $nota           = trim($_POST['nota']);
    $usuario_id     = $_SESSION['usuario_id'];
    $usuario_nombre = $_SESSION['usuario_nombre'];

    if (!empty($nota)) {
        try {
            // 1. GUARDAR LA NOTA EN BD
            $sql = "INSERT INTO notas_tickets (ticket_id, usuario_id, nota) VALUES (:tid, :uid, :nota)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':tid' => $ticket_id, ':uid' => $usuario_id, ':nota' => $nota]);

            // 2. OBTENER DATOS PARA NOTIFICAR
            // Buscamos info del ticket y del agente asignado
            $sql_info = "SELECT t.titulo, t.agente_id, 
                                u.email as email_agente, u.nombre as nombre_agente 
                         FROM tickets t
                         LEFT JOIN usuarios u ON t.agente_id = u.id
                         WHERE t.id = :id";
            $stmt_info = $pdo->prepare($sql_info);
            $stmt_info->execute([':id' => $ticket_id]);
            $ticket = $stmt_info->fetch();

            // 3. DEFINIR DESTINATARIOS (Lógica Robusta)
            $destinatarios = [];

            // A) El Supervisor SIEMPRE recibe copia (para monitoreo), 
            //    a menos que sea él mismo quien escribe la nota.
            $email_supervisor = 'diegomolina@dac-controls.com'; // <--- CORREO ADMIN
            
            // Si el usuario actual NO es el supervisor, agregamos al supervisor
            // (Puedes comparar por ID o por email si lo tienes en sesión, aquí asumimos por email hardcoded para asegurar)
            // Para pruebas: Vamos a enviarle SIEMPRE al supervisor, aunque sea él mismo, para confirmar que funciona.
            $destinatarios[$email_supervisor] = 'Supervisor DAC';

            // B) El Agente Asignado recibe correo, 
            //    si existe Y si no es él mismo quien escribe.
            if ($ticket && !empty($ticket['email_agente'])) {
                if ($ticket['agente_id'] != $usuario_id) {
                    $destinatarios[$ticket['email_agente']] = $ticket['nombre_agente'];
                }
            }

            // 4. ENVIAR LOS CORREOS
            foreach ($destinatarios as $email => $nombre) {
                // Preparamos el HTML
                $asunto = "📝 Nueva Nota Interna - Ticket #$ticket_id";
                
                $html = <<<HTML
                <div style="font-family: Arial, sans-serif; background-color: #f0f8ff; padding: 40px 0; color: #334e68;">
                    <div style="background-color: #ffffff; max-width: 600px; margin: 0 auto; border-radius: 20px; border-left: 8px solid #ffc107; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                        <div style="padding: 30px;">
                            <span style="background-color: #fff8e1; color: #d97706; padding: 5px 12px; border-radius: 50px; font-size: 11px; font-weight: bold; text-transform: uppercase;">🔒 Nota Privada</span>
                            
                            <h2 style="color: #334e68; margin-top: 15px; font-size: 20px;">Hola, $nombre</h2>
                            <p style="font-size: 15px; line-height: 1.5;"><strong>$usuario_nombre</strong> agregó un comentario al ticket <strong>#$ticket_id</strong>:</p>
                            
                            <div style="background-color: #fffff0; border: 1px dashed #d97706; padding: 15px; border-radius: 10px; color: #4b5563; margin: 15px 0;">
                                <em>"$nota"</em>
                            </div>
                            
                            <p style="font-size: 13px; color: #829ab1; margin-bottom: 0;">Ticket: {$ticket['titulo']}</p>
                            
                            <center style="margin-top: 20px;">
                                <a href="http://localhost/sistema_tickets/views/ver_ticket.php?id=$ticket_id#seccionNotas" 
                                   style="display: inline-block; background-color: #334e68; color: white; padding: 10px 20px; border-radius: 50px; text-decoration: none; font-size: 14px;">
                                   Ver Conversación
                                </a>
                            </center>
                        </div>
                    </div>
                </div>
HTML;
                // Intentamos enviar y registramos si falla
                try {
                    enviarCorreo($email, $nombre, $asunto, $html);
                    error_log("Correo nota enviado a: $email"); // Log de éxito
                } catch (Exception $eMail) {
                    error_log("Error enviando a $email: " . $eMail->getMessage()); // Log de error
                }
            }

            // Éxito: Volvemos al ticket
            header("Location: ../views/ver_ticket.php?id=$ticket_id#seccionNotas");
            exit;

        } catch (PDOException $e) {
            error_log("Error BD Nota: " . $e->getMessage());
            header("Location: ../views/ver_ticket.php?id=$ticket_id&error=db_nota");
            exit;
        }
    } else {
        header("Location: ../views/ver_ticket.php?id=$ticket_id");
        exit;
    }
}
?>