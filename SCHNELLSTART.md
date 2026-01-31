# 🚀 SCHNELLSTART - MyPlanio

**Entwickelt von:** Justin Jung | **Kontakt:** justinjung@t-online.de

---

## 📜 LIZENZHINWEIS

**Private Nutzung:** ✅ Kostenlos mit Namensnennung  
**Geschäftliche Nutzung:** 📧 Lizenz erforderlich - justinjung@t-online.de

---

## ⚡ In 5 Minuten startklar!

### Schritt 1: Entpacken
Entpacken Sie `myplanio.zip` in Ihr Webserver-Verzeichnis:
- XAMPP: `C:\xampp\htdocs\myplanio`
- WAMP: `C:\wamp\www\myplanio`
- Linux: `/var/www/html/myplanio`

### Schritt 2: Datenbank erstellen

**Mit phpMyAdmin:**
1. Öffnen Sie phpMyAdmin (meist unter `http://localhost/phpmyadmin`)
2. Klicken Sie auf "Neu" (neue Datenbank)
3. Name: `myplanio`
4. Zeichenkodierung: `utf8mb4_unicode_ci`
5. "Erstellen" klicken
6. Datenbank auswählen → Tab "SQL"
7. Öffnen Sie die Datei `database.sql` im Editor
8. Kopieren Sie den kompletten Inhalt
9. Fügen Sie ihn in das SQL-Feld ein
10. "OK" klicken

**Mit MySQL Kommandozeile (für Fortgeschrittene):**
```bash
mysql -u root -p
CREATE DATABASE myplanio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE myplanio;
SOURCE /pfad/zu/myplanio/database.sql;
EXIT;
```

### Schritt 3: Datenbank-Verbindung konfigurieren

Öffnen Sie `includes/config.php` in einem Editor und passen Sie an:

```php
define('DB_HOST', 'localhost');     // Normalerweise 'localhost'
define('DB_USER', 'root');          // Ihr MySQL-Benutzername
define('DB_PASS', '');              // Ihr MySQL-Passwort (oft leer bei XAMPP)
define('DB_NAME', 'myplanio');      // Name der Datenbank
```

**Typische Einstellungen:**
- **XAMPP/WAMP**: User: `root`, Passwort: `` (leer)
- **Linux**: User: `root`, Passwort: Ihr gesetztes Passwort

### Schritt 4: Im Browser öffnen

Öffnen Sie in Ihrem Browser:
```
http://localhost/myplanio
```

### Schritt 5: Erste Anmeldung (Admin)

```
Benutzername: admin
Passwort: admin123
```

⚠️ **WICHTIG:** Gehen Sie sofort zu "Profil" und ändern Sie das Passwort!

---

## 📋 Schnellübersicht Funktionen

### Als Admin:
1. **Benutzer genehmigen**: Admin-Panel → Ausstehende Genehmigungen
2. **Admin-Rechte vergeben**: Admin-Panel → Alle Benutzer → "Admin machen"

### Als Benutzer:
1. **Registrieren**: Auf der Login-Seite "Jetzt registrieren"
2. **Termin erstellen**: Auf einen Tag im Kalender klicken
3. **Gruppe erstellen**: Menü "Gruppen" → "Neue Gruppe erstellen"

---

## ⚙️ Systemanforderungen

**Minimal:**
- PHP 7.4+
- MySQL 5.7+
- Webserver (Apache/Nginx)
- 50 MB freier Speicher

**Empfohlen:**
- PHP 8.0+
- MySQL 8.0+
- Apache mit mod_rewrite
- 100 MB freier Speicher

---

## 🔧 Häufige Probleme

### Problem: "Datenbankverbindung fehlgeschlagen"
**Lösung:**
1. Prüfen Sie `includes/config.php` - sind die Zugangsdaten korrekt?
2. Ist MySQL gestartet?
3. Existiert die Datenbank `kalender_app`?

### Problem: Weiße Seite / Keine Anzeige
**Lösung:**
1. Aktivieren Sie PHP-Fehlerausgabe: In `includes/config.php` oben einfügen:
   ```php
   ini_set('display_errors', 1);
   error_reporting(E_ALL);
   ```
2. Prüfen Sie die Apache/PHP Error-Logs

### Problem: "Ihr Konto wurde noch nicht freigegeben"
**Lösung:**
1. Melden Sie sich mit dem Admin-Account an (`admin` / `admin123`)
2. Gehen Sie zum Admin-Panel
3. Genehmigen Sie den Benutzer unter "Ausstehende Genehmigungen"

### Problem: CSS/JS wird nicht geladen
**Lösung:**
1. Prüfen Sie die Browser-Konsole (F12)
2. Stellen Sie sicher, dass die Ordner `css/` und `js/` existieren
3. Prüfen Sie Dateipfade - evtl. Anpassung nötig wenn nicht im Root-Verzeichnis

---

## 📱 Mobile Nutzung

Die App ist vollständig responsive! Nutzen Sie sie auf:
- Smartphones (iOS/Android)
- Tablets
- Desktop-Computern

---

## 🎨 Design-Features

✅ Komplettes Schwarz-Weiß-Design
✅ Responsive für alle Bildschirmgrößen
✅ Moderne, klare Oberfläche
✅ Monatskalender mit Grid-Ansicht

---

## 📞 Support

Bei Problemen:
1. Prüfen Sie die `README.md` für Details
2. Schauen Sie in die Browser-Konsole (F12)
3. Prüfen Sie PHP/Apache Error-Logs

---

## ✨ Erste Schritte nach der Installation

1. **Admin-Passwort ändern** (Profil → Passwort ändern)
2. **Testbenutzer registrieren** (zum Testen der Genehmigung)
3. **Erste Gruppe erstellen** (Gruppen → Neue Gruppe)
4. **Ersten Termin anlegen** (Klick auf Kalendertag)

---

Viel Erfolg mit MyPlanio! 🎉

**© 2024 Justin Jung** | justinjung@t-online.de  
Private Nutzung kostenlos mit Namensnennung | Geschäftliche Nutzung: Lizenz erforderlich

