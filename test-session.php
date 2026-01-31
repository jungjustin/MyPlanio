<?php
// Debug-Datei für Session-Tests
session_start();

echo "<h1>Session Debug</h1>";
echo "<pre>";

echo "PHP Version: " . PHP_VERSION . "\n";
echo "Session Status: " . session_status() . " (1=disabled, 2=none, 3=active)\n";
echo "Session ID: " . session_id() . "\n\n";

echo "Session Daten:\n";
print_r($_SESSION);

echo "\n\nPHP Info für Sessions:\n";
echo "session.save_path: " . ini_get('session.save_path') . "\n";
echo "session.save_handler: " . ini_get('session.save_handler') . "\n";
echo "session.use_cookies: " . ini_get('session.use_cookies') . "\n";
echo "session.cookie_lifetime: " . ini_get('session.cookie_lifetime') . "\n";

// Test: Session-Variable setzen
if (!isset($_SESSION['test'])) {
    $_SESSION['test'] = 'Session funktioniert!';
}

echo "\n\nTest-Variable: " . ($_SESSION['test'] ?? 'NICHT GESETZT') . "\n";

// Datenbank-Test
echo "\n\nDatenbank-Test:\n";
try {
    require_once 'includes/config.php';
    echo "Datenbank-Verbindung: OK\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    echo "Anzahl Benutzer: " . $result['count'] . "\n";
    
    $adminStmt = $pdo->query("SELECT username, is_approved, is_admin FROM users WHERE username = 'admin'");
    $admin = $adminStmt->fetch();
    if ($admin) {
        echo "\nAdmin-Account gefunden:\n";
        print_r($admin);
    } else {
        echo "\nAdmin-Account NICHT gefunden!\n";
    }
    
} catch (PDOException $e) {
    echo "Datenbank-Fehler: " . $e->getMessage() . "\n";
}

echo "</pre>";

echo "<hr>";
echo '<p><a href="login.php">Zurück zum Login</a></p>';
?>
