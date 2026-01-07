<?php
session_start();
require_once '../includes/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

$content = $_POST['content'] ?? '';
$content = trim(strip_tags($content));

if (empty($content)) {
    echo json_encode(['status' => 'error', 'message' => 'Message content is required']);
    exit;
}

// Determine User
$username = 'Visitor';
$is_admin = 0;

if (isset($_SESSION['admin_user'])) {
    $username = $_SESSION['admin_user'];
    $is_admin = ($_SESSION['admin_role'] ?? 'user') === 'admin' ? 1 : 0;
}

try {
    $pdo = getDB();
    
    // Check if board is enabled
    $stmt = $pdo->prepare("SELECT value FROM settings WHERE `key` = 'board_status'");
    $stmt->execute();
    $status = $stmt->fetchColumn();
    if ($status === 'disabled' && !$is_admin) {
        echo json_encode(['status' => 'error', 'message' => 'Message board is currently closed.']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO messages (username, content, is_admin, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$username, $content, $is_admin]);
    
    echo json_encode(['status' => 'success']);
} catch (PDOException $e) {
    // Log error in production, show generic here
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
