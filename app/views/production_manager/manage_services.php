<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Services - Rangamadala</title>
    <link rel="stylesheet" href="/Rangamadala/public/assets/CSS/ui-theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="logo">
            <h2>🎭</h2>
        </div>
        <ul class="menu">
            <li>
                <a href="<?= ROOT ?>/production_manager/dashboard?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="active">
                <a href="<?= ROOT ?>/production_manager/manage_services?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>">
                    <i class="fas fa-briefcase"></i>
                    <span>Manage Services</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/production_manager/manage_budget?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>">
                    <i class="fas fa-chart-bar"></i>
                    <span>Budget Management</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/production_manager/book_theater?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>">
                    <i class="fas fa-theater-masks"></i>
                    <span>Theater Bookings</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/artistdashboard">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Profile</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main--content">
        <a href="<?= ROOT ?>/production_manager/dashboard?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>" class="back-button">
            <i class="fas fa-arrow-left"></i>
            Back to Dashboard
        </a>

        <!-- Header -->
        <div class="header--wrapper">
            <div class="header--title">
                <span><?= isset($drama->drama_name) ? esc($drama->drama_name) : 'Drama' ?></span>
                <h2>Service Management</h2>
            </div>
            <div class="header-controls">
                <button class="btn btn-primary" onclick="openRequestServiceModal()">
                    <i class="fas fa-plus"></i>
                    Request Service
                </button>
            </div>
        </div>

        <!-- Service Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3><?= isset($totalCount) ? $totalCount : '0' ?></h3>
                <p>Total Services Requested</p>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, var(--success), #1f9b3b);">
                <h3><?= isset($confirmedCount) ? $confirmedCount : '0' ?></h3>
                <p>Confirmed Services</p>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, var(--warning), #e0a800);">
                <h3><?= isset($pendingCount) ? $pendingCount : '0' ?></h3>
                <p>Pending Responses</p>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, var(--info), #138496);">
                <h3>-</h3>
                <p>Estimated Service Costs</p>
            </div>
        </div>

        <!-- Services List -->
        <div class="content" style="padding: 28px;">
            <h3 style="margin-bottom: 16px;">Service Requests</h3>
            
            <?php if (isset($services) && is_array($services) && !empty($services)): ?>
                <?php foreach ($services as $service): ?>
                    <?php if (!is_object($service)) continue; ?>
                    <?php 
                        $statusClass = 'pending';
                        $statusText = 'Pending';
                        $bgColor = '#fffbf0';
                        $borderColor = 'var(--warning)';
                        
                        if (isset($service->status)) {
                            if ($service->status === 'accepted') {
                                $statusClass = 'assigned';
                                $statusText = 'Confirmed';
                                $bgColor = '#f0f7f4';
                                $borderColor = 'var(--success)';
                            } elseif ($service->status === 'rejected') {
                                $statusClass = 'rejected';
                                $statusText = 'Rejected';
                                $bgColor = '#fef0f0';
                                $borderColor = 'var(--danger)';
                            }
                        }
                    ?>
                    <div class="card-section" style="margin-bottom: 16px; background: <?= $bgColor ?>; border-left-color: <?= $borderColor ?>;"><div style="display: flex; justify-content: space-between; align-items: flex-start;">
                            <div style="flex: 1;">
                                <h3 style="color: var(--ink); margin-bottom: 8px;">
                                    <i class="fas fa-briefcase" style="color: <?= $borderColor ?>; margin-right: 8px;"></i>
                                    <?= isset($service->service_required) ? esc($service->service_required) : 'Service' ?>
                                </h3>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 12px;">
                                    <div>
                                        <p style="font-size: 12px; color: var(--muted); text-transform: uppercase; font-weight: 700; margin-bottom: 4px;">Requester</p>
                                        <p style="color: var(--ink); font-weight: 600;"><?= isset($service->requester_name) ? esc($service->requester_name) : 'N/A' ?></p>
                                    </div>
                                    <div>
                                        <p style="font-size: 12px; color: var(--muted); text-transform: uppercase; font-weight: 700; margin-bottom: 4px;">Contact</p>
                                        <p style="color: var(--ink); font-weight: 600;"><?= isset($service->requester_phone) ? esc($service->requester_phone) : 'N/A' ?></p>
                                    </div>
                                    <div>
                                        <p style="font-size: 12px; color: var(--muted); text-transform: uppercase; font-weight: 700; margin-bottom: 4px;">Service Date</p>
                                        <p style="color: var(--ink); font-weight: 600;"><?= isset($service->start_date) ? date('M d, Y', strtotime($service->start_date)) : 'N/A' ?></p>
                                    </div>
                                    <div>
                                        <p style="font-size: 12px; color: var(--muted); text-transform: uppercase; font-weight: 700; margin-bottom: 4px;">Status</p>
                                        <span class="status-badge <?= $statusClass ?>"><?= $statusText ?></span>
                                    </div>
                                </div>
                            </div>
                            <div style="display: flex; gap: 8px; flex-direction: column;">
                                <button class="btn btn-secondary" style="padding: 8px 12px; font-size: 12px;" onclick="viewServiceDetails(<?= isset($service->id) ? $service->id : 'null' ?>)">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-danger" style="padding: 8px 12px; font-size: 12px;" onclick="cancelService(<?= isset($service->id) ? $service->id : 'null' ?>)">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="text-align: center; padding: 60px 30px; color: var(--muted);">
                    <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
                    <p>No service requests yet. Start by requesting a service from available providers.</p>
                    <button class="btn btn-primary" style="margin-top: 20px;" onclick="openRequestServiceModal()">
                        <i class="fas fa-plus"></i> Request Service
                    </button>
                </div>
            <?php endif; ?>
    </main>

    <!-- Request Service Modal -->
    <div id="requestServiceModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeRequestServiceModal()">&times;</span>
            <h2><i class="fas fa-plus"></i> Request New Service</h2>
            
            <div class="form-group">
                <label for="serviceType">Service Type</label>
                <select id="serviceType" onchange="updateServiceProviders()">
                    <option value="">Select Service Type</option>
                    <option value="sound">Sound & Audio</option>
                    <option value="lighting">Lighting & Effects</option>
                    <option value="makeup">Makeup & Costume</option>
                    <option value="transport">Transportation</option>
                    <option value="catering">Catering & Refreshments</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <div class="form-group">
                <label for="serviceProvider">Service Provider</label>
                <select id="serviceProvider">
                    <option value="">Select Service Provider</option>
                </select>
            </div>

            <div class="form-group">
                <label for="serviceDate">Service Date</label>
                <input type="date" id="serviceDate">
            </div>

            <div class="form-group">
                <label for="serviceDescription">Description</label>
                <textarea id="serviceDescription" placeholder="Describe the service requirements and specifications"></textarea>
            </div>

            <div class="form-group">
                <label for="estimatedBudget">Estimated Budget (LKR)</label>
                <input type="number" id="estimatedBudget" placeholder="Enter estimated amount" min="0" step="1000">
            </div>

            <div class="form-group">
                <label for="specialRequirements">Special Requirements</label>
                <textarea id="specialRequirements" placeholder="Any special needs or requirements for this service" style="min-height: 80px;"></textarea>
            </div>

            <div style="background: var(--brand-soft); border-left: 4px solid var(--brand); padding: 14px; border-radius: 8px; margin-bottom: 16px;">
                <p style="color: var(--ink); font-size: 12px; margin: 0;">
                    <i class="fas fa-info-circle" style="color: var(--brand); margin-right: 6px;"></i>
                    <strong>Note:</strong> After the service provider accepts your request, you'll be able to make payment via card. The cost will then be added to your drama budget.
                </p>
            </div>

            <div class="modal-actions">
                <button class="btn btn-secondary" onclick="closeRequestServiceModal()">Cancel</button>
                <button class="btn btn-primary" onclick="submitServiceRequest()">Submit Request</button>
            </div>
        </div>
    </div>

    <!-- Card Payment Modal (Online Payment) -->
    <div id="cardPaymentModal" class="modal">
        <div class="modal-content" style="max-width: 500px;">
            <span class="close" onclick="closeCardPaymentModal()">&times;</span>
            <h2><i class="fas fa-credit-card"></i> Online Card Payment</h2>
            
            <div style="background: linear-gradient(135deg, var(--brand-soft) 0%, rgba(186,142,35,0.05) 100%); border-left: 4px solid var(--brand); padding: 16px; border-radius: 8px; margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span style="color: var(--ink); font-weight: 600;">Service Provider:</span>
                    <span id="paymentProviderName" style="color: var(--brand); font-weight: 700;">-</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span style="color: var(--ink); font-weight: 600;">Service Type:</span>
                    <span id="paymentServiceType" style="color: var(--ink);">-</span>
                </div>
                <div style="display: flex; justify-content: space-between; border-top: 1px solid rgba(186,142,35,0.2); padding-top: 12px; margin-top: 12px;">
                    <span style="color: var(--ink); font-weight: 700; font-size: 16px;">Amount to Pay:</span>
                    <span id="paymentAmount" style="color: var(--brand); font-weight: 700; font-size: 18px;">LKR 0</span>
                </div>
            </div>

            <h3 style="margin-bottom: 14px; font-size: 14px; color: var(--ink);">Card Details:</h3>

            <div class="form-group">
                <label for="cardHolderName">Cardholder Name</label>
                <input type="text" id="cardHolderName" placeholder="Full name on card">
            </div>

            <div class="form-group">
                <label for="cardNumber">Card Number</label>
                <input type="text" id="cardNumber" placeholder="1234 5678 9012 3456" maxlength="19">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <div class="form-group">
                    <label for="expiryDate">Expiry Date</label>
                    <input type="text" id="expiryDate" placeholder="MM/YY" maxlength="5">
                </div>
                <div class="form-group">
                    <label for="cvv">CVV</label>
                    <input type="text" id="cvv" placeholder="***" maxlength="4">
                </div>
            </div>

            <div style="background: #fff3cd; border-left: 4px solid var(--warning); padding: 12px; border-radius: 8px; margin-bottom: 16px;">
                <p style="color: #856404; font-size: 12px; margin: 0;">
                    <i class="fas fa-shield-alt" style="margin-right: 6px;"></i>
                    Your payment is secure and encrypted. Card details are not stored.
                </p>
            </div>

            <div class="modal-actions">
                <button class="btn btn-secondary" onclick="closeCardPaymentModal()">Cancel</button>
                <button class="btn btn-primary" onclick="processCardPayment()">
                    <i class="fas fa-lock"></i>
                    Pay & Add to Budget
                </button>
            </div>
        </div>
    </div>

    <!-- Service Details Modal -->
    <div id="serviceDetailsModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeServiceDetailsModal()">&times;</span>
            <h2 id="serviceDetailsTitle">Service Details</h2>
            
            <div id="serviceDetailsContent"></div>
            
            <div class="modal-actions">
                <button class="btn btn-secondary" onclick="closeServiceDetailsModal()">Close</button>
            </div>
        </div>
    </div>

    <script src="/Rangamadala/public/assets/JS/manage-services.js"></script>
</body>
</html>
