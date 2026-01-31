<?php
require_once '../includes/config.php';
requireLogin();

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$user_id = $_SESSION['user_id'];

try {
    switch ($action) {
        case 'list':
            listUsers($pdo, $user_id);
            break;
            
        default:
            jsonResponse(['success' => false, 'message' => 'Ungültige Aktion'], 400);
    }
} catch (Exception $e) {
    jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
}

function listUsers($pdo, $user_id) {
    // Nur genehmigte Benutzer anzeigen
    $stmt = $pdo->prepare("
        SELECT user_id, username, full_name, email
        FROM users
        WHERE is_approved = TRUE AND user_id != ?
        ORDER BY full_name
    ");
    $stmt->execute([$user_id]);
    $users = $stmt->fetchAll();
    
    jsonResponse(['success' => true, 'users' => $users]);
}
?>
