<?php
// actions/crear_ticket.php
session_start();
require '../config/db.php'; 
require '../includes/mailer.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // 1. VALIDACIÓN BÁSICA DE SESIÓN
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: ../index.php");
        exit;
    }

    $usuario_id = $_SESSION['usuario_id'];
    $usuario_nombre = isset($_SESSION['usuario_nombre']) ? $_SESSION['usuario_nombre'] : 'Usuario';

    // 2. RECIBIR Y LIMPIAR DATOS (Agregamos trim para borrar espacios extra)
    $titulo       = trim($_POST['titulo']);
    $descripcion  = trim($_POST['descripcion']);
    $prioridad    = $_POST['prioridad'];
    $departamento = $_POST['departamento'];

    try {
        // ---------------------------------------------------------
        // A. ASIGNACIÓN AUTOMÁTICA (Algoritmo de Carga de Trabajo)
        // ---------------------------------------------------------
        $sql_asignacion = "
            SELECT u.id, u.nombre, COUNT(t.id) as carga_trabajo
            FROM usuarios u
            LEFT JOIN tickets t ON u.id = t.agente_id AND t.estado != 'resuelto'
            WHERE u.rol = 'tecnico' AND u.activo = 1
            GROUP BY u.id
            ORDER BY carga_trabajo ASC
            LIMIT 1
        ";
        $stmt_tecnico = $pdo->query($sql_asignacion);
        $tecnico_asignado = $stmt_tecnico->fetch();

        $agente_id = $tecnico_asignado ? $tecnico_asignado['id'] : null;
        $nombre_tecnico = $tecnico_asignado ? $tecnico_asignado['nombre'] : 'Por Asignar';

        // ---------------------------------------------------------
        // B. GUARDAR EN BASE DE DATOS
        // ---------------------------------------------------------
        $sql = "INSERT INTO tickets (usuario_id, agente_id, titulo, descripcion, prioridad, departamento, estado, fecha_creacion) 
                VALUES (:usuario_id, :agente_id, :titulo, :descripcion, :prioridad, :departamento, 'abierto', NOW())";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':usuario_id'   => $usuario_id,
            ':agente_id'    => $agente_id,
            ':titulo'       => $titulo,
            ':descripcion'  => $descripcion,
            ':prioridad'    => $prioridad,
            ':departamento' => $departamento
        ]);

        $ticket_id = $pdo->lastInsertId(); 

        // ---------------------------------------------------------
        // C. ENVIAR NOTIFICACIONES (DISEÑO PREMIUM ACTUALIZADO)
        // ---------------------------------------------------------
        
        // 1. Al Usuario Creador (Diseño Azul Corporativo)
        $stmt_u = $pdo->prepare("SELECT email FROM usuarios WHERE id = :id");
        $stmt_u->execute([':id' => $usuario_id]);
        $user_data = $stmt_u->fetch();
        
        if ($user_data && !empty($user_data['email'])) {
            $asunto = "✔ Ticket #$ticket_id Recibido - DAC Controls";
            
            // HTML Estilizado (Tarjeta moderna)
            $html = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; color: #333; border: 1px solid #e0e0e0; border-radius: 10px; overflow: hidden;'>
                <div style='background-color: #0d6efd; color: white; padding: 20px; text-align: center;'>
                    <h2 style='margin:0;'>Ticket Registrado</h2>
                </div>
                <div style='padding: 20px;'>
                    <p>Hola <strong>$usuario_nombre</strong>,</p>
                    <p>Tu solicitud ha sido ingresada correctamente a nuestro sistema.</p>
                    
                    <div style='background-color: #f8f9fa; padding: 15px; border-radius: 8px; border-left: 4px solid #0d6efd; margin: 15px 0;'>
                        <p style='margin: 5px 0;'><strong>📂 Título:</strong> $titulo</p>
                        <p style='margin: 5px 0;'><strong>🛠️ Asignado a:</strong> $nombre_tecnico</p>
                        <p style='margin: 5px 0;'><strong>🚦 Prioridad:</strong> " . ucfirst($prioridad) . "</p>
                    </div>

                    <p style='text-align: center; margin-top: 25px;'>
                        <a href='http://localhost/sistema_tickets/views/ver_ticket.php?id=$ticket_id' 
                           style='background-color: #0d6efd; color: white; padding: 10px 20px; text-decoration: none; border-radius: 50px; font-weight: bold;'>
                           Ver Estado del Ticket
                        </a>
                    </p>
                </div>
                <div style='background-color: #f1f1f1; padding: 10px; text-align: center; font-size: 12px; color: #666;'>
                    Sistema de Tickets - DAC Controls
                </div>
            </div>";
            
            enviarCorreo($user_data['email'], $usuario_nombre, $asunto, $html);
        }

        // 2. Al Supervisor (Diseño Alerta Amarilla)
        $supervisor_email = 'diegomolina@dac-controls.com'; // <--- CORREO ADMIN
        
        $asunto_sup = "🔔 Nuevo Ticket #$ticket_id ($departamento)";
        $html_sup = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; color: #333; border: 1px solid #e0e0e0; border-radius: 10px; overflow: hidden;'>
            <div style='background-color: #212529; color: white; padding: 15px; text-align: center;'>
                <h3 style='margin:0;'>Nueva Solicitud</h3>
            </div>
            <div style='padding: 20px;'>
                <p>Se requiere atención para un nuevo ticket.</p>
                
                <table style='width: 100%; border-collapse: collapse;'>
                    <tr><td style='padding: 5px; color: #666;'>Solicitante:</td><td><strong>$usuario_nombre</strong></td></tr>
                    <tr><td style='padding: 5px; color: #666;'>Departamento:</td><td>$departamento</td></tr>
                    <tr><td style='padding: 5px; color: #666;'>Prioridad:</td><td><strong style='color: #d63384;'>" . strtoupper($prioridad) . "</strong></td></tr>
                </table>

                <div style='margin-top: 15px; background-color: #fff3cd; color: #856404; padding: 10px; border-radius: 5px;'>
                    <em>\"$descripcion\"</em>
                </div>

                <p style='margin-top: 20px;'>
                    <a href='http://localhost/sistema_tickets/views/ver_ticket.php?id=$ticket_id' style='color: #0d6efd; text-decoration: none; font-weight: bold;'>Administrar Ticket &rarr;</a>
                </p>
            </div>
        </div>";

        enviarCorreo($supervisor_email, 'Supervisor DAC', $asunto_sup, $html_sup);

        // ---------------------------------------------------------
        // 4. REDIRECCIÓN FINAL (CORREGIDA)
        // ---------------------------------------------------------
        // Cambiamos 'mensaje=exito' por 'msg=ticket_creado' para que salga la alerta verde
        header("Location: ../views/dashboard.php?msg=ticket_creado");
        exit;

    } catch (PDOException $e) {
        error_log("Error DB: " . $e->getMessage());
        header("Location: ../views/dashboard.php?error=db_error");
        exit;
    }
} else {
    header("Location: ../views/dashboard.php");
    exit;
}
?>