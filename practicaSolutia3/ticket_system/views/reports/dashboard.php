<?php
require_once 'ticket_system/config/database.php';
require_once 'ticket_system/models/Report.php';

// Crear instancia del modelo Report y obtener datos
$reportModel = new Report();
$data = $reportModel->getReportData();
$kpis = $data['kpis'];
$trends = $data['trends'];
$priorityStats = $data['priorityStats'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Tickets</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Chart.js para gráficos -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Sistema de Tickets</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?controller=report&action=customReport">Informes</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <h1>Dashboard de Tickets</h1>
        
        <!-- KPIs principales -->
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card text-center bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total de Tickets</h5>
                        <h2><?php echo $kpis['total_tickets']; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center bg-warning text-white">
                    <div class="card-body">
                        <h5 class="card-title">Tickets Abiertos</h5>
                        <h2><?php echo $kpis['open_tickets']; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Tickets Cerrados</h5>
                        <h2><?php echo $kpis['closed_tickets']; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Tiempo Promedio</h5>
                        <h2><?php echo $kpis['avg_resolution_time']; ?> hrs</h2>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Gráficos -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Tickets por Estado</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Tickets por Prioridad</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="priorityChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Tendencia de Tickets (Últimos 6 meses)</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Enlace a Informes Personalizados -->
        <div class="row mt-4 mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h5 class="m-0 font-weight-bold text-primary">Informes</h5>
                    </div>
                    <div class="card-body">
                        <p>Para generar informes personalizados con filtros avanzados, haga clic en el siguiente botón:</p>
                        <a href="index.php?controller=report&action=customReport" class="btn btn-primary">
                            <i class="fas fa-file-alt"></i> Informes Personalizados
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <div class="container">
            <p class="mb-0">Sistema de Tickets &copy; <?php echo date('Y'); ?></p>
        </div>
    </footer>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Datos para los gráficos
        const statusLabels = [<?php 
            if (isset($kpis['tickets_by_status']) && is_array($kpis['tickets_by_status'])) {
                foreach ($kpis['tickets_by_status'] as $status) {
                    $statusLabel = '';
                    switch ($status['status']) {
                        case 'open': $statusLabel = 'Abierto'; break;
                        case 'in_progress': $statusLabel = 'En Progreso'; break;
                        case 'resolved': $statusLabel = 'Resuelto'; break;
                        case 'closed': $statusLabel = 'Cerrado'; break;
                        default: $statusLabel = $status['status']; break;
                    }
                    echo "'" . $statusLabel . "',";
                }
            } else {
                echo "'Abierto','En Progreso','Resuelto','Cerrado'";
            }
        ?>];
        
        const statusValues = [<?php 
            if (isset($kpis['tickets_by_status']) && is_array($kpis['tickets_by_status'])) {
                foreach ($kpis['tickets_by_status'] as $status) {
                    echo ($status['total'] ?? 0) . ",";
                }
            } else {
                echo "0,0,0,0";
            }
        ?>];
        
        const priorityLabels = [<?php 
            if (isset($priorityStats) && is_array($priorityStats)) {
                foreach ($priorityStats as $p) {
                    echo "'" . ucfirst($p['priority'] ?? 'Desconocido') . "',";
                }
            } else {
                echo "'Baja','Media','Alta','Urgente'";
            }
        ?>];
        
        const priorityValues = [<?php 
            if (isset($priorityStats) && is_array($priorityStats)) {
                foreach ($priorityStats as $p) {
                    echo ($p['total'] ?? 0) . ",";
                }
            } else {
                echo "0,0,0,0";
            }
        ?>];
        
        const trendLabels = [<?php 
            if (isset($trends) && is_array($trends)) {
                foreach ($trends as $trend) {
                    echo "'" . ($trend['period'] ?? '') . "',";
                }
            }
        ?>];
        
        const trendTickets = [<?php 
            if (isset($trends) && is_array($trends)) {
                foreach ($trends as $trend) {
                    echo ($trend['total_tickets'] ?? 0) . ",";
                }
            }
        ?>];
        
        const trendClosed = [<?php 
            if (isset($trends) && is_array($trends)) {
                foreach ($trends as $trend) {
                    echo ($trend['closed_tickets'] ?? 0) . ",";
                }
            }
        ?>];
        
        // Crear gráficos
        window.onload = function() {
            // Gráfico de estados
            if (statusLabels.length > 0) {
                new Chart(document.getElementById('statusChart'), {
                    type: 'pie',
                    data: {
                        labels: statusLabels,
                        datasets: [{
                            label: 'Tickets por Estado',
                            data: statusValues,
                            backgroundColor: [
                                'rgba(255, 206, 86, 0.7)', // Amarillo para Abierto
                                'rgba(54, 162, 235, 0.7)', // Azul para En Progreso
                                'rgba(75, 192, 192, 0.7)', // Verde claro para Resuelto
                                'rgba(153, 102, 255, 0.7)' // Morado para Cerrado
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'right',
                            },
                            title: {
                                display: true,
                                text: 'Distribución por Estado'
                            }
                        }
                    }
                });
            }
            
            // Gráfico de prioridades
            if (priorityLabels.length > 0) {
                new Chart(document.getElementById('priorityChart'), {
                    type: 'bar',
                    data: {
                        labels: priorityLabels,
                        datasets: [{
                            label: 'Tickets por Prioridad',
                            data: priorityValues,
                            backgroundColor: [
                                'rgba(75, 192, 192, 0.7)',
                                'rgba(255, 206, 86, 0.7)',
                                'rgba(255, 159, 64, 0.7)',
                                'rgba(255, 99, 132, 0.7)'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false
                            },
                            title: {
                                display: true,
                                text: 'Distribución por Prioridad'
                            }
                        }
                    }
                });
            }
            
            // Gráfico de tendencias
            if (trendLabels.length > 0) {
                new Chart(document.getElementById('trendChart'), {
                    type: 'line',
                    data: {
                        labels: trendLabels,
                        datasets: [{
                            label: 'Total Tickets',
                            data: trendTickets,
                            borderColor: 'rgba(54, 162, 235, 1)',
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            fill: true
                        }, {
                            label: 'Tickets Cerrados',
                            data: trendClosed,
                            borderColor: 'rgba(75, 192, 192, 1)',
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Tendencia de Tickets'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        };
    </script>
</body>
</html>
