// Add this to the end of admindashboard.js

// ===================================
// REGISTRATIONS VIEW FUNCTIONALITY
// ===================================
function initRegistrationsView() {
  // Filter buttons
  const filterButtons = document.querySelectorAll('.filter-btn');
  filterButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      filterButtons.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      const filter = btn.getAttribute('data-filter');
      filterRegistrations(filter);
    });
  });
  
  // Load registrations when view is accessed
  const registrationsNav = document.querySelector('[data-view="registrations"]');
  if (registrationsNav) {
    registrationsNav.addEventListener('click', loadRegistrations);
  }
}

function loadRegistrations() {
  const loading = document.getElementById('registrationsLoading');
  const empty = document.getElementById('registrationsEmpty');
  const table = document.getElementById('registrationsTable');
  const tbody = document.getElementById('registrationsTableBody');
  
  // Show loading
  loading.style.display = 'block';
  empty.style.display = 'none';
  table.style.display = 'none';
  
  // Fetch pending registrations
  fetch(ROOT + '/admindashboard/getPendingRegistrations')
    .then(response => response.json())
    .then(data => {
      pendingRegistrations = data;
      loading.style.display = 'none';
      
      if (data.length === 0) {
        empty.style.display = 'block';
      } else {
        table.style.display = 'table';
        renderRegistrations(data);
      }
    })
    .catch(error => {
      console.error('Error loading registrations:', error);
      loading.style.display = 'none';
      empty.style.display = 'block';
    });
}

function renderRegistrations(registrations) {
  const tbody = document.getElementById('registrationsTableBody');
  tbody.innerHTML = '';
  
  registrations.forEach(user => {
    const tr = document.createElement('tr');
    tr.setAttribute('data-role', user.role);
    
    const initials = user.full_name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
    const formattedDate = new Date(user.created_at).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    });
    
    tr.innerHTML = `
      <td>
        <div class="user-cell">
          <div class="user-avatar">${initials}</div>
          <div class="user-info">
            <h4>${user.full_name}</h4>
            <p>${user.email}</p>
          </div>
        </div>
      </td>
      <td>
        <span class="role-badge ${user.role}">${user.role.replace('_', ' ')}</span>
      </td>
      <td>${user.phone || 'N/A'}</td>
      <td>${formattedDate}</td>
      <td>
        <div class="action-buttons">
          <button class="btn btn-approve" onclick="approveUser(${user.id}, '${user.full_name}')">
            <span class="material-symbols-rounded">check_circle</span>
            Approve
          </button>
          <button class="btn btn-reject" onclick="showRejectModal(${user.id}, '${user.full_name}')">
            <span class="material-symbols-rounded">cancel</span>
            Reject
          </button>
        </div>
      </td>
    `;
    
    tbody.appendChild(tr);
  });
}

function filterRegistrations(filter) {
  const rows = document.querySelectorAll('#registrationsTableBody tr');
  rows.forEach(row => {
    if (filter === 'all') {
      row.style.display = '';
    } else {
      const role = row.getAttribute('data-role');
      row.style.display = role === filter ? '' : 'none';
    }
  });
}

function approveUser(userId, userName) {
  if (!confirm(`Are you sure you want to approve ${userName}?`)) {
    return;
  }
  
  fetch(ROOT + '/admindashboard/approveUser', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({ user_id: userId })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      alert('User approved successfully!');
      loadRegistrations(); // Reload the list
    } else {
      alert('Error: ' + (data.message || 'Failed to approve user'));
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('An error occurred while approving the user');
  });
}

function showRejectModal(userId, userName) {
  // Create modal HTML
  const modalHTML = `
    <div class="modal-overlay active" id="rejectModal">
      <div class="modal-content">
        <div class="modal-header">
          <h3>Reject Registration</h3>
          <button class="modal-close" onclick="closeRejectModal()">
            <span class="material-symbols-rounded">close</span>
          </button>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to reject <strong>${userName}</strong>?</p>
          <div class="form-group">
            <label for="rejectionReason">Reason for Rejection (optional)</label>
            <textarea id="rejectionReason" placeholder="Enter reason for rejection..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" onclick="closeRejectModal()">Cancel</button>
          <button class="btn btn-reject" onclick="rejectUser(${userId})">Confirm Reject</button>
        </div>
      </div>
    </div>
  `;
  
  // Remove existing modal if any
  const existingModal = document.getElementById('rejectModal');
  if (existingModal) {
    existingModal.remove();
  }
  
  // Add modal to body
  document.body.insertAdjacentHTML('beforeend', modalHTML);
}

function closeRejectModal() {
  const modal = document.getElementById('rejectModal');
  if (modal) {
    modal.remove();
  }
}

function rejectUser(userId) {
  const reason = document.getElementById('rejectionReason').value;
  
  fetch(ROOT + '/admindashboard/rejectUser', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({ 
      user_id: userId,
      reason: reason 
    })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      closeRejectModal();
      alert('User rejected successfully!');
      loadRegistrations(); // Reload the list
    } else {
      alert('Error: ' + (data.message || 'Failed to reject user'));
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('An error occurred while rejecting the user');
  });
}

// Make functions global
window.approveUser = approveUser;
window.showRejectModal = showRejectModal;
window.closeRejectModal = closeRejectModal;
window.rejectUser = rejectUser;
