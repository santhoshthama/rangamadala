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
  
  // Also load immediately if the registrations view is already visible
  const registrationsView = document.getElementById('registrations');
  if (registrationsView && registrationsView.classList.contains('active')) {
    loadRegistrations();
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
          <button class="btn btn-secondary" onclick="showUserDetails(${user.id})">
            <span class="material-symbols-rounded">visibility</span>
            View
          </button>
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

function showUserDetails(userId) {
  fetch(ROOT + '/admindashboard/getRegistrationDetails?user_id=' + encodeURIComponent(userId))
    .then(response => response.json())
    .then(data => {
      if (!data.success) {
        toastError(data.message || 'Failed to load user details');
        return;
      }

      const user = data.user;
      const sp = data.service_provider || null;

      const nicImage = user.nic_photo ? `<img src="${ROOT}/${user.nic_photo}" alt="NIC" class="nic-image" />` : '<em>No NIC image uploaded</em>';

      let extraDetails = '';
      if (sp) {
        const frontImg = sp.nic_photo_front ? `<img src="${ROOT}/${sp.nic_photo_front}" alt="NIC Front" class="nic-image" />` : '<em>No front image</em>';
        const backImg = sp.nic_photo_back ? `<img src="${ROOT}/${sp.nic_photo_back}" alt="NIC Back" class="nic-image" />` : '<em>No back image</em>';

        extraDetails = `
          <div class="details-section-header">
            <span class="material-symbols-rounded">work</span>
            <span>Service Provider Details</span>
          </div>
          <div class="input-box readonly">
            <input type="text" value="${sp.professional_title || 'N/A'}" readonly />
            <i class="material-symbols-rounded">badge</i>
            <label>Professional Title</label>
          </div>
          <div class="input-box readonly">
            <input type="text" value="${sp.location || 'N/A'}" readonly />
            <i class="material-symbols-rounded">location_on</i>
            <label>Location</label>
          </div>
          <div class="input-box readonly">
            <input type="text" value="${sp.nic_number || 'N/A'}" readonly />
            <i class="material-symbols-rounded">credit_card</i>
            <label>NIC Number</label>
          </div>
          <div class="input-box readonly">
            <input type="text" value="${sp.years_experience || 'N/A'}" readonly />
            <i class="material-symbols-rounded">timeline</i>
            <label>Years of Experience</label>
          </div>
          <div class="input-box readonly">
            <input type="text" value="${sp.professional_summary || 'N/A'}" readonly />
            <i class="material-symbols-rounded">description</i>
            <label>Professional Summary</label>
          </div>
          <div class="input-box readonly">
            <input type="text" value="${sp.availability === 1 ? 'Available' : 'Not available'}" readonly />
            <i class="material-symbols-rounded">event_available</i>
            <label>Availability</label>
          </div>
          <div class="details-section-header">
            <span class="material-symbols-rounded">badge</span>
            <span>NIC Verification Images</span>
          </div>
          <div class="nic-images-row">
            <div class="nic-image-box">
              <h5>NIC Front</h5>
              ${frontImg}
            </div>
            <div class="nic-image-box">
              <h5>NIC Back</h5>
              ${backImg}
            </div>
          </div>
        `;
      } else {
        extraDetails = `
          <div class="details-section-header">
            <span class="material-symbols-rounded">badge</span>
            <span>NIC / Verification</span>
          </div>
          <div class="nic-images-row">
            <div class="nic-image-box">
              ${nicImage}
            </div>
          </div>
        `;
      }

      const roleDisplay = user.role.replace('_', ' ');
      const statusDisplay = user.verification_status || 'pending';

      const modalHTML = `
        <div class="modal-overlay active" id="userDetailsModal">
          <div class="modal-content user-form-modal">
            <div class="modal-header">
              <h3>Registration Details</h3>
              <button class="modal-close" onclick="closeUserDetailsModal()">
                <span class="material-symbols-rounded">close</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="details-section-header">
                <span class="material-symbols-rounded">person</span>
                <span>Personal Information</span>
              </div>
              <div class="input-box readonly">
                <input type="text" value="${user.full_name}" readonly />
                <i class="material-symbols-rounded">person</i>
                <label>Full Name</label>
              </div>
              <div class="input-box readonly">
                <input type="email" value="${user.email}" readonly />
                <i class="material-symbols-rounded">mail</i>
                <label>Email Address</label>
              </div>
              <div class="input-box readonly">
                <input type="text" value="${user.phone || 'N/A'}" readonly />
                <i class="material-symbols-rounded">phone</i>
                <label>Phone Number</label>
              </div>
              <div class="input-box readonly">
                <input type="text" value="${roleDisplay}" readonly />
                <i class="material-symbols-rounded">badge</i>
                <label>Role</label>
              </div>
              <div class="input-box readonly">
                <input type="text" value="${statusDisplay}" readonly />
                <i class="material-symbols-rounded">verified</i>
                <label>Verification Status</label>
              </div>
              ${extraDetails}
            </div>
            <div class="modal-footer">
              <button class="btn btn-secondary" onclick="closeUserDetailsModal()">
                <span class="material-symbols-rounded">close</span>
                Close
              </button>
              <button class="btn btn-approve" onclick="closeUserDetailsModal(); approveUser(${user.id}, '${user.full_name}')">
                <span class="material-symbols-rounded">check_circle</span>
                Approve
              </button>
              <button class="btn btn-reject" onclick="closeUserDetailsModal(); showRejectModal(${user.id}, '${user.full_name}')">
                <span class="material-symbols-rounded">cancel</span>
                Reject
              </button>
            </div>
          </div>
        </div>
      `;

      const existingModal = document.getElementById('userDetailsModal');
      if (existingModal) {
        existingModal.remove();
      }

      document.body.insertAdjacentHTML('beforeend', modalHTML);
    })
    .catch(error => {
      console.error('Error loading user details:', error);
      toastError('An error occurred while loading user details');
    });
}

function closeUserDetailsModal() {
  const modal = document.getElementById('userDetailsModal');
  if (modal) {
    modal.remove();
  }
}

async function approveUser(userId, userName) {
  const confirmed = await showConfirm(
    `Are you sure you want to approve ${userName}?`,
    { title: 'Approve User', confirmText: 'Yes, Approve', type: 'success' }
  );
  if (!confirmed) return;
  
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
      toastSuccess('User approved successfully!');
      loadRegistrations(); // Reload the list
    } else {
      toastError(data.message || 'Failed to approve user');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    toastError('An error occurred while approving the user');
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
      toastSuccess('User has been rejected');
      loadRegistrations(); // Reload the list
    } else {
      toastError(data.message || 'Failed to reject user');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    toastError('An error occurred while rejecting the user');
  });
}

// Make functions global
window.approveUser = approveUser;
window.showRejectModal = showRejectModal;
window.closeRejectModal = closeRejectModal;
window.rejectUser = rejectUser;
window.showUserDetails = showUserDetails;
window.closeUserDetailsModal = closeUserDetailsModal;
