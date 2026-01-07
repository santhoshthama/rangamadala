// Manage Roles - Role management functionality

const urlParams = new URLSearchParams(window.location.search);
const dramaId = urlParams.get('drama_id') || 1;

console.log('Manage Roles page initialized for Drama ID:', dramaId);

// Tab switching
function showTab(tabName) {
    // Hide all tabs
    const tabs = document.querySelectorAll('.tab-content');
    tabs.forEach(tab => tab.style.display = 'none');
    
    // Remove active class from buttons
    const buttons = document.querySelectorAll('.tab-button');
    buttons.forEach(btn => btn.classList.remove('active'));
    
    // Show selected tab
    const selectedTab = document.getElementById(tabName + 'Tab');
    if (selectedTab) {
        selectedTab.style.display = 'block';
    }
    
    // Add active class to clicked button
    event.target.classList.add('active');
    
    console.log('Switched to tab:', tabName);
}

// Load roles data
function loadRolesData() {
    console.log('Loading roles for drama_id:', dramaId);
    // TODO: Fetch roles data from backend
}

// Open create role modal
function openCreateRoleModal() {
    const modal = document.getElementById('createRoleModal');
    if (modal) {
        modal.style.display = 'block';
    }
    console.log('Create role modal opened');
}

// Close create role modal
function closeCreateRoleModal() {
    const modal = document.getElementById('createRoleModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

// Submit create role form
function submitCreateRole() {
    console.log('Creating new role...');
    const roleName = document.getElementById('roleNameInput').value;
    const salary = document.getElementById('salaryInput').value;
    
    if (!roleName || !salary) {
        alert('Please fill all required fields');
        return;
    }
    
    // TODO: Send to backend
    console.log('New role:', {role_name: roleName, salary: salary, drama_id: dramaId});
    alert('Role created successfully!');
    closeCreateRoleModal();
    loadRolesData();
}

// Accept application
function acceptApplication(applicationId) {
    console.log('Accepting application:', applicationId);
    // TODO: Send acceptance to backend
    alert('Application accepted!');
    loadRolesData();
}

// Reject application
function rejectApplication(applicationId) {
    console.log('Rejecting application:', applicationId);
    // TODO: Send rejection to backend
    alert('Application rejected!');
    loadRolesData();
}

// Cancel sent request
function cancelRequest(requestId) {
    console.log('Cancelling request:', requestId);
    // TODO: Send cancellation to backend
    alert('Request cancelled!');
    loadRolesData();
}

// Remove assigned role
function removeAssignedRole(roleId) {
    if (confirm('Are you sure you want to remove this artist from the role?')) {
        console.log('Removing role assignment:', roleId);
        // TODO: Send removal to backend
        alert('Artist removed from role!');
        loadRolesData();
    }
}

// Search artists modal
function openSearchArtistsModal() {
    const modal = document.getElementById('searchArtistsModal');
    if (modal) {
        modal.style.display = 'block';
    }
}

function closeSearchArtistsModal() {
    const modal = document.getElementById('searchArtistsModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    loadRolesData();
    console.log('Manage Roles page loaded');
});

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('createRoleModal');
    if (event.target === modal) {
        closeCreateRoleModal();
    }
};
