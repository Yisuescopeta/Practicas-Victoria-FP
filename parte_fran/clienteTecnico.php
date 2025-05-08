<?php
session_start();
require 'database.php';

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

// Obtener lista de técnicos
$stmt = $pdo->prepare("SELECT id, username FROM users WHERE role = 'tech'");
$stmt->execute();
$tecnicos = $stmt->fetchAll();

// Procesar mensaje de éxito/error si existe
$mensaje = $_GET['mensaje'] ?? '';
$tipoMensaje = $_GET['tipo_mensaje'] ?? '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto - Sistema de Tickets</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --color-primary: #3498db;
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

        body.dark-mode {
            --color-primary: #ff8c42;
            --color-bg: #121212;
            --color-text: #f8f9fa;
            --color-card: #1e1e1e;
            --color-border: #444;
        }

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

        .contact-container {
            background-color: var(--color-card);
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        body.dark-mode .contact-container {
            background-color: #2c2c2c;
        }

        .contact-title {
            color: var(--color-primary);
            margin-bottom: 25px;
            font-weight: 700;
            border-bottom: 2px solid var(--color-primary);
            padding-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: flex;
            align-items: center;
            gap: 8px;
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

        .btn-send {
            background-color: var(--color-primary);
            color: white;
            border: none;
        }

        .btn-send:hover {
            background-color: var(--color-primary-dark);
            transform: translateY(-2px);
        }

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
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-link:hover, .nav-link.active {
            background-color: rgba(52, 152, 219, 0.1);
            color: var(--color-primary);
        }

        body.dark-mode .nav-link:hover, 
        body.dark-mode .nav-link.active {
            background-color: rgba(255, 140, 66, 0.1);
        }

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

        textarea.form-control {
            min-height: 200px;
            resize: vertical;
        }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 16px 12px;
        }

        body.dark-mode select.form-control {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23f8f9fa' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
        }

        .main-title {
            display: flex;
            align-items: center;
            gap: 10px;
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
                                <a class="nav-link d-flex align-items-center gap-2" href="gestionPerfilUsuario.php">
                                    <i class="fas fa-user-cog"></i> Editar Perfil
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active d-flex align-items-center gap-2" href="clienteTecnico.php">
                                    <i class="fas fa-comments"></i> Comunicación
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>

                <div class="col-md-9">
                    <main class="main-content">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2 class="main-title">
                                <i class="fas fa-comments"></i>
                                <span>Contactar con técnico</span>
                            </h2>
                        </div>
                        
                        <?php if ($mensaje): ?>
                            <div class="alert <?= $tipoMensaje === 'success' ? 'alert-success' : 'alert-danger' ?>">
                                <i class="fas <?= $tipoMensaje === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?>"></i>
                                <?= htmlspecialchars($mensaje) ?>
                            </div>
                        <?php endif; ?>

                        <div class="contact-container">
                            <h3 class="contact-title">
                                <i class="fas fa-paper-plane"></i>
                                <span>Enviar mensaje</span>
                            </h3>
                            
                            <form method="POST" action="procesar_contacto.php">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="asunto">
                                                <i class="fas fa-heading"></i>
                                                <span>Asunto:</span>
                                            </label>
                                            <input type="text" id="asunto" name="asunto" class="form-control" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="tecnico">
                                                <i class="fas fa-user-tie"></i>
                                                <span>Técnico:</span>
                                            </label>
                                            <select id="tecnico" name="tecnico" class="form-control" required>
                                                <?php foreach ($tecnicos as $tecnico): ?>
                                                    <option value="<?= htmlspecialchars($tecnico['id']) ?>">
                                                        <?= htmlspecialchars($tecnico['username']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="mensaje">
                                                <i class="fas fa-comment-dots"></i>
                                                <span>Mensaje:</span>
                                            </label>
                                            <textarea id="mensaje" name="mensaje" class="form-control" required></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <button type="button" class="btn btn-cancel" onclick="window.location.href='dashboard.php'">
                                        <i class="fas fa-times"></i>
                                        <span>Cancelar</span>
                                    </button>
                                    <button type="submit" class="btn btn-send">
                                        <i class="fas fa-paper-plane"></i>
                                        <span>Enviar Mensaje</span>
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

        // Validación del formulario
        document.querySelector('form').addEventListener('submit', function(e) {
            const mensaje = document.getElementById('mensaje').value.trim();
            
            if (mensaje.length < 10) {
                e.preventDefault();
                alert('El mensaje debe tener al menos 10 caracteres.');
                return false;
            }
            
            if (!confirm('¿Estás seguro de que deseas enviar este mensaje?')) {
                e.preventDefault();
                return false;
            }
            
            return true;
        });
    </script>
</body>
</html>