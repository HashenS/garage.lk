<?php
session_start();
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Include database configuration
    require_once '../../../src/config/database.php';

    // Validate required fields
    if (!isset($_POST['email']) || !isset($_POST['password'])) {
        throw new Exception("Please provide both email and password");
    }

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Please enter a valid email address");
    }

    // Get user from database
    $stmt = $pdo->prepare("
        SELECT id, first_name, last_name, email, password, role, phone 
        FROM users 
        WHERE email = ?
    ");
    
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        throw new Exception("Invalid email or password");
    }

    // Set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['name'] = $user['first_name'] . ' ' . $user['last_name'];
    $_SESSION['email'] = $user['email'];

    // If user is a garage owner, get garage details
    if ($user['role'] === 'garage') {
        $stmt = $pdo->prepare("
            SELECT id, business_name, verification_status 
            FROM garages 
            WHERE user_id = ?
        ");
        $stmt->execute([$user['id']]);
        $garage = $stmt->fetch();
        
        if ($garage) {
            $_SESSION['garage_id'] = $garage['id'];
            $_SESSION['business_name'] = $garage['business_name'];
            $_SESSION['verification_status'] = $garage['verification_status'];
        }
    }

    // Return success response with redirect URL
    echo json_encode([
        'success' => true,
        'message' => 'Login successful! Redirecting...',
        'redirect' => $user['role'] === 'garage' ? 'garage-dashboard.php' : 
                     ($user['role'] === 'admin' ? 'admin-dashboard.php' : 'customer-dashboard.php')
    ]);

} catch (Exception $e) {
    // Log the error (in a production environment)
    error_log("Login Error: " . $e->getMessage());

    // Return error response
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 