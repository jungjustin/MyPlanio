# 🔧 INSTALLATION & FEHLERBEHEBUNG - MyPlanio

**Entwickelt von:** Justin Jung | **E-Mail:** justinjung@t-online.de

---

## 📜 LIZENZ & NUTZUNG

### Private Nutzung
✅ **Kostenlos** mit Namensnennung  
✅ Anpassungen erlaubt  
⚠️ Namensnennung "MyPlanio - Justin Jung" erforderlich

### Geschäftliche Nutzung
📧 **Lizenz erforderlich** - Kontakt: justinjung@t-online.de  
❌ Keine kommerzielle Nutzung ohne Genehmigung

---

## 📋 Schritt-für-Schritt Installation

### 1️⃣ Datenbank erstellen

**Wichtig:** Führen Sie diese Schritte in der richtigen Reihenfolge aus!

#### Mit phpMyAdmin:
1. Öffnen Sie phpMyAdmin (`http://localhost/phpmyadmin`)
2. Klicken Sie links auf "Neu"
3. Datenbankname: `myplanio`
4. Zeichensatz: `utf8mb4_unicode_ci`
5. Klicken Sie "Erstellen"
6. Wählen Sie die neue Datenbank aus (links anklicken)
7. Klicken Sie oben auf "SQL"
8. Öffnen Sie die Datei `database.sql` mit einem Texteditor
9. Kopieren Sie den KOMPLETTEN Inhalt
10. Fügen Sie ihn in das SQL-Feld ein
11. Klicken Sie "OK"
12. Sie sollten die Meldung sehen: "5 Zeilen eingefügt"

#### Mit MySQL Kommandozeile:
```bash
mysql -u root -p
# Passwort eingeben
CREATE DATABASE myplanio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE myplanio;
source /vollständiger/pfad/zu/database.sql;
# Prüfen ob Tabellen erstellt wurden:
SHOW TABLES;
# Sollte 5 Tabellen anzeigen
EXIT;
```

### 2️⃣ Datenbank-Verbindung konfigurieren

Öffnen Sie `includes/config.php` und passen Sie die Zeilen 6-9 an:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');          // Ihr MySQL-Benutzername
define('DB_PASS', '');              // Ihr MySQL-Passwort
define('DB_NAME', 'myplanio');
```

**Typische Einstellungen:**
- **XAMPP (Windows/Mac):** User: `root`, Pass: `` (leer)
- **WAMP (Windows):** User: `root`, Pass: `` (leer) oder Ihr gesetztes Passwort
- **MAMP (Mac):** User: `root`, Pass: `root`
- **Linux (LAMP):** User: `root`, Pass: Ihr bei Installation gesetztes Passwort

### 3️⃣ Testen

1. Öffnen Sie im Browser: `http://localhost/myplanio/test-session.php`
2. Prüfen Sie:
   - ✅ Session Status sollte "3" sein (aktiv)
   - ✅ Datenbank-Verbindung: OK
   - ✅ Anzahl Benutzer: 1
   - ✅ Admin-Account gefunden: Ja

3. Öffnen Sie: `http://localhost/myplanio/test-password.php`
4. Prüfen Sie:
   - ✅ Verifizierung des admin123 Passworts: OK
   - ✅ Admin-Account in Datenbank: Gefunden
   - ✅ Passwort 'admin123' funktioniert: JA

### 4️⃣ Login testen

1. Öffnen Sie: `http://localhost/myplanio/login.php`
2. Anmelden mit:
   - Benutzername: `admin`
   - Passwort: `admin123`
3. Sie sollten zum Kalender weitergeleitet werden

---

## 🐛 FEHLERBEHEBUNG

### Problem: "Datenbankverbindung fehlgeschlagen"

**Mögliche Ursachen:**

1. **MySQL läuft nicht**
   - XAMPP: Starten Sie MySQL im XAMPP Control Panel
   - WAMP: Starten Sie alle Services
   - Linux: `sudo service mysql start`

2. **Falsche Zugangsdaten in config.php**
   - Öffnen Sie phpMyAdmin und schauen Sie oben rechts welcher User angemeldet ist
   - Testen Sie die Verbindung mit diesen Daten

3. **Datenbank existiert nicht**
   ```sql
   -- In phpMyAdmin SQL ausführen:
   SHOW DATABASES LIKE 'myplanio';
   -- Sollte die Datenbank anzeigen
   ```

**Lösung:**
Öffnen Sie `test-session.php` - dort sehen Sie die genaue Fehlermeldung!

---

### Problem: Login-Formular wird geleert, keine Fehlermeldung

**Ursache:** Sessions funktionieren nicht korrekt.

**Lösungen:**

1. **Session-Ordner prüfen:**
   ```php
   // In test-session.php schauen Sie auf:
   // session.save_path
   ```
   Dieser Ordner muss existieren und beschreibbar sein!

2. **Für XAMPP Windows:**
   - Öffnen Sie `php.ini` (im XAMPP Ordner: `php/php.ini`)
   - Suchen Sie: `session.save_path`
   - Setzen Sie: `session.save_path = "C:/xampp/tmp"`
   - Starten Sie Apache neu

3. **Für Linux:**
   ```bash
   sudo chmod 777 /var/lib/php/sessions
   # oder
   sudo chmod 777 /tmp
   ```

4. **Testen Sie:**
   - Öffnen Sie `test-session.php`
   - Laden Sie die Seite neu (F5)
   - Prüfen Sie ob "Test-Variable: Session funktioniert!" erscheint

---

### Problem: "ERR_TOO_MANY_REDIRECTS" im Admin-Bereich

**Ursache:** Session-Daten werden nicht gespeichert, führt zu Endlos-Schleife.

**Lösung:**

1. **Löschen Sie Browser-Cache und Cookies:**
   - Chrome: Strg+Shift+Del → Cookies löschen
   - Firefox: Strg+Shift+Del → Cookies löschen

2. **Session-Probleme beheben (siehe oben)**

3. **Temporäre Lösung - Direktzugriff:**
   - Öffnen Sie `includes/config.php`
   - Zeile 2: Ändern Sie zu: `error_reporting(E_ALL);`
   - Zeile 3: Ändern Sie zu: `ini_set('display_errors', 1);`
   - Versuchen Sie erneut - jetzt sehen Sie Fehlermeldungen!

---

### Problem: Admin-Passwort funktioniert nicht

**Ursache:** Admin-Account wurde nicht korrekt angelegt.

**Lösung:**

1. Öffnen Sie phpMyAdmin
2. Wählen Sie Datenbank `myplanio`
3. Klicken Sie auf Tabelle `users`
4. Klicken Sie oben auf "SQL"
5. Führen Sie aus:
   ```sql
   DELETE FROM users WHERE username = 'admin';
   
   INSERT INTO users (username, password_hash, full_name, is_approved, is_admin) 
   VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', TRUE, TRUE);
   ```

6. Oder verwenden Sie `test-password.php` um einen neuen Hash zu generieren

---

### Problem: "Call to undefined function password_hash()"

**Ursache:** PHP-Version zu alt.

**Lösung:**
- Mindestens PHP 7.4 erforderlich
- Prüfen Sie Version: `<?php echo PHP_VERSION; ?>`
- Aktualisieren Sie PHP in XAMPP/WAMP

---

### Problem: API-Aufrufe (Events/Gruppen) funktionieren nicht

**Prüfen Sie:**

1. **Browser-Konsole öffnen (F12)**
   - Gibt es rote Fehler?
   - Klicken Sie auf "Network" / "Netzwerk"
   - Versuchen Sie einen Termin zu erstellen
   - Schauen Sie welche Anfragen fehlschlagen

2. **Prüfen Sie Dateipfade:**
   ```
   myplanio/
   ├── api/
   │   ├── events.php    ← Muss existieren
   │   ├── groups.php    ← Muss existieren
   │   └── users.php     ← Muss existieren
   ```

3. **Testen Sie direkt:**
   - Öffnen Sie: `http://localhost/myplanio/api/events.php?action=list`
   - Erwartete Antwort: JSON mit "success" Feld
   - Wenn Fehler: Lesen Sie die Fehlermeldung

---

### Problem: CSS/JavaScript wird nicht geladen

**Prüfen Sie:**

1. **Browser-Konsole (F12):**
   - Gibt es 404-Fehler für CSS/JS-Dateien?

2. **Dateipfade prüfen:**
   ```
   myplanio/
   ├── css/
   │   └── style.css     ← Muss existieren
   ├── js/
   │   ├── calendar.js   ← Muss existieren
   │   └── groups.js     ← Muss existieren
   ```

3. **Wenn Sie NICHT im Root-Verzeichnis installiert haben:**
   - Die App erwartet `http://localhost/myplanio/`
   - NICHT `http://localhost/` oder ein Unterordner davon

---

## 📊 Diagnostik-Checkliste

Arbeiten Sie diese Checkliste ab:

- [ ] MySQL läuft (XAMPP/WAMP Control Panel)
- [ ] Datenbank `myplanio` existiert
- [ ] 5 Tabellen in Datenbank vorhanden (users, groups_table, group_members, events, event_participants)
- [ ] Admin-User in Tabelle `users` vorhanden
- [ ] `includes/config.php` hat korrekte DB-Zugangsdaten
- [ ] `test-session.php` zeigt Session Status = 3
- [ ] `test-password.php` zeigt Admin-Account gefunden
- [ ] Browser-Cache gelöscht
- [ ] Browser-Cookies gelöscht
- [ ] PHP Version 7.4 oder höher

---

## 🆘 Wenn nichts hilft

1. **Aktivieren Sie Fehlerausgabe:**
   In `includes/config.php` ganz oben:
   ```php
   error_reporting(E_ALL);
   ini_set('display_errors', 1);
   ```

2. **Prüfen Sie Apache/PHP Error Logs:**
   - XAMPP: `xampp/apache/logs/error.log`
   - WAMP: `wamp/logs/apache_error.log`
   - Linux: `/var/log/apache2/error.log`

3. **Erstellen Sie ein Minimal-Test:**
   Datei `test.php`:
   ```php
   <?php
   phpinfo();
   ```
   Schauen Sie ob PHP läuft und welche Version.

---

## ✅ Nach erfolgreicher Installation

1. **Löschen Sie die Test-Dateien:**
   - `test-session.php`
   - `test-password.php`

2. **Deaktivieren Sie Error-Display:**
   In `includes/config.php`:
   ```php
   error_reporting(0);
   ini_set('display_errors', 0);
   ```

3. **Ändern Sie das Admin-Passwort:**
   - Anmelden als admin
   - Profil → Passwort ändern

---

Viel Erfolg! 🎉
