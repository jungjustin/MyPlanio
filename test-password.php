<?php
// Passwort-Hash Generator und Tester

echo "<h1>Passwort Hash Generator</h1>";

// Teste das Standard-Admin-Passwort
$test_password = 'admin123';
$expected_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

echo "<h2>Test: admin123</h2>";
echo "Passwort: admin123<br>";
echo "Erwarteter Hash: " . $expected_hash . "<br>";
echo "Verifizierung: " . (password_verify($test_password, $expected_hash) ? '<b style="color:green">OK ✓</b>' : '<b style="color:red">FEHLER ✗</b>') . "<br>";

echo "<hr>";

// Neuen Hash generieren
echo "<h2>Neuen Hash generieren</h2>";
$new_hash = password_hash('admin123', PASSWORD_DEFAULT);
echo "Neuer Hash für 'admin123': <br>";
echo "<code>" . $new_hash . "</code><br>";
echo "Verifizierung: " . (password_verify('admin123', $new_hash) ? '<b style="color:green">OK ✓</b>' : '<b style="color:red">FEHLER ✗</b>') . "<br>";

echo "<hr>";

// Datenbank-Check
echo "<h2>Datenbank Admin-Account Check</h2>";
try {
    require_once 'includes/config.php';
    
    $stmt = $pdo->query("SELECT user_id, username, password_hash, is_approved, is_admin FROM users WHERE username = 'admin'");
    $admin = $stmt->fetch();
    
    if ($admin) {
        echo "<b>Admin-Account gefunden:</b><br>";
        echo "User ID: " . $admin['user_id'] . "<br>";
        echo "Username: " . $admin['username'] . "<br>";
        echo "Genehmigt: " . ($admin['is_approved'] ? 'Ja' : 'Nein') . "<br>";
        echo "Admin: " . ($admin['is_admin'] ? 'Ja' : 'Nein') . "<br>";
        echo "Passwort-Hash: <code>" . substr($admin['password_hash'], 0, 50) . "...</code><br>";
        echo "Passwort 'admin123' funktioniert: " . (password_verify('admin123', $admin['password_hash']) ? '<b style="color:green">JA ✓</b>' : '<b style="color:red">NEIN ✗</b>') . "<br>";
    } else {
        echo "<b style='color:red'>FEHLER: Admin-Account nicht gefunden!</b><br>";
        echo "<br>SQL zum Erstellen eines Admin-Accounts:<br>";
        echo "<textarea rows='5' cols='80'>";
        echo "INSERT INTO users (username, password_hash, full_name, is_approved, is_admin) \n";
        echo "VALUES ('admin', '$new_hash', 'Administrator', TRUE, TRUE);";
        echo "</textarea>";
    }
    
} catch (PDOException $e) {
    echo "<b style='color:red'>Datenbankfehler:</b> " . $e->getMessage();
}

echo "<hr>";
echo '<p><a href="login.php">Zum Login</a> | <a href="test-session.php">Session-Test</a></p>';
?>
