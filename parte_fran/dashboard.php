<?php
session_start();
require 'database.php';

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

// Obtener tickets del usuario
$sql = "SELECT * FROM tickets WHERE user_id = :user_id ORDER BY created_at DESC LIMIT 5";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $_SESSION['id']]);
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Contar tickets por estado
$counts = [
    'open' => 0,
    'in_progress' => 0,
    'resolved' => 0,
    'closed' => 0
];

foreach ($tickets as $ticket) {
    $counts[$ticket['status']]++;
}

// Mensaje de éxito si existe
$mensaje = $_GET['mensaje'] ?? '';
$tipoMensaje = $_GET['tipo_mensaje'] ?? '';
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
            --color-warning: #ffc107;
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

        .dashboard-container {
            background-color: var(--color-card);
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        body.dark-mode .dashboard-container {
            background-color: #2c2c2c;
        }

        .dashboard-title {
            color: var(--color-primary);
            margin-bottom: 25px;
            font-weight: 700;
            border-bottom: 2px solid var(--color-primary);
            padding-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .summary-card {
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            background-color: var(--color-card);
            border-left: 4px solid var(--color-primary);
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }

        .summary-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0,0,0,0.1);
        }

        .summary-title {
            font-size: 1rem;
            color: var(--color-text);
            opacity: 0.8;
            margin-bottom: 5px;
        }

        .summary-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--color-primary);
        }

        .card-icon {
            font-size: 2rem;
            color: var(--color-primary);
            opacity: 0.7;
        }

        .tickets-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 10px;
            overflow: hidden;
        }

        .tickets-table thead {
            background-color: var(--color-primary);
            color: white;
        }

        .tickets-table th {
            padding: 15px;
            text-align: left;
        }

        .tickets-table td {
            padding: 12px 15px;
            border-bottom: 1px solid var(--color-border);
        }

        .tickets-table tr:last-child td {
            border-bottom: none;
        }

        .tickets-table tbody tr:hover {
            background-color: rgba(52, 152, 219, 0.05);
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
        }

        .status-open {
            background-color: #ffeeba;
            color: #856404;
        }

        .status-in_progress {
            background-color: #bee5eb;
            color: #0c5460;
        }

        .status-resolved {
            background-color: #c3e6cb;
            color: #155724;
        }

        .status-closed {
            background-color: #d6d8db;
            color: #383d41;
        }

        .btn-new-ticket {
            background-color: var(--color-primary);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-new-ticket:hover {
            background-color: var(--color-primary-dark);
            transform: translateY(-2px);
            color: white;
        }

        .btn-view {
            background-color: transparent;
            border: 1px solid var(--color-primary);
            color: var(--color-primary);
            padding: 5px 10px;
            border-radius: 6px;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-view:hover {
            background-color: var(--color-primary);
            color: white;
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

        .main-title {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .no-tickets {
            padding: 20px;
            text-align: center;
            color: var(--color-text);
            opacity: 0.7;
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
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2 class="main-title">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Panel de Usuario</span>
                            </h2>
                        </div>
                        
                        <?php if ($mensaje): ?>
                            <div class="alert <?= $tipoMensaje === 'success' ? 'alert-success' : 'alert-danger' ?>">
                                <i class="fas <?= $tipoMensaje === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?>"></i>
                                <?= htmlspecialchars($mensaje) ?>
                            </div>
                        <?php endif; ?>

                        <div class="dashboard-container">
                            <h3 class="dashboard-title">
                                <i class="fas fa-chart-bar"></i>
                                <span>Resumen de Tickets</span>
                            </h3>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="summary-card">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="summary-title">Tickets Abiertos</div>
                                                <div class="summary-value"><?= $counts['open'] + $counts['in_progress'] ?></div>
                                            </div>
                                            <i class="fas fa-exclamation-circle card-icon"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="summary-card">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="summary-title">Tickets Resueltos</div>
                                                <div class="summary-value"><?= $counts['resolved'] + $counts['closed'] ?></div>
                                            </div>
                                            <i class="fas fa-check-circle card-icon"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="summary-card">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="summary-title">Total Tickets</div>
                                                <div class="summary-value"><?= count($tickets) ?></div>
                                            </div>
                                            <i class="fas fa-ticket-alt card-icon"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <h3 class="dashboard-title mt-5">
                                <i class="fas fa-ticket-alt"></i>
                                <span>Tickets Recientes</span>
                                <a href="crearTicket.php" class="btn-new-ticket ms-auto">
                                    <i class="fas fa-plus"></i>
                                    <span>Nuevo Ticket</span>
                                </a>
                            </h3>
                            
                            <div class="table-responsive">
                                <table class="tickets-table">
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
                                        <?php if (count($tickets) > 0): ?>
                                            <?php foreach ($tickets as $ticket): ?>
                                            <tr>
                                                <td>#<?= htmlspecialchars($ticket['id']) ?></td>
                                                <td><?= htmlspecialchars($ticket['title']) ?></td>
                                                <td><?= htmlspecialchars($ticket['priority']) ?></td>
                                                <td>
                                                    <span class="status-badge status-<?= htmlspecialchars($ticket['status']) ?>">
                                                        <?= htmlspecialchars($ticket['status']) ?>
                                                    </span>
                                                </td>
                                                <td><?= date('d/m/Y', strtotime($ticket['created_at'])) ?></td>
                                                <td>
                                                    <a href="ver_ticket.php?id=<?= $ticket['id'] ?>" class="btn-view">
                                                        <i class="fas fa-eye"></i>
                                                        <span>Ver</span>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="no-tickets">
                                                    <i class="fas fa-info-circle"></i> No hay tickets registrados
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
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
    </script>
</body>
</html>