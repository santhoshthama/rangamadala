<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle : 'Service Requests' ?> - Rangamadala</title>
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/service_provider_dashboard.css">
    <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/service provider/service_requests.css">
    <link rel="shortcut icon" href="<?= ROOT ?>/assets/images/Rangamadala logo.png" type="image/x-icon">
</head>
<body>
    <?php $activePage = 'requests'; include 'includes/service_provider/sidebar.php'; ?>
    
    <div class="main--content">
        <?php include 'includes/service_provider/header.php'; ?>

        <div class="container">
        
        <div class="tabs">
            <button class="tab" id="pendingTab" onclick="switchTab('pending')">3 Pending</button>
            <button class="tab" id="acceptedTab" onclick="switchTab('accepted')">2 Accepted</button>
            <button class="tab active" id="completedTab" onclick="switchTab('completed')">5 Completed</button>
        </div>

        <div class="requests-list" id="requestsList">
            <!-- Pending Requests -->
            <div class="request-item romeo-juliet pending-request" data-category="pending">
                <div class="request-info">
                    <h3>Romeo and Juliet</h3>
                    <div class="request-details">Sound Equipment • Requested by Theatre Group ABC</div>
                    <div class="service-date">Service Date: 2024-08-20</div>
                </div>
                <div class="request-actions">
                    <span class="status-badge status-pending">pending</span>
                    <span class="price">$500</span>
                    <button class="btn btn-reject" onclick="rejectRequest(this)"> Reject</button>
                    <button class="btn btn-accept" onclick="acceptRequest(this)"> Accept</button>
                </div>
            </div>

            <!-- Accepted Requests -->
            <div class="request-item hamlet accepted-request" data-category="accepted" style="display: none;">
                <div class="request-info">
                    <h3>Hamlet</h3>
                    <div class="request-details">Lighting Setup • Requested by Drama Society</div>
                    <div class="service-date">Service Date: 2024-08-25</div>
                </div>
                <div class="request-actions">
                    <span class="status-badge status-accepted">accepted</span>
                    <span class="price">$750</span>
                    <button class="btn btn-update" onclick="updatePayment(this)">Update Payment</button>
                </div>
            </div>

            <!-- Completed Requests -->
            <div class="request-item macbeth completed-request" data-category="completed" style="display: none;">
                <div class="request-info">
                    <h3>Macbeth</h3>
                    <div class="request-details">Costume Design • Requested by City Theatre</div>
                    <div class="service-date">Service Date: 2024-09-01</div>
                </div>
                <div class="request-actions">
                    <span class="status-badge status-completed">completed</span>
                    <span class="price">$1200</span>
                    <button class="btn btn-completed selected" onclick="markCompleted(this)">Completed</button>
                </div>
            </div>

            <!-- Additional Pending Request -->
            <div class="request-item pending-request" data-category="pending">
                <div class="request-info">
                    <h3>A Midsummer Night's Dream</h3>
                    <div class="request-details">Stage Design • Requested by University Theatre</div>
                    <div class="service-date">Service Date: 2024-08-22</div>
                </div>
                <div class="request-actions">
                    <span class="status-badge status-pending">pending</span>
                    <span class="price">$900</span>
                    <button class="btn btn-reject" onclick="rejectRequest(this)">🗑️ Reject</button>
                    <button class="btn btn-accept" onclick="acceptRequest(this)">✓ Accept</button>
                </div>
            </div>

            <!-- Additional Accepted Request -->
            <div class="request-item accepted-request" data-category="accepted" style="display: none;">
                <div class="request-info">
                    <h3>The Tempest</h3>
                    <div class="request-details">Audio Visual • Requested by Community Arts</div>
                    <div class="service-date">Service Date: 2024-08-28</div>
                </div>
                <div class="request-actions">
                    <span class="status-badge status-accepted">accepted</span>
                    <span class="price">$650</span>
                    <button class="btn btn-update" onclick="updatePayment(this)">Update Payment</button>
                </div>
            </div>
        </div>
    </div>

    <script>
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
    </script>
    </div>
</body>
</html>
