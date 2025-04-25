<?php require_once BASE_PATH . 'ticket_system/views/partials/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-primary"><i class="fas fa-tachometer-alt me-2"></i>Panel de Administración</h1>
        <button id="theme-button" class="btn btn-primary">
            <i class="fas fa-moon me-2"></i> Modo Oscuro
        </button>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-users me-2"></i> Usuarios
                </div>
                <div class="card-body">
                    <h5 class="card-title">Gestión de Usuarios</h5>
                    <p class="card-text">Administra los usuarios del sistema.</p>
                    <a href="index.php?controller=user&action=index" class="btn btn-primary">
                        <i class="fas fa-arrow-right me-2"></i> Ir a Usuarios
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-tags me-2"></i> Categorías
                </div>
                <div class="card-body">
                    <h5 class="card-title">Gestión de Categorías</h5>
                    <p class="card-text">Administra las categorías de tickets.</p>
                    <a href="index.php?controller=category&action=index" class="btn btn-primary">
                        <i class="fas fa-arrow-right me-2"></i> Ir a Categorías
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-chart-bar me-2"></i> Reportes
                </div>
                <div class="card-body">
                    <h5 class="card-title">Informes y Estadísticas</h5>
                    <p class="card-text">Visualiza reportes del sistema.</p>
                    <a href="index.php?controller=report&action=index" class="btn btn-primary">
                        <i class="fas fa-arrow-right me-2"></i> Ir a Reportes
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-file-alt me-2"></i> Informes Personalizados
                </div>
                <div class="card-body">
                    <h5 class="card-title">Generar Informes Personalizados</h5>
                    <p class="card-text">Crea informes personalizados según tus necesidades.</p>
                    <a href="index.php?controller=report&action=custom" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> Crear Informe Personalizado
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Script para el modo oscuro/claro
    document.addEventListener('DOMContentLoaded', function() {
        const themeButton = document.getElementById('theme-button');
        const body = document.body;
        const icon = themeButton.querySelector('i');

        // Cargar preferencia al inicio
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
        }

        function disableDarkMode() {
            body.classList.remove('dark-mode');
            icon.classList.replace('fa-sun', 'fa-moon');
            themeButton.innerHTML = '<i class="fas fa-moon me-2"></i> Modo Oscuro';
            localStorage.setItem('darkMode', 'false');
        }
    });
</script>

<?php require_once BASE_PATH . 'ticket_system/views/partials/footer.php'; ?>