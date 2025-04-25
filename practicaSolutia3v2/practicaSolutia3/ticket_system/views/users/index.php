<?php require_once BASE_PATH . 'ticket_system/views/partials/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-primary"><i class="fas fa-users me-2"></i>Gestión de Usuarios</h1>
        <div>
            <a href="index.php?controller=user&action=create" class="btn btn-primary me-2">
                <i class="fas fa-user-plus me-2"></i> Nuevo Usuario
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
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="usersTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th><i class="fas fa-user me-2"></i>Usuario</th>
                            <th><i class="fas fa-envelope me-2"></i>Email</th>
                            <th><i class="fas fa-user-tag me-2"></i>Rol</th>
                            <th><i class="fas fa-calendar-plus me-2"></i>Fecha de Creación</th>
                            <th><i class="fas fa-cogs me-2"></i>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="6" class="text-center">
                                    <i class="fas fa-info-circle me-2"></i>No hay usuarios registrados
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo $user['id']; ?></td>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <?php 
                                            $roles = [
                                                'admin' => '<span class="badge bg-danger">Administrador</span>',
                                                'tech' => '<span class="badge bg-primary">Técnico</span>',
                                                'client' => '<span class="badge bg-secondary">Cliente</span>'
                                            ];
                                            echo $roles[$user['role']] ?? $user['role']; 
                                        ?>
                                    </td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="index.php?controller=user&action=edit&id=<?php echo $user['id']; ?>" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit me-1"></i> Editar
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $user['id']; ?>">
                                                <i class="fas fa-trash-alt me-1"></i> Eliminar
                                            </button>
                                        </div>
                                        
                                        <!-- Modal de confirmación para eliminar -->
                                        <div class="modal fade" id="deleteModal<?php echo $user['id']; ?>" tabindex="-1" 
                                             aria-labelledby="deleteModalLabel<?php echo $user['id']; ?>" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title" id="deleteModalLabel<?php echo $user['id']; ?>">
                                                            <i class="fas fa-exclamation-triangle me-2"></i>Confirmar eliminación
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        ¿Estás seguro de que deseas eliminar al usuario <strong><?php echo htmlspecialchars($user['username']); ?></strong>?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                            <i class="fas fa-times me-2"></i>Cancelar
                                                        </button>
                                                        <form action="index.php?controller=user&action=delete" method="post">
                                                            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
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
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="mt-3">
        <a href="index.php?controller=admin&action=dashboard" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i> Volver al Panel de Administración
        </a>
    </div>
</div>

<script>
    $(document).ready(function() {
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
        }

        function disableDarkMode() {
            body.classList.remove('dark-mode');
            icon.classList.replace('fa-sun', 'fa-moon');
            themeButton.innerHTML = '<i class="fas fa-moon me-2"></i> Modo Oscuro';
            localStorage.setItem('darkMode', 'false');
        }

        // Inicializar DataTable
        $('#usersTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
            },
            responsive: true
        });
    });
</script>

<?php require_once BASE_PATH . 'ticket_system/views/partials/footer.php'; ?>