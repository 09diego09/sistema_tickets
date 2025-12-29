<?php
// includes/mailer.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Rutas absolutas para evitar errores de "archivo no encontrado"
require_once __DIR__ . '/PHPMailer-master/src/Exception.php';
require_once __DIR__ . '/PHPMailer-master/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer-master/src/SMTP.php';

function enviarCorreo($destinatarioEmail, $destinatarioNombre, $asunto, $cuerpoHTML) {
    
    // 1. CARGAR SECRETOS
    $ruta_secretos = __DIR__ . '/../config/secrets.php';
    
    if (file_exists($ruta_secretos)) {
        $secrets = require $ruta_secretos;
    } else {
        // Si no existe el real, intentamos cargar el ejemplo (aunque fallará el envío)
        $ruta_ejemplo = __DIR__ . '/../config/secrets.example.php';
        if (file_exists($ruta_ejemplo)) {
            $secrets = require $ruta_ejemplo;
        } else {
            return false; // Sin configuración no hacemos nada
        }
    }

    $mail = new PHPMailer(true);

    try {
        // 2. CONFIGURACIÓN DEL SERVIDOR
        $mail->isSMTP();
        $mail->Host       = $secrets['smtp_host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $secrets['smtp_user'];
        $mail->Password   = $secrets['smtp_pass'];
        $mail->SMTPSecure = 'tls';
        $mail->Port       = $secrets['smtp_port'];
        $mail->CharSet    = 'UTF-8'; // Importante para acentos y ñ

        // 3. REMITENTE Y DESTINATARIO
        $mail->setFrom($secrets['smtp_user'], 'HelpDesk DAC Controls');
        $mail->addAddress($destinatarioEmail, $destinatarioNombre);

        // 4. CONTENIDO
        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body    = $cuerpoHTML;
        
        // Versión texto plano por si acaso
        $mail->AltBody = strip_tags($cuerpoHTML);

        $mail->send();
        return true; // ¡Éxito!

    } catch (Exception $e) {
        // En producción podrías guardar el error en un log
        // error_log("Error Mailer: " . $mail->ErrorInfo);
        return false; // Falló
    }
}
?>