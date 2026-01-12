// Schedule Management - Schedule functionality

const urlParams = new URLSearchParams(window.location.search);
const dramaId = urlParams.get('drama_id') || 1;

console.log('Schedule Management page initialized for Drama ID:', dramaId);

// Tab switching
function showScheduleTab(tabName) {
    // Hide all tabs
    const tabs = document.querySelectorAll('.tab-content');
    tabs.forEach(tab => tab.classList.remove('active'));
    
    // Remove active class from buttons
    const buttons = document.querySelectorAll('.tab-button');
    buttons.forEach(btn => btn.classList.remove('active'));
    
    // Show selected tab
    const selectedTab = document.getElementById(tabName + 'Tab');
    if (selectedTab) {
        selectedTab.classList.add('active');
    }
    
    // Add active class to clicked button
    event.target.classList.add('active');
    
    console.log('Switched to schedule tab:', tabName);
}

// Load schedule data
function loadScheduleData() {
    console.log('Loading schedule for drama_id:', dramaId);
    // TODO: Fetch schedule data from backend
}

// Open schedule modal
function openScheduleModal() {
    const modal = document.getElementById('scheduleModal');
    if (modal) {
        modal.style.display = 'block';
    }
    console.log('Schedule modal opened');
}

// Close schedule modal
function closeScheduleModal() {
    const modal = document.getElementById('scheduleModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

// Submit schedule form
function submitSchedule() {
    const eventName = document.getElementById('eventNameInput').value;
    const eventDate = document.getElementById('eventDateInput').value;
    const eventTime = document.getElementById('eventTimeInput').value;
    const venue = document.getElementById('venueInput').value;
    const eventType = document.getElementById('eventTypeInput').value;
    
    if (!eventName || !eventDate || !eventTime || !venue) {
        alert('Please fill all required fields');
        return;
    }
    
    console.log('Creating schedule event...', {
        name: eventName,
        date: eventDate,
        time: eventTime,
        venue: venue,
        type: eventType,
        drama_id: dramaId
    });
    
    // TODO: Send to backend
    alert('Schedule event created successfully!');
    closeScheduleModal();
    loadScheduleData();
}

// Edit schedule event
function editScheduleEvent(eventId) {
    console.log('Edit schedule event:', eventId);
    // TODO: Load event data and open modal
    openScheduleModal();
}

// Delete schedule event
function deleteScheduleEvent(eventId) {
    if (confirm('Are you sure you want to delete this schedule event?')) {
        console.log('Deleting schedule event:', eventId);
        // TODO: Send deletion to backend
        alert('Schedule event deleted!');
        loadScheduleData();
    }
}

// Confirm attendance
function confirmAttendance(eventId) {
    console.log('Confirming attendance for event:', eventId);
    // TODO: Update attendance status
    alert('Attendance confirmed!');
}

// View schedule details
function viewScheduleDetails(eventId) {
    console.log('View schedule details for event:', eventId);
    // TODO: Open modal with event details
    alert('Event Details - ID: ' + eventId);
}

// Edit schedule
function editSchedule(eventId) {
    console.log('Edit schedule event:', eventId);
    openScheduleModal();
}

// Cancel schedule
function cancelSchedule(eventId) {
    if (confirm('Are you sure you want to cancel this schedule event?')) {
        console.log('Cancelling schedule event:', eventId);
        // TODO: Send cancellation to backend
        alert('Schedule event cancelled!');
        loadScheduleData();
    }
}

// View request details
function viewRequest(requestId) {
    console.log('View request details:', requestId);
    // TODO: Open modal with request details
    alert('Request Details - ID: ' + requestId);
}

// Cancel request
function cancelRequest(requestId) {
    if (confirm('Are you sure you want to cancel this request?')) {
        console.log('Cancelling request:', requestId);
        // TODO: Send cancellation to backend
        alert('Request cancelled!');
        loadScheduleData();
    }
}

// Filter schedule
function filterSchedule() {
    const typeFilter = document.getElementById('eventTypeFilter').value;
    const monthFilter = document.getElementById('monthFilter').value;
    
    console.log('Filtering schedule with type:', typeFilter, 'month:', monthFilter);
    // TODO: Apply filters and refresh display
}

// Export to calendar
function exportToCalendar() {
    console.log('Exporting schedule to Google Calendar for drama_id:', dramaId);
    // TODO: Generate calendar export link
    alert('Exporting schedule to Google Calendar...');
}

// Print schedule
function printSchedule() {
    console.log('Printing schedule for drama_id:', dramaId);
    window.print();
}

// Calendar variables
let currentMonth = new Date().getMonth();
let currentYear = new Date().getFullYear();

// Sample events data (will be replaced with backend data)
const sampleEvents = [
    { date: '2024-12-23', title: 'Interview - Dancer Leader', type: 'interview' },
    { date: '2024-12-25', title: 'Rehearsal - Act 1 Scene 3', type: 'rehearsal' },
    { date: '2024-12-28', title: 'Production Meeting', type: 'meeting' },
    { date: '2024-12-30', title: 'Performance - Lionel Wendt', type: 'rehearsal' }
];

// Generate calendar
function generateCalendar(month, year) {
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const today = new Date();
    
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                        'July', 'August', 'September', 'October', 'November', 'December'];
    
    // Update header
    document.getElementById('calendarMonthYear').textContent = monthNames[month] + ' ' + year;
    
    const calendarDays = document.getElementById('calendarDays');
    if (!calendarDays) return;
    
    calendarDays.innerHTML = '';
    
    // Empty cells before first day
    for (let i = 0; i < firstDay; i++) {
        const emptyCell = document.createElement('div');
        emptyCell.style.cssText = 'background: #f8f9fa; padding: 30px 10px; min-height: 80px;';
        calendarDays.appendChild(emptyCell);
    }
    
    // Days of the month
    for (let day = 1; day <= daysInMonth; day++) {
        const dayCell = document.createElement('div');
        const dateString = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        
        // Check if this is today
        const isToday = today.getDate() === day && 
                       today.getMonth() === month && 
                       today.getFullYear() === year;
        
        // Get events for this day
        const dayEvents = sampleEvents.filter(e => e.date === dateString);
        
        let cellContent = `<div style="font-weight: ${isToday ? 'bold' : 'normal'}; 
                                     color: ${isToday ? 'var(--brand)' : 'var(--ink)'}; 
                                     margin-bottom: 5px;">${day}</div>`;
        
        // Add event indicators
        dayEvents.forEach(event => {
            const color = event.type === 'interview' ? '#28a745' : 
                         event.type === 'rehearsal' ? '#007bff' : '#ffc107';
            cellContent += `<div style="background: ${color}; color: white; font-size: 10px; 
                                      padding: 2px 4px; margin: 2px 0; border-radius: 3px; 
                                      white-space: nowrap; overflow: hidden; text-overflow: ellipsis; 
                                      cursor: pointer;" 
                                 onclick="viewEventFromCalendar('${dateString}')" 
                                 title="${event.title}">${event.title}</div>`;
        });
        
        dayCell.innerHTML = cellContent;
        dayCell.style.cssText = `background: ${isToday ? '#fff3cd' : 'white'}; 
                                 padding: 10px; 
                                 min-height: 80px; 
                                 cursor: pointer; 
                                 border: ${isToday ? '2px solid var(--brand)' : 'none'};`;
        
        dayCell.onclick = function(e) {
            if (e.target.tagName !== 'DIV' || !e.target.onclick) {
                addEventToDate(dateString);
            }
        };
        
        calendarDays.appendChild(dayCell);
    }
}

// Previous month
function previousMonth() {
    currentMonth--;
    if (currentMonth < 0) {
        currentMonth = 11;
        currentYear--;
    }
    generateCalendar(currentMonth, currentYear);
    console.log('Previous month:', currentMonth, currentYear);
}

// Next month
function nextMonth() {
    currentMonth++;
    if (currentMonth > 11) {
        currentMonth = 0;
        currentYear++;
    }
    generateCalendar(currentMonth, currentYear);
    console.log('Next month:', currentMonth, currentYear);
}

// View event from calendar
function viewEventFromCalendar(dateString) {
    console.log('View events for date:', dateString);
    const events = sampleEvents.filter(e => e.date === dateString);
    if (events.length > 0) {
        alert(`Events on ${dateString}:\n\n${events.map(e => `• ${e.title} (${e.type})`).join('\n')}`);
    }
}

// Add event to specific date
function addEventToDate(dateString) {
    console.log('Add event to date:', dateString);
    // Pre-fill modal with selected date
    openScheduleModal();
    // TODO: Set date in modal form
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    loadScheduleData();
    generateCalendar(currentMonth, currentYear);
    console.log('Schedule Management page loaded');
});

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('scheduleModal');
    if (modal && event.target === modal) {
        closeScheduleModal();
    }
};
