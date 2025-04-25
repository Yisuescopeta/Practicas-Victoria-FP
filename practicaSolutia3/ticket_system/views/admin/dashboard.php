<?php require_once BASE_PATH . 'ticket_system/views/partials/header.php'; ?>

<div class="container mt-4">
    <h1>Panel de Administración</h1>
    
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    Usuarios
                </div>
                <div class="card-body">
                    <h5 class="card-title">Gestión de Usuarios</h5>
                    <p class="card-text">Administra los usuarios del sistema.</p>
                    <a href="index.php?controller=user&action=index" class="btn btn-primary">Ir a Usuarios</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    Categorías
                </div>
                <div class="card-body">
                    <h5 class="card-title">Gestión de Categorías</h5>
                    <p class="card-text">Administra las categorías de tickets.</p>
                    <a href="index.php?controller=category&action=index" class="btn btn-primary">Ir a Categorías</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    Reportes
                </div>
                <div class="card-body">
                    <h5 class="card-title">Informes y Estadísticas</h5>
                    <p class="card-text">Visualiza reportes del sistema.</p>
                    <a href="index.php?controller=report&action=index" class="btn btn-primary">Ir a Reportes</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Añadir enlaces a informes personalizados -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Informes Personalizados
                </div>
                <div class="card-body">
                    <h5 class="card-title">Generar Informes Personalizados</h5>
                    <p class="card-text">Crea informes personalizados según tus necesidades.</p>
                    <!-- Usar la acción 'custom' en lugar de 'customReport' -->
                    <a href="index.php?controller=report&action=custom" class="btn btn-primary">Crear Informe Personalizado</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once BASE_PATH . 'ticket_system/views/partials/footer.php'; ?>
