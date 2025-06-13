<?php
require_once '../models/Garage.php';
require_once '../models/GarageDocument.php';

class GarageController {
    private $garage;
    private $garageDocument;
    private $db;

    public function __construct($db) {
        $this->db = $db;
        $this->garage = new Garage($db);
        $this->garageDocument = new GarageDocument($db);
    }

    // Register new garage
    public function registerGarage($data, $files) {
        try {
            // Start transaction
            $this->db->beginTransaction();

            // Register garage
            $garage_data = [
                'user_id' => $data['user_id'],
                'business_name' => $data['business_name'],
                'business_registration_number' => $data['brn'],
                'owner_name' => $data['owner_name'],
                'owner_nic' => $data['owner_nic'],
                'address' => $data['address'],
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'verification_status' => 'pending'
            ];

            $garage_id = $this->garage->registerGarage($garage_data);

            if (!$garage_id) {
                throw new Exception("Failed to register garage");
            }

            // Handle document uploads
            $required_documents = ['brn', 'nic', 'utility_bill', 'garage_photo'];
            foreach ($required_documents as $doc_type) {
                if (!isset($files[$doc_type])) {
                    throw new Exception("Missing required document: $doc_type");
                }

                $upload_result = $this->garageDocument->handleFileUpload($files[$doc_type], $doc_type);
                if (!$upload_result['success']) {
                    throw new Exception($upload_result['message']);
                }

                $this->garageDocument->uploadDocument($garage_id, $doc_type, $upload_result['file_path']);
            }

            // Commit transaction
            $this->db->commit();
            return ['success' => true, 'message' => 'Garage registered successfully'];

        } catch (Exception $e) {
            // Rollback transaction on error
            $this->db->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // Verify garage
    public function verifyGarage($garage_id, $admin_id, $status, $notes = '') {
        try {
            // Start transaction
            $this->db->beginTransaction();

            // Update garage verification status
            if (!$this->garage->updateVerificationStatus($garage_id, $status, $admin_id)) {
                throw new Exception("Failed to update garage verification status");
            }

            // Update all associated documents
            $documents = $this->garageDocument->getGarageDocuments($garage_id);
            foreach ($documents as $doc) {
                if (!$this->garageDocument->updateVerificationStatus($doc['id'], $status, $admin_id)) {
                    throw new Exception("Failed to update document verification status");
                }
            }

            // Send email notification to garage owner
            $this->sendVerificationEmail($garage_id, $status, $notes);

            // Commit transaction
            $this->db->commit();
            return ['success' => true, 'message' => 'Garage verification completed successfully'];

        } catch (Exception $e) {
            // Rollback transaction on error
            $this->db->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // Get pending verifications
    public function getPendingVerifications() {
        $garages = $this->garage->getPendingVerifications();
        $result = [];

        foreach ($garages as $garage) {
            $documents = $this->garageDocument->getGarageDocuments($garage['id']);
            $garage['documents'] = $documents;
            $result[] = $garage;
        }

        return $result;
    }

    // Search garages
    public function searchGarages($latitude, $longitude, $radius_km = 10) {
        return $this->garage->searchByLocation($latitude, $longitude, $radius_km);
    }

    // Get garage details
    public function getGarageDetails($garage_id) {
        return $this->garage->getGarageWithDocuments($garage_id);
    }

    // Send verification email
    private function sendVerificationEmail($garage_id, $status, $notes) {
        $garage = $this->garage->findById($garage_id);
        if (!$garage) {
            return false;
        }

        $subject = "Garage.lk - Verification Status Update";
        $message = "Dear " . $garage['business_name'] . ",\n\n";
        $message .= "Your garage verification status has been updated to: " . strtoupper($status) . "\n\n";
        
        if ($notes) {
            $message .= "Notes: " . $notes . "\n\n";
        }

        if ($status === 'verified') {
            $message .= "Congratulations! Your garage has been verified. You can now start accepting bookings and managing your services.\n\n";
        } elseif ($status === 'rejected') {
            $message .= "Your garage verification has been rejected. Please review the notes above and submit a new application.\n\n";
        }

        $message .= "Best regards,\nGarage.lk Team";

        // Send email using your preferred email service
        // mail($garage['email'], $subject, $message);
        
        return true;
    }
}
?> 