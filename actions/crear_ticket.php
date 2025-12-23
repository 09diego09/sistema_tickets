<?php
// actions/crear_ticket.php
session_start();
require '../config/db.php'; 

// --- CARGAMOS PHPMAILER ---
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../includes/PHPMailer-master/src/Exception.php';
require '../includes/PHPMailer-master/src/PHPMailer.php';
require '../includes/PHPMailer-master/src/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $usuario_id = $_SESSION['usuario_id'];
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $prioridad = $_POST['prioridad'];
    $departamento = $_POST['departamento'];

    try {
        // ---------------------------------------------------------
        // PASO 1: LÓGICA DE ASIGNACIÓN AUTOMÁTICA
        // ---------------------------------------------------------
        // CORRECCIÓN: Usamos 'agente_id' en lugar de 'tecnico_id'
        $sql_asignacion = "
            SELECT u.id, u.nombre, COUNT(t.id) as carga_trabajo
            FROM usuarios u
            LEFT JOIN tickets t ON u.id = t.agente_id AND t.estado != 'resuelto'
            WHERE u.rol = 'tecnico'
            GROUP BY u.id
            ORDER BY carga_trabajo ASC
            LIMIT 1
        ";
        
        $stmt_tecnico = $pdo->query($sql_asignacion);
        $tecnico_asignado = $stmt_tecnico->fetch();

        $agente_id = $tecnico_asignado ? $tecnico_asignado['id'] : null;
        $nombre_tecnico = $tecnico_asignado ? $tecnico_asignado['nombre'] : 'Por Asignar';


        // ---------------------------------------------------------
        // PASO 2: GUARDAR EN BASE DE DATOS
        // ---------------------------------------------------------
        // CORRECCIÓN: Insertamos en 'agente_id'
        $sql = "INSERT INTO tickets (usuario_id, agente_id, titulo, descripcion, prioridad, departamento, estado, fecha_creacion) 
                VALUES (:usuario_id, :agente_id, :titulo, :descripcion, :prioridad, :departamento, 'abierto', NOW())";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':usuario_id' => $usuario_id,
            ':agente_id' => $agente_id,  // Usamos la variable con el ID del técnico
            ':titulo' => $titulo,
            ':descripcion' => $descripcion,
            ':prioridad' => $prioridad,
            ':departamento' => $departamento
        ]);

        $ticket_id = $pdo->lastInsertId(); 


        // ---------------------------------------------------------
        // PASO 3: ENVIAR NOTIFICACIÓN POR CORREO
        // ---------------------------------------------------------
        $mail = new PHPMailer(true);
        try {
            $mail->CharSet = 'UTF-8';
            $mail->isSMTP();
            $mail->Host       = 'sandbox.smtp.mailtrap.io';
            $mail->SMTPAuth   = true;
            $mail->Port       = 2525;
            $mail->Username   = 'ae5170fadf82e3'; // TUS CREDENCIALES
            $mail->Password   = '001e4e46de9b92'; // TUS CREDENCIALES

            $mail->setFrom('notificaciones@daccontrols.com', 'HelpDesk DAC');
            $mail->addAddress('soporte@daccontrols.com', 'Equipo de Soporte');

            $mail->isHTML(true);
            $mail->Subject = "Nuevo Ticket #$ticket_id - Asignado a $nombre_tecnico";
            
            $bodyContent = "
            <div style='font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: auto; border: 1px solid #e0e0e0; border-radius: 10px; overflow: hidden;'>
                <div style='background-color: #0071bc; padding: 20px; text-align: center;'>
                    <h2 style='color: #ffffff; margin: 0;'>Nuevo Ticket Asignado</h2>
                </div>
                <div style='padding: 20px;'>
                    <p style='font-size: 16px;'>Se ha generado un nuevo ticket y el sistema lo ha asignado automáticamente.</p>
                    
                    <div style='background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                        <p style='margin: 5px 0;'><strong>🆔 ID Ticket:</strong> #$ticket_id</p>
                        <p style='margin: 5px 0;'><strong>👤 Solicitante:</strong> " . $_SESSION['usuario_nombre'] . "</p>
                        <p style='margin: 5px 0;'><strong>🛠️ Técnico Asignado:</strong> <span style='color: #0071bc; font-weight: bold;'>$nombre_tecnico</span></p>
                        <p style='margin: 5px 0;'><strong>🏢 Departamento:</strong> $departamento</p>
                        <p style='margin: 5px 0;'><strong>⚠️ Prioridad:</strong> " . strtoupper($prioridad) . "</p>
                    </div>

                    <h3 style='color: #555; font-size: 16px; border-bottom: 2px solid #eee; padding-bottom: 10px;'>Descripción del Problema</h3>
                    <p style='line-height: 1.6; color: #444;'>$descripcion</p>
                    
                    <hr style='border: 0; border-top: 1px solid #eee; margin: 20px 0;'>
                    <p style='font-size: 12px; color: #999; text-align: center;'>Sistema de Tickets DAC Controls - Notificación Automática</p>
                </div>
            </div>";

            $mail->Body = $bodyContent;
            $mail->send();

        } catch (Exception $e) {
            // Si falla el correo, continuamos
        }

        header("Location: ../views/dashboard.php?mensaje=exito");
        exit;

    } catch (PDOException $e) {
        echo "Error en la base de datos: " . $e->getMessage();
    }
}
?>