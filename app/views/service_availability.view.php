<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle : 'Availability Calendar' ?> - Rangamadala</title>
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/service provider/service_provider_dashboard.css">
    <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/service provider/service_availability.css">
    <link rel="shortcut icon" href="<?= ROOT ?>/assets/images/Rangamadala logo.png" type="image/x-icon">
</head>
<body>
    <?php $activePage = 'availability'; include 'includes/service_provider/sidebar.php'; ?>
    
    <div class="main--content">
        <?php include 'includes/service_provider/header.php'; ?>

        <div class="container">
        <div class="header">
            <div class="title-section">
                <h1>Availability Calendar</h1>
                <p>Manage your service availability</p>
            </div>
        </div>

        <!-- Legend for calendar status -->
        <div class="legend-container">
            <div class="legend-item">
                <span class="legend-dot available"></span>
                <span>Available</span>
            </div>
            <div class="legend-item">
                <span class="legend-dot booked"></span>
                <span>Booked</span>
            </div>
            <div class="legend-item">
                <span class="legend-dot past"></span>
                <span>Past Date</span>
            </div>
        </div>

        <button class="update-btn" onclick="updateAvailability()">
            <span class="btn-icon">⟲</span>
            Update Availability
        </button>

        <div class="calendar-container">
            <!-- Left Panel - Calendar -->
            <div class="calendar-panel">
                <div class="panel-header">
                    <h2>Calendar</h2>
                    <p>Select your available dates</p>
                </div>
                
                <div class="calendar">
                    <div class="calendar-header">
                        <button class="nav-btn" id="prevMonth" onclick="previousMonth()">&lt;</button>
                        <h3 id="monthYear">August 2025</h3>
                        <button class="nav-btn" id="nextMonth" onclick="nextMonth()">&gt;</button>
                    </div>
                    
                    <div class="calendar-grid">
                        <div class="day-header">Su</div>
                        <div class="day-header">Mo</div>
                        <div class="day-header">Tu</div>
                        <div class="day-header">We</div>
                        <div class="day-header">Th</div>
                        <div class="day-header">Fr</div>
                        <div class="day-header">Sa</div>
                        
                        <div id="calendarDays"></div>
                    </div>
                </div>
            </div>

            <!-- Right Panel - Available Dates -->
            <div class="dates-panel">
                <div class="panel-header">
                    <h2>Available Dates</h2>
                    <p>Your current availability</p>
                </div>
                
                <div class="available-dates-list" id="availableDatesList">
                    <!-- Available dates will be populated here -->
                </div>
                
                <button class="add-date-btn" onclick="showAddDateForm()" id="addDateBtn" disabled>
                    <span class="btn-icon">+</span>
                    Add Selected Date
                </button>
            </div>
        </div>
    </div>

    <!-- Modal for adding date with description -->
    <div class="modal-overlay" id="modalOverlay">
        <div class="modal">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            <h3>Add Available Date</h3>
            <div class="form-group">
                <label for="selectedDateDisplay">Selected Date:</label>
                <input type="text" id="selectedDateDisplay" readonly>
            </div>
            <div class="form-group">
                <label for="dateDescription">Description:</label>
                <textarea 
                    id="dateDescription" 
                    placeholder="Enter a description for this booking (e.g., 'Available for photography sessions', 'Wedding consultation', etc.)"
                    rows="4"
                ></textarea>
            </div>
            <div class="modal-actions">
                <button class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button class="btn btn-primary" onclick="addDateWithDescription()">Add Date</button>
            </div>
        </div>
    </div>

    <!-- Modal for viewing date details -->
    <div class="modal-overlay" id="viewModalOverlay">
        <div class="modal view-modal">
            <span class="close-modal" onclick="closeViewModal()">&times;</span>
            <h3 id="viewModalTitle">Date Details</h3>
            <div class="date-details">
                <div class="detail-row">
                    <strong>Date:</strong>
                    <span id="viewDateText"></span>
                </div>
                <div class="detail-row status-row">
                    <strong>Status:</strong>
                    <span id="viewStatusBadge" class="status-badge"></span>
                </div>
                <div class="detail-row" id="bookedForRow" style="display: none;">
                    <strong>Booked For:</strong>
                    <span id="bookedForText"></span>
                </div>
                <div class="detail-row">
                    <strong>Description:</strong>
                    <p id="viewDescriptionText" class="description-content"></p>
                </div>
            </div>
            <div class="modal-actions">
                <button class="btn btn-secondary" onclick="closeViewModal()">Close</button>
                <button class="btn btn-warning" onclick="editDate()" id="editBtn">Edit</button>
                <button class="btn btn-danger" onclick="removeCurrentDate()" id="removeBtn">Remove</button>
            </div>
        </div>
    </div>

    <!-- Modal for accepting booking request
    <div class="modal-overlay" id="bookingModalOverlay">
        <div class="modal booking-modal">
            <span class="close-modal" onclick="closeBookingModal()">&times;</span>
            <h3>Accept Booking Request</h3>
            <div class="form-group">
                <label for="bookingDateDisplay">Date:</label>
                <input type="text" id="bookingDateDisplay" readonly>
            </div>
            <div class="form-group">
                <label for="clientName">Client/Request Name:</label>
                <input 
                    type="text" 
                    id="clientName" 
                    placeholder="Enter client name or request description"
                    required
                >
            </div>
            <div class="form-group">
                <label for="bookingDescription">Booking Details:</label>
                <textarea 
                    id="bookingDescription" 
                    placeholder="Enter booking details (service type, notes, etc.)"
                    rows="4"
                ></textarea>
            </div>
            <div class="modal-actions">
                <button class="btn btn-secondary" onclick="closeBookingModal()">Cancel</button>
                <button class="btn btn-success" onclick="confirmBooking()">Confirm Booking</button>
            </div>
        </div>
    </div> -->

    <script>
        // Calendar state
        let currentMonth = new Date().getMonth();
        let currentYear = new Date().getFullYear();
        let selectedDate = null;
        let currentViewingDate = null;

        // Data storage
        let availableDatesData = {};

        const months = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];

        // Initialize calendar on page load
        document.addEventListener('DOMContentLoaded', function() {
            initializeCalendar();
        });

        function initializeCalendar() {
            updateCalendarDisplay();
            generateCalendarDays();
            updateAvailableDatesList();
        }

        function updateCalendarDisplay() {
            document.getElementById('monthYear').textContent = `${months[currentMonth]} ${currentYear}`;
        }

        function generateCalendarDays() {
            const calendarDays = document.getElementById('calendarDays');
            calendarDays.innerHTML = '';

            const firstDay = new Date(currentYear, currentMonth, 1).getDay();
            const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            // Add empty cells for days before the first day of the month
            for (let i = 0; i < firstDay; i++) {
                const emptyDay = document.createElement('div');
                emptyDay.className = 'calendar-day empty';
                calendarDays.appendChild(emptyDay);
            }

            // Add days of the month
            for (let day = 1; day <= daysInMonth; day++) {
                const dayElement = document.createElement('div');
                dayElement.className = 'calendar-day';
                dayElement.textContent = day;
                
                const currentDate = new Date(currentYear, currentMonth, day);
                currentDate.setHours(0, 0, 0, 0);
                const dateString = `${currentMonth + 1}/${day}/${currentYear}`;
                
                // Check if date is in the past
                if (currentDate < today) {
                    dayElement.classList.add('past');
                } else {
                    // Check if this date has availability data
                    if (availableDatesData[dateString]) {
                        const dateData = availableDatesData[dateString];
                        if (dateData.status === 'booked') {
                            dayElement.classList.add('booked');
                            dayElement.title = `Booked for: ${dateData.bookedFor}`;
                        } else {
                            dayElement.classList.add('selected');
                            dayElement.title = 'Available';
                        }
                        // Add click event to view details
                        dayElement.addEventListener('click', () => viewDateDetails(dateString));
                    } else {
                        // Add click event to select date
                        dayElement.addEventListener('click', () => selectDate(day, dayElement, dateString));
                    }
                }
                
                // Check if it's today
                if (currentDate.getTime() === today.getTime()) {
                    dayElement.classList.add('today');
                }
                
                calendarDays.appendChild(dayElement);
            }
        }

        function selectDate(day, element, dateString) {
            // Remove previous selection highlight
            document.querySelectorAll('.calendar-day.highlight').forEach(el => {
                el.classList.remove('highlight');
            });
            
            // Add highlight to selected day
            element.classList.add('highlight');
            selectedDate = dateString;
            
            // Enable add date button
            document.getElementById('addDateBtn').disabled = false;
            
            showMessage(`Selected date: ${selectedDate}`, 'info');
        }

        function previousMonth() {
            currentMonth--;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            updateCalendarDisplay();
            generateCalendarDays();
            selectedDate = null;
            document.getElementById('addDateBtn').disabled = true;
        }

        function nextMonth() {
            currentMonth++;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            updateCalendarDisplay();
            generateCalendarDays();
            selectedDate = null;
            document.getElementById('addDateBtn').disabled = true;
        }

        // Modal functions
        function showAddDateForm() {
            if (!selectedDate) {
                showMessage('Please select a date from the calendar first', 'error');
                return;
            }
            
            if (availableDatesData[selectedDate]) {
                showMessage('This date is already in your available dates', 'warning');
                return;
            }
            
            document.getElementById('selectedDateDisplay').value = selectedDate;
            document.getElementById('dateDescription').value = '';
            document.getElementById('modalOverlay').classList.add('active');
        }

        function closeModal() {
            document.getElementById('modalOverlay').classList.remove('active');
        }

        function addDateWithDescription() {
            const description = document.getElementById('dateDescription').value.trim();
            
            if (!description) {
                showMessage('Please enter a description for this date', 'error');
                return;
            }
            
            // Add the selected date with description
            availableDatesData[selectedDate] = {
                date: selectedDate,
                description: description,
                status: 'available',
                bookedFor: null,
                bookingDetails: null,
                addedOn: new Date().toISOString()
            };
            
            updateAvailableDatesList();
            generateCalendarDays();
            closeModal();
            
            showMessage(`Added ${selectedDate} to available dates`, 'success');
            selectedDate = null;
            document.getElementById('addDateBtn').disabled = true;
            
            // Remove highlight from calendar
            document.querySelectorAll('.calendar-day.highlight').forEach(el => {
                el.classList.remove('highlight');
            });
        }

        // View date details modal
        function viewDateDetails(dateString) {
            const dateData = availableDatesData[dateString];
            if (!dateData) return;
            
            currentViewingDate = dateString;
            
            document.getElementById('viewDateText').textContent = dateString;
            document.getElementById('viewDescriptionText').textContent = dateData.description;
            
            // Update status badge
            const statusBadge = document.getElementById('viewStatusBadge');
            statusBadge.textContent = dateData.status;
            statusBadge.className = `status-badge ${dateData.status}`;
            
            // Show/hide booked info
            const bookedForRow = document.getElementById('bookedForRow');
            if (dateData.status === 'booked') {
                bookedForRow.style.display = 'block';
                document.getElementById('bookedForText').textContent = dateData.bookedFor;
                document.getElementById('viewModalTitle').textContent = 'Booking Details';
                // Hide edit button for booked dates
                document.getElementById('editBtn').style.display = 'none';
            } else {
                bookedForRow.style.display = 'none';
                document.getElementById('viewModalTitle').textContent = 'Date Details';
                document.getElementById('editBtn').style.display = 'inline-block';
            }
            
            document.getElementById('viewModalOverlay').classList.add('active');
        }

        function closeViewModal() {
            document.getElementById('viewModalOverlay').classList.remove('active');
            currentViewingDate = null;
        }

        function editDate() {
            if (!currentViewingDate) return;
            
            closeViewModal();
            selectedDate = currentViewingDate;
            
            document.getElementById('selectedDateDisplay').value = selectedDate;
            document.getElementById('dateDescription').value = availableDatesData[selectedDate].description;
            document.getElementById('modalOverlay').classList.add('active');
        }

        function removeCurrentDate() {
            if (!currentViewingDate) return;
            
            if (confirm(`Are you sure you want to remove ${currentViewingDate}?`)) {
                delete availableDatesData[currentViewingDate];
                updateAvailableDatesList();
                generateCalendarDays();
                closeViewModal();
                showMessage(`Removed ${currentViewingDate} from available dates`, 'success');
            }
        }

        // Booking modal functions
        function showBookingModal(dateString) {
            currentViewingDate = dateString;
            document.getElementById('bookingDateDisplay').value = dateString;
            document.getElementById('clientName').value = '';
            document.getElementById('bookingDescription').value = availableDatesData[dateString].description;
            document.getElementById('bookingModalOverlay').classList.add('active');
        }

        function closeBookingModal() {
            document.getElementById('bookingModalOverlay').classList.remove('active');
            currentViewingDate = null;
        }

        function confirmBooking() {
            const clientName = document.getElementById('clientName').value.trim();
            const bookingDetails = document.getElementById('bookingDescription').value.trim();
            
            if (!clientName) {
                showMessage('Please enter client/request name', 'error');
                return;
            }
            
            if (!currentViewingDate || !availableDatesData[currentViewingDate]) return;
            
            // Update the date status to booked
            availableDatesData[currentViewingDate].status = 'booked';
            availableDatesData[currentViewingDate].bookedFor = clientName;
            availableDatesData[currentViewingDate].bookingDetails = bookingDetails;
            availableDatesData[currentViewingDate].bookedOn = new Date().toISOString();
            
            updateAvailableDatesList();
            generateCalendarDays();
            closeBookingModal();
            
            showMessage(`Date ${currentViewingDate} has been booked for ${clientName}`, 'success');
        }

        // Update available dates list
        function updateAvailableDatesList() {
            const container = document.getElementById('availableDatesList');
            container.innerHTML = '';
            
            // Convert object to array and sort by date
            const sortedDates = Object.keys(availableDatesData).sort((a, b) => new Date(a) - new Date(b));
            
            if (sortedDates.length === 0) {
                container.innerHTML = '<p style="text-align: center; color: #6c757d; padding: 20px;">No available dates added yet.</p>';
                return;
            }
            
            sortedDates.forEach(date => {
                const dateData = availableDatesData[date];
                const dateItem = document.createElement('div');
                dateItem.className = 'date-item';
                
                let bookedInfoHTML = '';
                if (dateData.status === 'booked') {
                    bookedInfoHTML = `
                        <div class="booked-info">
                            <strong>Booked For:</strong>
                            <span>${dateData.bookedFor}</span>
                        </div>
                    `;
                }
                
                dateItem.innerHTML = `
                    <div class="date-item-header">
                        <span class="date-text">${date}</span>
                        <span class="status-badge ${dateData.status}">${dateData.status}</span>
                    </div>
                    <div class="date-description">${dateData.description}</div>
                    ${bookedInfoHTML}
                    <div class="date-actions">
                        ${dateData.status === 'available' 
                            ? `<button onclick="showBookingModal('${date}')"></button>` 
                            : ''}
                        <button class="btn-small btn-view" onclick="viewDateDetails('${date}')">View Details</button>
                        <button class="btn-small btn-remove" onclick="removeDate('${date}')">Remove</button>
                    </div>
                `;
                
                container.appendChild(dateItem);
            });
        }

        function removeDate(dateToRemove) {
            if (confirm(`Are you sure you want to remove ${dateToRemove}?`)) {
                delete availableDatesData[dateToRemove];
                updateAvailableDatesList();
                generateCalendarDays();
                showMessage(`Removed ${dateToRemove} from available dates`, 'success');
            }
        }

        // Update availability (simulate saving to server)
        function updateAvailability() {
            showMessage('Availability updated successfully!', 'success');
            
            // Add loading effect
            const btn = document.querySelector('.update-btn');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<span class="btn-icon">⟲</span> Updating...';
            btn.disabled = true;
            
            // Simulate API call
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
                showMessage('All changes saved!', 'success');
            }, 1500);
        }

        // Show notification messages
        function showMessage(text, type) {
            // Remove any existing messages
            const existingMessage = document.querySelector('.notification-message');
            if (existingMessage) {
                existingMessage.remove();
            }
            
            // Create message element
            const message = document.createElement('div');
            message.className = 'notification-message';
            message.textContent = text;
            
            // Style based on type
            const colors = {
                success: { bg: '#28a745', color: 'white' },
                error: { bg: '#dc3545', color: 'white' },
                warning: { bg: '#ffc107', color: '#212529' },
                info: { bg: '#17a2b8', color: 'white' }
            };
            
            const color = colors[type] || colors.info;
            
            Object.assign(message.style, {
                position: 'fixed',
                top: '20px',
                right: '20px',
                padding: '16px 24px',
                background: color.bg,
                color: color.color,
                borderRadius: '8px',
                boxShadow: '0 4px 12px rgba(0,0,0,0.15)',
                zIndex: '10000',
                fontWeight: '500',
                fontSize: '14px',
                maxWidth: '350px',
                animation: 'slideInRight 0.3s ease'
            });
            
            // Add notification animation styles
            if (!document.getElementById('notification-styles')) {
                const style = document.createElement('style');
                style.id = 'notification-styles';
                style.textContent = `
                    @keyframes slideInRight {
                        from {
                            transform: translateX(400px);
                            opacity: 0;
                        }
                        to {
                            transform: translateX(0);
                            opacity: 1;
                        }
                    }
                    
                    @keyframes slideOutRight {
                        from {
                            transform: translateX(0);
                            opacity: 1;
                        }
                        to {
                            transform: translateX(400px);
                            opacity: 0;
                        }
                    }
                `;
                document.head.appendChild(style);
            }
            
            document.body.appendChild(message);
            
            // Auto remove after 4 seconds
            setTimeout(() => {
                message.style.animation = 'slideOutRight 0.3s ease';
                setTimeout(() => {
                    if (message.parentNode) {
                        message.remove();
                    }
                }, 300);
            }, 4000);
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal-overlay')) {
                if (event.target.id === 'modalOverlay') {
                    closeModal();
                } else if (event.target.id === 'viewModalOverlay') {
                    closeViewModal();
                } else if (event.target.id === 'bookingModalOverlay') {
                    closeBookingModal();
                }
            }
        }
    </script>
    </div>
</body>
</html>