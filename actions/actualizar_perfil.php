<?php
// actions/actualizar_perfil.php
session_start();
require '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['usuario_id'])) {
    
    $id = $_SESSION['usuario_id'];
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    // 1. NUEVO: Capturamos el teléfono. 
    // Usamos el operador ?? '' por si el campo viniera vacío para evitar errores
    $telefono = trim($_POST['telefono'] ?? ''); 
    
    $current_pass = $_POST['current_password'];
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    // 2. VERIFICAR CONTRASEÑA ACTUAL (Esto se mantiene igual, es seguridad básica)
    $stmt = $pdo->prepare("SELECT password FROM usuarios WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $user_db = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user_db || !password_verify($current_pass, $user_db['password'])) {
        // Contraseña actual incorrecta
        header("Location: ../views/mi_perfil.php?error=pass_incorrecto");
        exit;
    }

    try {
        // 3. ¿QUIERE CAMBIAR LA CLAVE?
        if (!empty($new_pass)) {
            if ($new_pass === $confirm_pass) {
                // Encriptamos la nueva
                $new_hash = password_hash($new_pass, PASSWORD_DEFAULT);
                
                // ACTUALIZACIÓN COMPLETA (Nombre, Email, Teléfono y Clave)
                $sql = "UPDATE usuarios SET nombre = :nombre, email = :email, tel_usuarios = :telefono, password = :pass WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':nombre' => $nombre, 
                    ':email' => $email, 
                    ':telefono' => $telefono, // <--- Nuevo dato
                    ':pass' => $new_hash, 
                    ':id' => $id
                ]);
            } else {
                header("Location: ../views/mi_perfil.php?error=no_coinciden");
                exit;
            }
        } else {
            // ACTUALIZACIÓN PARCIAL (Nombre, Email y Teléfono, MANTIENE clave vieja)
            $sql = "UPDATE usuarios SET nombre = :nombre, email = :email, tel_usuarios = :telefono WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nombre' => $nombre, 
                ':email' => $email, 
                ':telefono' => $telefono, // <--- Nuevo dato
                ':id' => $id
            ]);
        }

        // Actualizamos nombre en sesión para que se refleje al instante en el sidebar
        $_SESSION['usuario_nombre'] = $nombre;

        header("Location: ../views/mi_perfil.php?msg=actualizado");
        exit;

    } catch (PDOException $e) {
        // Si quieres ver el error exacto mientras pruebas, descomenta la siguiente línea:
        // die($e->getMessage());
        header("Location: ../views/mi_perfil.php?error=db_error");
        exit;
    }

} else {
    header("Location: ../index.php");
    exit;
}
?>