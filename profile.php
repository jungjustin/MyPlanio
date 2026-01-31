<?php
require_once 'includes/config.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Aktuelle Benutzerdaten laden
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($full_name)) {
        $error = 'Name ist erforderlich.';
    } elseif (!empty($new_password) && $new_password !== $confirm_password) {
        $error = 'Passwörter stimmen nicht überein.';
    } elseif (!empty($new_password) && strlen($new_password) < 6) {
        $error = 'Passwort muss mindestens 6 Zeichen lang sein.';
    } else {
        if (!empty($new_password)) {
            $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $updateStmt = $pdo->prepare("
                UPDATE users 
                SET full_name = ?, email = ?, password_hash = ?
                WHERE user_id = ?
            ");
            $updateStmt->execute([$full_name, $email, $password_hash, $user_id]);
        } else {
            $updateStmt = $pdo->prepare("
                UPDATE users 
                SET full_name = ?, email = ?
                WHERE user_id = ?
            ");
            $updateStmt->execute([$full_name, $email, $user_id]);
        }
        
        $_SESSION['full_name'] = $full_name;
        $success = 'Profil erfolgreich aktualisiert.';
        
        // Daten neu laden
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - MyPlanio</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <h1>📅 MyPlanio</h1>
            <nav>
                <a href="index.php">Kalender</a>
                <a href="groups.php">Gruppen</a>
                <?php if ($_SESSION['is_admin']): ?>
                    <a href="admin/">Admin</a>
                <?php endif; ?>
                <a href="logout.php">Abmelden</a>
            </nav>
        </div>
    </nav>

    <div class="container">
        <div style="max-width: 600px; margin: 50px auto;">
            <h2 style="margin-bottom: 30px;">Mein Profil</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <div class="card">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="username">Benutzername</label>
                        <input type="text" id="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                        <small>Benutzername kann nicht geändert werden</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="full_name">Vollständiger Name *</label>
                        <input type="text" id="full_name" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">E-Mail</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>">
                    </div>
                    
                    <hr style="margin: 30px 0; border: 1px solid #e5e5e5;">
                    
                    <h3 style="margin-bottom: 20px; font-size: 18px;">Passwort ändern</h3>
                    <p style="color: #666; margin-bottom: 20px; font-size: 14px;">Lassen Sie die Felder leer, wenn Sie Ihr Passwort nicht ändern möchten.</p>
                    
                    <div class="form-group">
                        <label for="new_password">Neues Passwort</label>
                        <input type="password" id="new_password" name="new_password" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Passwort bestätigen</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control">
                    </div>
                    
                    <div style="display: flex; gap: 10px; margin-top: 30px;">
                        <button type="submit" class="btn btn-primary" style="flex: 1;">Speichern</button>
                        <a href="index.php" class="btn" style="flex: 1;">Abbrechen</a>
                    </div>
                </form>
            </div>
            
            <div class="card" style="margin-top: 20px;">
                <h3 style="margin-bottom: 15px; font-size: 18px;">Kontoinformationen</h3>
                <p><strong>Registriert am:</strong> <?php echo date('d.m.Y H:i', strtotime($user['created_at'])); ?></p>
                <p><strong>Letzter Login:</strong> <?php echo $user['last_login'] ? date('d.m.Y H:i', strtotime($user['last_login'])) : 'Nie'; ?></p>
                <p><strong>Rolle:</strong> <?php echo $user['is_admin'] ? 'Administrator' : 'Benutzer'; ?></p>
            </div>
        </div>
    </div>
</body>
</html>
