<?php
// test_mail.php
// Este archivo sirve para probar SOLO el correo, sin base de datos ni logins.

// Incluimos el mailer
require 'includes/mailer.php';

echo "<h1>Iniciando prueba de correo...</h1>";

$mail = getMailer();

if ($mail) {
    // ACTIVAMOS MODO DEBUG: Esto nos dirá exactamente qué responde Gmail
    $mail->SMTPDebug = 2; 
    $mail->Debugoutput = 'html';

    try {
        $mail->addAddress('dmc5812@gmail.com'); // Te lo envías a ti mismo
        $mail->Subject = 'Prueba de Diagnóstico - Sistema Tickets';
        $mail->Body = 'Si lees esto, el sistema de correos funciona correctamente.';
        
        echo "<div style='background: #f0f0f0; padding: 10px; border: 1px solid #ccc;'>";
        if ($mail->send()) {
            echo "<h2 style='color: green'>✅ ÉXITO: Correo enviado.</h2>";
        } else {
            echo "<h2 style='color: red'>❌ ERROR: No se envió.</h2>";
        }
        echo "</div>";
        
    } catch (Exception $e) {
        echo "<h2 style='color: red'>❌ EXCEPCIÓN: " . $mail->ErrorInfo . "</h2>";
    }
} else {
    echo "<h2 style='color: red'>❌ ERROR CRÍTICO: No se pudo cargar PHPMailer. Revisa las rutas.</h2>";
}
?>