<?php
// actions/guardar_nueva_clave.php
require '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = $_POST['token'];
    $email = $_POST['email'];
    $pass = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if ($pass !== $confirm) {
        die("Las contraseñas no coinciden. <a href='javascript:history.back()'>Volver</a>");
    }

    // 1. Encriptar nueva clave
    $new_hash = password_hash($pass, PASSWORD_DEFAULT);

    try {
        // 2. Actualizar Usuario
        $sql = "UPDATE usuarios SET password = :pass WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':pass' => $new_hash, ':email' => $email]);

        // 3. Borrar el token (para que no se pueda usar de nuevo)
        $stmt_del = $pdo->prepare("DELETE FROM password_resets WHERE email = :email");
        $stmt_del->execute([':email' => $email]);

        // 4. Redirigir al login
        header("Location: ../index.php?msg=clave_actualizada");
        exit;

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>