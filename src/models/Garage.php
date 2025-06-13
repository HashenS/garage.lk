<?php
require_once 'BaseModel.php';

class Garage extends BaseModel {
    protected $table_name = 'garages';

    public function __construct($db) {
        parent::__construct($db);
    }

    // Register new garage
    public function registerGarage($data) {
        return $this->create($data);
    }

    // Get garage by user ID
    public function getGarageByUserId($user_id) {
        return $this->read(['user_id' => $user_id])[0] ?? null;
    }

    // Update verification status
    public function updateVerificationStatus($garage_id, $status, $admin_id) {
        $data = [
            'verification_status' => $status,
            'verification_date' => date('Y-m-d H:i:s')
        ];

        // Log admin action
        $this->logAdminAction($admin_id, "Updated verification status to $status for garage ID: $garage_id");

        return $this->update($garage_id, $data);
    }

    // Get all pending verifications
    public function getPendingVerifications() {
        return $this->read(['verification_status' => 'pending'], 'created_at DESC');
    }

    // Get verified garages
    public function getVerifiedGarages() {
        return $this->read(['verification_status' => 'verified'], 'business_name ASC');
    }

    // Search garages by location
    public function searchByLocation($latitude, $longitude, $radius_km = 10) {
        // Using Haversine formula to find garages within radius
        $query = "SELECT *, 
                 (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * 
                 cos(radians(longitude) - radians(?)) + 
                 sin(radians(?)) * sin(radians(latitude)))) AS distance 
                 FROM " . $this->table_name . "
                 WHERE verification_status = 'verified'
                 HAVING distance < ?
                 ORDER BY distance";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$latitude, $longitude, $latitude, $radius_km]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    // Get garage with all related documents
    public function getGarageWithDocuments($garage_id) {
        $query = "SELECT g.*, 
                 GROUP_CONCAT(d.document_type) as document_types,
                 GROUP_CONCAT(d.file_path) as document_paths
                 FROM " . $this->table_name . " g
                 LEFT JOIN garage_documents d ON g.id = d.garage_id
                 WHERE g.id = ?
                 GROUP BY g.id";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$garage_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    // Log admin action
    private function logAdminAction($admin_id, $action) {
        $query = "INSERT INTO admin_logs (admin_id, action) VALUES (?, ?)";
        try {
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$admin_id, $action]);
        } catch(PDOException $e) {
            echo "Error logging admin action: " . $e->getMessage();
            return false;
        }
    }
}
?> 