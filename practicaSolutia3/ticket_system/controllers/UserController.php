<?php
class UserController {
    private $userModel;
    
    public function __construct() {
        require_once BASE_PATH . 'ticket_system/models/User.php';
        $this->userModel = new User();
    }
    
    // Mostrar lista de usuarios
    public function index() {
        $users = $this->userModel->getAllUsers();
        require_once BASE_PATH . 'ticket_system/views/users/index.php';
    }
    
    // Mostrar formulario para crear usuario
    public function create() {
        $errors = [];
        require_once BASE_PATH . 'ticket_system/views/users/create.php';
    }
    
    // Procesar la creación de un usuario
    public function store() {
        $errors = [];
        
        // Validar datos
        if (empty($_POST['username'])) {
            $errors[] = "El nombre de usuario es obligatorio";
        } elseif ($this->userModel->usernameExists($_POST['username'])) {
            $errors[] = "El nombre de usuario ya está en uso";
        }
        
        if (empty($_POST['email'])) {
            $errors[] = "El correo electrónico es obligatorio";
        } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "El formato del correo electrónico no es válido";
        } elseif ($this->userModel->emailExists($_POST['email'])) {
            $errors[] = "El correo electrónico ya está en uso";
        }
        
        if (empty($_POST['password'])) {
            $errors[] = "La contraseña es obligatoria";
        } elseif (strlen($_POST['password']) < 6) {
            $errors[] = "La contraseña debe tener al menos 6 caracteres";
        }
        
        if (empty($_POST['role'])) {
            $errors[] = "El rol es obligatorio";
        }
        
        // Si hay errores, volver al formulario
        if (!empty($errors)) {
            require_once BASE_PATH . 'ticket_system/views/users/create.php';
            return;
        }
        
        // Crear usuario
        $userData = [
            'username' => $_POST['username'],
            'password' => $_POST['password'],
            'email' => $_POST['email'],
            'role' => $_POST['role'],
            'active' => isset($_POST['active']) ? 1 : 0
        ];
        
        if ($this->userModel->createUser($userData)) {
            $_SESSION['success_message'] = "Usuario creado correctamente";
            header('Location: index.php?controller=user&action=index');
            exit;
        } else {
            $errors[] = "Error al crear el usuario";
            require_once BASE_PATH . 'ticket_system/views/users/create.php';
        }
    }
    
    // Mostrar formulario para editar usuario
    public function edit($id = null) {
        if (!$id && isset($_GET['id'])) {
            $id = $_GET['id'];
        }
        
        $user = $this->userModel->getUserById($id);
        
        if (!$user) {
            $_SESSION['error_message'] = "Usuario no encontrado";
            header('Location: index.php?controller=user&action=index');
            exit;
        }
        
        $errors = [];
        require_once BASE_PATH . 'ticket_system/views/users/edit.php';
    }
    
    // Procesar la actualización de un usuario
    public function update() {
        $id = $_POST['id'] ?? null;
        $user = $this->userModel->getUserById($id);
        
        if (!$user) {
            $_SESSION['error_message'] = "Usuario no encontrado";
            header('Location: index.php?controller=user&action=index');
            exit;
        }
        
        $errors = [];
        
        // Validar datos
        if (empty($_POST['username'])) {
            $errors[] = "El nombre de usuario es obligatorio";
        } elseif ($this->userModel->usernameExists($_POST['username'], $id)) {
            $errors[] = "El nombre de usuario ya está en uso";
        }
        
        if (empty($_POST['email'])) {
            $errors[] = "El correo electrónico es obligatorio";
        } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "El formato del correo electrónico no es válido";
        } elseif ($this->userModel->emailExists($_POST['email'], $id)) {
            $errors[] = "El correo electrónico ya está en uso";
        }
        
        if (!empty($_POST['password']) && strlen($_POST['password']) < 6) {
            $errors[] = "La contraseña debe tener al menos 6 caracteres";
        }
        
        if (empty($_POST['role'])) {
            $errors[] = "El rol es obligatorio";
        }
        
        // Si hay errores, volver al formulario
        if (!empty($errors)) {
            require_once BASE_PATH . 'ticket_system/views/users/edit.php';
            return;
        }
        
        // Actualizar usuario
        $userData = [
            'username' => $_POST['username'],
            'password' => $_POST['password'] ?? '',
            'email' => $_POST['email'],
            'role' => $_POST['role'],
            'active' => isset($_POST['active']) ? 1 : 0
        ];
        
        if ($this->userModel->updateUser($id, $userData)) {
            $_SESSION['success_message'] = "Usuario actualizado correctamente";
            header('Location: index.php?controller=user&action=index');
            exit;
        } else {
            $errors[] = "Error al actualizar el usuario";
            require_once BASE_PATH . 'ticket_system/views/users/edit.php';
        }
    }
    
    // Eliminar un usuario
    public function delete() {
        $id = $_POST['id'] ?? ($_GET['id'] ?? null);
        
        if (!$id) {
            $_SESSION['error_message'] = "ID de usuario no especificado";
            header('Location: index.php?controller=user&action=index');
            exit;
        }
        
        if ($this->userModel->deleteUser($id)) {
            $_SESSION['success_message'] = "Usuario eliminado correctamente";
        } else {
            $_SESSION['error_message'] = "No se puede eliminar el usuario porque tiene tickets asignados";
        }
        
        header('Location: index.php?controller=user&action=index');
        exit;
    }
}
?>
