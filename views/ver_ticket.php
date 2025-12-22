<?php
// sistema_tickets/views/ver_ticket.php
require '../includes/header.php';
require '../config/db.php';

// 1. Validar que venga un ID en la URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$id_ticket = $_GET['id'];

// 2. Traer toda la información del ticket y del usuario creador
$sql = "SELECT t.*, u.nombre as creador 
        FROM tickets t 
        JOIN usuarios u ON t.usuario_id = u.id 
        WHERE t.id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id_ticket]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

// Si no existe el ticket, volver al dashboard
if (!$ticket) {
    header("Location: dashboard.php?error=no_encontrado");
    exit;
}
?>

<div class="container-fluid mb-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="text-muted small mb-1">Ticket #<?php echo $ticket['id']; ?></div>
            <h2 class="fw-bold text-dark mb-0"><?php echo htmlspecialchars($ticket['titulo']); ?></h2>
        </div>
        <a href="dashboard.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="bi bi-arrow-left me-2"></i>Volver al tablero
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                    <h5 class="fw-bold text-secondary"><i class="bi bi-file-text me-2"></i>Descripción del Problema</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="p-3 bg-light rounded text-dark" style="min-height: 150px; line-height: 1.6;">
                        <?php echo nl2br(htmlspecialchars($ticket['descripcion'])); ?>
                    </div>
                    <div class="mt-3 text-muted small text-end">
                        Creado el: <?php echo date('d/m/Y H:i', strtotime($ticket['fecha_creacion'])); ?>
                    </div>
                </div>
            </div>

            </div>

        <div class="col-lg-4">
            
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-uppercase text-muted small mb-3">Gestión</h6>
                    
                    <div class="mb-4 text-center">
                        <span class="d-block text-muted small mb-1">Estado Actual</span>
                        <?php 
                            $clase_estado = 'primary';
                            if($ticket['estado'] == 'cerrado') $clase_estado = 'success';
                            if($ticket['estado'] == 'abierto') $clase_estado = 'danger'; // Ojo: en tu BD pusiste 'abierto', no 'nuevo'
                        ?>
                        <span class="badge bg-<?php echo $clase_estado; ?> fs-6 px-4 py-2 rounded-pill">
                            <?php echo ucfirst($ticket['estado']); ?>
                        </span>
                    </div>

                    <div class="d-grid gap-2">
                        <?php if($ticket['estado'] != 'cerrado'): ?>
                            <form action="../actions/actualizar_estado.php" method="POST">
                                <input type="hidden" name="id" value="<?php echo $ticket['id']; ?>">
                                <input type="hidden" name="nuevo_estado" value="cerrado">
                                <button type="submit" class="btn btn-success w-100 fw-bold text-white">
                                    <i class="bi bi-check-circle-fill me-2"></i> Marcar como Resuelto
                                </button>
                            </form>
                        <?php else: ?>
                            <form action="../actions/actualizar_estado.php" method="POST">
                                <input type="hidden" name="id" value="<?php echo $ticket['id']; ?>">
                                <input type="hidden" name="nuevo_estado" value="abierto">
                                <button type="submit" class="btn btn-outline-secondary w-100">
                                    <i class="bi bi-arrow-counterclockwise me-2"></i> Reabrir Ticket
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-uppercase text-muted small mb-3">Información de Contacto</h6>
                    
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3">
                            <i class="bi bi-person text-primary me-2"></i>
                            <strong>Solicitante:</strong><br>
                            <span class="ms-4 text-muted"><?php echo htmlspecialchars($ticket['creador']); ?></span>
                        </li>
                        <li class="mb-3">
                            <i class="bi bi-building text-primary me-2"></i>
                            <strong>Departamento:</strong><br>
                            <span class="ms-4 text-muted"><?php echo htmlspecialchars($ticket['departamento']); ?></span>
                        </li>
                        <li class="mb-3">
                            <i class="bi bi-envelope text-primary me-2"></i>
                            <strong>Email:</strong><br>
                            <span class="ms-4 text-muted">
                                <?php echo !empty($ticket['email_contacto']) ? $ticket['email_contacto'] : 'No especificado'; ?>
                            </span>
                        </li>
                        <li class="mb-3">
                            <i class="bi bi-telephone text-primary me-2"></i>
                            <strong>Teléfono:</strong><br>
                            <span class="ms-4 text-muted">
                                <?php echo !empty($ticket['telefono_contacto']) ? $ticket['telefono_contacto'] : 'No especificado'; ?>
                            </span>
                        </li>
                        <li>
                            <i class="bi bi-exclamation-circle text-primary me-2"></i>
                            <strong>Prioridad:</strong><br>
                            <span class="ms-4 badge bg-secondary bg-opacity-10 text-dark border">
                                <?php echo ucfirst($ticket['prioridad']); ?>
                            </span>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>