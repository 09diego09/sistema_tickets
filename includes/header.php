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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        /* ESTILOS GLOBALES */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
        }

        /* SIDEBAR */
        .sidebar {
            width: 260px;
            min-height: 100vh;
            background: linear-gradient(180deg, #0071bc 0%, #29abe2 100%);
            color: white;
            position: fixed;
            z-index: 100;
            transition: all 0.3s;
        }

        .sidebar-header {
            padding: 25px 20px;
            background: rgba(0, 0, 0, 0.1);
            font-size: 1.4rem;
            font-weight: bold;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.85);
            padding: 12px 20px;
            font-size: 0.95rem;
            border-left: 4px solid transparent;
            transition: all 0.2s;
            display: flex;
            align-items: center;
        }

        .nav-link:hover {
            color: white;
            background: rgba(255, 255, 255, 0.1);
            border-left-color: #fff;
        }

        .nav-link.active {
            color: #0071bc;
            background: white;
            border-left-color: #29abe2;
            font-weight: 600;
        }

        .nav-link i {
            margin-right: 10px;
            font-size: 1.1rem;
        }

        /* DROPDOWN DEL SIDEBAR */
        .collapse.show {
            background: rgba(0, 0, 0, 0.1);
        }
        .nav-sub-link {
            padding-left: 50px !important;
            font-size: 0.9rem;
            color: rgba(255,255,255,0.7) !important;
        }
        .nav-sub-link:hover {
            color: white !important;
        }
        
        .dropdown-toggle::after {
            margin-left: auto;
        }

        .content-wrapper {
            margin-left: 260px;
            padding: 30px;
            min-height: 100vh;
        }

        /* PERFIL */
        .user-panel {
            padding: 20px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 10px;
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
        }
    </style>
</head>
<body>

    <nav class="sidebar">
        <div class="sidebar-header">
            <i class="bi bi-hdd-network"></i> HelpDesk
        </div>

        <div class="user-panel">
            <div class="user-avatar"><i class="bi bi-person-fill"></i></div>
            <div class="user-info">
                <div class="fw-bold small"><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></div>
                <div class="small opacity-75" style="font-size: 0.75rem;">
                    <?php echo ucfirst($rol); ?>
                </div>
            </div>
        </div>

        <ul class="nav flex-column mt-2">
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
                <li class="nav-item mt-3">
                    <div class="text-white opacity-50 small px-3 mb-1 text-uppercase fw-bold" style="font-size: 0.7rem;">
                        Técnico
                    </div>
                </li>
                <li class="nav-item">
                    <a href="../views/mis_tickets.php?view=global" class="nav-link">
                        <i class="bi bi-inbox-fill"></i> Tickets Globales
                    </a>
                </li>
            <?php endif; ?>

            <?php if ($rol === 'admin'): ?>
                <li class="nav-item mt-3">
                    <div class="text-white opacity-50 small px-3 mb-1 text-uppercase fw-bold" style="font-size: 0.7rem;">
                        Administración
                    </div>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link dropdown-toggle" href="#submenuAdmin" data-bs-toggle="collapse" role="button" aria-expanded="false">
                        <i class="bi bi-gear-fill"></i> Gestión
                    </a>
                    <div class="collapse" id="submenuAdmin">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a href="../views/admin_usuarios.php" class="nav-link nav-sub-link">
                                    <i class="bi bi-people"></i> Usuarios
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link nav-sub-link">
                                    <i class="bi bi-bar-chart"></i> Estadísticas
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>
            
            <li class="nav-item mt-5">
                <a href="../actions/logout.php" class="nav-link text-danger-emphasis" style="opacity: 0.9;">
                    <i class="bi bi-box-arrow-left"></i> Cerrar Sesión
                </a>
            </li>
        </ul>
    </nav>

    <div class="content-wrapper">