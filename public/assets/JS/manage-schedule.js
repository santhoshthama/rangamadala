// Dynamic data loaded from database via manage_schedule.php
// The 'schedules' variable is populated from PHP in the view file
let serviceData = [];

// Convert database schedules to calendar format
if (typeof schedules !== 'undefined' && Array.isArray(schedules)) {
    serviceData = schedules.map(schedule => {
        // Parse the scheduled date
        const dateObj = schedule.scheduledDate ? new Date(schedule.scheduledDate) : new Date();
        
        return {
            id: schedule.id || null,
            type: schedule.serviceName || 'Service',
            provider: schedule.venue || 'TBD',
            date: dateObj,
            cost: 0, // Cost should come from budget or service request
            status: schedule.status === 'completed' ? 'paid' : schedule.status === 'in_progress' ? 'accepted' : 'awaiting',
            description: schedule.notes || 'No description provided',
            startTime: schedule.startTime || '',
            endTime: schedule.endTime || ''
        };
    });
    
    console.log('Converted ' + serviceData.length + ' database schedules to calendar format');
} else {
    console.warn('No schedule data available from database');
}

let currentDate = new Date();
let currentFilter = '';

// Initialize calendar on page load
document.addEventListener('DOMContentLoaded', function() {
    renderCalendar();
    renderTimeline();
});

function renderCalendar() {
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    
    // Update month/year display
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                       'July', 'August', 'September', 'October', 'November', 'December'];
    document.getElementById('currentMonth').textContent = `${monthNames[month]} ${year}`;
    
    // Get first day of month and number of days
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const daysInPrevMonth = new Date(year, month, 0).getDate();
    
    const calendarGrid = document.getElementById('calendarGrid');
    calendarGrid.innerHTML = '';
    
    // Previous month dates
    for (let i = firstDay - 1; i >= 0; i--) {
        const date = daysInPrevMonth - i;
        const dateElement = createDateElement(date, month - 1, year, true);
        calendarGrid.appendChild(dateElement);
    }
    
    // Current month dates
    for (let date = 1; date <= daysInMonth; date++) {
        const dateElement = createDateElement(date, month, year, false);
        calendarGrid.appendChild(dateElement);
    }
    
    // Next month dates
    const totalCells = calendarGrid.children.length;
    const remainingCells = 42 - totalCells;
    for (let date = 1; date <= remainingCells; date++) {
        const dateElement = createDateElement(date, month + 1, year, true);
        calendarGrid.appendChild(dateElement);
    }
}

function createDateElement(date, month, year, isOtherMonth) {
    const div = document.createElement('div');
    div.className = 'calendar-date';
    
    if (isOtherMonth) {
        div.classList.add('other-month');
    }
    
    // Check if today
    const today = new Date();
    if (!isOtherMonth && date === today.getDate() && 
        month === today.getMonth() && year === today.getFullYear()) {
        div.classList.add('today');
    }
    
    // Create date element
    const dateNum = document.createElement('div');
    dateNum.className = 'date-number';
    dateNum.textContent = date;
    div.appendChild(dateNum);
    
    // Get events for this date
    const eventContainer = document.createElement('div');
    eventContainer.className = 'date-events';
    
    const actualMonth = isOtherMonth ? (month < 0 ? 11 : month > 11 ? 0 : month) : month;
    const actualYear = isOtherMonth && month < 0 ? year - 1 : isOtherMonth && month > 11 ? year + 1 : year;
    
    const events = getEventsForDate(date, actualMonth, actualYear);
    events.forEach(event => {
        if (!currentFilter || event.status === currentFilter) {
            const eventEl = document.createElement('div');
            eventEl.className = `event-item ${event.status}`;
            eventEl.textContent = event.type.substring(0, 15);
            eventEl.onclick = (e) => {
                e.stopPropagation();
                showEventModal(event);
            };
            eventContainer.appendChild(eventEl);
        }
    });
    
    div.appendChild(eventContainer);
    
    return div;
}

function getEventsForDate(date, month, year) {
    return serviceData.filter(service => {
        const serviceDate = service.date;
        return serviceDate.getDate() === date &&
               serviceDate.getMonth() === month &&
               serviceDate.getFullYear() === year;
    });
}

function renderTimeline() {
    const timelineContent = document.getElementById('timelineContent');
    timelineContent.innerHTML = '';
    
    // Sort services by date
    const sortedServices = [...serviceData].sort((a, b) => a.date - b.date);
    
    sortedServices.forEach(service => {
        if (!currentFilter || service.status === currentFilter) {
            const timelineItem = document.createElement('div');
            timelineItem.className = 'timeline-item';
            
            const date = service.date.toLocaleDateString('en-US', 
                { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' });
            
            timelineItem.innerHTML = `
                <div class="timeline-date">${date}</div>
                <div class="timeline-content">
                    <div class="timeline-title">${service.type}</div>
                    <div class="timeline-status">
                        <strong>Provider:</strong> ${service.provider}<br>
                        <strong>Cost:</strong> LKR ${service.cost.toLocaleString()}
                    </div>
                    <div>
                        <span class="status-badge ${service.status}">${getStatusLabel(service.status)}</span>
                    </div>
                </div>
            `;
            
            timelineItem.style.cursor = 'pointer';
            timelineItem.onclick = () => showEventModal(service);
            timelineContent.appendChild(timelineItem);
        }
    });
}

function getStatusLabel(status) {
    const labels = {
        'awaiting': 'Awaiting Response',
        'accepted': 'Accepted',
        'paid': 'Paid & Confirmed',
        'theater': 'Theater Booking',
        'completed': 'Completed'
    };
    return labels[status] || status;
}

function previousMonth() {
    currentDate.setMonth(currentDate.getMonth() - 1);
    renderCalendar();
    renderTimeline();
}

function nextMonth() {
    currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar();
    renderTimeline();
}

function switchView(view) {
    // Update buttons
    document.querySelectorAll('.view-toggle button').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.closest('button').classList.add('active');
    
    // Update views
    const calendarView = document.getElementById('calendarView');
    const timelineView = document.getElementById('timelineView');
    
    if (view === 'calendar') {
        calendarView.classList.add('active');
        timelineView.classList.remove('active');
        calendarView.parentElement.style.display = 'block';
    } else {
        calendarView.classList.remove('active');
        timelineView.classList.add('active');
        renderTimeline();
    }
}

function filterByStatus(status) {
    currentFilter = status;
    
    // Update filter buttons
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
    
    // Re-render both views
    renderCalendar();
    renderTimeline();
}

function showEventModal(service) {
    const modal = document.getElementById('eventModal');
    document.getElementById('eventTitle').textContent = service.type;
    
    const eventDetails = document.getElementById('eventDetails');
    const date = service.date.toLocaleDateString('en-US', 
        { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    
    eventDetails.innerHTML = `
        <div class="modal-section">
            <div class="modal-section-title">Scheduled Date</div>
            <div class="modal-section-content">${date}</div>
        </div>
        <div class="modal-section">
            <div class="modal-section-title">Service Provider</div>
            <div class="modal-section-content">${service.provider}</div>
        </div>
        <div class="modal-section">
            <div class="modal-section-title">Cost</div>
            <div class="modal-section-content">LKR ${service.cost.toLocaleString()}</div>
        </div>
        <div class="modal-section">
            <div class="modal-section-title">Description</div>
            <div class="modal-section-content">${service.description}</div>
        </div>
        <div class="modal-section">
            <div class="modal-section-title">Status</div>
            <div class="modal-section-content">
                <span class="status-badge ${service.status}">${getStatusLabel(service.status)}</span>
            </div>
        </div>
    `;
    
    const modalActions = document.getElementById('modalActions');
    modalActions.innerHTML = '';
    
    if (service.status === 'awaiting') {
        modalActions.innerHTML = `
            <button class="btn-secondary-modal" onclick="closeEventModal()">Close</button>
            <button class="btn-primary-modal" onclick="viewServiceDetails(${service.id})">View Details</button>
        `;
    } else if (service.status === 'accepted') {
        modalActions.innerHTML = `
            <button class="btn-secondary-modal" onclick="closeEventModal()">Close</button>
            <button class="btn-primary-modal" onclick="openPaymentModal(${service.id})">Pay Now</button>
        `;
    } else if (service.status === 'paid') {
        modalActions.innerHTML = `
            <button class="btn-secondary-modal" onclick="closeEventModal()">Close</button>
            <button class="btn-primary-modal" onclick="viewPaymentDetails(${service.id})">View Payment</button>
        `;
    } else {
        modalActions.innerHTML = `
            <button class="btn-primary-modal" onclick="closeEventModal()">Close</button>
        `;
    }
    
    modal.classList.add('active');
}

function closeEventModal() {
    document.getElementById('eventModal').classList.remove('active');
}

function viewServiceDetails(serviceId) {
    // TODO: Implement - Redirect to manage_services.php with service_id
    console.log('View service details for ID:', serviceId);
    window.location.href = `manage_services.php?drama_id=1&service_id=${serviceId}`;
}

function openPaymentModal(serviceId) {
    // TODO: Implement - Open payment modal from manage_services.js
    console.log('Open payment for service ID:', serviceId);
    closeEventModal();
    // Redirect to manage_services.php and open payment modal
    window.location.href = `manage_services.php?drama_id=1&service_id=${serviceId}&payment=true`;
}

function viewPaymentDetails(serviceId) {
    // TODO: Implement - Show payment receipt/details
    console.log('View payment details for service ID:', serviceId);
    alert('Payment Details - Service ID: ' + serviceId + '\nPayment Status: Completed\nAmount: LKR 65,000');
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('eventModal');
    if (event.target === modal) {
        closeEventModal();
    }
});
