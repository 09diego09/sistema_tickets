<?php
// views/ver_ticket.php
require '../includes/header.php';
require '../config/db.php';

// 1. Validar que venga un ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$id_ticket = $_GET['id'];

// 2. CONSULTA DEL TICKET (Datos principales)
$sql = "SELECT t.*, u.nombre as creador, u.email as email_creador 
        FROM tickets t 
        JOIN usuarios u ON t.usuario_id = u.id 
        WHERE t.id = :id";

$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id_ticket]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

// Si no existe, volver
if (!$ticket) {
    header("Location: dashboard.php?error=no_encontrado");
    exit;
}

// 3. SEGURIDAD: Permisos de acceso
// Staff = Admin o Técnico
$es_staff     = ($_SESSION['usuario_rol'] == 'admin' || $_SESSION['usuario_rol'] == 'tecnico');
// Dueño = El usuario que creó el ticket
$es_mi_ticket = ($ticket['usuario_id'] == $_SESSION['usuario_id']);

if (!$es_staff && !$es_mi_ticket) {
    echo "<div class='container mt-5 alert alert-danger shadow-sm border-0'>⛔ Acceso denegado. No tienes permisos para ver este ticket.</div>";
    require '../includes/footer.php';
    exit;
}

// 4. CHECKLIST (Sub-tareas dentro del ticket)
$stmtCheck = $pdo->prepare("SELECT * FROM ticket_checklist WHERE ticket_id = :id ORDER BY id ASC");
$stmtCheck->execute([':id' => $id_ticket]); 
$checklist = $stmtCheck->fetchAll(PDO::FETCH_ASSOC);

// Calcular progreso del checklist
$total_tareas = count($checklist);
$completadas  = 0;
foreach($checklist as $c) { if($c['completado']) $completadas++; }
$porcentaje   = ($total_tareas > 0) ? round(($completadas / $total_tareas) * 100) : 0;

// 5. CARGAR NOTAS INTERNAS (Solo para staff)
$notas = [];
if ($es_staff) {
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

<div class="container-fluid pb-5">
    
    <div class="mb-4">
        <a href="dashboard.php" class="btn btn-light btn-sm shadow-sm text-primary fw-bold mb-3 rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Volver al Dashboard
        </a>
        
        <div class="d-flex justify-content-between align-items-start flex-wrap">
            <div>
                <h3 class="fw-bold text-dark mb-1">
                    <span class="text-muted opacity-50 me-2">#<?php echo $ticket['id']; ?></span><?php echo htmlspecialchars($ticket['titulo']); ?>
                </h3>
                <p class="text-muted mb-0">
                    <i class="bi bi-building me-1"></i><?php echo htmlspecialchars($ticket['departamento']); ?>
                </p>
            </div>

            <div class="mt-2 mt-md-0">
                <?php 
                    $est = $ticket['estado'];
                    $classE = ($est=='abierto') ? 'danger' : (($est=='en_proceso') ? 'warning' : (($est=='cerrado') ? 'secondary' : 'success'));
                    $iconE  = ($est=='abierto') ? 'bi-exclamation-circle' : (($est=='cerrado') ? 'bi-check-all' : 'bi-arrow-repeat');
                    $labelE = ucfirst(str_replace('_', ' ', $est));
                ?>
                <span class="badge bg-<?php echo $classE; ?> bg-opacity-10 text-<?php echo $classE; ?> fs-6 px-4 py-2 rounded-pill shadow-sm border border-<?php echo $classE; ?>">
                    <i class="bi <?php echo $iconE; ?> me-2"></i><?php echo $labelE; ?>
                </span>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px; overflow: hidden;">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold text-primary mb-0"><i class="bi bi-file-text me-2"></i>Detalle de la Solicitud</h6>
                </div>
                <div class="card-body p-4">
                    <p class="text-secondary" style="white-space: pre-wrap; line-height: 1.6;"><?php echo htmlspecialchars($ticket['descripcion']); ?></p>
                    
                    <?php if(!empty($ticket['adjunto'])): ?>
                        <div class="mt-4 p-3 bg-light border rounded shadow-sm">
                            <h6 class="fw-bold text-dark small mb-2"><i class="bi bi-paperclip me-1"></i>Archivo Adjunto</h6>
                            <?php 
                                $ext = strtolower(pathinfo($ticket['adjunto'], PATHINFO_EXTENSION));
                                $ruta_img = "../assets/uploads/" . $ticket['adjunto'];
                            ?>
                            <?php if(in_array($ext, ['jpg', 'jpeg', 'png'])): ?>
                                <a href="<?php echo $ruta_img; ?>" target="_blank">
                                    <img src="<?php echo $ruta_img; ?>" class="img-fluid rounded border bg-white" style="max-height: 300px;" alt="Evidencia">
                                </a>
                                <div class="mt-2 small text-muted"><i class="bi bi-zoom-in"></i> Clic para ampliar</div>
                            <?php else: ?>
                                <a href="<?php echo $ruta_img; ?>" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill">
                                    <i class="bi bi-file-earmark-arrow-down-fill me-2"></i>Ver Documento (<?php echo strtoupper($ext); ?>)
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex flex-wrap gap-3 mt-4 pt-3 border-top">
                        <div class="badge bg-light text-muted border px-3 py-2 rounded-pill fw-normal">
                            <i class="bi bi-calendar3 me-2 text-primary"></i>Creado: <strong><?php echo date('d/m/Y H:i', strtotime($ticket['fecha_creacion'])); ?></strong>
                        </div>
                        <?php 
                            $prio = $ticket['prioridad'];
                            $classP = ($prio=='alta') ? 'danger' : (($prio=='media') ? 'warning' : 'success');
                        ?>
                        <div class="badge bg-<?php echo $classP; ?> bg-opacity-10 text-<?php echo $classP; ?> border border-<?php echo $classP; ?> px-3 py-2 rounded-pill fw-bold">
                            <i class="bi bi-tag-fill me-2"></i>Prioridad <?php echo ucfirst($prio); ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($es_staff): ?>
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-list-check me-2 text-primary"></i>Lista de Tareas (Interno)</h6>
                    <span class="badge bg-light text-primary border" id="progress-text"><?php echo $porcentaje; ?>% Completado</span>
                </div>
                
                <div class="card-body px-4">
                    <div class="progress mb-3" style="height: 6px;">
                        <div class="progress-bar bg-success" id="progress-bar" role="progressbar" style="width: <?php echo $porcentaje; ?>%"></div>
                    </div>

                    <div id="lista-checklist" class="mb-3">
                        <?php foreach($checklist as $item): ?>
                            <div class="d-flex align-items-center mb-2 p-2 rounded hover-item" id="item-<?php echo $item['id']; ?>" style="background-color: #f8f9fa;">
                                <div class="form-check mb-0">
                                    <input class="form-check-input check-tarea" type="checkbox" 
                                           value="<?php echo $item['id']; ?>" 
                                           <?php echo $item['completado'] ? 'checked' : ''; ?>
                                           style="cursor: pointer; transform: scale(1.2);">
                                </div>
                                <span class="ms-3 flex-grow-1 <?php echo $item['completado'] ? 'text-decoration-line-through text-muted' : ''; ?>" id="text-<?php echo $item['id']; ?>">
                                    <?php echo htmlspecialchars($item['titulo_tarea']); ?>
                                </span>
                                <button class="btn btn-sm text-danger btn-eliminar-tarea" data-id="<?php echo $item['id']; ?>">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if($ticket['estado'] != 'resuelto' && $ticket['estado'] != 'cerrado'): ?>
                    <div class="input-group">
                        <input type="text" id="nuevo-item" class="form-control" placeholder="Agregar nueva tarea técnica..." onkeypress="if(event.key === 'Enter') agregarTarea()">
                        <button class="btn btn-primary" type="button" onclick="agregarTarea()"><i class="bi bi-plus-lg"></i></button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($es_staff): ?>
                <div class="card border-0 shadow-sm mb-4 bg-light" id="seccionNotas" style="border-radius: 15px;">
                    <div class="card-header bg-warning bg-opacity-10 border-0 pt-3 px-4 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold text-dark mb-0">
                            <i class="bi bi-chat-square-text-fill me-2 text-warning"></i>Notas Internas 
                        </h6>
                        <span class="badge bg-warning text-dark shadow-sm"><i class="bi bi-lock-fill me-1"></i>Privado</span>
                    </div>
                    <div class="card-body p-4">
                        
                        <div class="mb-4 pe-2" style="max-height: 400px; overflow-y: auto;">
                            <?php if (count($notas) > 0): ?>
                                <?php foreach($notas as $nota): ?>
                                    <div class="d-flex mb-3">
                                        <div class="me-3 flex-shrink-0">
                                            <div class="rounded-circle bg-white border shadow-sm text-primary d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">
                                                <?php echo strtoupper(substr($nota['autor'], 0, 1)); ?>
                                            </div>
                                        </div>
                                        <div class="bg-white p-3 rounded-3 shadow-sm border w-100 position-relative">
                                            <div class="d-flex justify-content-between mb-1">
                                                <strong class="text-dark small"><?php echo htmlspecialchars($nota['autor']); ?></strong>
                                                <small class="text-muted" style="font-size: 0.7rem;"><?php echo date('d/m H:i', strtotime($nota['fecha'])); ?></small>
                                            </div>
                                            <p class="mb-0 text-secondary small" style="white-space: pre-wrap;"><?php echo nl2br(htmlspecialchars($nota['nota'])); ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center py-4 opacity-50">
                                    <i class="bi bi-chat-square-dots fs-1 d-block mb-2"></i>
                                    <small>No hay notas internas aún.</small>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="bg-white p-2 rounded-pill shadow-sm border">
                            <form action="../actions/agregar_nota.php" method="POST" class="d-flex">
                                <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
                                <input type="text" name="nota" class="form-control border-0 shadow-none bg-transparent ps-3" placeholder="Escribir una nota para el equipo..." required>
                                <button class="btn btn-primary rounded-circle shadow-sm" type="submit" style="width: 40px; height: 40px;">
                                    <i class="bi bi-send-fill" style="font-size: 0.9rem; margin-left: -2px;"></i>
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-lg-4">
            
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                <div class="card-body p-4 text-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center mx-auto mb-3 shadow-sm" style="width: 70px; height: 70px; font-size: 1.8rem;">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <h6 class="fw-bold mb-0"><?php echo htmlspecialchars($ticket['creador']); ?></h6>
                    <span class="badge bg-light text-muted border mt-2 mb-3"><?php echo htmlspecialchars($ticket['departamento']); ?></span>
                    
                    <div class="bg-light rounded p-2 text-start mt-2">
                        <small class="text-muted d-block mb-1"><i class="bi bi-envelope me-2"></i>Correo:</small>
                        <span class="text-dark small fw-bold text-break">
                            <?php echo !empty($ticket['email_creador']) ? $ticket['email_creador'] : 'No registrado'; ?>
                        </span>
                    </div>
                </div>
            </div>

            <?php if ($es_staff): ?>
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold text-dark mb-0 small text-uppercase" style="letter-spacing: 1px;">Gestión del Ticket</h6>
                </div>
                <div class="card-body p-4">
                    
                    <form action="../actions/asignar_ticket.php" method="POST" class="mb-4">
                        <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
                        <label class="form-label small fw-bold text-muted">Asignar Técnico:</label>
                        <div class="input-group shadow-sm rounded">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-tools text-primary"></i></span>
                            <select name="tecnico_id" class="form-select border-start-0 ps-0" style="font-size: 0.9rem;">
                                <option value="">-- Sin Asignar --</option>
                                <?php 
                                    $stmt_tecs = $pdo->query("SELECT id, nombre FROM usuarios WHERE rol = 'tecnico' AND activo = 1");
                                    while($tec = $stmt_tecs->fetch()){
                                        $selected = ($ticket['agente_id'] == $tec['id']) ? 'selected' : '';
                                        echo "<option value='{$tec['id']}' $selected>{$tec['nombre']}</option>";
                                    }
                                ?>
                            </select>
                            <button class="btn btn-dark btn-sm px-3" type="submit">OK</button>
                        </div>
                    </form>

                    <hr class="border-secondary opacity-10 my-4">

                    <form action="../actions/actualizar_estado.php" method="POST">
                        <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
                        <label class="form-label small fw-bold text-muted">Cambiar Estado:</label>
                        <div class="input-group shadow-sm rounded">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-stoplights text-primary"></i></span>
                            <select name="estado" class="form-select border-start-0 ps-0" style="font-size: 0.9rem;">
                                <option value="abierto" <?php echo $ticket['estado']=='abierto'?'selected':''; ?>>Abierto</option>
                                <option value="en_proceso" <?php echo $ticket['estado']=='en_proceso'?'selected':''; ?>>En Proceso</option>
                                <option value="resuelto" <?php echo $ticket['estado']=='resuelto'?'selected':''; ?>>Resuelto</option>
                                <option value="cerrado" <?php echo $ticket['estado']=='cerrado'?'selected':''; ?>>Cerrado (Final)</option>
                            </select>
                            <button class="btn btn-primary btn-sm px-3" type="submit">OK</button>
                        </div>
                    </form>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<script>
const TICKET_ID = <?php echo $id_ticket; ?>; // ID Global para JS

// 1. AGREGAR TAREA
async function agregarTarea() {
    const input = document.getElementById('nuevo-item');
    const texto = input.value.trim();
    if(!texto) return;

    try {
        const res = await fetch('../actions/checklist_acciones.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ accion: 'agregar', ticket_id: TICKET_ID, tarea: texto })
        });
        // Si todo sale bien, recargamos
        if(res.ok) location.reload();
    } catch (error) { console.error(error); }
}

// 2. MARCAR / DESMARCAR
document.querySelectorAll('.check-tarea').forEach(chk => {
    chk.addEventListener('change', async function() {
        const id = this.value;
        const estado = this.checked ? 1 : 0;
        
        // Efecto visual inmediato
        const textoSpan = document.getElementById('text-' + id);
        if(this.checked) {
            textoSpan.classList.add('text-decoration-line-through', 'text-muted');
        } else {
            textoSpan.classList.remove('text-decoration-line-through', 'text-muted');
        }

        // Backend
        await fetch('../actions/checklist_acciones.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ accion: 'toggle', item_id: id, estado: estado })
        });
        
        location.reload(); // Para actualizar la barra
    });
});

// 3. ELIMINAR TAREA
document.querySelectorAll('.btn-eliminar-tarea').forEach(btn => {
    btn.addEventListener('click', async function() {
        if(!confirm('¿Borrar esta tarea?')) return;
        const id = this.dataset.id;
        
        await fetch('../actions/checklist_acciones.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ accion: 'eliminar', item_id: id })
        });
        location.reload();
    });
});
</script>

<?php require '../includes/footer.php'; ?>