<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Artist Roles - Rangamadala</title>
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
            <li class="active">
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
                <h2>Manage Artist Roles</h2>
            </div>
            <div class="header-controls">
                <button class="btn btn-primary" onclick="openCreateRoleModal()">
                    <i class="fas fa-plus-circle"></i>
                    Create Role
                </button>
                <a href="<?= ROOT ?>/director/search_artists?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>" class="btn btn-success">
                    <i class="fas fa-search"></i>
                    Search Artists
                </a>
            </div>
        </div>

        <!-- Tabs for Different Views -->
        <div class="tabs">
            <button class="tab-button active" onclick="showTab('roles')">
                <i class="fas fa-users"></i>
                All Roles (15)
            </button>
            <button class="tab-button" onclick="showTab('applications')">
                <i class="fas fa-envelope"></i>
                Applications (8)
            </button>
            <button class="tab-button" onclick="showTab('requests')">
                <i class="fas fa-paper-plane"></i>
                Sent Requests (3)
            </button>
        </div>

        <!-- Tab Content: All Roles -->
        <div id="rolesTab" class="tab-content active">
            <div class="content">
                <div class="profile-container" style="grid-template-columns: 1fr;">
                    <div class="details">
                        <!-- Filled Roles -->
                        <div class="card-section">
                            <h3>
                                <span><i class="fas fa-check-circle"></i> Filled Roles (12)</span>
                            </h3>
                            <ul>
                                <li>
                                    <div>
                                        <strong>King Sinhabahu (Lead Role)</strong>
                                        <div class="request-info">
                                            Artist: Tharaka Rathnayake | Salary: LKR 80,000 | Assigned: 2024-11-22
                                        </div>
                                    </div>
                                    <div style="display: flex; gap: 8px; align-items: center;">
                                        <span class="status-badge assigned">Filled</span>
                                        <button class="btn btn-primary" style="font-size: 11px; padding: 6px 12px;" onclick="viewRoleDetails(1)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-secondary" style="font-size: 11px; padding: 6px 12px;" onclick="editRole(1)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-danger" style="font-size: 11px; padding: 6px 12px;" onclick="removeArtist(1)">
                                            <i class="fas fa-user-times"></i>
                                        </button>
                                    </div>
                                </li>
                                <li>
                                    <div>
                                        <strong>Princess Suppadevi (Lead Role)</strong>
                                        <div class="request-info">
                                            Artist: Dilini Wickramasinghe | Salary: LKR 70,000 | Assigned: 2024-11-23
                                        </div>
                                    </div>
                                    <div style="display: flex; gap: 8px; align-items: center;">
                                        <span class="status-badge assigned">Filled</span>
                                        <button class="btn btn-primary" style="font-size: 11px; padding: 6px 12px;" onclick="viewRoleDetails(2)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-secondary" style="font-size: 11px; padding: 6px 12px;" onclick="editRole(2)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-danger" style="font-size: 11px; padding: 6px 12px;" onclick="removeArtist(2)">
                                            <i class="fas fa-user-times"></i>
                                        </button>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <!-- Roles with Pending Requests -->
                        <div class="card-section">
                            <h3>
                                <span><i class="fas fa-clock"></i> Awaiting Artist Response (1)</span>
                            </h3>
                            <ul>
                                <li>
                                    <div>
                                        <strong>Dancer Troupe Leader</strong>
                                        <div class="request-info">
                                            Request sent to: Samantha Fernando | Salary: LKR 50,000 | Sent: 2024-12-19
                                        </div>
                                        <div class="request-info">
                                            <strong>Status:</strong> Artist has not yet responded to your role request
                                        </div>
                                    </div>
                                    <div style="display: flex; gap: 8px; align-items: center;">
                                        <span class="status-badge requested">Pending Response</span>
                                        <button class="btn btn-primary" style="font-size: 11px; padding: 6px 12px;" onclick="viewRequest(1)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-danger" style="font-size: 11px; padding: 6px 12px;" onclick="cancelRequest(1)">
                                            <i class="fas fa-times"></i>
                                            Cancel Request
                                        </button>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <!-- Vacant Roles -->
                        <div class="card-section">
                            <h3>
                                <span><i class="fas fa-user-slash"></i> Vacant Roles (2)</span>
                            </h3>
                            <ul>
                                <li>
                                    <div>
                                        <strong>Supporting Actor - Court Member (3 positions)</strong>
                                        <div class="request-info">
                                            Salary: LKR 35,000 each | Status: 5 applications received | Published as Vacancy
                                        </div>
                                    </div>
                                    <div style="display: flex; gap: 8px; align-items: center;">
                                        <span class="status-badge unassigned">Vacant</span>
                                        <button class="btn btn-warning" style="font-size: 11px; padding: 6px 12px;" onclick="viewApplications(3)">
                                            <i class="fas fa-envelope"></i> 5 Applications
                                        </button>
                                        <button class="btn btn-success" style="font-size: 11px; padding: 6px 12px;" onclick="searchArtistsForRole(3)">
                                            <i class="fas fa-search"></i> Find Artist
                                        </button>
                                        <button class="btn btn-secondary" style="font-size: 11px; padding: 6px 12px;" onclick="editRole(3)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>
                                </li>
                                <li>
                                    <div>
                                        <strong>Musician - Drum Player</strong>
                                        <div class="request-info">
                                            Salary: LKR 30,000 | Status: 2 applications received
                                        </div>
                                    </div>
                                    <div style="display: flex; gap: 8px; align-items: center;">
                                        <span class="status-badge unassigned">Vacant</span>
                                        <button class="btn btn-warning" style="font-size: 11px; padding: 6px 12px;" onclick="viewApplications(4)">
                                            <i class="fas fa-envelope"></i> 2 Applications
                                        </button>
                                        <button class="btn btn-primary" style="font-size: 11px; padding: 6px 12px;" onclick="publishVacancy(4)">
                                            <i class="fas fa-bullhorn"></i> Publish
                                        </button>
                                        <button class="btn btn-secondary" style="font-size: 11px; padding: 6px 12px;" onclick="editRole(4)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Content: Applications -->
        <div id="applicationsTab" class="tab-content">
            <div class="content">
                <div class="profile-container" style="grid-template-columns: 1fr;">
                    <div class="details">
                        <div class="card-section">
                            <h3>Applications from Artists</h3>
                            <div class="info-box">
                                <i class="fas fa-info-circle"></i>
                                Artists have applied for roles in your drama. Review and accept/reject their applications.
                            </div>
                            <ul>
                                <li>
                                    <div>
                                        <strong>Kasun Perera</strong>
                                        <div class="request-info">
                                            Applied for: Supporting Actor - Court Member | Applied: 2024-12-18
                                        </div>
                                        <div class="request-info">
                                            Experience: 8 years | Specialization: Lead Actor | Rating: ★★★★★ 4.8
                                        </div>
                                    </div>
                                    <div style="display: flex; gap: 8px;">
                                        <button class="btn btn-primary" style="font-size: 11px; padding: 6px 12px;" onclick="viewApplicationDetails(1)">
                                            <i class="fas fa-user"></i>
                                            View Profile
                                        </button>
                                        <button class="btn btn-success" style="font-size: 11px; padding: 6px 12px;" onclick="acceptApplication(1)">
                                            <i class="fas fa-check"></i>
                                            Accept & Assign
                                        </button>
                                        <button class="btn btn-danger" style="font-size: 11px; padding: 6px 12px;" onclick="rejectApplication(1)">
                                            <i class="fas fa-times"></i>
                                            Reject
                                        </button>
                                    </div>
                                </li>
                                <li>
                                    <div>
                                        <strong>Ruwan Jayasinghe</strong>
                                        <div class="request-info">
                                            Applied for: Musician - Drum Player | Applied: 2024-12-17
                                        </div>
                                        <div class="request-info">
                                            Experience: 3 years | Specialization: Comedian | Rating: ★★★★☆ 4.3
                                        </div>
                                    </div>
                                    <div style="display: flex; gap: 8px;">
                                        <button class="btn btn-primary" style="font-size: 11px; padding: 6px 12px;" onclick="viewApplicationDetails(2)">
                                            <i class="fas fa-user"></i>
                                            View Profile
                                        </button>
                                        <button class="btn btn-success" style="font-size: 11px; padding: 6px 12px;" onclick="acceptApplication(2)">
                                            <i class="fas fa-check"></i>
                                            Accept & Assign
                                        </button>
                                        <button class="btn btn-danger" style="font-size: 11px; padding: 6px 12px;" onclick="rejectApplication(2)">
                                            <i class="fas fa-times"></i>
                                            Reject
                                        </button>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Content: Sent Requests -->
        <div id="requestsTab" class="tab-content">
            <div class="content">
                <div class="profile-container" style="grid-template-columns: 1fr;">
                    <div class="details">
                        <div class="card-section">
                            <h3>Role Requests Sent to Artists</h3>
                            <div class="info-box">
                                <i class="fas fa-info-circle"></i>
                                You have sent role requests to these artists. Waiting for them to accept or reject.
                            </div>
                            <ul>
                                <li>
                                    <div>
                                        <strong>Samantha Fernando</strong>
                                        <div class="request-info">
                                            Role: Dancer Troupe Leader | Salary: LKR 50,000 | Sent: 2024-12-19
                                        </div>
                                        <div class="request-info">
                                            Experience: 12 years | Specialization: Dancer | Rating: ★★★★★ 4.9
                                        </div>
                                    </div>
                                    <div style="display: flex; gap: 8px; align-items: center;">
                                        <span class="status-badge requested">Awaiting Response</span>
                                        <button class="btn btn-secondary" style="font-size: 11px; padding: 6px 12px;" onclick="viewRequest(1)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-danger" style="font-size: 11px; padding: 6px 12px;" onclick="cancelRequest(1)">
                                            <i class="fas fa-times"></i>
                                            Cancel
                                        </button>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Create Role Modal -->
    <div id="createRoleModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeCreateRoleModal()">&times;</span>
            <h2><i class="fas fa-plus-circle"></i> Create New Role for Sinhabahu</h2>
            <div class="modal-body">
                <form id="createRoleForm">
                    <div class="form-group">
                        <label for="roleName">Role Name *</label>
                        <input type="text" id="roleName" required placeholder="e.g., Lead Actor, Villain, Dancer">
                    </div>
                    <div class="form-group">
                        <label for="roleDescription">Role Description *</label>
                        <textarea id="roleDescription" required placeholder="Describe the character and requirements..."></textarea>
                    </div>
                    <div class="form-group">
                        <label for="roleSalary">Salary (LKR)</label>
                        <input type="number" id="roleSalary" placeholder="e.g., 50000">
                    </div>
                    <div class="form-group">
                        <label for="roleRequirements">Special Requirements</label>
                        <textarea id="roleRequirements" placeholder="Age range, experience, skills needed..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-actions">
                <button class="btn btn-primary" onclick="submitCreateRole()">
                    <i class="fas fa-check"></i>
                    Create Role
                </button>
                <button class="btn btn-secondary" onclick="closeCreateRoleModal()">Cancel</button>
            </div>
        </div>
    </div>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const dramaId = urlParams.get('drama_id') || 1;
        
        function showTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
            
            document.getElementById(tabName + 'Tab').classList.add('active');
            event.target.classList.add('active');
        }
        
        function openCreateRoleModal() {
            document.getElementById('createRoleModal').style.display = 'block';
        }
        
        function closeCreateRoleModal() {
            document.getElementById('createRoleModal').style.display = 'none';
        }
        
        function openSearchArtistsModal() {
            window.location.href = 'search_artists.php?drama_id=' + dramaId;
        }
        
        function openPublishVacancyModal() {
            alert('Publish vacancy modal will open here');
        }
    </script>
    <script src="/Rangamadala/public/assets/JS/manage-roles.js"></script>
</body>
</html>
