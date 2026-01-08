<?php
// views/crear_ticket.php
require '../includes/header.php'; // Traemos la barra lateral y estilos
require '../config/db.php';     // Conexión a BD (aunque aquí no la usamos directo, es buena práctica tenerla)

// Si no hay sesión iniciada, lo mandamos al login de una.
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit;
}
?>

<div class="container-fluid mb-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-0"><i class="bi bi-plus-circle-dotted text-primary me-2"></i>Nuevo Ticket</h3>
            <p class="text-muted small mb-0 ms-1">Completa el formulario para reportar una incidencia.</p>
        </div>
        <a href="dashboard.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-body p-4 p-lg-5">
                    
                    <form action="../actions/crear_ticket.php" method="POST" enctype="multipart/form-data">
                        
                        <h6 class="text-primary fw-bold text-uppercase mb-3 small" style="letter-spacing: 1px;">
                            <i class="bi bi-person-lines-fill me-2"></i>Información del Solicitante
                        </h6>

                        <div class="row mb-4">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label class="form-label text-muted small fw-bold">Nombre</label>
                                <input type="text" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?>" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Departamento *</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-building text-primary"></i></span>
                                    <select name="departamento" class="form-select border-start-0 ps-0" required>
                                        <option value="">Seleccionar...</option>
                                        <option value="TI">TI / Sistemas</option>
                                        <option value="Recursos Humanos">Recursos Humanos</option>
                                        <option value="Contabilidad">Contabilidad</option>
                                        <option value="Ventas">Ventas</option>
                                        <option value="Operaciones">Operaciones</option>
                                        <option value="Administración">Administración</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <hr class="opacity-10 my-4">

                        <h6 class="text-primary fw-bold text-uppercase mb-3 small" style="letter-spacing: 1px;">
                            <i class="bi bi-exclamation-diamond-fill me-2"></i>Detalle de la Incidencia
                        </h6>

                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Asunto *</label>
                            <input type="text" name="titulo" class="form-control" placeholder="Ej: Fallo en impresora piso 2" required>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label class="form-label text-muted small fw-bold">Prioridad *</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="prioridad" value="media" id="prioMedia" checked>
                                        <label class="form-check-label badge bg-warning bg-opacity-10 text-warning border border-warning px-3 py-1 rounded-pill" style="cursor: pointer;" for="prioMedia">Media</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="prioridad" value="alta" id="prioAlta">
                                        <label class="form-check-label badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-1 rounded-pill" style="cursor: pointer;" for="prioAlta">Alta</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="prioridad" value="baja" id="prioBaja">
                                        <label class="form-check-label badge bg-success bg-opacity-10 text-success border border-success px-3 py-1 rounded-pill" style="cursor: pointer;" for="prioBaja">Baja</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Descripción Detallada *</label>
                            <textarea name="descripcion" class="form-control" rows="5" placeholder="Explica qué sucedió..." required></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-muted small fw-bold">Adjuntar Evidencia (Opcional)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-paperclip text-primary"></i></span>
                                <input type="file" name="adjunto" class="form-control border-start-0 ps-0" accept="image/png, image/jpeg, application/pdf">
                            </div>
                            <div class="form-text small">Formatos: JPG, PNG o PDF. Máx 2MB.</div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg shadow-sm fw-bold" style="border-radius: 10px;">
                                <i class="bi bi-send-fill me-2"></i>Enviar Ticket
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4 d-none d-lg-block">
            <div class="card border-0 shadow-sm mb-4 bg-info bg-opacity-10" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-info-circle-fill me-2 text-info"></i>¿Ayuda Inmediata?</h5>
                    <p class="small text-muted mb-3">Si tu problema detiene la operación crítica (ej: servidor caído), llama a soporte directo.</p>
                    <div class="d-flex align-items-center bg-white p-3 rounded shadow-sm">
                        <i class="bi bi-telephone-fill fs-4 text-primary me-3"></i>
                        <div>
                            <small class="text-muted d-block">Soporte Urgente</small>
                            <span class="fw-bold text-dark">+56 9 45685320</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require '../includes/footer.php'; ?>