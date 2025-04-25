<?php
// CAMBIO: Eliminar la redefinición de BASE_PATH y el inicio de sesión
// Estos ya están definidos en el archivo index.php principal

// Incluir header
require_once BASE_PATH . 'ticket_system/views/partials/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gestión de Categorías</h1>
        <a href="index.php?controller=category&action=create" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nueva Categoría
        </a>
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
    
    <div class="card">
        <div class="card-body">
            <?php if (empty($categories)): ?>
                <div class="alert alert-info">
                    No hay categorías registradas.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <!-- CAMBIO: Eliminar la columna 'Estado' que mostraría el valor de 'active' -->
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td><?php echo $category['id']; ?></td>
                                    <td><?php echo htmlspecialchars($category['name']); ?></td>
                                    <td><?php echo htmlspecialchars($category['description'] ?? ''); ?></td>
                                    <!-- CAMBIO: Eliminar la celda que mostraría el estado activo/inactivo -->
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="index.php?controller=category&action=edit&id=<?php echo $category['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil"></i> Editar
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $category['id']; ?>">
                                                <i class="bi bi-trash"></i> Eliminar
                                            </button>
                                        </div>
                                        
                                        <!-- Modal de confirmación para eliminar -->
                                        <div class="modal fade" id="deleteModal<?php echo $category['id']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?php echo $category['id']; ?>" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deleteModalLabel<?php echo $category['id']; ?>">Confirmar eliminación</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        ¿Está seguro de que desea eliminar la categoría <strong><?php echo htmlspecialchars($category['name']); ?></strong>?
                                                        <p class="text-danger mt-2">
                                                            <i class="bi bi-exclamation-triangle"></i> Esta acción no se puede deshacer. Si la categoría tiene tickets asociados, no podrá ser eliminada.
                                                        </p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                        <form action="index.php?controller=category&action=delete" method="post" style="display: inline;">
                                                            <input type="hidden" name="id" value="<?php echo $category['id']; ?>">
                                                            <button type="submit" class="btn btn-danger">Eliminar</button>
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

<?php
// Incluir footer
require_once BASE_PATH . 'ticket_system/views/partials/footer.php';
?>
