// Add this to the end of admindashboard.js

// ===================================
// REGISTRATIONS VIEW FUNCTIONALITY
// ===================================
let pendingDramaRequests = [];

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

  // Load pending drama approvals when dedicated view is accessed
  const dramaApprovalsNav = document.querySelector('[data-view="drama-approvals"]');
  if (dramaApprovalsNav) {
    dramaApprovalsNav.addEventListener('click', loadDramaRequests);
  }
  
  // Also load immediately if the registrations view is already visible
  const registrationsView = document.getElementById('registrations');
  if (registrationsView && registrationsView.classList.contains('active')) {
    loadRegistrations();
  }

  const dramaApprovalsView = document.getElementById('drama-approvals');
  if (dramaApprovalsView && dramaApprovalsView.classList.contains('active')) {
    loadDramaRequests();
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

function loadDramaRequests() {
  const loading = document.getElementById('dramaRequestsLoading');
  const empty = document.getElementById('dramaRequestsEmpty');
  const table = document.getElementById('dramaRequestsTable');

  if (!loading || !empty || !table) {
    return;
  }

  loading.style.display = 'block';
  empty.style.display = 'none';
  table.style.display = 'none';

  fetch(ROOT + '/admindashboard/getPendingDramaRequests')
    .then(response => response.json())
    .then(data => {
      pendingDramaRequests = Array.isArray(data) ? data : [];
      loading.style.display = 'none';

      if (!Array.isArray(data) || data.length === 0) {
        empty.style.display = 'block';
      } else {
        table.style.display = 'table';
        renderDramaRequests(data);
      }
    })
    .catch(error => {
      console.error('Error loading drama requests:', error);
      loading.style.display = 'none';
      empty.style.display = 'block';
    });
}

function renderDramaRequests(requests) {
  const tbody = document.getElementById('dramaRequestsTableBody');
  if (!tbody) {
    return;
  }

  tbody.innerHTML = '';

  requests.forEach(req => {
    const tr = document.createElement('tr');
    const formattedDate = new Date(req.created_at).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    });

    const certificateLink = req.certificate_image
      ? `<a href="${ROOT}/uploads/certificates/${encodeURIComponent(req.certificate_image)}" target="_blank" rel="noopener">View file</a>`
      : 'N/A';

    tr.innerHTML = `
      <td>
        <div class="user-info">
          <h4>${escapeHtml(req.drama_name)}</h4>
          <p>${escapeHtml(req.owner_name || 'N/A')}</p>
        </div>
      </td>
      <td>
        <div class="user-info">
          <h4>${escapeHtml(req.artist_name || 'N/A')}</h4>
          <p>${escapeHtml(req.artist_email || 'N/A')}</p>
        </div>
      </td>
      <td>
        <div>${escapeHtml(req.certificate_number || 'N/A')}</div>
        <small>${certificateLink}</small>
      </td>
      <td>${formattedDate}</td>
      <td>
        <div class="action-buttons">
          <button class="btn btn-secondary" onclick="showDramaRequestDetails(${req.id})">
            <span class="bx bx-show"></span>
            View
          </button>
          <button class="btn btn-approve" onclick="approveDramaRequest(${req.id}, '${escapeJsString(req.drama_name)}')">
            <span class="bx bx-check-circle"></span>
            Approve
          </button>
          <button class="btn btn-reject" onclick="showRejectDramaModal(${req.id}, '${escapeJsString(req.drama_name)}')">
            <span class="bx bx-x"></span>
            Reject
          </button>
        </div>
      </td>
    `;

    tbody.appendChild(tr);
  });
}

function showDramaRequestDetails(requestId) {
  const request = pendingDramaRequests.find(req => Number(req.id) === Number(requestId));

  if (!request) {
    toastError('Unable to load drama request details.');
    return;
  }

  const requestDate = request.created_at
    ? new Date(request.created_at).toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    : 'N/A';

  const hasCertificateFile = Boolean(request.certificate_image);
  const encodedCertificateFile = hasCertificateFile
    ? encodeURIComponent(request.certificate_image)
    : '';
  const certificateUrl = hasCertificateFile
    ? `${ROOT}/uploads/certificates/${encodedCertificateFile}`
    : '';
  const fileExtension = hasCertificateFile
    ? String(request.certificate_image).split('.').pop().toLowerCase()
    : '';
  const isImageFile = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(fileExtension);

  const certificatePreview = hasCertificateFile
    ? isImageFile
      ? `<img src="${certificateUrl}" alt="Certificate Image" class="nic-image" />`
      : `<div class="certificate-file-box">
           <span class="bx bx-description"></span>
           <p>Certificate document uploaded</p>
         </div>`
    : '<em>No certificate file uploaded</em>';

  const modalHTML = `
    <div class="modal-overlay active" id="dramaRequestDetailModal">
      <div class="modal-content user-form-modal drama-request-modal">
        <div class="modal-header">
          <h3>Drama Request Details</h3>
          <button class="modal-close" onclick="closeDramaRequestDetailsModal()">
            <span class="bx bx-x"></span>
          </button>
        </div>
        <div class="modal-body">
          <div class="details-section-header">
            <span class="bx bx-theater-comedy"></span>
            <span>Drama Information</span>
          </div>

          <div class="input-box readonly">
            <input type="text" value="${escapeHtml(request.drama_name || 'N/A')}" readonly />
            <i class="bx bx-movie"></i>
            <label>Drama Name</label>
          </div>

          <div class="input-box readonly">
            <input type="text" value="${escapeHtml(request.owner_name || 'N/A')}" readonly />
            <i class="bx bx-user"></i>
            <label>Producer Name</label>
          </div>

          <div class="input-box readonly">
            <input type="text" value="${escapeHtml(request.certificate_number || 'N/A')}" readonly />
            <i class="bx bx-badge"></i>
            <label>Certificate Number</label>
          </div>

          <div class="form-group drama-description-group">
            <label>Drama Description</label>
            <textarea readonly>${escapeHtml(request.description || 'No description provided')}</textarea>
          </div>

          <div class="details-section-header">
            <span class="bx bx-user"></span>
            <span>Artist Information</span>
          </div>

          <div class="input-box readonly">
            <input type="text" value="${escapeHtml(request.artist_name || 'N/A')}" readonly />
            <i class="bx bx-user"></i>
            <label>Artist Name</label>
          </div>

          <div class="input-box readonly">
            <input type="text" value="${escapeHtml(request.artist_email || 'N/A')}" readonly />
            <i class="bx bx-envelope"></i>
            <label>Artist Email</label>
          </div>

          <div class="input-box readonly">
            <input type="text" value="${escapeHtml(request.artist_phone || 'N/A')}" readonly />
            <i class="bx bx-phone"></i>
            <label>Artist Phone</label>
          </div>

          <div class="input-box readonly">
            <input type="text" value="${escapeHtml(requestDate)}" readonly />
            <i class="bx bx-schedule">schedule</i>
            <label>Requested At</label>
          </div>

          <div class="details-section-header">
            <span class="bx bx-image"></span>
            <span>Certificate File</span>
          </div>

          <div class="nic-images-row">
            <div class="nic-image-box">
              ${certificatePreview}
              ${hasCertificateFile ? `<a href="${certificateUrl}" target="_blank" rel="noopener" class="certificate-file-link">Open Uploaded File</a>` : ''}
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" onclick="closeDramaRequestDetailsModal()">
            Close
          </button>
          <button class="btn btn-approve" onclick="closeDramaRequestDetailsModal(); approveDramaRequest(${request.id}, '${escapeJsString(request.drama_name || 'this drama')}')">
            <span class="bx bx-check-circle"></span>
            Approve
          </button>
          <button class="btn btn-reject" onclick="closeDramaRequestDetailsModal(); showRejectDramaModal(${request.id}, '${escapeJsString(request.drama_name || 'this drama')}')">
            <span class="bx bx-x"></span>
            Reject
          </button>
        </div>
      </div>
    </div>
  `;

  const existingModal = document.getElementById('dramaRequestDetailModal');
  if (existingModal) {
    existingModal.remove();
  }

  document.body.insertAdjacentHTML('beforeend', modalHTML);
}

function closeDramaRequestDetailsModal() {
  const modal = document.getElementById('dramaRequestDetailModal');
  if (modal) {
    modal.remove();
  }
}

async function approveDramaRequest(requestId, dramaName) {
  const confirmed = await showConfirm(
    `Approve drama request for ${dramaName}? This creates the drama draft only; artist must publish it to audience later.`,
    { title: 'Approve Drama Request', confirmText: 'Approve', type: 'success' }
  );
  if (!confirmed) return;

  fetch(ROOT + '/admindashboard/approveDramaRequest', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({ request_id: requestId })
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        toastSuccess(data.message || 'Drama request approved successfully.');
        if (typeof loadOverviewStats === 'function') {
          loadOverviewStats();
        }
        loadDramaRequests();
      } else {
        toastError(data.message || 'Failed to approve drama request');
      }
    })
    .catch(error => {
      console.error('Error approving drama request:', error);
      toastError('An error occurred while approving drama request');
    });
}

function showRejectDramaModal(requestId, dramaName) {
  const modalHTML = `
    <div class="modal-overlay active" id="rejectDramaModal">
      <div class="modal-content">
        <div class="modal-header">
          <h3>Reject Drama Request</h3>
          <button class="modal-close" onclick="closeRejectDramaModal()">
            <span class="bx bx-x"></span>
          </button>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to reject <strong>${escapeHtml(dramaName)}</strong>?</p>
          <div class="form-group">
            <label for="dramaRejectionReason">Reason for Rejection (optional)</label>
            <textarea id="dramaRejectionReason" placeholder="Enter reason for rejection..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" onclick="closeRejectDramaModal()">Cancel</button>
          <button class="btn btn-reject" onclick="rejectDramaRequest(${requestId})">Confirm Reject</button>
        </div>
      </div>
    </div>
  `;

  const existingModal = document.getElementById('rejectDramaModal');
  if (existingModal) {
    existingModal.remove();
  }

  document.body.insertAdjacentHTML('beforeend', modalHTML);
}

function closeRejectDramaModal() {
  const modal = document.getElementById('rejectDramaModal');
  if (modal) {
    modal.remove();
  }
}

function rejectDramaRequest(requestId) {
  const reason = document.getElementById('dramaRejectionReason')?.value || '';

  fetch(ROOT + '/admindashboard/rejectDramaRequest', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({ request_id: requestId, reason })
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        closeRejectDramaModal();
        toastSuccess(data.message || 'Drama request has been rejected');
        if (typeof loadOverviewStats === 'function') {
          loadOverviewStats();
        }
        loadDramaRequests();
      } else {
        toastError(data.message || 'Failed to reject drama request');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      toastError('An error occurred while rejecting drama request');
    });
}

function escapeHtml(value) {
  const div = document.createElement('div');
  div.textContent = value ?? '';
  return div.innerHTML;
}

function escapeJsString(value) {
  return String(value ?? '')
    .replace(/\\/g, '\\\\')
    .replace(/'/g, "\\'")
    .replace(/\n/g, ' ')
    .replace(/\r/g, ' ');
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
            <span class="bx bx-show"></span>
            View
          </button>
          <button class="btn btn-approve" onclick="approveUser(${user.id}, '${user.full_name}')">
            <span class="bx bx-check-circle"></span>
            Approve
          </button>
          <button class="btn btn-reject" onclick="showRejectModal(${user.id}, '${user.full_name}')">
            <span class="bx bx-x"></span>
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
            <span class="bx bx-work">work</span>
            <span>Service Provider Details</span>
          </div>
          <div class="input-box readonly">
            <input type="text" value="${sp.professional_title || 'N/A'}" readonly />
            <i class="bx bx-badge"></i>
            <label>Professional Title</label>
          </div>
          <div class="input-box readonly">
            <input type="text" value="${sp.location || 'N/A'}" readonly />
            <i class="bx bx-location"></i>
            <label>Location</label>
          </div>
          <div class="input-box readonly">
            <input type="text" value="${sp.nic_number || 'N/A'}" readonly />
            <i class="bx bx-credit-card"></i>
            <label>NIC Number</label>
          </div>
          <div class="input-box readonly">
            <input type="text" value="${sp.years_experience || 'N/A'}" readonly />
            <i class="bx bx-timeline"></i>
            <label>Years of Experience</label>
          </div>
          <div class="input-box readonly">
            <input type="text" value="${sp.professional_summary || 'N/A'}" readonly />
            <i class="bx bx-description"></i>
            <label>Professional Summary</label>
          </div>
          <div class="input-box readonly">
            <input type="text" value="${sp.availability === 1 ? 'Available' : 'Not available'}" readonly />
            <i class="bx bx-event-available"></i>
            <label>Availability</label>
          </div>
          <div class="details-section-header">
            <span class="bx bx-badge"></span>
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
            <span class="bx bx-badge"></span>
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
                <span class="bx bx-x"></span>
              </button>
            </div>
            <div class="modal-body">
              <div class="details-section-header">
                <span class="bx bx-badge"></span>
                <span>Personal Information</span>
              </div>
              <div class="input-box readonly">
                <input type="text" value="${user.full_name}" readonly />
                <i class="bx bx-user"></i>
                <label>Full Name</label>
              </div>
              <div class="input-box readonly">
                <input type="email" value="${user.email}" readonly />
                <i class="bx bx-envelope"></i>
                <label>Email Address</label>
              </div>
              <div class="input-box readonly">
                <input type="text" value="${user.phone || 'N/A'}" readonly />
                <i class="bx bx-phone"></i>
                <label>Phone Number</label>
              </div>
              <div class="input-box readonly">
                <input type="text" value="${roleDisplay}" readonly />
                <i class="bx bx-badge"></i>
                <label>Role</label>
              </div>
              <div class="input-box readonly">
                <input type="text" value="${statusDisplay}" readonly />
                <i class="bx bx-verified"></i>
                <label>Verification Status</label>
              </div>
              ${extraDetails}
            </div>
            <div class="modal-footer">
              <button class="btn btn-secondary" onclick="closeUserDetailsModal()">
                <span class="bx bx-x"></span>
                Close
              </button>
              <button class="btn btn-approve" onclick="closeUserDetailsModal(); approveUser(${user.id}, '${user.full_name}')">
                <span class="bx bx-check-circle"></span>
                Approve
              </button>
              <button class="btn btn-reject" onclick="closeUserDetailsModal(); showRejectModal(${user.id}, '${user.full_name}')">
                <span class="bx bx-cancel"></span>
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
      if (typeof loadOverviewStats === 'function') {
        loadOverviewStats();
      }
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
            <span class="bx bx-x"></span>
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
      if (typeof loadOverviewStats === 'function') {
        loadOverviewStats();
      }
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
window.approveDramaRequest = approveDramaRequest;
window.showRejectDramaModal = showRejectDramaModal;
window.closeRejectDramaModal = closeRejectDramaModal;
window.rejectDramaRequest = rejectDramaRequest;
window.showDramaRequestDetails = showDramaRequestDetails;
window.closeDramaRequestDetailsModal = closeDramaRequestDetailsModal;
