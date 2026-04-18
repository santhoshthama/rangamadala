<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budget Management - Rangamadala</title>
    <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/ui-theme.css">
    <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/production_manager/manage_budget.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <?php $dramaId = isset($dramaId) ? (int)$dramaId : (int)($drama->id ?? 0); ?>
    <?php
        $serviceTypes = (isset($serviceTypes) && is_array($serviceTypes)) ? $serviceTypes : [];
        $serviceRequests = (isset($serviceRequests) && is_array($serviceRequests)) ? $serviceRequests : [];
    ?>
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
            <li>
                <a href="<?= ROOT ?>/production_manager/manage_services?drama_id=<?= $dramaId ?>">
                    <i class="fas fa-briefcase"></i>
                    <span>Manage Services</span>
                </a>
            </li>
            <li class="active">
                <a href="<?= ROOT ?>/production_manager/manage_budget?drama_id=<?= $dramaId ?>">
                    <i class="fas fa-chart-bar"></i>
                    <span>Budget Management</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/artistdashboard/calendar">
                    <i class="bx bx-calendar-week"></i>
                    <span>Artist Calendar</span>
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

        <!-- Header -->
        <div class="header--wrapper">
            <div class="header--title">
                <span><?= isset($drama->drama_name) ? esc($drama->drama_name) : 'Drama' ?></span>
                <h2>Budget Management</h2>
            </div>
            <div class="header-controls">
                <button class="btn btn-primary" onclick="openAddBudgetModal()">
                    <i class="bx bx-plus"></i>
                    Add Budget Item
                </button>
                <button class="btn btn-secondary" onclick="exportBudgetReport()">
                    <i class="bx bx-download"></i>
                    Export Report
                </button>
            </div>
        </div>

        <!-- Budget Summary Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>LKR <?= isset($totalBudget) ? number_format($totalBudget) : '0' ?></h3>
                <p>Total Allocated</p>
            </div>
            <div class="stat-card pm-budget-card-spent">
                <h3>LKR <?= isset($totalSpent) ? number_format($totalSpent) : '0' ?> (<?= isset($percentSpent) ? $percentSpent : '0' ?>%)</h3>
                <p>Total Spent</p>
            </div>
            <div class="stat-card pm-budget-card-remaining">
                <h3>LKR <?= isset($remainingBudget) ? number_format($remainingBudget) : '0' ?></h3>
                <p>Remaining Balance</p>
            </div>
            <div class="stat-card pm-budget-card-total-items">
                <h3><?= isset($budgetItems) && is_array($budgetItems) ? count($budgetItems) : '0' ?></h3>
                <p>Total Budget Items</p>
            </div>
        </div>

        <!-- Budget Overview Chart -->
        <div class="content pm-budget-overview">
            <h3 class="pm-budget-section-title">Budget Breakdown by Category</h3>
            <div class="pm-budget-grid">
                <!-- Chart -->
                <div>
                    <div class="pm-budget-chart-card">
                        <canvas id="budgetChart" class="pm-budget-chart-canvas"></canvas>
                    </div>
                </div>
                <!-- Category Breakdown -->
                <div>
                    <div class="pm-budget-category-card">
                        <?php if (isset($categorySummary) && is_array($categorySummary) && !empty($categorySummary)): ?>
                            <ul class="pm-budget-category-list">
                                <?php 
                                $categoryCount = 0;
                                foreach ($categorySummary as $catData): 
                                    $categoryCount++;
                                    $isLast = $categoryCount === count($categorySummary);
                                    $categoryName = isset($catData->category) ? ucfirst($catData->category) : 'Unknown';
                                    $categoryTotal = isset($catData->total_allocated) ? floatval($catData->total_allocated) : 0;
                                    $percentage = 0;
                                    if ($totalBudget > 0) {
                                        $percentage = round(($categoryTotal / $totalBudget) * 100);
                                    }
                                ?>
                                    <li class="pm-budget-category-item<?= $isLast ? ' pm-budget-category-item--last' : '' ?>">
                                        <span>
                                            <strong><?= esc($categoryName) ?></strong><br>
                                            <small class="pm-budget-muted"><?= $percentage ?>%</small>
                                        </span>
                                        <span class="pm-budget-category-value">LKR <?= number_format($categoryTotal) ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="pm-budget-empty">
                                No budget categories yet
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Budget Items Table -->
        <div class="content pm-budget-table-section">
            <h3 class="pm-budget-section-title">Budget Items</h3>
            <div class="pm-budget-table-wrap">
                <table class="pm-budget-table">
                    <thead>
                        <tr>
                            <th class="pm-budget-table-header-cell">Item Name</th>
                            <th class="pm-budget-table-header-cell">Category</th>
                            <th class="pm-budget-table-header-cell">Linked Service</th>
                            <th class="pm-budget-table-header-cell pm-budget-table-header-cell--right">Allocated</th>
                            <th class="pm-budget-table-header-cell pm-budget-table-header-cell--right">Spent</th>
                            <th class="pm-budget-table-header-cell">Status</th>
                            <th class="pm-budget-table-header-cell pm-budget-table-header-cell--center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="budgetItemsTable">
                        <?php if (isset($budgetItems) && is_array($budgetItems) && !empty($budgetItems)): ?>
                            <?php foreach ($budgetItems as $item): ?>
                                <?php 
                                    $statusClass = 'pending';
                                    $statusText = ucfirst($item->status ?? 'pending');
                                    
                                    if (isset($item->status)) {
                                        if ($item->status === 'approved' || $item->status === 'completed') {
                                            $statusClass = 'assigned';
                                        } elseif ($item->status === 'cancelled') {
                                            $statusClass = 'rejected';
                                        }
                                    }
                                ?>
                                <tr class="pm-budget-row">
                                    <td class="pm-budget-cell"><?= isset($item->item_name) ? esc($item->item_name) : 'N/A' ?></td>
                                    <td class="pm-budget-cell"><?= isset($item->category) ? ucfirst($item->category) : 'N/A' ?></td>
                                    <td class="pm-budget-cell"><?= !empty($item->service_request_id) ? ('Request #' . (int)$item->service_request_id) : 'Manual' ?></td>
                                    <td class="pm-budget-cell pm-budget-cell--right">LKR <?= isset($item->allocated_amount) ? number_format($item->allocated_amount) : '0' ?></td>
                                    <td class="pm-budget-cell pm-budget-cell--right">LKR <?= isset($item->spent_amount) ? number_format($item->spent_amount) : '0' ?></td>
                                    <td class="pm-budget-cell"><span class="status-badge <?= $statusClass ?>"><?= $statusText ?></span></td>
                                    <td class="pm-budget-cell pm-budget-cell--center">
                                        <button class="btn btn-secondary pm-budget-action-btn" onclick="editBudgetItem(<?= isset($item->id) ? $item->id : 'null' ?>)">
                                            <i class="bx bx-pencil-alt"></i>
                                        </button>
                                        <button class="btn btn-danger pm-budget-action-btn" onclick="deleteBudgetItem(<?= isset($item->id) ? $item->id : 'null' ?>)">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr class="pm-budget-row">
                                <td colspan="7" class="pm-budget-cell pm-budget-cell--center pm-budget-muted">
                                    <i class="bx bx-file-invoice-dollar pm-budget-empty-icon"></i>
                                    <p>No budget items yet. Add your first budget item to get started.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Add/Edit Budget Modal -->
    <div id="budgetModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeBudgetModal()">&times;</span>
            <h2><i class="fas fa-plus"></i> Add Budget Item</h2>

            <input type="hidden" id="budgetItemId" value="">
            
            <div class="form-group">
                <label for="itemName">Item Name</label>
                <input type="text" id="itemName" placeholder="Enter budget item name">
            </div>

            <div class="form-group">
                <label for="serviceRequestId">Link to Service Request (Optional)</label>
                <select id="serviceRequestId" onchange="handleServiceRequestChange()">
                    <option value="">Manual Budget Item (Not linked)</option>
                    <?php foreach ($serviceRequests as $request): ?>
                        <?php
                            $rid = (int)($request->id ?? 0);
                            if ($rid <= 0) {
                                continue;
                            }
                            $serviceType = trim((string)($request->service_type ?? 'Service'));
                            $providerName = trim((string)($request->provider_name ?? 'Unassigned provider'));
                            $budgetVal = isset($request->budget) ? (float)$request->budget : 0;
                            $statusVal = trim((string)($request->status ?? 'pending'));
                        ?>
                        <option value="<?= $rid ?>">
                            #<?= $rid ?> - <?= esc($serviceType) ?> | <?= esc($providerName) ?> | LKR <?= number_format($budgetVal) ?> | <?= esc(ucfirst($statusVal)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="pm-budget-note">When linked, category and status follow the service/payment lifecycle automatically.</small>
            </div>

            <div class="form-group">
                <label for="itemCategory">Category</label>
                <select id="itemCategory">
                    <option value="">Select Category</option>
                    <?php foreach ($serviceTypes as $serviceType): ?>
                        <?php $serviceType = trim((string)$serviceType); if ($serviceType === '') { continue; } ?>
                        <option value="<?= esc($serviceType) ?>"><?= esc($serviceType) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="itemAmount">Allocated Amount (LKR)</label>
                <input type="number" id="itemAmount" placeholder="Enter amount" min="0" step="1000">
            </div>

            <div class="form-group">
                <label for="spentAmount">Spent Amount (LKR)</label>
                <input type="number" id="spentAmount" placeholder="Enter spent amount" min="0" step="1000" value="0">
            </div>

            <div class="form-group">
                <label for="paymentStatus">Status</label>
                <select id="paymentStatus">
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
                <small class="pm-budget-note">For linked requests, this is auto-derived from request and payment states.</small>
            </div>

            <div class="form-group">
                <label for="notes">Notes</label>
                <textarea id="notes" placeholder="Add notes or details about this budget item"></textarea>
            </div>

            <div class="modal-actions">
                <button class="btn btn-secondary" onclick="closeBudgetModal()">Cancel</button>
                <button class="btn btn-primary" onclick="saveBudgetItem()">Save Item</button>
            </div>
        </div>
    </div>

    <script>
        window.PM_BUDGET_API_BASE = '<?= ROOT ?>/production_manager';
        window.PM_SERVICE_REQUESTS = <?= json_encode($serviceRequests) ?>;
    </script>
    <script src="<?= ROOT ?>/assets/JS/manage-budget.js"></script>
</body>
</html>
