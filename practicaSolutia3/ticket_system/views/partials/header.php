<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Tickets</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="ticket_system/css/styles.css">
    <style>
        /* Estilos para mantener el pie de página al fondo */
        html, body {
            height: 100%;
            margin: 0;
        }
        .wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh; /* Altura mínima del viewport */
        }
        .content {
            flex: 1; /* Hace que el contenido ocupe el espacio disponible */
        }
        footer {
            flex-shrink: 0; /* Evita que el pie de página se comprima */
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <div class="logo me-3">
                    <img src="https://camaradesevilla.com/wp-content/uploads/2024/07/S00-logo-Grupo-Solutia-v01-1.png" 
                         alt="Logo del Sistema" class="img-fluid" style="max-width: 150px;">
                </div>
                <a class="navbar-brand" href="index.php">Sistema de Tickets</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">Inicio</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?controller=report&action=index">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?controller=report&action=custom">Informes</a>
                        </li>
                    </ul>
                    <div class="d-flex align-items-center ms-auto">
                        <button id="theme-button" class="btn btn-primary-custom">
                            <i class="fas fa-moon me-2"></i>Modo Oscuro
                        </button>
                    </div>
                </div>
            </div>
        </nav>
        <div class="content">