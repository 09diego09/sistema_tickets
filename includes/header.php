<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si no hay sesión, fuera de aquí
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit;
}

// Recuperamos el rol para usarlo en el HTML
$rol = $_SESSION['usuario_rol'] ?? 'usuario'; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HelpDesk - Panel</title>
    
    <link rel="manifest" href="../manifest.json">
    <meta name="theme-color" content="#0072ff">
    <link rel="apple-touch-icon" href="../assets/icon-192.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
<style>
    /* --- FUENTES Y GENERAL --- */
    body {
        font-family: 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
        background-color: #f0f8ff; 
        color: #334e68; 
        overflow-x: hidden; /* Evita scroll horizontal indeseado */
    }

    /* --- BARRA LATERAL (SIDEBAR) --- */
    .sidebar {
        width: 260px;
        min-height: 100vh;
        background: linear-gradient(180deg, #00c6ff 0%, #0072ff 100%);
        color: white;
        position: fixed;
        z-index: 1050; /* Por encima de todo */
        box-shadow: 4px 0 15px rgba(0, 198, 255, 0.2);
        transition: all 0.3s ease; /* Animación suave al entrar/salir */
    }

    .sidebar-header {
        padding: 25px 20px;
        background: rgba(255, 255, 255, 0.1);
        font-size: 1.4rem;
        font-weight: bold;
        letter-spacing: 1px;
    }

    /* --- ENLACES DEL MENÚ --- */
    .nav-link {
        color: rgba(255, 255, 255, 0.8);
        padding: 15px 25px;
        font-weight: 500;
        transition: all 0.3s ease;
        border-right: 4px solid transparent;
        border-left: none;
    }

    .nav-link:hover {
        color: white;
        background: rgba(255, 255, 255, 0.15);
        padding-left: 30px; 
    }

    .nav-link.active {
        color: #0072ff; 
        background: white; 
        border-right-color: #00c6ff;
        font-weight: 700;
        box-shadow: -5px 0 15px rgba(0,0,0,0.05); 
    }

    .nav-link i { margin-right: 12px; font-size: 1.2rem; }

    /* --- CONTENIDO PRINCIPAL --- */
    .content-wrapper {
        margin-left: 260px; /* Espacio para el sidebar en Desktop */
        padding: 40px;
        background: transparent !important;
        transition: margin-left 0.3s ease;
    }

    /* --- ESTILOS RESPONSIVE (MÓVIL) --- */
    @media (max-width: 768px) {
        .sidebar {
            margin-left: -260px; /* Ocultar sidebar a la izquierda */
        }
        .sidebar.active {
            margin-left: 0; /* Mostrar al activar */
        }
        .content-wrapper {
            margin-left: 0 !important; /* Contenido ocupa todo el ancho */
            padding: 20px; /* Menos padding en celular */
        }
        
        /* Botón Hamburguesa */
        .mobile-nav-toggle {
            display: flex !important; /* Mostrar solo en móvil */
        }
        
        /* Oscurecer fondo al abrir menú */
        .overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1040;
        }
        .overlay.active { display: block; }
    }

    /* Botón Hamburguesa Flotante (Oculto en PC) */
    .mobile-nav-toggle {
        display: none;
        position: fixed;
        top: 15px;
        right: 15px;
        z-index: 1100;
        background: #0072ff;
        color: white;
        border: none;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        box-shadow: 0 4px 10px rgba(0, 114, 255, 0.4);
        cursor: pointer;
    }

    /* --- TARJETAS (CARDS) --- */
    .card {
        border: 1px solid rgba(0, 0, 0, 0.05); 
        border-radius: 20px; 
        box-shadow: 0 6px 20px rgba(0, 198, 255, 0.08) !important; 
        transition: box-shadow 0.3s ease, border-color 0.3s ease;
    }
    .row .card:hover {
        border-color: rgba(0, 198, 255, 0.5);
        box-shadow: 0 0 25px rgba(0, 198, 255, 0.3) !important;
    }
    
    .btn-primary {
        background-color: #00c6ff;
        border-color: #00c6ff;
        box-shadow: 0 4px 12px rgba(0, 198, 255, 0.3);
        font-weight: 600;
        padding: 10px 25px;
    }
    .btn-primary:hover {
        background-color: #00aadd; 
        border-color: #00aadd;
    }

    /* User Panel */
    .user-panel {
        background: rgba(0, 0, 0, 0.1); 
        padding: 20px 25px;
        margin: 0;
        display: flex; 
        align-items: center;
        gap: 15px; 
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    .user-avatar {
        background: rgba(255, 255, 255, 0.2); 
        color: white; 
        width: 45px; height: 45px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem;
        backdrop-filter: blur(5px);
    }
    .user-panel.hover-effect { transition: background 0.3s; }
    .user-panel.hover-effect:hover { background: rgba(255, 255, 255, 0.15); cursor: pointer; }

    #particles-js {
        position: fixed; 
        width: 100%; height: 100%;
        top: 0; left: 0;
        z-index: -1; 
    }
</style>
</head>
<body>

    <div id="particles-js"></div>
    
    <button class="mobile-nav-toggle" onclick="toggleSidebar()">
        <i class="bi bi-list"></i>
    </button>
    
    <div class="overlay" onclick="toggleSidebar()"></div>

    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header text-center">
            <img src="../assets/logo_blanco.png" alt="DAC Controls" style="max-width: 80%; height: auto; opacity: 0.95;">
        </div>

        <a href="../views/mi_perfil.php" class="text-decoration-none">
            <div class="user-panel hover-effect">
                <div class="user-avatar">
                    <i class="bi bi-person-fill"></i>
                </div>
                <div class="user-info">
                    <div class="fw-bold text-white" style="line-height: 1.2;"><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></div>
                    <div class="small text-white opacity-75 text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                        <?php echo ucfirst($rol); ?>
                    </div>
                </div>
            </div>
        </a>

        <ul class="nav flex-column mt-3">
            <li class="nav-item">
                <a href="../views/dashboard.php" class="nav-link">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="../views/crear_ticket.php" class="nav-link">
                    <i class="bi bi-plus-circle"></i> Nuevo Ticket
                </a>
            </li>
            <li class="nav-item">
                <a href="../views/mis_tickets.php?view=personal" class="nav-link">
                    <i class="bi bi-person-workspace"></i> Mis Solicitudes
                </a>
            </li>

            <?php if ($rol === 'admin' || $rol === 'tecnico'): ?>
                <li class="nav-item mt-4 mb-2">
                    <div class="text-white opacity-50 small px-4 text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 1px;">
                        Zona Técnica
                    </div>
                </li>
                <li class="nav-item">
                    <a href="../views/mis_tickets.php?view=global" class="nav-link">
                        <i class="bi bi-inbox-fill"></i> Tickets Globales
                    </a>
                </li>
            <?php endif; ?>

            <?php if ($rol === 'admin'): ?>
                <li class="nav-item mt-4 mb-2">
                    <div class="text-white opacity-50 small px-4 text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 1px;">
                        Administración
                    </div>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link dropdown-toggle" href="#submenuAdmin" data-bs-toggle="collapse" role="button" aria-expanded="false">
                        <i class="bi bi-gear-fill"></i> Gestión
                    </a>
                    <div class="collapse" id="submenuAdmin">
                        <ul class="nav flex-column ps-3 bg-black bg-opacity-10">
                            <li class="nav-item">
                                <a href="../views/admin_usuarios.php" class="nav-link nav-sub-link py-2" style="font-size: 0.9rem;">
                                    <i class="bi bi-people"></i> Usuarios
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="../views/estadisticas.php" class="nav-link nav-sub-link py-2" style="font-size: 0.9rem;">
                                    <i class="bi bi-bar-chart"></i> Estadísticas
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>
            
            <li class="nav-item mt-5 pt-3 border-top border-white border-opacity-10">
                <a href="../actions/logout.php" class="nav-link text-white-50 hover-danger">
                    <i class="bi bi-box-arrow-left"></i> Cerrar Sesión
                </a>
            </li>
        </ul>
    </nav>

    <div class="content-wrapper">

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
            document.querySelector('.overlay').classList.toggle('active');
        }
    </script>