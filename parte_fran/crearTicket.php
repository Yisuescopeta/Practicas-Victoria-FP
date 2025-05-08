<?php
session_start();
require 'database.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id'];

// Consulta a la base de datos para obtener las categorías
$sql = "SELECT * FROM categories";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $category_id = $_POST['category'];
    $priority = $_POST['priority'];
    $description = trim($_POST['description']);
    $attachment_path = null;

    if (empty($title) || empty($category_id) || empty($priority) || empty($description)) {
        echo "Todos los campos son obligatorios.";
        exit();
    }

    if (!empty($_FILES['attachment']['name'])) {
        $upload_dir = "uploads/";
   
        // Verifica si la carpeta de carga existe, si no, la crea
        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0777, true)) {
                echo "Error al crear la carpeta de subida.";
                exit();
            }
        }
   
        $filename = basename($_FILES["attachment"]["name"]);
        $target_file = $upload_dir . time() . "_" . $filename;
       
        if (move_uploaded_file($_FILES["attachment"]["tmp_name"], $target_file)) {
            $attachment_path = $target_file;
        } else {
            echo "Error al subir el archivo.";
            exit();
        }
    }

    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("INSERT INTO tickets (user_id, category_id, title, description, priority, status, created_at) VALUES (?, ?, ?, ?, ?, 'open', NOW())");
        $stmt->execute([$user_id, $category_id, $title, $description, $priority]);

        $ticket_id = $pdo->lastInsertId();

        if ($attachment_path) {
            $stmt = $pdo->prepare("INSERT INTO attachments (ticket_id, filename, filepath, filesize) VALUES (?, ?, ?, ?)");
            $stmt->execute([$ticket_id, $filename, $attachment_path, $_FILES['attachment']['size']]);
        }

        $pdo->commit();
        header("Location: dashboard.php");
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "Error al crear el ticket: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nuevo Ticket - Sistema de Tickets</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --color-primary: #3498db;
            --color-primary-dark: #e67e22;
            --color-bg: #f8f9fa;
            --color-text: #343a40;
            --color-card: #ffffff;
            --color-border: #dee2e6;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--color-bg);
            color: var(--color-text);
            transition: all 0.3s ease;
        }

        /* Header mejorado */
        .header {
            background: var(--color-card);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 15px 0;
            border-bottom: 2px solid var(--color-primary);
        }

        .user-menu {
            cursor: pointer;
            transition: all 0.3s;
            color: var(--color-primary);
        }

        .user-menu:hover {
            opacity: 0.8;
        }

        /* Formulario */
        .form-container {
            background-color: var(--color-card);
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .form-title {
            color: var(--color-primary);
            margin-bottom: 25px;
            font-weight: 700;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--color-primary);
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--color-border);
            border-radius: 6px;
            font-size: 16px;
            transition: all 0.3s ease;
            background-color: var(--color-card);
            color: var(--color-text);
        }

        .form-control:focus {
            border-color: var(--color-primary);
            box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.25);
            outline: none;
        }

        textarea.form-control {
            min-height: 150px;
            resize: vertical;
        }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 16px 12px;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 30px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.3s;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-cancel {
            background-color: #6c757d;
            color: white;
            border: none;
        }

        .btn-cancel:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
            color: white;
        }

        .btn-primary {
            background-color: var(--color-primary);
            color: white;
            border: none;
        }

        .btn-primary:hover {
            background-color: var(--color-primary-dark);
            transform: translateY(-2px);
            color: white;
        }

        /* Navbar lateral */
        .sidebar {
            background-color: var(--color-card);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .nav-link {
            color: var(--color-text);
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 5px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-link:hover, .nav-link.active {
            background-color: rgba(52, 152, 219, 0.1);
            color: var(--color-primary);
        }

        .nav-link i {
            width: 20px;
            text-align: center;
        }

        /* File input personalizado */
        .custom-file-input {
            position: relative;
            overflow: hidden;
            display: inline-block;
        }

        .custom-file-input input[type="file"] {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .custom-file-label {
            display: block;
            padding: 10px 15px;
            background-color: var(--color-card);
            border: 1px dashed var(--color-border);
            border-radius: 6px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .custom-file-label:hover {
            border-color: var(--color-primary);
            background-color: rgba(52, 152, 219, 0.05);
        }

        .main-title {
            display: flex;
            align-items: center;
            gap: 10px;
        }
    </style>
</head>
<body>
    <div class="container-fluid p-0">
        <header class="header">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="logo">
                        <img src="https://camaradesevilla.com/wp-content/uploads/2024/07/S00-logo-Grupo-Solutia-v01-1.png" 
                             alt="Logo" style="max-width: 150px;">
                    </div>
                    <div class="d-flex align-items-center gap-4">
                        <div class="user-menu position-relative">
                            <span class="d-flex align-items-center gap-2">
                                <i class="fas fa-user-circle"></i>
                                <?php echo htmlspecialchars($_SESSION['username']); ?> ▼
                            </span>
                            <div class="dropdown-menu position-absolute end-0 mt-2 shadow" 
                                 style="display: none; min-width: 180px; background-color: var(--color-card);">
                                <a href="gestionPerfilUsuario.php" class="dropdown-item d-flex align-items-center gap-2">
                                    <i class="fas fa-user-cog"></i> Mi Perfil
                                </a>
                                <a href="logout.php" class="dropdown-item d-flex align-items-center gap-2 text-danger">
                                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="container mt-4">
            <div class="row">
                <div class="col-md-3">
                    <nav class="sidebar">
                        <ul class="nav flex-column w-100">
                            <li class="nav-item">
                                <a class="nav-link d-flex align-items-center gap-2" href="dashboard.php">
                                    <i class="fas fa-tachometer-alt"></i> Panel
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link d-flex align-items-center gap-2" href="misTickets.php">
                                    <i class="fas fa-ticket-alt"></i> Mis Tickets
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active d-flex align-items-center gap-2" href="crearTicket.php">
                                    <i class="fas fa-plus-circle"></i> Nuevo Ticket
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link d-flex align-items-center gap-2" href="gestionPerfilUsuario.php">
                                    <i class="fas fa-user-cog"></i> Editar Perfil
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link d-flex align-items-center gap-2" href="clienteTecnico.php">
                                    <i class="fas fa-comments"></i> Comunicación
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>

                <div class="col-md-9">
                    <main class="main-content">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2 class="main-title">
                                <i class="fas fa-plus-circle"></i>
                                <span>Crear Nuevo Ticket</span>
                            </h2>
                            <a href="misTickets.php" class="btn btn-primary">
                                <i class="fas fa-ticket-alt me-1"></i> Ver Mis Tickets
                            </a>
                        </div>
                        
                        <div class="form-container">
                            <h3 class="form-title"><i class="fas fa-ticket-alt me-2"></i>Complete los datos del ticket</h3>
                            
                            <form action="crearTicket.php" method="POST" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="title"><i class="fas fa-heading me-2"></i>Título:</label>
                                    <input type="text" id="title" name="title" class="form-control" placeholder="Ingrese un título descriptivo" required>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="category"><i class="fas fa-tag me-2"></i>Categoría:</label>
                                            <select id="category" name="category" class="form-control" required>
                                                <?php foreach ($categorias as $categoria): ?>
                                                    <option value="<?= htmlspecialchars($categoria['id']) ?>">
                                                        <?= htmlspecialchars($categoria['name']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="priority"><i class="fas fa-exclamation-circle me-2"></i>Prioridad:</label>
                                            <select id="priority" name="priority" class="form-control" required>
                                                <option value="low">Baja</option>
                                                <option value="medium" selected>Media</option>
                                                <option value="high">Alta</option>
                                                <option value="urgent">Urgente</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="description"><i class="fas fa-align-left me-2"></i>Descripción:</label>
                                    <textarea id="description" name="description" class="form-control" 
                                              placeholder="Describa el problema con detalle..." required></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label><i class="fas fa-paperclip me-2"></i>Adjuntar archivo (opcional):</label>
                                    <div class="custom-file-input">
                                        <input type="file" id="attachment" name="attachment" class="form-control-file">
                                        <label for="attachment" class="custom-file-label">
                                            <i class="fas fa-cloud-upload-alt me-2"></i>
                                            <span id="file-name">Seleccionar archivo</span>
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="form-actions">
                                    <button type="button" class="btn btn-cancel" onclick="window.location.href='dashboard.php'">
                                        <i class="fas fa-times me-1"></i> Cancelar
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-check me-1"></i> Crear Ticket
                                    </button>
                                </div>
                            </form>
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Scripts personalizados -->
    <script>
        // Menú desplegable de usuario
        const userMenu = document.querySelector('.user-menu');
        const dropdownMenu = document.querySelector('.dropdown-menu');

        userMenu.addEventListener('click', () => {
            dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
        });

        // Cerrar menú al hacer clic fuera
        document.addEventListener('click', (e) => {
            if (!userMenu.contains(e.target)) {
                dropdownMenu.style.display = 'none';
            }
        });

        // Mostrar nombre de archivo seleccionado
        document.getElementById('attachment').addEventListener('change', function(e) {
            const fileName = e.target.files[0] ? e.target.files[0].name : 'Seleccionar archivo';
            document.getElementById('file-name').textContent = fileName;
        });

        // Función para el modo oscuro (mantenida de la versión original)
        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
        }
    </script>
</body>
</html>