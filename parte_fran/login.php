<?php
// Inicio de sesión con configuración segura
session_start([
    'cookie_lifetime' => 86400, // 1 día de duración
    'cookie_secure' => isset($_SERVER['HTTPS']), // Solo HTTPS si está disponible
    'cookie_httponly' => true, // Protección contra XSS
    'use_strict_mode' => true // Mayor seguridad
]);

include("database.php");

// Configuración de seguridad
define('MAX_LOGIN_ATTEMPTS', 5); // Intentos máximos antes de bloqueo
define('LOGIN_LOCKOUT_TIME', 15 * 60); // 15 minutos de bloqueo (en segundos)
define('REMEMBER_ME_EXPIRY', 30 * 24 * 60 * 60); // 30 días para "Recordarme"

// Limpiar sesión si acceden al login estando logueados
if ($_SERVER['REQUEST_METHOD'] !== 'POST' && isset($_SESSION['id'])) {
    session_unset();
    session_destroy();
    session_start();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['username']) && !empty($_POST['password'])) {
        $username = trim($_POST['username']);
        $password = $_POST['password'];

        // Verificar intentos fallidos usando solo la sesión
        $attempts_key = 'login_attempts_' . md5($username);
        $last_attempt_key = 'last_attempt_' . md5($username);
        
        $login_attempts = isset($_SESSION[$attempts_key]) ? $_SESSION[$attempts_key] : 0;
        $last_attempt = isset($_SESSION[$last_attempt_key]) ? $_SESSION[$last_attempt_key] : 0;
        
        // Verificar si la cuenta está temporalmente bloqueada
        if ($login_attempts >= MAX_LOGIN_ATTEMPTS && (time() - $last_attempt) < LOGIN_LOCKOUT_TIME) {
            $remaining_time = ceil((LOGIN_LOCKOUT_TIME - (time() - $last_attempt)) / 60);
            $error_message = "Demasiados intentos fallidos. Por favor, espere $remaining_time minutos antes de intentar nuevamente.";
        } else {
            try {
                $sql = "SELECT id, username, password, role FROM users WHERE username = :credential";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':credential', $username, PDO::PARAM_STR);
                $stmt->execute();

                if ($stmt->rowCount() === 1) {
                    $user = $stmt->fetch();
                    
                    if (password_verify($password, $user['password'])) {
                        // Restablecer contador de intentos fallidos
                        unset($_SESSION[$attempts_key]);
                        unset($_SESSION[$last_attempt_key]);
                        
                        // Regenerar ID de sesión por seguridad
                        session_regenerate_id(true);
                        
                        // Establecer variables de sesión
                        $_SESSION['id'] = $user['id'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['role'] = $user['role'];
                        
                        // Redirección según el rol
                        $redirect_page = ($user['role'] === 'tech') ? 'dashboardTecnico.php' : 'dashboard.php';
                        
                        // Redirección con JavaScript como respaldo
                        echo '<script>window.location.href = "'.$redirect_page.'";</script>';
                        header("Location: ".$redirect_page);
                        exit();
                    } else {
                        // Incrementar contador de intentos fallidos
                        $_SESSION[$attempts_key] = $login_attempts + 1;
                        $_SESSION[$last_attempt_key] = time();
                        
                        $remaining_attempts = MAX_LOGIN_ATTEMPTS - ($login_attempts + 1);
                        $error_message = "Contraseña incorrecta. Intentos restantes: $remaining_attempts";
                    }
                } else {
                    $error_message = "Usuario no encontrado";
                }
            } catch (PDOException $e) {
                error_log("Error de base de datos: " . $e->getMessage());
                $error_message = "Error del sistema. Por favor intente más tarde.";
            }
        }
    } else {
        $error_message = "Por favor complete todos los campos";
    }
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
    </style>
</head>
<body class="bg-light">
    <div class="container">
        <header class="d-flex justify-content-between align-items-center py-3 border-bottom border-primary">
            <div class="logo">
                <img src="https://camaradesevilla.com/wp-content/uploads/2024/07/S00-logo-Grupo-Solutia-v01-1.png" 
                     alt="Logo del Sistema" class="img-fluid" style="max-width: 150px;">
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