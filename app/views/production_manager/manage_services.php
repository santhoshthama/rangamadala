<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Services - Rangamadala</title>
    <link rel="stylesheet" href="/Rangamadala/public/assets/CSS/ui-theme.css">
    <link rel="stylesheet" href="/Rangamadala/public/assets/CSS/production_manager/manage_services.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <?php $dramaId = isset($drama->id) ? (int)$drama->id : (int)($_GET['drama_id'] ?? 1); ?>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="logo">
            <h2>🎭</h2>
        </div>
        <ul class="menu">
            <li>
                <a href="<?= ROOT ?>/production_manager/dashboard?drama_id=<?= $dramaId ?>">
                    <i class="bx bx-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="active">
                <a href="<?= ROOT ?>/production_manager/manage_services?drama_id=<?= $dramaId ?>">
                    <i class="bx bx-briefcase"></i>
                    <span>Manage Services</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/production_manager/manage_budget?drama_id=<?= $dramaId ?>">
                    <i class="bx bx-chart-bar"></i>
                    <span>Budget Management</span>
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
        <a href="<?= ROOT ?>/production_manager/dashboard?drama_id=<?= $dramaId ?>" class="back-button">
            <i class="bx bx-arrow-left"></i>
            Back to Dashboard
        </a>

        <?php 
            $serviceMissing = isset($_GET['service_missing']);
            $prefillService = isset($_GET['prefill_service']) ? $_GET['prefill_service'] : '';
            $showAddModal = isset($_GET['show_add_modal']);
            $returnUrl = isset($_GET['return_url']) ? $_GET['return_url'] : '';
        ?>

        <!-- Header -->
        <div class="header--wrapper">
            <div class="header--title">
                <span><?= isset($drama->drama_name) ? esc($drama->drama_name) : 'Drama' ?></span>
                <h2>Service Management</h2>
            </div>
            <div class="header-controls">
                <a class="btn btn-primary" href="<?= ROOT ?>/BrowseServiceProviders?drama_id=<?= isset($drama->id) ? $drama->id : ($_GET['drama_id'] ?? 0) ?>">
                    <i class="bx bx-plus"></i>
                    Browse Service
                </a>
                <button type="button" class="btn btn-secondary" onclick="openAddServiceModal()">
                    <i class="bx bx-plus-circle"></i>
                    Add Service
                </button>
            </div>
        </div>

        <?php if ($serviceMissing): ?>
            <div class="service-alert service-alert-warning">
                <strong>Service should be add before request.</strong>
                <span>Select the service type below and add it to continue.</span>
            </div>
        <?php endif; ?>

        <!-- Service Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3><?= isset($totalCount) ? $totalCount : '0' ?></h3>
                <p>Total Services Requested</p>
            </div>
            <div class="stat-card stat-card--confirmed">
                <h3><?= isset($confirmedCount) ? $confirmedCount : '0' ?></h3>
                <p>Confirmed Services</p>
            </div>
            <div class="stat-card stat-card--pending">
                <h3><?= isset($pendingCount) ? $pendingCount : '0' ?></h3>
                <p>Pending Responses</p>
            </div>
            <div class="stat-card stat-card--estimated">
                <h3>-</h3>
                <p>Estimated Service Costs</p>
            </div>
        </div>

        <!-- Services List -->
        <div class="requests-list">

            <?php 
                // Check if we have either service requests or drama services to display
                $hasServiceRequests = isset($services) && is_array($services) && !empty($services);
                $hasDramaServices = isset($dramaServices) && is_array($dramaServices) && !empty($dramaServices);
                $hasAnyServices = $hasServiceRequests || $hasDramaServices;
            ?>

            <?php if ($hasAnyServices): ?>
                <?php
                    // Group service requests by service_type
                    $grouped = [];
                    if ($hasServiceRequests) {
                        foreach ($services as $srv) {
                            if (!is_object($srv)) { continue; }
                            $typeKey = isset($srv->service_type) && $srv->service_type !== '' ? htmlspecialchars($srv->service_type) : 'Other';
                            if (!isset($grouped[$typeKey])) { $grouped[$typeKey] = []; }
                            $grouped[$typeKey][] = $srv;
                        }
                    }

                    // Add DB-defined drama services to grouped cards
                    if (isset($dramaServices) && is_array($dramaServices)) {
                        foreach ($dramaServices as $dramaSvc) {
                            $key = htmlspecialchars($dramaSvc->service_type);
                            if (!isset($grouped[$key])) {
                                $grouped[$key] = [];
                            }
                        }
                    }

                    // Build meta map from DB
                    $serviceMetaMap = [];
                    if (isset($dramaServices) && is_array($dramaServices)) {
                        foreach ($dramaServices as $dramaSvc) {
                            $serviceMetaMap[$dramaSvc->service_type] = [
                                'budget' => $dramaSvc->budget,
                                'description' => $dramaSvc->description,
                            ];
                        }
                    }

                    $dramaId = isset($drama->id) ? (int)$drama->id : (int)($_GET['drama_id'] ?? 0);
                    $allTypes = [
                        'Theater Production',
                        'Lighting Design',
                        'Sound Systems',
                        'Video Production',
                        'Set Design',
                        'Costume Design',
                        'Other',
                        'Makeup & Hair',
                    ];
                ?>

                <?php foreach ($grouped as $type => $items): ?>
                    <div class="service-group-card">
                        <?php $rawType = html_entity_decode($type, ENT_QUOTES, 'UTF-8'); $canRemove = in_array($rawType, array_map(function($s){ return $s->service_type; }, $dramaServices ?? [])); ?>
                        <div class="service-group-card__header">
                            <h3 class="service-group-card__title"><?= htmlspecialchars($type) ?></h3>
                            <div class="service-group-card__actions">
                                <span class="service-group-card__count"><?= count($items) ?> request(s)</span>
                                <a class="btn btn-primary service-group-card__browse-button" href="<?= ROOT ?>/BrowseServiceProviders?drama_id=<?= (int)$dramaId ?>&service_type=<?= urlencode($rawType) ?>">
                                    <i class="fas fa-search"></i> Browse Service
                                </a>
                                <?php if ($canRemove): ?>
                                    <form method="POST" action="<?= ROOT ?>/production_manager/save_required_services?drama_id=<?= (int)$dramaId ?>" class="service-group-card__remove-form">
                                        <input type="hidden" name="remove_service_type" value="<?= htmlspecialchars($rawType) ?>">
                                        <button type="submit" class="btn btn-secondary service-group-card__remove-button">
                                            <i class="fas fa-trash"></i> Remove
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if (isset($serviceMetaMap[$rawType])): $meta = $serviceMetaMap[$rawType]; ?>
                            <div class="service-group-card__meta">
                                <?php if (!empty($meta['budget'])): ?>
                                    <div><strong>Budget:</strong> Rs <?= htmlspecialchars($meta['budget']) ?></div>
                                <?php endif; ?>
                                <?php if (!empty($meta['description'])): ?>
                                    <div><strong>Description:</strong> <?= htmlspecialchars($meta['description']) ?></div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <div class="service-group-card__body">
                            <?php foreach ($items as $service): ?>
                                <?php 
                                    $status = isset($service->status) ? strtolower($service->status) : 'pending';
                                    $statusText = ucfirst($status);
                                    $statusClass = 'status-badge status-' . htmlspecialchars($status);
                                    $hideGenericBadge = false;
                                    
                                    // Check if payment date is overdue for provider_responded status
                                    if ($status === 'provider_responded') {
                                        $serviceDetails = $service->service_details_json ? json_decode($service->service_details_json, true) : [];
                                        $providerResponse = $serviceDetails['provider_response'] ?? [];
                                        $advanceDueDate = $providerResponse['advance_due_date'] ?? null;
                                        $needsAdvance = $providerResponse['needs_advance'] === true || $providerResponse['needs_advance'] === 'true' || $providerResponse['needs_advance'] === 1;
                                        
                                        if ($advanceDueDate && $needsAdvance) {
                                            $dueDate = new DateTime($advanceDueDate);
                                            $today = new DateTime();
                                            $today->setTime(0, 0, 0);
                                            $dueDate->setTime(0, 0, 0);
                                            
                                            if ($dueDate < $today) {
                                                $statusText = 'Payment date overdue';
                                                $statusClass .= ' status-overdue';
                                            }
                                        }
                                    }
                                    
                                    // Hide generic badge for completed with payment pending confirmation
                                    if ($status === 'completed') {
                                        $paymentGateway = $service->payment_gateway ?? '';
                                        $advancePaymentStatus = strtolower($service->advance_payment_status ?? '');
                                        $hasPendingCashBankPayment = ($paymentGateway === 'cash' || $paymentGateway === 'bank_transfer') && $advancePaymentStatus === 'pending';
                                        
                                        if ($hasPendingCashBankPayment) {
                                            $hideGenericBadge = true;
                                        }
                                    }
                                    
                                    // Always hide generic badge for completed_paid (uses custom badge)
                                    if ($status === 'completed_paid') {
                                        $hideGenericBadge = true;
                                    }
                                    
                                    $budget = isset($service->budget) && $service->budget !== null ? number_format((float)$service->budget, 2) : null;
                                    $dateLabel = '';
                                    if (!empty($service->service_date)) {
                                        $dateLabel = 'Service Date: ' . htmlspecialchars($service->service_date);
                                    } elseif (!empty($service->start_date) || !empty($service->end_date)) {
                                        $dateLabel = 'Schedule: ' . htmlspecialchars($service->start_date) . ' to ' . htmlspecialchars($service->end_date);
                                    }
                                    $provider = isset($service->provider_name) ? htmlspecialchars($service->provider_name) : 'Provider';
                                    $title = $provider;
                                ?>
                                <div class="request-item" data-category="<?= htmlspecialchars($status) ?>">
                                    <div class="request-info">
                                        <h3><?= $title ?></h3>
                                        <?php if ($dateLabel): ?><div class="service-date"><?= $dateLabel ?></div><?php endif; ?>
                                        <?php if (!empty($service->created_at)): ?><div class="request-date" style="font-size: 12px; color: #999; margin-top: 4px;">Requested on <?= date('M d, Y', strtotime($service->created_at)) ?></div><?php endif; ?>
                                        <?php if (!empty($service->service_required)): ?><div class="request-snippet" style="margin-top: 8px; font-size: 13px; color: #555; line-height: 1.4;"><?= htmlspecialchars(substr($service->service_required, 0, 100)) ?><?= strlen($service->service_required) > 100 ? '...' : '' ?></div><?php endif; ?>
                                    </div>
                                    <div class="request-actions">
                                        <?php if (!$hideGenericBadge): ?><span class="<?= $statusClass ?>"><?= htmlspecialchars($statusText) ?></span><?php endif; ?>
                                        <?php if ($budget !== null): ?><span class="price">Rs <?= $budget ?></span><?php endif; ?>
                                        
                                        <?php if ($status === 'provider_responded'): ?>
                                            <?php
                                                $serviceDetails = $service->service_details_json ? json_decode($service->service_details_json, true) : [];
                                                $providerResponse = $serviceDetails['provider_response'] ?? [];
                                            ?>
                                            <button class="btn-details" onclick="openConfirmModal(<?= (int)$service->id ?>, <?= htmlspecialchars(json_encode($providerResponse), ENT_QUOTES, 'UTF-8') ?>)">
                                                Review & Confirm
                                            </button>
                                            <button class="btn-details" onclick="openPMRequestDetails(event, <?= htmlspecialchars(json_encode((array)$service), ENT_QUOTES, 'UTF-8') ?>)">
                                                View Details
                                            </button>
                                        <?php elseif ($status === 'confirmed'): ?>
                                            <button class="btn-details" onclick="openPMRequestDetails(event, <?= htmlspecialchars(json_encode((array)$service), ENT_QUOTES, 'UTF-8') ?>)">
                                                View Details
                                            </button>
                                            <div class="service-group-card__status-row">⏱️ Awaiting Provider Acceptance</div>
                                        <?php elseif ($status === 'accepted'): ?>
                                            <button class="btn-details" onclick="openPMRequestDetails(event, <?= htmlspecialchars(json_encode((array)$service), ENT_QUOTES, 'UTF-8') ?>)">
                                                View Details
                                            </button>
                                            <div class="service-group-card__status-row service-group-card__status-row--success">🟢 In Progress</div>
                                        <?php elseif ($status === 'completed'): ?>
                                            <?php
                                                $serviceDetailsForPayment = $service->service_details_json ? json_decode($service->service_details_json, true) : [];
                                                $providerResponseForPayment = $serviceDetailsForPayment['provider_response'] ?? [];
                                                $quoteAmount = (float)($providerResponseForPayment['quote_amount'] ?? ($service->budget ?? 0));
                                                $advanceAmount = (float)($providerResponseForPayment['advance_amount'] ?? 0);
                                                $paymentStatus = strtolower($service->calculated_payment_status ?? 'unpaid');
                                                $remainingAmount = max(0, $quoteAmount - $advanceAmount);
                                                
                                                // Check if payment is pending for cash/bank
                                                $paymentGateway = $service->payment_gateway ?? '';
                                                $advancePaymentStatus = strtolower($service->advance_payment_status ?? '');
                                                $hasPendingCashBankPayment = ($paymentGateway === 'cash' || $paymentGateway === 'bank_transfer') && $advancePaymentStatus === 'pending';
                                                $providerVerificationRejected = false;
                                                $providerVerificationReason = '';
                                                if (!empty($service->transaction_response)) {
                                                    $transactionData = json_decode($service->transaction_response, true);
                                                    if (is_array($transactionData) && (($transactionData['provider_verification_status'] ?? '') === 'rejected')) {
                                                        $providerVerificationRejected = true;
                                                        $providerVerificationReason = trim((string)($transactionData['provider_verification_reason'] ?? ''));
                                                    }
                                                }
                                            ?>
                                            <?php if ($hasPendingCashBankPayment): ?>
                                                <?php if ($providerVerificationRejected): ?>
                                                    <span class="status-badge status-verification-failed">Verification Failed</span>
                                                <?php else: ?>
                                                    <span class="status-badge status-fully-paid">Fully Paid</span>
                                                <?php endif; ?>
                                                <button class="btn-details" onclick="openPMRequestDetails(event, <?= htmlspecialchars(json_encode((array)$service), ENT_QUOTES, 'UTF-8') ?>)">
                                                    View Details
                                                </button>
                                                <?php if ($providerVerificationRejected): ?>
                                                    <button class="btn-details btn-details--danger" 
                                                        onclick="window.location.href='<?= ROOT ?>/Payment/checkout?request_id=<?= (int)$service->id ?>&amount=<?= urlencode(number_format($remainingAmount, 2, '.', '')) ?>&type=remaining'">
                                                        Re-submit Payment
                                                    </button>
                                                    <div class="service-group-card__status-row service-group-card__status-row--danger">
                                                        ⚠️ Provider could not verify<?= $providerVerificationReason !== '' ? ': ' . htmlspecialchars($providerVerificationReason) : '' ?>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="service-group-card__status-row">⏳ Awaiting provider's payment confirmation</div>
                                                <?php endif; ?>
                                            <?php elseif ($paymentStatus === 'partially_paid' && $remainingAmount > 0): ?>
                                                <button class="btn-details btn-details--success" onclick="window.location.href='<?= ROOT ?>/Payment/checkout?request_id=<?= (int)$service->id ?>&amount=<?= number_format($remainingAmount, 2, '.', '') ?>&type=remaining'">
                                                    Pay Remaining (Rs <?= number_format($remainingAmount, 2) ?>)
                                                </button>
                                                <button class="btn-details" onclick="openPMRequestDetails(event, <?= htmlspecialchars(json_encode((array)$service), ENT_QUOTES, 'UTF-8') ?>)">
                                                    View Details
                                                </button>
                                            <?php else: ?>
                                                <button class="btn-details btn-details--success" onclick="window.location.href='<?= ROOT ?>/Payment/checkout?request_id=<?= (int)$service->id ?>&amount=<?= number_format($quoteAmount, 2, '.', '') ?>&type=full'">
                                                    Pay Full Amount (Rs <?= number_format($quoteAmount, 2) ?>)
                                                </button>
                                                <button class="btn-details" onclick="openPMRequestDetails(event, <?= htmlspecialchars(json_encode((array)$service), ENT_QUOTES, 'UTF-8') ?>)">
                                                    View Details
                                                </button>
                                            <?php endif; ?>
                                        <?php elseif ($status === 'completed_paid'): ?>
                                            <span class="status-badge status-completed_fully_paid">Completed and Fully paid</span>
                                            <button class="btn-details" onclick="openPMRequestDetails(event, <?= htmlspecialchars(json_encode((array)$service), ENT_QUOTES, 'UTF-8') ?>)">
                                                View Details
                                            </button>
                                        <?php else: ?>
                                            <button class="btn-details" onclick="openPMRequestDetails(event, <?= htmlspecialchars(json_encode((array)$service), ENT_QUOTES, 'UTF-8') ?>)">View Details</button>
                                        <?php endif; ?>
                                        
                                        <?php if (in_array($status, ['pending'])): ?>
                                            <button class="btn-reject" onclick="cancelServiceRequest(this)" data-id="<?= (int)$service->id ?>">Cancel</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="service-empty-state">
                    <i class="fas fa-inbox service-empty-state__icon"></i>
                    <p class="service-empty-state__text">No service requests yet. Start by adding service and request service by existing providers.</p>
                    <div class="service-empty-state__actions">
                        <button type="button" class="btn btn-secondary service-empty-state__button" onclick="openAddServiceModal()">
                            <i class="fas fa-plus-circle"></i> Add Service Type
                        </button>
                        <a class="btn btn-primary service-empty-state__link" href="<?= ROOT ?>/BrowseServiceProviders?drama_id=<?= isset($drama->id) ? $drama->id : ($_GET['drama_id'] ?? 0) ?>">
                            <i class="fas fa-search"></i> Browse Service
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Request Details Modal -->
    <div id="detailsModal" class="modal-overlay">
        <div class="modal-dialog modal-dialog--details" id="detailsContent">
        </div>
    </div>

    <!-- Add Service Modal -->
    <div id="addServiceModal" class="modal-overlay modal-overlay--add">
        <div class="modal-dialog modal-dialog--add">
            <div class="modal-header">
                <h3 class="modal-title">Add Service Type</h3>
                <button type="button" onclick="closeAddServiceModal()" class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <?php
                    $allTypes = [
                        'Theater Production',
                        'Lighting Design',
                        'Sound Systems',
                        'Video Production',
                        'Set Design',
                        'Costume Design',
                        'Other',
                        'Makeup & Hair',
                    ];
                    $existingServices = isset($dramaServices) ? array_map(function($s){ return $s->service_type; }, $dramaServices) : [];
                    $dramaId = isset($drama->id) ? (int)$drama->id : (int)($_GET['drama_id'] ?? 0);
                ?>
                <form method="POST" action="<?= ROOT ?>/production_manager/save_required_services?drama_id=<?= $dramaId ?>" class="form-stack">
                    <?php if (!empty($returnUrl)): ?>
                        <input type="hidden" name="return_url" value="<?= htmlspecialchars($returnUrl) ?>">
                    <?php endif; ?>
                    <label class="form-stack__label">Select service types to add</label>
                    <div class="form-grid">
                        <?php foreach ($allTypes as $t): 
                            $isExisting = in_array($t, $existingServices);
                        ?>
                            <label class="form-option <?= $isExisting ? 'form-option--disabled' : '' ?>">
                                <input type="checkbox" name="required_services[]" value="<?= htmlspecialchars($t) ?>" <?= $isExisting ? 'checked disabled' : '' ?>>
                                <span><?= htmlspecialchars($t) ?><?= $isExisting ? ' (added)' : '' ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <div class="form-field form-field--mt10">
                        <label class="form-stack__label">Budget (optional)</label>
                        <input type="text" name="service_budget" placeholder="Enter budget" class="form-input">
                    </div>
                    <div class="form-field">
                        <label class="form-stack__label">Description (optional)</label>
                        <textarea name="service_description" rows="3" placeholder="Add a short description" class="form-textarea"></textarea>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn modal-button modal-button--secondary" onclick="closeAddServiceModal()">Cancel</button>
                        <button type="submit" class="btn btn-primary modal-button modal-button--primary"><i class="fas fa-plus"></i> Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openAddServiceModal(){
            var m = document.getElementById('addServiceModal');
            if (m){ m.style.display = 'flex'; }
        }
        function closeAddServiceModal(){
            var m = document.getElementById('addServiceModal');
            if (m){ m.style.display = 'none'; }
        }
        // Close on outside click
        document.addEventListener('click', function(e){
            var m = document.getElementById('addServiceModal');
            if (!m || m.style.display === 'none') return;
            if (e.target === m) { closeAddServiceModal(); }
        });

        // Auto-open add service modal if redirected for missing service
        (function(){
            var shouldOpen = <?= $showAddModal ? 'true' : 'false' ?>;
            var prefill = <?= json_encode($prefillService) ?>;
            if (shouldOpen) {
                openAddServiceModal();
                if (prefill) {
                    var selector = 'input[type="checkbox"][name="required_services[]"][value="' + prefill.replace(/"/g,'\\"') + '"]';
                    var cb = document.querySelector(selector);
                    if (cb && !cb.disabled) {
                        cb.checked = true;
                    }
                }
            }
        })();

        // Open request details modal
        function openRequestDetailsFromButton(button) {
            const requestData = JSON.parse(button.getAttribute('data-request'));
            const detailsContent = document.getElementById('detailsContent');
            
            let html = '<div class="details-summary">';
            html += '<h3 class="details-summary__title">Request Details</h3>';
            
            html += '<div class="details-summary__box">';
            
            if (requestData.provider_name) {
                html += '<div class="details-summary__row"><label class="details-summary__label">Provider Name</label><div class="details-summary__value">' + (requestData.provider_name || '-') + '</div></div>';
            }
            
            if (requestData.service_required) {
                html += '<div class="details-summary__row"><label class="details-summary__label">Service Required</label><div class="details-summary__value">' + (requestData.service_required || '-') + '</div></div>';
            }
            
            if (requestData.budget) {
                html += '<div class="details-summary__row"><label class="details-summary__label">Budget</label><div class="details-summary__value">Rs ' + (requestData.budget || '-') + '</div></div>';
            }
            
            if (requestData.service_date) {
                html += '<div class="details-summary__row"><label class="details-summary__label">Service Date</label><div class="details-summary__value">' + (requestData.service_date || '-') + '</div></div>';
            }
            
            if (requestData.start_date || requestData.end_date) {
                html += '<div class="details-summary__row"><label class="details-summary__label">Duration</label><div class="details-summary__value">' + (requestData.start_date || '-') + ' to ' + (requestData.end_date || '-') + '</div></div>';
            }
            
            if (requestData.status) {
                html += '<div class="details-summary__row"><label class="details-summary__label">Status</label><div class="details-summary__value">' + (requestData.status || '-') + '</div></div>';
            }
            
            html += '</div>';
            html += '<div class="details-summary__footer">';
            html += '<button onclick="closeDetailsModal()" class="details-summary__close">Close</button>';
            html += '</div>';
            html += '</div>';
            
            detailsContent.innerHTML = html;
            document.getElementById('detailsModal').style.display = 'flex';
        }

        function closeDetailsModal() {
            document.getElementById('detailsModal').style.display = 'none';
        }

        async function parseJsonResponse(res) {
            const raw = await res.text();
            try {
                return JSON.parse(raw);
            } catch (e) {
                const preview = (raw || '').replace(/\s+/g, ' ').trim().slice(0, 180);
                throw new Error(preview || 'Invalid server response');
            }
        }

        function cancelServiceRequest(button) {
            const requestId = button.getAttribute('data-id');
            if (!requestId) {
                showMessage('Invalid request', 'error');
                return;
            }

            if (!confirm('Cancel this pending request?')) {
                return;
            }

            fetch('<?= ROOT ?>/Production_manager/cancelServiceRequest', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ id: requestId })
            })
            .then(parseJsonResponse)
            .then(json => {
                if (json.success) {
                    showMessage('Request cancelled successfully', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showMessage(json.error || 'Failed to cancel request', 'error');
                }
            })
            .catch(e => showMessage('Network error: ' + e.message, 'error'));
        }

        // Close details modal on outside click
        document.addEventListener('click', function(e) {
            const modal = document.getElementById('detailsModal');
            if (e.target === modal) {
                closeDetailsModal();
            }
        });
    </script>

    <!-- Provider Response View -->
    <div id="confirmModal" class="modal-overlay modal-overlay--confirm">
        <div class="modal-dialog modal-dialog--confirm">
            <div class="modal-header modal-header--confirm">
                <h3 class="modal-title">Provider Response</h3>
                <button onclick="closeConfirmModal()" class="modal-close">&times;</button>
            </div>
            <div class="modal-dialog--confirm-content">
                <div class="modal-section">
                    <h4 class="modal-section__title">Quotation Details</h4>
                    
                    <div class="modal-field">
                        <label class="modal-label">Quotation Amount</label>
                        <div class="modal-value modal-value--strong">Rs <span id="review_quote_amount">-</span></div>
                    </div>

                    <div class="modal-field">
                        <label class="modal-label">Advance Payment Required</label>
                        <div class="modal-value modal-value--strong"><span id="review_advance_status">No</span></div>
                    </div>

                    <div id="advanceDetailsRow" class="modal-section modal-section--subtle" style="display: none;">
                        <div class="modal-section__title" style="padding-bottom: 8px; border-bottom: 1px solid #e5e7eb; margin-bottom: 12px;">Advance Payment Details</div>
                        <div class="modal-field">
                            <label class="modal-label">Advance Amount</label>
                            <div class="modal-value modal-value--strong">Rs <span id="review_advance_amount">-</span></div>
                        </div>
                        <div class="modal-field">
                            <label class="modal-label">Advance Due Date</label>
                            <div class="modal-value modal-value--strong"><span id="review_advance_due_date">-</span></div>
                        </div>
                    </div>

                    <div class="modal-field">
                        <label class="modal-label">Final Payment Due Date</label>
                        <div class="modal-value modal-value--strong"><span id="review_final_payment_due">-</span></div>
                    </div>

                    <div id="providerNoteRow" class="modal-section modal-section--note" style="display: none;">
                        <label class="modal-label" style="margin-bottom: 6px;">Provider Notes</label>
                        <div class="modal-value modal-value--muted" id="review_provider_note"></div>
                    </div>
                </div>

                <input type="hidden" id="confirm_request_id">
                <input type="hidden" id="confirm_advance_amount">
                <input type="hidden" id="confirm_needs_advance">

                <!-- Note about advance payment (informational only) -->
                <div id="advanceInfoSection" class="modal-section modal-section--success" style="display: none;">
                    <p class="modal-info">
                        <strong>💳 Payment Required:</strong> After confirming, you'll be redirected to pay the advance amount of <strong>Rs <span id="advance_info_amount">0</span></strong>.
                    </p>
                </div>

                <!-- Action Buttons (single set) -->
                <div class="modal-actions modal-actions--confirm">
                    <button onclick="closeConfirmModal()" class="modal-button modal-button--secondary">Close</button>
                    <button onclick="rejectProviderResponse()" class="modal-button modal-button--danger">
                        Reject
                    </button>
                    <button id="confirmBtn" onclick="acceptProviderResponse()" class="modal-button modal-button--primary">
                        Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const CONFIRM_ENDPOINTS = {
            confirm: '<?= ROOT ?>/Production_manager/confirmProviderResponse',
            reject: '<?= ROOT ?>/Production_manager/rejectProviderResponse'
        };

        function openConfirmModal(requestId, providerResponse) {
            document.getElementById('confirm_request_id').value = requestId;
            
            document.getElementById('review_quote_amount').textContent = providerResponse.quote_amount || '-';
            
            const needsAdvance = providerResponse.needs_advance === true || providerResponse.needs_advance === 'true' || providerResponse.needs_advance === 1;
            const advanceAmount = providerResponse.advance_amount || 0;
            
            document.getElementById('confirm_needs_advance').value = needsAdvance ? '1' : '0';
            document.getElementById('confirm_advance_amount').value = advanceAmount;
            
            if (needsAdvance) {
                document.getElementById('review_advance_status').textContent = 'Required';
                document.getElementById('advanceDetailsRow').style.display = 'block';
                document.getElementById('review_advance_amount').textContent = advanceAmount;
                document.getElementById('review_advance_due_date').textContent = providerResponse.advance_due_date || '-';
                
                // Show informational section about advance payment
                document.getElementById('advanceInfoSection').style.display = 'block';
                document.getElementById('advance_info_amount').textContent = advanceAmount;
            } else {
                document.getElementById('review_advance_status').textContent = 'Not Required';
                document.getElementById('advanceDetailsRow').style.display = 'none';
                document.getElementById('advanceInfoSection').style.display = 'none';
            }

            if (providerResponse.final_payment_due_date) {
                document.getElementById('review_final_payment_due').textContent = providerResponse.final_payment_due_date;
            }

            if (providerResponse.note) {
                document.getElementById('providerNoteRow').style.display = 'block';
                document.getElementById('review_provider_note').textContent = providerResponse.note;
            } else {
                document.getElementById('providerNoteRow').style.display = 'none';
            }

            // Disable confirm button if advance due date has passed
            const advanceDueDate = providerResponse.advance_due_date;
            const confirmBtn = document.getElementById('confirmBtn');

            if (advanceDueDate && needsAdvance) {
                const dueDate = new Date(advanceDueDate);
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                dueDate.setHours(0, 0, 0, 0);
                
                if (dueDate < today) {
                    confirmBtn.disabled = true;
                    confirmBtn.style.opacity = '0.5';
                    confirmBtn.style.cursor = 'not-allowed';
                    confirmBtn.title = 'Payment deadline has passed';
                } else {
                    confirmBtn.disabled = false;
                    confirmBtn.style.opacity = '1';
                    confirmBtn.style.cursor = 'pointer';
                    confirmBtn.title = '';
                }
            } else {
                confirmBtn.disabled = false;
                confirmBtn.style.opacity = '1';
                confirmBtn.style.cursor = 'pointer';
                confirmBtn.title = '';
            }

            document.getElementById('confirmModal').style.display = 'flex';
        }

        function closeConfirmModal() {
            document.getElementById('confirmModal').style.display = 'none';
        }

        function acceptProviderResponse() {
            const requestId = document.getElementById('confirm_request_id').value;
            const needsAdvance = document.getElementById('confirm_needs_advance').value === '1';
            const advanceAmount = document.getElementById('confirm_advance_amount').value;

            fetch(CONFIRM_ENDPOINTS.confirm, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ request_id: requestId })
            })
            .then(parseJsonResponse)
            .then(json => {
                if (json.success) {
                    closeConfirmModal();
                    
                    // If advance payment required, redirect to payment page
                    if (needsAdvance && advanceAmount > 0) {
                        showMessage('Redirecting to payment...', 'success');
                        setTimeout(() => {
                            window.location.href = '<?= ROOT ?>/Payment/checkout?request_id=' + requestId + '&amount=' + advanceAmount + '&type=advance';
                        }, 1000);
                    } else {
                        showMessage('Service confirmed successfully!', 'success');
                        setTimeout(() => location.reload(), 1500);
                    }
                } else {
                    showMessage(json.error || 'Failed to accept', 'error');
                }
            })
            .catch(e => showMessage('Network error: ' + e.message, 'error'));
        }

        function rejectProviderResponse() {
            const requestId = document.getElementById('confirm_request_id').value;
            const reason = prompt('Enter reason for rejecting this response:');
            if (reason === null) return;

            fetch(CONFIRM_ENDPOINTS.reject, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ request_id: requestId, reason })
            })
            .then(parseJsonResponse)
            .then(json => {
                if (json.success) {
                    showMessage('Response rejected', 'error');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showMessage(json.error || 'Failed to reject', 'error');
                }
            })
            .catch(e => showMessage('Network error: ' + e.message, 'error'));
        }

        window.onclick = function(event) {
            const confirmModal = document.getElementById('confirmModal');
            const paymentModal = document.getElementById('paymentModal');
            if (event.target === confirmModal) {
                closeConfirmModal();
            }
            if (event.target === paymentModal) {
                paymentModal.style.display = 'none';
            }
        };

        function showMessage(text, type) {
            const message = document.createElement('div');
            message.textContent = text;
            message.className = 'toast-message ' + (type === 'success' ? 'toast-message--success' : 'toast-message--error');
            
            document.body.appendChild(message);
            
            setTimeout(() => {
                document.body.removeChild(message);
            }, 3000);
        }

        function openPMRequestDetails(event, service) {
            event.stopPropagation();
            const modal = document.getElementById('pmDetailsModal');
            const serviceDetails = service.service_details_json ? JSON.parse(service.service_details_json) : {};
            const providerResponse = serviceDetails.provider_response || {};
            
            // Build payment status section
            let paymentStatusHTML = '';
            if (service.calculated_payment_status) {
                paymentStatusHTML = `<div>
                    <strong>Payment Status:</strong> <span style="padding: 5px 10px; border-radius: 4px; background: #f0f0f0; text-transform: capitalize;">${service.calculated_payment_status}</span>
                </div>`;
            }

            // Build payment details section for completed/completed_paid
            let paymentDetailsHTML = '';
            if ((service.status === 'completed' || service.status === 'completed_paid') && providerResponse.quote_amount) {
                const quoteAmount = parseFloat(providerResponse.quote_amount || 0);
                const advanceAmount = parseFloat(providerResponse.advance_amount || 0);
                const remainingAmount = service.calculated_payment_status === 'paid' ? 0 : Math.max(0, quoteAmount - advanceAmount);
                
                let paymentMethodHTML = '';
                if (service.payment_gateway) {
                    const gatewayMap = {
                        'payhere': 'PayHere (Online)',
                        'cash': 'Cash Payment',
                        'bank_transfer': 'Bank Transfer'
                    };
                    paymentMethodHTML = `<p style="margin: 5px 0;"><strong>Payment Method:</strong> ${gatewayMap[service.payment_gateway] || service.payment_gateway}</p>`;
                }
                
                let paymentDateHTML = '';
                if (service.paid_at) {
                    paymentDateHTML = `<p style="margin: 5px 0;"><strong>Payment Date:</strong> ${new Date(service.paid_at).toLocaleString()}</p>`;
                }
                
                let transactionDetailsHTML = '';
                if (service.transaction_response) {
                    try {
                        const txData = JSON.parse(service.transaction_response);
                        if (txData.received_date) {
                            transactionDetailsHTML += `<p style="margin: 5px 0;"><strong>Received Date:</strong> ${txData.received_date}</p>`;
                        }
                        if (txData.note) {
                            transactionDetailsHTML += `<p style="margin: 5px 0;"><strong>Payment Note:</strong> ${txData.note}</p>`;
                        }
                        if (txData.bank_slip_path) {
                            transactionDetailsHTML += `<p style="margin: 5px 0;"><strong>Bank Slip:</strong> <a href="<?= ROOT ?>/Payment/viewBankSlip/${service.payment_id}" target="_blank" style="color: #3b82f6;">View Bank Slip</a></p>`;
                        }
                        if (txData.order_id) {
                            transactionDetailsHTML += `<p style="margin: 5px 0;"><strong>Order ID:</strong> ${txData.order_id}</p>`;
                        }
                    } catch (e) {
                        // Invalid JSON, skip
                    }
                }
                
                let paymentStatusBadge = '';
                if (service.advance_payment_status === 'completed' || service.advance_payment_status === 'success') {
                    paymentStatusBadge = '<span style="color: #16a34a;">✓ Confirmed</span>';
                } else if (service.advance_payment_status === 'pending') {
                    paymentStatusBadge = '<span style="color: #f59e0b;">⏳ Pending Confirmation</span>';
                }
                
                paymentDetailsHTML = `
                    <div style="margin-bottom: 20px;">
                        <strong>Payment Information:</strong>
                        <div style="background: #f9f9f9; padding: 12px; border-radius: 4px; margin-top: 8px;">
                            <p style="margin: 5px 0;"><strong>Total Amount:</strong> Rs ${quoteAmount.toFixed(2)} ${paymentStatusBadge}</p>
                            ${advanceAmount > 0 ? `<p style="margin: 5px 0;"><strong>Advance Paid:</strong> Rs ${advanceAmount.toFixed(2)}</p>` : ''}
                            ${remainingAmount > 0 ? `<p style="margin: 5px 0;"><strong>Remaining Amount:</strong> Rs ${remainingAmount.toFixed(2)}</p>` : ''}
                            ${paymentMethodHTML}
                            ${paymentDateHTML}
                            ${transactionDetailsHTML}
                        </div>
                    </div>
                `;
            }

            // Build service-specific fields based on service type
            let serviceSpecificHTML = '';
            
            if (service.service_type === 'Theater Production') {
                serviceSpecificHTML = `
                    <div style="margin-bottom: 20px;">
                        <strong>Theater Production Details:</strong>
                        <div style="background: #f9f9f9; padding: 12px; border-radius: 4px; margin-top: 8px;">
                            <p style="margin: 5px 0;"><strong>Venue Type:</strong> ${service.theater_venue_type || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Stage Type:</strong> ${service.theater_stage_type || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Stage Size:</strong> ${service.theater_stage_size || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Number of Days:</strong> ${service.theater_num_days || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Time:</strong> ${service.theater_time || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Budget Range:</strong> ${service.theater_budget_range || 'N/A'}</p>
                        </div>
                    </div>
                `;
            } else if (service.service_type === 'Lighting Design') {
                serviceSpecificHTML = `
                    <div style="margin-bottom: 20px;">
                        <strong>Lighting Design Details:</strong>
                        <div style="background: #f9f9f9; padding: 12px; border-radius: 4px; margin-top: 8px;">
                            <p style="margin: 5px 0;"><strong>Lighting Services:</strong> ${service.lighting_stage_lighting || service.lighting_spotlights || service.lighting_custom_programming || service.lighting_moving_heads ? 'Services selected' : 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Number of Lights:</strong> ${service.lighting_num_lights || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Effects:</strong> ${service.lighting_effects || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Technician Needed:</strong> ${service.lighting_technician_needed || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Budget Range:</strong> ${service.lighting_budget_range || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Additional Requirements:</strong> ${service.lighting_additional_requirements || 'N/A'}</p>
                        </div>
                    </div>
                `;
            } else if (service.service_type === 'Sound Systems') {
                serviceSpecificHTML = `
                    <div style="margin-bottom: 20px;">
                        <strong>Sound Systems Details:</strong>
                        <div style="background: #f9f9f9; padding: 12px; border-radius: 4px; margin-top: 8px;">
                            <p style="margin: 5px 0;"><strong>Sound Services:</strong> ${service.sound_speakers || service.sound_microphones || service.sound_mixing_console || service.sound_recording ? 'Services selected' : 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Venue Size:</strong> ${service.sound_venue_size || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Number of Microphones:</strong> ${service.sound_num_microphones || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Recording Required:</strong> ${service.sound_recording_required || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Technician Needed:</strong> ${service.sound_technician_needed || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Budget Range:</strong> ${service.sound_budget_range || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Additional Requirements:</strong> ${service.sound_additional_requirements || 'N/A'}</p>
                        </div>
                    </div>
                `;
            } else if (service.service_type === 'Video Production') {
                serviceSpecificHTML = `
                    <div style="margin-bottom: 20px;">
                        <strong>Video Production Details:</strong>
                        <div style="background: #f9f9f9; padding: 12px; border-radius: 4px; margin-top: 8px;">
                            <p style="margin: 5px 0;"><strong>Video Services:</strong> ${service.video_filming || service.video_editing || service.video_live_streaming || service.video_photography ? 'Services selected' : 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Number of Cameras:</strong> ${service.video_num_cameras || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Duration:</strong> ${service.video_duration || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Editing Required:</strong> ${service.video_editing_required || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Budget Range:</strong> ${service.video_budget_range || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Additional Requirements:</strong> ${service.video_additional_requirements || 'N/A'}</p>
                        </div>
                    </div>
                `;
            } else if (service.service_type === 'Set Design') {
                serviceSpecificHTML = `
                    <div style="margin-bottom: 20px;">
                        <strong>Set Design Details:</strong>
                        <div style="background: #f9f9f9; padding: 12px; border-radius: 4px; margin-top: 8px;">
                            <p style="margin: 5px 0;"><strong>Set Components:</strong> ${service.set_backdrop || service.set_props || service.set_furniture || service.set_platforms ? 'Components selected' : 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Stage Size:</strong> ${service.set_stage_size || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Number of Sets:</strong> ${service.set_num_sets || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Installation Required:</strong> ${service.set_installation_required || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Budget Range:</strong> ${service.set_budget_range || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Additional Requirements:</strong> ${service.set_additional_requirements || 'N/A'}</p>
                        </div>
                    </div>
                `;
            } else if (service.service_type === 'Costume Design') {
                serviceSpecificHTML = `
                    <div style="margin-bottom: 20px;">
                        <strong>Costume Design Details:</strong>
                        <div style="background: #f9f9f9; padding: 12px; border-radius: 4px; margin-top: 8px;">
                            <p style="margin: 5px 0;"><strong>Costume Services:</strong> ${service.costume_design || service.costume_rental || service.costume_alterations ? 'Services selected' : 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Number of Costumes:</strong> ${service.costume_num_costumes || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Period/Style:</strong> ${service.costume_period_style || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Custom Design Required:</strong> ${service.costume_custom_design || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Budget Range:</strong> ${service.costume_budget_range || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Additional Requirements:</strong> ${service.costume_additional_requirements || 'N/A'}</p>
                        </div>
                    </div>
                `;
            } else if (service.service_type === 'Makeup & Hair') {
                serviceSpecificHTML = `
                    <div style="margin-bottom: 20px;">
                        <strong>Makeup & Hair Details:</strong>
                        <div style="background: #f9f9f9; padding: 12px; border-radius: 4px; margin-top: 8px;">
                            <p style="margin: 5px 0;"><strong>Services:</strong> ${service.makeup_stage || service.makeup_special_effects || service.makeup_hair_styling ? 'Services selected' : 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Number of Artists:</strong> ${service.makeup_num_artists || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Makeup Style:</strong> ${service.makeup_style || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Special Effects Required:</strong> ${service.makeup_special_effects_required || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Budget Range:</strong> ${service.makeup_budget_range || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Additional Requirements:</strong> ${service.makeup_additional_requirements || 'N/A'}</p>
                        </div>
                    </div>
                `;
            }

            document.getElementById('pmDetailsContent').innerHTML = `
                <div style="padding: 20px; background: #fff; border-radius: 8px; max-height: 70vh; overflow-y: auto;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h2 style="margin: 0; color: #333;">${service.service_type || 'Request'} - ${service.drama_name || 'N/A'}</h2>
                        <button onclick="closePMRequestDetails()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #666;">&times;</button>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div>
                            <strong>Status:</strong> <span style="padding: 5px 10px; border-radius: 4px; background: #f0f0f0; text-transform: capitalize;">${service.status}</span>
                        </div>
                        ${paymentStatusHTML}
                    </div>

                    ${providerResponse && Object.keys(providerResponse).length > 0 ? `
                    <div style="margin-bottom: 20px;">
                        <strong>Provider Response:</strong>
                        <div style="background: #f9f9f9; padding: 12px; border-radius: 4px; margin-top: 8px;">
                            ${providerResponse.quote_amount ? `<p style="margin: 5px 0;"><strong>Quotation Amount:</strong> Rs ${providerResponse.quote_amount}</p>` : ''}
                            ${providerResponse.needs_advance ? `<p style="margin: 5px 0;"><strong>Advance Payment:</strong> Required - Rs ${providerResponse.advance_amount || '0'} (Due: ${providerResponse.advance_due_date || 'N/A'})</p>` : ''}
                            ${providerResponse.final_payment_due_date ? `<p style="margin: 5px 0;"><strong>Final Payment Due:</strong> ${providerResponse.final_payment_due_date}</p>` : ''}
                            ${providerResponse.note ? `<p style="margin: 5px 0;"><strong>Provider Notes:</strong> ${providerResponse.note}</p>` : ''}
                        </div>
                    </div>
                    ` : ''}

                    ${paymentDetailsHTML}

                    <div style="margin-bottom: 20px;">
                        <strong>Requester Information:</strong>
                        <div style="background: #f9f9f9; padding: 12px; border-radius: 4px; margin-top: 8px;">
                            <p style="margin: 5px 0;"><strong>Name:</strong> ${service.requester_name || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Email:</strong> <a href="mailto:${service.requester_email || ''}">${service.requester_email || 'N/A'}</a></p>
                            <p style="margin: 5px 0;"><strong>Phone:</strong> ${service.requester_phone || 'N/A'}</p>
                        </div>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <strong>Schedule:</strong>
                        <div style="background: #f9f9f9; padding: 12px; border-radius: 4px; margin-top: 8px;">
                            ${service.service_date ? `<p style="margin: 5px 0;"><strong>Service Date:</strong> ${service.service_date}</p>` : ''}
                            <p style="margin: 5px 0;"><strong>Start Date:</strong> ${service.start_date || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>End Date:</strong> ${service.end_date || 'N/A'}</p>
                        </div>
                    </div>

                    ${serviceSpecificHTML}

                    <div style="margin-bottom: 20px;">
                        <strong>Description:</strong>
                        <div style="background: #f9f9f9; padding: 12px; border-radius: 4px; margin-top: 8px; word-wrap: break-word;">
                            ${service.service_required || 'No description provided'}
                        </div>
                    </div>

                    ${service.budget ? `
                    <div style="margin-bottom: 20px;">
                        <strong>Budget Range:</strong>
                        <div style="background: #f9f9f9; padding: 12px; border-radius: 4px; margin-top: 8px;">
                            Rs ${service.budget}
                        </div>
                    </div>
                    ` : ''}

                    ${serviceDetails.additional_requirements ? `
                    <div style="margin-bottom: 20px;">
                        <strong>Additional Requirements:</strong>
                        <div style="background: #f9f9f9; padding: 12px; border-radius: 4px; margin-top: 8px; word-wrap: break-word;">
                            ${serviceDetails.additional_requirements}
                        </div>
                    </div>
                    ` : ''}

                    <div style="border-top: 1px solid #ddd; padding-top: 15px; margin-top: 20px; font-size: 12px; color: #666;">
                        <p style="margin: 5px 0;"><strong>Created:</strong> ${service.created_at ? new Date(service.created_at).toLocaleString() : 'N/A'}</p>
                    </div>
                </div>
            `;
            modal.style.display = 'flex';
        }

        function closePMRequestDetails() {
            document.getElementById('pmDetailsModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const pmDetailsModal = document.getElementById('pmDetailsModal');
            if (event.target === pmDetailsModal) {
                pmDetailsModal.style.display = 'none';
            }
        };
    </script>

    <!-- PM Request Details Modal -->
    <div id="pmDetailsModal" class="modal-overlay">
        <div class="modal-dialog modal-dialog--details modal-content--details" id="pmDetailsContent">
        </div>
    </div>
