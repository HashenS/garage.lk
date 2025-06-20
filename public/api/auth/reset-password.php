<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../../src/config/database.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Validate required fields
    if (!isset($_POST['email']) || !isset($_POST['code']) || !isset($_POST['new_password']) || !isset($_POST['confirm_new_password'])) {
        throw new Exception("All fields are required.");
    }

    $email = trim($_POST['email']);
    $code = trim($_POST['code']);
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Invalid email format.");
    }

    // Check if passwords match
    if ($new_password !== $confirm_new_password) {
        throw new Exception("New password and confirm password do not match.");
    }

    // Password strength validation (optional, but recommended)
    if (strlen($new_password) < 8) {
        throw new Exception("Password must be at least 8 characters long.");
    }

    // Get user ID from email
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        throw new Exception("Invalid email address.");
    }

    $user_id = $user['id'];

    // Verify the reset code
    $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE user_id = ? AND code = ? AND expires_at > NOW()");
    $stmt->execute([$user_id, $code]);
    $reset_entry = $stmt->fetch();

    if (!$reset_entry) {
        throw new Exception("Invalid or expired reset code.");
    }

    // Hash the new password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Update the user's password
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->execute([$hashed_password, $user_id]);

    // Delete the used reset code
    $stmt = $pdo->prepare("DELETE FROM password_resets WHERE user_id = ?");
    $stmt->execute([$user_id]);

    echo json_encode([
        'success' => true,
        'message' => 'Your password has been reset successfully. You can now log in.'
    ]);

} catch (Exception $e) {
    error_log("Password Reset Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 