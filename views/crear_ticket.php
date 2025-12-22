<?php require '../includes/header.php'; ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-secondary mb-0">Nuevo Ticket</h3>
            <p class="text-muted small mb-0">Completa el formulario para reportar una incidencia.</p>
        </div>
        <a href="dashboard.php" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-body p-5">
                    
                    <form action="../actions/guardar_ticket.php" method="POST">
                        
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
                                <input type="text" name="departamento" class="form-control" placeholder="Ej: Contabilidad, RRHH..." required>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Teléfono de Contacto</label>
                                <input type="text" name="contacto" class="form-control" placeholder="+56 9 ...">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Email de Contacto</label>
                                <input type="email" name="email" class="form-control" value="usuario@empresa.com">
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
                                <button type="submit" class="btn btn-primary px-5 py-2 fw-bold" style="background: linear-gradient(135deg, #0071bc 0%, #29abe2 100%); border:none;">
                                    <i class="bi bi-send-fill me-2"></i> Enviar Ticket
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4 d-none d-lg-block">
            <div class="alert alert-info border-0 shadow-sm" style="border-radius: 15px;">
                <h5 class="alert-heading fw-bold"><i class="bi bi-info-circle me-2"></i>¿Necesitas ayuda inmediata?</h5>
                <p class="small">Si tu problema es crítico y detiene la operación de toda la empresa, por favor llama directamente a soporte.</p>
                <hr>
                <p class="mb-0 fw-bold"><i class="bi bi-telephone-fill me-2"></i> Anexo 5500</p>
            </div>
            
            <div class="card border-0 shadow-sm mt-3" style="border-radius: 15px;">
                <div class="card-body">
                    <h6 class="fw-bold text-secondary">Tiempos de Respuesta</h6>
                    <ul class="list-unstyled small text-muted mt-2 mb-0">
                        <li class="mb-2">🔴 <strong>Crítica:</strong> 1 - 2 horas</li>
                        <li class="mb-2">🟠 <strong>Alta:</strong> 4 - 8 horas</li>
                        <li class="mb-2">🟡 <strong>Media:</strong> 24 horas</li>
                        <li>🟢 <strong>Baja:</strong> 48 horas</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>