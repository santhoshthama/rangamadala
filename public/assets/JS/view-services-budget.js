// View Services & Budget - View-only service and budget functionality

const urlParams = new URLSearchParams(window.location.search);
const dramaId = urlParams.get('drama_id') || 1;

console.log('View Services & Budget page initialized for Drama ID:', dramaId);

// Tab switching
function showServiceTab(tabName) {
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
    
    console.log('Switched to service tab:', tabName);
    loadTabData(tabName);
}

// Load services and budget data
function loadTabData(tabName) {
    console.log('Loading', tabName, 'data for drama_id:', dramaId);
    // TODO: Fetch data from backend based on tab
    
    switch(tabName) {
        case 'services':
            loadServices();
            break;
        case 'budget':
            loadBudget();
            break;
        case 'theaters':
            loadTheaters();
            break;
    }
}

// Load services
function loadServices() {
    console.log('Loading services for drama_id:', dramaId);
    // TODO: Fetch services data from backend
}

// Load budget
function loadBudget() {
    console.log('Loading budget for drama_id:', dramaId);
    // TODO: Fetch budget data from backend
}

// Load theater bookings
function loadTheaters() {
    console.log('Loading theater bookings for drama_id:', dramaId);
    // TODO: Fetch theater booking data from backend
}

// View service details
function viewServiceDetails(serviceId) {
    console.log('View service details:', serviceId);
    // TODO: Open modal with service information
    alert('Service Details:\nService ID: ' + serviceId + '\nNote: Full details managed by Production Manager');
}

// View budget item details
function viewBudgetItemDetails(itemId) {
    console.log('View budget item details:', itemId);
    // TODO: Open modal with budget item information
    alert('Budget Item Details:\nItem ID: ' + itemId + '\nNote: Budget management handled by Production Manager');
}

// View theater details
function viewTheaterDetails(bookingId) {
    console.log('View theater details:', bookingId);
    // TODO: Open modal with theater booking information
    alert('Theater Booking Details:\nBooking ID: ' + bookingId + '\nNote: Theater bookings managed by Production Manager');
}

// Export budget report
function exportBudgetReport() {
    console.log('Exporting budget report for drama_id:', dramaId);
    // TODO: Generate and download budget report
    alert('Generating budget report...');
}

// Print service summary
function printServiceSummary() {
    console.log('Printing service summary for drama_id:', dramaId);
    window.print();
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    loadTabData('services');
    console.log('View Services & Budget page loaded');
});
