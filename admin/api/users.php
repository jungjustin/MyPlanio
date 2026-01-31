<?php
require_once '../../includes/config.php';
requireAdmin();

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'approve':
            approveUser($pdo);
            break;
            
        case 'delete':
            deleteUser($pdo);
            break;
            
        case 'toggle_admin':
            toggleAdmin($pdo);
            break;
            
        default:
            jsonResponse(['success' => false, 'message' => 'Ungültige Aktion'], 400);
    }
} catch (Exception $e) {
    jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
}

function approveUser($pdo) {
    $data = json_decode(file_get_contents('php://input'), true);
    $user_id = $data['user_id'] ?? null;
    
    if (!$user_id) {
        jsonResponse(['success' => false, 'message' => 'Benutzer ID fehlt'], 400);
    }
    
    $stmt = $pdo->prepare("UPDATE users SET is_approved = TRUE WHERE user_id = ?");
    $stmt->execute([$user_id]);
    
    jsonResponse(['success' => true]);
}

function deleteUser($pdo) {
    $data = json_decode(file_get_contents('php://input'), true);
    $user_id = $data['user_id'] ?? null;
    
    if (!$user_id) {
        jsonResponse(['success' => false, 'message' => 'Benutzer ID fehlt'], 400);
    }
    
    // Verhindere Löschung des Standard-Admin
    $checkStmt = $pdo->prepare("SELECT username FROM users WHERE user_id = ?");
    $checkStmt->execute([$user_id]);
    $user = $checkStmt->fetch();
    
    if ($user && $user['username'] === 'admin') {
        jsonResponse(['success' => false, 'message' => 'Standard-Admin kann nicht gelöscht werden'], 400);
    }
    
    $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    
    jsonResponse(['success' => true]);
}

function toggleAdmin($pdo) {
    $data = json_decode(file_get_contents('php://input'), true);
    $user_id = $data['user_id'] ?? null;
    $is_admin = $data['is_admin'] ?? false;
    
    if (!$user_id) {
        jsonResponse(['success' => false, 'message' => 'Benutzer ID fehlt'], 400);
    }
    
    $stmt = $pdo->prepare("UPDATE users SET is_admin = ? WHERE user_id = ?");
    $stmt->execute([$is_admin, $user_id]);
    
    jsonResponse(['success' => true]);
}
?>
