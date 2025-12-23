<?php
// sistema_tickets/actions/guardar_usuario.php
session_start();
require '../config/db.php';

// 1. SEGURIDAD: Solo admin puede guardar usuarios
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id']; // Si viene vacío es CREAR, si trae número es EDITAR
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $rol = $_POST['rol'];
    $activo = $_POST['activo'];

    try {
        // --- CASO 1: CREAR NUEVO USUARIO ---
        if (empty($id)) {
            // Validar que la contraseña no esté vacía para nuevos usuarios
            if (empty($password)) {
                header("Location: ../views/admin_usuarios.php?error=pass_requerida");
                exit;
            }

            // Encriptar contraseña
            $hash = password_hash($password, PASSWORD_DEFAULT);

            // Insertamos usando exactamente tus columnas
            $sql = "INSERT INTO usuarios (nombre, email, password, rol, activo, fecha_creacion) 
                    VALUES (:nom, :email, :pass, :rol, :act, NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nom' => $nombre,
                ':email' => $email,
                ':pass' => $hash,
                ':rol' => $rol,
                ':act' => $activo
            ]);

        // --- CASO 2: ACTUALIZAR USUARIO EXISTENTE ---
        } else {
            // Lógica para la contraseña: ¿La cambiaron o sigue igual?
            if (!empty($password)) {
                // Si escribió algo, actualizamos todo incluyendo la pass
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $sql = "UPDATE usuarios SET nombre = :nom, email = :email, password = :pass, rol = :rol, activo = :act WHERE id = :id";
                $params = [
                    ':nom' => $nombre, 
                    ':email' => $email, 
                    ':pass' => $hash, 
                    ':rol' => $rol, 
                    ':act' => $activo, 
                    ':id' => $id
                ];
            } else {
                // Si la dejó en blanco, NO tocamos la contraseña (se mantiene la vieja)
                $sql = "UPDATE usuarios SET nombre = :nom, email = :email, rol = :rol, activo = :act WHERE id = :id";
                $params = [
                    ':nom' => $nombre, 
                    ':email' => $email, 
                    ':rol' => $rol, 
                    ':act' => $activo, 
                    ':id' => $id
                ];
            }
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
        }

        // Volver a la lista con éxito
        header("Location: ../views/admin_usuarios.php?msg=guardado");

    } catch (PDOException $e) {
        // Error común: Email duplicado
        if ($e->getCode() == 23000) {
             header("Location: ../views/admin_usuarios.php?error=email_duplicado");
        } else {
             die("Error en base de datos: " . $e->getMessage());
        }
    }
}
?>