<?php
// sistema_tickets/views/mis_tickets.php
require '../includes/header.php';
require '../config/db.php';

if (!isset($_SESSION['usuario_id'])) { header("Location: ../index.php"); exit; }
$id_usuario = $_SESSION['usuario_id'];
$rol_usuario = $_SESSION['usuario_rol'];
$vista = isset($_GET['view']) ? $_GET['view'] : 'personal';

if ($vista == 'global' && $rol_usuario == 'usuario') { $vista = 'personal'; }

// CONSULTAS CON JOIN A LA TABLA USUARIOS (para el agente)
// Agregamos: LEFT JOIN usuarios a ON t.agente_id = a.id
// Seleccionamos: a.nombre as agente

if ($vista == 'global') {
    $titulo_pagina = "Mesa de Ayuda (Global)";
    $subtitulo = "Visión general de todos los tickets.";
    $icono = "bi-inbox-fill";
    
    $sql = "SELECT t.*, u.nombre as creador, a.nombre as agente 
            FROM tickets t 
            JOIN usuarios u ON t.usuario_id = u.id 
            LEFT JOIN usuarios a ON t.agente_id = a.id
            ORDER BY t.fecha_creacion DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
} else {
    $titulo_pagina = "Mis Solicitudes";
    $subtitulo = "Tickets creados por ti o asignados a tu cargo.";
    $icono = "bi-person-workspace";
    
    $sql = "SELECT t.*, u.nombre as creador, a.nombre as agente 
            FROM tickets t 
            JOIN usuarios u ON t.usuario_id = u.id 
            LEFT JOIN usuarios a ON t.agente_id = a.id
            WHERE t.usuario_id = :uid OR t.agente_id = :aid
            ORDER BY t.fecha_creacion DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':uid' => $id_usuario, ':aid' => $id_usuario]);
}
$lista_tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h3 class="fw-bold text-dark mb-0"><i class="bi <?php echo $icono; ?> text-primary me-2"></i><?php echo $titulo_pagina; ?></h3><p class="text-muted small mb-0 ms-1"><?php echo $subtitulo; ?></p></div>
        <a href="crear_ticket.php" class="btn btn-primary btn-sm rounded-pill px-3"><i class="bi bi-plus-lg"></i> Nuevo Ticket</a>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4"># ID</th>
                        <th>Asunto</th>
                        <th>Solicitante</th>
                        <th>Asignado a</th> <th>Fecha</th>
                        <th>Prioridad</th>
                        <th>Estado</th>
                        <th class="text-end pe-4">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($lista_tickets) > 0): ?>
                        <?php foreach($lista_tickets as $t): ?>
                            <tr>
                                <td class="ps-4 fw-bold text-muted">#<?php echo $t['id']; ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($t['titulo']); ?></div>
                                    <div class="small text-muted"><?php echo htmlspecialchars($t['departamento']); ?></div>
                                </td>
                                <td><?php echo htmlspecialchars($t['creador']); ?></td>
                                
                                <td>
                                    <?php if(!empty($t['agente'])): ?>
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info">
                                            <i class="bi bi-tools me-1"></i><?php echo htmlspecialchars($t['agente']); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted small fst-italic">Pendiente</span>
                                    <?php endif; ?>
                                </td>

                                <td class="small text-muted"><?php echo date('d/m/Y', strtotime($t['fecha_creacion'])); ?></td>
                                
                                <td>
                                    <?php 
                                        $p_c = 'secondary';
                                        if($t['prioridad']=='alta') $p_c='danger';
                                        if($t['prioridad']=='media') $p_c='warning';
                                    ?>
                                    <span class="badge text-<?php echo $p_c; ?> border border-<?php echo $p_c; ?> rounded-pill px-2"><?php echo ucfirst($t['prioridad']); ?></span>
                                </td>
                                
                                <td>
                                    <?php 
                                        $bg = 'secondary';
                                        if($t['estado']=='abierto') $bg='danger';
                                        if($t['estado']=='en_proceso') $bg='warning';
                                        if($t['estado']=='cerrado') $bg='success';
                                    ?>
                                    <span class="badge bg-<?php echo $bg; ?> bg-opacity-10 text-<?php echo $bg; ?> rounded-pill px-3"><?php echo ucfirst(str_replace('_',' ',$t['estado'])); ?></span>
                                </td>
                                <td class="text-end pe-4"><a href="ver_ticket.php?id=<?php echo $t['id']; ?>" class="btn btn-sm btn-light border text-primary"><i class="bi bi-eye"></i></a></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="text-center py-5 text-muted">No hay tickets.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require '../includes/footer.php'; ?>