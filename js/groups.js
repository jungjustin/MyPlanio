let groups = [];
let allUsers = [];
let selectedColor = '#ffb3ba';
let currentGroupId = null;

document.addEventListener('DOMContentLoaded', function() {
    loadGroups();
    loadAllUsers();
    setupEventListeners();
});

function setupEventListeners() {
    document.getElementById('groupForm').addEventListener('submit', saveGroup);
    
    // Farb-Picker
    document.querySelectorAll('.color-option').forEach(option => {
        option.addEventListener('click', function() {
            document.querySelectorAll('.color-option').forEach(o => o.classList.remove('selected'));
            this.classList.add('selected');
            selectedColor = this.dataset.color;
            document.getElementById('groupColor').value = selectedColor;
        });
    });
    
    // User Menu Toggle
    const userMenuBtn = document.getElementById('userMenuBtn');
    if (userMenuBtn) {
        userMenuBtn.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('userDropdown').classList.toggle('active');
        });
    }
    
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.user-menu')) {
            const dropdown = document.getElementById('userDropdown');
            if (dropdown) {
                dropdown.classList.remove('active');
            }
        }
    });
}

async function loadGroups() {
    try {
        const response = await fetch('api/groups.php?action=list');
        const data = await response.json();
        
        if (data.success) {
            groups = data.groups;
            renderGroups();
        }
    } catch (error) {
        console.error('Fehler beim Laden der Gruppen:', error);
    }
}

async function loadAllUsers() {
    try {
        const response = await fetch('api/users.php?action=list');
        const data = await response.json();
        
        if (data.success) {
            allUsers = data.users;
        }
    } catch (error) {
        console.error('Fehler beim Laden der Benutzer:', error);
    }
}

function renderGroups() {
    const container = document.getElementById('groupsList');
    
    if (groups.length === 0) {
        container.innerHTML = `
            <div class="card" style="text-align: center; padding: 60px 20px;">
                <h3 style="margin-bottom: 10px;">Keine Gruppen</h3>
                <p style="color: #666;">Erstellen Sie Ihre erste Gruppe, um mit anderen zu teilen.</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = '';
    
    groups.forEach(group => {
        const card = document.createElement('div');
        card.className = 'card';
        card.style.borderLeft = `6px solid ${group.color}`;
        card.innerHTML = `
            <div style="display: flex; justify-content: space-between; align-items: start;">
                <div style="flex: 1;">
                    <h3 style="margin-bottom: 10px; font-size: 20px;">
                        <span style="display: inline-block; width: 20px; height: 20px; background-color: ${group.color}; border: 2px solid var(--border); margin-right: 10px; vertical-align: middle; border-radius: 4px;"></span>
                        ${group.group_name}
                    </h3>
                    ${group.description ? `<p style="color: var(--text-secondary); margin-bottom: 15px;">${group.description}</p>` : ''}
                    <p style="font-size: 14px;">
                        <strong>Mitglieder:</strong> ${group.member_count}
                        ${group.user_is_admin ? ' <span style="background: var(--pastel-blue); color: var(--bg-primary); padding: 2px 8px; font-size: 12px; margin-left: 10px; border-radius: 3px; font-weight: 600;">ADMIN</span>' : ''}
                    </p>
                </div>
                <div style="display: flex; gap: 10px;">
                    <button class="btn btn-small" onclick="showMembers(${group.group_id})">Mitglieder</button>
                    ${group.user_is_admin ? `
                        <button class="btn btn-small" onclick="editGroup(${group.group_id})">Bearbeiten</button>
                        <button class="btn btn-danger btn-small" onclick="deleteGroup(${group.group_id})">Löschen</button>
                    ` : `
                        <button class="btn btn-danger btn-small" onclick="leaveGroup(${group.group_id})">Verlassen</button>
                    `}
                </div>
            </div>
        `;
        container.appendChild(card);
    });
}

function openCreateGroupModal() {
    document.getElementById('groupModalTitle').textContent = 'Neue Gruppe erstellen';
    document.getElementById('groupForm').reset();
    document.getElementById('groupId').value = '';
    
    // Farbe zurücksetzen
    document.querySelectorAll('.color-option').forEach(o => o.classList.remove('selected'));
    document.querySelector('.color-option[data-color="#ffb3ba"]').classList.add('selected');
    selectedColor = '#ffb3ba';
    
    document.getElementById('createGroupModal').classList.add('active');
}

function closeCreateGroupModal() {
    document.getElementById('createGroupModal').classList.remove('active');
}

function editGroup(groupId) {
    const group = groups.find(g => g.group_id == groupId);
    if (!group) return;
    
    document.getElementById('groupModalTitle').textContent = 'Gruppe bearbeiten';
    document.getElementById('groupId').value = group.group_id;
    document.getElementById('groupName').value = group.group_name;
    document.getElementById('groupDescription').value = group.description || '';
    
    // Farbe setzen
    selectedColor = group.color;
    document.getElementById('groupColor').value = group.color;
    document.querySelectorAll('.color-option').forEach(o => o.classList.remove('selected'));
    const colorOption = document.querySelector(`.color-option[data-color="${group.color}"]`);
    if (colorOption) colorOption.classList.add('selected');
    
    document.getElementById('createGroupModal').classList.add('active');
}

async function saveGroup(e) {
    e.preventDefault();
    
    const groupId = document.getElementById('groupId').value;
    const groupData = {
        group_id: groupId || null,
        group_name: document.getElementById('groupName').value,
        description: document.getElementById('groupDescription').value,
        color: selectedColor
    };
    
    const action = groupId ? 'update' : 'create';
    
    try {
        const response = await fetch(`api/groups.php?action=${action}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(groupData)
        });
        
        const data = await response.json();
        
        if (data.success) {
            closeCreateGroupModal();
            loadGroups();
        } else {
            alert('Fehler: ' + (data.message || 'Unbekannter Fehler'));
        }
    } catch (error) {
        console.error('Fehler beim Speichern:', error);
        alert('Fehler beim Speichern der Gruppe');
    }
}

async function deleteGroup(groupId) {
    if (!confirm('Möchten Sie diese Gruppe wirklich löschen? Alle Gruppentermine werden ebenfalls gelöscht.')) {
        return;
    }
    
    try {
        const response = await fetch('api/groups.php?action=delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ group_id: groupId })
        });
        
        const data = await response.json();
        
        if (data.success) {
            loadGroups();
        } else {
            alert('Fehler: ' + (data.message || 'Unbekannter Fehler'));
        }
    } catch (error) {
        console.error('Fehler beim Löschen:', error);
        alert('Fehler beim Löschen der Gruppe');
    }
}

async function leaveGroup(groupId) {
    if (!confirm('Möchten Sie diese Gruppe wirklich verlassen?')) {
        return;
    }
    
    try {
        const response = await fetch('api/groups.php?action=leave', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ group_id: groupId })
        });
        
        const data = await response.json();
        
        if (data.success) {
            loadGroups();
        } else {
            alert('Fehler: ' + (data.message || 'Unbekannter Fehler'));
        }
    } catch (error) {
        console.error('Fehler beim Verlassen:', error);
        alert('Fehler beim Verlassen der Gruppe');
    }
}

async function showMembers(groupId) {
    currentGroupId = groupId;
    const group = groups.find(g => g.group_id == groupId);
    
    document.getElementById('membersModalTitle').textContent = `Mitglieder von "${group.group_name}"`;
    
    try {
        const response = await fetch(`api/groups.php?action=members&group_id=${groupId}`);
        const data = await response.json();
        
        if (data.success) {
            renderMembers(data.members, group.user_is_admin);
        }
    } catch (error) {
        console.error('Fehler beim Laden der Mitglieder:', error);
    }
    
    document.getElementById('membersModal').classList.add('active');
}

function renderMembers(members, isAdmin) {
    const container = document.getElementById('membersList');
    
    if (members.length === 0) {
        container.innerHTML = '<p>Keine Mitglieder</p>';
        return;
    }
    
    let html = '<table class="table"><thead><tr><th>Name</th><th>Benutzername</th><th>Rolle</th>';
    if (isAdmin) {
        html += '<th>Aktionen</th>';
    }
    html += '</tr></thead><tbody>';
    
    members.forEach(member => {
        html += `
            <tr>
                <td>${member.full_name}</td>
                <td>${member.username}</td>
                <td>${member.is_admin ? '<strong>Admin</strong>' : 'Mitglied'}</td>
        `;
        
        if (isAdmin && !member.is_admin) {
            html += `
                <td>
                    <button class="btn btn-danger btn-small" onclick="removeMember(${member.user_id})">Entfernen</button>
                </td>
            `;
        } else if (isAdmin) {
            html += '<td>-</td>';
        }
        
        html += '</tr>';
    });
    
    html += '</tbody></table>';
    container.innerHTML = html;
    
    // Add member section nur für Admins anzeigen
    document.getElementById('addMemberSection').style.display = isAdmin ? 'block' : 'none';
    
    if (isAdmin) {
        updateNewMemberSelect(members);
    }
}

function updateNewMemberSelect(currentMembers) {
    const select = document.getElementById('newMemberSelect');
    select.innerHTML = '<option value="">Benutzer auswählen...</option>';
    
    const memberIds = currentMembers.map(m => m.user_id);
    
    allUsers.forEach(user => {
        if (!memberIds.includes(user.user_id)) {
            const option = document.createElement('option');
            option.value = user.user_id;
            option.textContent = `${user.full_name} (${user.username})`;
            select.appendChild(option);
        }
    });
}

async function addMember() {
    const userId = document.getElementById('newMemberSelect').value;
    
    if (!userId) {
        alert('Bitte wählen Sie einen Benutzer aus');
        return;
    }
    
    try {
        const response = await fetch('api/groups.php?action=add_member', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                group_id: currentGroupId,
                user_id: userId
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showMembers(currentGroupId); // Neu laden
        } else {
            alert('Fehler: ' + (data.message || 'Unbekannter Fehler'));
        }
    } catch (error) {
        console.error('Fehler beim Hinzufügen:', error);
        alert('Fehler beim Hinzufügen des Mitglieds');
    }
}

async function removeMember(userId) {
    if (!confirm('Möchten Sie dieses Mitglied wirklich entfernen?')) {
        return;
    }
    
    try {
        const response = await fetch('api/groups.php?action=remove_member', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                group_id: currentGroupId,
                user_id: userId
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showMembers(currentGroupId); // Neu laden
        } else {
            alert('Fehler: ' + (data.message || 'Unbekannter Fehler'));
        }
    } catch (error) {
        console.error('Fehler beim Entfernen:', error);
        alert('Fehler beim Entfernen des Mitglieds');
    }
}

function closeMembersModal() {
    document.getElementById('membersModal').classList.remove('active');
}
