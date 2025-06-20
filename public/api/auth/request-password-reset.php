<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../../src/config/database.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    if (!isset($_POST['email'])) {
        throw new Exception("Email is required.");
    }

    $email = trim($_POST['email']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Invalid email format.");
    }

    // Check if user exists
    $stmt = $pdo->prepare("SELECT id, phone FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        // For security, do not reveal if the email exists or not
        throw new Exception("If your email is registered, a password reset code has been sent.");
    }

    $user_id = $user['id'];
    $phone = $user['phone'];

    // Generate a unique reset code
    $code = rand(100000, 999999); // 6-digit code
    $expires_at = date('Y-m-d H:i:s', strtotime('+15 minutes')); // Code valid for 15 minutes

    // Store the code in the database
    // First, delete any existing codes for this user
    $stmt = $pdo->prepare("DELETE FROM password_resets WHERE user_id = ?");
    $stmt->execute([$user_id]);

    $stmt = $pdo->prepare("INSERT INTO password_resets (user_id, code, expires_at) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $code, $expires_at]);

    // Simulate sending SMS
    error_log("Password Reset Code for user $email (Phone: $phone): $code. Expires at: $expires_at");

    echo json_encode([
        'success' => true,
        'message' => 'A password reset code has been sent to your registered phone number. It is valid for 15 minutes.'
    ]);

} catch (Exception $e) {
    error_log("Password Reset Request Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 