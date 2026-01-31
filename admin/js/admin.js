async function approveUser(userId) {
    if (!confirm('Möchten Sie diesen Benutzer wirklich genehmigen?')) {
        return;
    }
    
    try {
        const response = await fetch('api/users.php?action=approve', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ user_id: userId })
        });
        
        const data = await response.json();
        
        if (data.success) {
            location.reload();
        } else {
            alert('Fehler: ' + (data.message || 'Unbekannter Fehler'));
        }
    } catch (error) {
        console.error('Fehler:', error);
        alert('Fehler beim Genehmigen des Benutzers');
    }
}

async function deleteUser(userId) {
    if (!confirm('Möchten Sie diesen Benutzer wirklich löschen? Alle zugehörigen Daten werden ebenfalls gelöscht.')) {
        return;
    }
    
    try {
        const response = await fetch('api/users.php?action=delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ user_id: userId })
        });
        
        const data = await response.json();
        
        if (data.success) {
            location.reload();
        } else {
            alert('Fehler: ' + (data.message || 'Unbekannter Fehler'));
        }
    } catch (error) {
        console.error('Fehler:', error);
        alert('Fehler beim Löschen des Benutzers');
    }
}

async function toggleAdmin(userId, isAdmin) {
    const action = isAdmin ? 'Admin-Rechte erteilen' : 'Admin-Rechte entfernen';
    
    if (!confirm(`Möchten Sie wirklich ${action}?`)) {
        return;
    }
    
    try {
        const response = await fetch('api/users.php?action=toggle_admin', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ 
                user_id: userId,
                is_admin: isAdmin
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            location.reload();
        } else {
            alert('Fehler: ' + (data.message || 'Unbekannter Fehler'));
        }
    } catch (error) {
        console.error('Fehler:', error);
        alert('Fehler beim Ändern der Benutzerrechte');
    }
}
