<?php
// Iniciar la sesión para verificar si el usuario está autenticado
session_start();

// Verificar si la sesión está iniciada, de lo contrario redirigir al login
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

// Incluir el archivo de la base de datos (asumiendo que contiene la conexión a la base de datos)
require 'database.php';

// Consulta para obtener los tickets
$sql = "SELECT * FROM tickets WHERE user_id = :user_id"; // Asegúrate de personalizar esta consulta a tu esquema
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
    <link rel="stylesheet" href="estilodashboard.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <div class="logo">
                <img src="https://camaradesevilla.com/wp-content/uploads/2024/07/S00-logo-Grupo-Solutia-v01-1.png" alt="Logo del Sistema">
            </div>
            <div class="header-right">
                <div class="theme-toggle">
                    <button id="theme-button">Modo Oscuro</button>
                </div>
                <div class="user-menu">
                    <span>USUARIO ▼</span>
                    <div class="user-dropdown">
                        <a href="logout.php">Cerrar Sesión</a>
                    </div>
                </div>
            </div>
        </header>

        <nav class="navbar">
            <ul>
                <li><a href="dashboard.php">Panel</a></li>
                <li><a href="#">Mis Tickets</a></li>
                <li><a href="gestion_usuario.php">Perfil</a></li>
                <li><a href="clienteTecnico.php">Comunicación</a></li>
            </ul>
        </nav>

        <main class="main-content">
            <div class="dashboard-summary">
                <h2>Resumen</h2>
                <div class="summary-cards">
                    <div class="card">
                        <h3>Tickets Abiertos</h3>
                        <p><?php ?></p>
                    </div>
                    <div class="card">
                        <h3>Tickets Resueltos</h3>
                        <p>10</p>
                    </div>
                    <div class="card">
                        <h3>Total Tickets</h3>
                        <p>15</p>
                    </div>
                </div>
                <button class="new-ticket-button">+ Nuevo Ticket</button>
            </div>

            <div class="recent-tickets">
                <h2>Tickets Recientes</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Descripcion</th>
                            <th>Prioridad</th>
                            <th>Estado</th>
                            <th>Fecha de creación</th>
                            <th>Fecha actualización</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($tickets as $ticket) {?>
                        <tr>
                            <td><?php echo $ticket['id']; ?></td>
                            <td><?php echo $ticket['title']; ?></td>
                            <td><?php echo $ticket['description']; ?></td>
                            <td><?php echo $ticket['priority']; ?></td>
                            <td><?php echo $ticket['status']; ?></td>
                            <td><?php echo $ticket['created_at']; ?></td>
                            <td><?php echo $ticket['updated_at']; ?></td>
                        </tr>
                        <?php }?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>