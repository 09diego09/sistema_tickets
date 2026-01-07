<?php
// views/mis_tareas.php
require '../includes/header.php';
require '../config/db.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit;
}

$mi_id = $_SESSION['usuario_id'];
$mi_rol = $_SESSION['usuario_rol'];

// 1. Obtener MIS tareas
$sqlMis = "SELECT t.*, u.nombre as creador_nombre 
           FROM tareas t 
           JOIN usuarios u ON t.creador_id = u.id 
           WHERE t.usuario_asignado_id = :mi_id 
           ORDER BY t.completada ASC, t.fecha_creacion DESC";
$stmt = $pdo->prepare($sqlMis);
$stmt->execute(['mi_id' => $mi_id]); 
$mis_tareas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 2. Obtener tareas enviadas (Admin/Tecnico)
$tareas_enviadas = [];
$usuarios_lista = []; // Inicializamos la variable

if ($mi_rol != 'usuario') {
    $sqlEnv = "SELECT t.*, u.nombre as responsable_nombre 
               FROM tareas t 
               JOIN usuarios u ON t.usuario_asignado_id = u.id 
               WHERE t.creador_id = :creador 
               AND t.usuario_asignado_id != :excluir_propio
               ORDER BY t.completada ASC, t.fecha_creacion DESC";
    $stmtEnv = $pdo->prepare($sqlEnv);
    $stmtEnv->execute(['creador' => $mi_id, 'excluir_propio' => $mi_id]);
    $tareas_enviadas = $stmtEnv->fetchAll(PDO::FETCH_ASSOC);

    // Obtenemos la lista para el buscador
    $stmtUsers = $pdo->query("SELECT id, nombre FROM usuarios WHERE activo = 1 ORDER BY nombre ASC");
    $usuarios_lista = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="container-fluid mb-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-0"><i class="bi bi-check2-circle text-primary me-2"></i>Gestión de Tareas</h3>
            <p class="text-muted small mb-0 ms-1">Organización diaria y asignaciones.</p>
        </div>
        <div>
            <span class="badge bg-white text-dark border shadow-sm px-3 py-2">
                <i class="bi bi-calendar-event me-2"></i><?php echo date('d/m/Y'); ?>
            </span>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h6 class="fw-bold text-dark mb-0">Nueva Tarea</h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label small text-muted text-uppercase fw-bold">Título de la tarea</label>
                        <input type="text" id="nueva-tarea" class="form-control bg-light border-0" placeholder="Ej: Revisar servidor..." onkeypress="if(event.key === 'Enter') crearTarea()">
                    </div>

                    <?php if ($mi_rol != 'usuario'): ?>
                    <div class="mb-4">
                        <label class="form-label small text-muted text-uppercase fw-bold">Asignar a</label>
                        
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                            <input class="form-control bg-light border-0" list="listaUsuarios" id="input-asignar" placeholder="Escribe un nombre...">
                            
                            <datalist id="listaUsuarios">
                                <option data-id="<?php echo $mi_id; ?>" value="Para mí (Personal)">
                                
                                <?php if (!empty($usuarios_lista)): ?>
                                    <?php foreach($usuarios_lista as $u): ?>
                                        <?php if($u['id'] != $mi_id): ?>
                                            <option data-id="<?php echo $u['id']; ?>" value="<?php echo htmlspecialchars($u['nombre']); ?>">
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </datalist>
                        </div>
                        <div class="form-text small mt-1">Si lo dejas vacío, será para ti.</div>

                    </div>
                    <?php else: ?>
                        <input type="hidden" id="input-asignar" value=""> 
                        <datalist id="listaUsuarios"></datalist>
                    <?php endif; ?>

                    <button class="btn btn-primary w-100 rounded-pill fw-bold" onclick="crearTarea()">
                        <i class="bi bi-plus-lg me-2"></i>Agregar Tarea
                    </button>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm mt-3 bg-primary text-white" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <h1 class="display-4 fw-bold mb-0"><?php echo count($mis_tareas); ?></h1>
                    <p class="mb-0 opacity-75">Tareas pendientes en tu lista</p>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: 15px; min-height: 500px;">
                
                <div class="card-header bg-white border-bottom pt-4 px-4 pb-0">
                    <ul class="nav nav-tabs card-header-tabs border-0" id="misTabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active border-0 border-bottom border-3 border-primary text-dark fw-bold pb-3" id="tab-mis" data-bs-toggle="tab" data-bs-target="#panel-mis" type="button">
                                Mis Pendientes
                            </button>
                        </li>
                        <?php if ($mi_rol != 'usuario'): ?>
                        <li class="nav-item ms-4">
                            <button class="nav-link border-0 text-muted pb-3" id="tab-enviadas" data-bs-toggle="tab" data-bs-target="#panel-enviadas" type="button">
                                Asignadas a Otros
                            </button>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>

                <div class="card-body p-0">
                    <div class="tab-content" id="misTabsContent">
                        
                        <div class="tab-pane fade show active" id="panel-mis" role="tabpanel">
                            <?php if (empty($mis_tareas)): ?>
                                <div class="text-center py-5 mt-4">
                                    <i class="bi bi-clipboard-check text-muted opacity-25" style="font-size: 4rem;"></i>
                                    <h6 class="text-muted mt-3">No tienes tareas pendientes.</h6>
                                </div>
                            <?php else: ?>
                                <div class="list-group list-group-flush">
                                    <?php foreach($mis_tareas as $t): ?>
                                        <?php 
                                            $bgClass = $t['completada'] ? 'bg-light' : 'bg-white';
                                            $textClass = $t['completada'] ? 'text-decoration-line-through text-muted' : 'text-dark fw-bold';
                                            $checkState = $t['completada'] ? 'checked' : '';
                                        ?>
                                        <div class="list-group-item p-3 d-flex align-items-center <?php echo $bgClass; ?>">
                                            <div class="form-check">
                                                <input class="form-check-input tarea-check shadow-none" 
                                                       type="checkbox" 
                                                       onchange="toggleTarea(<?php echo $t['id']; ?>, this.checked)"
                                                       <?php echo $checkState; ?> 
                                                       style="transform: scale(1.2); cursor: pointer;">
                                            </div>
                                            <div class="ms-3 flex-grow-1">
                                                <span class="<?php echo $textClass; ?>"><?php echo htmlspecialchars($t['titulo']); ?></span>
                                                <?php if($t['creador_id'] != $mi_id): ?>
                                                    <div class="mt-1">
                                                        <span class="badge bg-warning text-dark bg-opacity-25 border border-warning text-warning-emphasis rounded-pill" style="font-size: 0.7rem;">
                                                            Asignado por: <?php echo htmlspecialchars($t['creador_nombre']); ?>
                                                        </span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <button type="button" 
                                                    class="btn btn-sm btn-light text-danger border shadow-sm rounded-circle" 
                                                    onclick="eliminarTarea(<?php echo $t['id']; ?>)"
                                                    style="width: 32px; height: 32px; position: relative; z-index: 10;" 
                                                    title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if ($mi_rol != 'usuario'): ?>
                        <div class="tab-pane fade" id="panel-enviadas" role="tabpanel">
                            <?php if (empty($tareas_enviadas)): ?>
                                <div class="text-center py-5 mt-4">
                                    <p class="text-muted">No has asignado tareas.</p>
                                </div>
                            <?php else: ?>
                                <div class="list-group list-group-flush">
                                    <?php foreach($tareas_enviadas as $t): ?>
                                        <div class="list-group-item p-3 d-flex align-items-center">
                                            <div class="me-3">
                                                <?php if($t['completada']): ?>
                                                    <span class="text-success"><i class="bi bi-check-circle-fill fs-5"></i></span>
                                                <?php else: ?>
                                                    <span class="text-warning"><i class="bi bi-clock-history fs-5"></i></span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex-grow-1">
                                                <span class="d-block fw-bold text-dark"><?php echo htmlspecialchars($t['titulo']); ?></span>
                                                <span class="small text-muted">Responsable: <i class="bi bi-person-fill"></i> <?php echo htmlspecialchars($t['responsable_nombre']); ?></span>
                                            </div>
                                            
                                            <button type="button" 
                                                    class="btn btn-sm btn-light text-danger border shadow-sm rounded-circle" 
                                                    onclick="eliminarTarea(<?php echo $t['id']; ?>)"
                                                    style="width: 32px; height: 32px; position: relative; z-index: 10;"
                                                    title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // ESTILOS DE TABS
    const triggerTabList = document.querySelectorAll('#misTabs button')
    triggerTabList.forEach(triggerEl => {
        const tabTrigger = new bootstrap.Tab(triggerEl)
        triggerEl.addEventListener('click', event => {
            event.preventDefault()
            tabTrigger.show()
            document.querySelectorAll('#misTabs button').forEach(btn => {
                btn.classList.remove('border-bottom', 'border-3', 'border-primary', 'text-dark', 'fw-bold');
                btn.classList.add('text-muted');
            });
            triggerEl.classList.remove('text-muted');
            triggerEl.classList.add('border-bottom', 'border-3', 'border-primary', 'text-dark', 'fw-bold');
        })
    })

    // 1. CREAR - LÓGICA DE BÚSQUEDA CORREGIDA
    async function crearTarea() {
        const inputTitulo = document.getElementById('nueva-tarea');
        const inputAsignar = document.getElementById('input-asignar');
        const datalist = document.getElementById('listaUsuarios');
        
        const texto = inputTitulo.value.trim();
        if(!texto) return;

        // Por defecto, si no se selecciona nadie, el ID soy yo mismo
        let idSeleccionado = <?php echo $mi_id; ?>; 

        // Solo procesamos el nombre si soy Admin/Tecnico y escribí algo
        if (inputAsignar && inputAsignar.value.trim() !== '') {
            const nombreEscrito = inputAsignar.value;
            let encontrado = false;

            // Buscamos en las opciones del datalist cuál coincide con lo escrito
            for (let i = 0; i < datalist.options.length; i++) {
                if (datalist.options[i].value === nombreEscrito) {
                    // ¡Encontrado! Obtenemos el ID real del atributo data-id
                    idSeleccionado = datalist.options[i].getAttribute('data-id');
                    encontrado = true;
                    break;
                }
            }
        }
        

        try {
            const res = await fetch('../actions/tareas_controller.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    accion: 'crear', 
                    titulo: texto, 
                    asignado_a: idSeleccionado // Enviamos el ID, no el nombre
                })
            });
            if(res.ok) location.reload();
        } catch (error) { console.error(error); }
    }

    // 2. TOGGLE
    async function toggleTarea(id, estado) {
        try {
            await fetch('../actions/tareas_controller.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ accion: 'toggle', id: id, estado: estado ? 1 : 0 })
            });
            location.reload();
        } catch (error) { console.error(error); }
    }

    // 3. ELIMINAR
    async function eliminarTarea(id) {
        if(!confirm('¿Estás seguro de eliminar esta tarea permanentemente?')) return;
        
        try {
            const res = await fetch('../actions/tareas_controller.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ accion: 'eliminar', id: id })
            });
            
            const data = await res.json();
            if(data.status === 'ok') {
                location.reload();
            } else {
                alert('Error al eliminar: ' + (data.msg || 'Desconocido'));
            }
        } catch (error) {
            console.error("Error crítico:", error);
            alert('Error de conexión. Revisa la consola.');
        }
    }
</script>

<?php require '../includes/footer.php'; ?>