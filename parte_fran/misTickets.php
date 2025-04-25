<?php
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

require 'database.php';

// Filtrado
$estado = $_GET['status'] ?? '';
$categoria = $_GET['category'] ?? '';
$fecha_inicio = $_GET['start_date'] ?? '';
$fecha_fin = $_GET['end_date'] ?? '';
$orderBy = $_GET['orderby'] ?? 'created_at';
$orderDir = $_GET['dir'] ?? 'desc';
$allowedFields = ['title', 'category_name', 'created_at'];
$allowedDir = ['asc', 'desc'];

if (!in_array($orderBy, $allowedFields)) {
    $orderBy = 'created_at';
}
if (!in_array($orderDir, $allowedDir)) {
    $orderDir = 'desc';
}

$sql = "SELECT t.id, t.title, t.description, t.created_at, t.status, c.name AS category_name 
        FROM tickets t 
        JOIN categories c ON t.category_id = c.id 
        WHERE t.user_id = :user_id";

$params = ['user_id' => $_SESSION['id']];

if (!empty($estado)) {
    $sql .= " AND t.status = :status";
    $params['status'] = $estado;
}
if (!empty($categoria)) {
    $sql .= " AND c.name = :category";
    $params['category'] = $categoria;
}
if (!empty($fecha_inicio) && !empty($fecha_fin)) {
    $sql .= " AND DATE(t.created_at) BETWEEN :start_date AND :end_date";
    $params['start_date'] = $fecha_inicio;
    $params['end_date'] = $fecha_fin;
}

$sql .= " ORDER BY $orderBy $orderDir";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Función para alternar dirección de orden
function toggleDir($currentDir) {
    return $currentDir === 'asc' ? 'desc' : 'asc';
}

function linkWithOrder($field, $label, $currentField, $currentDir) {
    $newDir = ($field === $currentField) ? toggleDir($currentDir) : 'asc';
    $query = $_GET;
    $query['orderby'] = $field;
    $query['dir'] = $newDir;
    $url = htmlspecialchars($_SERVER['PHP_SELF']) . '?' . http_build_query($query);
    return "<a href=\"$url\">$label</a>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Tickets</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --color-primary: #3498db;  /* Azul */
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
            background: var(--color-primary);
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
                        <h2 class="mb-4">Mis Tickets</h2>
                        
                        <!-- Formulario de filtrado -->
                        <form method="GET" class="filter-form">
                            <label for="status">Estado:</label>
                            <select name="status" id="status">
                                <option value="">Todos</option>
                                <option value="open" <?= $estado === 'open' ? 'selected' : '' ?>>Abierto</option>
                                <option value="in_progress" <?= $estado === 'in_progress' ? 'selected' : '' ?>>En progreso</option>
                                <option value="resolved" <?= $estado === 'resolved' ? 'selected' : '' ?>>Resuelto</option>
                                <option value="closed" <?= $estado === 'closed' ? 'selected' : '' ?>>Cerrado</option>
                            </select>

                            <label for="category">Categoría:</label>
                            <select name="category" id="category">
                                <option value="">Todas</option>
                                <option value="Hardware" <?= $categoria === 'Hardware' ? 'selected' : '' ?>>Hardware</option>
                                <option value="Software" <?= $categoria === 'Software' ? 'selected' : '' ?>>Software</option>
                                <option value="Red" <?= $categoria === 'Red' ? 'selected' : '' ?>>Red</option>
                                <option value="Otros" <?= $categoria === 'Otros' ? 'selected' : '' ?>>Otros</option>
                            </select>

                            <label for="start_date">Desde:</label>
                            <input type="date" name="start_date" id="start_date" value="<?= htmlspecialchars($fecha_inicio) ?>">

                            <label for="end_date">Hasta:</label>
                            <input type="date" name="end_date" id="end_date" value="<?= htmlspecialchars($fecha_fin) ?>">

                            <button type="submit" class="btn btn-primary">Filtrar</button>
                        </form>

                        <?php if (count($tickets) > 0): ?>
                        <table class="table table-hover tickets-table">
                            <thead>
                                <tr>
                                    <th><?= linkWithOrder('title', 'Título', $orderBy, $orderDir) ?></th>
                                    <th>Descripción</th>
                                    <th><?= linkWithOrder('category_name', 'Categoría', $orderBy, $orderDir) ?></th>
                                    <th><?= linkWithOrder('created_at', 'Fecha de creación', $orderBy, $orderDir) ?></th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tickets as $ticket): ?>
                                <tr>
                                    <td><?= htmlspecialchars($ticket['title']) ?></td>
                                    <td><?= htmlspecialchars($ticket['description']) ?></td>
                                    <td><?= htmlspecialchars($ticket['category_name']) ?></td>
                                    <td><?= htmlspecialchars($ticket['created_at']) ?></td>
                                    <td>
                                        <form action="eliminar_ticket.php" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este ticket?');">
                                            <input type="hidden" name="ticket_id" value="<?= $ticket['id'] ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                        </form>
                                        <a href="editar_ticket.php?id=<?= $ticket['id'] ?>" class="btn btn-primary btn-sm">Editar</a>
                                        <a href="ver_ticket.php?id=<?= $ticket['id'] ?>" class="btn btn-info btn-sm">Ver Comentarios</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php else: ?>
                        <p>No tienes tickets registrados.</p>
                        <?php endif; ?>
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