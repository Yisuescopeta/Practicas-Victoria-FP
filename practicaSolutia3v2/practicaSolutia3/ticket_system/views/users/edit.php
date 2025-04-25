<?php require_once BASE_PATH . 'ticket_system/views/partials/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-primary"><i class="fas fa-user-edit me-2"></i>Editar Usuario</h1>
        <button id="theme-button" class="btn btn-primary">
            <i class="fas fa-moon me-2"></i> Modo Oscuro
        </button>
    </div>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="index.php?controller=user&action=update" method="post">
                <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="username" class="form-label">Nombre de Usuario <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?php echo isset($_POST['username']) ? $_POST['username'] : $user['username']; ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo isset($_POST['email']) ? $_POST['email'] : $user['email']; ?>" required>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="password" class="form-label">Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password">
                        </div>
                        <div class="form-text">Dejar en blanco para mantener la contraseña actual.</div>
                    </div>
                    <div class="col-md-6">
                        <label for="role" class="form-label">Rol <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                            <select class="form-select" id="role" name="role" required>
                                <option value="" disabled>Seleccionar rol</option>
                                <option value="admin" <?php echo ((isset($_POST['role']) ? $_POST['role'] : $user['role']) == 'admin') ? 'selected' : ''; ?>>Administrador</option>
                                <option value="tech" <?php echo ((isset($_POST['role']) ? $_POST['role'] : $user['role']) == 'tech') ? 'selected' : ''; ?>>Técnico</option>
                                <option value="client" <?php echo ((isset($_POST['role']) ? $_POST['role'] : $user['role']) == 'client') ? 'selected' : ''; ?>>Cliente</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="index.php?controller=user&action=index" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> Actualizar Usuario
                    </button>
                </div>
            </form>
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
    });
</script>

<?php require_once BASE_PATH . 'ticket_system/views/partials/footer.php'; ?>