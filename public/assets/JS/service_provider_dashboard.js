// Keep track of loaded tabs
const loadedTabs = {};

//for back button
function showDashboard() {
    window.location.href = "first_dashboard.html"; // replace with your dashboard URL
}



// Base URL provided by PHP; fallback to origin + project path if not injected
const APP_ROOT = window.APP_ROOT || `${window.location.origin}/Rangamadala/public`;

// Load tab content dynamically
function loadTab(id, file) {
    if (loadedTabs[id]) return;

    fetch(file)
        .then(response => {
            if (!response.ok) throw new Error('File not found');
            return response.text();
        })
        .then(data => {
            const container = document.getElementById(id);
            container.innerHTML = data;
            loadedTabs[id] = true;
        })
        .catch(err => {
            console.error('Error loading file:', err);
            document.getElementById(id).innerHTML = '<p>Error loading content.</p>';
        });
}

// Load first tab by default
document.addEventListener('DOMContentLoaded', () => {
    loadTab('content1', `${APP_ROOT}/index.php?url=ServiceRequests`);
    loadTab('content2', `${APP_ROOT}/index.php?url=ServiceAvailability`);
    loadTab('content3', `${APP_ROOT}/index.php?url=ServicePayment`);
    loadTab('content4', `${APP_ROOT}/index.php?url=ServiceAnalytics`);
});

//js of service requuest

        let currentTab = 'completed';

        function switchTab(category) {
            // Remove active class from all tabs
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });

            // Add active class to clicked tab
            document.getElementById(category + 'Tab').classList.add('active');

            // Hide all requests
            document.querySelectorAll('.request-item').forEach(item => {
                item.style.display = 'none';
            });

            // Show requests for selected category
            document.querySelectorAll(`[data-category="${category}"]`).forEach(item => {
                item.style.display = 'flex';
            });

            currentTab = category;
        }

        function acceptRequest(button) {
            button.classList.add('selected');
            button.textContent = '✓ Accepted';
            
            // Disable reject button
            const rejectBtn = button.parentElement.querySelector('.btn-reject');
            if (rejectBtn) {
                rejectBtn.style.opacity = '0.5';
                rejectBtn.style.pointerEvents = 'none';
            }

            // Show success message
            showMessage('Request accepted successfully!', 'success');
        }

        function rejectRequest(button) {
            button.classList.add('selected');
            button.textContent = '✗ Rejected';
            button.classList.remove('btn-reject');
            button.classList.add('btn-rejected');
            
            // Disable accept button
            const acceptBtn = button.parentElement.querySelector('.btn-accept');
            if (acceptBtn) {
                acceptBtn.style.opacity = '0.5';
                acceptBtn.style.pointerEvents = 'none';
            }

            // Show message
            showMessage('Request rejected', 'error');
        }

        function updatePayment(button) {
            button.classList.add('selected');
            button.textContent = 'Payment Updated';
            showMessage('Payment information updated!', 'success');
        }

        function markCompleted(button) {
            if (button.classList.contains('selected')) {
                button.classList.remove('selected');
                button.textContent = 'Mark Complete';
            } else {
                button.classList.add('selected');
                button.textContent = 'Completed ✓';
            }
        }

        function goToDashboard() {
            showMessage('Navigating to Dashboard...', 'info');
        }

        function showMessage(text, type) {
            // Create message element
            const message = document.createElement('div');
            message.className = `message message-${type}`;
            message.textContent = text;
            
            // Style the message
            message.style.position = 'fixed';
            message.style.top = '20px';
            message.style.right = '20px';
            message.style.padding = '12px 20px';
            message.style.borderRadius = '6px';
            message.style.zIndex = '1000';
            message.style.fontWeight = '500';
            message.style.transition = 'all 0.3s ease';
            
            if (type === 'success') {
                message.style.background = '#28a745';
                message.style.color = 'white';
            } else if (type === 'error') {
                message.style.background = '#dc3545';
                message.style.color = 'white';
            } else {
                message.style.background = '#17a2b8';
                message.style.color = 'white';
            }
            
            document.body.appendChild(message);
            
            // Remove message after 3 seconds
            setTimeout(() => {
                message.style.opacity = '0';
                message.style.transform = 'translateY(-20px)';
                setTimeout(() => {
                    document.body.removeChild(message);
                }, 300);
            }, 3000);
        }

        // Initialize - show completed requests by default
        document.addEventListener('DOMContentLoaded', function() {
            switchTab('completed');
        });
    


//js of availability 
let currentMonth = 7; // August (0-indexed)
        let currentYear = 2025;
        let selectedDate = null;
        let availableDates = ['8/15/2024', '8/16/2024', '8/20/2024', '8/22/2024'];

        const months = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];

        function initializeCalendar() {
            updateCalendarDisplay();
            generateCalendarDays();
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
                const dateString = `${currentMonth + 1}/${day}/${currentYear}`;
                
                // Check if this date is in available dates
                if (availableDates.includes(dateString)) {
                    dayElement.classList.add('selected');
                }
                
                // Check if it's today
                if (currentDate.toDateString() === today.toDateString()) {
                    dayElement.classList.add('today');
                }
                
                // Add click event
                dayElement.addEventListener('click', () => selectDate(day, dayElement));
                
                calendarDays.appendChild(dayElement);
            }
        }

        function selectDate(day, element) {
            // Remove previous selection highlight
            document.querySelectorAll('.calendar-day.highlight').forEach(el => {
                el.classList.remove('highlight');
            });
            
            // Add highlight to selected day
            element.classList.add('highlight');
            selectedDate = `${currentMonth + 1}/${day}/${currentYear}`;
            
            // Show success message
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
        }

        function nextMonth() {
            currentMonth++;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            updateCalendarDisplay();
            generateCalendarDays();
        }

        function showAddDateForm() {
            if (!selectedDate) {
                showMessage('Please select a date from the calendar first', 'error');
                return;
            }
            
            if (availableDates.includes(selectedDate)) {
                showMessage('This date is already in your available dates', 'warning');
                return;
            }
            
            // Add the selected date to available dates
            availableDates.push(selectedDate);
            updateAvailableDatesList();
            
            // Update calendar display
            generateCalendarDays();
            
            showMessage(`Added ${selectedDate} to available dates`, 'success');
            selectedDate = null;
        }

        function removeDate(dateToRemove) {
            availableDates = availableDates.filter(date => date !== dateToRemove);
            updateAvailableDatesList();
            generateCalendarDays();
            showMessage(`Removed ${dateToRemove} from available dates`, 'success');
        }

        function updateAvailableDatesList() {
            const container = document.getElementById('availableDatesList');
            container.innerHTML = '';
            
            availableDates.sort((a, b) => new Date(a) - new Date(b));
            
            availableDates.forEach(date => {
                const dateItem = document.createElement('div');
                dateItem.className = 'date-item';
                dateItem.innerHTML = `
                    <span class="date-text">${date}</span>
                    <button class="remove-btn" onclick="removeDate('${date}')">Remove</button>
                `;
                container.appendChild(dateItem);
            });
        }

        function updateAvailability() {
            showMessage('Availability updated successfully!', 'success');
            
            // Add loading effect to button
            const btn = document.querySelector('.update-btn');
            btn.classList.add('loading');
            
            setTimeout(() => {
                btn.classList.remove('loading');
            }, 2000);
        }

        function goToDashboard() {
            showMessage('Navigating to Dashboard...', 'info');
        }

        function showMessage(text, type) {
            // Create message element
            const message = document.createElement('div');
            message.className = `message message-${type}`;
            message.textContent = text;
            
            // Style the message
            message.style.position = 'fixed';
            message.style.top = '20px';
            message.style.right = '20px';
            message.style.padding = '12px 20px';
            message.style.borderRadius = '6px';
            message.style.zIndex = '1000';
            message.style.fontWeight = '500';
            message.style.transition = 'all 0.3s ease';
            message.style.maxWidth = '300px';
            
            if (type === 'success') {
                message.style.background = '#28a745';
                message.style.color = 'white';
            } else if (type === 'error') {
                message.style.background = '#dc3545';
                message.style.color = 'white';
            } else if (type === 'warning') {
                message.style.background = '#ffc107';
                message.style.color = '#212529';
            } else {
                message.style.background = '#17a2b8';
                message.style.color = 'white';
            }
            
            document.body.appendChild(message);
            
            // Remove message after 4 seconds
            setTimeout(() => {
                message.style.opacity = '0';
                message.style.transform = 'translateY(-20px)';
                setTimeout(() => {
                    if (document.body.contains(message)) {
                        document.body.removeChild(message);
                    }
                }, 300);
            }, 4000);
        }

        // Initialize calendar when page loads
        document.addEventListener('DOMContentLoaded', function() {
            initializeCalendar();
        });


        //js of payment
        let totalPending = 2450;
        let totalReceived = 8750;
        let currentFilter = 'all';

        function filterPayments(filterType) {
            const paymentItems = document.querySelectorAll('.payment-item');
            const pendingBtn = document.getElementById('pendingBtn');
            const receivedBtn = document.getElementById('receivedBtn');
            const filterMessage = document.getElementById('filterMessage');
            let visibleCount = 0;

            // Update button states
            pendingBtn.classList.remove('active');
            receivedBtn.classList.remove('active');

            if (filterType === 'pending') {
                pendingBtn.classList.add('active');
                currentFilter = 'pending';
            } else if (filterType === 'received') {
                receivedBtn.classList.add('active');
                currentFilter = 'paid';
            } else {
                currentFilter = 'all';
            }

            // Show/hide payment items based on filter
            paymentItems.forEach(item => {
                const status = item.getAttribute('data-status');
                if (currentFilter === 'all' || status === currentFilter) {
                    item.classList.remove('hidden');
                    visibleCount++;
                } else {
                    item.classList.add('hidden');
                }
            });

            // Show message if no items visible
            if (visibleCount === 0) {
                filterMessage.style.display = 'block';
            } else {
                filterMessage.style.display = 'none';
            }

            // Add ripple effect
            const activeBtn = filterType === 'pending' ? pendingBtn : receivedBtn;
            createRipple(activeBtn);
        }

        function createRipple(button) {
            const ripple = document.createElement('div');
            ripple.style.position = 'absolute';
            ripple.style.borderRadius = '50%';
            ripple.style.background = 'rgba(255,255,255,0.6)';
            ripple.style.transform = 'scale(0)';
            ripple.style.animation = 'ripple 0.6s linear';
            ripple.style.left = '50%';
            ripple.style.top = '50%';
            ripple.style.width = '20px';
            ripple.style.height = '20px';
            ripple.style.marginLeft = '-10px';
            ripple.style.marginTop = '-10px';

            button.appendChild(ripple);

            setTimeout(() => {
                ripple.remove();
            }, 600);
        }

        function showAllPayments() {
            document.getElementById('pendingBtn').classList.remove('active');
            document.getElementById('receivedBtn').classList.remove('active');
            filterPayments('all');
        }

        function showPaymentDetails(title, service, requester, amount, status) {
            const modal = document.getElementById('paymentModal');
            const modalBody = document.getElementById('modalBody');
            
            const statusClass = status === 'Paid' ? 'paid-status' : 'pending-status';
            
            modalBody.innerHTML = `
                <div class="modal-payment-info">
                    <h3>${title}</h3>
                    <p><strong>Service:</strong> ${service}</p>
                    <p><strong>Client:</strong> ${requester}</p>
                    <p><strong>Amount:</strong> <span class="modal-amount">$${amount}</span></p>
                    <p><strong>Status:</strong> <span class="payment-status-badge ${statusClass}">${status}</span></p>
                    <p><strong>Date:</strong> ${new Date().toLocaleDateString()}</p>
                </div>
                ${status === 'Pending' ? `
                    <div class="modal-actions">
                        <button class="btn btn-mark-paid" onclick="markAsPaid('${title}', ${amount})">Mark as Paid</button>
                        <button class="btn btn-send-reminder" onclick="sendReminder('${title}')">Send Payment Reminder</button>
                    </div>
                ` : `
                    <div class="modal-actions">
                        <button class="btn btn-download" onclick="downloadInvoice('${title}')">Download Invoice</button>
                        <button class="btn btn-send-receipt" onclick="sendReceipt('${title}')">Send Receipt</button>
                    </div>
                `}
            `;
            
            modal.style.display = 'block';
        }

        function closeModal() {
            document.getElementById('paymentModal').style.display = 'none';
        }

        function markAsPaid(title, amount) {
            const paymentItems = document.querySelectorAll('.payment-item');
            paymentItems.forEach(item => {
                const titleElement = item.querySelector('h3');
                if (titleElement.textContent === title) {
                    const statusBadge = item.querySelector('.payment-status-badge');
                    statusBadge.textContent = 'Paid';
                    statusBadge.className = 'payment-status-badge paid-status';
                    item.setAttribute('data-status', 'paid');
                    
                    // Add animation effect
                    item.style.transform = 'scale(1.02)';
                    setTimeout(() => {
                        item.style.transform = 'scale(1)';
                    }, 300);
                }
            });

            // Update totals
            totalPending -= amount;
            totalReceived += amount;
            updateSummaryTotals();

            closeModal();
            showMessage(`${title} marked as paid!`, 'success');
        }

        function sendReminder(title) {
            closeModal();
            showMessage(`Payment reminder sent for ${title}`, 'info');
        }

        function downloadInvoice(title) {
            closeModal();
            showMessage(`Downloading invoice for ${title}...`, 'info');
        }

        function sendReceipt(title) {
            closeModal();
            showMessage(`Receipt sent for ${title}`, 'success');
        }

        function updateSummaryTotals() {
            document.querySelector('.pending .summary-amount').textContent = `$${totalPending.toLocaleString()}`;
            document.querySelector('.received .summary-amount').textContent = `$${totalReceived.toLocaleString()}`;
        }

        function showMessage(text, type) {
            const message = document.createElement('div');
            message.className = `message message-${type}`;
            message.textContent = text;
            
            message.style.position = 'fixed';
            message.style.top = '20px';
            message.style.right = '20px';
            message.style.padding = '12px 20px';
            message.style.borderRadius = '6px';
            message.style.zIndex = '10000';
            message.style.fontWeight = '500';
            message.style.transition = 'all 0.3s ease';
            message.style.maxWidth = '300px';
            
            if (type === 'success') {
                message.style.background = '#b8860b';
                message.style.color = 'white';
            } else if (type === 'error') {
                message.style.background = '#dc3545';
                message.style.color = 'white';
            } else if (type === 'warning') {
                message.style.background = '#daa520';
                message.style.color = 'white';
            } else {
                message.style.background = '#a0892c';
                message.style.color = 'white';
            }
            
            document.body.appendChild(message);
            
            setTimeout(() => {
                message.style.opacity = '0';
                message.style.transform = 'translateY(-20px)';
                setTimeout(() => {
                    if (document.body.contains(message)) {
                        document.body.removeChild(message);
                    }
                }, 300);
            }, 4000);
        }

        // Add CSS for ripple animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);

        // Initialize event listeners
        document.addEventListener('DOMContentLoaded', function() {
            const paymentItems = document.querySelectorAll('.payment-item');
            paymentItems.forEach(item => {
                item.addEventListener('click', function() {
                    const title = this.querySelector('h3').textContent;
                    const service = this.querySelector('.payment-details').textContent;
                    const requester = this.querySelector('.payment-requester').textContent;
                    const amount = this.querySelector('.payment-amount').textContent.replace('$', '');
                    const status = this.querySelector('.payment-status-badge').textContent;
                    
                    showPaymentDetails(title, service, requester, amount, status);
                });
            });

            // Close modal when clicking outside
            window.addEventListener('click', function(event) {
                const modal = document.getElementById('paymentModal');
                if (event.target === modal) {
                    closeModal();
                }
            });

            // Initialize with pending filter active
            filterPayments('pending');
        });