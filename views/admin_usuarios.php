<?php
// sistema_tickets/views/admin_usuarios.php
require '../includes/header.php';
require '../config/db.php';

// 1. SEGURIDAD: Solo Admins
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'admin') {
    header("Location: dashboard.php?error=acceso_denegado");
    exit;
}

// 2. OBTENER USUARIOS
$sql = "SELECT * FROM usuarios ORDER BY nombre ASC";
$stmt = $pdo->query($sql);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid mb-5">
    
    <div class="row align-items-center mb-4 g-3">
        <div class="col-md-6">
            <h3 class="fw-bold text-dark mb-0"><i class="bi bi-people-fill text-primary me-2"></i>Gestión de Personal</h3>
            <p class="text-muted small mb-0 ms-1">Administra accesos y roles del equipo.</p>
        </div>
        <div class="col-md-6 d-flex justify-content-md-end gap-2">
            <div class="input-group shadow-sm" style="max-width: 300px;">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" id="buscador" class="form-control border-start-0 ps-0" placeholder="Buscar usuario..." onkeyup="filtrarUsuarios()">
            </div>
            
            <button class="btn btn-primary fw-bold shadow-sm px-4 rounded-pill" data-bs-toggle="modal" data-bs-target="#modalUsuario" onclick="limpiarModal()">
                <i class="bi bi-plus-lg me-1"></i>Nuevo
            </button>
        </div>
    </div>

    <div class="row g-4" id="contenedorUsuarios">
        <?php foreach($usuarios as $u): ?>
            <?php 
                // Definir estilos según rol
                $rol = strtolower(trim($u['rol']));
                $bg_avatar = 'secondary';
                $badge_class = 'secondary';
                $icono = 'bi-person';

                if($rol === 'admin') { 
                    $bg_avatar = 'dark'; 
                    $badge_class = 'dark'; 
                    $icono = 'bi-shield-lock-fill';
                }
                elseif($rol === 'tecnico') { 
                    $bg_avatar = 'primary'; 
                    $badge_class = 'primary'; 
                    $icono = 'bi-tools';
                }
            ?>

            <div class="col-md-6 col-lg-4 usuario-card" data-nombre="<?php echo strtolower($u['nombre']); ?>" data-email="<?php echo strtolower($u['email']); ?>">
                <div class="card border-0 shadow-sm h-100 position-relative" style="border-radius: 15px; overflow: hidden; transition: transform 0.2s;">
                    
                    <div class="position-absolute top-0 end-0 m-3">
                        <?php if($u['activo']): ?>
                            <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill px-2 py-1"><i class="bi bi-check-circle-fill me-1"></i>Activo</span>
                        <?php else: ?>
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger rounded-pill px-2 py-1"><i class="bi bi-x-circle-fill me-1"></i>Inactivo</span>
                        <?php endif; ?>
                    </div>

                    <div class="card-body p-4 d-flex align-items-center">
                        <div class="rounded-circle bg-<?php echo $bg_avatar; ?> bg-opacity-10 text-<?php echo $bg_avatar; ?> d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 60px; height: 60px; font-size: 1.5rem;">
                            <i class="bi <?php echo $icono; ?>"></i>
                        </div>
                        
                        <div class="ms-3 w-100 overflow-hidden">
                            <h6 class="fw-bold text-dark mb-0 text-truncate"><?php echo htmlspecialchars($u['nombre']); ?></h6>
                            <small class="text-muted d-block text-truncate mb-2"><?php echo htmlspecialchars($u['email']); ?></small>
                            
                            <span class="badge bg-<?php echo $badge_class; ?> text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">
                                <?php echo htmlspecialchars($u['rol']); ?>
                            </span>
                        </div>
                    </div>

                    <div class="card-footer bg-light border-0 p-3 d-flex justify-content-between align-items-center">
                        <small class="text-muted" style="font-size: 0.75rem;">
                            <i class="bi bi-calendar3 me-1"></i>Reg: <?php echo date('d/m/Y', strtotime($u['fecha_creacion'])); ?>
                        </small>
                        
                        <div>
                            <button class="btn btn-sm btn-white border shadow-sm text-primary me-1 rounded-circle" 
                                    style="width: 32px; height: 32px;"
                                    onclick='editarUsuario(<?php echo json_encode($u); ?>)' 
                                    title="Editar">
                                <i class="bi bi-pencil-fill" style="font-size: 0.8rem;"></i>
                            </button>
                            
                            <?php if($u['id'] != $_SESSION['usuario_id']): ?>
                                <a href="../actions/eliminar_usuario.php?id=<?php echo $u['id']; ?>" 
                                   class="btn btn-sm btn-white border shadow-sm text-danger rounded-circle"
                                   style="width: 32px; height: 32px; line-height: 1.8;"
                                   onclick="return confirm('¿Estás seguro de eliminar a <?php echo $u['nombre']; ?>?');"
                                   title="Eliminar">
                                    <i class="bi bi-trash-fill" style="font-size: 0.8rem;"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div id="noResultados" class="text-center py-5 d-none">
        <i class="bi bi-search fs-1 text-muted opacity-25"></i>
        <p class="text-muted mt-2">No se encontraron usuarios.</p>
    </div>

</div>

<div class="modal fade" id="modalUsuario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0 ps-4 pe-4 pt-4">
                <h5 class="modal-title fw-bold text-dark" id="modalTitulo">Nuevo Miembro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="../actions/guardar_usuario.php" method="POST">
                <div class="modal-body p-4">
                    <input type="hidden" name="id" id="usuario_id">

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase">Nombre Completo</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-person text-secondary"></i></span>
                            <input type="text" name="nombre" id="nombre" class="form-control bg-light border-0" placeholder="Ej: Juan Pérez" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase">Correo Electrónico</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-envelope text-secondary"></i></span>
                            <input type="email" name="email" id="email" class="form-control bg-light border-0" placeholder="correo@empresa.com" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase">Rol</label>
                            <select name="rol" id="rol" class="form-select bg-light border-0" style="cursor: pointer;">
                                <option value="usuario">👤 Usuario</option>
                                <option value="tecnico">🛠️ Técnico</option>
                                <option value="admin">🔐 Admin</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase">Estado</label>
                            <select name="activo" id="activo" class="form-select bg-light border-0">
                                <option value="1">🟢 Activo</option>
                                <option value="0">🔴 Inactivo</option>
                            </select>
                        </div>
                    </div>

                    <div class="bg-primary bg-opacity-10 p-3 rounded-3 mt-4">
                        <label class="form-label fw-bold small text-primary mb-1" id="labelPass">Contraseña de Acceso</label>
                        <div class="input-group bg-white rounded shadow-sm">
                            <span class="input-group-text bg-white border-0"><i class="bi bi-key text-primary"></i></span>
                            <input type="password" name="password" class="form-control border-0" placeholder="••••••••">
                        </div>
                        <div class="form-text small text-muted mt-2 ps-1" id="helpPass">Obligatoria para nuevos usuarios.</div>
                    </div>
                </div>
                
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 text-muted" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Función para buscar en tiempo real
    function filtrarUsuarios() {
        let input = document.getElementById('buscador').value.toLowerCase();
        let tarjetas = document.getElementsByClassName('usuario-card');
        let hayResultados = false;

        for (let i = 0; i < tarjetas.length; i++) {
            let nombre = tarjetas[i].getAttribute('data-nombre');
            let email = tarjetas[i].getAttribute('data-email');
            
            if (nombre.includes(input) || email.includes(input)) {
                tarjetas[i].classList.remove('d-none');
                hayResultados = true;
            } else {
                tarjetas[i].classList.add('d-none');
            }
        }

        let mensajeVacio = document.getElementById('noResultados');
        if (hayResultados) {
            mensajeVacio.classList.add('d-none');
        } else {
            mensajeVacio.classList.remove('d-none');
        }
    }

    function limpiarModal() {
        document.getElementById('modalTitulo').innerText = 'Nuevo Miembro';
        document.getElementById('usuario_id').value = ''; 
        document.getElementById('nombre').value = '';
        document.getElementById('email').value = '';
        document.getElementById('rol').value = 'usuario';
        document.getElementById('activo').value = '1';
        
        document.getElementById('labelPass').innerText = 'Contraseña *';
        document.getElementById('helpPass').innerText = 'Obligatoria para nuevos usuarios.';
    }

    function editarUsuario(usuario) {
        var myModal = new bootstrap.Modal(document.getElementById('modalUsuario'));
        myModal.show();

        document.getElementById('modalTitulo').innerText = 'Editar Perfil';
        document.getElementById('usuario_id').value = usuario.id; 
        document.getElementById('nombre').value = usuario.nombre;
        document.getElementById('email').value = usuario.email;
        document.getElementById('rol').value = usuario.rol; 
        document.getElementById('activo').value = usuario.activo;

        document.getElementById('labelPass').innerText = 'Nueva Contraseña (Opcional)';
        document.getElementById('helpPass').innerText = 'Déjalo vacío si no quieres cambiarla.';
    }
</script>

<?php require '../includes/footer.php'; ?>