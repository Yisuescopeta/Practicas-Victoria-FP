<?php
require 'database.php';
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['id'], $_SESSION['role'])) {
    echo "Usuario no autenticado.";
    exit();
}

// Verificar que el ticket_id y comment_id estén presentes en la URL
if (isset($_GET['ticket_id'], $_GET['comment_id'])) {
    $ticket_id = $_GET['ticket_id'];
    $comment_id = $_GET['comment_id'];
    $user_id = $_SESSION['id'];
    $user_role = $_SESSION['role']; // 'admin', 'tech', 'client'

    // Obtener el comentario
    $stmt = $pdo->prepare("SELECT * FROM comments WHERE id = ? AND ticket_id = ?");
    $stmt->execute([$comment_id, $ticket_id]);
    $comment = $stmt->fetch();

    if ($comment) {
        // Verificar si el usuario tiene permisos para eliminar el comentario
        if ($comment['user_id'] == $user_id || $user_role === 'admin') {
            // Eliminar el comentario
            $deleteStmt = $pdo->prepare("DELETE FROM comments WHERE id = ?");
            if ($deleteStmt->execute([$comment_id])) {
                header("Location: ver_comentarios.php?ticket_id=$ticket_id");
                exit();
            } else {
                echo "Error al eliminar el comentario.";
            }
        } else {
            echo "No tienes permisos para eliminar este comentario.";
        }
    } else {
        echo "Comentario no encontrado.";
    }
} else {
    echo "Datos incompletos o parámetros no válidos.";
    exit();
}
?>
