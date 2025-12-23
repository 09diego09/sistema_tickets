<?php
// prueba_correo.php

// 1. Cargamos las clases necesarias de PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// AJUSTA ESTA RUTA si tu carpeta se llama distinto
// Debería estar en: sistema_tickets/includes/PHPMailer-master/src/...
require 'includes/PHPMailer-master/src/Exception.php';
require 'includes/PHPMailer-master/src/PHPMailer.php';
require 'includes/PHPMailer-master/src/SMTP.php';

// 2. Creamos una instancia del correo
$mail = new PHPMailer(true);
$mail->CharSet = 'UTF-8';

try {
    // --- TUS CREDENCIALES DE MAILTRAP ---
    $mail->isSMTP();
    $mail->Host       = 'sandbox.smtp.mailtrap.io';
    $mail->SMTPAuth   = true;
    $mail->Port       = 2525;
    $mail->Username   = 'ae5170fadf82e3'; // <--- Tu usuario
    $mail->Password   = '001e4e46de9b92'; // <--- Tu contraseña

    // --- CONFIGURACIÓN DEL MENSAJE ---
    
    // Remitente (Puede ser cualquiera, es simulado)
    $mail->setFrom('notificaciones@daccontrols.com', 'HelpDesk DAC Controls');
    
    // Destinatario (También simulado, llegará a tu bandeja de Mailtrap)
    $mail->addAddress('gerencia@daccontrols.com', 'Gerente');     

    // Contenido HTML
    $mail->isHTML(true);                                  
    $mail->Subject = 'Prueba de Conexión Exitosa 🚀';
    $mail->Body    = '
        <div style="font-family: Arial, sans-serif; padding: 20px; border: 1px solid #ddd; border-radius: 10px;">
            <h2 style="color: #0071bc;">¡El sistema de correos funciona!</h2>
            <p>Este es un correo de prueba enviado desde <strong>Localhost</strong> usando Mailtrap.</p>
            <hr>
            <p style="color: #555;">Si estás viendo esto, significa que tu código PHP ya sabe cómo "hablar" con un servidor de correos.</p>
            <br>
            <small>Enviado automáticamente por el Sistema de Tickets.</small>
        </div>
    ';
    
    // Texto plano para clientes de correo antiguos que no soportan HTML
    $mail->AltBody = 'El sistema de correos funciona. Este es un mensaje de prueba en texto plano.';

    // 3. ¡ENVIAR!
    $mail->send();
    echo '<div style="font-family: sans-serif; color: green; padding: 20px; border: 2px solid green; border-radius: 10px; text-align: center; margin-top: 50px;">
            <h1>✅ ¡Correo Enviado!</h1>
            <p>Ahora ve a tu pestaña de <strong>Mailtrap</strong> en el navegador y revisa la bandeja de entrada (My Inbox).</p>
          </div>';

} catch (Exception $e) {
    echo '<div style="font-family: sans-serif; color: red; padding: 20px; border: 2px solid red; border-radius: 10px; text-align: center; margin-top: 50px;">
            <h1>❌ Hubo un error</h1>
            <p>El correo no pudo enviarse.</p>
            <p><strong>Detalle del error:</strong> ' . $mail->ErrorInfo . '</p>
          </div>';
}
?>