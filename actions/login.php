<?php
// sistema_tickets/actions/login.php
session_start();
require '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Limpiamos espacios invisibles (Clave del éxito anterior)
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // 2. Buscamos al usuario
    $sql = "SELECT id, nombre, password, rol, activo FROM usuarios WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':email' => $email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // 3. Verificamos contraseña
    if ($usuario && password_verify($password, $usuario['password'])) {
        // Verificar si está activo
        if ($usuario['activo'] == 0) {
            header("Location: ../index.php?error=inactivo");
            exit;
        }

        // ¡LOGIN EXITOSO! Guardamos datos en sesión
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nombre'] = $usuario['nombre'];
        $_SESSION['usuario_rol'] = $usuario['rol'];

        // Redirigir al Dashboard
        header("Location: ../views/dashboard.php");
        exit;
    } else {
        // Falló
        header("Location: ../index.php?error=credenciales");
        exit;
    }
} else {
    header("Location: ../index.php");
    exit;
}
?>