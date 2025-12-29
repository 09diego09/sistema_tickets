<?php
// actions/crear_ticket.php
session_start();
// Activamos reporte de errores temporalmente para ver si explota algo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../config/db.php'; 
require '../includes/mailer.php'; 

// Función auxiliar para colores
if (!function_exists('getColorPrioridad')) {
    function getColorPrioridad($prio) {
        if ($prio == 'alta') return '#dc3545';
        if ($prio == 'media') return '#ffc107';
        return '#198754';
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (!isset($_SESSION['usuario_id'])) { header("Location: ../index.php"); exit; }

    $usuario_id = $_SESSION['usuario_id'];
    $usuario_nombre = $_SESSION['usuario_nombre'] ?? 'Usuario';

    $titulo       = trim($_POST['titulo']);
    $descripcion  = trim($_POST['descripcion']);
    $prioridad    = $_POST['prioridad'];
    $departamento = $_POST['departamento'];
    
    // 1. SUBIDA DE ARCHIVOS
    $nombre_archivo = null; 
    if (isset($_FILES['adjunto']) && $_FILES['adjunto']['error'] == 0) {
        $directorio_destino = "../assets/uploads/";
        if (!is_dir($directorio_destino)) { mkdir($directorio_destino, 0777, true); } // Crear carpeta si no existe

        $archivo_info = pathinfo($_FILES['adjunto']['name']);
        $extension = strtolower($archivo_info['extension']);
        $permitidos = ['jpg', 'jpeg', 'png', 'pdf'];

        if (in_array($extension, $permitidos)) {
            $nombre_archivo = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "", $archivo_info['basename']);
            if (!move_uploaded_file($_FILES['adjunto']['tmp_name'], $directorio_destino . $nombre_archivo)) {
                $nombre_archivo = null; 
            }
        }
    }

    try {
        // 2. LOGICA DE ASIGNACIÓN
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

        // 3. GUARDAR TICKET
        $sql = "INSERT INTO tickets (usuario_id, agente_id, titulo, descripcion, prioridad, departamento, adjunto, estado, fecha_creacion) 
                VALUES (:usuario_id, :agente_id, :titulo, :descripcion, :prioridad, :departamento, :adjunto, 'abierto', NOW())";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':usuario_id'   => $usuario_id,
            ':agente_id'    => $agente_id,
            ':titulo'       => $titulo,
            ':descripcion'  => $descripcion,
            ':prioridad'    => $prioridad,
            ':departamento' => $departamento,
            ':adjunto'      => $nombre_archivo
        ]);

        $ticket_id = $pdo->lastInsertId(); 

        // 4. ENVÍO DE CORREOS (Con bloque Try-Catch independiente)
        try {
            // Estilos Corporativos
            $bg_app = "#f0f8ff"; 
            $color_primary = "#0072ff"; 
            $color_text = "#334e68";
            $color_prio = getColorPrioridad($prioridad);

            // A) CORREO CLIENTE (Usando HEREDOC para evitar errores de comillas)
            $stmt_u = $pdo->prepare("SELECT email FROM usuarios WHERE id = :id");
            $stmt_u->execute([':id' => $usuario_id]);
            $user_data = $stmt_u->fetch();
            
            if ($user_data && !empty($user_data['email'])) {
                $asunto = "✔ Ticket #$ticket_id Recibido - DAC Controls";
                
                // HTML SEGURO
                $html = <<<HTML
                <div style="font-family: 'Segoe UI', Arial, sans-serif; background-color: $bg_app; padding: 40px 0; color: $color_text;">
                    <div style="background-color: #ffffff; max-width: 600px; margin: 0 auto; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); overflow: hidden;">
                        <div style="background: linear-gradient(180deg, #00c6ff 0%, #0072ff 100%); padding: 35px; text-align: center;">
                            <h1 style="color: white; margin: 0; font-size: 26px;">DAC CONTROLS</h1>
                            <p style="color: rgba(255,255,255,0.9); margin: 5px 0 0;">Mesa de Ayuda</p>
                        </div>
                        <div style="padding: 40px 30px;">
                            <h2 style="color: $color_text; margin-top: 0;">Hola, $usuario_nombre 👋</h2>
                            <p>Tu solicitud ha sido ingresada. Detalles del registro:</p>
                            <div style="background-color: #f0f4f8; border-radius: 15px; padding: 20px; margin: 25px 0;">
                                <p style="margin:5px 0;"><strong>Asunto:</strong> $titulo</p>
                                <p style="margin:5px 0;"><strong>Técnico:</strong> $nombre_tecnico</p>
                            </div>
                            <center>
                                <a href="http://localhost/sistema_tickets/views/ver_ticket.php?id=$ticket_id" style="display: inline-block; background-color: $color_primary; color: #ffffff; padding: 12px 35px; border-radius: 50px; text-decoration: none; font-weight: bold;">Ver Ticket</a>
                            </center>
                        </div>
                    </div>
                </div>
HTML;
                enviarCorreo($user_data['email'], $usuario_nombre, $asunto, $html);
            }

            // B) CORREO SUPERVISOR
            $supervisor_email = 'diegomolina@dac-controls.com';
            $asunto_sup = "🔔 Nuevo Ticket #$ticket_id | $departamento";
            $aviso_adjunto = $nombre_archivo ? '<div style="color:#0072ff; font-weight:bold; margin-top:10px;">📎 Archivo Adjunto</div>' : '';

            $html_sup = <<<HTML
            <div style="font-family: Arial, sans-serif; background-color: $bg_app; padding: 40px 0;">
                <div style="background-color: #ffffff; max-width: 600px; margin: 0 auto; border-radius: 20px; border-top: 6px solid $color_prio; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                    <div style="padding: 30px; border-bottom: 1px solid #f0f4f8;">
                        <span style="float:right; background:#f0f4f8; color:#627d98; padding:5px 10px; border-radius:15px; font-size:12px; font-weight:bold;">#$ticket_id</span>
                        <h2 style="margin:0; color: $color_text; font-size:20px;">$titulo</h2>
                    </div>
                    <div style="padding: 30px;">
                        <table width="100%">
                            <tr>
                                <td><strong>Solicitante:</strong><br>$usuario_nombre</td>
                                <td><strong>Prioridad:</strong><br><span style="color:$color_prio;">$prioridad</span></td>
                            </tr>
                        </table>
                        <div style="background-color:#fffcf0; border:1px solid #fef3c7; border-radius:12px; padding:20px; margin-top:20px; color:#4b5563;">
                            <strong>Descripción:</strong><br>
                            $descripcion
                            $aviso_adjunto
                        </div>
                        <center style="margin-top:30px;">
                            <a href="http://localhost/sistema_tickets/views/ver_ticket.php?id=$ticket_id" style="display:inline-block; background-color:#334e68; color:white; padding:10px 30px; border-radius:50px; text-decoration:none;">Gestionar</a>
                        </center>
                    </div>
                </div>
            </div>
HTML;
            enviarCorreo($supervisor_email, 'Supervisor DAC', $asunto_sup, $html_sup);

        } catch (Exception $e) {
            // Si falla el correo, NO detenemos el sistema, solo lo registramos en el log
            error_log("Error enviando correo: " . $e->getMessage());
        }

        header("Location: ../views/dashboard.php?msg=ticket_creado");
        exit;

    } catch (PDOException $e) {
        error_log("Error BD: " . $e->getMessage());
        header("Location: ../views/dashboard.php?error=db_error");
        exit;
    }
}
?>