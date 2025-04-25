<?php
session_start();
include("database.php");

// Manejo del formulario de solicitud de recuperación
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Generar token y guardarlo en la base de datos
        $token = bin2hex(random_bytes(50));
        $stmt = $pdo->prepare("UPDATE users SET reset_token = :token, reset_expiry = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE id = :id");
        $stmt->execute(['token' => $token, 'id' => $user['id']]);

        // Enviar correo con enlace de restablecimiento
        $reset_link = "http://localhost/reset_password.php?token=$token";
        mail($email, "Recuperación de Contraseña", "Haga clic en el siguiente enlace para restablecer su contraseña: $reset_link");

        echo "Se ha enviado un correo con instrucciones para restablecer la contraseña.";
    } else {
        echo "Correo no encontrado.";
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            text-align: center;
            padding: 50px;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            max-width: 400px;
            margin: auto;
        }
        .form-group {
            margin-bottom: 15px;
        }
        input[type="email"] {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        .btn {
            background: #4CAF50;
            color: white;
            padding: 10px;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Recuperar Contraseña</h2>
        <form method="POST">
            <div class="form-group">
                <label for="email">Correo Electrónico:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <button type="submit" class="btn">Enviar Enlace</button>
        </form>
    </div>
</body>
</html>
