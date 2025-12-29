<?php
// includes/mailer.php

// 1. Cargamos el archivo de claves (si existe)
// Salimos de 'includes' (../) y entramos a 'config'
$ruta_secretos = __DIR__ . '/../config/secrets.php';

if (file_exists($ruta_secretos)) {
    require_once $ruta_secretos;
} else {
    // Esto detendrá el sistema si no encuentra las claves
    die("Error de seguridad: No encuentro el archivo de configuración en: " . $ruta_secretos);
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/PHPMailer/src/Exception.php';
require __DIR__ . '/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/PHPMailer/src/SMTP.php';

// ==========================================
// CONFIGURACIÓN CENTRAL (MOTOR DE CORREO)
// ==========================================
function getMailer() {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        
        // Usamos las constantes definidas en secrets.php
        $mail->Username   = SMTP_SECURE_EMAIL; 
        $mail->Password   = SMTP_SECURE_PASS;  

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        
        $mail->CharSet = 'UTF-8';
        $mail->setFrom(SMTP_SECURE_EMAIL, 'Sistema de Tickets'); 

        return $mail;
    } catch (Exception $e) {
        return null;
    }
}

// ==========================================
// FUNCIÓN 1: NOTIFICACIÓN DE NOTAS (Con copia oculta)
// ==========================================
function notificarNuevaNota($ticket_id, $titulo_ticket, $autor_nombre, $contenido_nota, $lista_emails) {
    
    $mail = getMailer(); 

    if (!$mail || empty($lista_emails)) {
        error_log("Error: getMailer devolvió null o lista de emails vacía.");
        return false;
    }

    try {
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
        error_log("Error enviando email: " . $mail->ErrorInfo);
        return false;
    }
}

// ==========================================
// FUNCIÓN 2: ENVÍO GENÉRICO (El puente)
// ==========================================
function enviarCorreo($destinatario_email, $destinatario_nombre, $asunto, $cuerpo_html) {
    
    $mail = getMailer(); 

    if (!$mail) {
        error_log("Mailer Error: No se pudo obtener la instancia del mailer.");
        return false;
    }

    try {
        $mail->clearAddresses();
        
        $mail->addAddress($destinatario_email, $destinatario_nombre);

        $mail->Subject = $asunto;
        $mail->Body    = $cuerpo_html;
        $mail->AltBody = strip_tags($cuerpo_html);

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("Error enviando correo a $destinatario_email: " . $mail->ErrorInfo);
        return false;
    }
}
?>