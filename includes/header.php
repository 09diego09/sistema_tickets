<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - HelpDesk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        /* ESTÉTICA GLOBAL - TIPO DAC CONTROLS */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9; /* Gris muy suave para el fondo de contenido */
        }

        /* BARRA LATERAL (SIDEBAR) */
        .sidebar {
            width: 260px;
            min-height: 100vh;
            /* El mismo degradado del Login para mantener consistencia */
            background: linear-gradient(180deg, #0071bc 0%, #29abe2 100%);
            color: white;
            position: fixed; /* Fija a la izquierda */
            transition: all 0.3s;
            z-index: 100;
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar-header {
            padding: 25px 20px;
            background: rgba(0, 0, 0, 0.1); /* Un poco más oscuro para el logo */
            font-size: 1.4rem;
            font-weight: bold;
            letter-spacing: 1px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        /* ENLACES DEL MENÚ */
        .nav-link {
            color: rgba(255, 255, 255, 0.85);
            padding: 15px 20px;
            font-size: 1rem;
            border-left: 4px solid transparent; /* Para el efecto hover */
            transition: all 0.2s;
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

        /* ÁREA DE CONTENIDO PRINCIPAL */
        .content-wrapper {
            margin-left: 260px; /* Mismo ancho que la sidebar */
            padding: 30px;
            min-height: 100vh;
        }

        /* PERFIL DE USUARIO EN SIDEBAR */
        .user-panel {
            padding: 20px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 10px;
        }
        .user-avatar {
            width: 45px;
            height: 45px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 1.2rem;
        }
    </style>
</head>
<body>

    <nav class="sidebar">
        <div class="sidebar-header">
            <i class="bi bi-hdd-network"></i> Dac-Controls
        </div>

        <div class="user-panel">
            <div class="user-avatar">
                <i class="bi bi-person-fill"></i>
            </div>
            <div class="user-info">
                <div style="font-weight: 600;"><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></div>
                <div style="font-size: 0.8rem; opacity: 0.7;">Rol: <?php echo ucfirst($_SESSION['usuario_rol']); ?></div>
            </div>
        </div>

        <ul class="nav flex-column mt-2">
            <li class="nav-item">
                <a href="../views/dashboard.php" class="nav-link active">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="../views/crear_ticket.php" class="nav-link">
                    <i class="bi bi-plus-circle"></i> Nuevo Ticket
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-archive"></i> Historial
                </a>
            </li>
            
            <li class="nav-item mt-5">
                <a href="../actions/logout.php" class="nav-link text-danger-emphasis" style="opacity: 0.9;">
                    <i class="bi bi-box-arrow-left"></i> Cerrar Sesión
                </a>
            </li>
        </ul>
    </nav>

    <div class="content-wrapper">