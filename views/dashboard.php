<?php 
require '../includes/header.php'; 
require '../config/db.php';

// 1. CONSULTA DE TICKETS
$sql = "SELECT t.*, u.nombre as creador 
        FROM tickets t 
        JOIN usuarios u ON t.usuario_id = u.id 
        ORDER BY t.fecha_creacion DESC";
$stmt = $pdo->query($sql);
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<div class="card mb-4 border-0 shadow-sm overflow-hidden" style="border-radius: 15px;">
    <div class="card-body position-relative p-5 text-white" style="background: linear-gradient(135deg, #0071bc 0%, #29abe2 100%);">
        <div id="particles-banner" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"></div>
        <div class="position-relative z-1">
            <h2 class="fw-bold">Hola, <?php echo $_SESSION['usuario_nombre']; ?> 👋</h2>
            <?php if(isset($_GET['msg']) && $_GET['msg'] == 'ticket_creado'): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert" style="border-radius: 15px;">
        <i class="bi bi-check-circle-fill me-2"></i>
        <strong>¡Excelente!</strong> Tu ticket ha sido creado correctamente y ya está en la lista.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>


            <p class="mb-0 opacity-75">Bienvenido al panel de gestión de tickets.</p>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                    <i class="bi bi-ticket-detailed text-primary fs-4"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Total Tickets</h6>
                    <h3 class="fw-bold mb-0"><?php echo count($tickets); ?></h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                    <i class="bi bi-clock-history text-warning fs-4"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Pendientes</h6>
                    <h3 class="fw-bold mb-0">-</h3> 
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                    <i class="bi bi-check-circle text-success fs-4"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Resueltos</h6>
                    <h3 class="fw-bold mb-0">-</h3> 
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold text-secondary">Tickets Recientes</h5>
        <a href="crear_ticket.php" class="btn btn-primary btn-sm rounded-pill px-3">
            <i class="bi bi-plus-lg"></i> Nuevo Ticket
        </a>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4"># ID</th>
                    <th>Asunto</th>
                    <th>Departamento</th>
                    <th>Prioridad</th>
                    <th>Estado</th>
                    <th>Creado por</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($tickets) > 0): ?>
                    <?php foreach($tickets as $ticket): ?>
                        <tr>
                            <td class="ps-4 fw-bold">#<?php echo $ticket['id']; ?></td>
                            <td><?php echo htmlspecialchars($ticket['titulo']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['departamento']); ?></td>
                            
                            <td>
                                <?php 
                                    $color_prio = 'secondary';
                                    if($ticket['prioridad'] == 'alta') $color_prio = 'danger';
                                    if($ticket['prioridad'] == 'media') $color_prio = 'warning';
                                    if($ticket['prioridad'] == 'baja') $color_prio = 'success';
                                ?>
                                <span class="badge bg-<?php echo $color_prio; ?> bg-opacity-10 text-<?php echo $color_prio; ?> px-3 py-2 rounded-pill">
                                    <?php echo ucfirst($ticket['prioridad']); ?>
                                </span>
                            </td>

                            <td><span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill"><?php echo ucfirst($ticket['estado']); ?></span></td>
                            <td class="text-muted small"><?php echo $ticket['creador']; ?></td>
                            <td>
                                <a href="ver_ticket.php?id=<?php echo $ticket['id']; ?>" class="btn btn-sm btn-light border" title="Ver detalle">
    <i class="bi bi-eye text-primary"></i>
</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-3 opacity-50"></i>
                            No hay tickets registrados aún.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
<script>
    particlesJS("particles-banner", {
        "particles": {
            "number": { "value": 40 },
            "size": { "value": 2 },
            "color": { "value": "#ffffff" },
            "line_linked": { "enable": true, "color": "#ffffff", "opacity": 0.3 },
            "move": { "speed": 1 }
        },
        "interactivity": { "events": { "onhover": { "enable": false } } },
        "retina_detect": true
    });
</script>

<?php require '../includes/footer.php';?>