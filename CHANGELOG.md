# 🔄 CHANGELOG - MyPlanio Version 2.0

**Entwickelt von:** Justin Jung  
**Kontakt:** justinjung@t-online.de

---

## 📜 Lizenzhinweis

**MyPlanio** ist für private Nutzung kostenlos (mit Namensnennung).  
Für geschäftliche Nutzung kontaktieren Sie: justinjung@t-online.de

---

## ✨ Neue Features & Verbesserungen

### 🎨 Design-Verbesserungen

**Event-Anzeige:**
- ✅ **BEHOBEN:** Termine im Kalender jetzt mit weißer Schrift (statt schwarz)
- ✅ Bessere Lesbarkeit auf allen Hintergründen
- ✅ Event-Items haben jetzt weiße Schrift für optimalen Kontrast

### 📱 Mobile Optimierung

**Responsive Verbesserungen:**
- ✅ Kalender-Tage jetzt größer auf Mobile (100px statt 80px)
- ✅ Sidebar wird auf Mobile unter dem Kalender angezeigt
- ✅ Bessere Schriftgrößen für Mobile-Geräte
- ✅ Navigation zentriert auf Mobile
- ✅ Buttons besser angepasst für Touch-Bedienung

**Grid-Layout:**
- Tablet (≤768px): Optimiertes Single-Column-Layout
- Mobile (≤480px): Kompakte Ansicht mit besseren Abständen

### 👥 Teilnehmer-Verwaltung

**NEU: Checkbox-System statt Multiselect:**
- ✅ Keine komplizierte Strg+Klick-Auswahl mehr
- ✅ Einfaches Anklicken zum Auswählen mehrerer Personen
- ✅ Viel besser auf Touch-Geräten (Handy/Tablet)
- ✅ Scrollbare Liste bei vielen Benutzern
- ✅ Hover-Effekt für besseres Feedback
- ✅ Ausgewählte Teilnehmer werden farblich markiert

**Anzeige in Termin-Details:**
- ✅ **NEU:** Ersteller des Termins wird angezeigt
- ✅ **NEU:** Alle markierten Personen werden aufgelistet
- ✅ Übersichtliche Darstellung mit Namen

### 🐛 Bugfixes

**Datums-Bug behoben:**
- ✅ **BEHOBEN:** Klick auf Tag setzt jetzt korrektes Datum (vorher 1 Tag zurück)
- ✅ **BEHOBEN:** Ganztägig-Checkbox lässt Datum nicht mehr verschwinden
- ✅ Datum bleibt beim Umschalten zwischen Ganztägig/Zeitplan erhalten
- ✅ Korrekte Zeitzone-Behandlung

### 🎯 Weitere Verbesserungen

**Kalender:**
- Heute-Markierung mit Pastellblau-Border
- Optimierte Event-Hover-Effekte
- Bessere Scroll-Performance

**Formulare:**
- Checkbox-Styling mit Pastellblau-Akzent
- Besseres Feedback bei Interaktionen

---

## 📋 Änderungsdetails

### CSS-Änderungen (`style.css`)

```css
/* Event-Items jetzt mit weißer Schrift */
.event-item {
    color: var(--text-primary); /* war: var(--bg-primary) */
}

/* Neue Participant-Checkbox Styles */
.participant-checkbox {
    accent-color: var(--pastel-blue);
}

/* Mobile Responsiveness verbessert */
@media (max-width: 768px) {
    .calendar-day {
        min-height: 100px; /* war: 80px */
    }
    
    body .container > div[style*="grid-template-columns"] {
        display: block !important; /* Sidebar unter Kalender */
    }
}
```

### JavaScript-Änderungen (`calendar.js`)

**Datums-Bug behoben:**
```javascript
// Korrektes Datum beim Tag-Klick
const year = date.getFullYear();
const month = String(date.getMonth() + 1).padStart(2, '0');
const day = String(date.getDate()).padStart(2, '0');
const dateStr = `${year}-${month}-${day}`;
```

**Ganztägig-Umschaltung:**
```javascript
// Datum bleibt beim Umschalten erhalten
if (currentStart) {
    const startDate = currentStart.split('T')[0];
    startInput.value = startDate;
}
```

**Neue Checkbox-Liste für Teilnehmer:**
```javascript
// Checkbox statt Multiselect
data.users.forEach(user => {
    const checkbox = document.createElement('input');
    checkbox.type = 'checkbox';
    checkbox.className = 'participant-checkbox';
    // ...
});
```

**Teilnehmer-Anzeige im Detail:**
```javascript
// Ersteller und Teilnehmer anzeigen
if (event.created_by_name) {
    detailsHTML += `<p><strong>Erstellt von:</strong> ${event.created_by_name}</p>`;
}

// Teilnehmer laden und anzeigen
const response = await fetch(`api/events.php?action=participants&event_id=${event.event_id}`);
```

### API-Änderungen (`api/events.php`)

**Ersteller-Name mitliefern:**
```php
// LEFT JOIN mit users-Tabelle
SELECT e.*, g.group_name, u.full_name as created_by_name
FROM events e
LEFT JOIN users u ON e.created_by = u.user_id
```

### HTML-Änderungen (`index.php`)

**Multiselect durch Checkbox-Container ersetzt:**
```html
<!-- Alt: <select multiple> -->
<!-- Neu: -->
<div id="eventParticipants" style="max-height: 200px; overflow-y: auto; ...">
    <!-- Checkboxen werden dynamisch eingefügt -->
</div>
```

---

## 🚀 Migration von V1.0 zu V2.0

### Wenn Sie bereits V1.0 installiert haben:

1. **Backup erstellen:**
   ```bash
   # Datenbank sichern
   mysqldump -u root -p kalender_app > backup.sql
   
   # Dateien sichern
   cp -r /pfad/zur/app /pfad/zur/app_backup
   ```

2. **Neue Dateien hochladen:**
   - Ersetzen Sie alle Dateien außer `includes/config.php`
   - Oder passen Sie `config.php` manuell mit Ihren DB-Daten an

3. **Keine Datenbank-Änderungen nötig:**
   - Schema ist identisch
   - Keine Migration erforderlich

4. **Browser-Cache leeren:**
   - Strg+Shift+Del
   - CSS/JavaScript-Cache löschen

### Neu-Installation:

Folgen Sie der normalen Installationsanleitung in `INSTALLATION.md`

---

## 📱 Getestet auf

- ✅ Chrome Desktop (Windows/Mac/Linux)
- ✅ Firefox Desktop
- ✅ Safari Desktop (Mac)
- ✅ Chrome Mobile (Android)
- ✅ Safari Mobile (iOS)
- ✅ Tablets (iPad, Android-Tablets)

---

## 🎯 Bekannte Einschränkungen

- Teilnehmer-Status (pending/accepted/declined) wird noch nicht verwendet
- Keine Push-Benachrichtigungen
- Keine Erinnerungen

---

## 💡 Tipps & Tricks

### Mobile Nutzung:
- Termin-Details: Tap auf Event im Kalender
- Mehrere Personen markieren: Einfach alle anklicken
- Scrollen in Teilnehmer-Liste: Touch & Swipe

### Desktop:
- Schnelles Erstellen: Tag-Klick → Formular
- Bearbeiten: Event-Klick → Bearbeiten-Button
- Löschen: Event öffnen → Bearbeiten → Löschen

---

## 🙏 Feedback

Bugs gefunden? Verbesserungsvorschläge?
- Nutzen Sie die Test-Dateien zum Debuggen
- Prüfen Sie Browser-Konsole (F12)
- Aktivieren Sie Error-Display in `config.php`

---

**Version:** 2.0  
**Release-Datum:** $(date)  
**Kompatibilität:** PHP 7.4+, MySQL 5.7+
