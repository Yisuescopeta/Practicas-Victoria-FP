<?php
session_start();
include("database.php");

// Procesar el formulario de registro
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    
    // Validar que los campos no estén vacíos
    if (empty($username) || empty($password) || empty($email)) {
        $error_message = "Todos los campos son obligatorios.";
    } else {
        // Hash de la contraseña
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insertar el nuevo usuario en la base de datos
        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, password, email) VALUES (:username, :password, :email)");
            $stmt->execute([
                'username' => $username,
                'password' => $hashed_password,
                'email' => $email
            ]);

            // Redirigir al login después del registro exitoso
            header("Location: login.php");
            exit();

        } catch (PDOException $e) {
            $error_message = "Error al registrar el usuario: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro | Sistema de Tickets</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="estilologin.css">
    <style>
        :root {
            --color-primary: #3498db;
            --color-primary-dark: #2980b9;
            --color-success: #2ecc71;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .register-box {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
        }

        .register-box:hover {
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

        .btn-register {
            background: var(--color-primary);
            border: none;
            transition: all 0.3s;
        }

        .btn-register:hover {
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
            <div class="register-box bg-white p-4 rounded shadow-sm w-100" style="max-width: 500px;">
                <h1 class="text-center text-primary mb-4">
                    <i class="fas fa-user-plus"></i> Registro de Usuario
                </h1>
                
                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
                <?php endif; ?>
                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
                <?php endif; ?>
                
                <form method="POST" action="register.php" id="registerForm">
                    <div class="mb-3">
                        <label for="username" class="form-label">Usuario:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" id="username" name="username" class="form-control" required>
                        </div>
                        <small id="usernameHelp" class="form-text text-muted">Mínimo 4 caracteres.</small>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo electrónico:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" id="password" name="password" class="form-control" required>
                        </div>
                        <small id="passwordHelp" class="form-text text-muted">Mínimo 6 caracteres.</small>
                    </div>
                    <button type="submit" class="btn btn-primary btn-register w-100 py-2">
                        <i class="fas fa-user-plus me-2"></i> Registrarse
                    </button>
                </form>
                
                <div class="mt-3 text-center">
                    <a href="login.php" class="text-primary text-decoration-none">
                        <i class="fas fa-sign-in-alt me-1"></i> ¿Ya tienes cuenta? Inicia sesión
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
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();
            const email = document.getElementById('email').value.trim();
            
            if (!username || !password || !email) {
                e.preventDefault();
                alert('¡Todos los campos son obligatorios!');
            } else if (password.length < 6) {
                e.preventDefault();
                alert('La contraseña debe tener al menos 6 caracteres.');
            }
        });

        // ===== [ FUERZA DE CONTRASEÑA ] =====
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthText = document.getElementById('passwordHelp');
            
            if (password.length === 0) {
                strengthText.textContent = 'Mínimo 6 caracteres.';
            } else if (password.length < 6) {
                strengthText.innerHTML = '<span style="color:red">Débil</span> (mínimo 6 caracteres)';
            } else if (password.length < 10) {
                strengthText.innerHTML = '<span style="color:orange">Media</span> (usa números o símbolos)';
            } else {
                strengthText.innerHTML = '<span style="color:green">Fuerte</span>';
            }
        });
    </script>
</body>
</html>