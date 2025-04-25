<?php
// Incluir header
require_once BASE_PATH . 'ticket_system/views/partials/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Crear Nuevo Usuario</h1>
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
    
    <div class="card">
        <div class="card-body">
            <form action="index.php?controller=user&action=store" method="post">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="username" class="form-label">Nombre de Usuario <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="username" name="username" value="<?php echo $_POST['username'] ?? ''; ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo $_POST['email'] ?? ''; ?>" required>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="password" class="form-label">Contraseña <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <div class="form-text">La contraseña debe tener al menos 6 caracteres.</div>
                    </div>
                    <div class="col-md-6">
                        <label for="role" class="form-label">Rol <span class="text-danger">*</span></label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="" selected disabled>Seleccionar rol</option>
                            <option value="admin" <?php echo (isset($_POST['role']) && $_POST['role'] == 'admin') ? 'selected' : ''; ?>>Administrador</option>
                            <option value="tech" <?php echo (isset($_POST['role']) && $_POST['role'] == 'tech') ? 'selected' : ''; ?>>Técnico</option>
                            <option value="client" <?php echo (isset($_POST['role']) && $_POST['role'] == 'client') ? 'selected' : ''; ?>>Cliente</option>
                        </select>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="index.php?controller=user&action=index" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Guardar Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
// Incluir footer
require_once BASE_PATH . 'ticket_system/views/partials/footer.php';
?>
