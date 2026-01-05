// Production Manager Dashboard - Navigation and initialization

const urlParams = new URLSearchParams(window.location.search);
const dramaId = urlParams.get('drama_id') || 1;

console.log('Production Manager Dashboard initialized for Drama ID:', dramaId);

// Navigate to different sections
function navigateTo(page) {
    window.location.href = page;
}

// View PM service schedule (NOT director's schedule)
function viewSchedule() {
    // Navigate to PM's service schedule management
    navigateTo(`manage_schedule.php?drama_id=${dramaId}`);
}

// Load dashboard data from backend
document.addEventListener('DOMContentLoaded', function() {
    console.log('Dashboard DOM loaded');
    loadDashboardData();
});

// Load dashboard statistics and recent items
function loadDashboardData() {
    console.log('Loading dashboard data for drama_id:', dramaId);
    // TODO: Fetch dashboard data from backend API
    // - Budget statistics
    // - Recent service requests
    // - Upcoming theater bookings
    // - Outstanding tasks
}

// Redirect to services management
function manageServices() {
    navigateTo(`manage_services.php?drama_id=${dramaId}`);
}

// Redirect to budget management
function manageBudget() {
    navigateTo(`manage_budget.php?drama_id=${dramaId}`);
}

// Redirect to theater bookings
function bookTheater() {
    navigateTo(`book_theater.php?drama_id=${dramaId}`);
}

// Redirect to service schedule
function viewServiceSchedule() {
    navigateTo(`manage_schedule.php?drama_id=${dramaId}`);
