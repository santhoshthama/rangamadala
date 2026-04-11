<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Services - Rangamadala</title>
    <link rel="stylesheet" href="/Rangamadala/public/assets/CSS/ui-theme.css">
    <link rel="stylesheet" href="/Rangamadala/public/assets/CSS/production_manager/manage_services.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="active">
                <a href="<?= ROOT ?>/production_manager/manage_services?drama_id=<?= $dramaId ?>">
                    <i class="fas fa-briefcase"></i>
                    <span>Manage Services</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/production_manager/manage_budget?drama_id=<?= $dramaId ?>">
                    <i class="fas fa-chart-bar"></i>
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
            <i class="fas fa-arrow-left"></i>
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
                    <i class="fas fa-plus"></i>
                    Browse Service
                </a>
                <button type="button" class="btn btn-secondary" onclick="openAddServiceModal()">
                    <i class="fas fa-plus-circle"></i>
                    Add Service
                </button>
            </div>
        </div>

        <?php if ($serviceMissing): ?>
            <div style="margin:16px 0; padding:12px 14px; border-radius:8px; background:#fff5e6; color:#8a5500; border:1px solid #f4d7a6;">
                <strong>Service should be add before request.</strong>
                <span style="margin-left:8px;">Select the service type below and add it to continue.</span>
            </div>
        <?php endif; ?>

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
                    <div class="service-group-card" style="background:#fff;border:1px solid #eee;border-radius:10px;margin-bottom:20px;overflow:hidden;">
                        <?php $rawType = html_entity_decode($type, ENT_QUOTES, 'UTF-8'); $canRemove = in_array($rawType, array_map(function($s){ return $s->service_type; }, $dramaServices ?? [])); ?>
                        <div style="padding:16px 20px;background:linear-gradient(135deg,#f7f3e9,#efe3c6);border-bottom:1px solid #e7d8af;display:flex;align-items:center;justify-content:space-between;">
                            <h3 style="margin:0; font-size:18px; color:#5a4515;"><?= htmlspecialchars($type) ?></h3>
                            <div style="display:flex; align-items:center; gap:10px;">
                                <span style="font-size:12px;color:#8a7a4e;"><?= count($items) ?> request(s)</span>
                                <a class="btn btn-primary" style="padding:6px 10px; font-size:12px;" href="<?= ROOT ?>/BrowseServiceProviders?drama_id=<?= (int)$dramaId ?>&service_type=<?= urlencode($rawType) ?>">
                                    <i class="fas fa-search"></i> Browse Service
                                </a>
                                <?php if ($canRemove): ?>
                                    <form method="POST" action="<?= ROOT ?>/production_manager/save_required_services?drama_id=<?= (int)$dramaId ?>" style="margin:0;">
                                        <input type="hidden" name="remove_service_type" value="<?= htmlspecialchars($rawType) ?>">
                                        <button type="submit" class="btn btn-secondary" style="padding:6px 10px; font-size:12px; background:#d9534f; border-color:#d9534f; color:#fff;">
                                            <i class="fas fa-trash"></i> Remove
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if (isset($serviceMetaMap[$rawType])): $meta = $serviceMetaMap[$rawType]; ?>
                            <div style="padding:10px 20px; border-bottom:1px solid #f1e7c9; background:#fffdf7; display:flex; gap:20px; font-size:13px; color:#5a4515;">
                                <?php if (!empty($meta['budget'])): ?>
                                    <div><strong>Budget:</strong> Rs <?= htmlspecialchars($meta['budget']) ?></div>
                                <?php endif; ?>
                                <?php if (!empty($meta['description'])): ?>
                                    <div><strong>Description:</strong> <?= htmlspecialchars($meta['description']) ?></div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <div style="padding: 12px 20px;">
                            <?php foreach ($items as $service): ?>
                                <?php 
                                    $status = isset($service->status) ? strtolower($service->status) : 'pending';
                                    $statusText = ucfirst($status);
                                    $statusStyle = '';
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
                                                $statusStyle = 'background: rgba(220, 20, 60, 0.15); color: #DC143C; border: 1px solid rgba(220, 20, 60, 0.3);';
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
                                        <?php if (!$hideGenericBadge): ?><span class="status-badge status-<?= htmlspecialchars($status) ?>" style="<?= $statusStyle ?>"><?= htmlspecialchars($statusText) ?></span><?php endif; ?>
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
                                            <div style="font-style: italic; color: #666; font-size: 13px; margin-left: auto; text-align: right;">⏱️ Awaiting Provider Acceptance</div>
                                        <?php elseif ($status === 'accepted'): ?>
                                            <button class="btn-details" onclick="openPMRequestDetails(event, <?= htmlspecialchars(json_encode((array)$service), ENT_QUOTES, 'UTF-8') ?>)">
                                                View Details
                                            </button>
                                            <div style="font-style: italic; color: #666; font-size: 13px; margin-left: auto; text-align: right;">🟢 In Progress</div>
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
                                                    <span class="status-badge" style="background: rgba(220, 20, 60, 0.15); color: #DC143C; border: 1px solid rgba(220, 20, 60, 0.3);">Verification Failed</span>
                                                <?php else: ?>
                                                    <span class="status-badge" style="background: #fef3c7; color: #92400e; border: 1px solid #fcd34d;">Fully Paid</span>
                                                <?php endif; ?>
                                                <button class="btn-details" onclick="openPMRequestDetails(event, <?= htmlspecialchars(json_encode((array)$service), ENT_QUOTES, 'UTF-8') ?>)">
                                                    View Details
                                                </button>
                                                <?php if ($providerVerificationRejected): ?>
                                                    <button class="btn-details" style="background: #dc2626; color: white; box-shadow: 0 3px 10px rgba(220, 38, 38, 0.35);" 
                                                        onclick="window.location.href='<?= ROOT ?>/Payment/checkout?request_id=<?= (int)$service->id ?>&amount=<?= urlencode(number_format($remainingAmount, 2, '.', '')) ?>&type=remaining'">
                                                        Re-submit Payment
                                                    </button>
                                                    <div style="font-style: italic; color: #DC143C; font-size: 13px; margin-left: auto; text-align: right;">
                                                        ⚠️ Provider could not verify<?= $providerVerificationReason !== '' ? ': ' . htmlspecialchars($providerVerificationReason) : '' ?>
                                                    </div>
                                                <?php else: ?>
                                                    <div style="font-style: italic; color: #666; font-size: 13px; margin-left: auto; text-align: right;">⏳ Awaiting provider's payment confirmation</div>
                                                <?php endif; ?>
                                            <?php elseif ($paymentStatus === 'partially_paid' && $remainingAmount > 0): ?>
                                                <button class="btn-details" style="background: #16a34a; box-shadow: 0 3px 10px rgba(22, 163, 74, 0.35);" onclick="window.location.href='<?= ROOT ?>/Payment/checkout?request_id=<?= (int)$service->id ?>&amount=<?= number_format($remainingAmount, 2, '.', '') ?>&type=remaining'">
                                                    Pay Remaining (Rs <?= number_format($remainingAmount, 2) ?>)
                                                </button>
                                                <button class="btn-details" onclick="openPMRequestDetails(event, <?= htmlspecialchars(json_encode((array)$service), ENT_QUOTES, 'UTF-8') ?>)">
                                                    View Details
                                                </button>
                                            <?php else: ?>
                                                <button class="btn-details" style="background: #16a34a; box-shadow: 0 3px 10px rgba(22, 163, 74, 0.35);" onclick="window.location.href='<?= ROOT ?>/Payment/checkout?request_id=<?= (int)$service->id ?>&amount=<?= number_format($quoteAmount, 2, '.', '') ?>&type=full'">
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
                <div style="text-align: center; padding: 60px 30px; color: var(--muted, #999);">
                    <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
                    <p style="margin-bottom: 30px; font-size: 16px;">No service requests yet. Start by adding service and request service by existing providers.</p>
                    <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                        <button type="button" class="btn btn-secondary" onclick="openAddServiceModal()" style="padding: 12px 24px; font-size: 14px;">
                            <i class="fas fa-plus-circle"></i> Add Service Type
                        </button>
                        <a class="btn btn-primary" href="<?= ROOT ?>/BrowseServiceProviders?drama_id=<?= isset($drama->id) ? $drama->id : ($_GET['drama_id'] ?? 0) ?>" style="padding: 12px 24px; font-size: 14px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                            <i class="fas fa-search"></i> Browse Service
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Request Details Modal -->
    <div id="detailsModal" style="display: none; position: fixed; z-index: 999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.4); align-items: center; justify-content: center;">
        <div style="background-color: #fefefe; padding: 0; border-radius: 8px; width: 90%; max-width: 700px; box-shadow: 0 4px 6px rgba(0,0,0,0.15);" id="detailsContent">
        </div>
    </div>

    <!-- Add Service Modal -->
    <div id="addServiceModal" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; background-color: rgba(0,0,0,0.4); align-items:center; justify-content:center;">
        <div style="background:#fff; border-radius:8px; width:90%; max-width:520px; box-shadow:0 4px 6px rgba(0,0,0,0.15);">
            <div style="padding:16px 20px; border-bottom:1px solid #eee; display:flex; align-items:center; justify-content:space-between;">
                <h3 style="margin:0; font-size:18px;">Add Service Type</h3>
                <button type="button" onclick="closeAddServiceModal()" style="background:none; border:none; font-size:20px; cursor:pointer;">&times;</button>
            </div>
            <div style="padding:16px 20px;">
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
                <form method="POST" action="<?= ROOT ?>/production_manager/save_required_services?drama_id=<?= $dramaId ?>" style="display:flex; flex-direction:column; gap:12px;">
                    <?php if (!empty($returnUrl)): ?>
                        <input type="hidden" name="return_url" value="<?= htmlspecialchars($returnUrl) ?>">
                    <?php endif; ?>
                    <label style="font-size:14px; color:#444;">Select service types to add</label>
                    <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:8px;">
                        <?php foreach ($allTypes as $t): 
                            $isExisting = in_array($t, $existingServices);
                        ?>
                            <label style="display:flex; align-items:center; gap:8px; padding:8px 10px; border:1px solid #eee; border-radius:6px; background:#fafafa; color:#333; cursor: <?= $isExisting ? 'not-allowed; opacity: 0.6;' : 'pointer;' ?>">
                                <input type="checkbox" name="required_services[]" value="<?= htmlspecialchars($t) ?>" <?= $isExisting ? 'checked disabled' : '' ?>>
                                <span><?= htmlspecialchars($t) ?><?= $isExisting ? ' (added)' : '' ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <div style="display:flex; flex-direction:column; gap:6px; margin-top:10px;">
                        <label style="font-size:14px; color:#444;">Budget (optional)</label>
                        <input type="text" name="service_budget" placeholder="Enter budget" style="padding:10px; border:1px solid #ddd; border-radius:6px;">
                    </div>
                    <div style="display:flex; flex-direction:column; gap:6px;">
                        <label style="font-size:14px; color:#444;">Description (optional)</label>
                        <textarea name="service_description" rows="3" placeholder="Add a short description" style="padding:10px; border:1px solid #ddd; border-radius:6px;"></textarea>
                    </div>
                    <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:8px;">
                        <button type="button" class="btn" onclick="closeAddServiceModal()">Cancel</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Add</button>
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
            
            let html = '<div style="padding: 24px;">';
            html += '<h3 style="margin: 0 0 16px 0; font-size: 18px; font-weight: 600; color: #1f2937;">Request Details</h3>';
            
            html += '<div style="background: #f9fafb; padding: 16px; border-radius: 6px; border: 1px solid #e5e7eb; margin-bottom: 20px;">';
            
            if (requestData.provider_name) {
                html += '<div style="margin-bottom: 12px;"><label style="font-size: 12px; font-weight: 600; color: #6b7280;">Provider Name</label><div style="font-size: 14px; color: #1f2937;">' + (requestData.provider_name || '-') + '</div></div>';
            }
            
            if (requestData.service_required) {
                html += '<div style="margin-bottom: 12px;"><label style="font-size: 12px; font-weight: 600; color: #6b7280;">Service Required</label><div style="font-size: 14px; color: #1f2937;">' + (requestData.service_required || '-') + '</div></div>';
            }
            
            if (requestData.budget) {
                html += '<div style="margin-bottom: 12px;"><label style="font-size: 12px; font-weight: 600; color: #6b7280;">Budget</label><div style="font-size: 14px; color: #1f2937;">Rs ' + (requestData.budget || '-') + '</div></div>';
            }
            
            if (requestData.service_date) {
                html += '<div style="margin-bottom: 12px;"><label style="font-size: 12px; font-weight: 600; color: #6b7280;">Service Date</label><div style="font-size: 14px; color: #1f2937;">' + (requestData.service_date || '-') + '</div></div>';
            }
            
            if (requestData.start_date || requestData.end_date) {
                html += '<div style="margin-bottom: 12px;"><label style="font-size: 12px; font-weight: 600; color: #6b7280;">Duration</label><div style="font-size: 14px; color: #1f2937;">' + (requestData.start_date || '-') + ' to ' + (requestData.end_date || '-') + '</div></div>';
            }
            
            if (requestData.status) {
                html += '<div style="margin-bottom: 0;"><label style="font-size: 12px; font-weight: 600; color: #6b7280;">Status</label><div style="font-size: 14px; color: #1f2937;">' + (requestData.status || '-') + '</div></div>';
            }
            
            html += '</div>';
            html += '<div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 24px;">';
            html += '<button onclick="closeDetailsModal()" style="padding: 10px 20px; font-size: 14px; font-weight: 500; border: none; border-radius: 6px; cursor: pointer; background: #6b7280; color: #fff;">Close</button>';
            html += '</div>';
            html += '</div>';
            
            detailsContent.innerHTML = html;
            document.getElementById('detailsModal').style.display = 'flex';
        }

        function closeDetailsModal() {
            document.getElementById('detailsModal').style.display = 'none';
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
            .then(res => res.json())
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
    <div id="confirmModal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.4); align-items: center; justify-content: center; flex-direction: column;">
        <div style="background: #fff; border-radius: 8px; width: 90%; max-width: 550px; box-shadow: 0 4px 6px rgba(0,0,0,0.15); max-height: 90vh; overflow-y: auto;">
            <div style="padding: 20px; border-bottom: 1px solid #ddd; background: linear-gradient(135deg, #d4af37, #aa8c2c); display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0;">
                <h3 style="margin: 0; font-size: 18px; color: #1a1410;">Provider Response</h3>
                <button onclick="closeConfirmModal()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #1a1410; padding: 0; width: 30px; height: 30px;">&times;</button>
            </div>
            <div style="padding: 24px;">
                <div style="background: #f9fafb; padding: 16px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #e5e7eb;">
                    <h4 style="margin: 0 0 16px 0; font-size: 14px; font-weight: 600; color: #1f2937;">Quotation Details</h4>
                    
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; margin-bottom: 4px;">Quotation Amount</label>
                        <div style="font-size: 15px; font-weight: 500; color: #1f2937;">Rs <span id="review_quote_amount">-</span></div>
                    </div>

                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; margin-bottom: 4px;">Advance Payment Required</label>
                        <div style="font-size: 15px; font-weight: 500; color: #1f2937;"><span id="review_advance_status">No</span></div>
                    </div>

                    <div id="advanceDetailsRow" style="display: none; background: #fffdf7; padding: 12px; border: 1px solid #f0e4c6; border-radius: 6px; margin-bottom: 16px;">
                        <div style="font-size: 12px; font-weight: 600; color: #1f2937; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb;">Advance Payment Details</div>
                        <div style="margin-bottom: 12px;">
                            <label style="display: block; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; margin-bottom: 4px;">Advance Amount</label>
                            <div style="font-size: 15px; font-weight: 500; color: #1f2937;">Rs <span id="review_advance_amount">-</span></div>
                        </div>
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; margin-bottom: 4px;">Advance Due Date</label>
                            <div style="font-size: 15px; font-weight: 500; color: #1f2937;"><span id="review_advance_due_date">-</span></div>
                        </div>
                    </div>

                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; margin-bottom: 4px;">Final Payment Due Date</label>
                        <div style="font-size: 15px; font-weight: 500; color: #1f2937;"><span id="review_final_payment_due">-</span></div>
                    </div>

                    <div id="providerNoteRow" style="display: none; background: #f3f4f6; padding: 12px; border: 1px solid #e5e7eb; border-radius: 6px;">
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; margin-bottom: 6px;">Provider Notes</label>
                        <div style="font-size: 13px; color: #374151; font-style: italic;" id="review_provider_note"></div>
                    </div>
                </div>

                <input type="hidden" id="confirm_request_id">
                <input type="hidden" id="confirm_advance_amount">
                <input type="hidden" id="confirm_needs_advance">

                <!-- Note about advance payment (informational only) -->
                <div id="advanceInfoSection" style="display: none; background: #ecfdf5; padding: 14px; border-radius: 6px; margin-bottom: 20px; border-left: 4px solid #10b981;">
                    <p style="margin: 0; font-size: 13px; color: #065f46;">
                        <strong>💳 Payment Required:</strong> After confirming, you'll be redirected to pay the advance amount of <strong>Rs <span id="advance_info_amount">0</span></strong>.
                    </p>
                </div>

                <!-- Action Buttons (single set) -->
                <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px;">
                    <button onclick="closeConfirmModal()" style="padding: 10px 20px; font-size: 14px; font-weight: 500; border: none; border-radius: 6px; cursor: pointer; background: #6b7280; color: #fff; transition: background 0.2s;">Close</button>
                    <button onclick="rejectProviderResponse()" style="padding: 10px 20px; font-size: 14px; font-weight: 500; border: none; border-radius: 6px; cursor: pointer; background: #ef4444; color: #fff; transition: background 0.2s;">
                        Reject
                    </button>
                    <button id="confirmBtn" onclick="acceptProviderResponse()" style="padding: 10px 20px; font-size: 14px; font-weight: 500; border: none; border-radius: 6px; cursor: pointer; background: linear-gradient(135deg, #d4af37, #aa8c2c); color: #1a1410; transition: background 0.2s;">
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
            .then(res => res.json())
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
            .then(res => res.json())
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
            message.style.position = 'fixed';
            message.style.top = '20px';
            message.style.right = '20px';
            message.style.padding = '12px 20px';
            message.style.borderRadius = '6px';
            message.style.zIndex = '1001';
            message.style.fontWeight = '500';
            
            if (type === 'success') {
                message.style.background = '#28a745';
                message.style.color = 'white';
            } else if (type === 'error') {
                message.style.background = '#dc3545';
                message.style.color = 'white';
            }
            
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
    <div id="pmDetailsModal" style="display: none; position: fixed; z-index: 999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.4); align-items: center; justify-content: center;">
        <div style="background-color: #fefefe; padding: 0; border-radius: 8px; width: 90%; max-width: 700px; box-shadow: 0 4px 6px rgba(0,0,0,0.15);" id="pmDetailsContent">
        </div>
    </div>
