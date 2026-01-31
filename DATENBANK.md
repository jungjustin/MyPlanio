# 📊 Datenbank-Schema - MyPlanio

**Entwickelt von:** Justin Jung  
**E-Mail:** justinjung@t-online.de

---

## 📜 Lizenzhinweis

Diese Dokumentation ist Teil von **MyPlanio**.  
**Private Nutzung:** Kostenlos mit Namensnennung  
**Geschäftliche Nutzung:** Lizenz erforderlich - justinjung@t-online.de

---

## Übersicht

Die Kalender-App verwendet 5 Haupttabellen für die Datenverwaltung:

```
┌─────────────┐
│   USERS     │ ◄─────┐
│             │       │
│ • user_id   │       │
│ • username  │       │
│ • password  │       │
│ • is_admin  │       │
└─────────────┘       │
       │              │
       │ erstellt     │ Mitglied
       │              │
       ▼              │
┌─────────────┐       │
│   GROUPS    │ ──────┤
│             │       │
│ • group_id  │       │
│ • name      │       │
│ • created_by│       │
└─────────────┘       │
       │              │
       │ hat          │
       │              │
       ▼              │
┌──────────────┐      │
│ GROUP_MEMBERS│◄─────┘
│              │
│ • group_id   │
│ • user_id    │
│ • is_admin   │
└──────────────┘

┌─────────────┐
│   EVENTS    │
│             │
│ • event_id  │
│ • title     │
│ • start     │
│ • end       │
│ • created_by│ ────┐
│ • group_id  │     │
└─────────────┘     │
       │            │
       │ hat        │ erstellt von
       │            │
       ▼            ▼
┌──────────────┐  ┌──────┐
│EVENT_PARTIC. │  │USERS │
│              │  │      │
│ • event_id   │  └──────┘
│ • user_id    │
│ • status     │
└──────────────┘
```

---

## Tabellen-Details

### 1. USERS (Benutzer)
Speichert alle Benutzerkonten.

**Felder:**
- `user_id` (INT, PRIMARY KEY) - Eindeutige Benutzer-ID
- `username` (VARCHAR, UNIQUE) - Benutzername für Login
- `password_hash` (VARCHAR) - Gehashtes Passwort
- `email` (VARCHAR) - E-Mail-Adresse
- `full_name` (VARCHAR) - Vollständiger Name
- `is_approved` (BOOLEAN) - Admin-Genehmigung
- `is_admin` (BOOLEAN) - Admin-Rechte
- `created_at` (TIMESTAMP) - Registrierungsdatum
- `last_login` (TIMESTAMP) - Letzter Login

**Beziehungen:**
- Erstellt Gruppen (→ groups_table)
- Erstellt Events (→ events)
- Ist Gruppenmitglied (→ group_members)
- Ist Event-Teilnehmer (→ event_participants)

---

### 2. GROUPS_TABLE (Gruppen)
Speichert alle Gruppen.

**Felder:**
- `group_id` (INT, PRIMARY KEY) - Eindeutige Gruppen-ID
- `group_name` (VARCHAR) - Name der Gruppe
- `created_by` (INT, FOREIGN KEY → users) - Ersteller
- `description` (TEXT) - Beschreibung
- `color` (VARCHAR) - Farbcode (Schwarz-Weiß-Töne)
- `created_at` (TIMESTAMP) - Erstelldatum

**Beziehungen:**
- Hat Mitglieder (→ group_members)
- Hat Events (→ events)
- Gehört einem Ersteller (← users)

---

### 3. GROUP_MEMBERS (Gruppenmitglieder)
Verbindungstabelle zwischen Benutzern und Gruppen.

**Felder:**
- `member_id` (INT, PRIMARY KEY) - Eindeutige ID
- `group_id` (INT, FOREIGN KEY → groups_table) - Gruppen-ID
- `user_id` (INT, FOREIGN KEY → users) - Benutzer-ID
- `is_admin` (BOOLEAN) - Gruppen-Admin-Rechte
- `joined_at` (TIMESTAMP) - Beitrittsdatum

**Unique Key:** `(group_id, user_id)` - Verhindert Duplikate

---

### 4. EVENTS (Termine)
Speichert alle Termine (privat und Gruppen).

**Felder:**
- `event_id` (INT, PRIMARY KEY) - Eindeutige Termin-ID
- `title` (VARCHAR) - Titel des Termins
- `description` (TEXT) - Beschreibung
- `start_datetime` (DATETIME) - Startzeit
- `end_datetime` (DATETIME) - Endzeit
- `location` (VARCHAR) - Ort
- `color` (VARCHAR) - Farbcode
- `is_all_day` (BOOLEAN) - Ganztägig
- `created_by` (INT, FOREIGN KEY → users) - Ersteller
- `group_id` (INT, FOREIGN KEY → groups_table) - Gruppe (optional)
- `is_private` (BOOLEAN) - Privater Termin
- `created_at` (TIMESTAMP) - Erstelldatum
- `updated_at` (TIMESTAMP) - Letzte Änderung

**Beziehungen:**
- Gehört einem Benutzer (← users)
- Gehört einer Gruppe (← groups_table) [optional]
- Hat Teilnehmer (→ event_participants)

---

### 5. EVENT_PARTICIPANTS (Termin-Teilnehmer)
Verbindungstabelle zwischen Terminen und markierten Personen.

**Felder:**
- `participant_id` (INT, PRIMARY KEY) - Eindeutige ID
- `event_id` (INT, FOREIGN KEY → events) - Termin-ID
- `user_id` (INT, FOREIGN KEY → users) - Benutzer-ID
- `status` (ENUM) - Status: 'pending', 'accepted', 'declined'

**Unique Key:** `(event_id, user_id)` - Verhindert Duplikate

---

## Datenfluss-Beispiele

### Beispiel 1: Benutzer erstellt privaten Termin
```
1. User wird in EVENTS als created_by gespeichert
2. is_private = TRUE
3. group_id = NULL
```

### Beispiel 2: Gruppentermin mit Teilnehmern
```
1. User erstellt Event
2. is_private = FALSE
3. group_id wird gesetzt
4. Teilnehmer werden in EVENT_PARTICIPANTS eingetragen
```

### Beispiel 3: Neue Gruppe erstellen
```
1. Gruppe wird in GROUPS_TABLE erstellt
2. Ersteller wird automatisch in GROUP_MEMBERS mit is_admin=TRUE eingetragen
```

---

## Cascade-Verhalten (Automatische Löschungen)

**Wenn ein Benutzer gelöscht wird:**
- ✅ Alle seine Events werden gelöscht
- ✅ Seine Gruppenmitgliedschaften werden entfernt
- ✅ Seine Event-Teilnahmen werden entfernt
- ✅ Seine erstellten Gruppen werden gelöscht

**Wenn eine Gruppe gelöscht wird:**
- ✅ Alle Gruppenmitgliedschaften werden entfernt
- ✅ Alle Gruppentermine werden gelöscht

**Wenn ein Event gelöscht wird:**
- ✅ Alle Teilnehmer-Einträge werden entfernt

---

## Indizes für Performance

**USERS:**
- INDEX auf `username`
- INDEX auf `is_approved`

**GROUPS_TABLE:**
- INDEX auf `created_by`

**GROUP_MEMBERS:**
- INDEX auf `group_id`
- INDEX auf `user_id`

**EVENTS:**
- INDEX auf `created_by`
- INDEX auf `group_id`
- INDEX auf `start_datetime`
- INDEX auf `end_datetime`

**EVENT_PARTICIPANTS:**
- INDEX auf `event_id`
- INDEX auf `user_id`

---

## Sicherheit

### Passwort-Hashing
Passwörter werden mit PHP's `password_hash()` (bcrypt) gespeichert.

### SQL-Injection Schutz
Alle Queries verwenden Prepared Statements mit PDO.

### Datenintegrität
Foreign Keys erzwingen referentielle Integrität.

---

## Zeichenkodierung
Alle Tabellen verwenden `utf8mb4_unicode_ci` für vollständige Unicode-Unterstützung (inkl. Emojis).
