<?php
// actions/actualizar_estado.php
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
                
                $mail = new PHPMailer(true);
                try {
                    // Configuración Mailtrap
                    $mail->isSMTP();
                    $mail->Host       = 'sandbox.smtp.mailtrap.io';
                    $mail->SMTPAuth   = true;
                    $mail->Port       = 2525;
                    $mail->Username   = 'ae5170fadf82e3'; // TUS CREDENCIALES
                    $mail->Password   = '001e4e46de9b92'; // TUS CREDENCIALES
                    $mail->CharSet    = 'UTF-8';

                    $mail->setFrom('notificaciones@daccontrols.com', 'HelpDesk DAC');
                    $mail->addAddress($usuario['email'], $usuario['nombre']);

                    $mail->isHTML(true);
                    $mail->Subject = "Ticket #$ticket_id - Estado: " . strtoupper($nuevo_estado);
                    $mail->Body    = "
                        <div style='font-family: Arial, sans-serif; color: #333;'>
                            <h2 style='color: #28a745;'>Estado Actualizado</h2>
                            <p>El ticket <strong>#$ticket_id</strong> ahora está: <strong>" . strtoupper($nuevo_estado) . "</strong></p>
                            <hr>
                            <small>DAC Controls Helpdesk</small>
                        </div>
                    ";

                    $mail->send();
                } catch (Exception $e) {
                    // Ignoramos errores de correo para no frenar el sistema
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