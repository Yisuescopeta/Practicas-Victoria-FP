<?php
session_start();

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
    <title>Inicio - Sistema de Tickets</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome (íconos) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts (Montserrat) -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="estilologin.css">
    <style>
        :root {
            --color-primary: #3498db;
            --color-primary-dark: #2980b9;
            --color-orange: #ff8c42;
            --color-bg-dark: #121212;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f8f9fa;
            color: #333;
            transition: all 0.3s ease;
        }

        .hero-section {
            background: linear-gradient(135deg, #3498db, #2c3e50);
            color: white;
            padding: 80px 0;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .hero-title {
            font-weight: 700;
            font-size: 2.8rem;
            margin-bottom: 20px;
        }

        .hero-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 30px;
        }

        .feature-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
            border-left: 4px solid var(--color-primary);
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .feature-icon {
            font-size: 2.5rem;
            color: var(--color-primary);
            margin-bottom: 15px;
        }

        .btn-custom {
            padding: 12px 25px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
        }

        .btn-primary-custom {
            background-color: var(--color-primary);
        }

        .btn-primary-custom:hover {
            background-color: var(--color-primary-dark);
            transform: translateY(-2px);
        }

        .btn-outline-custom {
            border: 2px solid var(--color-primary);
            color: var(--color-primary);
        }

        .btn-outline-custom:hover {
            background-color: var(--color-primary);
            color: white;
        }

        /* Modo oscuro */
        body.dark-mode {
            background-color: var(--color-bg-dark);
            color: #eee;
        }

        body.dark-mode .feature-card {
            background-color: #2c2c2c;
            border-left-color: var(--color-orange);
        }

        body.dark-mode .hero-section {
            background: linear-gradient(135deg, #1a2a3a, #121a24);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2rem;
            }
            .hero-subtitle {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid p-0">
        <!-- Header -->
        <header class="header d-flex justify-content-between align-items-center py-3 px-4">
            <div class="logo">
                <img src="https://camaradesevilla.com/wp-content/uploads/2024/07/S00-logo-Grupo-Solutia-v01-1.png" 
                     alt="Logo del Sistema" class="img-fluid" style="max-width: 150px;">
            </div>
            <div class="theme-toggle">
                <button id="theme-button" class="btn btn-primary-custom btn-custom">
                    <i class="fas fa-moon me-2"></i>Modo Oscuro
                </button>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="hero-section text-center">
            <div class="container">
                <h1 class="hero-title">Sistema de Gestión de Tickets</h1>
                <p class="hero-subtitle">Soporte técnico eficiente para tu empresa</p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="login.php" class="btn btn-primary-custom btn-custom">
                        <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                    </a>
                    <a href="register.php" class="btn btn-outline-custom btn-custom">
                        <i class="fas fa-user-plus me-2"></i>Registrarse
                    </a>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="container my-5 py-5">
            <h2 class="text-center mb-5 fw-bold">Características Principales</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-ticket-alt"></i>
                        </div>
                        <h3>Tickets Organizados</h3>
                        <p>Gestiona y realiza seguimiento de todos tus tickets de soporte en un solo lugar.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-bell"></i>
                        </div>
                        <h3>Notificaciones</h3>
                        <p>Recibe alertas en tiempo real sobre actualizaciones de tus tickets.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3>Reportes</h3>
                        <p>Genera informes detallados del rendimiento del soporte técnico.</p>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>