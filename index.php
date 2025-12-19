<?php
// sistema_tickets/index.php
session_start();

// Si ya está logueado se va directo al loggin. 
if (isset($_SESSION['usuario_id'])) {
    header("Location: views/dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistema de Tickets</title>
    <link rel="stylesheet" href="css/style.css"> 
    <style>
        /* Estilo provisorio */
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background: #f0f2f5; }
        .login-box { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 300px; }
        input { width: 100%; padding: 10px; margin: 10px 0; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #007bff; color: white; border: none; cursor: pointer; }
        .error { color: red; font-size: 0.9em; text-align: center; }
    </style>
</head>
<body>

<div class="login-box">
    <h2 style="text-align: center;">Acceso Usuarios</h2>
    
    <?php if(isset($_GET['error'])): ?>
        <p class="error">Credenciales incorrectas</p>
    <?php endif; ?>

    <form action="actions/login.php" method="POST">
        <label>Email:</label>
        <input type="email" name="email" required placeholder="admin@tickets.com">
        
        <label>Contraseña:</label>
        <input type="password" name="password" required placeholder="123456">
        
        <button type="submit">Ingresar</button>
    </form>
</div>

</body>
</html>