<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Production Managers - Rangamadala</title>
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
            <li class="active">
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
        <a href="<?= ROOT ?>/director/dashboard?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>" class="back-button">
            <i class="fas fa-arrow-left"></i>
            Back to Dashboard
        </a>

        <!-- Header -->
        <div class="header--wrapper">
            <div class="header--title">
                <span>Sinhabahu</span>
                <h2>Production Manager</h2>
            </div>
        </div>

        <!-- Info Box -->
        <div class="info-box" style="margin-bottom: 30px;">
            <i class="fas fa-info-circle"></i>
            <strong>Note:</strong> Each drama has one Production Manager who manages services, budget, and theater bookings. You can assign or change the manager below.
        </div>

        <!-- Content -->
        <div class="content">
            <div class="profile-container" style="grid-template-columns: 1fr;">
                <div class="details">
                    <!-- Current Manager -->
                    <div class="card-section">
                        <h3>
                            <span><i class="fas fa-user-tie"></i> Current Production Manager</span>
                            <button class="btn btn-success" style="font-size: 12px; padding: 8px 16px;" onclick="openAssignManagerModal()">
                                <i class="fas fa-user-plus"></i>
                                Change Manager
                            </button>
                        </h3>
                        <ul>
                            <li>
                                <div>
                                    <strong>Priyantha Silva</strong>
                                    <div class="request-info">
                                        Artist ID: ART-1845 | Assigned: 2024-11-20 | Experience: 10 years
                                    </div>
                                    <div class="request-info">
                                        <strong>Manages:</strong> Services, Budget, Theater Bookings, Payments
                                    </div>
                                </div>
                                <div style="display: flex; gap: 8px; align-items: center;">
                                    <span class="status-badge assigned">Active</span>
                                    <button class="btn btn-primary" style="font-size: 11px; padding: 6px 12px;" onclick="viewManagerDetails()">
                                        <i class="fas fa-eye"></i>
                                        View Profile
                                    </button>
                                    <button class="btn btn-danger" style="font-size: 11px; padding: 6px 12px;" onclick="removeManager()">
                                        <i class="fas fa-user-times"></i>
                                        Remove
                                    </button>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <!-- Manager Activity -->
                    <div class="card-section">
                        <h3><i class="fas fa-chart-line"></i> Recent Activity</h3>
                        <ul>
                            <li>
                                <div>
                                    <strong>Booked Tower Hall Theatre</strong>
                                    <div class="request-info">Rehearsal on Dec 25, 2024 | 3:00 PM - 6:00 PM</div>
                                </div>
                                <span class="info-text" style="font-size: 11px;">2 hours ago</span>
                            </li>
                            <li>
                                <div>
                                    <strong>Requested Lighting Service</strong>
                                    <div class="request-info">From Bright Lights Co. | Estimated: LKR 45,000</div>
                                </div>
                                <span class="info-text" style="font-size: 11px;">5 hours ago</span>
                            </li>
                            <li>
                                <div>
                                    <strong>Added Budget Item</strong>
                                    <div class="request-info">Stage Props - LKR 25,000</div>
                                </div>
                                <span class="info-text" style="font-size: 11px;">Yesterday</span>
                            </li>
                            <li>
                                <div>
                                    <strong>Updated Payment Status</strong>
                                    <div class="request-info">Makeup Service - Marked as Paid</div>
                                </div>
                                <span class="info-text" style="font-size: 11px;">2 days ago</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Permissions & Access -->
                    <div class="card-section">
                        <h3><i class="fas fa-key"></i> Manager Permissions</h3>
                        <div class="drama-info">
                            <div class="service-info-item">
                                <span class="service-info-label">Services Management</span>
                                <span class="service-info-value"><span class="status-badge assigned">Full Access</span></span>
                            </div>
                            <div class="service-info-item">
                                <span class="service-info-label">Budget Management</span>
                                <span class="service-info-value"><span class="status-badge assigned">Full Access</span></span>
                            </div>
                            <div class="service-info-item">
                                <span class="service-info-label">Theater Bookings</span>
                                <span class="service-info-value"><span class="status-badge assigned">Full Access</span></span>
                            </div>
                            <div class="service-info-item">
                                <span class="service-info-label">Payment Tracking</span>
                                <span class="service-info-value"><span class="status-badge assigned">Full Access</span></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Assign Manager Modal -->
    <div id="assignManagerModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeAssignManagerModal()">&times;</span>
            <h2><i class="fas fa-user-plus"></i> Assign Production Manager</h2>
            <div class="modal-body">
                <div class="info-box">
                    <i class="fas fa-info-circle"></i>
                    Only verified artists can be assigned as Production Manager. This will replace the current manager.
                </div>
                <form id="assignManagerForm">
                    <div class="form-group">
                        <label for="searchArtist">Search Artist *</label>
                        <input type="text" id="searchArtist" placeholder="Enter artist name or ID..." onkeyup="searchArtists()">
                        <span class="info-text">Start typing to search for artists</span>
                    </div>

                    <div id="artistSearchResults" style="display: none; margin-bottom: 20px; max-height: 200px; overflow-y: auto; border: 1px solid var(--border); border-radius: 8px; padding: 10px;">
                        <!-- Search results will appear here -->
                    </div>

                    <div class="form-group">
                        <label for="selectedArtist">Selected Artist</label>
                        <input type="text" id="selectedArtist" readonly placeholder="No artist selected">
                        <input type="hidden" id="selectedArtistId">
                    </div>

                    <div class="form-group">
                        <label for="managerNotes">Notes (Optional)</label>
                        <textarea id="managerNotes" placeholder="Add any special instructions or notes..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-actions">
                <button class="btn btn-primary" onclick="submitAssignManager()">
                    <i class="fas fa-check"></i>
                    Assign Manager
                </button>
                <button class="btn btn-secondary" onclick="closeAssignManagerModal()">Cancel</button>
            </div>
        </div>
    </div>

    <!-- Manager Details Modal -->
    <div id="managerDetailsModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeManagerDetailsModal()">&times;</span>
            <h2><i class="fas fa-user-tie"></i> Manager Details</h2>
            <div class="modal-body" id="managerDetailsBody">
                <!-- Manager details will be loaded here -->
            </div>
            <div class="modal-actions">
                <button class="btn btn-secondary" onclick="closeManagerDetailsModal()">Close</button>
            </div>
        </div>
    </div>

    <script>
        // Get drama_id from URL parameter
        const urlParams = new URLSearchParams(window.location.search);
        const dramaId = urlParams.get('drama_id') || 1;
        console.log('Current Drama ID:', dramaId);
    </script>
    <script src="/Rangamadala/public/assets/JS/assign-managers.js"></script>
</body>
</html>
