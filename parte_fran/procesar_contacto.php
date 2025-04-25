<?php
require 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $asunto = trim($_POST['asunto'] ?? '');
    $tecnico_id = trim($_POST['tecnico'] ?? '');
    $mensaje = trim($_POST['mensaje'] ?? '');

    if (empty($asunto) || empty($tecnico_id) || empty($mensaje)) {
        die("Error: Todos los campos son obligatorios");
    }

    try {
        // Obtener el email del técnico
        $stmt = $pdo->prepare("SELECT email FROM users WHERE id = ? AND role = 'tech'");
        $stmt->execute([$tecnico_id]);
        $tecnico = $stmt->fetch();

        if (!$tecnico) {
            die("Error: Técnico no encontrado");
        }

        // Configurar y enviar el correo
        $to = $tecnico['email'];
        $subject = "Mensaje del sistema: " . $asunto;
        $message = wordwrap($mensaje, 70, "\r\n");
        $headers = "From: sistema@tudominio.com\r\n";
        $headers .= "Reply-To: no-reply@tudominio.com\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        if (mail($to, $subject, $message, $headers)) {
            header("Location: clienteTecnico.php?success=1");
            exit;
        } else {
            die("Error al enviar el correo");
        }
        
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
} else {
    die("Acceso no permitido");
}