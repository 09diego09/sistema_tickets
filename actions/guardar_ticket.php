<?php
// sistema_tickets/actions/guardar_ticket.php
session_start();
require '../config/db.php';

// 1. Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit;
}

// 2. Verificar si vienen datos del formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Recibimos y limpiamos los datos
    $usuario_id = $_SESSION['usuario_id'];
    $departamento = trim($_POST['departamento']);
    $contacto = trim($_POST['contacto']); // Teléfono
    $email_contacto = trim($_POST['email']); // Email extra
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $prioridad = $_POST['prioridad'];
    
    // Validaciones básicas
    if (empty($titulo) || empty($descripcion) || empty($departamento)) {
        // Si falta algo, devolvemos al usuario con error
        header("Location: ../views/crear_ticket.php?error=vacios");
        exit;
    }

    try {
        // 3. Consulta SQL para INSERTAR
        $sql = "INSERT INTO tickets 
                (usuario_id, titulo, descripcion, departamento, telefono_contacto, email_contacto, prioridad, estado, fecha_creacion) 
                VALUES 
                (:uid, :tit, :desc, :dep, :tel, :mail, :prio, 'abierto', NOW())";
        
        $stmt = $pdo->prepare($sql);
        
        $stmt->execute([
            ':uid' => $usuario_id,
            ':tit' => $titulo,
            ':desc' => $descripcion,
            ':dep' => $departamento,
            ':tel' => $contacto,
            ':mail' => $email_contacto,
            ':prio' => $prioridad
        ]);

        // 4. ¡Éxito! Redirigir al Dashboard con mensaje de victoria
        header("Location: ../views/dashboard.php?msg=ticket_creado");
        exit;

    } catch (PDOException $e) {
        // Error de base de datos
        die("Error al guardar en BD: " . $e->getMessage());
    }

} else {
    // Si intentan entrar directo sin formulario
    header("Location: ../views/crear_ticket.php");
    exit;
}
?>