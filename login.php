<?php
require_once 'includes/config.php';

// Wenn bereits angemeldet, zum Kalender weiterleiten
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Bitte alle Felder ausfüllen.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password_hash'])) {
                if (!$user['is_approved']) {
                    $error = 'Ihr Konto wurde noch nicht freigegeben. Bitte warten Sie auf die Bestätigung eines Administrators.';
                } else {
                    // Session-Daten setzen
                    $_SESSION['user_id'] = (int)$user['user_id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['full_name'] = $user['full_name'];
                    $_SESSION['is_admin'] = (bool)$user['is_admin'];
                    $_SESSION['is_approved'] = (bool)$user['is_approved'];
                    
                    // Letzten Login aktualisieren
                    $updateStmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE user_id = ?");
                    $updateStmt->execute([$user['user_id']]);
                    
                    // Weiterleitung
                    header('Location: index.php');
                    exit;
                }
            } else {
                $error = 'Ungültige Anmeldedaten.';
            }
        } catch (PDOException $e) {
            $error = 'Datenbankfehler: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MyPlanio</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="auth-container">
        <h2>Anmelden</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Benutzername</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="password">Passwort</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%;">Anmelden</button>
        </form>
        
        <div class="auth-links">
            <p>Noch kein Konto? <a href="register.php">Jetzt registrieren</a></p>
        </div>
    </div>
    
    <footer style="text-align: center; padding: 20px; color: var(--text-muted); font-size: 12px;">
        <p>MyPlanio - Entwickelt von <a href="mailto:justinjung@t-online.de" style="color: var(--pastel-blue);">Justin Jung</a></p>
    </footer>
</body>
</html>
