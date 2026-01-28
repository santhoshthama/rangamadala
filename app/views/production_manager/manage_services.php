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
                                        <span class="status-badge status-<?= htmlspecialchars($status) ?>"><?= htmlspecialchars($statusText) ?></span>
                                        <?php if ($budget !== null): ?><span class="price">Rs <?= $budget ?></span><?php endif; ?>
                                        
                                        <?php if ($status === 'provider_responded'): ?>
                                            <?php
                                                $serviceDetails = $service->service_details_json ? json_decode($service->service_details_json, true) : [];
                                                $providerResponse = $serviceDetails['provider_response'] ?? [];
                                            ?>
                                            <button class="btn-details" onclick="openConfirmModal(<?= (int)$service->id ?>, <?= htmlspecialchars(json_encode($providerResponse), ENT_QUOTES, 'UTF-8') ?>)">
                                                Review & Confirm
                                            </button>
                                            <button class="btn-details" data-request="<?= htmlspecialchars(json_encode((array)$service), ENT_QUOTES, 'UTF-8') ?>" onclick="openRequestDetailsFromButton(this)">
                                                View Details
                                            </button>
                                        <?php elseif ($status === 'confirmed'): ?>
                                            <div style="font-style: italic; color: #666; font-size: 13px;">⏱️ Awaiting Provider Acceptance</div>
                                        <?php else: ?>
                                            <button class="btn-details" data-request="<?= htmlspecialchars(json_encode((array)$service), ENT_QUOTES, 'UTF-8') ?>" onclick="openRequestDetailsFromButton(this)">View Details</button>
                                        <?php endif; ?>
                                        
                                        <button class="btn-reject" onclick="cancelServiceRequest(this)" data-id="<?= (int)$service->id ?>">Cancel</button>
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
                        <strong>💳 Payment Required:</strong> After confirming, you'll be redirected to a secure checkout page to complete the advance payment of <strong>Rs <span id="advance_info_amount">0</span></strong> via PayPal.
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

        // Payment Functions - Placeholder for PayPal integration
        function initiateAdvancePayment() {
            const requestId = document.getElementById('confirm_request_id').value;
            const amount = document.getElementById('confirm_advance_amount').value;
            
            if (!requestId || !amount) {
                showMessage('Missing payment information', 'error');
                return;
            }

            // TODO: Integrate PayPal payment here
            showMessage('PayPal integration pending', 'info');
        }

        function onAdvancePaymentSuccess(requestId) {
            showMessage('Advance payment completed! You can now confirm the service.', 'success');
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
            if (event.target === confirmModal) {
                closeConfirmModal();
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
    </script>
