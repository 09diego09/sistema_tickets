<?php
// actions/actualizar_estado.php
session_start();
require '../config/db.php';
require '../includes/mailer.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ticket_id']) && isset($_POST['estado'])) {
    
    $ticket_id = $_POST['ticket_id'];
    $nuevo_estado = $_POST['estado'];

    try {
        // 1. ACTUALIZAR BD
        $sql = "UPDATE tickets SET estado = :estado WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':estado' => $nuevo_estado, ':id' => $ticket_id]);

        // 2. ENVIAR CORREO (¡AHORA SIN FILTROS!)
        // Buscamos al dueño del ticket
        $sql_user = "SELECT u.email, u.nombre FROM tickets t JOIN usuarios u ON t.usuario_id = u.id WHERE t.id = :id";
        $stmt_user = $pdo->prepare($sql_user);
        $stmt_user->execute([':id' => $ticket_id]);
        $usuario = $stmt_user->fetch();

        if ($usuario && !empty($usuario['email'])) {
            
            // Definimos colores según el estado para que se vea bonito
            $color = '#6c757d'; // Gris por defecto
            if ($nuevo_estado == 'abierto') $color = '#dc3545'; // Rojo
            if ($nuevo_estado == 'en_proceso') $color = '#ffc107'; // Amarillo
            if ($nuevo_estado == 'resuelto') $color = '#28a745'; // Verde
            if ($nuevo_estado == 'cerrado') $color = '#000000'; // Negro

            $asunto = "Actualización Ticket #$ticket_id: " . strtoupper(str_replace('_', ' ', $nuevo_estado));
            
            $html = "
            <div style='font-family: Arial, sans-serif; color: #333; max-width: 600px;'>
                <h2 style='color: #007bff;'>Novedades en tu Ticket</h2>
                <p>Hola <strong>{$usuario['nombre']}</strong>,</p>
                <p>Te informamos que tu ticket <strong>#$ticket_id</strong> ha cambiado de estado.</p>
                
                <div style='background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                    <p style='margin: 0;'>Nuevo Estado:</p>
                    <h3 style='margin: 5px 0; color: $color;'>
                        " . strtoupper(str_replace('_', ' ', $nuevo_estado)) . "
                    </h3>
                </div>
                
                <hr>
                <small>Sistema de Tickets DAC Controls</small>
            </div>";

            // Enviamos el correo
            enviarCorreo($usuario['email'], $usuario['nombre'], $asunto, $html);
        }

        // 3. REDIRIGIR
        header("Location: ../views/dashboard.php?mensaje=actualizado");
        exit;

    } catch (PDOException $e) {
        echo "Error BD: " . $e->getMessage();
    }
} else {
    header("Location: ../views/dashboard.php?error=datos");
    exit;
}
?>