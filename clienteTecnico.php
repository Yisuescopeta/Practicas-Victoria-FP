<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto - Sistema de Tickets</title>
    <link rel="stylesheet" href="estilocontacto.css">
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
            <div class="contact-form">
                <h2>Contacto</h2>
                <?php if (isset($_GET['success'])): ?>
                    <p class="success-message">Mensaje enviado con éxito.</p>
                <?php endif; ?>
                <form action="procesar_contacto.php" method="POST">
                    <div class="form-group">
                        <label for="asunto">Asunto:</label>
                        <input type="text" id="asunto" name="asunto" required>
                    </div>
                    <div class="form-group">
                        <label for="tecnico">Seleccionar Técnico:</label>
                        <select id="tecnico" name="tecnico" required>
                            <?php
                            require 'database.php';
                            $stmt = $pdo->prepare("SELECT id, username FROM users WHERE role = 'tech'");
                            $stmt->execute();
                            $tecnicos = $stmt->fetchAll();
                            foreach ($tecnicos as $tecnico) {
                                echo "<option value='" . htmlspecialchars($tecnico['id']) . "'>" . htmlspecialchars($tecnico['username']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="mensaje">Mensaje:</label>
                        <textarea id="mensaje" name="mensaje" rows="5" required></textarea>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="cancel-button">Cancelar</button>
                        <button type="submit" class="send-message-button">Enviar Mensaje</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
