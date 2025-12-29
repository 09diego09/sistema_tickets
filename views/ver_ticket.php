<?php
// views/ver_ticket.php
require '../includes/header.php';
require '../config/db.php';

// Validar ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$id_ticket = $_GET['id'];

// 1. CONSULTA DEL TICKET (Incluye el email del creador)
$sql = "SELECT t.*, u.nombre as creador, u.email as email_creador 
        FROM tickets t 
        JOIN usuarios u ON t.usuario_id = u.id 
        WHERE t.id = :id";

$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id_ticket]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ticket) {
    header("Location: dashboard.php?error=no_encontrado");
    exit;
}

// 2. SEGURIDAD: Permisos de visualización
$es_staff = ($_SESSION['usuario_rol'] == 'admin' || $_SESSION['usuario_rol'] == 'tecnico');
$es_mi_ticket = ($ticket['usuario_id'] == $_SESSION['usuario_id']);

if (!$es_staff && !$es_mi_ticket) {
    echo "<div class='container mt-5 alert alert-danger'>Acceso denegado.</div>";
    exit;
}

// 3. CARGAR NOTAS INTERNAS (CORREGIDO: Usando tabla notas_tickets)
$notas = [];
if ($es_staff) {
    // AQUI CAMBIAMOS: Usamos la tabla nueva y ordenamos por fecha descendente (lo nuevo primero)
    $sql_notas = "SELECT n.*, u.nombre as autor, u.rol 
                  FROM notas_tickets n 
                  JOIN usuarios u ON n.usuario_id = u.id 
                  WHERE n.ticket_id = :id 
                  ORDER BY n.fecha DESC";
    $stmt_notas = $pdo->prepare($sql_notas);
    $stmt_notas->execute([':id' => $id_ticket]);
    $notas = $stmt_notas->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="container-fluid">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="dashboard.php" class="text-decoration-none text-muted small mb-2 d-block">
                <i class="bi bi-arrow-left"></i> Volver al Dashboard
            </a>
            <h3 class="fw-bold text-dark mb-0">
                Ticket #<?php echo $ticket['id']; ?>: <?php echo htmlspecialchars($ticket['titulo']); ?>
            </h3>
        </div>
        
        <?php 
            $bg = 'secondary';
            if($ticket['estado'] == 'abierto') $bg = 'danger';
            if($ticket['estado'] == 'en_proceso') $bg = 'warning';
            if($ticket['estado'] == 'resuelto') $bg = 'success';
            if($ticket['estado'] == 'cerrado') $bg = 'dark';
        ?>
        <span class="badge bg-<?php echo $bg; ?> fs-6 px-4 py-2 rounded-pill text-uppercase">
            <?php echo str_replace('_', ' ', $ticket['estado']); ?>
        </span>
    </div>

    <div class="row">
        <div class="col-lg-8">
            
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-primary mb-3">Descripción del Problema</h5>
                    <p class="text-muted" style="white-space: pre-wrap;"><?php echo htmlspecialchars($ticket['descripcion']); ?></p>
                    
                    <div class="d-flex mt-4 pt-3 border-top">
                        <small class="text-muted me-4">
                            <i class="bi bi-calendar3 me-1"></i> Creado: <?php echo date('d/m/Y H:i', strtotime($ticket['fecha_creacion'])); ?>
                        </small>
                        <small class="text-muted">
                            <i class="bi bi-tag me-1"></i> Prioridad: <?php echo ucfirst($ticket['prioridad']); ?>
                        </small>
                    </div>
                </div>
            </div>

            <?php if ($es_staff): ?>
                <div class="card border-0 shadow-sm bg-light mb-4" id="seccionNotas" style="border-radius: 15px;">
                    <div class="card-header bg-transparent border-0 pt-4 px-4">
                        <h6 class="fw-bold text-dark mb-0">
                            <i class="bi bi-chat-left-text-fill me-2"></i>Notas Internas 
                            <span class="badge bg-dark ms-2" style="font-size: 0.6rem;">PRIVADO</span>
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        
                        <div class="bg-white p-3 rounded mb-3 shadow-sm" style="max-height: 300px; overflow-y: auto;">
                            <?php if (count($notas) > 0): ?>
                                <?php foreach($notas as $nota): ?>
                                    <div class="d-flex mb-3 border-bottom pb-2">
                                        <div class="me-3">
                                            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 35px; height: 35px; font-size: 0.8rem; font-weight: bold;">
                                                <?php echo strtoupper(substr($nota['autor'], 0, 1)); ?>
                                            </div>
                                        </div>
                                        <div class="w-100">
                                            <div class="d-flex justify-content-between mb-1">
                                                <strong class="small text-dark"><?php echo htmlspecialchars($nota['autor']); ?></strong>
                                                <small class="text-muted" style="font-size: 0.75rem;"><?php echo date('d/m H:i', strtotime($nota['fecha'])); ?></small>
                                            </div>
                                            <p class="mb-0 small text-secondary"><?php echo nl2br(htmlspecialchars($nota['nota'])); ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center text-muted small py-3 opacity-50">
                                    No hay notas internas registradas.
                                </div>
                            <?php endif; ?>
                        </div>

                        <form action="../actions/agregar_nota.php" method="POST">
                            <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
                            <div class="input-group">
                                <input type="text" name="nota" class="form-control" placeholder="Escribe una nota interna..." required>
                                <button class="btn btn-dark" type="submit"><i class="bi bi-send-fill"></i></button>
                            </div>
                        </form>

                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-lg-4">
            
            <?php if ($es_staff): ?>
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-uppercase text-muted small mb-3">Gestión Operativa</h6>
                    
                    <form action="../actions/asignar_ticket.php" method="POST" class="mb-4">
                        <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
                        <label class="form-label small fw-bold text-muted">Asignado a:</label>
                        <div class="input-group">
                            <select name="tecnico_id" class="form-select form-select-sm">
                                <option value="">Seleccionar...</option>
                                <?php 
                                    // Listar técnicos activos
                                    $stmt_tecs = $pdo->query("SELECT id, nombre FROM usuarios WHERE rol = 'tecnico' AND activo = 1");
                                    while($tec = $stmt_tecs->fetch()){
                                        $selected = ($ticket['agente_id'] == $tec['id']) ? 'selected' : '';
                                        echo "<option value='{$tec['id']}' $selected>{$tec['nombre']}</option>";
                                    }
                                ?>
                            </select>
                            <button class="btn btn-outline-primary btn-sm" type="submit">Asignar</button>
                        </div>
                    </form>

                    <hr class="opacity-10">

                    <form action="../actions/actualizar_estado.php" method="POST">
                        <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
                        <label class="form-label small fw-bold text-muted">Estado:</label>
                        <div class="input-group">
                            <select name="estado" class="form-select form-select-sm">
                                <option value="abierto" <?php echo $ticket['estado']=='abierto'?'selected':''; ?>>🔴 Abierto</option>
                                <option value="en_proceso" <?php echo $ticket['estado']=='en_proceso'?'selected':''; ?>>🟡 En Proceso</option>
                                <option value="resuelto" <?php echo $ticket['estado']=='resuelto'?'selected':''; ?>>🟢 Resuelto</option>
                                <option value="cerrado" <?php echo $ticket['estado']=='cerrado'?'selected':''; ?>>⚫ Cerrado</option>
                            </select>
                            <button class="btn btn-primary btn-sm fw-bold" type="submit">Actualizar</button>
                        </div>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-uppercase text-muted small mb-3">SOLICITANTE</h6>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3">
                            <i class="bi bi-person text-primary me-2"></i>
                            <strong>Nombre:</strong>
                            <span class="text-muted ms-1"><?php echo htmlspecialchars($ticket['creador']); ?></span>
                        </li>
                        <li class="mb-3">
                            <i class="bi bi-building text-primary me-2"></i>
                            <strong>Depto:</strong>
                            <span class="text-muted ms-1"><?php echo htmlspecialchars($ticket['departamento']); ?></span>
                        </li>
                        <li>
                            <i class="bi bi-envelope text-primary me-2"></i>
                            <strong>Email:</strong><br>
                            <span class="text-muted ms-4">
                                <?php echo !empty($ticket['email_creador']) ? $ticket['email_creador'] : 'No especificado'; ?>
                            </span>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>