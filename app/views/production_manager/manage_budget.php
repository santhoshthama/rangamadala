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
<<<<<<< HEAD
                <a href="<?= ROOT ?>/production_manager/dashboard?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>">
=======
                <a href="dashboard.php?drama_id=1">
>>>>>>> 4a7cd2f6e0c168dac5fa3f8773e569dc3f8aba4b
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
<<<<<<< HEAD
                <a href="<?= ROOT ?>/production_manager/manage_services?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>">
=======
                <a href="manage_services.php?drama_id=1">
>>>>>>> 4a7cd2f6e0c168dac5fa3f8773e569dc3f8aba4b
                    <i class="fas fa-briefcase"></i>
                    <span>Manage Services</span>
                </a>
            </li>
            <li class="active">
<<<<<<< HEAD
                <a href="<?= ROOT ?>/production_manager/manage_budget?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>">
=======
                <a href="manage_budget.php?drama_id=1">
>>>>>>> 4a7cd2f6e0c168dac5fa3f8773e569dc3f8aba4b
                    <i class="fas fa-chart-bar"></i>
                    <span>Budget Management</span>
                </a>
            </li>
            <li>
<<<<<<< HEAD
                <a href="<?= ROOT ?>/production_manager/book_theater?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>">
=======
                <a href="book_theater.php?drama_id=1">
>>>>>>> 4a7cd2f6e0c168dac5fa3f8773e569dc3f8aba4b
                    <i class="fas fa-theater-masks"></i>
                    <span>Theater Bookings</span>
                </a>
            </li>
            <li>
<<<<<<< HEAD
                <a href="<?= ROOT ?>/artistdashboard">
=======
                <a href="../artist/profile.php">
>>>>>>> 4a7cd2f6e0c168dac5fa3f8773e569dc3f8aba4b
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Profile</span>
                </a>
            </li>
            <li>
<<<<<<< HEAD
                <a href="<?= ROOT ?>/logout">
=======
                <a href="../../public/index.php">
>>>>>>> 4a7cd2f6e0c168dac5fa3f8773e569dc3f8aba4b
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main--content">
<<<<<<< HEAD
        <a href="<?= ROOT ?>/production_manager/dashboard?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>" class="back-button">
=======
        <a href="dashboard.php?drama_id=1" class="back-button">
>>>>>>> 4a7cd2f6e0c168dac5fa3f8773e569dc3f8aba4b
            <i class="fas fa-arrow-left"></i>
            Back to Dashboard
        </a>

        <!-- Header -->
        <div class="header--wrapper">
            <div class="header--title">
                <span>Sinhabahu</span>
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
                <h3>LKR 800,000</h3>
                <p>Total Allocated</p>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, var(--success), #1f9b3b);">
                <h3>LKR 336,000</h3>
                <p>Total Spent (42%)</p>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, var(--warning), #e0a800);">
                <h3>LKR 464,000</h3>
                <p>Remaining Balance</p>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, var(--info), #138496);">
                <h3>24</h3>
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
                        <ul style="list-style: none; padding: 0;">
                            <li style="padding: 10px 0; border-bottom: 1px solid #eee; display: flex; justify-content: space-between;">
                                <span><strong>Venue Rental</strong><br><small style="color: var(--muted);">40%</small></span>
                                <span style="font-weight: 700; color: var(--brand);">LKR 320,000</span>
                            </li>
                            <li style="padding: 10px 0; border-bottom: 1px solid #eee; display: flex; justify-content: space-between;">
                                <span><strong>Technical Services</strong><br><small style="color: var(--muted);">25%</small></span>
                                <span style="font-weight: 700; color: var(--brand);">LKR 200,000</span>
                            </li>
                            <li style="padding: 10px 0; border-bottom: 1px solid #eee; display: flex; justify-content: space-between;">
                                <span><strong>Costumes & Makeup</strong><br><small style="color: var(--muted);">20%</small></span>
                                <span style="font-weight: 700; color: var(--brand);">LKR 160,000</span>
                            </li>
                            <li style="padding: 10px 0; display: flex; justify-content: space-between;">
                                <span><strong>Other Expenses</strong><br><small style="color: var(--muted);">15%</small></span>
                                <span style="font-weight: 700; color: var(--brand);">LKR 120,000</span>
                            </li>
                        </ul>
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
                            <th style="padding: 14px; text-align: right; font-weight: 700;">Amount</th>
                            <th style="padding: 14px; text-align: left; font-weight: 700;">Status</th>
                            <th style="padding: 14px; text-align: center; font-weight: 700;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="budgetItemsTable">
                        <tr style="border-bottom: 1px solid var(--border);">
                            <td style="padding: 14px;">Elphinstone Theatre Rental</td>
                            <td style="padding: 14px;">Venue Rental</td>
                            <td style="padding: 14px; text-align: right; font-weight: 700;">LKR 250,000</td>
                            <td style="padding: 14px;"><span class="status-badge assigned">Paid</span></td>
                            <td style="padding: 14px; text-align: center;">
                                <button class="btn btn-secondary" style="padding: 6px 10px; font-size: 12px;" onclick="editBudgetItem(1)">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                                <button class="btn btn-danger" style="padding: 6px 10px; font-size: 12px;" onclick="deleteBudgetItem(1)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr style="border-bottom: 1px solid var(--border);">
                            <td style="padding: 14px;">Sound System Setup</td>
                            <td style="padding: 14px;">Technical Services</td>
                            <td style="padding: 14px; text-align: right; font-weight: 700;">LKR 120,000</td>
                            <td style="padding: 14px;"><span class="status-badge pending">Pending Payment</span></td>
                            <td style="padding: 14px; text-align: center;">
                                <button class="btn btn-secondary" style="padding: 6px 10px; font-size: 12px;" onclick="editBudgetItem(2)">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                                <button class="btn btn-danger" style="padding: 6px 10px; font-size: 12px;" onclick="deleteBudgetItem(2)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr style="border-bottom: 1px solid var(--border);">
                            <td style="padding: 14px;">Professional Lighting</td>
                            <td style="padding: 14px;">Technical Services</td>
                            <td style="padding: 14px; text-align: right; font-weight: 700;">LKR 80,000</td>
                            <td style="padding: 14px;"><span class="status-badge assigned">Paid</span></td>
                            <td style="padding: 14px; text-align: center;">
                                <button class="btn btn-secondary" style="padding: 6px 10px; font-size: 12px;" onclick="editBudgetItem(3)">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                                <button class="btn btn-danger" style="padding: 6px 10px; font-size: 12px;" onclick="deleteBudgetItem(3)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr style="border-bottom: 1px solid var(--border);">
                            <td style="padding: 14px;">Costume & Makeup</td>
                            <td style="padding: 14px;">Costumes & Makeup</td>
                            <td style="padding: 14px; text-align: right; font-weight: 700;">LKR 160,000</td>
                            <td style="padding: 14px;"><span class="status-badge pending">Pending Payment</span></td>
                            <td style="padding: 14px; text-align: center;">
                                <button class="btn btn-secondary" style="padding: 6px 10px; font-size: 12px;" onclick="editBudgetItem(4)">
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
