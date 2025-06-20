<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../src/config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("SELECT id, make, model, year, license_plate, vin, color FROM vehicles WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    $vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'vehicles' => $vehicles]);

} catch (PDOException $e) {
    error_log("Database error fetching vehicles: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error: Could not retrieve vehicles.']);
}
?> 