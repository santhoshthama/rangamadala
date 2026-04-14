// ===================================
// USER MANAGEMENT FUNCTIONALITY (ADM-01, ADM-02)
// ===================================

let allUsers = [];

// Initialize user management view
function initUserManagementView() {
  // Filter buttons for user management
  const userFilterButtons = document.querySelectorAll('.filter-btn[data-target="users"]');
  userFilterButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      userFilterButtons.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      const filter = btn.getAttribute('data-filter');
      filterUsers(filter);
    });
  });
  
  // Load users when view is accessed
  const usersNav = document.querySelector('[data-view="users"]');
  if (usersNav) {
    usersNav.addEventListener('click', loadAllUsers);
  }
}

// Load all users for the user management table
function loadAllUsers() {
  const loading = document.getElementById('usersLoading');
  const empty = document.getElementById('usersEmpty');
  const table = document.getElementById('usersTable');
  const tbody = document.getElementById('usersTableBody');
  
  if (!loading || !empty || !table || !tbody) return;
  
  // Show loading
  loading.style.display = 'block';
  empty.style.display = 'none';
  table.style.display = 'none';
  
  // Fetch all users
  fetch(ROOT + '/admindashboard/getAllUsers')
    .then(response => response.json())
    .then(data => {
      allUsers = data;
      loading.style.display = 'none';
      
      if (data.length === 0) {
        empty.style.display = 'block';
      } else {
        table.style.display = 'table';
        renderUsers(data);
      }
    })
    .catch(error => {
      console.error('Error loading users:', error);
      loading.style.display = 'none';
      empty.style.display = 'block';
    });
}

// Render users in the table
function renderUsers(users) {
  const tbody = document.getElementById('usersTableBody');
  if (!tbody) return;
  
  tbody.innerHTML = '';
  
  users.forEach(user => {
    const tr = document.createElement('tr');
    tr.setAttribute('data-role', user.role);
    tr.setAttribute('data-user-id', user.id);
    
    const initials = user.full_name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
    const formattedDate = new Date(user.created_at).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    });
    
    // Determine status badge
    let statusBadge = '';
    if (user.is_verified == 1 && user.verification_status === 'approved') {
      statusBadge = '<span class="status-badge success">Active</span>';
    } else if (user.verification_status === 'pending' || !user.verification_status) {
      statusBadge = '<span class="status-badge warning">Pending</span>';
    } else if (user.verification_status === 'rejected') {
      statusBadge = '<span class="status-badge danger">Rejected</span>';
    } else {
      statusBadge = '<span class="status-badge">' + (user.verification_status || 'Unknown') + '</span>';
    }
    
    // Format role display
    const roleDisplay = user.role.replace('_', ' ');
    const roleClass = user.role.replace('_', '-');
    
    tr.innerHTML = `
      <td>
        <div class="user-cell">
          <div class="user-avatar">${initials}</div>
          <div class="user-info">
            <h4>${escapeHtml(user.full_name)}</h4>
            <p>${escapeHtml(user.email)}</p>
          </div>
        </div>
      </td>
      <td>
        <span class="role-badge ${roleClass}">${roleDisplay}</span>
      </td>
      <td>${escapeHtml(user.phone) || 'N/A'}</td>
      <td>${statusBadge}</td>
      <td>${formattedDate}</td>
      <td>
        <div class="action-buttons">
          <button class="btn btn-secondary btn-sm" onclick="showEditUserModal(${user.id})" title="Edit User">
            <span class="bx bx-edit"></span>
          </button>
          <button class="btn btn-danger btn-sm" onclick="confirmRemoveUser(${user.id}, '${escapeHtml(user.full_name)}')" title="Remove User">
            <span class="bx bx-trash"></span>
          </button>
        </div>
      </td>
    `;
    
    tbody.appendChild(tr);
  });
}

// Filter users by role
function filterUsers(filter) {
  const rows = document.querySelectorAll('#usersTableBody tr');
  rows.forEach(row => {
    if (filter === 'all') {
      row.style.display = '';
    } else {
      const role = row.getAttribute('data-role');
      row.style.display = role === filter ? '' : 'none';
    }
  });
}

// Show Add User Modal
function showAddUserModal() {
  const modalHTML = `
    <div class="modal-overlay active" id="addUserModal">
      <div class="modal-content user-form-modal">
        <div class="modal-header">
          <h3>Add New User</h3>
          <button class="modal-close" onclick="closeAddUserModal()">
            <span class="bx bx-x"></span>
          </button>
        </div>
        <div class="modal-body">
          <form id="addUserForm">
            <div class="input-box">
              <input type="text" id="addUserName" placeholder="Full Name" required />
              <i class="bx bx-user"></i>
            </div>
            <div class="input-box">
              <input type="email" id="addUserEmail" placeholder="Email Address" required />
              <i class="bx bx-envelope"></i>
            </div>
            <div class="input-box">
              <input type="tel" id="addUserPhone" placeholder="Phone Number" />
              <i class="bx bx-phone"></i>
            </div>
            <div class="input-box select-box">
              <select id="addUserRole" required>
                <option value="">Select a role</option>
                <option value="artist">Artist</option>
                <option value="audience">Audience</option>
                <option value="service_provider">Service Provider</option>
              </select>
              <i class="bx bx-badge">badge</i>
            </div>
            <div class="input-box">
              <input type="password" id="addUserPassword" placeholder="Password (min 6 characters)" required minlength="6" />
              <i class="bx bx-lock"></i>
            </div>
            <div class="input-box">
              <input type="password" id="addUserConfirmPassword" placeholder="Confirm Password" required minlength="6" />
              <i class="bx bx-lock-reset"></i>
            </div>
            <p class="form-note">
              <span class="bx bx-info-circle"></span>
              Users added by admin are automatically verified and can log in immediately.
            </p>
          </form>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" onclick="closeAddUserModal()">Cancel</button>
          <button class="btn btn-primary" onclick="submitAddUser()">
            <span class="bx bx-user-plus"></span>
            Add User
          </button>
        </div>
      </div>
    </div>
  `;
  
  // Remove existing modal if any
  const existingModal = document.getElementById('addUserModal');
  if (existingModal) {
    existingModal.remove();
  }
  
  // Add modal to body
  document.body.insertAdjacentHTML('beforeend', modalHTML);
}

// Close Add User Modal
function closeAddUserModal() {
  const modal = document.getElementById('addUserModal');
  if (modal) {
    modal.remove();
  }
}

// Submit Add User Form
function submitAddUser() {
  const fullName = document.getElementById('addUserName').value.trim();
  const email = document.getElementById('addUserEmail').value.trim();
  const phone = document.getElementById('addUserPhone').value.trim();
  const role = document.getElementById('addUserRole').value;
  const password = document.getElementById('addUserPassword').value;
  const confirmPassword = document.getElementById('addUserConfirmPassword').value;
  
  // Validate required fields
  if (!fullName || !email || !role || !password) {
    alert('Please fill in all required fields');
    return;
  }
  
  // Validate email format
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) {
    alert('Please enter a valid email address');
    return;
  }
  
  // Validate password match
  if (password !== confirmPassword) {
    alert('Passwords do not match');
    return;
  }
  
  // Validate password length
  if (password.length < 6) {
    alert('Password must be at least 6 characters');
    return;
  }
  
  // Submit to server
  fetch(ROOT + '/admindashboard/addUser', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      full_name: fullName,
      email: email,
      phone: phone,
      role: role,
      password: password
    })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      closeAddUserModal();
      alert('User added successfully!');
      loadAllUsers(); // Reload the list
    } else {
      alert('Error: ' + (data.message || 'Failed to add user'));
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('An error occurred while adding the user');
  });
}

// Show Edit User Modal
function showEditUserModal(userId) {
  // First fetch the user details
  fetch(ROOT + '/admindashboard/getUserDetails?user_id=' + encodeURIComponent(userId))
    .then(response => response.json())
    .then(data => {
      if (!data.success) {
        alert(data.message || 'Failed to load user details');
        return;
      }
      
      const user = data.user;
      
      const modalHTML = `
        <div class="modal-overlay active" id="editUserModal">
          <div class="modal-content user-form-modal">
            <div class="modal-header">
              <h3>Edit User</h3>
              <button class="modal-close" onclick="closeEditUserModal()">
                <span class="bx bx-x"></span>
              </button>
            </div>
            <div class="modal-body">
              <form id="editUserForm">
                <input type="hidden" id="editUserId" value="${user.id}" />
                <div class="input-box">
                  <input type="text" id="editUserName" value="${escapeHtml(user.full_name)}" placeholder="Full Name" required />
                  <i class="bx bx-user"></i>
                </div>
                <div class="input-box">
                  <input type="email" id="editUserEmail" value="${escapeHtml(user.email)}" placeholder="Email Address" required />
                  <i class="bx bx-envelope"></i>
                </div>
                <div class="input-box">
                  <input type="tel" id="editUserPhone" value="${escapeHtml(user.phone || '')}" placeholder="Phone Number" />
                  <i class="bx bx-phone"></i>
                </div>
                <div class="input-box select-box">
                  <select id="editUserRole" required>
                    <option value="artist" ${user.role === 'artist' ? 'selected' : ''}>Artist</option>
                    <option value="audience" ${user.role === 'audience' ? 'selected' : ''}>Audience</option>
                    <option value="service_provider" ${user.role === 'service_provider' ? 'selected' : ''}>Service Provider</option>
                  </select>
                  <i class="bx bx-badge"></i>
                </div>
              </form>
            </div>
            <div class="modal-footer">
              <button class="btn btn-secondary" onclick="closeEditUserModal()">Cancel</button>
              <button class="btn btn-primary" onclick="submitEditUser()">
                <span class="bx bx-save"></span>
                Save Changes
              </button>
            </div>
          </div>
        </div>
      `;
      
      // Remove existing modal if any
      const existingModal = document.getElementById('editUserModal');
      if (existingModal) {
        existingModal.remove();
      }
      
      // Add modal to body
      document.body.insertAdjacentHTML('beforeend', modalHTML);
    })
    .catch(error => {
      console.error('Error:', error);
      alert('An error occurred while loading user details');
    });
}

// Close Edit User Modal
function closeEditUserModal() {
  const modal = document.getElementById('editUserModal');
  if (modal) {
    modal.remove();
  }
}

// Submit Edit User Form
function submitEditUser() {
  const userId = document.getElementById('editUserId').value;
  const fullName = document.getElementById('editUserName').value.trim();
  const email = document.getElementById('editUserEmail').value.trim();
  const phone = document.getElementById('editUserPhone').value.trim();
  const role = document.getElementById('editUserRole').value;
  
  // Validate required fields
  if (!fullName || !email || !role) {
    alert('Please fill in all required fields');
    return;
  }
  
  // Validate email format
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) {
    alert('Please enter a valid email address');
    return;
  }
  
  // Submit to server
  fetch(ROOT + '/admindashboard/updateUser', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      user_id: userId,
      full_name: fullName,
      email: email,
      phone: phone,
      role: role
    })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      closeEditUserModal();
      alert('User updated successfully!');
      loadAllUsers(); // Reload the list
    } else {
      alert('Error: ' + (data.message || 'Failed to update user'));
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('An error occurred while updating the user');
  });
}

// Confirm and Remove User
function confirmRemoveUser(userId, userName) {
  if (!confirm(`Are you sure you want to remove "${userName}"?\n\nThis action cannot be undone.`)) {
    return;
  }
  
  // Submit to server
  fetch(ROOT + '/admindashboard/removeUser', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      user_id: userId
    })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      alert('User removed successfully!');
      loadAllUsers(); // Reload the list
    } else {
      alert('Error: ' + (data.message || 'Failed to remove user'));
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('An error occurred while removing the user');
  });
}

// Helper function to escape HTML
function escapeHtml(text) {
  if (!text) return '';
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

// Initialize on DOM load
document.addEventListener('DOMContentLoaded', function() {
  initUserManagementView();
});

// Make functions global
window.showAddUserModal = showAddUserModal;
window.closeAddUserModal = closeAddUserModal;
window.submitAddUser = submitAddUser;
window.showEditUserModal = showEditUserModal;
window.closeEditUserModal = closeEditUserModal;
window.submitEditUser = submitEditUser;
window.confirmRemoveUser = confirmRemoveUser;
window.loadAllUsers = loadAllUsers;
