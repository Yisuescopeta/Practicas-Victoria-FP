<?php require_once BASE_PATH . 'ticket_system/views/partials/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-primary"><i class="fas fa-tags me-2"></i>Gestión de Categorías</h1>
        <div>
            <a href="index.php?controller=category&action=create" class="btn btn-primary me-2">
                <i class="fas fa-plus-circle me-2"></i> Nueva Categoría
            </a>
            <button id="theme-button" class="btn btn-primary">
                <i class="fas fa-moon me-2"></i> Modo Oscuro
            </button>
        </div>
    </div>
    
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php 
            echo $_SESSION['success_message'];
            unset($_SESSION['success_message']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php 
            echo $_SESSION['error_message'];
            unset($_SESSION['error_message']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <div class="card shadow-sm">
        <div class="card-body">
            <?php if (empty($categories)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> No hay categorías registradas.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="categoriesTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th><i class="fas fa-tag me-2"></i>Nombre</th>
                                <th><i class="fas fa-align-left me-2"></i>Descripción</th>
                                <th><i class="fas fa-cogs me-2"></i>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td><?php echo $category['id']; ?></td>
                                    <td><?php echo htmlspecialchars($category['name']); ?></td>
                                    <td><?php echo htmlspecialchars($category['description'] ?? ''); ?></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="index.php?controller=category&action=edit&id=<?php echo $category['id']; ?>" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit me-1"></i> Editar
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $category['id']; ?>">
                                                <i class="fas fa-trash-alt me-1"></i> Eliminar
                                            </button>
                                        </div>
                                        
                                        <!-- Modal de confirmación para eliminar -->
                                        <div class="modal fade" id="deleteModal<?php echo $category['id']; ?>" tabindex="-1" 
                                             aria-labelledby="deleteModalLabel<?php echo $category['id']; ?>" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title" id="deleteModalLabel<?php echo $category['id']; ?>">
                                                            <i class="fas fa-exclamation-triangle me-2"></i>Confirmar eliminación
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        ¿Está seguro de que desea eliminar la categoría <strong><?php echo htmlspecialchars($category['name']); ?></strong>?
                                                        <p class="text-danger mt-2">
                                                            <i class="fas fa-exclamation-circle me-2"></i> Esta acción no se puede deshacer. Si la categoría tiene tickets asociados, no podrá ser eliminada.
                                                        </p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                            <i class="fas fa-times me-2"></i>Cancelar
                                                        </button>
                                                        <form action="index.php?controller=category&action=delete" method="post" style="display: inline;">
                                                            <input type="hidden" name="id" value="<?php echo $category['id']; ?>">
                                                            <button type="submit" class="btn btn-danger">
                                                                <i class="fas fa-trash-alt me-2"></i>Eliminar
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    // Script para el modo oscuro/claro
    document.addEventListener('DOMContentLoaded', function() {
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
        }

        function disableDarkMode() {
            body.classList.remove('dark-mode');
            icon.classList.replace('fa-sun', 'fa-moon');
            themeButton.innerHTML = '<i class="fas fa-moon me-2"></i> Modo Oscuro';
            localStorage.setItem('darkMode', 'false');
        }

        // Inicializar DataTable
        $('#categoriesTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
            },
            responsive: true
        });
    });
</script>

<?php require_once BASE_PATH . 'ticket_system/views/partials/footer.php'; ?>