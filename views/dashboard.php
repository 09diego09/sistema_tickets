<?php 
require '../includes/header.php'; 
require '../config/db.php';

$id_usuario = $_SESSION['usuario_id'];
$rol_usuario = $_SESSION['usuario_rol'];

// 1. CONSULTA INTELIGENTE (Seguridad por Rol + Nombre del Técnico)
// Agregamos: LEFT JOIN usuarios a ON t.agente_id = a.id
// Y seleccionamos: a.nombre as agente
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

// CÁLCULO DE CONTADORES
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

<?php if(isset($_GET['msg']) && $_GET['msg'] == 'ticket_creado'): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert" style="border-radius: 15px;">
        <i class="bi bi-check-circle-fill me-2"></i><strong>¡Excelente!</strong> Ticket creado y asignado automáticamente.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row mb-4">
    <div class="col-md-4"><div class="card border-0 shadow-sm h-100 p-3"><h3 class="fw-bold"><?php echo $total_tickets; ?></h3><small>Total</small></div></div>
    <div class="col-md-4"><div class="card border-0 shadow-sm h-100 p-3"><h3 class="fw-bold"><?php echo $total_pendientes; ?></h3><small>Pendientes</small></div></div>
    <div class="col-md-4"><div class="card border-0 shadow-sm h-100 p-3"><h3 class="fw-bold"><?php echo $total_resueltos; ?></h3><small>Resueltos</small></div></div>
</div>

<div class="card border-0 shadow-sm" style="border-radius: 15px;">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-0">
        <h5 class="mb-0 fw-bold text-dark">Tickets Pendientes</h5>
        <a href="crear_ticket.php" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold"><i class="bi bi-plus-lg"></i> Nuevo Ticket</a>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4"># ID</th>
                    <th>Asunto</th>
                    <th>Asignado a 🛠️</th> <th>Prioridad</th>
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
                            <td><?php echo htmlspecialchars($ticket['titulo']); ?></td>
                            
                            <td>
                                <?php if(!empty($ticket['agente'])): ?>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center me-2" style="width: 25px; height: 25px; font-size: 0.7rem;">
                                            <?php echo strtoupper(substr($ticket['agente'], 0, 1)); ?>
                                        </div>
                                        <span class="small fw-bold text-dark"><?php echo htmlspecialchars($ticket['agente']); ?></span>
                                    </div>
                                <?php else: ?>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border">Sin Asignar</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php 
                                    $p_c = 'secondary';
                                    if($ticket['prioridad']=='alta') $p_c='danger';
                                    if($ticket['prioridad']=='media') $p_c='warning';
                                ?>
                                <span class="badge text-<?php echo $p_c; ?> border border-<?php echo $p_c; ?> rounded-pill px-2"><?php echo ucfirst($ticket['prioridad']); ?></span>
                            </td>

                            <td>
                                <?php 
                                    $e_bg = 'primary';
                                    if($ticket['estado']=='en_proceso') $e_bg='warning';
                                    if($ticket['estado']=='espera') $e_bg='secondary';
                                ?>
                                <span class="badge bg-<?php echo $e_bg; ?> bg-opacity-10 text-<?php echo $e_bg; ?> px-3 py-2 rounded-pill"><?php echo ucfirst(str_replace('_',' ',$ticket['estado'])); ?></span>
                            </td>
                            
                            <td class="text-muted small"><?php echo htmlspecialchars($ticket['creador']); ?></td>
                            <td class="text-end pe-4">
                                <a href="ver_ticket.php?id=<?php echo $ticket['id']; ?>" class="btn btn-sm btn-light border text-primary"><i class="bi bi-eye"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="text-center py-5 text-muted">Todo limpio.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require '../includes/footer.php'; ?>