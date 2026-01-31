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
    $password_confirm = $_POST['password_confirm'] ?? '';
    $email = trim($_POST['email'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    
    if (empty($username) || empty($password) || empty($full_name)) {
        $error = 'Bitte alle Pflichtfelder ausfüllen.';
    } elseif (strlen($username) < 3) {
        $error = 'Benutzername muss mindestens 3 Zeichen lang sein.';
    } elseif ($password !== $password_confirm) {
        $error = 'Passwörter stimmen nicht überein.';
    } elseif (strlen($password) < 6) {
        $error = 'Passwort muss mindestens 6 Zeichen lang sein.';
    } else {
        try {
            // Prüfen ob Benutzername bereits existiert
            $stmt = $pdo->prepare("SELECT user_id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            
            if ($stmt->fetch()) {
                $error = 'Benutzername bereits vergeben.';
            } else {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                
                $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, email, full_name, is_approved) VALUES (?, ?, ?, ?, FALSE)");
                
                if ($stmt->execute([$username, $password_hash, $email, $full_name])) {
                    $success = 'Registrierung erfolgreich! Ihr Konto muss von einem Administrator freigegeben werden.';
                    // Formular leeren
                    $username = $password = $password_confirm = $email = $full_name = '';
                } else {
                    $error = 'Fehler bei der Registrierung. Bitte versuchen Sie es erneut.';
                }
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
    <title>Registrierung - MyPlanio</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="auth-container">
        <h2>Registrieren</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Benutzername *</label>
                <input type="text" id="username" name="username" class="form-control" value="<?php echo htmlspecialchars($username ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="full_name">Vollständiger Name *</label>
                <input type="text" id="full_name" name="full_name" class="form-control" value="<?php echo htmlspecialchars($full_name ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">E-Mail</label>
                <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Passwort *</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="password_confirm">Passwort bestätigen *</label>
                <input type="password" id="password_confirm" name="password_confirm" class="form-control" required>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%;">Registrieren</button>
        </form>
        
        <div class="auth-links">
            <p>Bereits ein Konto? <a href="login.php">Jetzt anmelden</a></p>
        </div>
    </div>
    
    <footer style="text-align: center; padding: 20px; color: var(--text-muted); font-size: 12px;">
        <p>MyPlanio - Entwickelt von <a href="mailto:justinjung@t-online.de" style="color: var(--pastel-blue);">Justin Jung</a></p>
    </footer>
</body>
</html>
