<?php
// actions/solicitar_recuperacion.php
require '../config/db.php';
require '../includes/mailer.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);

    // 1. Verificar si el usuario existe
    $stmt = $pdo->prepare("SELECT id, nombre FROM usuarios WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        // 2. Generar Token Único y Fecha de Expiración (1 hora)
        $token = bin2hex(random_bytes(50));
        $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // 3. Guardar en tabla password_resets
        // Primero borramos si ya había solicitado antes para no llenar de basura
        $stmt_del = $pdo->prepare("DELETE FROM password_resets WHERE email = :email");
        $stmt_del->execute([':email' => $email]);

        // Insertamos el nuevo
        $sql = "INSERT INTO password_resets (email, token, expira) VALUES (:email, :token, :expira)";
        $stmt_insert = $pdo->prepare($sql);
        $stmt_insert->execute([':email' => $email, ':token' => $token, ':expira' => $expira]);

        // 4. Enviar Correo (Diseño Premium)
        $link = "http://localhost/sistema_tickets/views/reset_password.php?token=" . $token;
        // para prueba de marcha blanca, cambiar 'localhost' por IP (ej: 192.168.1.XX)
        
        $asunto = "🔐 Recuperación de Contraseña - DAC Controls";
        $html = <<<HTML
        <div style="font-family: 'Segoe UI', Arial, sans-serif; background-color: #f0f8ff; padding: 40px 0;">
            <div style="background-color: #ffffff; max-width: 500px; margin: 0 auto; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); overflow: hidden;">
                <div style="background: linear-gradient(180deg, #00c6ff 0%, #0072ff 100%); padding: 30px; text-align: center;">
                    <h1 style="color: white; margin: 0; font-size: 24px;">Restablecer Clave</h1>
                </div>
                <div style="padding: 40px 30px; text-align: center;">
                    <p style="color: #334e68; font-size: 16px;">Hola <strong>{$usuario['nombre']}</strong>,</p>
                    <p style="color: #627d98;">Recibimos una solicitud para cambiar tu contraseña. Haz clic abajo para crear una nueva:</p>
                    
                    <a href="$link" style="display: inline-block; background-color: #0072ff; color: #ffffff; padding: 15px 30px; border-radius: 50px; text-decoration: none; font-weight: bold; margin: 20px 0; box-shadow: 0 4px 10px rgba(0, 114, 255, 0.3);">
                        Crear Nueva Contraseña
                    </a>
                    
                    <p style="color: #999; font-size: 13px; margin-top: 20px;">Si no fuiste tú, ignora este correo. El enlace expira en 1 hora.</p>
                </div>
            </div>
        </div>
HTML;

        enviarCorreo($email, $usuario['nombre'], $asunto, $html);
        
        // Redirigir con éxito (incluso si el correo falla, para no dar pistas a hackers
        header("Location: ../views/recuperar.php?msg=enviado");
        exit;
    } else {
        // Por seguridad, a veces se dice "Si el correo existe, se envió", pero para uso interno es mejor avisar si se equivocaron.
        header("Location: ../views/recuperar.php?error=email_no_existe");
        exit;
    }
}
?>