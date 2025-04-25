<?php
class Ticket {
    private $db;
    
    public function __construct() {
        // Usar la función global getDB() de tu Database.php
        require_once __DIR__ . '/../config/Database.php';
        $this->db = getDB();
    }
    
    /**
     * Obtener todos los estados posibles de tickets
     * @return array Lista de estados
     */
    public function getAllStatuses() {
        return [
            'open' => 'Abierto',
            'in_progress' => 'En Progreso',
            'resolved' => 'Resuelto',
            'closed' => 'Cerrado'
        ];
    }
    
    /**
     * Crear un nuevo ticket
     * @param array $data Datos del ticket
     * @return int|bool ID del ticket creado o false en caso de error
     */
    public function create($data) {
        // Modificado para incluir el campo assigned_to
        $sql = "INSERT INTO tickets (title, description, status, priority, category_id, user_id, assigned_to, created_at, updated_at) 
                VALUES (:title, :description, :status, :priority, :category_id, :user_id, :assigned_to, NOW(), NOW())";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':title', $data['title']);
            $stmt->bindParam(':description', $data['description']);
            $stmt->bindParam(':status', $data['status']);
            $stmt->bindParam(':priority', $data['priority']);
            $stmt->bindParam(':category_id', $data['category_id']);
            $stmt->bindParam(':user_id', $data['user_id']);
            
            // Asignar técnico si está disponible, de lo contrario será NULL
            $assigned_to = isset($data['assigned_to']) ? $data['assigned_to'] : null;
            $stmt->bindParam(':assigned_to', $assigned_to);
            
            if ($stmt->execute()) {
                return $this->db->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error al crear ticket: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener un ticket por su ID
     * @param int $id ID del ticket
     * @return array|bool Datos del ticket o false si no existe
     */
    public function getById($id) {
        // Modificado para incluir el técnico asignado
        $sql = "SELECT t.*, c.name as category_name, u1.username as client_name, 
                u2.username as technician_name
                FROM tickets t 
                LEFT JOIN categories c ON t.category_id = c.id 
                LEFT JOIN users u1 ON t.user_id = u1.id 
                LEFT JOIN users u2 ON t.assigned_to = u2.id 
                WHERE t.id = :id";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener ticket: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Actualizar un ticket existente
     * @param int $id ID del ticket
     * @param array $data Datos a actualizar
     * @return bool Resultado de la operación
     */
    public function update($id, $data) {
        $sql = "UPDATE tickets SET ";
        $params = [];
        
        foreach ($data as $key => $value) {
            if ($key != 'id') {
                $sql .= "$key = :$key, ";
                $params[":$key"] = $value;
            }
        }
        
        $sql .= "updated_at = NOW() WHERE id = :id";
        $params[':id'] = $id;
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Error al actualizar ticket: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Asignar un ticket a un técnico
     * @param int $ticketId ID del ticket
     * @param int $technicianId ID del técnico
     * @return bool Resultado de la operación
     */
    public function assignToTechnician($ticketId, $technicianId) {
        $sql = "UPDATE tickets SET assigned_to = :technician_id, updated_at = NOW() WHERE id = :id";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':technician_id', $technicianId, PDO::PARAM_INT);
            $stmt->bindParam(':id', $ticketId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al asignar ticket: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Cambiar el estado de un ticket
     * @param int $ticketId ID del ticket
     * @param string $status Nuevo estado
     * @return bool Resultado de la operación
     */
    public function changeStatus($ticketId, $status) {
        $sql = "UPDATE tickets SET status = :status, updated_at = NOW() WHERE id = :id";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':id', $ticketId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al cambiar estado del ticket: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener todos los tickets con filtros opcionales
     * @param array $filters Filtros a aplicar
     * @return array Lista de tickets
     */
    public function getAll($filters = []) {
        // Modificado para incluir el técnico asignado
        $sql = "SELECT t.*, c.name as category_name, u1.username as client_name,
                u2.username as technician_name
                FROM tickets t 
                LEFT JOIN categories c ON t.category_id = c.id 
                LEFT JOIN users u1 ON t.user_id = u1.id 
                LEFT JOIN users u2 ON t.assigned_to = u2.id 
                WHERE 1=1 ";
        
        $params = [];
        
        // Aplicar filtros
        if (!empty($filters['status'])) {
            $sql .= "AND t.status = :status ";
            $params[':status'] = $filters['status'];
        }
        
        if (!empty($filters['priority'])) {
            $sql .= "AND t.priority = :priority ";
            $params[':priority'] = $filters['priority'];
        }
        
        if (!empty($filters['category_id'])) {
            $sql .= "AND t.category_id = :category_id ";
            $params[':category_id'] = $filters['category_id'];
        }
        
        if (!empty($filters['user_id'])) {
            $sql .= "AND t.user_id = :user_id ";
            $params[':user_id'] = $filters['user_id'];
        }
        
        // Filtro para técnico asignado
        if (!empty($filters['assigned_to'])) {
            $sql .= "AND t.assigned_to = :assigned_to ";
            $params[':assigned_to'] = $filters['assigned_to'];
        }
        
        // Ordenar por fecha de creación descendente (más recientes primero)
        $sql .= "ORDER BY t.created_at DESC";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener tickets: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener estadísticas de tickets por estado
     * @return array Estadísticas de tickets
     */
    public function getStatsByStatus() {
        $sql = "SELECT status, COUNT(*) as count FROM tickets GROUP BY status";
        
        try {
            $stmt = $this->db->query($sql);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $stats = [];
            foreach ($results as $row) {
                $stats[$row['status']] = $row['count'];
            }
            
            return $stats;
        } catch (PDOException $e) {
            error_log("Error al obtener estadísticas: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Eliminar un ticket
     * @param int $id ID del ticket
     * @return bool Resultado de la operación
     */
    public function delete($id) {
        $sql = "DELETE FROM tickets WHERE id = :id";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al eliminar ticket: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Buscar tickets por término de búsqueda
     * @param string $term Término de búsqueda
     * @return array Resultados de la búsqueda
     */
    public function search($term) {
        // Modificado para incluir el técnico asignado
        $sql = "SELECT t.*, c.name as category_name, u1.username as client_name,
                u2.username as technician_name
                FROM tickets t 
                LEFT JOIN categories c ON t.category_id = c.id 
                LEFT JOIN users u1 ON t.user_id = u1.id 
                LEFT JOIN users u2 ON t.assigned_to = u2.id 
                WHERE t.title LIKE :term OR t.description LIKE :term 
                ORDER BY t.created_at DESC";
        
        $term = "%$term%";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':term', $term);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al buscar tickets: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Generar reporte personalizado de tickets
     * @param string $startDate Fecha de inicio (YYYY-MM-DD)
     * @param string $endDate Fecha de fin (YYYY-MM-DD)
     * @param int $technician ID del técnico (opcional)
     * @param int $category ID de la categoría (opcional)
     * @param string $status Estado del ticket (opcional)
     * @return array Resultados del reporte
     */
    public function getCustomReport($startDate, $endDate, $technician = '', $category = '', $status = '') {
        try {
            $sql = "SELECT t.id, t.title, t.status, t.priority, c.name as category_name, 
                    t.created_at, t.updated_at, u1.username as client_name, 
                    u2.username as technician_name 
                    FROM tickets t 
                    LEFT JOIN categories c ON t.category_id = c.id 
                    LEFT JOIN users u1 ON t.user_id = u1.id 
                    LEFT JOIN users u2 ON t.assigned_to = u2.id 
                    WHERE t.created_at BETWEEN :start_date AND :end_date";
            
            $params = [
                ':start_date' => $startDate . ' 00:00:00',
                ':end_date' => $endDate . ' 23:59:59'
            ];
            
            if (!empty($technician)) {
                $sql .= " AND t.assigned_to = :technician";
                $params[':technician'] = $technician;
            }
            
            if (!empty($category)) {
                $sql .= " AND t.category_id = :category";
                $params[':category'] = $category;
            }
            
            if (!empty($status)) {
                $sql .= " AND t.status = :status";
                $params[':status'] = $status;
            }
            
            $sql .= " ORDER BY t.id DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en getCustomReport: " . $e->getMessage());
            return [];
        }
    }
}
?>
