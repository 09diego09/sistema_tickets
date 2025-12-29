<?php
// actions/actualizar_perfil.php
session_start();
require '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['usuario_id'])) {
    
    $id = $_SESSION['usuario_id'];
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    
    $current_pass = $_POST['current_password'];
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    // 1. VERIFICAR CONTRASEÑA ACTUAL
    $stmt = $pdo->prepare("SELECT password FROM usuarios WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $user_db = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user_db || !password_verify($current_pass, $user_db['password'])) {
        // Contraseña actual incorrecta
        header("Location: ../views/mi_perfil.php?error=pass_incorrecto");
        exit;
    }

    try {
        // 2. ¿QUIERE CAMBIAR LA CLAVE?
        if (!empty($new_pass)) {
            if ($new_pass === $confirm_pass) {
                // Encriptamos la nueva
                $new_hash = password_hash($new_pass, PASSWORD_DEFAULT);
                
                $sql = "UPDATE usuarios SET nombre = :nombre, email = :email, password = :pass WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':nombre' => $nombre, ':email' => $email, ':pass' => $new_hash, ':id' => $id]);
            } else {
                header("Location: ../views/mi_perfil.php?error=no_coinciden");
                exit;
            }
        } else {
            // Solo actualizar datos, mantener clave vieja
            $sql = "UPDATE usuarios SET nombre = :nombre, email = :email WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':nombre' => $nombre, ':email' => $email, ':id' => $id]);
        }

        // Actualizamos nombre en sesión para que se refleje al instante en el sidebar
        $_SESSION['usuario_nombre'] = $nombre;

        header("Location: ../views/mi_perfil.php?msg=actualizado");
        exit;

    } catch (PDOException $e) {
        header("Location: ../views/mi_perfil.php?error=db_error");
        exit;
    }

} else {
    header("Location: ../index.php");
    exit;
}
?>