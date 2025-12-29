<?php
// actions/agregar_nota.php
session_start();
require '../config/db.php';
require '../includes/mailer.php';

// 1. SEGURIDAD
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $ticket_id = $_POST['ticket_id'];
    $texto_nota = trim($_POST['nota']);
    $usuario_id = $_SESSION['usuario_id'];
    $usuario_nombre = $_SESSION['usuario_nombre'];

    // CORRECCIÓN ERROR 1: Obtenemos el email actual desde la BD, no de la sesión
    $stmt_u = $pdo->prepare("SELECT email FROM usuarios WHERE id = :uid");
    $stmt_u->execute([':uid' => $usuario_id]);
    $user_data = $stmt_u->fetch(PDO::FETCH_ASSOC);
    $usuario_email_actual = $user_data['email'];

    if (!empty($texto_nota)) {
        try {
            // 2. INSERTAR LA NOTA
            $sql = "INSERT INTO notas_tickets (ticket_id, usuario_id, nota, fecha) 
                    VALUES (:tid, :uid, :nota, NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':tid' => $ticket_id,
                ':uid' => $usuario_id,
                ':nota' => $texto_nota
            ]);
            
            // 3. LÓGICA DE NOTIFICACIÓN
            // A) Datos del ticket
            $stmt_ticket = $pdo->prepare("SELECT titulo, agente_id FROM tickets WHERE id = :id");
            $stmt_ticket->execute([':id' => $ticket_id]);
            $ticket_info = $stmt_ticket->fetch(PDO::FETCH_ASSOC);

            if ($ticket_info) {
                $destinatarios = [];

                // B) Admins
                $stmt_admins = $pdo->query("SELECT email FROM usuarios WHERE rol = 'admin' AND activo = 1");
                while ($admin = $stmt_admins->fetch(PDO::FETCH_ASSOC)) {
                    $destinatarios[] = $admin['email'];
                }

                // C) Técnico Asignado
                if (!empty($ticket_info['agente_id'])) {
                    $stmt_tecnico = $pdo->prepare("SELECT email FROM usuarios WHERE id = :id AND activo = 1");
                    $stmt_tecnico->execute([':id' => $ticket_info['agente_id']]);
                    $tecnico = $stmt_tecnico->fetch(PDO::FETCH_ASSOC);
                    if ($tecnico) {
                        $destinatarios[] = $tecnico['email'];
                    }
                }

                // D) Limpieza (Quitar duplicados y quitarme a mí mismo)
                $destinatarios = array_unique($destinatarios);
                // Usamos el email que trajimos de la BD
                $destinatarios = array_diff($destinatarios, [$usuario_email_actual]);

                // E) Enviar
                if (!empty($destinatarios)) {
                    notificarNuevaNota(
                        $ticket_id, 
                        $ticket_info['titulo'], 
                        $usuario_nombre, 
                        $texto_nota, 
                        $destinatarios
                    );
                }
            }

            header("Location: ../views/ver_ticket.php?id=$ticket_id&mensaje=nota_agregada");
            exit;

        } catch (PDOException $e) {
            header("Location: ../views/ver_ticket.php?id=$ticket_id&error=db_error");
            exit;
        }
    } else {
        header("Location: ../views/ver_ticket.php?id=$ticket_id&error=campo_vacio");
        exit;
    }
} else {
    header("Location: ../views/dashboard.php");
    exit;
}
?>