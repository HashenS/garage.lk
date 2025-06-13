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
    $required_fields = ['first_name', 'last_name', 'email', 'phone', 'password', 'role'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            throw new Exception("Please fill in all required fields");
        }
    }

    // Validate email
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Please enter a valid email address");
    }

    // Validate phone number for Sri Lankan format
    $phone = trim($_POST['phone']);
    if (strlen($phone) < 10 || strlen($phone) > 12) {
        throw new Exception("Phone number must be between 10 and 12 digits (e.g., 0771234567 or +94771234567)");
    }

    // Remove any non-digit characters except the + sign at the start
    $phone = preg_replace('/[^0-9+]/', '', $phone);
    
    // Check if it's a valid Sri Lankan number
    if (!preg_match('/^(\+94|0)[1-9][0-9]{8}$/', $phone)) {
        throw new Exception("Please enter a valid Sri Lankan phone number (e.g., 0771234567 or +94771234567)");
    }

    // Standardize phone number format to +94 format
    if (substr($phone, 0, 1) === '0') {
        $phone = '+94' . substr($phone, 1);
    }

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$_POST['email']]);
    if ($stmt->rowCount() > 0) {
        throw new Exception("This email address is already registered. Please use a different email or login to your account.");
    }

    // Hash password
    $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Begin transaction
    $pdo->beginTransaction();

    try {
        // Insert user
        $stmt = $pdo->prepare("
            INSERT INTO users (first_name, last_name, email, phone, password, role)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $_POST['first_name'],
            $_POST['last_name'],
            $_POST['email'],
            $phone,
            $password_hash,
            $_POST['role']
        ]);

        $user_id = $pdo->lastInsertId();

        // If user is a garage owner, create garage entry
        if ($_POST['role'] === 'garage') {
            $stmt = $pdo->prepare("
                INSERT INTO garages (user_id, business_name, business_registration_number, address, latitude, longitude, phone, email)
                VALUES (?, ?, '', '', 0, 0, ?, ?)
            ");

            $stmt->execute([
                $user_id,
                $_POST['first_name'] . "'s Garage",
                $phone,
                $_POST['email']
            ]);
        }

        // Commit transaction
        $pdo->commit();

        // Set session variables
        $_SESSION['user_id'] = $user_id;
        $_SESSION['role'] = $_POST['role'];
        $_SESSION['name'] = $_POST['first_name'] . ' ' . $_POST['last_name'];
        $_SESSION['email'] = $_POST['email'];

        // Return success response
        echo json_encode([
            'success' => true,
            'message' => 'Account created successfully! Redirecting to dashboard...',
            'redirect' => $_POST['role'] === 'garage' ? 'garage-dashboard.php' : 'customer-dashboard.php'
        ]);

    } catch (Exception $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        throw $e;
    }

} catch (Exception $e) {
    // Log the error (in a production environment)
    error_log("Registration Error: " . $e->getMessage());

    // Return error response
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 