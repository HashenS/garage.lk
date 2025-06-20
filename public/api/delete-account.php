<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../src/config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
        echo json_encode(['success' => false, 'message' => 'Unauthorized access or not logged in as a customer.']);
        exit();
    }

    $user_id = $_SESSION['user_id'];

    try {
        // Start a transaction for atomicity
        $pdo->beginTransaction();

        // Delete user's vehicles (if any) - CASCADE should handle this if foreign key is set up correctly
        // However, explicitly deleting can be safer if CASCADE isn't perfectly configured or if other related tables exist without cascade.
        // For now, relying on CASCADE for `vehicles` due to the schema.sql definition, but noting this for future.

        // Delete the user from the database
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$user_id]);

        // Commit the transaction
        $pdo->commit();

        // Clear session variables and destroy the session after successful deletion
        $_SESSION = array();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();

        echo json_encode(['success' => true, 'message' => 'Your account has been successfully deleted.']);

    } catch (PDOException $e) {
        $pdo->rollBack(); // Rollback transaction on error
        error_log("Database error during account deletion: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error: Could not delete account. Please try again.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?> 