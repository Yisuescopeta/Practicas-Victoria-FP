<?php
// Definir la constante BASE_PATH
define('BASE_PATH', __DIR__ . '/');

// Iniciar sesión
session_start();

// Incluir los controladores
require_once 'ticket_system/controllers/UserController.php';
require_once 'ticket_system/controllers/CategoryController.php';
require_once 'ticket_system/controllers/ReportController.php';
require_once 'ticket_system/controllers/AdminController.php'; // Añadir esta línea

// Determinar el controlador a utilizar
$controller = $_GET['controller'] ?? 'admin';
$action = $_GET['action'] ?? 'dashboard';

// Crear la instancia del controlador correspondiente
switch ($controller) {
    case 'user':
        $controllerInstance = new UserController();
        break;
    case 'category':
        $controllerInstance = new CategoryController();
        break;
    case 'report':
        $controllerInstance = new ReportController();
        break;
    case 'admin':
        $controllerInstance = new AdminController();
        break;
    default:
        echo 'Controlador no encontrado: ' . $controller;
        exit;
}

// Llamar al método correspondiente
if (method_exists($controllerInstance, $action)) {
    $controllerInstance->$action();
} else {
    // Si el método no existe, mostrar un error
    echo 'Acción no encontrada: ' . $action . ' en el controlador ' . $controller;
}
?>
