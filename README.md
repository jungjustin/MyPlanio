# MyPlanio

Eine vollständige responsive Kalender-Anwendung mit Benutzerverwaltung, Gruppenkalendern und umfangreichen Terminfunktionen.

**Entwickelt von:** Justin Jung  
**Kontakt:** justinjung@t-online.de

---

## 📜 Lizenz & Nutzungsbedingungen

### Für private Nutzung:
✅ **Kostenlos nutzbar** mit Namensnennung  
✅ Sie dürfen die Software privat nutzen und anpassen  
⚠️ **Pflicht:** Namensnennung "MyPlanio - Entwickelt von Justin Jung"  
⚠️ **Pflicht:** Link zu justinjung@t-online.de muss erhalten bleiben

### Für geschäftliche/kommerzielle Nutzung:
📧 **Bitte kontaktieren Sie:** justinjung@t-online.de  
Für geschäftliche Nutzung ist eine separate Lizenzvereinbarung erforderlich.

### Verboten:
❌ Weiterverkauf ohne Genehmigung  
❌ Entfernung der Urheberrechtshinweise  
❌ Kommerzielle Nutzung ohne Lizenz

---

## Features

### Benutzerverwaltung
- ✅ Benutzerregistrierung mit Genehmigungssystem
- ✅ Admin-Panel zur Benutzerverwaltung
- ✅ Profilseiten mit Passwortänderung
- ✅ Rollen-System (Admin/Benutzer)

### Termine
- ✅ Private Termine erstellen, bearbeiten, löschen
- ✅ Start- und Endzeit (inkl. Ganztags-Option)
- ✅ Ortsangabe
- ✅ Farbauswahl (Schwarz-Weiß-Töne)
- ✅ Personen markieren
- ✅ Beschreibung hinzufügen

### Gruppen
- ✅ Gruppen erstellen und verwalten
- ✅ Mitglieder hinzufügen/entfernen
- ✅ Gruppenkalender für gemeinsame Termine
- ✅ Admin-Rollen in Gruppen

### Design
- ✅ Vollständig in Schwarz-Weiß gehalten
- ✅ Responsive Design für alle Geräte
- ✅ Moderne, klare Benutzeroberfläche
- ✅ Monatsansicht mit Kalender-Grid

## Installation

### Voraussetzungen
- PHP 7.4 oder höher
- MySQL 5.7 oder höher
- Webserver (Apache/Nginx)

### Schritt-für-Schritt Anleitung

#### 1. Dateien hochladen
Laden Sie alle Dateien in Ihr Webserver-Verzeichnis (z.B. `/var/www/html/kalender`).

#### 2. Datenbank einrichten

**Option A: phpMyAdmin verwenden**
1. Öffnen Sie phpMyAdmin
2. Erstellen Sie eine neue Datenbank: `myplanio`
3. Wählen Sie die Datenbank aus
4. Gehen Sie zum Tab "SQL"
5. Kopieren Sie den Inhalt von `database.sql` und führen Sie ihn aus

**Option B: MySQL Kommandozeile**
```bash
mysql -u root -p
```
```sql
CREATE DATABASE myplanio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE myplanio;
source /pfad/zu/database.sql;
```

#### 3. Datenbank-Konfiguration anpassen

Öffnen Sie die Datei `includes/config.php` und passen Sie folgende Zeilen an:

```php
define('DB_HOST', 'localhost');     // Ihr Datenbank-Host
define('DB_USER', 'root');          // Ihr Datenbank-Benutzername
define('DB_PASS', '');              // Ihr Datenbank-Passwort
define('DB_NAME', 'myplanio');      // Name der Datenbank
```

#### 4. Berechtigungen setzen (Linux/Mac)

```bash
chmod -R 755 /pfad/zur/kalender-app
chmod -R 777 /pfad/zur/kalender-app/uploads  # Falls Sie später einen Upload-Ordner hinzufügen
```

#### 5. Webserver konfigurieren

**Apache (.htaccess bereits vorhanden)**
Stellen Sie sicher, dass `mod_rewrite` aktiviert ist:
```bash
sudo a2enmod rewrite
sudo service apache2 restart
```

**Nginx - Beispielkonfiguration**
```nginx
server {
    listen 80;
    server_name ihr-domain.de;
    root /pfad/zur/kalender-app;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```

#### 6. Erste Anmeldung

**Standard Admin-Zugang:**
- Benutzername: `admin`
- Passwort: `admin123`

⚠️ **WICHTIG:** Ändern Sie das Admin-Passwort sofort nach der ersten Anmeldung!

## Verwendung

### Als Administrator

1. **Anmelden** mit Admin-Zugangsdaten
2. **Admin-Panel** öffnen über die Navigation
3. **Benutzer genehmigen**: Neue Registrierungen erscheinen unter "Ausstehende Genehmigungen"
4. **Benutzer verwalten**: Admin-Rechte vergeben, Benutzer löschen

### Als Benutzer

1. **Registrieren** über die Registrierungsseite
2. **Warten** auf Admin-Freigabe
3. Nach Freigabe **anmelden**
4. **Termine erstellen**:
   - Klick auf einen Tag im Kalender
   - Formular ausfüllen
   - Optional: Gruppe auswählen, Personen markieren, Farbe ändern
5. **Gruppen erstellen**:
   - Zu "Gruppen" navigieren
   - Neue Gruppe erstellen
   - Mitglieder hinzufügen

### Termine verwalten

**Termin erstellen:**
- Auf einen Tag klicken
- Titel eingeben (Pflichtfeld)
- Start/Ende-Zeit wählen
- Optional: Beschreibung, Ort, Farbe, Gruppe, Teilnehmer hinzufügen

**Termin bearbeiten:**
- Auf Termin im Kalender klicken
- "Bearbeiten" wählen
- Änderungen vornehmen
- Speichern

**Termin löschen:**
- Termin öffnen
- Auf "Löschen" klicken
- Bestätigen

### Gruppen verwalten

**Als Gruppenersteller/Admin:**
- Gruppe bearbeiten (Name, Beschreibung, Farbe)
- Mitglieder hinzufügen/entfernen
- Gruppe löschen

**Als Gruppenmitglied:**
- Termine zum Gruppenkalender hinzufügen
- Gruppentermine ansehen
- Gruppe verlassen

## Verzeichnisstruktur

```
kalender-app/
├── admin/                  # Admin-Panel
│   ├── api/               # Admin API-Endpunkte
│   ├── js/                # Admin JavaScript
│   └── index.php          # Admin Dashboard
├── api/                   # API-Endpunkte
│   ├── events.php         # Termin-API
│   ├── groups.php         # Gruppen-API
│   └── users.php          # Benutzer-API
├── css/                   # Stylesheets
│   └── style.css          # Haupt-CSS
├── includes/              # PHP-Includes
│   └── config.php         # Konfiguration & DB-Verbindung
├── js/                    # JavaScript-Dateien
│   ├── calendar.js        # Kalender-Funktionalität
│   └── groups.js          # Gruppen-Funktionalität
├── database.sql           # Datenbank-Schema
├── index.php              # Hauptkalender
├── login.php              # Login-Seite
├── register.php           # Registrierungs-Seite
├── groups.php             # Gruppenverwaltung
├── profile.php            # Benutzerprofil
├── logout.php             # Logout
└── README.md              # Diese Datei
```

## Sicherheitshinweise

1. **Passwörter ändern**: Ändern Sie das Standard-Admin-Passwort sofort
2. **HTTPS verwenden**: Verwenden Sie SSL/TLS für Produktionsumgebungen
3. **Datenbank-Zugangsdaten**: Schützen Sie die `config.php` vor direktem Zugriff
4. **Updates**: Halten Sie PHP und MySQL aktuell
5. **Backups**: Erstellen Sie regelmäßig Datenbank-Backups

## Troubleshooting

### "Datenbankverbindung fehlgeschlagen"
- Prüfen Sie die Zugangsdaten in `includes/config.php`
- Stellen Sie sicher, dass MySQL läuft
- Prüfen Sie, ob die Datenbank existiert

### "404 Not Found" bei API-Aufrufen
- Aktivieren Sie `mod_rewrite` (Apache)
- Prüfen Sie die Nginx-Konfiguration
- Stellen Sie sicher, dass `.htaccess` vorhanden ist (Apache)

### Termine werden nicht angezeigt
- Öffnen Sie die Browser-Konsole (F12) auf Fehler
- Prüfen Sie, ob die API-Endpunkte erreichbar sind
- Stellen Sie sicher, dass Sie angemeldet und genehmigt sind

### Session-Probleme
- Stellen Sie sicher, dass PHP Sessions schreiben kann
- Prüfen Sie: `session.save_path` in `php.ini`

## Browser-Kompatibilität

- ✅ Chrome/Edge (neueste Versionen)
- ✅ Firefox (neueste Versionen)
- ✅ Safari (neueste Versionen)
- ✅ Mobile Browser (iOS Safari, Chrome Mobile)

## Support & Weiterentwicklung

### Mögliche Erweiterungen
- E-Mail-Benachrichtigungen für Termine
- Import/Export von Kalendern (iCal)
- Erinnerungen und Notifications
- Wiederkehrende Termine
- Dateianhänge bei Terminen
- Wochenansicht und Tagesansicht
- Drag & Drop für Termine

## Lizenz

**MyPlanio** - © 2024 Justin Jung (justinjung@t-online.de)

**Private Nutzung:** Kostenlos mit Namensnennung  
**Geschäftliche Nutzung:** Lizenz erforderlich - Kontakt: justinjung@t-online.de

Diese Software wird "wie besehen" zur Verfügung gestellt, ohne jegliche Garantie.

## Credits

Entwickelt mit PHP, MySQL, JavaScript und viel ☕ von **Justin Jung**
