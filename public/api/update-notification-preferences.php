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
    $email_notifications = isset($_POST['email_notifications']) ? 1 : 0;
    $sms_notifications = isset($_POST['sms_notifications']) ? 1 : 0;

    try {
        $stmt = $pdo->prepare("UPDATE users SET email_notifications = ?, sms_notifications = ? WHERE id = ?");
        $stmt->execute([$email_notifications, $sms_notifications, $user_id]);

        echo json_encode(['success' => true, 'message' => 'Notification preferences updated successfully!']);

    } catch (PDOException $e) {
        error_log("Database error updating notification preferences: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error: Could not update notification preferences.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?> 