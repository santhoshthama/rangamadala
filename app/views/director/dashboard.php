<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Director Dashboard - Sinhabahu - Rangamadala</title>
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
                <a href="<?= ROOT ?>/director/dashboard?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/director/drama_details?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>">
                    <i class="fas fa-film"></i>
                    <span>Drama Details</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/director/manage_roles?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>">
                    <i class="fas fa-users"></i>
                    <span>Artist Roles</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/director/assign_managers?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>">
                    <i class="fas fa-user-tie"></i>
                    <span>Production Manager</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/director/schedule_management?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Schedule</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/director/view_services_budget?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>">
                    <i class="fas fa-dollar-sign"></i>
                    <span>Services & Budget</span>
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
        <a href="<?= ROOT ?>/artistdashboard" class="back-button">
            <i class="fas fa-arrow-left"></i>
            Back to Profile
        </a>

        <!-- Header -->
        <div class="header--wrapper">
            <div class="header--title">
                <span>Director Dashboard</span>
                <h2>Sinhabahu</h2>
                <p style="color: var(--muted); font-size: 14px; margin-top: 8px;">
                    Historical Drama | Sinhala | Status: <span class="status-badge assigned">Active</span>
                </p>
            </div>
            <div class="user--info">
                <div class="role-badge">
                    <i class="fas fa-video"></i>
                    Director
                </div>
                <img src="../../assets/images/default-avatar.jpg" alt="Artist Avatar">
            </div>
        </div>

        <!-- Statistics Cards for THIS Drama -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3 id="totalRoles">15</h3>
                <p>Total Roles</p>
            </div>
            <div class="stat-card">
                <h3 id="filledRoles">12/15</h3>
                <p>Filled Roles</p>
            </div>
            <div class="stat-card">
                <h3 id="productionManagers">1</h3>
                <p>Production Manager</p>
            </div>
            <div class="stat-card">
                <h3 id="pendingApplications">8</h3>
                <p>Pending Applications</p>
            </div>
        </div>

        <!-- Navigation Tab Bar -->
        <div class="nav-tabs-bar">
            <a href="<?= ROOT ?>/director/dashboard?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>" class="nav-tab-btn active">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="<?= ROOT ?>/director/drama_details?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>" class="nav-tab-btn">
                <i class="fas fa-film"></i>  Edit Drama Details
            </a>
            <a href="<?= ROOT ?>/director/manage_roles?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>" class="nav-tab-btn">
                <i class="fas fa-users"></i> Artist Roles
            </a>
            <a href="<?= ROOT ?>/director/assign_managers?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>" class="nav-tab-btn">
                <i class="fas fa-user-tie"></i> Production Manager
            </a>
            <a href="<?= ROOT ?>/director/schedule_management?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>" class="nav-tab-btn">
                <i class="fas fa-calendar-alt"></i> Schedule
            </a>
            <a href="<?= ROOT ?>/director/view_services_budget?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>" class="nav-tab-btn">
                <i class="fas fa-dollar-sign"></i> Services & Budget
            </a>
        </div>

        <!-- Recent Dramas -->
        <div class="content">
            <div class="profile-container" style="grid-template-columns: 1fr;">
                <div class="details">
                    <!-- Drama Overview Card Section -->
                    <div class="card-section">
                        <h3>
                            <span>Drama Overview</span>
                            <a href="<?= ROOT ?>/director/drama_details?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>" class="btn btn-primary" style="font-size: 12px; padding: 8px 16px;">
                                <i class="fas fa-edit"></i>
                                Edit Details
                            </a>
                        </h3>
                        <div class="drama-info">
                            <div class="service-info-item">
                                <span class="service-info-label">Created</span>
                                <span class="service-info-value">2024-11-20</span>
                            </div>
                            <div class="service-info-item">
                                <span class="service-info-label">Genre</span>
                                <span class="service-info-value">Historical</span>
                            </div>
                            <div class="service-info-item">
                                <span class="service-info-label">Language</span>
                                <span class="service-info-value">Sinhala</span>
                            </div>
                            <div class="service-info-item">
                                <span class="service-info-label">Expected Budget</span>
                                <span class="service-info-value">LKR 800,000</span>
                            </div>
                            <div class="service-info-item">
                                <span class="service-info-label">Certificate Status</span>
                                <span class="service-info-value"><span class="status-badge assigned">Verified</span></span>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Role Applications -->
                    <div class="card-section">
                        <h3>
                            <span>Pending Role Applications</span>
                            <a href="<?= ROOT ?>/director/manage_roles?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>&tab=applications" class="btn btn-warning" style="font-size: 12px; padding: 8px 16px;">Review All</a>
                        </h3>
                        <ul id="pendingApplicationsList">
                            <li>
                                <div>
                                    <strong>Kasun Perera</strong>
                                    <div class="request-info">Applied for: Dancer Troupe Leader | Applied: 2024-12-18</div>
                                </div>
                                <div style="display: flex; gap: 8px;">
                                    <button class="btn btn-primary" style="font-size: 11px; padding: 6px 12px;" onclick="viewApplication(1)">
                                        <i class="fas fa-eye"></i>
                                        View
                                    </button>
                                    <button class="btn btn-success" style="font-size: 11px; padding: 6px 12px;" onclick="acceptApplication(1)">Accept</button>
                                    <button class="btn btn-danger" style="font-size: 11px; padding: 6px 12px;" onclick="rejectApplication(1)">Reject</button>
                                </div>
                            </li>
                            <li>
                                <div>
                                    <strong>Nimal Silva</strong>
                                    <div class="request-info">Applied for: Supporting Actor | Applied: 2024-12-17</div>
                                </div>
                                <div style="display: flex; gap: 8px;">
                                    <button class="btn btn-primary" style="font-size: 11px; padding: 6px 12px;" onclick="viewApplication(2)">
                                        <i class="fas fa-eye"></i>
                                        View
                                    </button>
                                    <button class="btn btn-success" style="font-size: 11px; padding: 6px 12px;" onclick="acceptApplication(2)">Accept</button>
                                    <button class="btn btn-danger" style="font-size: 11px; padding: 6px 12px;" onclick="rejectApplication(2)">Reject</button>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <!-- Artist Roles Summary -->
                    <div class="card-section">
                        <h3>
                            <span>Artist Roles Summary</span>
                            <a href="<?= ROOT ?>/director/manage_roles?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>" class="btn btn-primary" style="font-size: 12px; padding: 8px 16px;">
                                <i class="fas fa-users"></i>
                                Manage All Roles
                            </a>
                        </h3>
                        <ul>
                            <li>
                                <div>
                                    <strong>King Sinhabahu</strong>
                                    <div class="request-info">Salary: LKR 80,000</div>
                                </div>
                                <span class="status-badge assigned">Tharaka Rathnayake</span>
                            </li>
                            <li>
                                <div>
                                    <strong>Princess Suppadevi</strong>
                                    <div class="request-info">Salary: LKR 70,000</div>
                                </div>
                                <span class="status-badge assigned">Dilini Wickramasinghe</span>
                            </li>
                            <li>
                                <div>
                                    <strong>Dancer Troupe Leader</strong>
                                    <div class="request-info">Salary: LKR 50,000</div>
                                </div>
                                <span class="status-badge requested">Request Sent</span>
                            </li>
                            <li>
                                <div>
                                    <strong>Supporting Actor (3 positions)</strong>
                                    <div class="request-info">Salary: LKR 35,000 each</div>
                                </div>
                                <span class="status-badge unassigned">Vacant (5 Applications)</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Production Manager -->
                    <div class="card-section">
                        <h3>
                            <span>Production Manager</span>
                            <a href="<?= ROOT ?>/director/assign_managers?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>" class="btn btn-success" style="font-size: 12px; padding: 8px 16px;">
                                <i class="fas fa-user-plus"></i>
                                Change Manager
                            </a>
                        </h3>
                        <ul>
                            <li>
                                <div>
                                    <strong>Priyantha Silva</strong>
                                    <div class="request-info">Manages services, budget & theater bookings | Assigned: 2024-11-20</div>
                                </div>
                                <div style="display: flex; gap: 8px; align-items: center;">
                                    <span class="status-badge assigned">Active</span>
                                    <button class="btn btn-secondary" style="font-size: 11px; padding: 6px 12px;" onclick="viewManagerDetails()">
                                        <i class="fas fa-eye"></i>
                                        View Profile
                                    </button>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <!-- Upcoming Schedule -->
                    <div class="card-section">
                        <h3>
                            <span>Upcoming Schedule</span>
                            <a href="<?= ROOT ?>/director/schedule_management?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>" class="btn btn-primary" style="font-size: 12px; padding: 8px 16px;">Manage Schedule</a>
                        </h3>
                        <ul id="upcomingScheduleList">
                            <li>
                                <div>
                                    <strong>Interview - Role: Dancer Troupe Leader</strong>
                                    <div class="request-info">Date: 2024-12-23 | Time: 2:00 PM | Venue: Online (Zoom)</div>
                                </div>
                                <span class="status-badge assigned">Confirmed</span>
                            </li>
                            <li>
                                <div>
                                    <strong>Rehearsal - Act 1 Scene 3</strong>
                                    <div class="request-info">Date: 2024-12-25 | Time: 3:00 PM | Venue: Tower Hall Theatre</div>
                                </div>
                                <span class="status-badge pending">Pending</span>
                            </li>
                            <li>
                                <div>
                                    <strong>Production Meeting</strong>
                                    <div class="request-info">Date: 2024-12-28 | Time: 9:00 AM | Venue: Office</div>
                                </div>
                                <span class="status-badge assigned">Confirmed</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Services & Budget Overview -->
                    <div class="card-section">
                        <h3>
                            <span>Services & Budget Overview</span>
                            <a href="<?= ROOT ?>/director/view_services_budget?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>" class="btn btn-primary" style="font-size: 12px; padding: 8px 16px;">View Details</a>
                        </h3>
                        <div class="drama-info">
                            <div class="service-info-item">
                                <span class="service-info-label">Total Budget</span>
                                <span class="service-info-value">LKR 800,000</span>
                            </div>
                            <div class="service-info-item">
                                <span class="service-info-label">Budget Used</span>
                                <span class="service-info-value">LKR 336,000 (42%)</span>
                            </div>
                            <div class="service-info-item">
                                <span class="service-info-label">Services Booked</span>
                                <span class="service-info-value">7 services</span>
                            </div>
                            <div class="service-info-item">
                                <span class="service-info-label">Pending Payments</span>
                                <span class="service-info-value">LKR 125,000</span>
                            </div>
                        </div>
                        <div class="view-only-notice" style="margin-top: 15px;">
                            <i class="fas fa-info-circle"></i>
                            Budget is managed by Production Managers. You have view-only access.
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card-section">
                        <h3>Quick Actions</h3>
                        <div class="permission-controls">
                            <a href="<?= ROOT ?>/director/manage_roles?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>" class="btn btn-primary">
                                <i class="fas fa-users"></i>
                                Manage Roles
                            </a>
                            <a href="<?= ROOT ?>/director/search_artists?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>" class="btn btn-success">
                                <i class="fas fa-search"></i>
                                Search Artists
                            </a>
                            <a href="<?= ROOT ?>/director/schedule_management?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>" class="btn btn-warning">
                                <i class="fas fa-calendar-plus"></i>
                                Add Schedule
                            </a>
                            <a href="<?= ROOT ?>/director/assign_managers?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>" class="btn btn-secondary">
                                <i class="fas fa-user-plus"></i>
                                Assign Manager
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
        // Backend will use this to load drama-specific data
    </script>
    <script src="/Rangamadala/public/assets/JS/director-dashboard.js"></script>
</body>
</html>
