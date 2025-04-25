<?php
require 'database.php';

// Verificar que el ticket_id esté presente en la URL
if (isset($_GET['ticket_id'])) {
    $ticket_id = $_GET['ticket_id'];

    // Obtener el ticket y sus comentarios
    $stmt = $pdo->prepare("SELECT t.*, u.username as cliente, c.name as categoria 
                           FROM tickets t
                           JOIN users u ON t.user_id = u.id
                           JOIN categories c ON t.category_id = c.id
                           WHERE t.id = ?");
    $stmt->execute([$ticket_id]);
    $ticket = $stmt->fetch();

    // Obtener los comentarios para el ticket
    $commentStmt = $pdo->prepare("SELECT c.comment, c.created_at, u.username
                                  FROM comments c
                                  JOIN users u ON c.user_id = u.id
                                  WHERE c.ticket_id = ?
                                  ORDER BY c.created_at ASC");
    $commentStmt->execute([$ticket_id]);
    $comments = $commentStmt->fetchAll();
} else {
    echo "Ticket no encontrado.";
    exit();
}

// Si se envió un nuevo comentario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment'])) {
    $new_comment = $_POST['comment'];
    $user_id = 1; // Asumimos que el usuario está autenticado con id = 1, modificar según tu sistema de autenticación
    $stmt = $pdo->prepare("INSERT INTO comments (ticket_id, user_id, comment) VALUES (?, ?, ?)");
    $stmt->execute([$ticket_id, $user_id, $new_comment]);
    header("Location: ver_comentarios.php?ticket_id=$ticket_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comentarios del Ticket #<?php echo $ticket['id']; ?></title>
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
            </div>
        </header>

        <nav class="navbar">
            <ul>
                <li><a href="panel_tecnico.php" class="active">Panel Técnico</a></li>
                <li><a href="gestionPerfilTecnico.php">Editar Perfil</a></li>
            </ul>
        </nav>

        <main class="main-content">
            <div class="ticket-details">
                <h2>Detalles del Ticket #<?php echo htmlspecialchars($ticket['id']); ?></h2>
                <p><strong>Título:</strong> <?php echo htmlspecialchars($ticket['title']); ?></p>
                <p><strong>Cliente:</strong> <?php echo htmlspecialchars($ticket['cliente']); ?></p>
                <p><strong>Categoría:</strong> <?php echo htmlspecialchars($ticket['categoria']); ?></p>
                <p><strong>Prioridad:</strong> <?php echo htmlspecialchars($ticket['priority']); ?></p>
                <p><strong>Estado:</strong> <?php echo htmlspecialchars($ticket['status']); ?></p>
                <p><strong>Creado el:</strong> <?php echo htmlspecialchars($ticket['created_at']); ?></p>

                <form method="POST" action="deleteTicket.php" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este ticket?');">
                    <input type="hidden" name="ticket_id" value="<?php echo htmlspecialchars($ticket['id']); ?>">
                    <button type="submit" class="btn btn-danger">Eliminar Ticket</button>
                </form>
            </div>

            <div class="chat">
    <h3>Historial de Comentarios</h3>
    <div class="chat-messages">
        <?php if (count($comments) > 0): ?>
            <?php foreach ($comments as $comment): ?>
                <div class="chat-message">
                    <strong><?php echo htmlspecialchars($comment['username']); ?>:</strong>
                    <p><?php echo nl2br(htmlspecialchars($comment['comment'])); ?></p>
                    <p><small>Publicado el <?php echo htmlspecialchars($comment['created_at']); ?></small></p>

                    <!-- Botón para eliminar comentario -->
                    <form action="eliminar_comentario.php" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este comentario?');">
                            <input type="hidden" name="comment_id" value="<?= $comentario['id'] ?>">
                            <input type="hidden" name="ticket_id" value="<?= $ticket_id ?>">
                            <button type="submit">Eliminar</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No hay comentarios para este ticket.</p>
        <?php endif; ?>
    </div>

    <!-- Formulario para agregar un nuevo comentario -->
    <form method="POST" action="ver_comentarios.php?ticket_id=<?php echo $ticket_id; ?>" class="comment-form">
        <textarea name="comment" rows="4" placeholder="Escribe tu comentario..." required></textarea>
        <button type="submit">Agregar Comentario</button>
    </form>
</div>

            <div class="back-button">
                <a href="dashboardTecnico.php" class="btn btn-primary">Volver al Panel</a>
            </div>
        </main>
    </div>
</body>
</html>
