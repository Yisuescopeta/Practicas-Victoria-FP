<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Usuario - Sistema de Tickets</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="estilodashboard.css">
    <style>
        :root {
            --color-primary: #3498db;  /* Azul en modo claro */
            --color-primary-dark: #e67e22;
            --color-bg-dark: #121212;
            --color-card-dark: #1e1e1e;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f8f9fa;
            transition: all 0.3s ease;
        }

        /* Header mejorado */
        .header {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .user-menu {
            cursor: pointer;
            transition: all 0.3s;
        }

        .user-menu:hover {
            color: var(--color-primary);
        }

        /* Cards de resumen */
        .summary-card {
            border-radius: 10px;
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: transform 0.3s;
            border-left: 4px solid var(--color-primary);
        }

        .summary-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0,0,0,0.1);
        }

        .card-icon {
            font-size: 1.8rem;
            color: var(--color-primary);
        }

        /* Tabla de tickets */
        .tickets-table {
            border-radius: 10px;
            overflow: hidden;
        }

        .tickets-table thead {
            background-color: var(--color-primary);
            color: white;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-open {
            background-color: #ffeeba;
            color: #856404;
        }

        .status-resolved {
            background-color: #c3e6cb;
            color: #155724;
        }

        /* Botón nuevo ticket */
        .btn-new-ticket {
            background: var(--color-primary);
            border: none;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-new-ticket:hover {
            background: var(--color-primary-dark);
            transform: translateY(-2px);
        }

        /* Modo oscuro */
        body.dark-mode {
            background-color: var(--color-bg-dark);
            color: #eee;
        }

        body.dark-mode .header {
            background: #1e1e1e;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }

        body.dark-mode .summary-card {
            background: var(--color-card-dark);
        }

        body.dark-mode .tickets-table {
            background: var(--color-card-dark);
        }

        body.dark-mode .table {
            color: #eee;
        }

        @media (max-width: 768px) {
            .summary-cards {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid p-0">
        <header class="header d-flex justify-content-between align-items-center p-3">
            <div class="logo">
                <img src="https://camaradesevilla.com/wp-content/uploads/2024/07/S00-logo-Grupo-Solutia-v01-1.png" 
                     alt="Logo" style="max-width: 150px;">
            </div>
            <div class="d-flex align-items-center gap-4">
                <div class="theme-toggle">
                    <button id="theme-button" class="btn btn-sm" 
                            style="background: var(--color-primary); color: white;">
                        <i class="fas fa-moon me-1"></i> Modo Oscuro
                    </button>
                </div>
                <div class="user-menu position-relative">
                    <span class="d-flex align-items-center gap-2">
                        <i class="fas fa-user-circle"></i>
                        <?php echo htmlspecialchars($_SESSION['username']); ?> ▼
                    </span>
                    <div class="dropdown-menu position-absolute end-0 mt-2 shadow" 
                         style="display: none; min-width: 180px;">
                        <a href="perfil.php" class="dropdown-item d-flex align-items-center gap-2">
                            <i class="fas fa-user-cog"></i> Mi Perfil
                        </a>
                        <a href="logout.php" class="dropdown-item d-flex align-items-center gap-2 text-danger">
                            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <div class="container mt-4">
            <div class="row">
                <div class="col-md-3">
                    <nav class="navbar bg-light rounded p-3">
                        <ul class="nav flex-column w-100">
                            <li class="nav-item">
                                <a class="nav-link active d-flex align-items-center gap-2" href="index_usuario.php">
                                    <i class="fas fa-tachometer-alt"></i> Panel
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link d-flex align-items-center gap-2" href="mis_tickets.php">
                                    <i class="fas fa-ticket-alt"></i> Mis Tickets
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link d-flex align-items-center gap-2" href="nuevo_ticket.php">
                                    <i class="fas fa-plus-circle"></i> Nuevo Ticket
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>

                <div class="col-md-9">
                    <main class="main-content">
                        <h2 class="mb-4">Bienvenido, <?php echo htmlspecialchars($_SESSION['username']); ?></h2>
                        
                        <!-- Resumen rápido -->
                        <div class="row mb-4 summary-cards">
                            <div class="col-md-4 mb-3">
                                <div class="summary-card p-3 h-100">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="text-muted">Tickets Abiertos</h5>
                                            <h2 class="mb-0">5</h2>
                                        </div>
                                        <i class="fas fa-exclamation-circle card-icon"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="summary-card p-3 h-100">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="text-muted">En Proceso</h5>
                                            <h2 class="mb-0">3</h2>
                                        </div>
                                        <i class="fas fa-spinner card-icon"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="summary-card p-3 h-100">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="text-muted">Resueltos</h5>
                                            <h2 class="mb-0">12</h2>
                                        </div>
                                        <i class="fas fa-check-circle card-icon"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
<br>

                        <!-- Tickets recientes -->
                        <div class="card shadow-sm">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Tickets Recientes</h5>
                                <a href="nuevo_ticket.php" class="btn btn-new-ticket">
                                    <i class="fas fa-plus me-1"></i> Nuevo Ticket
                                </a>
                
                            </div>
                            <br>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover tickets-table mb-0">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Título</th>
                                                <th>Prioridad</th>
                                                <th>Estado</th>
                                                <th>Fecha</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>#125</td>
                                                <td>Problema con el software</td>
                                                <td><span class="badge bg-warning">Alta</span></td>
                                                <td><span class="status-badge status-open">Abierto</span></td>
                                                <td>20/05/2023</td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <!-- Más filas de tickets... -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>