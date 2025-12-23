<?php
// actions/actualizar_estado.php

// 1. CARGA DE SECRETOS (Tu lógica está perfecta aquí)
$ruta_secretos = '../config/secrets.php';
if (file_exists($ruta_secretos)) {
    $secrets = require $ruta_secretos;
} else {
    // Fallback por si alguien descarga el repo y olvida crear el archivo
    $secrets = require '../config/secrets.example.php';
}

session_start();
require '../config/db.php';

// Cargar PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../includes/PHPMailer-master/src/Exception.php';
require '../includes/PHPMailer-master/src/PHPMailer.php';
require '../includes/PHPMailer-master/src/SMTP.php';

// Validamos que lleguen los datos
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ticket_id']) && isset($_POST['estado'])) {
    
    $ticket_id = $_POST['ticket_id'];
    $nuevo_estado = $_POST['estado'];

    try {
        // 1. ACTUALIZAR EL ESTADO EN LA BD
        $sql = "UPDATE tickets SET estado = :estado WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':estado' => $nuevo_estado, ':id' => $ticket_id]);

        // 2. ENVIAR CORREO (Solo si se resuelve o cierra)
        if ($nuevo_estado == 'resuelto' || $nuevo_estado == 'cerrado') {
            
            // Buscar datos del dueño del ticket
            $sql_user = "SELECT u.email, u.nombre FROM tickets t JOIN usuarios u ON t.usuario_id = u.id WHERE t.id = :id";
            $stmt_user = $pdo->prepare($sql_user);
            $stmt_user->execute([':id' => $ticket_id]);
            $usuario = $stmt_user->fetch();

            // Verificamos si tiene correo válido
            if ($usuario && !empty($usuario['email']) && filter_var($usuario['email'], FILTER_VALIDATE_EMAIL)) {
                
                $mail = new PHPMailer(true); // Inicializamos una sola vez
                try {
                    // --- CONFIGURACIÓN USANDO SECRETS ---
                    $mail->CharSet = 'UTF-8';
                    $mail->isSMTP();
                    $mail->Host       = $secrets['smtp_host'];
                    $mail->SMTPAuth   = true;
                    $mail->SMTPSecure = 'tls';
                    $mail->Port       = $secrets['smtp_port'];

                    // Credenciales desde el archivo seguro
                    $mail->Username   = $secrets['smtp_user']; 
                    $mail->Password   = $secrets['smtp_pass']; 

                    // Remitente
                    $mail->setFrom($secrets['smtp_user'], 'HelpDesk DAC');

                    // DESTINATARIO (¡Esto faltaba!)
                    $mail->addAddress($usuario['email'], $usuario['nombre']);

                    // CONTENIDO
                    $mail->isHTML(true);
                    $mail->Subject = "Ticket #$ticket_id Actualizado - Estado: " . strtoupper($nuevo_estado);
                    $mail->Body    = "
                        <div style='font-family: Arial, sans-serif; color: #333;'>
                            <h2 style='color: #28a745;'>¡Actualización de Ticket!</h2>
                            <p>Hola <strong>{$usuario['nombre']}</strong>,</p>
                            <p>Te informamos que tu ticket <strong>#$ticket_id</strong> ha cambiado a estado:</p>
                            <h3 style='background-color: #f8f9fa; padding: 10px; border-left: 5px solid #28a745; display: inline-block;'>
                                " . strtoupper($nuevo_estado) . "
                            </h3>
                            <hr>
                            <small>Sistema de Tickets DAC Controls</small>
                        </div>
                    ";
                    
                    $mail->send();

                } catch (Exception $e) {
                    // Ignoramos errores de correo para no frenar el sistema
                    // (Opcional: puedes descomentar la siguiente línea para depurar si falla)
                    // error_log("Error Mailer: " . $mail->ErrorInfo);
                }
            }
        }

        // 3. REDIRIGIR AL DASHBOARD
        header("Location: ../views/dashboard.php?mensaje=actualizado");
        exit;

    } catch (PDOException $e) {
        echo "Error de BD: " . $e->getMessage();
    }
} else {
    // Si faltan datos
    header("Location: ../views/dashboard.php?error=faltan_datos");
    exit;
}
?>