<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

require 'database.php'; // Asegúrate de que este archivo tenga la conexión PDO correcta

// Verificar si el ID del ticket está presente en la URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("El ticket no existe.");
}

// Obtener el ID del ticket desde la URL (en lugar de $_POST)
$id_ticket = $_GET['id'];

// Obtener los datos del formulario
$titulo = $_POST['titulo'];
$descripcion = $_POST['descripcion'];
$estado = $_POST['estado'];

// Actualizar el ticket en la base de datos
$sql = "UPDATE tickets SET title = :titulo, description = :descripcion, status = :estado WHERE id = :id";
$stmt = $pdo->prepare($sql);

$stmt->bindParam(':titulo', $titulo);
$stmt->bindParam(':descripcion', $descripcion);
$stmt->bindParam(':estado', $estado);
$stmt->bindParam(':id', $id_ticket, PDO::PARAM_INT);

if ($stmt->execute()) {
    // Redirigir al usuario a la lista de tickets después de la actualización
    header('Location: misTickets.php');
    exit();
} else {
    echo "Error al actualizar el ticket.";
}
?>
