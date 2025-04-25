<?php
// Incluir el archivo de conexión a la base de datos
require_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ticket_id'])) {
    $ticket_id = $_POST['ticket_id'];

    try {
        // Iniciar la transacción para asegurarse de que ambas operaciones se realicen correctamente
        $pdo->beginTransaction();

        // Eliminar los archivos adjuntos relacionados con el ticket
        $stmt = $pdo->prepare("DELETE FROM attachments WHERE ticket_id = :ticket_id");
        $stmt->execute(['ticket_id' => $ticket_id]);

        // Ahora eliminar el ticket
        $stmt = $pdo->prepare("DELETE FROM tickets WHERE id = :ticket_id");
        $stmt->execute(['ticket_id' => $ticket_id]);

        // Confirmar la transacción
        $pdo->commit();

        // Redirigir al usuario después de eliminar el ticket (opcional)
        header('Location: misTickets.php'); // Cambia a la página que prefieras
        exit();
    } catch (PDOException $e) {
        // Si ocurre algún error, deshacer la transacción
        $pdo->rollBack();
        echo "Error al eliminar el ticket: " . $e->getMessage();
    }
} else {
    echo "No se ha recibido un ID de ticket válido.";
}
?>

