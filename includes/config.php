<?php
// Fehlerberichterstattung für Entwicklung (für Produktion auskommentieren)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Datenbank Konfiguration
define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // Hier deinen DB-Benutzer eintragen
define('DB_PASS', ''); // Hier dein DB-Passwort eintragen
define('DB_NAME', 'myplanio');

// Session starten BEVOR irgendwas ausgegeben wird
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_start();
}

// Datenbankverbindung herstellen
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch(PDOException $e) {
    die("Datenbankverbindung fehlgeschlagen: " . $e->getMessage());
}

// Hilfsfunktionen
function isLoggedIn() {
    return isset($_SESSION['user_id']) && 
           isset($_SESSION['is_approved']) && 
           $_SESSION['is_approved'] === true;
}

function isAdmin() {
    return isLoggedIn() && 
           isset($_SESSION['is_admin']) && 
           $_SESSION['is_admin'] === true;
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . getBaseUrl() . 'login.php');
        exit;
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: ' . getBaseUrl() . 'index.php');
        exit;
    }
}

function getBaseUrl() {
    // Ermittelt die Basis-URL basierend auf dem aktuellen Pfad
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $script = $_SERVER['SCRIPT_NAME'];
    
    // Entferne den Dateinamen, behalte nur den Pfad
    $path = dirname($script);
    
    // Wenn wir im admin-Ordner sind, gehe eine Ebene hoch
    if (strpos($path, '/admin') !== false) {
        $path = dirname($path);
    }
    
    return $protocol . '://' . $host . $path . '/';
}

// JSON Response Funktion
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
?>
