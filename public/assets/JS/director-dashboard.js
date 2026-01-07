// Director Dashboard - Functionality

// Get drama ID from URL
const urlParams = new URLSearchParams(window.location.search);
const dramaId = urlParams.get('drama_id') || 1;

console.log('Director Dashboard initialized for Drama ID:', dramaId);

// Load drama data
function loadDramaData() {
    // TODO: Fetch from backend with drama_id
    console.log('Loading drama data for drama_id:', dramaId);
}

// View application details
function viewApplication(applicationId) {
    console.log('View application:', applicationId);
    // TODO: Open modal with application details
}

// Accept application
function acceptApplication(applicationId) {
    console.log('Accept application:', applicationId);
    // TODO: Send accept request to backend
    // TODO: Refresh pending applications list
}

// Reject application
function rejectApplication(applicationId) {
    console.log('Reject application:', applicationId);
    // TODO: Send reject request to backend
    // TODO: Refresh pending applications list
}

// Open manager details
function viewManagerDetails() {
    console.log('View manager details');
    // TODO: Open modal with manager profile
}

// Refresh dashboard
function refreshDashboard() {
    console.log('Refreshing dashboard...');
    loadDramaData();
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    loadDramaData();
    console.log('Dashboard loaded successfully');
});
