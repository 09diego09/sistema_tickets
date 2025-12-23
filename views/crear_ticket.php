<?php
// sistema_tickets/views/crear_ticket.php
require '../includes/header.php';
require '../config/db.php'; 

// Solo usuarios logueados
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit;
}
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-secondary mb-0">Nuevo Ticket</h3>
            <p class="text-muted small mb-0">Completa el formulario para reportar una incidencia.</p>
        </div>
        <a href="dashboard.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-body p-5">
                    
                    <form action="../actions/crear_ticket.php" method="POST">
                        
                        <h6 class="text-primary fw-bold text-uppercase mb-3 small" style="letter-spacing: 1px;">
                            <i class="bi bi-person-lines-fill me-2"></i>Información del Solicitante
                        </h6>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Nombre</label>
                                <input type="text" class="form-control bg-light" value="<?php echo $_SESSION['usuario_nombre']; ?>" readonly>
                                <div class="form-text">El ticket se registrará a tu nombre.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Departamento *</label>
                                <select name="departamento" class="form-select" required>
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

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Teléfono de Contacto</label>
                                <input type="text" name="telefono" class="form-control" placeholder="+56 9 ...">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Email de Contacto</label>
                                <input type="email" name="email" class="form-control" value="<?php echo $_SESSION['usuario_email'] ?? ''; ?>">
                            </div>
                        </div>

                        <hr class="my-4 opacity-10">

                        <h6 class="text-primary fw-bold text-uppercase mb-3 small" style="letter-spacing: 1px;">
                            <i class="bi bi-ticket-detailed-fill me-2"></i>Detalle de la Incidencia
                        </h6>

                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Asunto *</label>
                            <input type="text" name="titulo" class="form-control form-control-lg" placeholder="Breve resumen del problema" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-muted small fw-bold">Descripción Detallada *</label>
                            <textarea name="descripcion" class="form-control" rows="5" placeholder="Explica qué sucedió, qué estabas haciendo y si aparece algún error..." required></textarea>
                        </div>

                        <div class="row align-items-end">
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Nivel de Urgencia</label>
                                <select name="prioridad" class="form-select">
                                    <option value="baja">🟢 Baja (Consulta general)</option>
                                    <option value="media" selected>🟡 Media (Problema funcional)</option>
                                    <option value="alta">🟠 Alta (Impide trabajar)</option>
                                    <option value="critica">🔴 Crítica (Sistema caído)</option>
                                </select>
                            </div>
                            <div class="col-md-6 text-end">
                                <button type="submit" class="btn btn-primary px-5 py-2 fw-bold" style="background: linear-gradient(135deg, #0071bc 0%, #29abe2 100%); border:none; border-radius: 50px;">
                                    <i class="bi bi-send-fill me-2"></i> Enviar Ticket
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4 d-none d-lg-block">
            
            <div class="card mb-4 border-0 shadow-sm" style="background-color: #d1ecf1; color: #0c5460; border-radius: 15px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold"><i class="bi bi-info-circle-fill me-2"></i>¿Ayuda Inmediata?</h5>
                    <p class="small mb-2">Si tu problema detiene la operación crítica de la empresa, llama directamente a soporte.</p>
                    <hr style="opacity: 0.2; border-color: #0c5460;">
                    <p class="mb-0 fw-bold fs-5"><i class="bi bi-telephone-fill me-2"></i> +56 945685320</p>
                </div>
            </div>
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-secondary mb-3">Tiempos de Respuesta</h6>
                    <ul class="list-unstyled small text-muted mb-0">
                        <li class="mb-3 d-flex align-items-center">
                            <span class="badge bg-danger rounded-circle p-2 me-2"> </span> 
                            <strong>Crítica:</strong> <span class="ms-auto">1 - 2 horas</span>
                        </li>
                        <li class="mb-3 d-flex align-items-center">
                            <span class="badge bg-warning text-dark rounded-circle p-2 me-2"> </span>
                            <strong>Alta:</strong> <span class="ms-auto">4 - 8 horas</span>
                        </li>
                        <li class="mb-3 d-flex align-items-center">
                            <span class="badge bg-warning rounded-circle p-2 me-2" style="opacity: 0.5"> </span>
                            <strong>Media:</strong> <span class="ms-auto">24 horas</span>
                        </li>
                        <li class="d-flex align-items-center">
                            <span class="badge bg-success rounded-circle p-2 me-2"> </span>
                            <strong>Baja:</strong> <span class="ms-auto">48 horas</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>