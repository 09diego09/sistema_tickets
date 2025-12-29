<?php 
// views/dashboard.php
require '../includes/header.php'; 
require '../config/db.php';

$id_usuario = $_SESSION['usuario_id'];
$rol_usuario = $_SESSION['usuario_rol'];

// 1. CONSULTA SQL (Mantenemos tu lógica original que funciona bien)
if ($rol_usuario == 'admin' || $rol_usuario == 'tecnico') {
    $sql = "SELECT t.*, u.nombre as creador, a.nombre as agente 
            FROM tickets t 
            JOIN usuarios u ON t.usuario_id = u.id 
            LEFT JOIN usuarios a ON t.agente_id = a.id
            ORDER BY t.fecha_creacion DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
} else {
    $sql = "SELECT t.*, u.nombre as creador, a.nombre as agente 
            FROM tickets t 
            JOIN usuarios u ON t.usuario_id = u.id 
            LEFT JOIN usuarios a ON t.agente_id = a.id
            WHERE t.usuario_id = :id
            ORDER BY t.fecha_creacion DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id_usuario]);
}

$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 2. CÁLCULO DE KPIS
$total_tickets = count($tickets);
$total_pendientes = 0;
$total_resueltos = 0;

foreach($tickets as $t) {
    if($t['estado'] == 'cerrado') {
        $total_resueltos++;
    } else {
        $total_pendientes++;
    }
}
?>

<div class="container-fluid">

    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'ticket_creado'): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert" style="border-radius: 15px;">
            <i class="bi bi-check-circle-fill me-2"></i><strong>¡Excelente!</strong> Ticket creado correctamente.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="mb-4">
        <h3 class="fw-bold text-dark mb-0"><i class="bi bi-speedometer2 text-primary me-2"></i>Panel de Control</h3>
        <p class="text-muted small mb-0 ms-1">Resumen de actividad y pendientes.</p>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 p-3" style="border-radius: 15px;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="fw-bold mb-0 text-dark"><?php echo $total_tickets; ?></h3>
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.75rem;">Total</small>
                    </div>
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                        <i class="bi bi-folder2-open fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 p-3" style="border-radius: 15px;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="fw-bold mb-0 text-warning"><?php echo $total_pendientes; ?></h3>
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.75rem;">Pendientes</small>
                    </div>
                    <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle">
                        <i class="bi bi-hourglass-split fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 p-3" style="border-radius: 15px;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="fw-bold mb-0 text-success"><?php echo $total_resueltos; ?></h3>
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.75rem;">Resueltos</small>
                    </div>
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle">
                        <i class="bi bi-check-circle fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
            <h5 class="mb-0 fw-bold text-secondary"><i class="bi bi-list-task me-2"></i>Tickets Pendientes</h5>
            <a href="crear_ticket.php" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                <i class="bi bi-plus-lg me-1"></i>Nuevo Ticket
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4"># ID</th>
                            <th>Asunto</th>
                            <th>Asignado a</th>
                            <th>Prioridad</th>
                            <th>Estado</th>
                            <th>Creado por</th>
                            <th class="text-end pe-4">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($total_pendientes > 0): ?>
                            <?php foreach($tickets as $ticket): ?>
                                <?php if($ticket['estado'] == 'cerrado') continue; ?>
                                
                                <tr>
                                    <td class="ps-4 fw-bold text-muted">#<?php echo $ticket['id']; ?></td>
                                    
                                    <td>
                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($ticket['titulo']); ?></div>
                                        <div class="small text-muted">
                                            <i class="bi bi-building me-1"></i><?php echo htmlspecialchars($ticket['departamento'] ?? 'General'); ?>
                                        </div>
                                    </td>
                                    
                                    <td>
                                        <?php if(!empty($ticket['agente'])): ?>
                                            <span class="badge bg-info bg-opacity-10 text-info border border-info rounded-pill">
                                                <i class="bi bi-person-check-fill me-1"></i><?php echo htmlspecialchars($ticket['agente']); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary rounded-pill">
                                                Sin Asignar
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?php 
                                            $prio = $ticket['prioridad'];
                                            $classP = ($prio=='alta') ? 'danger' : (($prio=='media') ? 'warning' : 'success');
                                        ?>
                                        <span class="badge text-<?php echo $classP; ?> border border-<?php echo $classP; ?> rounded-pill px-2">
                                            <?php echo ucfirst($prio); ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?php 
                                            $est = $ticket['estado'];
                                            // En dashboard casi siempre será rojo (abierto) o amarillo (proceso)
                                            $classE = ($est=='abierto') ? 'danger' : 'warning';
                                            $iconE  = ($est=='abierto') ? 'bi-exclamation-circle' : 'bi-arrow-repeat';
                                        ?>
                                        <span class="badge bg-<?php echo $classE; ?> bg-opacity-10 text-<?php echo $classE; ?> rounded-pill px-3">
                                            <i class="bi <?php echo $iconE; ?> me-1 small"></i>
                                            <?php echo ucfirst(str_replace('_',' ',$est)); ?>
                                        </span>
                                    </td>
                                    
                                    <td class="text-muted small"><?php echo htmlspecialchars($ticket['creador']); ?></td>
                                    
                                    <td class="text-end pe-4">
                                        <a href="ver_ticket.php?id=<?php echo $ticket['id']; ?>" class="btn btn-sm btn-light border text-primary shadow-sm" title="Ver Detalles">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <div class="mb-2"><i class="bi bi-emoji-smile fs-1 opacity-25"></i></div>
                                    ¡Todo limpio! No tienes tickets pendientes.
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