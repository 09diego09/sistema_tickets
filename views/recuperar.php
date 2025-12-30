<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - DAC Controls</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #00c6ff 0%, hsla(213, 100%, 50%, 1.00) 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .card-recover {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            padding: 40px;
            width: 100%;
            max-width: 450px;
        }
    </style>
</head>
<body>

    <div class="card-recover text-center">
        <div class="mb-4">
          <img src="../assets/DAC_logo_innovative-negro.png" alt="DAC Controls" style="max-width: 180px;">
            </div>
        
        <h4 class="fw-bold text-dark mb-1">¿Olvidaste tu clave?</h4>
        <p class="text-muted small mb-4">No te preocupes. Ingresa tu correo y te enviaremos un enlace para restablecerla.</p>

        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'enviado'): ?>
            <div class="alert alert-success border-0 shadow-sm rounded-3 small">
                <i class="bi bi-check-circle-fill me-1"></i> ¡Listo! Revisa tu bandeja de entrada.
            </div>
        <?php endif; ?>

        <?php if(isset($_GET['error']) && $_GET['error'] == 'email_no_existe'): ?>
            <div class="alert alert-danger border-0 shadow-sm rounded-3 small">
                <i class="bi bi-x-circle-fill me-1"></i> Ese correo no está registrado.
            </div>
        <?php endif; ?>

        <form action="../actions/solicitar_recuperacion.php" method="POST">
            <div class="form-floating mb-3">
                <input type="email" name="email" class="form-control rounded-3" id="floatingInput" placeholder="nombre@ejemplo.com" required>
                <label for="floatingInput">Correo Electrónico</label>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm mb-3" style="background: #0072ff; border: none;">
                Enviar Enlace de Recuperación
            </button>
            
            <a href="../index.php" class="text-decoration-none text-muted small">
                <i class="bi bi-arrow-left me-1"></i> Volver al Login
            </a>
        </form>
    </div>

</body>
</html>