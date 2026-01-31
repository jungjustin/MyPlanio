<?php
require_once 'includes/config.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$full_name = $_SESSION['full_name'];
$is_admin = $_SESSION['is_admin'];
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalender - <?php echo htmlspecialchars($full_name); ?> - MyPlanio</title>
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
        <div style="display: grid; grid-template-columns: 250px 1fr; gap: 20px; margin-top: 20px;">
            <!-- Sidebar -->
            <div>
                <div class="sidebar">
                    <h3>Meine Gruppen</h3>
                    <ul class="group-list" id="groupList">
                        <li style="text-align: center; padding: 20px;">
                            <div class="loading"></div>
                        </li>
                    </ul>
                    <button class="btn btn-primary" style="width: 100%; margin-top: 10px;" onclick="location.href='groups.php'">Gruppen verwalten</button>
                </div>
                
                <div class="sidebar" style="margin-top: 20px;">
                    <h3>Filter</h3>
                    <div class="checkbox-group">
                        <input type="checkbox" id="filterPrivate" checked>
                        <label for="filterPrivate">Private Termine</label>
                    </div>
                    <div class="checkbox-group" style="margin-top: 10px;">
                        <input type="checkbox" id="filterGroup" checked>
                        <label for="filterGroup">Gruppentermine</label>
                    </div>
                </div>
            </div>

            <!-- Hauptbereich -->
            <div>
                <div class="calendar-container">
                    <div class="calendar-header">
                        <div class="calendar-nav">
                            <button class="btn" id="prevMonth">◀ Vorheriger</button>
                            <button class="btn" id="todayBtn">Heute</button>
                            <button class="btn" id="nextMonth">Nächster ▶</button>
                        </div>
                        <h2 id="currentMonth"></h2>
                        <button class="btn btn-primary" id="addEventBtn">+ Neuer Termin</button>
                    </div>
                    
                    <div class="calendar-grid">
                        <div class="calendar-day-header">Mo</div>
                        <div class="calendar-day-header">Di</div>
                        <div class="calendar-day-header">Mi</div>
                        <div class="calendar-day-header">Do</div>
                        <div class="calendar-day-header">Fr</div>
                        <div class="calendar-day-header">Sa</div>
                        <div class="calendar-day-header">So</div>
                    </div>
                    
                    <div class="calendar-grid" id="calendarDays"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal für neuen/bearbeiteten Termin -->
    <div class="modal" id="eventModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Neuer Termin</h3>
                <button class="close-modal" onclick="closeEventModal()">&times;</button>
            </div>
            <form id="eventForm">
                <input type="hidden" id="eventId">
                
                <div class="form-group">
                    <label for="eventTitle">Titel *</label>
                    <input type="text" id="eventTitle" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="eventDescription">Beschreibung</label>
                    <textarea id="eventDescription" class="form-control"></textarea>
                </div>
                
                <div class="checkbox-group" style="margin-bottom: 15px;">
                    <input type="checkbox" id="eventAllDay">
                    <label for="eventAllDay">Ganztägig</label>
                </div>
                
                <div class="form-group">
                    <label for="eventStart">Start *</label>
                    <input type="datetime-local" id="eventStart" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="eventEnd">Ende *</label>
                    <input type="datetime-local" id="eventEnd" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="eventLocation">Ort</label>
                    <input type="text" id="eventLocation" class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="eventGroup">Gruppe (optional)</label>
                    <select id="eventGroup" class="form-control">
                        <option value="">Privater Termin</option>
                    </select>
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
                    <input type="hidden" id="eventColor" value="#ffb3ba">
                </div>
                
                <div class="form-group">
                    <label>Personen markieren (optional)</label>
                    <div id="eventParticipants" style="max-height: 200px; overflow-y: auto; border: 2px solid var(--border); padding: 10px; background-color: var(--bg-tertiary);">
                        <p style="color: var(--text-muted); font-size: 12px; margin-bottom: 10px;">Lädt...</p>
                    </div>
                </div>
                
                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">Speichern</button>
                    <button type="button" class="btn" onclick="closeEventModal()" style="flex: 1;">Abbrechen</button>
                    <button type="button" class="btn btn-danger" id="deleteEventBtn" style="display: none;">Löschen</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal für Termindetails -->
    <div class="modal" id="eventDetailModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="detailTitle"></h3>
                <button class="close-modal" onclick="closeDetailModal()">&times;</button>
            </div>
            <div id="eventDetails"></div>
            <div style="margin-top: 20px; display: flex; gap: 10px;">
                <button class="btn btn-primary" id="editEventBtn">Bearbeiten</button>
                <button class="btn" onclick="closeDetailModal()">Schließen</button>
            </div>
        </div>
    </div>

    <script src="js/calendar.js"></script>
    
    <footer style="text-align: center; padding: 20px; margin-top: 40px; border-top: 1px solid var(--border); color: var(--text-muted); font-size: 12px;">
        <p>MyPlanio - Entwickelt von <a href="mailto:justinjung@t-online.de" style="color: var(--pastel-blue);">Justin Jung</a></p>
    </footer>
</body>
</html>
