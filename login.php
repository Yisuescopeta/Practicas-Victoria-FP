<?php
session_start();

// Configuración de la base de datos
$host = 'localhost';
$dbname = 'ticket_system';
$user = 'root';
$pass = '';

// Conexión a la base de datos
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Procesar el formulario de login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Buscar el usuario en la base de datos
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        
        // Redirigir según el rol
        switch ($user['role']) {
            case 'admin':
                header('Location: index_admin.php');
                break;
            case 'tech':
                header('Location: index_tech.php');
                break;
            default:
                header('Location: index_usuario.php');
        }
        exit();
    } else {
        $error_message = "Usuario o contraseña incorrectos";
    }
}

// Redirigir si ya está logueado
if (isset($_SESSION['user_id'])) {
    switch ($_SESSION['role']) {
        case 'admin':
            header('Location: index_admin.php');
            break;
        case 'tech':
            header('Location: index_tech.php');
            break;
        default:
            header('Location: index_usuario.php');
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Tickets</title>
    <!-- Bootstrap 5 (solo CSS) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome (íconos) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="estilologin.css">
    <style>
        :root {
            --color-primary: #3498db;
            --color-primary-dark: #2980b9;
            --color-orange: #ff8c42;
            --color-bg-dark: #121212;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .login-box {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
        }

        .login-box:hover {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .input-group-text {
            background-color: rgba(52, 152, 219, 0.1);
            border: none;
            color: var(--color-primary);
        }

        .form-control {
            border-left: none !important;
        }

        .btn-login {
            background: var(--color-primary);
            border: none;
            transition: all 0.3s;
        }

        .btn-login:hover {
            background: var(--color-primary-dark);
            transform: translateY(-2px);
        }

        /* Modo oscuro (se activará con JS) */
        body.dark-mode {
            background-color: var(--color-bg-dark);
            color: #eee;
        }

        body.dark-mode .login-box {
            background: #2c2c2c;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        body.dark-mode .input-group-text {
            background: #333;
            color: var(--color-orange);
        }
    </style>
</head>
<body class="bg-light">
    <div class="container">
        <header class="d-flex justify-content-between align-items-center py-3 border-bottom border-primary">
            <div class="logo">
                <img src="https://camaradesevilla.com/wp-content/uploads/2024/07/S00-logo-Grupo-Solutia-v01-1.png" 
                     alt="Logo del Sistema" class="img-fluid" style="max-width: 150px;">
            </div>
            <div class="theme-toggle">
                <button id="theme-button" class="btn" style="background-color: var(--color-primary); color: white;">
                    <i class="fas fa-moon me-2"></i> Modo Oscuro
                </button>
            </div>
        </header>

        <main class="main-content d-flex justify-content-center align-items-center flex-grow-1 py-5">
            <div class="login-box bg-white p-4 rounded shadow-sm w-100" style="max-width: 400px;">
                <h1 class="text-center text-primary mb-4">
                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                </h1>
                
                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
                <?php endif; ?>
                
                <form method="POST" action="login.php" id="loginForm">
                    <div class="mb-3">
                        <label for="username" class="form-label">Usuario:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" id="username" name="username" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" id="password" name="password" class="form-control" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-login w-100 py-2">
                        <i class="fas fa-sign-in-alt me-2"></i> Ingresar
                    </button>
                </form>
                
                <div class="mt-3 text-center">
                    <a href="register.php" class="text-primary text-decoration-none">
                        <i class="fas fa-user-plus me-1"></i> ¿No tienes cuenta? Regístrate
                    </a>
                </div>
            </div>
        </main>
    </div>

    <!-- Bootstrap JS + Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- JavaScript para mejoras interactivas -->
    <script>
        // ===== [ MODO OSCURO DINÁMICO ] =====
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

        // ===== [ VALIDACIÓN EN TIEMPO REAL ] =====
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();
            
            if (!username || !password) {
                e.preventDefault();
                alert('¡Todos los campos son obligatorios!');
            }
        });

        // ===== [ EFECTO HOVER EN INPUTS ] =====
        const inputs = document.querySelectorAll('.form-control');
        inputs.forEach(input => {
            input.addEventListener('focus', () => {
                input.parentElement.style.border = '1px solid var(--color-primary)';
                input.parentElement.style.boxShadow = '0 0 0 0.25rem rgba(52, 152, 219, 0.25)';
            });
            
            input.addEventListener('blur', () => {
                input.parentElement.style.border = '';
                input.parentElement.style.boxShadow = '';
            });
        });
    </script>
</body>
</html>