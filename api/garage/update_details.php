<?php
session_start();

header('Content-Type: application/json');

// Check if user is logged in and is a garage owner
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'garage') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

require_once __DIR__ . '/../../src/config/database.php';

$user_id = $_SESSION['user_id'];

// Get the POST data
$businessName = $_POST['businessName'] ?? '';
$phone = $_POST['phone'] ?? '';
$address = $_POST['address'] ?? '';
$latitude = $_POST['latitude'] ?? '';
$longitude = $_POST['longitude'] ?? '';
$googleMapsLink = $_POST['googleMapsLink'] ?? '';

// Basic validation
if (empty($businessName) || empty($phone) || empty($address) || empty($latitude) || empty($longitude)) {
    echo json_encode(['success' => false, 'message' => 'Business name, phone, address, latitude, and longitude are required.']);
    exit;
}

// Validate latitude and longitude (simple regex for basic numeric check)
if (!preg_match('/^-?([1-8]?\d(\.\d+)?|90(\.0+)?)$/', $latitude) ||
    !preg_match('/^-?((1?[0-7]?\d(\.\d+)?)|180(\.0+)?)$/', $longitude)) {
    echo json_encode(['success' => false, 'message' => 'Invalid latitude or longitude format.']);
    exit;
}

// Validate Google Maps Link if provided
if (!empty($googleMapsLink) && !filter_var($googleMapsLink, FILTER_VALIDATE_URL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid Google Maps Link format.']);
    exit;
}

try {
    // Fetch current garage info to check verification status
    $stmt = $pdo->prepare("SELECT verification_status, photos FROM garages WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $garage_info = $stmt->fetch(PDO::FETCH_ASSOC);

    $new_verification_status = $garage_info['verification_status'];
    $existing_photos = json_decode($garage_info['photos'] ?? '[]', true);

    // If current status is not verified, set to pending for re-verification
    if ($new_verification_status !== 'verified') {
        $new_verification_status = 'pending';
    }

    $upload_dir = __DIR__ . '/../../public/uploads/garage_photos/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $uploaded_photos = [];
    if (isset($_FILES['garagePhotos']) && count($_FILES['garagePhotos']['name']) > 0) {
        $total_files = count($_FILES['garagePhotos']['name']);

        if ($total_files > 5) {
            echo json_encode(['success' => false, 'message' => 'You can upload a maximum of 5 photos.']);
            exit;
        }

        for ($i = 0; $i < $total_files; $i++) {
            $file_name = $_FILES['garagePhotos']['name'][$i];
            $file_tmp = $_FILES['garagePhotos']['tmp_name'][$i];
            $file_size = $_FILES['garagePhotos']['size'][$i];
            $file_type = $_FILES['garagePhotos']['type'][$i];
            $file_error = $_FILES['garagePhotos']['error'][$i];

            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_ext = ['jpg', 'jpeg', 'png'];

            if (in_array($file_ext, $allowed_ext) && $file_error === 0 && $file_size <= 5 * 1024 * 1024) { // 5MB
                $new_file_name = uniqid('garage_') . '.' . $file_ext;
                $file_destination = $upload_dir . $new_file_name;

                if (move_uploaded_file($file_tmp, $file_destination)) {
                    $uploaded_photos[] = 'uploads/garage_photos/' . $new_file_name;
                } else {
                    error_log("Failed to move uploaded file: " . $file_tmp . " to " . $file_destination);
                    echo json_encode(['success' => false, 'message' => 'Failed to upload photo: ' . $file_name]);
                    exit;
                }
            } else {
                $error_message = 'Invalid file: ' . $file_name . '. ';
                if (!in_array($file_ext, $allowed_ext)) $error_message .= 'Only JPG, JPEG, PNG are allowed.';
                if ($file_size > 5 * 1024 * 1024) $error_message .= 'Max size is 5MB.';
                if ($file_error !== 0) $error_message .= 'Error code: ' . $file_error;

                echo json_encode(['success' => false, 'message' => $error_message]);
                exit;
            }
        }
    }

    // Combine existing and new photos. New uploads will replace old ones if any are provided.
    $photos_to_save = !empty($uploaded_photos) ? $uploaded_photos : $existing_photos;
    $photos_json = json_encode($photos_to_save);

    // Update garage details in the database
    $stmt = $pdo->prepare("UPDATE garages SET business_name = ?, phone = ?, address = ?, latitude = ?, longitude = ?, google_maps_link = ?, photos = ?, verification_status = ? WHERE user_id = ?");
    $stmt->execute([$businessName, $phone, $address, $latitude, $longitude, $googleMapsLink, $photos_json, $new_verification_status, $user_id]);

    // Update session variables (only if changes were successfully applied to DB)
    $_SESSION['business_name'] = $businessName;
    $_SESSION['verification_status'] = $new_verification_status;

    echo json_encode(['success' => true, 'message' => 'Garage details updated successfully. Verification status is now ' . $new_verification_status . '.']);

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    error_log("General error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An unexpected error occurred.']);
}

?> 