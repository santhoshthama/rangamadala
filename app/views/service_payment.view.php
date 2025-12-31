<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Management</title>
    <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/service provider/service_payment.css">

</head>
<body>
    <div class="container">
        <div class="header">
            <div class="title-section">
                <h1>Payment Management</h1>
                <p>Track your earnings and payment status</p>
            </div>
        </div>

        <div class="payment-summary">
            <button class="summary-item pending active" onclick="filterPayments('pending')" id="pendingBtn">
                <span class="summary-amount">$2,450</span>
                <span class="summary-label">Pending</span>
            </button>
            <button class="summary-item received" onclick="filterPayments('received')" id="receivedBtn">
                <span class="summary-amount">$8,750</span>
                <span class="summary-label">Received</span>
            </button>
        </div>

        <div class="payments-list" id="paymentsList">
            <div class="payment-item" data-status="pending">
                <div class="payment-info">
                    <h3>Hamlet</h3>
                    <div class="payment-details">Lighting Setup</div>
                    <div class="payment-requester">Requested by Drama Society</div>
                </div>
                <div class="payment-actions">
                    <div class="payment-amount">$750</div>
                    <div class="payment-status-badge pending-status">Pending</div>
                </div>
            </div>

            <div class="payment-item" data-status="paid">
                <div class="payment-info">
                    <h3>Macbeth</h3>
                    <div class="payment-details">Costume Design</div>
                    <div class="payment-requester">Requested by City Theatre</div>
                </div>
                <div class="payment-actions">
                    <div class="payment-amount">$1200</div>
                    <div class="payment-status-badge paid-status">Paid</div>
                </div>
            </div>

            <div class="payment-item" data-status="pending">
                <div class="payment-info">
                    <h3>Romeo and Juliet</h3>
                    <div class="payment-details">Sound Equipment</div>
                    <div class="payment-requester">Requested by Theatre Group ABC</div>
                </div>
                <div class="payment-actions">
                    <div class="payment-amount">$500</div>
                    <div class="payment-status-badge pending-status">Pending</div>
                </div>
            </div>

            <div class="payment-item" data-status="paid">
                <div class="payment-info">
                    <h3>A Midsummer Night's Dream</h3>
                    <div class="payment-details">Stage Design</div>
                    <div class="payment-requester">Requested by University Theatre</div>
                </div>
                <div class="payment-actions">
                    <div class="payment-amount">$900</div>
                    <div class="payment-status-badge paid-status">Paid</div>
                </div>
            </div>

            <div class="payment-item" data-status="pending">
                <div class="payment-info">
                    <h3>The Tempest</h3>
                    <div class="payment-details">Audio Visual Setup</div>
                    <div class="payment-requester">Requested by Community Arts</div>
                </div>
                <div class="payment-actions">
                    <div class="payment-amount">$650</div>
                    <div class="payment-status-badge pending-status">Pending</div>
                </div>
            </div>

            <div class="payment-item" data-status="paid">
                <div class="payment-info">
                    <h3>King Lear</h3>
                    <div class="payment-details">Complete Production Setup</div>
                    <div class="payment-requester">Requested by Royal Theatre</div>
                </div>
                <div class="payment-actions">
                    <div class="payment-amount">$1500</div>
                    <div class="payment-status-badge paid-status">Paid</div>
                </div>
            </div>
        </div>

        <div class="filter-message" id="filterMessage" style="display: none;">
            No payments found for the selected filter.
        </div>
    </div>

    <!-- Payment Details Modal -->
    <div id="paymentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Payment Details</h2>
                <span class="close-modal" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Payment details will be inserted here -->
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>