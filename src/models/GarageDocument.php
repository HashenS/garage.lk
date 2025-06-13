<?php
require_once 'BaseModel.php';

class GarageDocument extends BaseModel {
    protected $table_name = 'garage_documents';

    public function __construct($db) {
        parent::__construct($db);
    }

    // Upload new document
    public function uploadDocument($garage_id, $document_type, $file_path) {
        $data = [
            'garage_id' => $garage_id,
            'document_type' => $document_type,
            'file_path' => $file_path,
            'verification_status' => 'pending'
        ];

        return $this->create($data);
    }

    // Get all documents for a garage
    public function getGarageDocuments($garage_id) {
        return $this->read(['garage_id' => $garage_id], 'upload_date DESC');
    }

    // Update document verification status
    public function updateVerificationStatus($document_id, $status, $admin_id) {
        $data = [
            'verification_status' => $status
        ];

        // Log admin action
        $this->logAdminAction($admin_id, "Updated document verification status to $status for document ID: $document_id");

        return $this->update($document_id, $data);
    }

    // Get all pending document verifications
    public function getPendingDocumentVerifications() {
        return $this->read(['verification_status' => 'pending'], 'upload_date DESC');
    }

    // Validate document type
    public function validateDocumentType($type) {
        $valid_types = ['brn', 'nic', 'utility_bill', 'garage_photo'];
        return in_array($type, $valid_types);
    }

    // Handle file upload
    public function handleFileUpload($file, $document_type) {
        if (!$this->validateDocumentType($document_type)) {
            return ['success' => false, 'message' => 'Invalid document type'];
        }

        $upload_dir = '../uploads/documents/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'pdf'];

        if (!in_array($file_extension, $allowed_extensions)) {
            return ['success' => false, 'message' => 'Invalid file type'];
        }

        $file_name = uniqid() . '_' . $document_type . '.' . $file_extension;
        $target_path = $upload_dir . $file_name;

        if (move_uploaded_file($file['tmp_name'], $target_path)) {
            return [
                'success' => true,
                'file_path' => 'uploads/documents/' . $file_name
            ];
        }

        return ['success' => false, 'message' => 'Failed to upload file'];
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