<?php
// views/estadisticas.php
require '../includes/header.php';
require '../config/db.php';

// 1. SEGURIDAD: Solo Admins
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'admin') {
    echo "<script>window.location.href='dashboard.php';</script>";
    exit;
}

// -----------------------------------------------------
// 2. OBTENER DATOS DE LA BD
// -----------------------------------------------------

// A) Tickets por DEPARTAMENTO (Gráfico de Dona)
$sql_depto = "SELECT departamento, COUNT(*) as total FROM tickets GROUP BY departamento";
$stmt = $pdo->query($sql_depto);
$data_depto = $stmt->fetchAll(PDO::FETCH_ASSOC);

$labels_depto = [];
$totales_depto = [];
foreach($data_depto as $d) {
    $labels_depto[] = $d['departamento'];
    $totales_depto[] = $d['total'];
}

// B) Rendimiento de TÉCNICOS (Gráfico de Barras)
// Solo contamos tickets cerrados o resueltos para medir "éxito", o todos para carga de trabajo.
// Aquí contaremos TODOS los asignados.
$sql_tech = "SELECT u.nombre, COUNT(t.id) as total 
             FROM tickets t 
             JOIN usuarios u ON t.agente_id = u.id 
             WHERE u.rol = 'tecnico'
             GROUP BY u.nombre 
             ORDER BY total DESC";
$stmt = $pdo->query($sql_tech);
$data_tech = $stmt->fetchAll(PDO::FETCH_ASSOC);

$labels_tech = [];
$totales_tech = [];
foreach($data_tech as $t) {
    $labels_tech[] = $t['nombre'];
    $totales_tech[] = $t['total'];
}

// C) Tickets ÚLTIMOS 15 DÍAS (Gráfico de Línea)
$sql_time = "SELECT DATE_FORMAT(fecha_creacion, '%d/%m') as fecha, COUNT(*) as total 
             FROM tickets 
             WHERE fecha_creacion >= DATE_SUB(NOW(), INTERVAL 15 DAY)
             GROUP BY DATE(fecha_creacion) 
             ORDER BY fecha_creacion ASC";
$stmt = $pdo->query($sql_time);
$data_time = $stmt->fetchAll(PDO::FETCH_ASSOC);

$labels_time = [];
$totales_time = [];
foreach($data_time as $d) {
    $labels_time[] = $d['fecha'];
    $totales_time[] = $d['total'];
}
?>

<div class="container-fluid mb-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-0"><i class="bi bi-graph-up-arrow text-primary me-2"></i>Reportes y Métricas</h3>
            <p class="text-muted small mb-0 ms-1">Análisis visual del rendimiento del sistema.</p>
        </div>
        <button onclick="window.print()" class="btn btn-light border shadow-sm rounded-pill px-3 text-muted">
            <i class="bi bi-printer me-2"></i>Imprimir Reporte
        </button>
    </div>

    <div class="row g-4 mb-4">
        
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h6 class="fw-bold text-dark mb-0">📊 Flujo de Tickets (Últimos 15 días)</h6>
                </div>
                <div class="card-body p-4">
                    <canvas id="chartTime" height="120"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h6 class="fw-bold text-dark mb-0">🏢 Por Departamento</h6>
                </div>
                <div class="card-body p-4 d-flex align-items-center justify-content-center">
                    <div style="width: 100%; max-width: 250px;">
                        <canvas id="chartDepto"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h6 class="fw-bold text-dark mb-0">🏆 Carga de Trabajo por Técnico</h6>
                </div>
                <div class="card-body p-4">
                    <canvas id="chartTech" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Configuración Global de Colores (Tu paleta)
    const colorPrimary = '#0072ff';
    const colorCyan    = '#00c6ff';
    const colorBg      = 'rgba(0, 198, 255, 0.1)';
    const colorBorder  = '#00c6ff';

    // 1. GRÁFICO DE LINEA (TIEMPO)
    const ctxTime = document.getElementById('chartTime').getContext('2d');
    
    // Crear gradiente bonito para la línea
    let gradient = ctxTime.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(0, 198, 255, 0.5)');   
    gradient.addColorStop(1, 'rgba(0, 198, 255, 0.0)');

    new Chart(ctxTime, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($labels_time); ?>,
            datasets: [{
                label: 'Tickets Nuevos',
                data: <?php echo json_encode($totales_time); ?>,
                borderColor: colorPrimary,
                backgroundColor: gradient,
                borderWidth: 2,
                pointBackgroundColor: '#fff',
                pointBorderColor: colorPrimary,
                pointRadius: 4,
                fill: true,
                tension: 0.4 // Curva suave
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { borderDash: [5, 5] } },
                x: { grid: { display: false } }
            }
        }
    });

    // 2. GRÁFICO DE DONA (DEPARTAMENTOS)
    const ctxDepto = document.getElementById('chartDepto').getContext('2d');
    new Chart(ctxDepto, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($labels_depto); ?>,
            datasets: [{
                data: <?php echo json_encode($totales_depto); ?>,
                backgroundColor: [
                    '#0072ff', '#00c6ff', '#4facfe', '#00f2fe', '#b9e9fc', '#212529'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            cutout: '70%', // Dona más fina
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true } }
            }
        }
    });

    // 3. GRÁFICO DE BARRAS (TECNICOS)
    const ctxTech = document.getElementById('chartTech').getContext('2d');
    new Chart(ctxTech, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($labels_tech); ?>,
            datasets: [{
                label: 'Tickets Asignados',
                data: <?php echo json_encode($totales_tech); ?>,
                backgroundColor: colorPrimary,
                borderRadius: 5, // Barras redondeadas
                barThickness: 30
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true },
                x: { grid: { display: false } }
            }
        }
    });
</script>

<?php require '../includes/footer.php'; ?>