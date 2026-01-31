<?php
require_once '../includes/config.php';
requireAdmin();

// Statistiken holen
$userStmt = $pdo->query("SELECT COUNT(*) as total FROM users");
$totalUsers = $userStmt->fetch()['total'];

$pendingStmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE is_approved = FALSE");
$pendingUsers = $pendingStmt->fetch()['total'];

$groupStmt = $pdo->query("SELECT COUNT(*) as total FROM groups_table");
$totalGroups = $groupStmt->fetch()['total'];

$eventStmt = $pdo->query("SELECT COUNT(*) as total FROM events");
$totalEvents = $eventStmt->fetch()['total'];

// Ausstehende Benutzer
$pendingUsersStmt = $pdo->query("
    SELECT user_id, username, full_name, email, created_at
    FROM users
    WHERE is_approved = FALSE
    ORDER BY created_at DESC
");
$pendingUsersList = $pendingUsersStmt->fetchAll();

// Alle Benutzer
$allUsersStmt = $pdo->query("
    SELECT user_id, username, full_name, email, is_approved, is_admin, created_at, last_login
    FROM users
    ORDER BY created_at DESC
");
$allUsersList = $allUsersStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - MyPlanio</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <h1>⚙️ MyPlanio Admin</h1>
            <nav>
                <a href="../index.php">Kalender</a>
                <a href="../groups.php">Gruppen</a>
                <a href="index.php">Admin</a>
                <a href="../logout.php">Abmelden</a>
            </nav>
        </div>
    </nav>

    <div class="container">
        <h2 style="margin: 30px 0 20px;">Dashboard</h2>
        
        <!-- Statistiken -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 40px;">
            <div class="card" style="text-align: center;">
                <h3 style="font-size: 40px; margin-bottom: 10px;"><?php echo $totalUsers; ?></h3>
                <p style="font-size: 18px; color: #666;">Benutzer gesamt</p>
            </div>
            
            <div class="card" style="text-align: center;">
                <h3 style="font-size: 40px; margin-bottom: 10px; color: <?php echo $pendingUsers > 0 ? '#000' : '#666'; ?>;">
                    <?php echo $pendingUsers; ?>
                </h3>
                <p style="font-size: 18px; color: #666;">Ausstehende Genehmigungen</p>
            </div>
            
            <div class="card" style="text-align: center;">
                <h3 style="font-size: 40px; margin-bottom: 10px;"><?php echo $totalGroups; ?></h3>
                <p style="font-size: 18px; color: #666;">Gruppen</p>
            </div>
            
            <div class="card" style="text-align: center;">
                <h3 style="font-size: 40px; margin-bottom: 10px;"><?php echo $totalEvents; ?></h3>
                <p style="font-size: 18px; color: #666;">Termine</p>
            </div>
        </div>

        <!-- Ausstehende Genehmigungen -->
        <?php if (count($pendingUsersList) > 0): ?>
            <div class="card" style="margin-bottom: 30px;">
                <div class="card-header">Ausstehende Benutzer-Genehmigungen</div>
                
                <table class="table">
                    <thead>
                        <tr>
                            <th>Benutzername</th>
                            <th>Name</th>
                            <th>E-Mail</th>
                            <th>Registriert am</th>
                            <th>Aktionen</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pendingUsersList as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email'] ?? '-'); ?></td>
                                <td><?php echo date('d.m.Y H:i', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <button class="btn btn-primary btn-small" onclick="approveUser(<?php echo $user['user_id']; ?>)">
                                        Genehmigen
                                    </button>
                                    <button class="btn btn-danger btn-small" onclick="deleteUser(<?php echo $user['user_id']; ?>)">
                                        Ablehnen
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <!-- Alle Benutzer -->
        <div class="card">
            <div class="card-header">Alle Benutzer</div>
            
            <table class="table">
                <thead>
                    <tr>
                        <th>Benutzername</th>
                        <th>Name</th>
                        <th>E-Mail</th>
                        <th>Status</th>
                        <th>Rolle</th>
                        <th>Registriert</th>
                        <th>Letzter Login</th>
                        <th>Aktionen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($allUsersList as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email'] ?? '-'); ?></td>
                            <td>
                                <?php if ($user['is_approved']): ?>
                                    <span style="color: #000; font-weight: bold;">✓ Aktiv</span>
                                <?php else: ?>
                                    <span style="color: #666;">⏳ Ausstehend</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($user['is_admin']): ?>
                                    <strong>Admin</strong>
                                <?php else: ?>
                                    Benutzer
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('d.m.Y', strtotime($user['created_at'])); ?></td>
                            <td><?php echo $user['last_login'] ? date('d.m.Y H:i', strtotime($user['last_login'])) : '-'; ?></td>
                            <td>
                                <?php if (!$user['is_admin']): ?>
                                    <button class="btn btn-small" onclick="toggleAdmin(<?php echo $user['user_id']; ?>, true)">
                                        Admin machen
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-small" onclick="toggleAdmin(<?php echo $user['user_id']; ?>, false)">
                                        Admin entfernen
                                    </button>
                                <?php endif; ?>
                                
                                <?php if ($user['username'] !== 'admin'): ?>
                                    <button class="btn btn-danger btn-small" onclick="deleteUser(<?php echo $user['user_id']; ?>)">
                                        Löschen
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="js/admin.js"></script>
</body>
</html>
