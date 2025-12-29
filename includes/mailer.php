<?php
// includes/mailer.php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 1. CARGA DE LIBRERÍAS (Ruta Absoluta para evitar errores)
// __DIR__ es "D:\XAMPP\htdocs\sistema_tickets\includes"
require __DIR__ . '/../PHPMailer/src/Exception.php';
require __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require __DIR__ . '/../PHPMailer/src/SMTP.php';

function getMailer() {
    $mail = new PHPMailer(true);
    try {
        // Configuración del Servidor
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; 
        $mail->SMTPAuth   = true;
        
        // --- TUS CREDENCIALES ---
        $mail->Username   = 'dmc5812@gmail.com'; 
        // ¡OJO! Aquí debe ir la Contraseña de Aplicación de 16 caracteres (sin espacios)
        $mail->Password   = 'ntrn lekd sukv rlqj'; 
        // ------------------------

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587; 
        
        $mail->setFrom('dmc5812@gmail.com', 'Sistema Tickets'); // Gmail obliga a que el remitente sea tu correo real
        $mail->CharSet = 'UTF-8';

        return $mail;

    } catch (Exception $e) {
        return null;
    }
}

// 2. FUNCIÓN DE NOTIFICACIÓN
function notificarNuevaNota($ticket_id, $titulo_ticket, $autor_nombre, $contenido_nota, $lista_emails) {
    
    $mail = getMailer(); 

    if (!$mail || empty($lista_emails)) {
        // Debug temporal: Si falla la carga del mailer
        error_log("Error: getMailer devolvió null o lista de emails vacía.");
        return false;
    }

    try {
        // Limpiar destinatarios previos por seguridad
        $mail->clearAddresses();
        $mail->clearBCCs();

        foreach ($lista_emails as $email) {
            $mail->addBCC($email); 
        }

        $mail->Subject = "Nueva Nota en Ticket #$ticket_id - $titulo_ticket";

        $mensaje = "
        <div style='font-family: Arial, sans-serif; color: #333;'>
            <h2 style='color: #0d6efd;'>💬 Nueva Nota Interna</h2>
            <p><strong>$autor_nombre</strong> ha dejado una nota en el ticket <strong>#$ticket_id</strong>.</p>
            <hr>
            <div style='background-color: #f8f9fa; padding: 15px; border-left: 4px solid #0d6efd; margin: 10px 0;'>
                <p style='margin: 0; font-style: italic;'>\"" . nl2br(htmlspecialchars($contenido_nota)) . "\"</p>
            </div>
            <p><a href='http://localhost/sistema_tickets/views/ver_ticket.php?id=$ticket_id'>Ver Ticket</a></p>
        </div>";

        $mail->Body = $mensaje;
        $mail->AltBody = "Nueva nota en ticket #$ticket_id: $contenido_nota";

        $mail->send();
        return true;

    } catch (Exception $e) {
        // Esto escribirá el error en el archivo de logs de PHP (xampp/php/logs/php_error_log)
        error_log("Error enviando email: " . $mail->ErrorInfo);
        return false;
    }
}
?>