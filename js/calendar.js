// Globale Variablen
let currentDate = new Date();
let events = [];
let groups = [];
let selectedColor = '#ffb3ba';
let selectedEventId = null;

// Beim Laden der Seite
document.addEventListener('DOMContentLoaded', function() {
    initializeCalendar();
    loadGroups();
    loadEvents();
    setupEventListeners();
});

// Event Listeners einrichten
function setupEventListeners() {
    document.getElementById('prevMonth').addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar();
    });
    
    document.getElementById('nextMonth').addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar();
    });
    
    document.getElementById('todayBtn').addEventListener('click', () => {
        currentDate = new Date();
        renderCalendar();
    });
    
    document.getElementById('addEventBtn').addEventListener('click', openNewEventModal);
    document.getElementById('eventForm').addEventListener('submit', saveEvent);
    
    // Farb-Picker
    document.querySelectorAll('.color-option').forEach(option => {
        option.addEventListener('click', function() {
            document.querySelectorAll('.color-option').forEach(o => o.classList.remove('selected'));
            this.classList.add('selected');
            selectedColor = this.dataset.color;
            document.getElementById('eventColor').value = selectedColor;
        });
    });
    
    // Ganztägig Checkbox
    document.getElementById('eventAllDay').addEventListener('change', function() {
        const startInput = document.getElementById('eventStart');
        const endInput = document.getElementById('eventEnd');
        
        if (this.checked) {
            // Extrahiere das aktuelle Datum (ohne Zeit)
            const currentStart = startInput.value;
            const currentEnd = endInput.value;
            
            // Wenn bereits ein Datum gesetzt ist, behalte es
            if (currentStart) {
                const startDate = currentStart.split('T')[0];
                startInput.type = 'date';
                startInput.value = startDate;
            } else {
                startInput.type = 'date';
            }
            
            if (currentEnd) {
                const endDate = currentEnd.split('T')[0];
                endInput.type = 'date';
                endInput.value = endDate;
            } else {
                endInput.type = 'date';
            }
        } else {
            // Füge Standard-Zeit hinzu wenn zurück zu datetime-local
            const currentStart = startInput.value;
            const currentEnd = endInput.value;
            
            startInput.type = 'datetime-local';
            endInput.type = 'datetime-local';
            
            if (currentStart) {
                startInput.value = currentStart + 'T09:00';
            }
            if (currentEnd) {
                endInput.value = currentEnd + 'T10:00';
            }
        }
    });
    
    // User Menu Toggle
    document.getElementById('userMenuBtn').addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('userDropdown').classList.toggle('active');
    });
    
    // Schließe Dropdown wenn außerhalb geklickt wird
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.user-menu')) {
            document.getElementById('userDropdown').classList.remove('active');
        }
    });
    
    // Filter Checkboxen
    document.getElementById('filterPrivate').addEventListener('change', renderCalendar);
    document.getElementById('filterGroup').addEventListener('change', renderCalendar);
    
    // Delete Button
    document.getElementById('deleteEventBtn').addEventListener('click', deleteEvent);
}

// Kalender initialisieren
function initializeCalendar() {
    renderCalendar();
}

// Kalender rendern
function renderCalendar() {
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    
    // Monat und Jahr anzeigen
    const monthNames = ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni',
                        'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'];
    document.getElementById('currentMonth').textContent = `${monthNames[month]} ${year}`;
    
    // Erster Tag des Monats
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    
    // Wochentag des ersten Tags (0 = Sonntag, anpassen auf Montag = 0)
    let startDay = firstDay.getDay() - 1;
    if (startDay === -1) startDay = 6;
    
    const daysInMonth = lastDay.getDate();
    const calendarDays = document.getElementById('calendarDays');
    calendarDays.innerHTML = '';
    
    // Tage vom vorherigen Monat
    const prevMonthDays = new Date(year, month, 0).getDate();
    for (let i = startDay - 1; i >= 0; i--) {
        const day = prevMonthDays - i;
        const dayDiv = createDayElement(day, true, new Date(year, month - 1, day));
        calendarDays.appendChild(dayDiv);
    }
    
    // Tage des aktuellen Monats
    for (let day = 1; day <= daysInMonth; day++) {
        const date = new Date(year, month, day);
        const dayDiv = createDayElement(day, false, date);
        calendarDays.appendChild(dayDiv);
    }
    
    // Tage vom nächsten Monat
    const remainingDays = 42 - (startDay + daysInMonth);
    for (let day = 1; day <= remainingDays; day++) {
        const dayDiv = createDayElement(day, true, new Date(year, month + 1, day));
        calendarDays.appendChild(dayDiv);
    }
}

// Tag-Element erstellen
function createDayElement(day, isOtherMonth, date) {
    const dayDiv = document.createElement('div');
    dayDiv.className = 'calendar-day';
    
    if (isOtherMonth) {
        dayDiv.classList.add('other-month');
    }
    
    // Heute markieren
    const today = new Date();
    if (date.toDateString() === today.toDateString()) {
        dayDiv.classList.add('today');
    }
    
    const dayNumber = document.createElement('div');
    dayNumber.className = 'day-number';
    dayNumber.textContent = day;
    dayDiv.appendChild(dayNumber);
    
    // Events für diesen Tag anzeigen
    const dayEvents = getEventsForDate(date);
    dayEvents.forEach(event => {
        const eventDiv = document.createElement('div');
        eventDiv.className = 'event-item';
        eventDiv.style.borderLeftColor = event.color;
        eventDiv.textContent = event.title;
        eventDiv.addEventListener('click', (e) => {
            e.stopPropagation();
            showEventDetail(event);
        });
        dayDiv.appendChild(eventDiv);
    });
    
    // Klick auf Tag öffnet neuen Termin
    dayDiv.addEventListener('click', () => {
        openNewEventModal(date);
    });
    
    return dayDiv;
}

// Events für ein bestimmtes Datum abrufen
function getEventsForDate(date) {
    const filterPrivate = document.getElementById('filterPrivate').checked;
    const filterGroup = document.getElementById('filterGroup').checked;
    
    return events.filter(event => {
        const eventStart = new Date(event.start_datetime);
        const eventEnd = new Date(event.end_datetime);
        
        // Datum-Vergleich
        const dateMatch = date >= new Date(eventStart.toDateString()) && 
                         date <= new Date(eventEnd.toDateString());
        
        if (!dateMatch) return false;
        
        // Filter anwenden
        if (event.is_private && !filterPrivate) return false;
        if (!event.is_private && !filterGroup) return false;
        
        return true;
    });
}

// Gruppen laden
async function loadGroups() {
    try {
        const response = await fetch('api/groups.php?action=list');
        const data = await response.json();
        
        if (data.success) {
            groups = data.groups;
            renderGroupList();
            updateGroupSelects();
        }
    } catch (error) {
        console.error('Fehler beim Laden der Gruppen:', error);
    }
}

// Gruppenliste rendern
function renderGroupList() {
    const groupList = document.getElementById('groupList');
    groupList.innerHTML = '';
    
    if (groups.length === 0) {
        groupList.innerHTML = '<li style="text-align: center; padding: 20px;">Keine Gruppen</li>';
        return;
    }
    
    groups.forEach(group => {
        const li = document.createElement('li');
        li.className = 'group-item';
        li.innerHTML = `
            <span>${group.group_name}</span>
            <span style="font-size: 12px; color: #666;">${group.member_count} Mitglieder</span>
        `;
        groupList.appendChild(li);
    });
}

// Gruppen-Selects aktualisieren
function updateGroupSelects() {
    const eventGroupSelect = document.getElementById('eventGroup');
    eventGroupSelect.innerHTML = '<option value="">Privater Termin</option>';
    
    groups.forEach(group => {
        const option = document.createElement('option');
        option.value = group.group_id;
        option.textContent = group.group_name;
        eventGroupSelect.appendChild(option);
    });
}

// Events laden
async function loadEvents() {
    try {
        const response = await fetch('api/events.php?action=list');
        const data = await response.json();
        
        if (data.success) {
            events = data.events;
            renderCalendar();
        }
    } catch (error) {
        console.error('Fehler beim Laden der Events:', error);
    }
}

// Neues Event Modal öffnen
function openNewEventModal(date = null) {
    selectedEventId = null;
    document.getElementById('modalTitle').textContent = 'Neuer Termin';
    document.getElementById('eventForm').reset();
    document.getElementById('eventId').value = '';
    document.getElementById('deleteEventBtn').style.display = 'none';
    
    // Farbe zurücksetzen
    document.querySelectorAll('.color-option').forEach(o => o.classList.remove('selected'));
    document.querySelector('.color-option[data-color="#ffb3ba"]').classList.add('selected');
    selectedColor = '#ffb3ba';
    
    // Wenn ein Datum übergeben wurde, als Start setzen
    if (date) {
        // Datum korrekt formatieren (lokale Zeitzone verwenden)
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        const dateStr = `${year}-${month}-${day}`;
        
        document.getElementById('eventStart').value = dateStr + 'T09:00';
        document.getElementById('eventEnd').value = dateStr + 'T10:00';
    }
    
    // Teilnehmer laden
    loadParticipantOptions();
    
    document.getElementById('eventModal').classList.add('active');
}

// Event Detail Modal öffnen
async function showEventDetail(event) {
    selectedEventId = event.event_id;
    document.getElementById('detailTitle').textContent = event.title;
    
    let detailsHTML = '';
    
    if (event.description) {
        detailsHTML += `<p><strong>Beschreibung:</strong><br>${event.description}</p>`;
    }
    
    const startDate = new Date(event.start_datetime);
    const endDate = new Date(event.end_datetime);
    
    if (event.is_all_day) {
        detailsHTML += `<p><strong>Wann:</strong> ${formatDate(startDate)}`;
        if (startDate.toDateString() !== endDate.toDateString()) {
            detailsHTML += ` bis ${formatDate(endDate)}`;
        }
        detailsHTML += ` (Ganztägig)</p>`;
    } else {
        detailsHTML += `<p><strong>Start:</strong> ${formatDateTime(startDate)}</p>`;
        detailsHTML += `<p><strong>Ende:</strong> ${formatDateTime(endDate)}</p>`;
    }
    
    if (event.location) {
        detailsHTML += `<p><strong>Ort:</strong> ${event.location}</p>`;
    }
    
    if (event.group_name) {
        detailsHTML += `<p><strong>Gruppe:</strong> ${event.group_name}</p>`;
    } else {
        detailsHTML += `<p><strong>Typ:</strong> Privater Termin</p>`;
    }
    
    // Ersteller anzeigen
    if (event.created_by_name) {
        detailsHTML += `<p><strong>Erstellt von:</strong> ${event.created_by_name}</p>`;
    }
    
    // Teilnehmer laden und anzeigen
    try {
        const response = await fetch(`api/events.php?action=participants&event_id=${event.event_id}`);
        const data = await response.json();
        
        if (data.success && data.participants.length > 0) {
            detailsHTML += `<p><strong>Markierte Personen:</strong><br>`;
            data.participants.forEach((participant, index) => {
                if (index > 0) detailsHTML += ', ';
                detailsHTML += participant.full_name;
            });
            detailsHTML += `</p>`;
        }
    } catch (error) {
        console.error('Fehler beim Laden der Teilnehmer:', error);
    }
    
    document.getElementById('eventDetails').innerHTML = detailsHTML;
    
    // Edit Button
    document.getElementById('editEventBtn').onclick = () => {
        closeDetailModal();
        editEvent(event);
    };
    
    document.getElementById('eventDetailModal').classList.add('active');
}

// Event bearbeiten
function editEvent(event) {
    selectedEventId = event.event_id;
    document.getElementById('modalTitle').textContent = 'Termin bearbeiten';
    document.getElementById('eventId').value = event.event_id;
    document.getElementById('eventTitle').value = event.title;
    document.getElementById('eventDescription').value = event.description || '';
    document.getElementById('eventLocation').value = event.location || '';
    document.getElementById('eventAllDay').checked = event.is_all_day;
    
    // Datum/Zeit setzen
    const startDate = new Date(event.start_datetime);
    const endDate = new Date(event.end_datetime);
    
    if (event.is_all_day) {
        document.getElementById('eventStart').type = 'date';
        document.getElementById('eventEnd').type = 'date';
        document.getElementById('eventStart').value = startDate.toISOString().split('T')[0];
        document.getElementById('eventEnd').value = endDate.toISOString().split('T')[0];
    } else {
        document.getElementById('eventStart').type = 'datetime-local';
        document.getElementById('eventEnd').type = 'datetime-local';
        document.getElementById('eventStart').value = formatDateTimeLocal(startDate);
        document.getElementById('eventEnd').value = formatDateTimeLocal(endDate);
    }
    
    // Gruppe setzen
    document.getElementById('eventGroup').value = event.group_id || '';
    
    // Farbe setzen
    selectedColor = event.color;
    document.getElementById('eventColor').value = event.color;
    document.querySelectorAll('.color-option').forEach(o => o.classList.remove('selected'));
    const colorOption = document.querySelector(`.color-option[data-color="${event.color}"]`);
    if (colorOption) colorOption.classList.add('selected');
    
    // Delete Button anzeigen
    document.getElementById('deleteEventBtn').style.display = 'block';
    
    // Teilnehmer laden
    loadParticipantOptions(event.event_id);
    
    document.getElementById('eventModal').classList.add('active');
}

// Teilnehmer-Optionen laden
async function loadParticipantOptions(eventId = null) {
    try {
        const response = await fetch('api/users.php?action=list');
        const data = await response.json();
        
        if (data.success) {
            const container = document.getElementById('eventParticipants');
            container.innerHTML = '';
            
            if (data.users.length === 0) {
                container.innerHTML = '<p style="color: var(--text-muted); font-size: 12px;">Keine weiteren Benutzer verfügbar</p>';
                return;
            }
            
            // Wenn Event bearbeitet wird, ausgewählte Teilnehmer laden
            let selectedParticipants = [];
            if (eventId) {
                const eventResponse = await fetch(`api/events.php?action=participants&event_id=${eventId}`);
                const eventData = await eventResponse.json();
                
                if (eventData.success) {
                    selectedParticipants = eventData.participants.map(p => p.user_id);
                }
            }
            
            // Checkbox für jeden Benutzer erstellen
            data.users.forEach(user => {
                const checkboxDiv = document.createElement('div');
                checkboxDiv.className = 'checkbox-group';
                checkboxDiv.style.marginBottom = '8px';
                
                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.id = `participant_${user.user_id}`;
                checkbox.value = user.user_id;
                checkbox.className = 'participant-checkbox';
                
                if (selectedParticipants.includes(user.user_id)) {
                    checkbox.checked = true;
                }
                
                const label = document.createElement('label');
                label.htmlFor = `participant_${user.user_id}`;
                label.textContent = user.full_name;
                label.style.marginBottom = '0';
                label.style.cursor = 'pointer';
                
                checkboxDiv.appendChild(checkbox);
                checkboxDiv.appendChild(label);
                container.appendChild(checkboxDiv);
            });
        }
    } catch (error) {
        console.error('Fehler beim Laden der Benutzer:', error);
        const container = document.getElementById('eventParticipants');
        container.innerHTML = '<p style="color: var(--pastel-pink); font-size: 12px;">Fehler beim Laden</p>';
    }
}

// Event speichern
async function saveEvent(e) {
    e.preventDefault();
    
    // Ausgewählte Teilnehmer sammeln
    const participantCheckboxes = document.querySelectorAll('.participant-checkbox:checked');
    const participants = Array.from(participantCheckboxes).map(cb => cb.value);
    
    const eventData = {
        event_id: document.getElementById('eventId').value,
        title: document.getElementById('eventTitle').value,
        description: document.getElementById('eventDescription').value,
        start_datetime: document.getElementById('eventStart').value,
        end_datetime: document.getElementById('eventEnd').value,
        location: document.getElementById('eventLocation').value,
        color: selectedColor,
        is_all_day: document.getElementById('eventAllDay').checked,
        group_id: document.getElementById('eventGroup').value || null,
        participants: participants
    };
    
    try {
        const response = await fetch('api/events.php?action=save', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(eventData)
        });
        
        const data = await response.json();
        
        if (data.success) {
            closeEventModal();
            loadEvents();
        } else {
            alert('Fehler: ' + (data.message || 'Unbekannter Fehler'));
        }
    } catch (error) {
        console.error('Fehler beim Speichern:', error);
        alert('Fehler beim Speichern des Termins');
    }
}

// Event löschen
async function deleteEvent() {
    if (!confirm('Möchten Sie diesen Termin wirklich löschen?')) {
        return;
    }
    
    const eventId = document.getElementById('eventId').value;
    
    try {
        const response = await fetch('api/events.php?action=delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ event_id: eventId })
        });
        
        const data = await response.json();
        
        if (data.success) {
            closeEventModal();
            loadEvents();
        } else {
            alert('Fehler beim Löschen: ' + (data.message || 'Unbekannter Fehler'));
        }
    } catch (error) {
        console.error('Fehler beim Löschen:', error);
        alert('Fehler beim Löschen des Termins');
    }
}

// Modals schließen
function closeEventModal() {
    document.getElementById('eventModal').classList.remove('active');
}

function closeDetailModal() {
    document.getElementById('eventDetailModal').classList.remove('active');
}

// Hilfsfunktionen für Datum-Formatierung
function formatDate(date) {
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    return `${day}.${month}.${year}`;
}

function formatDateTime(date) {
    const dateStr = formatDate(date);
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    return `${dateStr} ${hours}:${minutes}`;
}

function formatDateTimeLocal(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    return `${year}-${month}-${day}T${hours}:${minutes}`;
}
