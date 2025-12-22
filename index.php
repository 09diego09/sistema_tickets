<?php
// sistema_tickets/index.php
session_start();

// Si el usuario ya está logueado, lo mandamos directo al dashboard
if (isset($_SESSION['usuario_id'])) {
    header("Location: views/dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso - Sistema de Tickets</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        /* 1. CONFIGURACIÓN DEL FONDO (El estilo DAC Controls) */
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow: hidden; /* Evita barras de desplazamiento */
        }

        #particles-js {
            position: absolute;
            width: 100%;
            height: 100%;
            /* El degradado azul que definimos la vez pasada */
            background: linear-gradient(135deg, #29abe2 0%, #0071bc 100%);
            z-index: 1; /* Capa del fondo */
        }

        /* 2. EL CONTENEDOR DEL LOGIN (Flotando encima) */
        .login-wrapper {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center; /* Centrado Horizontal */
            align-items: center;     /* Centrado Vertical */
            z-index: 2; /* Capa superior */
            pointer-events: none; /* Permite clicks en el fondo si no tocas la caja */
        }

        /* 3. LA TARJETA DEL FORMULARIO */
        .card-login {
            background: rgba(255, 255, 255, 0.95); /* Blanco con ligerísima transparencia */
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
            pointer-events: auto; /* Reactivamos clicks aquí */
            border: none;
        }

        .btn-brand {
            background-color: #0071bc;
            border-color: #0071bc;
            color: white;
            font-weight: 600;
            padding: 12px;
            transition: all 0.3s;
        }

        .btn-brand:hover {
            background-color: #005a96;
            border-color: #005a96;
            transform: translateY(-2px);
        }

        .brand-logo {
            font-size: 2rem;
            font-weight: bold;
            color: #0071bc;
            margin-bottom: 10px;
            text-align: center;
        }
    </style>
</head>
<body>

    <div id="particles-js"></div>

    <div class="login-wrapper">
        <div class="card-login">
            <div class="brand-logo">
                <i class="bi bi-ticket-perforated-fill"></i> Dac-Controls HelpDesk
            </div>
            <p class="text-center text-muted mb-4">Ingresa tus credenciales</p>

            <?php if(isset($_GET['error'])): ?>
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <div>Credenciales incorrectas</div>
                </div>
            <?php endif; ?>

            <form action="actions/login.php" method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold">Correo Electrónico</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" class="form-control" placeholder="admin@tickets.com" required>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-bold">Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" class="form-control" placeholder="••••••" required>
                    </div>
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-brand">Ingresar al Sistema</button>
                </div>
            </form>
            
            <div class="text-center mt-3">
                <a href="#" class="text-decoration-none small text-muted">¿Olvidaste tu contraseña?</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
    
    <script>
        particlesJS("particles-js", {
            "particles": {
                "number": { "value": 80, "density": { "enable": true, "value_area": 800 } },
                "color": { "value": "#ffffff" },
                "shape": { "type": "circle" },
                "opacity": { "value": 0.5, "random": false },
                "size": { "value": 3, "random": true },
                "line_linked": {
                    "enable": true,
                    "distance": 150,
                    "color": "#ffffff",
                    "opacity": 0.4,
                    "width": 1
                },
                "move": {
                    "enable": true,
                    "speed": 2, // Velocidad moderada
                    "direction": "none",
                    "random": false,
                    "straight": false,
                    "out_mode": "out",
                    "bounce": false
                }
            },
            "interactivity": {
                "detect_on": "canvas",
                "events": {
                    "onhover": { "enable": true, "mode": "grab" },
                    "onclick": { "enable": true, "mode": "push" },
                    "resize": true
                },
                "modes": {
                    "grab": { "distance": 140, "line_linked": { "opacity": 1 } }
                }
            },
            "retina_detect": true
        });
    </script>
</body>
</html>