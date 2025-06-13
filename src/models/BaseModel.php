<?php
class BaseModel {
    protected $conn;
    protected $table_name;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create new record
    public function create($data) {
        $fields = array_keys($data);
        $values = array_values($data);
        $placeholders = str_repeat('?,', count($fields) - 1) . '?';
        
        $query = "INSERT INTO " . $this->table_name . " 
                 (" . implode(',', $fields) . ") 
                 VALUES (" . $placeholders . ")";

        try {
            $stmt = $this->conn->prepare($query);
            return $stmt->execute($values);
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    // Read all records
    public function read($conditions = [], $order_by = null, $limit = null) {
        $query = "SELECT * FROM " . $this->table_name;
        
        if (!empty($conditions)) {
            $query .= " WHERE " . implode(' AND ', array_map(function($key) {
                return "$key = ?";
            }, array_keys($conditions)));
        }
        
        if ($order_by) {
            $query .= " ORDER BY " . $order_by;
        }
        
        if ($limit) {
            $query .= " LIMIT " . $limit;
        }

        try {
            $stmt = $this->conn->prepare($query);
            if (!empty($conditions)) {
                $stmt->execute(array_values($conditions));
            } else {
                $stmt->execute();
            }
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    // Update record
    public function update($id, $data) {
        $fields = array_keys($data);
        $set_clause = implode('=?,', $fields) . '=?';
        
        $query = "UPDATE " . $this->table_name . " 
                 SET " . $set_clause . " 
                 WHERE id = ?";

        try {
            $stmt = $this->conn->prepare($query);
            $values = array_values($data);
            $values[] = $id;
            return $stmt->execute($values);
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    // Delete record
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";

        try {
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$id]);
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    // Find by ID
    public function findById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
}
?> 