<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Production Manager Dashboard - Rangamadala</title>
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
            <li class="active">
                <a href="dashboard.php?drama_id=1">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="manage_services.php?drama_id=1">
                    <i class="fas fa-briefcase"></i>
                    <span>Manage Services</span>
                </a>
            </li>
            <li>
                <a href="manage_budget.php?drama_id=1">
                    <i class="fas fa-chart-bar"></i>
                    <span>Budget Management</span>
                </a>
            </li>
            <li>
                <a href="book_theater.php?drama_id=1">
                    <i class="fas fa-theater-masks"></i>
                    <span>Theater Bookings</span>
                </a>
            </li>
            <li>
                <a href="manage_schedule.php?drama_id=1">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Service Schedule</span>
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
        <a href="../artist/profile.php" class="back-button">
            <i class="fas fa-arrow-left"></i>
            Back to Profile
        </a>

        <!-- Header -->
        <div class="header--wrapper">
            <div class="header--title">
                <span>Production Manager Dashboard</span>
                <h2>Sinhabahu</h2>
                <p style="color: var(--muted); font-size: 14px; margin-top: 8px;">
                    Historical Drama | Sinhala | Status: <span class="status-badge assigned">Active</span>
                </p>
            </div>
            <div class="user--info">
                <div class="role-badge">
                    <i class="fas fa-user-tie"></i>
                    Production Manager
                </div>
                <img src="../../assets/images/default-avatar.jpg" alt="Avatar">
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="stats-grid">
            <div class="stat-card" style="background: linear-gradient(135deg, var(--brand), var(--brand-strong));">
                <h3>LKR 800,000</h3>
                <p>Total Budget Allocated</p>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, var(--brand), var(--brand-strong));">
                <h3>LKR 336,000</h3>
                <p>Budget Used (42%)</p>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, var(--brand), var(--brand-strong));">
                <h3>12</h3>
                <p>Active Service Requests</p>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, var(--brand), var(--brand-strong));">
                <h3>4</h3>
                <p>Theater Bookings</p>
            </div>
        </div>

        <!-- Navigation Tab Bar -->
        <div class="nav-tabs-bar">
            <a href="dashboard.php?drama_id=1" class="nav-tab-btn active">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="manage_services.php?drama_id=1" class="nav-tab-btn">
                <i class="fas fa-briefcase"></i> Manage Services
            </a>
            <a href="manage_budget.php?drama_id=1" class="nav-tab-btn">
                <i class="fas fa-chart-bar"></i> Budget Management
            </a>
            <a href="book_theater.php?drama_id=1" class="nav-tab-btn">
                <i class="fas fa-theater-masks"></i> Theater Bookings
            </a>
            <a href="manage_schedule.php?drama_id=1" class="nav-tab-btn">
                <i class="fas fa-calendar-alt"></i> Service Schedule
            </a>
        </div>

        <!-- Content Sections -->
        <div class="content">
            <div class="profile-container" style="grid-template-columns: 1fr;">
                <div class="details">
                    <!-- Budget Overview Section -->
                    <div class="card-section">
                        <h3>
                            <i class="fas fa-wallet" style="color: var(--brand);"></i>
                            <span>Budget Overview</span>
                            <a href="manage_budget.php?drama_id=1" class="btn btn-primary" style="font-size: 12px; padding: 8px 16px;">
                                <i class="fas fa-pencil-alt"></i>
                                Manage Budget
                            </a>
                        </h3>
                        <div style="display: flex; align-items: center; gap: 20px; margin-top: 16px;">
                            <div style="flex: 1;">
                                <div style="background: #f0f0f0; border-radius: 10px; height: 30px; overflow: hidden;">
                                    <div style="background: linear-gradient(90deg, var(--brand), var(--brand-strong)); width: 42%; height: 100%; border-radius: 10px; transition: width 0.3s ease;"></div>
                                </div>
                                <p style="font-size: 12px; color: var(--muted); margin-top: 6px;">42% of budget used (LKR 336,000 of LKR 800,000)</p>
                            </div>
                        </div>
                        <div class="drama-info" style="margin-top: 16px;">
                            <div class="service-info-item">
                                <span class="service-info-label">Total Budget</span>
                                <span class="service-info-value">LKR 800,000</span>
                            </div>
                            <div class="service-info-item">
                                <span class="service-info-label">Budget Used</span>
                                <span class="service-info-value">LKR 336,000</span>
                            </div>
                            <div class="service-info-item">
                                <span class="service-info-label">Remaining Budget</span>
                                <span class="service-info-value">LKR 464,000</span>
                            </div>
                            <div class="service-info-item">
                                <span class="service-info-label">Budget Status</span>
                                <span class="service-info-value"><span class="status-badge assigned">On Track</span></span>
                            </div>
                        </div>
                    </div>

                    <!-- Service Requests Section -->
                    <div class="card-section">
                        <h3>
                            <span>Recent Service Requests</span>
                            <a href="manage_services.php?drama_id=1" class="btn btn-secondary" style="font-size: 12px; padding: 8px 16px;">
                                <i class="fas fa-arrow-right"></i>
                                View All Services
                            </a>
                        </h3>
                        <ul>
                            <li>
                                <div>
                                    <strong>Sound System Setup</strong>
                                    <div class="request-info">From: Sri Lanka Sound Services | Requested: 2024-12-20</div>
                                </div>
                                <span class="status-badge pending">Pending</span>
                            </li>
                            <li>
                                <div>
                                    <strong>Professional Lighting</strong>
                                    <div class="request-info">From: Colombo Lighting Studio | Cost: LKR 120,000</div>
                                </div>
                                <span class="status-badge assigned">Confirmed</span>
                            </li>
                            <li>
                                <div>
                                    <strong>Makeup & Costume</strong>
                                    <div class="request-info">From: Elite Makeup Artistry | Cost: LKR 85,000</div>
                                </div>
                                <span class="status-badge assigned">Confirmed</span>
                            </li>
                            <li>
                                <div>
                                    <strong>Stage Design & Props</strong>
                                    <div class="request-info">From: Creative Stage Solutions | Quote Pending</div>
                                </div>
                                <span class="status-badge requested">Quote Requested</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Theater Bookings Section -->
                    <div class="card-section">
                        <h3>
                            <span>Upcoming Theater Bookings</span>
                            <a href="book_theater.php?drama_id=1" class="btn btn-success" style="font-size: 12px; padding: 8px 16px;">
                                <i class="fas fa-plus"></i>
                                Book Theater
                            </a>
                        </h3>
                        <ul>
                            <li>
                                <div>
                                    <strong>Elphinstone Theatre</strong>
                                    <div class="request-info">Date: Jan 15, 2025 | Time: 7:00 PM - 10:00 PM | Cost: LKR 50,000</div>
                                </div>
                                <span class="status-badge assigned">Confirmed</span>
                            </li>
                            <li>
                                <div>
                                    <strong>Colombo Auditorium</strong>
                                    <div class="request-info">Date: Jan 22, 2025 | Time: 6:30 PM - 9:30 PM | Cost: LKR 45,000</div>
                                </div>
                                <span class="status-badge pending">Pending</span>
                            </li>
                            <li>
                                <div>
                                    <strong>Tower Hall Theatre</strong>
                                    <div class="request-info">Date: Jan 29, 2025 | Time: 7:00 PM - 10:00 PM | Inquiry Sent</div>
                                </div>
                                <span class="status-badge requested">Inquiry Sent</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Service Schedule Section -->
                    <div class="card-section">
                        <h3>
                            <span>Service Schedule</span>
                            <a href="manage_schedule.php?drama_id=1" class="btn btn-primary" style="font-size: 12px; padding: 8px 16px;">Manage Schedule</a>
                        </h3>
                        <ul id="serviceScheduleList">
                            <li>
                                <div>
                                    <strong>Sound System Installation</strong>
                                    <div class="request-info">Date: 2025-01-10 | Time: 9:00 AM | Venue: Elphinstone Theatre</div>
                                </div>
                                <span class="status-badge assigned">Scheduled</span>
                            </li>
                            <li>
                                <div>
                                    <strong>Lighting Setup & Testing</strong>
                                    <div class="request-info">Date: 2025-01-12 | Time: 2:00 PM | Venue: Elphinstone Theatre</div>
                                </div>
                                <span class="status-badge assigned">Scheduled</span>
                            </li>
                            <li>
                                <div>
                                    <strong>Costume Fitting Session</strong>
                                    <div class="request-info">Date: 2025-01-08 | Time: 10:00 AM | Venue: Studio</div>
                                </div>
                                <span class="status-badge pending">To be Confirmed</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card-section">
                        <h3>Quick Actions</h3>
                        <div class="permission-controls">
                            <a href="manage_services.php?drama_id=1" class="btn btn-primary">
                                <i class="fas fa-briefcase"></i>
                                Manage Services
                            </a>
                            <a href="manage_budget.php?drama_id=1" class="btn btn-success">
                                <i class="fas fa-chart-bar"></i>
                                Manage Budget
                            </a>
                            <a href="book_theater.php?drama_id=1" class="btn btn-warning">
                                <i class="fas fa-theater-masks"></i>
                                Book Theater
                            </a>
                            <a href="manage_schedule.php?drama_id=1" class="btn btn-secondary">
                                <i class="fas fa-calendar-alt"></i>
                                Service Schedule
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Get drama_id from URL parameter
        const urlParams = new URLSearchParams(window.location.search);
        const dramaId = urlParams.get('drama_id') || 1;
        
        // Mark active navigation tab based on current page
        const currentPage = window.location.pathname.split('/').pop();
        const navTabs = document.querySelectorAll('.nav-tab-btn');
        
        navTabs.forEach(tab => {
            // Remove active class from all tabs
            tab.classList.remove('active');
            
            // Add active class to matching tab
            const href = tab.getAttribute('href');
            if (href && href.includes(currentPage)) {
                tab.classList.add('active');
            }
        });
        
        // Special case: if on dashboard.php, mark dashboard tab as active
        if (currentPage === 'dashboard.php' || currentPage === '') {
            navTabs[0]?.classList.add('active');
        }
        
        console.log('Current Drama ID:', dramaId);
    </script>
    <script src="/Rangamadala/public/assets/JS/production-manager-dashboard.js"></script>
</body>
</html>
