<?php
// sistema_tickets/views/admin_usuarios.php
require '../includes/header.php';
require '../config/db.php';

// 1. SEGURIDAD: Solo Admins pueden entrar aquí
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'admin') {
    header("Location: dashboard.php?error=acceso_denegado");
    exit;
}

// 2. OBTENER USUARIOS
// Traemos todos los usuarios ordenados por nombre
$sql = "SELECT * FROM usuarios ORDER BY id DESC";
$stmt = $pdo->query($sql);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-0">Gestión de Usuarios</h3>
            <p class="text-muted small mb-0">Administra el personal, asigna roles y credenciales.</p>
        </div>
        <button class="btn btn-primary btn-sm px-4 py-2 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#modalUsuario" onclick="limpiarModal()">
            <i class="bi bi-person-plus-fill me-2"></i> Nuevo Usuario
        </button>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Creado</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($usuarios as $u): ?>
                    <tr>
                        <td class="ps-4 fw-bold text-muted">#<?php echo $u['id']; ?></td>
                        
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                    <i class="bi bi-person-fill fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($u['nombre']); ?></div>
                                    <div class="text-muted small"><?php echo htmlspecialchars($u['email']); ?></div>
                                </div>
                            </div>
                        </td>

                        <td>
                            <?php 
                                $bg_rol = 'secondary';
                                if($u['rol'] == 'admin') $bg_rol = 'dark';
                                if($u['rol'] == 'tecnico') $bg_rol = 'info';
                                if($u['rol'] == 'usuario') $bg_rol = 'light text-dark border';
                            ?>
                            <span class="badge bg-<?php echo $bg_rol; ?> px-3 rounded-pill text-uppercase">
                                <?php echo ucfirst($u['rol']); ?>
                            </span>
                        </td>

                        <td>
                            <?php if($u['activo']): ?>
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Activo</span>
                            <?php else: ?>
                                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">Inactivo</span>
                            <?php endif; ?>
                        </td>

                        <td><span class="text-muted small"><?php echo date('d/m/Y', strtotime($u['fecha_creacion'])); ?></span></td>
                        
                        <td class="text-end pe-4">
                            <button class="btn btn-sm btn-light border me-1" 
                                    onclick='editarUsuario(<?php echo json_encode($u); ?>)'
                                    title="Editar">
                                <i class="bi bi-pencil-fill text-primary"></i>
                            </button>
                            
                            <?php if($u['id'] != $_SESSION['usuario_id']): ?>
                                <a href="../actions/eliminar_usuario.php?id=<?php echo $u['id']; ?>" 
                                   class="btn btn-sm btn-light border text-danger" 
                                   onclick="return confirm('¿Estás seguro de eliminar a este usuario?');"
                                   title="Eliminar">
                                    <i class="bi bi-trash-fill"></i>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalUsuario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header bg-primary text-white" style="border-top-left-radius: 15px; border-top-right-radius: 15px;">
                <h5 class="modal-title fw-bold" id="modalTitulo">Nuevo Usuario</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="../actions/guardar_usuario.php" method="POST">
                <div class="modal-body p-4">
                    <input type="hidden" name="id" id="usuario_id">

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Nombre Completo</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Correo Electrónico</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-muted">Rol</label>
                            <select name="rol" id="rol" class="form-select">
                                <option value="usuario">Usuario Normal</option>
                                <option value="tecnico">Técnico</option>
                                <option value="admin">Administrador</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-muted">Estado</label>
                            <select name="activo" id="activo" class="form-select">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted" id="labelPass">Contraseña</label>
                        <input type="password" name="password" class="form-control" placeholder="Dejar en blanco para mantener la actual">
                        <div class="form-text small text-muted" id="helpPass">Para usuarios nuevos es obligatoria.</div>
                    </div>
                </div>
                
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light text-muted" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">Guardar Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function limpiarModal() {
        document.getElementById('modalTitulo').innerText = 'Nuevo Usuario';
        document.getElementById('usuario_id').value = ''; // ID vacío = Crear
        document.getElementById('nombre').value = '';
        document.getElementById('email').value = '';
        document.getElementById('rol').value = 'usuario';
        document.getElementById('activo').value = '1';
        
        // Ajustar textos de contraseña
        document.getElementById('labelPass').innerText = 'Contraseña *';
        document.getElementById('helpPass').innerText = 'Obligatoria para nuevos usuarios.';
    }

    function editarUsuario(usuario) {
        // Abrir modal manualmente
        var myModal = new bootstrap.Modal(document.getElementById('modalUsuario'));
        myModal.show();

        // Rellenar datos
        document.getElementById('modalTitulo').innerText = 'Editar Usuario';
        document.getElementById('usuario_id').value = usuario.id; // ID lleno = Actualizar
        document.getElementById('nombre').value = usuario.nombre;
        document.getElementById('email').value = usuario.email;
        document.getElementById('rol').value = usuario.rol;
        document.getElementById('activo').value = usuario.activo;

        // Ajustar textos de contraseña (opcional al editar)
        document.getElementById('labelPass').innerText = 'Nueva Contraseña (Opcional)';
        document.getElementById('helpPass').innerText = 'Escribe solo si quieres cambiarla.';
    }
</script>

<?php require '../includes/footer.php'; ?>