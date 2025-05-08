<?php
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

require 'database.php';

$sql = "SELECT username, email FROM users WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $_SESSION['id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevoUsername = trim($_POST['username']);
    $nuevoEmail = trim($_POST['email']);
    $nuevaPassword = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirm_password']);

    if (!empty($nuevoUsername) && !empty($nuevoEmail)) {
        if (!empty($nuevaPassword) && $nuevaPassword !== $confirmPassword) {
            $mensaje = "Las contraseñas no coinciden.";
        } else {
            $actualizaSQL = "UPDATE users SET username = :username, email = :email";
            $parametros = [
                'username' => $nuevoUsername,
                'email' => $nuevoEmail,
            ];

            if (!empty($nuevaPassword)) {
                $actualizaSQL .= ", password = :password";
                $parametros['password'] = password_hash($nuevaPassword, PASSWORD_DEFAULT);
            }

            $actualizaSQL .= " WHERE id = :id";
            $parametros['id'] = $_SESSION['id'];

            $stmt = $pdo->prepare($actualizaSQL);
            if ($stmt->execute($parametros)) {
                $mensaje = "Perfil actualizado correctamente.";
                $user['username'] = $nuevoUsername;
                $user['email'] = $nuevoEmail;
                $_SESSION['username'] = $nuevoUsername; // Actualizar sesión
            } else {
                $mensaje = "Error al actualizar el perfil.";
            }
        }
    } else {
        $mensaje = "Nombre de usuario y correo no pueden estar vacíos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil - Sistema de Tickets</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --color-primary: #3498db;  /* Azul en modo claro */
            --color-primary-dark: #e67e22;
            --color-bg: #f8f9fa;
            --color-text: #343a40;
            --color-card: #ffffff;
            --color-border: #dee2e6;
            --color-success: #28a745;
            --color-danger: #dc3545;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--color-bg);
            color: var(--color-text);
            transition: all 0.3s ease;
        }

        /* Modo oscuro */
        body.dark-mode {
            --color-primary: #ff8c42;  /* Naranja en modo oscuro */
            --color-bg: #121212;
            --color-text: #f8f9fa;
            --color-card: #1e1e1e;
            --color-border: #444;
        }

        /* Header mejorado */
        .header {
            background: var(--color-card);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 15px 0;
            border-bottom: 2px solid var(--color-primary);
        }

        .user-menu {
            cursor: pointer;
            transition: all 0.3s;
            color: var(--color-primary);
        }

        .user-menu:hover {
            opacity: 0.8;
        }

        /* Formulario de perfil */
        .profile-container {
            background-color: var(--color-card);
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        body.dark-mode .profile-container {
            background-color: #2c2c2c;
        }

        .profile-title {
            color: var(--color-primary);
            margin-bottom: 25px;
            font-weight: 700;
            border-bottom: 2px solid var(--color-primary);
            padding-bottom: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--color-primary);
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--color-border);
            border-radius: 6px;
            font-size: 16px;
            transition: all 0.3s ease;
            background-color: var(--color-card);
            color: var(--color-text);
        }

        body.dark-mode .form-control {
            background-color: #3c3c3c;
            border-color: #555;
        }

        .form-control:focus {
            border-color: var(--color-primary);
            box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.25);
            outline: none;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 30px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.3s;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-cancel {
            background-color: #6c757d;
            color: white;
            border: none;
        }

        .btn-cancel:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
        }

        .btn-save {
            background-color: var(--color-primary);
            color: white;
            border: none;
        }

        .btn-save:hover {
            background-color: var(--color-primary-dark);
            transform: translateY(-2px);
        }

        /* Navbar lateral */
        .sidebar {
            background-color: var(--color-card);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .nav-link {
            color: var(--color-text);
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 5px;
            transition: all 0.3s;
        }

        .nav-link:hover, .nav-link.active {
            background-color: rgba(52, 152, 219, 0.1);
            color: var(--color-primary);
        }

        .nav-link i {
            width: 20px;
            text-align: center;
            margin-right: 10px;
        }

        body.dark-mode .nav-link:hover, 
        body.dark-mode .nav-link.active {
            background-color: rgba(255, 140, 66, 0.1);
        }

        /* Mensajes de alerta */
        .alert {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background-color: rgba(40, 167, 69, 0.2);
            border-left: 4px solid var(--color-success);
            color: var(--color-success);
        }

        .alert-danger {
            background-color: rgba(220, 53, 69, 0.2);
            border-left: 4px solid var(--color-danger);
            color: var(--color-danger);
        }

        /* Indicador de fortaleza de contraseña */
        .password-strength {
            height: 5px;
            background-color: #e9ecef;
            border-radius: 3px;
            margin-top: 5px;
            overflow: hidden;
        }

        .strength-meter {
            height: 100%;
            width: 0;
            transition: width 0.3s, background-color 0.3s;
        }

        .strength-weak {
            background-color: #dc3545;
            width: 30%;
        }

        .strength-medium {
            background-color: #fd7e14;
            width: 60%;
        }

        .strength-strong {
            background-color: #28a745;
            width: 100%;
        }

        .strength-text {
            font-size: 0.8rem;
            margin-top: 5px;
            color: var(--color-text);
        }
    </style>
</head>
<body>
    <div class="container-fluid p-0">
        <header class="header">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="logo">
                        <img src="https://camaradesevilla.com/wp-content/uploads/2024/07/S00-logo-Grupo-Solutia-v01-1.png" 
                             alt="Logo" style="max-width: 150px;">
                    </div>
                    <div class="d-flex align-items-center gap-4">
                        <button id="theme-button" class="btn btn-sm">
                            <i class="fas fa-moon"></i> Modo Oscuro
                        </button>
                        <div class="user-menu position-relative">
                            <span class="d-flex align-items-center gap-2">
                                <i class="fas fa-user-circle"></i>
                                <?php echo htmlspecialchars($_SESSION['username']); ?> ▼
                            </span>
                            <div class="dropdown-menu position-absolute end-0 mt-2 shadow" 
                                 style="display: none; min-width: 180px; background-color: var(--color-card);">
                                <a href="gestionPerfilUsuario.php" class="dropdown-item d-flex align-items-center gap-2">
                                    <i class="fas fa-user-cog"></i> Mi Perfil
                                </a>
                                <a href="logout.php" class="dropdown-item d-flex align-items-center gap-2 text-danger">
                                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="container mt-4">
            <div class="row">
                <div class="col-md-3">
                    <nav class="sidebar">
                        <ul class="nav flex-column w-100">
                            <li class="nav-item">
                                <a class="nav-link d-flex align-items-center gap-2" href="dashboard.php">
                                    <i class="fas fa-tachometer-alt"></i> Panel
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link d-flex align-items-center gap-2" href="misTickets.php">
                                    <i class="fas fa-ticket-alt"></i> Mis Tickets
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link d-flex align-items-center gap-2" href="crearTicket.php">
                                    <i class="fas fa-plus-circle"></i> Nuevo Ticket
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active d-flex align-items-center gap-2" href="gestionPerfilUsuario.php">
                                    <i class="fas fa-user-cog"></i> Editar Perfil
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link d-flex align-items-center gap-2" href="clienteTecnico.php">
                                    <i class="fas fa-comments"></i> Comunicación
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>

                <div class="col-md-9">
                    <main class="main-content">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2><i class="fas fa-user-cog me-2"></i>Editar Perfil</h2>
                        </div>
                        
                        <?php if (!empty($mensaje)): ?>
                            <div class="alert <?= strpos($mensaje, 'correctamente') !== false ? 'alert-success' : 'alert-danger' ?>">
                                <i class="fas <?= strpos($mensaje, 'correctamente') !== false ? 'fa-check-circle' : 'fa-exclamation-circle' ?>"></i>
                                <?= htmlspecialchars($mensaje) ?>
                            </div>
                        <?php endif; ?>

                        <div class="profile-container">
                            <h3 class="profile-title"><i class="fas fa-user-edit me-2"></i>Información del Perfil</h3>
                            
                            <form method="POST" action="gestionPerfilUsuario.php">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="username"><i class="fas fa-user me-2"></i>Nombre de Usuario:</label>
                                            <input type="text" id="username" name="username" class="form-control" 
                                                   value="<?= htmlspecialchars($user['username']) ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email"><i class="fas fa-envelope me-2"></i>Correo Electrónico:</label>
                                            <input type="email" id="email" name="email" class="form-control" 
                                                   value="<?= htmlspecialchars($user['email']) ?>" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password"><i class="fas fa-lock me-2"></i>Nueva Contraseña (opcional):</label>
                                            <input type="password" id="password" name="password" class="form-control" 
                                                   placeholder="Deja en blanco si no deseas cambiarla">
                                            <div class="password-strength">
                                                <div class="strength-meter" id="strength-meter"></div>
                                            </div>
                                            <div class="strength-text" id="strength-text"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="confirm_password"><i class="fas fa-lock me-2"></i>Confirmar Contraseña:</label>
                                            <input type="password" id="confirm_password" name="confirm_password" class="form-control" 
                                                   placeholder="Repite la nueva contraseña">
                                            <div id="password-match" class="strength-text"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <button type="button" class="btn btn-cancel" onclick="window.location.href='dashboard.php'">
                                        <i class="fas fa-times"></i> Cancelar
                                    </button>
                                    <button type="submit" class="btn btn-save">
                                        <i class="fas fa-save"></i> Guardar Cambios
                                    </button>
                                </div>
                            </form>
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Scripts personalizados -->
    <script>
        // Menú desplegable de usuario
        const userMenu = document.querySelector('.user-menu');
        const dropdownMenu = document.querySelector('.dropdown-menu');

        userMenu.addEventListener('click', () => {
            dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
        });

        // Cerrar menú al hacer clic fuera
        document.addEventListener('click', (e) => {
            if (!userMenu.contains(e.target)) {
                dropdownMenu.style.display = 'none';
            }
        });

        // Tema oscuro/claro
        const themeButton = document.getElementById('theme-button');
        const body = document.body;

        // Verificar preferencia guardada
        if (localStorage.getItem('darkMode') === 'enabled') {
            body.classList.add('dark-mode');
            themeButton.innerHTML = '<i class="fas fa-sun"></i> Modo Claro';
        }

        themeButton.addEventListener('click', () => {
            body.classList.toggle('dark-mode');
            const isDarkMode = body.classList.contains('dark-mode');
            
            if (isDarkMode) {
                themeButton.innerHTML = '<i class="fas fa-sun"></i> Modo Claro';
                localStorage.setItem('darkMode', 'enabled');
            } else {
                themeButton.innerHTML = '<i class="fas fa-moon"></i> Modo Oscuro';
                localStorage.setItem('darkMode', 'disabled');
            }
        });

        // Validación de contraseña
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirm_password');
        const strengthMeter = document.getElementById('strength-meter');
        const strengthText = document.getElementById('strength-text');
        const passwordMatch = document.getElementById('password-match');

        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            // Verificar longitud
            if (password.length >= 8) strength += 1;
            if (password.length >= 12) strength += 1;
            
            // Verificar caracteres especiales
            if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) strength += 1;
            
            // Verificar números
            if (/\d/.test(password)) strength += 1;
            
            // Verificar mayúsculas y minúsculas
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength += 1;
            
            // Actualizar indicador visual
            switch(strength) {
                case 0:
                case 1:
                    strengthMeter.className = 'strength-meter strength-weak';
                    strengthText.textContent = 'Débil';
                    strengthText.style.color = '#dc3545';
                    break;
                case 2:
                case 3:
                    strengthMeter.className = 'strength-meter strength-medium';
                    strengthText.textContent = 'Moderada';
                    strengthText.style.color = '#fd7e14';
                    break;
                case 4:
                case 5:
                    strengthMeter.className = 'strength-meter strength-strong';
                    strengthText.textContent = 'Fuerte';
                    strengthText.style.color = '#28a745';
                    break;
            }
            
            // Verificar coincidencia si hay confirmación
            if (confirmPasswordInput.value) {
                checkPasswordMatch();
            }
        });

        confirmPasswordInput.addEventListener('input', checkPasswordMatch);

        function checkPasswordMatch() {
            if (passwordInput.value && confirmPasswordInput.value) {
                if (passwordInput.value === confirmPasswordInput.value) {
                    passwordMatch.textContent = 'Las contraseñas coinciden';
                    passwordMatch.style.color = '#28a745';
                } else {
                    passwordMatch.textContent = 'Las contraseñas no coinciden';
                    passwordMatch.style.color = '#dc3545';
                }
            } else {
                passwordMatch.textContent = '';
            }
        }

        // Validación del formulario
        function validarFormulario() {
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            
            if (password || confirmPassword) {
                if (password !== confirmPassword) {
                    alert('Las contraseñas no coinciden.');
                    return false;
                }
            }
            
            return confirm('¿Estás seguro de que deseas guardar los cambios?');
        }
    </script>
</body>
</html>