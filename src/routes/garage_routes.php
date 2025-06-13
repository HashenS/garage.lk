<?php
require_once '../controllers/GarageController.php';
require_once '../config/database.php';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize garage controller
$garageController = new GarageController($db);

// Handle CORS
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

// Route the request
switch ($method) {
    case 'POST':
        // Register new garage
        if (isset($_POST['action']) && $_POST['action'] === 'register') {
            $response = $garageController->registerGarage($_POST, $_FILES);
            echo json_encode($response);
        }
        // Verify garage
        else if (isset($_POST['action']) && $_POST['action'] === 'verify') {
            $response = $garageController->verifyGarage(
                $_POST['garage_id'],
                $_POST['admin_id'],
                $_POST['status'],
                $_POST['notes'] ?? ''
            );
            echo json_encode($response);
        }
        break;

    case 'GET':
        // Get pending verifications
        if (isset($_GET['action']) && $_GET['action'] === 'pending') {
            $response = $garageController->getPendingVerifications();
            echo json_encode($response);
        }
        // Search garages by location
        else if (isset($_GET['action']) && $_GET['action'] === 'search') {
            $response = $garageController->searchGarages(
                $_GET['latitude'],
                $_GET['longitude'],
                $_GET['radius'] ?? 10
            );
            echo json_encode($response);
        }
        // Get garage details
        else if (isset($_GET['action']) && $_GET['action'] === 'details') {
            $response = $garageController->getGarageDetails($_GET['garage_id']);
            echo json_encode($response);
        }
        break;

    default:
        // Method not allowed
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
?> 