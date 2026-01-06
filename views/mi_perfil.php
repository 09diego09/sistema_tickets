<?php
// views/mi_perfil.php
require '../includes/header.php';
require '../config/db.php';

// Validar sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit;
}

$id = $_SESSION['usuario_id'];
$msg = $_GET['msg'] ?? '';
$err = $_GET['error'] ?? '';

// Obtener datos frescos de la BD
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
$stmt->execute([':id' => $id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<div class="container-fluid mb-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-0"><i class="bi bi-person-circle text-primary me-2"></i>Mi Perfil</h3>
            <p class="text-muted small mb-0 ms-1">Gestiona tu información personal y seguridad.</p>
        </div>
    </div>

    <?php if ($msg == 'actualizado'): ?>
        <div class="alert alert-success border-0 shadow-sm rounded-pill px-4 mb-4">
            <i class="bi bi-check-circle-fill me-2"></i>Tus datos han sido actualizados correctamente.
        </div>
    <?php endif; ?>
    <?php if ($err == 'pass_incorrecto'): ?>
        <div class="alert alert-danger border-0 shadow-sm rounded-pill px-4 mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>La contraseña actual no coincide.
        </div>
    <?php endif; ?>
    <?php if ($err == 'no_coinciden'): ?>
        <div class="alert alert-warning border-0 shadow-sm rounded-pill px-4 mb-4">
            <i class="bi bi-exclamation-circle-fill me-2"></i>Las nuevas contraseñas no coinciden.
        </div>
    <?php endif; ?>

    <div class="row g-4">
        
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm text-center h-100" style="border-radius: 20px; overflow: hidden;">
                <div class="card-body p-5">
                    <div class="mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary shadow-sm" style="width: 100px; height: 100px; font-size: 3rem;">
                            <i class="bi bi-person-fill"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($usuario['nombre']); ?></h4>
                    <p class="text-muted mb-3"><?php echo htmlspecialchars($usuario['email']); ?></p>
                    
                    <?php 
                        $rol = ucfirst($usuario['rol']);
                        $badgeColor = ($usuario['rol'] == 'admin') ? 'dark' : (($usuario['rol'] == 'tecnico') ? 'primary' : 'secondary');
                    ?>
                    <span class="badge bg-<?php echo $badgeColor; ?> px-4 py-2 rounded-pill text-uppercase" style="letter-spacing: 1px;">
                        <?php echo $rol; ?>
                    </span>

                    <div class="mt-4 pt-4 border-top">
                        <small class="text-muted d-block mb-1">Cuenta creada el:</small>
                        <span class="fw-bold text-dark"><?php echo date('d/m/Y', strtotime($usuario['fecha_creacion'])); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-pencil-square me-2 text-primary"></i>Editar Información</h6>
                </div>
                <div class="card-body p-4">
                    
                    <form action="../actions/actualizar_perfil.php" method="POST">
                        
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label class="form-label text-muted small fw-bold text-uppercase">Nombre Completo</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="bi bi-person"></i></span>
                                    <input type="text" name="nombre" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase">Correo Electrónico</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="bi bi-envelope"></i></span>
                                    <input type="email" name="email" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label class="form-label text-muted small fw-bold text-uppercase">RUT</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0" style="background-color: #e9ecef;"><i class="bi bi-card-heading"></i></span>
                                    <input type="text" 
                                           class="form-control border-0" 
                                           value="<?php echo htmlspecialchars($usuario['rut_usuarios'] ?? ''); ?>" 
                                           readonly 
                                           style="background-color: #e9ecef; cursor: not-allowed; color: #6c757d;">
                                </div>
                                <div class="form-text small fst-italic mt-1"><i class="bi bi-lock-fill"></i> Dato no editable.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase">Teléfono de Contacto</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="bi bi-telephone"></i></span>
                                    <input type="text" 
                                           name="telefono" 
                                           class="form-control bg-light border-0" 
                                           placeholder="+56 9 1234 5678"
                                           value="<?php echo htmlspecialchars($usuario['tel_usuarios'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>

                        <hr class="opacity-10 my-4">

                        <h6 class="text-primary fw-bold text-uppercase mb-3 small" style="letter-spacing: 1px;">
                            <i class="bi bi-shield-lock me-2"></i>Seguridad
                        </h6>
                        <div class="alert alert-info bg-opacity-10 border-0 small text-muted">
                            <i class="bi bi-info-circle me-1"></i> Solo llena estos campos si deseas cambiar tu contraseña.
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label class="form-label text-muted small fw-bold">Nueva Contraseña</label>
                                <input type="password" name="new_password" class="form-control" placeholder="••••••••">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Confirmar Nueva Contraseña</label>
                                <input type="password" name="confirm_password" class="form-control" placeholder="••••••••">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-dark small fw-bold">Contraseña Actual <span class="text-danger">*</span></label>
                            <input type="password" name="current_password" class="form-control border-primary" placeholder="Requerida para guardar cambios" required>
                            <div class="form-text small">Por seguridad, debes ingresar tu contraseña actual para confirmar.</div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                                Guardar Cambios
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>

    </div>
</div>

<?php require '../includes/footer.php'; ?>