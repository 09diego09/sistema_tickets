<?php
require '../config/db.php';

// Validar Token
if (!isset($_GET['token'])) {
    die("Acceso inválido.");
}

$token = $_GET['token'];
$ahora = date('Y-m-d H:i:s');

// Verificar si el token existe y no ha expirado
$stmt = $pdo->prepare("SELECT email FROM password_resets WHERE token = :token AND expira >= :ahora");
$stmt->execute([':token' => $token, ':ahora' => $ahora]);
$reset_request = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reset_request) {
    die("<div style='text-align:center; padding:50px; font-family:sans-serif;'><h3>❌ Enlace inválido o expirado.</h3><p>Vuelve a solicitar la recuperación.</p><a href='recuperar.php'>Volver</a></div>");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Contraseña</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">

    <div class="card border-0 shadow-lg p-4" style="max-width: 400px; width: 100%; border-radius: 20px;">
        <div class="card-body">
            <h4 class="fw-bold text-primary text-center mb-4">Nueva Contraseña</h4>
            
            <form action="../actions/guardar_nueva_clave.php" method="POST">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($reset_request['email']); ?>">

                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">Ingresa tu nueva clave</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required minlength="4">
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold small text-muted">Confírmala</label>
                    <input type="password" name="confirm_password" class="form-control" placeholder="••••••••" required minlength="4">
                </div>

                <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold shadow-sm">
                    Guardar y Acceder
                </button>
            </form>
        </div>
    </div>

</body>
</html>