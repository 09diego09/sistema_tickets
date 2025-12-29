<?php
// sistema_tickets/views/mis_tickets.php
require '../includes/header.php';
require '../config/db.php';

// 1. Seguridad básica
if (!isset($_SESSION['usuario_id'])) { header("Location: ../index.php"); exit; }

$id_usuario  = $_SESSION['usuario_id'];
$rol_usuario = $_SESSION['usuario_rol'];

// 2. Determinar qué ver (Lógica simplificada)
// Solo permitimos 'global' si NO es un usuario normal.
$ver_global = (isset($_GET['view']) && $_GET['view'] == 'global' && $rol_usuario != 'usuario');

// 3. Configuración visual según la vista
if ($ver_global) {
    $titulo_pagina = "Mesa de Ayuda (Global)";
    $subtitulo     = "Visión general de todos los tickets.";
    $icono         = "bi-inbox-fill";
} else {
    $titulo_pagina = "Mis Solicitudes";
    $subtitulo     = "Tickets creados por ti o asignados a tu cargo.";
    $icono         = "bi-person-workspace";
}

// 4. Construcción Inteligente del SQL
// Definimos la base de la consulta UNA sola vez
$sql = "SELECT t.*, u.nombre as creador, a.nombre as agente 
        FROM tickets t 
        JOIN usuarios u ON t.usuario_id = u.id 
        LEFT JOIN usuarios a ON t.agente_id = a.id";

$params = [];

// Si NO es global, aplicamos el filtro de dueño/agente
if (!$ver_global) {
    $sql .= " WHERE t.usuario_id = :uid OR t.agente_id = :aid";
    $params = [':uid' => $id_usuario, ':aid' => $id_usuario];
}

// Ordenamos siempre igual
$sql .= " ORDER BY t.fecha_creacion DESC";

// 5. Ejecución única
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$lista_tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-0">
                <i class="bi <?php echo $icono; ?> text-primary me-2"></i><?php echo $titulo_pagina; ?>
            </h3>
            <p class="text-muted small mb-0 ms-1"><?php echo $subtitulo; ?></p>
        </div>
        <a href="crear_ticket.php" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
            <i class="bi bi-plus-lg"></i> Nuevo Ticket
        </a>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4"># ID</th>
                            <th>Asunto</th>
                            <th>Solicitante</th>
                            <th>Asignado a</th>
                            <th>Fecha</th>
                            <th>Prioridad</th>
                            <th>Estado</th>
                            <th class="text-end pe-4">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($lista_tickets) > 0): ?>
                            <?php foreach($lista_tickets as $t): ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-muted">#<?php echo $t['id']; ?></td>
                                    <td>
                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($t['titulo']); ?></div>
                                        <div class="small text-muted">
                                            <i class="bi bi-building me-1"></i><?php echo htmlspecialchars($t['departamento']); ?>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($t['creador']); ?></td>
                                    
                                    <td>
                                        <?php if(!empty($t['agente'])): ?>
                                            <span class="badge bg-info bg-opacity-10 text-info border border-info rounded-pill">
                                                <i class="bi bi-person-check-fill me-1"></i><?php echo htmlspecialchars($t['agente']); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary rounded-pill">
                                                Sin Asignar
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="small text-muted"><?php echo date('d/m/Y', strtotime($t['fecha_creacion'])); ?></td>
                                    
                                    <td>
                                        <?php 
                                            $prio = $t['prioridad'];
                                            $classP = ($prio=='alta') ? 'danger' : (($prio=='media') ? 'warning' : 'success');
                                        ?>
                                        <span class="badge text-<?php echo $classP; ?> border border-<?php echo $classP; ?> rounded-pill px-2">
                                            <?php echo ucfirst($prio); ?>
                                        </span>
                                    </td>
                                    
                                    <td>
                                        <?php 
                                            $est = $t['estado'];
                                            $classE = ($est=='abierto') ? 'danger' : (($est=='en_proceso') ? 'warning' : 'success');
                                            $iconE  = ($est=='cerrado') ? 'bi-check-circle' : 'bi-circle-fill';
                                        ?>
                                        <span class="badge bg-<?php echo $classE; ?> bg-opacity-10 text-<?php echo $classE; ?> rounded-pill px-3">
                                            <i class="bi <?php echo $iconE; ?> me-1 small"></i>
                                            <?php echo ucfirst(str_replace('_',' ',$est)); ?>
                                        </span>
                                    </td>

                                    <td class="text-end pe-4">
                                        <a href="ver_ticket.php?id=<?php echo $t['id']; ?>" class="btn btn-sm btn-light border text-primary shadow-sm" title="Ver Detalles">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <div class="mb-2"><i class="bi bi-inbox fs-1 opacity-25"></i></div>
                                    No se encontraron tickets.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php require '../includes/footer.php'; ?>