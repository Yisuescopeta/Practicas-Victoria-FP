<?php
session_start();
require 'database.php';

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

$sql = "SELECT * FROM tickets WHERE user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $_SESSION['id']]);
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Tickets</title>
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
            --color-bg: #f8f9fa;
            --color-text: #343a40;
            --color-card: #ffffff;
            --color-border: #dee2e6;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--color-bg);
            color: var(--color-text);
            transition: all 0.3s ease;
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

        /* Cards de resumen */
        .summary-card {
            border-radius: 10px;
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: transform 0.3s;
            border-left: 4px solid var(--color-primary);
            background-color: var(--color-card);
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
            color: white;
        }

        .btn-new-ticket:hover {
            background: var(--color-primary-dark);
            transform: translateY(-2px);
            color: white;
        }

        /* Panel */
        .panel {
            background-color: var(--color-card);
            border: 1px solid var(--color-border);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .panel-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            color: var(--color-primary);
        }

        .panel-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        .element {
            margin-bottom: 20px;
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

        /* Tabla */
        .table {
            color: var(--color-text);
        }

        .table-hover tbody tr:hover {
            background-color: rgba(52, 152, 219, 0.05);
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
                                <a class="nav-link active d-flex align-items-center gap-2" href="dashboard.php">
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
                                <a class="nav-link d-flex align-items-center gap-2" href="clienteTecnico.php">
                                    <i class="fas fa-comments"></i> Comunicación
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>

                <div class="col-md-9">
                    <main class="main-content">
                        <h2 class="mb-4">Bienvenido, <?php echo htmlspecialchars($_SESSION['username']); ?></h2>
                        
                        <!-- Contenedor de los paneles -->
                        <div class="panel-container">
                            <!-- Panel 1 -->
                            <div class="panel">
                                <h2 class="panel-title">Resumen rápido</h2>
                                <div class="row mb-4 summary-cards">
                                    <div class="col-md-4 mb-3">
                                        <div class="summary-card p-3 h-100">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h5 class="text-muted">Tickets Abiertos</h5>
                                                    <h2 class="mb-0"><?php echo count(array_filter($tickets, function($ticket) { 
                                                        return $ticket['status'] == 'open' || $ticket['status'] == 'in_progress'; 
                                                    })); ?></h2>
                                                </div>
                                                <i class="fas fa-exclamation-circle card-icon"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="summary-card p-3 h-100">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h5 class="text-muted">Tickets Resueltos</h5>
                                                    <h2 class="mb-0"><?php echo count(array_filter($tickets, function($ticket) { 
                                                        return $ticket['status'] == 'resolved' || $ticket['status'] == 'closed'; 
                                                    })); ?></h2>
                                                </div>
                                                <i class="fas fa-check-circle card-icon"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="summary-card p-3 h-100">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h5 class="text-muted">Total Tickets</h5>
                                                    <h2 class="mb-0"><?php echo count($tickets); ?></h2>
                                                </div>
                                                <i class="fas fa-ticket-alt card-icon"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Panel 2 -->
                            <div class="panel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h2 class="panel-title mb-0">Tickets Recientes</h2>
                                    <a href="crearTicket.php" class="btn btn-new-ticket">
                                        <i class="fas fa-plus me-1"></i> Nuevo Ticket
                                    </a>
                                </div>
                                <div class="card shadow-sm">
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-hover tickets-table mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Título</th>
                                                        <th>Descripción</th>
                                                        <th>Prioridad</th>
                                                        <th>Estado</th>
                                                        <th>Fecha de creación</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (count($tickets) > 0): ?>
                                                        <?php foreach ($tickets as $ticket): ?>
                                                        <tr>
                                                            <td><?php echo htmlspecialchars($ticket['id']); ?></td>
                                                            <td><?php echo htmlspecialchars($ticket['title']); ?></td>
                                                            <td><?php echo htmlspecialchars(substr($ticket['description'], 0, 50)); ?>...</td>
                                                            <td><?php echo htmlspecialchars($ticket['priority']); ?></td>
                                                            <td>
                                                                <span class="status-badge status-<?php echo htmlspecialchars($ticket['status']); ?>">
                                                                    <?php echo htmlspecialchars($ticket['status']); ?>
                                                                </span>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($ticket['created_at']); ?></td>
                                                            <td>
                                                                <a href="ver_ticket.php?id=<?php echo $ticket['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                                    <i class="fas fa-eye"></i> Ver
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <tr>
                                                            <td colspan="7" class="text-center">No hay tickets registrados</td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
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
    </script>
</body>
</html>