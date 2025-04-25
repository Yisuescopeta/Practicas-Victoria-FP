<?php
class User {
    private $db;
    
    public function __construct() {
        require_once 'ticket_system/config/database.php';
        $this->db = new Database();
    }
    
    /**
     * Obtener todos los técnicos
     * @return array Lista de técnicos
     */
    public function getAllTechnicians() {
        try {
            $sql = "SELECT id, username, email 
                    FROM users 
                    WHERE role = 'tech' 
                    ORDER BY username";
            
            return $this->db->query($sql);
        } catch (Exception $e) {
            error_log("Error en getAllTechnicians: " . $e->getMessage());
            return [];
        }
    }
    
    // Obtener todos los usuarios
    public function getAllUsers() {
        try {
            $sql = "SELECT id, username, email, role, created_at 
                    FROM users 
                    ORDER BY username";
            
            return $this->db->query($sql);
        } catch (Exception $e) {
            error_log("Error en getAllUsers: " . $e->getMessage());
            return [];
        }
    }
    
    // Obtener un usuario por ID
    public function getUserById($id) {
        try {
            $sql = "SELECT id, username, email, role, created_at 
                    FROM users 
                    WHERE id = ?";
            
            $result = $this->db->query($sql, [$id]);
            return $result[0] ?? null;
        } catch (Exception $e) {
            error_log("Error en getUserById: " . $e->getMessage());
            return null;
        }
    }
    
    // Crear un nuevo usuario
    public function createUser($userData) {
        try {
            $sql = "INSERT INTO users (username, password, email, role) 
                    VALUES (?, ?, ?, ?)";
            
            $params = [
                $userData['username'],
                password_hash($userData['password'], PASSWORD_DEFAULT),
                $userData['email'],
                $userData['role']
            ];
            
            $this->db->query($sql, $params);
            return true;
        } catch (Exception $e) {
            error_log("Error en createUser: " . $e->getMessage());
            return false;
        }
    }
    
    // Actualizar un usuario existente
    public function updateUser($id, $userData) {
        try {
            // Si la contraseña está vacía, no la actualizamos
            if (empty($userData['password'])) {
                $sql = "UPDATE users 
                        SET username = ?, email = ?, role = ? 
                        WHERE id = ?";
                
                $params = [
                    $userData['username'],
                    $userData['email'],
                    $userData['role'],
                    $id
                ];
            } else {
                $sql = "UPDATE users 
                        SET username = ?, password = ?, email = ?, role = ? 
                        WHERE id = ?";
                
                $params = [
                    $userData['username'],
                    password_hash($userData['password'], PASSWORD_DEFAULT),
                    $userData['email'],
                    $userData['role'],
                    $id
                ];
            }
            
            $this->db->query($sql, $params);
            return true;
        } catch (Exception $e) {
            error_log("Error en updateUser: " . $e->getMessage());
            return false;
        }
    }
    
    // Eliminar un usuario
    public function deleteUser($id) {
        try {
            // Primero verificamos si el usuario tiene tickets asignados
            $sql = "SELECT COUNT(*) as count FROM tickets WHERE user_id = ?";
            $result = $this->db->query($sql, [$id]);
            
            if ($result[0]['count'] > 0) {
                return false; // No podemos eliminar un usuario con tickets asignados
            }
            
            $sql = "DELETE FROM users WHERE id = ?";
            $this->db->query($sql, [$id]);
            return true;
        } catch (Exception $e) {
            error_log("Error en deleteUser: " . $e->getMessage());
            return false;
        }
    }
    
    // Verificar si un nombre de usuario ya existe
    public function usernameExists($username, $excludeId = null) {
        try {
            if ($excludeId) {
                $sql = "SELECT COUNT(*) as count FROM users WHERE username = ? AND id != ?";
                $result = $this->db->query($sql, [$username, $excludeId]);
            } else {
                $sql = "SELECT COUNT(*) as count FROM users WHERE username = ?";
                $result = $this->db->query($sql, [$username]);
            }
            
            return $result[0]['count'] > 0;
        } catch (Exception $e) {
            error_log("Error en usernameExists: " . $e->getMessage());
            return false;
        }
    }
    
    // Verificar si un email ya existe
    public function emailExists($email, $excludeId = null) {
        try {
            if ($excludeId) {
                $sql = "SELECT COUNT(*) as count FROM users WHERE email = ? AND id != ?";
                $result = $this->db->query($sql, [$email, $excludeId]);
            } else {
                $sql = "SELECT COUNT(*) as count FROM users WHERE email = ?";
                $result = $this->db->query($sql, [$email]);
            }
            
            return $result[0]['count'] > 0;
        } catch (Exception $e) {
            error_log("Error en emailExists: " . $e->getMessage());
            return false;
        }
    }
}
?>
