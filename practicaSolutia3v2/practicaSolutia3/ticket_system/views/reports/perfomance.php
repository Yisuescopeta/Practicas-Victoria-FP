<<?php require_once BASE_PATH . 'ticket_system/views/partials/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-primary"><i class="fas fa-chart-line me-2"></i>Gráficos de Rendimiento</h1>
        <div>
            <a href="index.php?controller=report&action=custom" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i> Volver a Informes
            </a>
            <button id="theme-button" class="btn btn-primary">
                <i class="fas fa-moon me-2"></i> Modo Oscuro
            </button>
        </div>
    </div>
    
    <div class="row">
        <!-- Gráfico de tickets por estado -->
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Tickets por Estado</h5>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="250"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Gráfico de tickets por categoría -->
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-tag me-2"></i>Tickets por Categoría</h5>
                </div>
                <div class="card-body">
                    <canvas id="categoryChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Gráfico de tickets por técnico -->
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user-cog me-2"></i>Tickets por Técnico</h5>
                </div>
                <div class="card-body">
                    <canvas id="technicianChart" height="250"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Tiempo promedio de resolución -->
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-stopwatch me-2"></i>Tiempo Promedio de Resolución</h5>
                </div>
                <div class="card-body d-flex justify-content-center align-items-center">
                    <div class="text-center">
                        <h2 class="display-4 text-primary">
                            <?php echo round($avgResolutionTime['avg_hours'], 1); ?>
                        </h2>
                        <p class="lead">horas</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Incluir Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Script para el modo oscuro/claro
    const themeButton = document.getElementById('theme-button');
    const body = document.body;
    const icon = themeButton.querySelector('i');

    if (localStorage.getItem('darkMode') === 'true') {
        enableDarkMode();
    }

    themeButton.addEventListener('click', toggleDarkMode);

    function toggleDarkMode() {
        if (body.classList.contains('dark-mode')) {
            disableDarkMode();
        } else {
            enableDarkMode();
        }
    }

    function enableDarkMode() {
        body.classList.add('dark-mode');
        icon.classList.replace('fa-moon', 'fa-sun');
        themeButton.innerHTML = '<i class="fas fa-sun me-2"></i> Modo Claro';
        localStorage.setItem('darkMode', 'true');
        
        // Actualizar colores de gráficos para modo oscuro
        updateChartColors(true);
    }

    function disableDarkMode() {
        body.classList.remove('dark-mode');
        icon.classList.replace('fa-sun', 'fa-moon');
        themeButton.innerHTML = '<i class="fas fa-moon me-2"></i> Modo Oscuro';
        localStorage.setItem('darkMode', 'false');
        
        // Actualizar colores de gráficos para modo claro
        updateChartColors(false);
    }

    // Datos para los gráficos
    const statusData = {
        labels: <?php echo json_encode(array_map(function($status) {
            switch ($status['status']) {
                case 'open': return 'Abierto';
                case 'in_progress': return 'En progreso';
                case 'resolved': return 'Resuelto';
                default: return ucfirst($status['status']);
            }
        }, $ticketsByStatus)); ?>,
        datasets: [{
            data: <?php echo json_encode(array_column($ticketsByStatus, 'count')); ?>,
            backgroundColor: [
                'rgba(255, 99, 132, 0.7)',
                'rgba(54, 162, 235, 0.7)',
                'rgba(75, 192, 192, 0.7)',
                'rgba(255, 206, 86, 0.7)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(255, 206, 86, 1)'
            ],
            borderWidth: 1
        }]
    };

    const categoryData = {
        labels: <?php echo json_encode(array_column($ticketsByCategory, 'category')); ?>,
        datasets: [{
            label: 'Tickets por Categoría',
            data: <?php echo json_encode(array_column($ticketsByCategory, 'count')); ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.7)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    };

    const technicianData = {
        labels: <?php echo json_encode(array_column($ticketsByTechnician, 'technician')); ?>,
        datasets: [{
            label: 'Tickets por Técnico',
            data: <?php echo json_encode(array_column($ticketsByTechnician, 'count')); ?>,
            backgroundColor: 'rgba(75, 192, 192, 0.7)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1
        }]
    };

    // Crear gráficos
    const statusChart = new Chart(
        document.getElementById('statusChart'),
        {
            type: 'pie',
            data: statusData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: body.classList.contains('dark-mode') ? '#fff' : '#333'
                        }
                    }
                }
            }
        }
    );

    const categoryChart = new Chart(
        document.getElementById('categoryChart'),
        {
            type: 'bar',
            data: categoryData,
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: body.classList.contains('dark-mode') ? '#fff' : '#333'
                        },
                        grid: {
                            color: body.classList.contains('dark-mode') ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)'
                        }
                    },
                    x: {
                        ticks: {
                            color: body.classList.contains('dark-mode') ? '#fff' : '#333'
                        },
                        grid: {
                            color: body.classList.contains('dark-mode') ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)'
                        }
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            color: body.classList.contains('dark-mode') ? '#fff' : '#333'
                        }
                    }
                }
            }
        }
    );

    const technicianChart = new Chart(
        document.getElementById('technicianChart'),
        {
            type: 'bar',
            data: technicianData,
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: body.classList.contains('dark-mode') ? '#fff' : '#333'
                        },
                        grid: {
                            color: body.classList.contains('dark-mode') ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)'
                        }
                    },
                    x: {
                        ticks: {
                            color: body.classList.contains('dark-mode') ? '#fff' : '#333'
                        },
                        grid: {
                            color: body.classList.contains('dark-mode') ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)'
                        }
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            color: body.classList.contains('dark-mode') ? '#fff' : '#333'
                        }
                    }
                }
            }
        }
    );

    // Función para actualizar colores de gráficos según el modo
    function updateChartColors(isDarkMode) {
        const textColor = isDarkMode ? '#fff' : '#333';
        const gridColor = isDarkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';
        
        // Actualizar gráfico de estados
        statusChart.options.plugins.legend.labels.color = textColor;
        statusChart.update();
        
        // Actualizar gráfico de categorías
        categoryChart.options.scales.x.ticks.color = textColor;
        categoryChart.options.scales.y.ticks.color = textColor;
        categoryChart.options.scales.x.grid.color = gridColor;
        categoryChart.options.scales.y.grid.color = gridColor;
        categoryChart.options.plugins.legend.labels.color = textColor;
        categoryChart.update();
        
        // Actualizar gráfico de técnicos
        technicianChart.options.scales.x.ticks.color = textColor;
        technicianChart.options.scales.y.ticks.color = textColor;
        technicianChart.options.scales.x.grid.color = gridColor;
        technicianChart.options.scales.y.grid.color = gridColor;
        technicianChart.options.plugins.legend.labels.color = textColor;
        technicianChart.update();
    }
});
</script>

<?php require_once BASE_PATH . 'ticket_system/views/partials/footer.php'; ?>