<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budget Management - Rangamadala</title>
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
            <li>
                <a href="<?= ROOT ?>/production_manager/manage_services?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>">
                    <i class="fas fa-briefcase"></i>
                    <span>Manage Services</span>
                </a>
            </li>
            <li class="active">
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
                <h2>Budget Management</h2>
            </div>
            <div class="header-controls">
                <button class="btn btn-primary" onclick="openAddBudgetModal()">
                    <i class="fas fa-plus"></i>
                    Add Budget Item
                </button>
                <button class="btn btn-secondary" onclick="exportBudgetReport()">
                    <i class="fas fa-download"></i>
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
            <div class="stat-card" style="background: linear-gradient(135deg, var(--success), #1f9b3b);">
                <h3>LKR <?= isset($totalSpent) ? number_format($totalSpent) : '0' ?> (<?= isset($percentSpent) ? $percentSpent : '0' ?>%)</h3>
                <p>Total Spent</p>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, var(--warning), #e0a800);">
                <h3>LKR <?= isset($remainingBudget) ? number_format($remainingBudget) : '0' ?></h3>
                <p>Remaining Balance</p>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, var(--info), #138496);">
                <h3><?= isset($budgetItems) && is_array($budgetItems) ? count($budgetItems) : '0' ?></h3>
                <p>Total Budget Items</p>
            </div>
        </div>

        <!-- Budget Overview Chart -->
        <div class="content" style="padding: 28px; margin-bottom: 26px;">
            <h3 style="margin-bottom: 16px;">Budget Breakdown by Category</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                <!-- Chart -->
                <div>
                    <div style="background: linear-gradient(135deg, rgba(186,142,35,0.1) 0%, rgba(186,142,35,0.05) 100%); border-radius: 12px; padding: 20px; text-align: center;">
                        <canvas id="budgetChart" style="max-width: 100%; height: 250px;"></canvas>
                    </div>
                </div>
                <!-- Category Breakdown -->
                <div>
                    <div style="background: #f8f9fa; border-radius: 12px; padding: 16px;">
                        <?php if (isset($categorySummary) && is_array($categorySummary) && !empty($categorySummary)): ?>
                            <ul style="list-style: none; padding: 0;">
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
                                    <li style="padding: 10px 0; <?= $isLast ? '' : 'border-bottom: 1px solid #eee;' ?> display: flex; justify-content: space-between;">
                                        <span>
                                            <strong><?= esc($categoryName) ?></strong><br>
                                            <small style="color: var(--muted);"><?= $percentage ?>%</small>
                                        </span>
                                        <span style="font-weight: 700; color: var(--brand);">LKR <?= number_format($categoryTotal) ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p style="text-align: center; color: var(--muted); padding: 20px;">
                                No budget categories yet
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Budget Items Table -->
        <div class="content" style="padding: 28px;">
            <h3 style="margin-bottom: 16px;">Budget Items</h3>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8f9fa; border-bottom: 2px solid var(--border-strong);">
                            <th style="padding: 14px; text-align: left; font-weight: 700;">Item Name</th>
                            <th style="padding: 14px; text-align: left; font-weight: 700;">Category</th>
                            <th style="padding: 14px; text-align: right; font-weight: 700;">Allocated</th>
                            <th style="padding: 14px; text-align: right; font-weight: 700;">Spent</th>
                            <th style="padding: 14px; text-align: left; font-weight: 700;">Status</th>
                            <th style="padding: 14px; text-align: center; font-weight: 700;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="budgetItemsTable">
                        <?php if (isset($budgetItems) && is_array($budgetItems) && !empty($budgetItems)): ?>
                            <?php foreach ($budgetItems as $item): ?>
                                <?php 
                                    $statusClass = 'pending';
                                    $statusText = ucfirst($item->status ?? 'pending');
                                    
                                    if (isset($item->status)) {
                                        if ($item->status === 'paid') {
                                            $statusClass = 'assigned';
                                        } elseif ($item->status === 'approved') {
                                            $statusClass = 'assigned';
                                        }
                                    }
                                ?>
                                <tr style="border-bottom: 1px solid var(--border);">
                                    <td style="padding: 14px;"><?= isset($item->item_name) ? esc($item->item_name) : 'N/A' ?></td>
                                    <td style="padding: 14px;"><?= isset($item->category) ? ucfirst($item->category) : 'N/A' ?></td>
                                    <td style="padding: 14px; text-align: right; font-weight: 700;">LKR <?= isset($item->allocated_amount) ? number_format($item->allocated_amount) : '0' ?></td>
                                    <td style="padding: 14px; text-align: right; font-weight: 700;">LKR <?= isset($item->spent_amount) ? number_format($item->spent_amount) : '0' ?></td>
                                    <td style="padding: 14px;"><span class="status-badge <?= $statusClass ?>"><?= $statusText ?></span></td>
                                    <td style="padding: 14px; text-align: center;">
                                        <button class="btn btn-secondary" style="padding: 6px 10px; font-size: 12px;" onclick="editBudgetItem(<?= isset($item->id) ? $item->id : 'null' ?>)">
                                            <i class="fas fa-pencil-alt"></i>
                                        </button>
                                        <button class="btn btn-danger" style="padding: 6px 10px; font-size: 12px;" onclick="deleteBudgetItem(<?= isset($item->id) ? $item->id : 'null' ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr style="border-bottom: 1px solid var(--border);">
                                <td colspan="6" style="padding: 30px; text-align: center; color: var(--muted);">
                                    <i class="fas fa-file-invoice-dollar" style="font-size: 32px; margin-bottom: 12px; opacity: 0.5;"></i>
                                    <p>No budget items yet. Add your first budget item to get started.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>>
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                                <button class="btn btn-danger" style="padding: 6px 10px; font-size: 12px;" onclick="deleteBudgetItem(4)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
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
            
            <div class="form-group">
                <label for="itemName">Item Name</label>
                <input type="text" id="itemName" placeholder="Enter budget item name">
            </div>

            <div class="form-group">
                <label for="itemCategory">Category</label>
                <select id="itemCategory">
                    <option value="">Select Category</option>
                    <option value="venue">Venue Rental</option>
                    <option value="technical">Technical Services</option>
                    <option value="costume">Costumes & Makeup</option>
                    <option value="marketing">Marketing & Promotion</option>
                    <option value="other">Other Expenses</option>
                </select>
            </div>

            <div class="form-group">
                <label for="itemAmount">Amount (LKR)</label>
                <input type="number" id="itemAmount" placeholder="Enter amount" min="0" step="1000">
            </div>

            <div class="form-group">
                <label for="paymentStatus">Payment Status</label>
                <select id="paymentStatus">
                    <option value="pending">Pending Payment</option>
                    <option value="paid">Paid</option>
                    <option value="partial">Partial Payment</option>
                </select>
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

    <script src="/Rangamadala/public/assets/JS/manage-budget.js"></script>
</body>
</html>
