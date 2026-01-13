<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services & Budget - Rangamadala</title>
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
                <a href="dashboard.php?drama_id=1">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="drama_details.php?drama_id=1">
                    <i class="fas fa-film"></i>
                    <span>Drama Details</span>
                </a>
            </li>
            <li>
                <a href="manage_roles.php?drama_id=1">
                    <i class="fas fa-users"></i>
                    <span>Artist Roles</span>
                </a>
            </li>
            <li>
                <a href="assign_managers.php?drama_id=1">
                    <i class="fas fa-user-tie"></i>
                    <span>Production Manager</span>
                </a>
            </li>
            <li>
                <a href="schedule_management.php?drama_id=1">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Schedule</span>
                </a>
            </li>
            <li class="active">
                <a href="view_services_budget.php?drama_id=1">
                    <i class="fas fa-dollar-sign"></i>
                    <span>Services & Budget</span>
                </a>
            </li>
            <li>
                <a href="../artist/profile.php">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Profile</span>
                </a>
            </li>
            <li>
                <a href="../../public/index.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main--content">
        <a href="dashboard.php?drama_id=1" class="back-button">
            <i class="fas fa-arrow-left"></i>
            Back to Dashboard
        </a>

        <!-- Header -->
        <div class="header--wrapper">
            <div class="header--title">
                <span>Sinhabahu</span>
                <h2>Services & Budget Overview</h2>
            </div>
        </div>

        <!-- View-Only Notice -->
        <div class="view-only-notice" style="margin-bottom: 30px;">
            <i class="fas fa-eye"></i>
            <strong>View-Only Access:</strong> Services and Budget are managed by your Production Managers. You can view all details but cannot make changes.
        </div>

        <!-- Budget Summary Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>LKR 800,000</h3>
                <p>Total Budget</p>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, var(--success), #1f9b3b);">
                <h3>LKR 336,000</h3>
                <p>Budget Used (42%)</p>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, var(--warning), #e0a800);">
                <h3>LKR 464,000</h3>
                <p>Remaining (58%)</p>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, var(--danger), #c82333);">
                <h3>LKR 125,000</h3>
                <p>Pending Payments</p>
            </div>
        </div>

        <!-- Tabs -->
        <div class="tabs">
            <button class="tab-button active" onclick="showTab('services')">
                <i class="fas fa-handshake"></i>
                Services
            </button>
            <button class="tab-button" onclick="showTab('budget')">
                <i class="fas fa-dollar-sign"></i>
                Budget Items
            </button>
            <button class="tab-button" onclick="showTab('theaters')">
                <i class="fas fa-theater-masks"></i>
                Theater Bookings
            </button>
        </div>

        <!-- Tab: Services -->
        <div id="servicesTab" class="tab-content active">
            <div class="content">
                <div class="profile-container" style="grid-template-columns: 1fr;">
                    <div class="details">
                        <div class="card-section">
                            <h3>
                                <span>Booked Services</span>
                            </h3>
                            <ul>
                                <li>
                                    <div>
                                        <strong>Lighting Service - Bright Lights Co.</strong>
                                        <div class="request-info">
                                            Managed by: Priyantha Silva | Booking Date: 2024-12-05
                                        </div>
                                        <div class="request-info">
                                            Amount: LKR 85,000 | Service Date: 2024-12-30
                                        </div>
                                    </div>
                                    <div style="display: flex; gap: 8px; align-items: center;">
                                        <span class="status-badge assigned">Confirmed</span>
                                        <span class="status-badge" style="background: #28a745; color: #fff;">Paid</span>
                                    </div>
                                </li>
                                <li>
                                    <div>
                                        <strong>Sound System - Audio Pro Services</strong>
                                        <div class="request-info">
                                            Managed by: Malini Fernando | Booking Date: 2024-12-08
                                        </div>
                                        <div class="request-info">
                                            Amount: LKR 65,000 | Service Date: 2024-12-30
                                        </div>
                                    </div>
                                    <div style="display: flex; gap: 8px; align-items: center;">
                                        <span class="status-badge assigned">Confirmed</span>
                                        <span class="status-badge pending">Pending Payment</span>
                                    </div>
                                </li>
                                <li>
                                    <div>
                                        <strong>Makeup & Costume - Glamour Studio</strong>
                                        <div class="request-info">
                                            Managed by: Malini Fernando | Booking Date: 2024-12-10
                                        </div>
                                        <div class="request-info">
                                            Amount: LKR 120,000 | Service Date: 2024-12-29 - 2024-12-30
                                        </div>
                                    </div>
                                    <div style="display: flex; gap: 8px; align-items: center;">
                                        <span class="status-badge assigned">Confirmed</span>
                                        <span class="status-badge" style="background: #28a745; color: #fff;">Paid</span>
                                    </div>
                                </li>
                                <li>
                                    <div>
                                        <strong>Stage Props - Creative Props Ltd.</strong>
                                        <div class="request-info">
                                            Managed by: Priyantha Silva | Booking Date: 2024-12-12
                                        </div>
                                        <div class="request-info">
                                            Amount: LKR 45,000 | Delivery: 2024-12-28
                                        </div>
                                    </div>
                                    <div style="display: flex; gap: 8px; align-items: center;">
                                        <span class="status-badge requested">Pending Confirmation</span>
                                        <span class="status-badge unassigned">Not Paid</span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab: Budget Items -->
        <div id="budgetTab" class="tab-content">
            <div class="content">
                <div class="profile-container" style="grid-template-columns: 1fr;">
                    <div class="details">
                        <div class="card-section">
                            <h3>Budget Breakdown</h3>
                            <ul>
                                <li>
                                    <div>
                                        <strong>Lighting Service</strong>
                                        <div class="request-info">
                                            Category: Technical Services | Added by: Priyantha Silva | Date: 2024-12-05
                                        </div>
                                    </div>
                                    <div style="display: flex; gap: 8px; align-items: center;">
                                        <span style="font-weight: 700; color: var(--brand);">LKR 85,000</span>
                                        <span class="status-badge" style="background: #28a745; color: #fff;">Paid</span>
                                    </div>
                                </li>
                                <li>
                                    <div>
                                        <strong>Sound System</strong>
                                        <div class="request-info">
                                            Category: Technical Services | Added by: Malini Fernando | Date: 2024-12-08
                                        </div>
                                    </div>
                                    <div style="display: flex; gap: 8px; align-items: center;">
                                        <span style="font-weight: 700; color: var(--brand);">LKR 65,000</span>
                                        <span class="status-badge pending">Pending</span>
                                    </div>
                                </li>
                                <li>
                                    <div>
                                        <strong>Makeup & Costume Design</strong>
                                        <div class="request-info">
                                            Category: Artistic Services | Added by: Malini Fernando | Date: 2024-12-10
                                        </div>
                                    </div>
                                    <div style="display: flex; gap: 8px; align-items: center;">
                                        <span style="font-weight: 700; color: var(--brand);">LKR 120,000</span>
                                        <span class="status-badge" style="background: #28a745; color: #fff;">Paid</span>
                                    </div>
                                </li>
                                <li>
                                    <div>
                                        <strong>Stage Props</strong>
                                        <div class="request-info">
                                            Category: Stage Design | Added by: Priyantha Silva | Date: 2024-12-12
                                        </div>
                                    </div>
                                    <div style="display: flex; gap: 8px; align-items: center;">
                                        <span style="font-weight: 700; color: var(--brand);">LKR 45,000</span>
                                        <span class="status-badge unassigned">Not Paid</span>
                                    </div>
                                </li>
                                <li>
                                    <div>
                                        <strong>Transportation</strong>
                                        <div class="request-info">
                                            Category: Logistics | Added by: Priyantha Silva | Date: 2024-12-14
                                        </div>
                                    </div>
                                    <div style="display: flex; gap: 8px; align-items: center;">
                                        <span style="font-weight: 700; color: var(--brand);">LKR 21,000</span>
                                        <span class="status-badge pending">Pending</span>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <!-- Budget by Category -->
                        <div class="card-section">
                            <h3>Budget by Category</h3>
                            <div class="drama-info">
                                <div class="service-info-item">
                                    <span class="service-info-label">Technical Services</span>
                                    <span class="service-info-value">LKR 150,000 (45%)</span>
                                </div>
                                <div class="service-info-item">
                                    <span class="service-info-label">Artistic Services</span>
                                    <span class="service-info-value">LKR 120,000 (36%)</span>
                                </div>
                                <div class="service-info-item">
                                    <span class="service-info-label">Stage Design</span>
                                    <span class="service-info-value">LKR 45,000 (13%)</span>
                                </div>
                                <div class="service-info-item">
                                    <span class="service-info-label">Logistics</span>
                                    <span class="service-info-value">LKR 21,000 (6%)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab: Theater Bookings -->
        <div id="theatersTab" class="tab-content">
            <div class="content">
                <div class="profile-container" style="grid-template-columns: 1fr;">
                    <div class="details">
                        <div class="card-section">
                            <h3>Theater Bookings</h3>
                            <ul>
                                <li>
                                    <div>
                                        <strong>Lionel Wendt Theatre</strong>
                                        <div class="request-info">
                                            Performance Date: 2024-12-30 | Time: 7:00 PM - 10:00 PM
                                        </div>
                                        <div class="request-info">
                                            Booked by: Priyantha Silva | Booking Date: 2024-11-28
                                        </div>
                                        <div class="request-info">
                                            Capacity: 500 seats | Booking Fee: LKR 150,000
                                        </div>
                                    </div>
                                    <div style="display: flex; gap: 8px; align-items: center;">
                                        <span class="status-badge assigned">Confirmed</span>
                                        <span class="status-badge" style="background: #28a745; color: #fff;">Paid</span>
                                    </div>
                                </li>
                                <li>
                                    <div>
                                        <strong>Tower Hall Theatre - Rehearsal</strong>
                                        <div class="request-info">
                                            Rehearsal Date: 2024-12-25 | Time: 3:00 PM - 6:00 PM
                                        </div>
                                        <div class="request-info">
                                            Booked by: Malini Fernando | Booking Date: 2024-12-15
                                        </div>
                                        <div class="request-info">
                                            Booking Fee: LKR 25,000
                                        </div>
                                    </div>
                                    <div style="display: flex; gap: 8px; align-items: center;">
                                        <span class="status-badge assigned">Confirmed</span>
                                        <span class="status-badge pending">Pending Payment</span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const dramaId = urlParams.get('drama_id') || 1;
        console.log('Current Drama ID:', dramaId);
    </script>
    <script src="/Rangamadala/public/assets/JS/view-services-budget.js"></script>
</body>
</html>
