<?php
// actions/crear_ticket.php
session_start();
require '../config/db.php'; 
require '../includes/mailer.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $usuario_id = $_SESSION['usuario_id'];
    // Validamos que exista el nombre en sesión, si no, ponemos uno genérico
    $usuario_nombre = isset($_SESSION['usuario_nombre']) ? $_SESSION['usuario_nombre'] : 'Usuario';

    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $prioridad = $_POST['prioridad'];
    $departamento = $_POST['departamento'];

    try {
        // ---------------------------------------------------------
        // 1. ASIGNACIÓN AUTOMÁTICA
        // ---------------------------------------------------------
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
        // 2. GUARDAR EN BASE DE DATOS
        // ---------------------------------------------------------
        $sql = "INSERT INTO tickets (usuario_id, agente_id, titulo, descripcion, prioridad, departamento, estado, fecha_creacion) 
                VALUES (:usuario_id, :agente_id, :titulo, :descripcion, :prioridad, :departamento, 'abierto', NOW())";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':usuario_id' => $usuario_id,
            ':agente_id' => $agente_id,
            ':titulo' => $titulo,
            ':descripcion' => $descripcion,
            ':prioridad' => $prioridad,
            ':departamento' => $departamento
        ]);

        $ticket_id = $pdo->lastInsertId(); 

        // ---------------------------------------------------------
        // 3. ENVIAR NOTIFICACIONES
        // ---------------------------------------------------------
        
        // A) Al Usuario Creador (Confirmación)
        // Buscamos su email real en la BD
        $stmt_u = $pdo->prepare("SELECT email FROM usuarios WHERE id = :id");
        $stmt_u->execute([':id' => $usuario_id]);
        $user_data = $stmt_u->fetch();
        
        if ($user_data && !empty($user_data['email'])) {
            $asunto = "Ticket #$ticket_id Creado Exitosamente";
            $html = "
            <div style='font-family: Arial, color: #333;'>
                <h2 style='color: #007bff;'>¡Ticket Recibido!</h2>
                <p>Hola <strong>$usuario_nombre</strong>, tu solicitud ha sido registrada.</p>
                <ul>
                    <li><strong>ID:</strong> #$ticket_id</li>
                    <li><strong>Título:</strong> $titulo</li>
                    <li><strong>Asignado a:</strong> $nombre_tecnico</li>
                </ul>
                <p>Te notificaremos cuando haya cambios.</p>
            </div>";
            
            enviarCorreo($user_data['email'], $usuario_nombre, $asunto, $html);
        }

        // B) Al Supervisor (Alerta Visual Mejorada)
        $supervisor_email = 'diegomolina@dac-controls.com'; // <--- Tu correo de admin
        
        $asunto_sup = "🔔 Nuevo Ticket #$ticket_id - Prioridad: " . strtoupper($prioridad);
        $html_sup = "
        <div style='font-family: Arial, sans-serif; color: #333; max-width: 600px;'>
            <h2 style='color: #d63384;'>⚠️ Nuevo Ticket Registrado</h2>
            <p>Se ha creado una nueva solicitud en el sistema.</p>
            
            <div style='background-color: #fff3cd; padding: 15px; border-radius: 5px; border-left: 5px solid #ffc107; margin: 20px 0;'>
                <p><strong>👤 Usuario:</strong> $usuario_nombre</p>
                <p><strong>📂 Título:</strong> $titulo</p>
                <p><strong>🏢 Depto:</strong> $departamento</p>
                <p><strong>🚨 Prioridad:</strong> " . strtoupper($prioridad) . "</p>
            </div>
            
            <p><a href='http://localhost/sistema_tickets/views/ver_ticket.php?id=$ticket_id'>Ir al Ticket</a></p>
            <hr>
            <small>Panel de Supervisión</small>
        </div>";

        enviarCorreo($supervisor_email, 'Supervisor DAC', $asunto_sup, $html_sup);

        // ---------------------------------------------------------
        // 4. REDIRECCIÓN FINAL (¡Esto era lo que faltaba!)
        // ---------------------------------------------------------
        header("Location: ../views/dashboard.php?mensaje=exito");
        exit;

    } catch (PDOException $e) {
        echo "Error BD: " . $e->getMessage();
    }
}
?>