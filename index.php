<?php
// sistema_tickets/index.php
session_start();

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
    <title>Acceso - Dac-Controls HelpDesk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        /* 1. FONDO MÁS OSCURO (Azul Corporativo Profundo) */
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Segoe UI', Roboto, Helvetica, sans-serif;
            overflow: hidden;
            /* Azul más oscuro y profesional (antes era celeste claro) */
            background-color: #0077c8; 
            /* Opcional: Si quieres un degradado sutil, descomenta esto: */
            /* background: linear-gradient(135deg, #0077c8 0%, #005c99 100%); */
        }

        #particles-js {
            position: absolute;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        .login-wrapper {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 2;
            pointer-events: none; /* Deja pasar clicks a las partículas en los bordes */
        }

        /* 2. TARJETA DE LOGIN */
        .card-login {
            background: #ffffff;
            padding: 45px 40px;
            border-radius: 12px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 420px;
            text-align: center;
            pointer-events: auto; /* Reactiva clicks dentro de la tarjeta */
        }

        .brand-title {
            color: #005c99; /* Azul oscuro */
            font-weight: 700;
            font-size: 1.5rem;
            margin-bottom: 5px;
            letter-spacing: -0.5px;
        }
        
        .brand-subtitle {
            color: #005c99;
            font-weight: 700;
            font-size: 1.8rem;
            margin-bottom: 10px;
        }

        .login-instruction {
            color: #8c98a4;
            font-size: 0.9rem;
            margin-bottom: 30px;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.9rem;
            color: #333;
            text-align: left;
            display: block;
            margin-bottom: 8px;
        }

        .form-control {
            background-color: #fff;
            border: 1px solid #e1e4e8;
            border-radius: 6px;
            padding: 12px 15px;
            font-size: 0.95rem;
            color: #333;
            box-shadow: none;
        }

        .form-control:focus {
            border-color: #0077c8;
            box-shadow: 0 0 0 3px rgba(0, 119, 200, 0.1);
        }

        .btn-brand {
            background-color: #005c99;
            border: none;
            color: white;
            font-weight: 600;
            padding: 12px;
            border-radius: 6px;
            width: 100%;
            margin-top: 10px;
            transition: all 0.2s;
        }

        .btn-brand:hover {
            background-color: #00447a;
            transform: translateY(-2px);
        }

        .forgot-link {
            color: #8c98a4;
            font-size: 0.85rem;
            text-decoration: none;
            margin-top: 20px;
            display: inline-block;
        }
    </style>
</head>
<body>

    <div id="particles-js"></div>

    <div class="login-wrapper">
        <div class="card-login">
            <div class="mb-2">
                <i class="bi bi-hdd-network-fill" style="font-size: 2rem; color: #005c99;"></i>
            </div>
            <div class="brand-title">Dac-Controls</div>
            <div class="brand-subtitle">HelpDesk</div>
            <div class="login-instruction">Ingresa tus credenciales</div>

            <?php if(isset($_GET['error'])): ?>
                <div class="alert alert-danger p-2 small mb-4" role="alert">
                    Datos incorrectos. Inténtalo de nuevo.
                </div>
            <?php endif; ?>

            <form action="actions/login.php" method="POST">
                <div class="mb-3 text-start">
                    <label class="form-label">Correo Electrónico</label>
                    <input type="email" name="email" class="form-control" placeholder="admin@tickets.com" required>
                </div>
                
                <div class="mb-4 text-start">
                    <label class="form-label">Contraseña</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••" required>
                </div>
                
                <button type="submit" class="btn btn-brand">Ingresar al Sistema</button>
            </form>
            
            <a href="#" class="forgot-link">¿Olvidaste tu contraseña?</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
    <script>
        // CONFIGURACIÓN MEJORADA DE PARTÍCULAS INTERACTIVAS
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
                    "opacity": 0.4, /* Líneas más visibles */
                    "width": 1
                },
                "move": { 
                    "enable": true, 
                    "speed": 2, 
                    "direction": "none", 
                    "random": false, 
                    "straight": false, 
                    "out_mode": "out", 
                    "bounce": false 
                }
            },
            "interactivity": {
                "detect_on": "window", /* Detectar mouse en toda la ventana */
                "events": {
                    "onhover": { 
                        "enable": true, 
                        "mode": "grab" /* EFECTO CLAVE: Conectar líneas al mouse */
                    },
                    "onclick": { 
                        "enable": true, 
                        "mode": "push" 
                    },
                    "resize": true
                },
                "modes": {
                    "grab": { 
                        "distance": 200, /* Distancia de alcance del mouse */
                        "line_linked": { "opacity": 1 } 
                    },
                    "push": { "particles_nb": 4 }
                }
            },
            "retina_detect": true
        });
    </script>
</body>
</html>