<?php
require_once '../includes/config.php';
requireLogin();

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$user_id = $_SESSION['user_id'];

try {
    switch ($action) {
        case 'list':
            listEvents($pdo, $user_id);
            break;
            
        case 'save':
            saveEvent($pdo, $user_id);
            break;
            
        case 'delete':
            deleteEvent($pdo, $user_id);
            break;
            
        case 'participants':
            getParticipants($pdo, $user_id);
            break;
            
        default:
            jsonResponse(['success' => false, 'message' => 'Ungültige Aktion'], 400);
    }
} catch (Exception $e) {
    jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
}

function listEvents($pdo, $user_id) {
    // Private Events des Benutzers
    $privateStmt = $pdo->prepare("
        SELECT e.*, NULL as group_name, u.full_name as created_by_name
        FROM events e
        LEFT JOIN users u ON e.created_by = u.user_id
        WHERE e.created_by = ? AND e.is_private = TRUE
    ");
    $privateStmt->execute([$user_id]);
    $privateEvents = $privateStmt->fetchAll();
    
    // Gruppen-Events
    $groupStmt = $pdo->prepare("
        SELECT e.*, g.group_name, u.full_name as created_by_name
        FROM events e
        INNER JOIN groups_table g ON e.group_id = g.group_id
        INNER JOIN group_members gm ON g.group_id = gm.group_id
        LEFT JOIN users u ON e.created_by = u.user_id
        WHERE gm.user_id = ? AND e.is_private = FALSE
    ");
    $groupStmt->execute([$user_id]);
    $groupEvents = $groupStmt->fetchAll();
    
    // Events wo Benutzer markiert ist
    $participantStmt = $pdo->prepare("
        SELECT e.*, g.group_name, u.full_name as created_by_name
        FROM events e
        LEFT JOIN groups_table g ON e.group_id = g.group_id
        INNER JOIN event_participants ep ON e.event_id = ep.event_id
        LEFT JOIN users u ON e.created_by = u.user_id
        WHERE ep.user_id = ? AND e.created_by != ?
    ");
    $participantStmt->execute([$user_id, $user_id]);
    $participantEvents = $participantStmt->fetchAll();
    
    $allEvents = array_merge($privateEvents, $groupEvents, $participantEvents);
    
    // Duplikate entfernen (basierend auf event_id)
    $uniqueEvents = [];
    $eventIds = [];
    foreach ($allEvents as $event) {
        if (!in_array($event['event_id'], $eventIds)) {
            $uniqueEvents[] = $event;
            $eventIds[] = $event['event_id'];
        }
    }
    
    jsonResponse(['success' => true, 'events' => $uniqueEvents]);
}

function saveEvent($pdo, $user_id) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $event_id = $data['event_id'] ?? null;
    $title = trim($data['title'] ?? '');
    $description = trim($data['description'] ?? '');
    $start_datetime = $data['start_datetime'] ?? '';
    $end_datetime = $data['end_datetime'] ?? '';
    $location = trim($data['location'] ?? '');
    $color = $data['color'] ?? '#ffb3ba';
    $is_all_day = $data['is_all_day'] ?? false;
    $group_id = $data['group_id'] ?? null;
    $participants = $data['participants'] ?? [];
    
    if (empty($title) || empty($start_datetime) || empty($end_datetime)) {
        jsonResponse(['success' => false, 'message' => 'Pflichtfelder fehlen'], 400);
    }
    
    // Wenn Gruppe ausgewählt, ist es kein privater Termin
    $is_private = empty($group_id);
    
    // Wenn Gruppe ausgewählt, prüfen ob Benutzer Mitglied ist
    if (!empty($group_id)) {
        $checkStmt = $pdo->prepare("SELECT 1 FROM group_members WHERE group_id = ? AND user_id = ?");
        $checkStmt->execute([$group_id, $user_id]);
        if (!$checkStmt->fetch()) {
            jsonResponse(['success' => false, 'message' => 'Sie sind kein Mitglied dieser Gruppe'], 403);
        }
    }
    
    $pdo->beginTransaction();
    
    try {
        if ($event_id) {
            // Event aktualisieren
            $stmt = $pdo->prepare("
                UPDATE events 
                SET title = ?, description = ?, start_datetime = ?, end_datetime = ?,
                    location = ?, color = ?, is_all_day = ?, group_id = ?, is_private = ?
                WHERE event_id = ? AND created_by = ?
            ");
            $stmt->execute([
                $title, $description, $start_datetime, $end_datetime,
                $location, $color, $is_all_day, $group_id, $is_private,
                $event_id, $user_id
            ]);
            
            // Alte Teilnehmer löschen
            $deleteStmt = $pdo->prepare("DELETE FROM event_participants WHERE event_id = ?");
            $deleteStmt->execute([$event_id]);
        } else {
            // Neues Event erstellen
            $stmt = $pdo->prepare("
                INSERT INTO events (title, description, start_datetime, end_datetime,
                                   location, color, is_all_day, created_by, group_id, is_private)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $title, $description, $start_datetime, $end_datetime,
                $location, $color, $is_all_day, $user_id, $group_id, $is_private
            ]);
            $event_id = $pdo->lastInsertId();
        }
        
        // Teilnehmer hinzufügen
        if (!empty($participants)) {
            $participantStmt = $pdo->prepare("
                INSERT INTO event_participants (event_id, user_id) VALUES (?, ?)
            ");
            foreach ($participants as $participant_id) {
                if ($participant_id != $user_id) { // Ersteller nicht als Teilnehmer
                    $participantStmt->execute([$event_id, $participant_id]);
                }
            }
        }
        
        $pdo->commit();
        jsonResponse(['success' => true, 'event_id' => $event_id]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function deleteEvent($pdo, $user_id) {
    $data = json_decode(file_get_contents('php://input'), true);
    $event_id = $data['event_id'] ?? null;
    
    if (!$event_id) {
        jsonResponse(['success' => false, 'message' => 'Event ID fehlt'], 400);
    }
    
    $stmt = $pdo->prepare("DELETE FROM events WHERE event_id = ? AND created_by = ?");
    $stmt->execute([$event_id, $user_id]);
    
    if ($stmt->rowCount() > 0) {
        jsonResponse(['success' => true]);
    } else {
        jsonResponse(['success' => false, 'message' => 'Event nicht gefunden oder keine Berechtigung'], 403);
    }
}

function getParticipants($pdo, $user_id) {
    $event_id = $_GET['event_id'] ?? null;
    
    if (!$event_id) {
        jsonResponse(['success' => false, 'message' => 'Event ID fehlt'], 400);
    }
    
    $stmt = $pdo->prepare("
        SELECT u.user_id, u.full_name, u.username, ep.status
        FROM event_participants ep
        INNER JOIN users u ON ep.user_id = u.user_id
        WHERE ep.event_id = ?
    ");
    $stmt->execute([$event_id]);
    $participants = $stmt->fetchAll();
    
    jsonResponse(['success' => true, 'participants' => $participants]);
}
?>
