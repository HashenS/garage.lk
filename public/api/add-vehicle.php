<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../src/config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
        echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $make = trim($_POST['make'] ?? '');
    $model = trim($_POST['model'] ?? '');
    $year = (int)($_POST['year'] ?? 0);
    $license_plate = trim($_POST['license_plate'] ?? '');
    $vin = trim($_POST['vin'] ?? '');
    $color = trim($_POST['color'] ?? '');

    // Basic validation
    if (empty($make) || empty($model) || empty($year) || empty($license_plate)) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields: Make, Model, Year, License Plate.']);
        exit();
    }

    if ($year < 1900 || $year > date('Y')) {
        echo json_encode(['success' => false, 'message' => 'Please enter a valid year.']);
        exit();
    }

    try {
        // Check if license plate or VIN already exists for this user
        $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM vehicles WHERE user_id = ? AND (license_plate = ? OR (vin IS NOT NULL AND vin = ?))");
        $stmt_check->execute([$user_id, $license_plate, $vin]);
        if ($stmt_check->fetchColumn() > 0) {
            echo json_encode(['success' => false, 'message' => 'A vehicle with this license plate or VIN already exists for your account.']);
            exit();
        }

        $stmt = $pdo->prepare("INSERT INTO vehicles (user_id, make, model, year, license_plate, vin, color) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $make, $model, $year, $license_plate, $vin, $color]);

        echo json_encode(['success' => true, 'message' => 'Vehicle added successfully!']);

    } catch (PDOException $e) {
        // Check for duplicate entry error specifically
        if ($e->getCode() == '23000') { // SQLSTATE for integrity constraint violation
            echo json_encode(['success' => false, 'message' => 'Duplicate entry for license plate or VIN. Please use unique values.']);
        } else {
            error_log("Database error: " . $e->getMessage()); // Log error for debugging
            echo json_encode(['success' => false, 'message' => 'Database error: Could not add vehicle.']);
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?> 