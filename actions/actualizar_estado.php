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
                   $mail = new PHPMailer(true);

                    // --- CONFIGURACIÓN GMAIL (REAL) ---
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->SMTPSecure = 'tls'; // Gmail requiere TLS sí o sí
                    $mail->Port       = 587;

                    // TUS CREDENCIALES
                    $mail->Username   = 'dmc5812@gmail.com'; // <--- TU GMAIL AQUÍ
                    $mail->Password   = 'gldf wcpf hakh nrcm'; // <--- LA CLAVE DE APLICACIÓN DE 16 LETRAS

                    // QUIÉN LO ENVÍA
                    $mail->setFrom('dmc5812@gmail.com', 'Soporte HelpDesk (Prueba)');
                    
                    // A QUIÉN SE LO ENVIAMOS (Lógica del Supervisor)
                    // Opción A (Elegante): Usar el email que viene de la base de datos
                    // $mail->addAddress($usuario['email'], $usuario['nombre']); 

                    // Opción B (Directa para tu prueba): Forzar el correo de tu jefe
                    $mail->addAddress('diegomolina@dac-controls.com', 'Jefe Supervisor'); 

                    // CONTENIDO
                    $mail->isHTML(true);
                    $mail->CharSet = 'UTF-8';
                    $mail->Subject = "Prueba de Sistema de Tickets - Estado: " . strtoupper($nuevo_estado);
                    $mail->Body    = "
                        <div style='font-family: Arial, sans-serif; color: #333;'>
                            <h2 style='color: #28a745;'>¡Prueba de Correo Real!</h2>
                            <p>Hola,</p>
                            <p>Este es un correo enviado automáticamente desde mi <strong>Sistema de Tickets Local</strong> usando el servidor SMTP de Gmail.</p>
                            <p>El ticket <strong>#$ticket_id</strong> ha cambiado a estado: <strong>" . strtoupper($nuevo_estado) . "</strong>.</p>
                            <hr>
                            <small>Desarrollado con PHP y PHPMailer</small>
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