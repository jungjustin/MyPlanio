<?php
require_once 'includes/config.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];
$is_admin = $_SESSION['is_admin'];
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gruppen - MyPlanio</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <h1>📅 MyPlanio</h1>
            <nav>
                <a href="index.php">Kalender</a>
                <a href="groups.php">Gruppen</a>
                <?php if ($is_admin): ?>
                    <a href="admin/">Admin</a>
                <?php endif; ?>
                <div class="user-menu" style="display: inline-block; position: relative;">
                    <a href="#" id="userMenuBtn"><?php echo htmlspecialchars($full_name); ?> ▼</a>
                    <div class="user-dropdown" id="userDropdown">
                        <a href="profile.php">Profil</a>
                        <a href="logout.php">Abmelden</a>
                    </div>
                </div>
            </nav>
        </div>
    </nav>

    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin: 30px 0 20px;">
            <h2>Meine Gruppen</h2>
            <button class="btn btn-primary" onclick="openCreateGroupModal()">+ Neue Gruppe erstellen</button>
        </div>

        <div id="groupsList"></div>
    </div>

    <!-- Modal: Neue Gruppe erstellen -->
    <div class="modal" id="createGroupModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="groupModalTitle">Neue Gruppe erstellen</h3>
                <button class="close-modal" onclick="closeCreateGroupModal()">&times;</button>
            </div>
            <form id="groupForm">
                <input type="hidden" id="groupId">
                
                <div class="form-group">
                    <label for="groupName">Gruppenname *</label>
                    <input type="text" id="groupName" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="groupDescription">Beschreibung</label>
                    <textarea id="groupDescription" class="form-control"></textarea>
                </div>
                
                <div class="form-group">
                    <label>Farbe</label>
                    <div class="color-picker-container">
                        <div class="color-option selected" data-color="#ffb3ba" style="background-color: #ffb3ba;" title="Rosa"></div>
                        <div class="color-option" data-color="#ffdfba" style="background-color: #ffdfba;" title="Orange"></div>
                        <div class="color-option" data-color="#ffffba" style="background-color: #ffffba;" title="Gelb"></div>
                        <div class="color-option" data-color="#baffc9" style="background-color: #baffc9;" title="Grün"></div>
                        <div class="color-option" data-color="#bae1ff" style="background-color: #bae1ff;" title="Blau"></div>
                        <div class="color-option" data-color="#e0bbff" style="background-color: #e0bbff;" title="Lila"></div>
                        <div class="color-option" data-color="#c9fff4" style="background-color: #c9fff4;" title="Mint"></div>
                        <div class="color-option" data-color="#ffd4ba" style="background-color: #ffd4ba;" title="Pfirsich"></div>
                    </div>
                    <input type="hidden" id="groupColor" value="#ffb3ba">
                </div>
                
                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">Speichern</button>
                    <button type="button" class="btn" onclick="closeCreateGroupModal()" style="flex: 1;">Abbrechen</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Gruppenmitglieder -->
    <div class="modal" id="membersModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="membersModalTitle">Gruppenmitglieder</h3>
                <button class="close-modal" onclick="closeMembersModal()">&times;</button>
            </div>
            
            <div id="membersList"></div>
            
            <div id="addMemberSection" style="margin-top: 20px; border-top: 2px solid #e5e5e5; padding-top: 20px;">
                <h4 style="margin-bottom: 15px;">Mitglied hinzufügen</h4>
                <div style="display: flex; gap: 10px;">
                    <select id="newMemberSelect" class="form-control">
                        <option value="">Benutzer auswählen...</option>
                    </select>
                    <button class="btn btn-primary" onclick="addMember()">Hinzufügen</button>
                </div>
            </div>
            
            <button class="btn" onclick="closeMembersModal()" style="width: 100%; margin-top: 20px;">Schließen</button>
        </div>
    </div>

    <script src="js/groups.js"></script>
    
    <footer style="text-align: center; padding: 20px; margin-top: 40px; border-top: 1px solid var(--border); color: var(--text-muted); font-size: 12px;">
        <p>MyPlanio - Entwickelt von <a href="mailto:justinjung@t-online.de" style="color: var(--pastel-blue);">Justin Jung</a></p>
    </footer>
</body>
</html>
