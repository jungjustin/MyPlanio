<?php
require_once '../includes/config.php';
requireLogin();

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$user_id = $_SESSION['user_id'];

try {
    switch ($action) {
        case 'list':
            listGroups($pdo, $user_id);
            break;
            
        case 'create':
            createGroup($pdo, $user_id);
            break;
            
        case 'update':
            updateGroup($pdo, $user_id);
            break;
            
        case 'delete':
            deleteGroup($pdo, $user_id);
            break;
            
        case 'members':
            getMembers($pdo, $user_id);
            break;
            
        case 'add_member':
            addMember($pdo, $user_id);
            break;
            
        case 'remove_member':
            removeMember($pdo, $user_id);
            break;
            
        case 'leave':
            leaveGroup($pdo, $user_id);
            break;
            
        default:
            jsonResponse(['success' => false, 'message' => 'Ungültige Aktion'], 400);
    }
} catch (Exception $e) {
    jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
}

function listGroups($pdo, $user_id) {
    $stmt = $pdo->prepare("
        SELECT g.*, 
               gm.is_admin as user_is_admin,
               (SELECT COUNT(*) FROM group_members WHERE group_id = g.group_id) as member_count
        FROM groups_table g
        INNER JOIN group_members gm ON g.group_id = gm.group_id
        WHERE gm.user_id = ?
        ORDER BY g.group_name
    ");
    $stmt->execute([$user_id]);
    $groups = $stmt->fetchAll();
    
    jsonResponse(['success' => true, 'groups' => $groups]);
}

function createGroup($pdo, $user_id) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $group_name = trim($data['group_name'] ?? '');
    $description = trim($data['description'] ?? '');
    $color = $data['color'] ?? '#ffb3ba';
    
    if (empty($group_name)) {
        jsonResponse(['success' => false, 'message' => 'Gruppenname erforderlich'], 400);
    }
    
    $pdo->beginTransaction();
    
    try {
        // Gruppe erstellen
        $stmt = $pdo->prepare("
            INSERT INTO groups_table (group_name, created_by, description, color)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$group_name, $user_id, $description, $color]);
        $group_id = $pdo->lastInsertId();
        
        // Ersteller als Admin-Mitglied hinzufügen
        $memberStmt = $pdo->prepare("
            INSERT INTO group_members (group_id, user_id, is_admin)
            VALUES (?, ?, TRUE)
        ");
        $memberStmt->execute([$group_id, $user_id]);
        
        $pdo->commit();
        jsonResponse(['success' => true, 'group_id' => $group_id]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function updateGroup($pdo, $user_id) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $group_id = $data['group_id'] ?? null;
    $group_name = trim($data['group_name'] ?? '');
    $description = trim($data['description'] ?? '');
    $color = $data['color'] ?? '#ffb3ba';
    
    if (!$group_id || empty($group_name)) {
        jsonResponse(['success' => false, 'message' => 'Gruppe ID und Name erforderlich'], 400);
    }
    
    // Prüfen ob Benutzer Admin der Gruppe ist
    $checkStmt = $pdo->prepare("
        SELECT 1 FROM group_members 
        WHERE group_id = ? AND user_id = ? AND is_admin = TRUE
    ");
    $checkStmt->execute([$group_id, $user_id]);
    
    if (!$checkStmt->fetch()) {
        jsonResponse(['success' => false, 'message' => 'Keine Berechtigung'], 403);
    }
    
    $stmt = $pdo->prepare("
        UPDATE groups_table 
        SET group_name = ?, description = ?, color = ?
        WHERE group_id = ?
    ");
    $stmt->execute([$group_name, $description, $color, $group_id]);
    
    jsonResponse(['success' => true]);
}

function deleteGroup($pdo, $user_id) {
    $data = json_decode(file_get_contents('php://input'), true);
    $group_id = $data['group_id'] ?? null;
    
    if (!$group_id) {
        jsonResponse(['success' => false, 'message' => 'Gruppe ID fehlt'], 400);
    }
    
    // Nur Ersteller kann Gruppe löschen
    $stmt = $pdo->prepare("DELETE FROM groups_table WHERE group_id = ? AND created_by = ?");
    $stmt->execute([$group_id, $user_id]);
    
    if ($stmt->rowCount() > 0) {
        jsonResponse(['success' => true]);
    } else {
        jsonResponse(['success' => false, 'message' => 'Gruppe nicht gefunden oder keine Berechtigung'], 403);
    }
}

function getMembers($pdo, $user_id) {
    $group_id = $_GET['group_id'] ?? null;
    
    if (!$group_id) {
        jsonResponse(['success' => false, 'message' => 'Gruppe ID fehlt'], 400);
    }
    
    // Prüfen ob Benutzer Mitglied ist
    $checkStmt = $pdo->prepare("SELECT 1 FROM group_members WHERE group_id = ? AND user_id = ?");
    $checkStmt->execute([$group_id, $user_id]);
    
    if (!$checkStmt->fetch()) {
        jsonResponse(['success' => false, 'message' => 'Keine Berechtigung'], 403);
    }
    
    $stmt = $pdo->prepare("
        SELECT u.user_id, u.username, u.full_name, gm.is_admin, gm.joined_at
        FROM group_members gm
        INNER JOIN users u ON gm.user_id = u.user_id
        WHERE gm.group_id = ?
        ORDER BY gm.is_admin DESC, u.full_name
    ");
    $stmt->execute([$group_id]);
    $members = $stmt->fetchAll();
    
    jsonResponse(['success' => true, 'members' => $members]);
}

function addMember($pdo, $user_id) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $group_id = $data['group_id'] ?? null;
    $new_user_id = $data['user_id'] ?? null;
    
    if (!$group_id || !$new_user_id) {
        jsonResponse(['success' => false, 'message' => 'Gruppe ID und Benutzer ID erforderlich'], 400);
    }
    
    // Prüfen ob Benutzer Admin der Gruppe ist
    $checkStmt = $pdo->prepare("
        SELECT 1 FROM group_members 
        WHERE group_id = ? AND user_id = ? AND is_admin = TRUE
    ");
    $checkStmt->execute([$group_id, $user_id]);
    
    if (!$checkStmt->fetch()) {
        jsonResponse(['success' => false, 'message' => 'Keine Berechtigung'], 403);
    }
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO group_members (group_id, user_id, is_admin)
            VALUES (?, ?, FALSE)
        ");
        $stmt->execute([$group_id, $new_user_id]);
        jsonResponse(['success' => true]);
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            jsonResponse(['success' => false, 'message' => 'Benutzer ist bereits Mitglied'], 400);
        }
        throw $e;
    }
}

function removeMember($pdo, $user_id) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $group_id = $data['group_id'] ?? null;
    $remove_user_id = $data['user_id'] ?? null;
    
    if (!$group_id || !$remove_user_id) {
        jsonResponse(['success' => false, 'message' => 'Gruppe ID und Benutzer ID erforderlich'], 400);
    }
    
    // Prüfen ob Benutzer Admin der Gruppe ist
    $checkStmt = $pdo->prepare("
        SELECT 1 FROM group_members 
        WHERE group_id = ? AND user_id = ? AND is_admin = TRUE
    ");
    $checkStmt->execute([$group_id, $user_id]);
    
    if (!$checkStmt->fetch()) {
        jsonResponse(['success' => false, 'message' => 'Keine Berechtigung'], 403);
    }
    
    // Prüfen ob zu entfernender Benutzer der Ersteller ist
    $creatorStmt = $pdo->prepare("SELECT created_by FROM groups_table WHERE group_id = ?");
    $creatorStmt->execute([$group_id]);
    $creator = $creatorStmt->fetch();
    
    if ($creator && $creator['created_by'] == $remove_user_id) {
        jsonResponse(['success' => false, 'message' => 'Gruppenersteller kann nicht entfernt werden'], 400);
    }
    
    $stmt = $pdo->prepare("DELETE FROM group_members WHERE group_id = ? AND user_id = ?");
    $stmt->execute([$group_id, $remove_user_id]);
    
    jsonResponse(['success' => true]);
}

function leaveGroup($pdo, $user_id) {
    $data = json_decode(file_get_contents('php://input'), true);
    $group_id = $data['group_id'] ?? null;
    
    if (!$group_id) {
        jsonResponse(['success' => false, 'message' => 'Gruppe ID fehlt'], 400);
    }
    
    // Prüfen ob Benutzer der Ersteller ist
    $creatorStmt = $pdo->prepare("SELECT created_by FROM groups_table WHERE group_id = ?");
    $creatorStmt->execute([$group_id]);
    $creator = $creatorStmt->fetch();
    
    if ($creator && $creator['created_by'] == $user_id) {
        jsonResponse(['success' => false, 'message' => 'Gruppenersteller kann die Gruppe nicht verlassen'], 400);
    }
    
    $stmt = $pdo->prepare("DELETE FROM group_members WHERE group_id = ? AND user_id = ?");
    $stmt->execute([$group_id, $user_id]);
    
    jsonResponse(['success' => true]);
}
?>
