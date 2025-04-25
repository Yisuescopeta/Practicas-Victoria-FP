<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tech') {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Técnico - Sistema de Tickets</title>
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
                    <span><?php echo htmlspecialchars($_SESSION['username']); ?> ▼</span>
                </div>
            </div>
        </header>

        <nav class="navbar">
            <ul>
                <li><a href="#">Panel</a></li>
                <li><a href="#">Tickets Asignados</a></li>
                <li><a href="logout.php">Cerrar Sesión</a></li>
            </ul>
        </nav>

        <main class="main-content">
            <h2>Bienvenido, Técnico</h2>
            <!-- Contenido específico para técnicos -->
        </main>
    </div>
</body>
</html>