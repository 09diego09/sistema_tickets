<?php
// sistema_tickets/actions/crear_ticket.php
session_start();
require '../config/db.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $prioridad = $_POST['prioridad'];
    $usuario_id = $_SESSION['usuario_id'];
    
    $departamento = trim($_POST['departamento']);
    $telefono = trim($_POST['telefono']);
    $email_contacto = trim($_POST['email']);
    $categoria_id = !empty($_POST['categoria_id']) ? $_POST['categoria_id'] : null;

    try {
        // --- LOGICA DE SORTEO DE TÉCNICO ---
        // 1. Buscamos técnicos activos. 
        // IMPORTANTE: Asegúrate de que en tu BD el rol esté escrito exactamente como 'tecnico' (sin tildes, minúsculas)
        // Si usaste 'Técnico' o 'admin', cámbialo aquí.
        
        $sql_sorteo = "SELECT id FROM usuarios WHERE rol = 'tecnico' AND activo = 1 ORDER BY RAND() LIMIT 1";
        $stmt_tech = $pdo->query($sql_sorteo);
        $tecnico_asignado = $stmt_tech->fetchColumn(); 
        
        // Verificamos si encontramos a alguien
        $agente_id = $tecnico_asignado ? $tecnico_asignado : null;

        // INSERTAR EL TICKET
        $sql = "INSERT INTO tickets (titulo, descripcion, usuario_id, categoria_id, prioridad, estado, 
                                     departamento, telefono_contacto, email_contacto, agente_id, fecha_creacion) 
                VALUES (:tit, :desc, :uid, :cat, :prio, 'abierto', :depto, :tel, :email, :agente, NOW())";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':tit' => $titulo,
            ':desc' => $descripcion,
            ':uid' => $usuario_id,
            ':cat' => $categoria_id,
            ':prio' => $prioridad,
            ':depto' => $departamento,
            ':tel' => $telefono,
            ':email' => $email_contacto,
            ':agente' => $agente_id 
        ]);

        header("Location: ../views/dashboard.php?msg=ticket_creado");

    } catch (PDOException $e) {
        die("Error al crear ticket: " . $e->getMessage());
    }
}
?>