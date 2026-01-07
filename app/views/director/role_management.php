<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Role Management - Rangamadala</title>
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
                <a href="dashboard.php">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="manage_dramas.php">
                    <i class="fas fa-film"></i>
                    <span>My Dramas</span>
                </a>
            </li>
            <li>
                <a href="create_drama.php">
                    <i class="fas fa-plus-circle"></i>
                    <span>Create Drama</span>
                </a>
            </li>
            <li>
                <a href="search_artists.php">
                    <i class="fas fa-search"></i>
                    <span>Search Artists</span>
                </a>
            </li>
            <li class="active">
                <a href="role_management.php">
                    <i class="fas fa-users"></i>
                    <span>Role Management</span>
                </a>
            </li>
            <li>
                <a href="schedule_management.php">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Schedule</span>
                </a>
            </li>
            <li>
                <a href="assign_managers.php">
                    <i class="fas fa-user-tie"></i>
                    <span>Assign Managers</span>
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
        <a href="dashboard.php" class="back-button">
            <i class="fas fa-arrow-left"></i>
            Back to Dashboard
        </a>

        <!-- Header -->
        <div class="header--wrapper">
            <div class="header--title">
                <span>Director Panel</span>
                <h2>Role Management</h2>
            </div>
            <div class="header-controls">
                <button class="btn btn-primary" onclick="openCreateRoleModal()">
                    <i class="fas fa-plus-circle"></i>
                    Create New Role
                </button>
                <button class="btn btn-success" onclick="openPublishVacancyModal()">
                    <i class="fas fa-bullhorn"></i>
                    Publish Vacancy
                </button>
            </div>
        </div>

        <!-- Drama Selection -->
        <div class="search-section" style="margin-bottom: 30px;">
            <div class="search-filters">
                <div class="filter-group">
                    <label for="selectDramaFilter">Filter by Drama</label>
                    <select id="selectDramaFilter" onchange="filterRolesByDrama()">
                        <option value="">All Dramas</option>
                        <option value="1">Maname</option>
                        <option value="2">Sinhabahu</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="filterRoleStatus">Filter by Status</label>
                    <select id="filterRoleStatus" onchange="filterRolesByStatus()">
                        <option value="">All Status</option>
                        <option value="filled">Filled</option>
                        <option value="vacant">Vacant</option>
                        <option value="pending">Pending Applications</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Tabs for Different Views -->
        <div class="tabs">
            <button class="tab-button active" onclick="showTab('roles')">
                <i class="fas fa-users"></i>
                All Roles
            </button>
            <button class="tab-button" onclick="showTab('applications')">
                <i class="fas fa-envelope"></i>
                Pending Applications (8)
            </button>
            <button class="tab-button" onclick="showTab('requests')">
                <i class="fas fa-paper-plane"></i>
                Sent Requests (5)
            </button>
        </div>

        <!-- Tab Content: All Roles -->
        <div id="rolesTab" class="tab-content active">
            <div class="content">
                <div class="profile-container" style="grid-template-columns: 1fr;">
                    <div class="details">
                        <!-- Drama: Maname -->
                        <div class="card-section">
                            <h3>
                                <span><i class="fas fa-film"></i> Maname</span>
                                <span class="status-badge assigned">Active</span>
                            </h3>
                            <ul>
                                <li>
                                    <div>
                                        <strong>Lead Actor</strong>
                                        <div class="request-info">Salary: LKR 75,000 | Status: Filled</div>
                                    </div>
                                    <div style="display: flex; gap: 8px; align-items: center;">
                                        <span class="status-badge assigned">Kasun Perera</span>
                                        <button class="btn btn-secondary" style="font-size: 11px; padding: 6px 12px;" onclick="viewRoleDetails(1)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-danger" style="font-size: 11px; padding: 6px 12px;" onclick="removeArtist(1)">
                                            <i class="fas fa-user-times"></i>
                                        </button>
                                    </div>
                                </li>
                                <li>
                                    <div>
                                        <strong>Supporting Actor</strong>
                                        <div class="request-info">Salary: LKR 45,000 | Status: Vacant</div>
                                    </div>
                                    <div style="display: flex; gap: 8px; align-items: center;">
                                        <span class="status-badge unassigned">Vacant</span>
                                        <button class="btn btn-primary" style="font-size: 11px; padding: 6px 12px;" onclick="viewApplications(2)">
                                            <i class="fas fa-envelope"></i> 3 Applications
                                        </button>
                                        <button class="btn btn-secondary" style="font-size: 11px; padding: 6px 12px;" onclick="editRole(2)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>
                                </li>
                                <li>
                                    <div>
                                        <strong>Villain</strong>
                                        <div class="request-info">Salary: LKR 60,000 | Status: Filled</div>
                                    </div>
                                    <div style="display: flex; gap: 8px; align-items: center;">
                                        <span class="status-badge assigned">Nimal Silva</span>
                                        <button class="btn btn-secondary" style="font-size: 11px; padding: 6px 12px;" onclick="viewRoleDetails(3)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-danger" style="font-size: 11px; padding: 6px 12px;" onclick="removeArtist(3)">
                                            <i class="fas fa-user-times"></i>
                                        </button>
                                    </div>
                                </li>
                                <li>
                                    <div>
                                        <strong>Comedian</strong>
                                        <div class="request-info">Salary: LKR 35,000 | Status: Vacant</div>
                                    </div>
                                    <div style="display: flex; gap: 8px; align-items: center;">
                                        <span class="status-badge unassigned">Vacant</span>
                                        <button class="btn btn-success" style="font-size: 11px; padding: 6px 12px;" onclick="searchArtistsForRole(4)">
                                            <i class="fas fa-search"></i> Find Artist
                                        </button>
                                        <button class="btn btn-secondary" style="font-size: 11px; padding: 6px 12px;" onclick="editRole(4)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <!-- Drama: Sinhabahu -->
                        <div class="card-section">
                            <h3>
                                <span><i class="fas fa-film"></i> Sinhabahu</span>
                                <span class="status-badge assigned">Active</span>
                            </h3>
                            <ul>
                                <li>
                                    <div>
                                        <strong>King Sinhabahu</strong>
                                        <div class="request-info">Salary: LKR 80,000 | Status: Filled</div>
                                    </div>
                                    <div style="display: flex; gap: 8px; align-items: center;">
                                        <span class="status-badge assigned">Tharaka Rathnayake</span>
                                        <button class="btn btn-secondary" style="font-size: 11px; padding: 6px 12px;" onclick="viewRoleDetails(5)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </li>
                                <li>
                                    <div>
                                        <strong>Princess Suppadevi</strong>
                                        <div class="request-info">Salary: LKR 70,000 | Status: Filled</div>
                                    </div>
                                    <div style="display: flex; gap: 8px; align-items: center;">
                                        <span class="status-badge assigned">Dilini Wickramasinghe</span>
                                        <button class="btn btn-secondary" style="font-size: 11px; padding: 6px 12px;" onclick="viewRoleDetails(6)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </li>
                                <li>
                                    <div>
                                        <strong>Dancer Troupe Leader</strong>
                                        <div class="request-info">Salary: LKR 50,000 | Status: Request Sent</div>
                                    </div>
                                    <div style="display: flex; gap: 8px; align-items: center;">
                                        <span class="status-badge requested">Awaiting Response</span>
                                        <button class="btn btn-secondary" style="font-size: 11px; padding: 6px 12px;" onclick="viewRoleDetails(7)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Content: Pending Applications -->
        <div id="applicationsTab" class="tab-content">
            <div class="content">
                <div class="profile-container" style="grid-template-columns: 1fr;">
                    <div class="details">
                        <div class="card-section">
                            <h3>Pending Applications</h3>
                            <ul>
                                <li>
                                    <div>
                                        <strong>Kasun Perera</strong>
                                        <div class="request-info">Role: Supporting Actor | Drama: Maname | Applied: 2024-12-18</div>
                                    </div>
                                    <div style="display: flex; gap: 8px;">
                                        <button class="btn btn-primary" style="font-size: 11px; padding: 6px 12px;" onclick="viewApplication(1)">
                                            <i class="fas fa-eye"></i>
                                            View
                                        </button>
                                        <button class="btn btn-success" style="font-size: 11px; padding: 6px 12px;" onclick="acceptApplication(1)">
                                            <i class="fas fa-check"></i>
                                            Accept
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
                                        <div class="request-info">Role: Comedian | Drama: Maname | Applied: 2024-12-17</div>
                                    </div>
                                    <div style="display: flex; gap: 8px;">
                                        <button class="btn btn-primary" style="font-size: 11px; padding: 6px 12px;" onclick="viewApplication(2)">
                                            <i class="fas fa-eye"></i>
                                            View
                                        </button>
                                        <button class="btn btn-success" style="font-size: 11px; padding: 6px 12px;" onclick="acceptApplication(2)">
                                            <i class="fas fa-check"></i>
                                            Accept
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
                            <h3>Sent Role Requests</h3>
                            <ul>
                                <li>
                                    <div>
                                        <strong>Samantha Fernando</strong>
                                        <div class="request-info">Role: Dancer Troupe Leader | Drama: Sinhabahu | Sent: 2024-12-19</div>
                                    </div>
                                    <div style="display: flex; gap: 8px; align-items: center;">
                                        <span class="status-badge requested">Pending</span>
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
            <h2><i class="fas fa-plus-circle"></i> Create New Role</h2>
            <div class="modal-body">
                <form id="createRoleForm">
                    <div class="form-group">
                        <label for="roleDrama">Select Drama *</label>
                        <select id="roleDrama" required>
                            <option value="">Choose a drama...</option>
                            <option value="1">Maname</option>
                            <option value="2">Sinhabahu</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="roleName">Role Name *</label>
                        <input type="text" id="roleName" required placeholder="e.g., Lead Actor, Villain">
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

    <!-- Publish Vacancy Modal -->
    <div id="publishVacancyModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closePublishVacancyModal()">&times;</span>
            <h2><i class="fas fa-bullhorn"></i> Publish Role Vacancy</h2>
            <div class="modal-body">
                <div class="info-box">
                    <i class="fas fa-info-circle"></i>
                    Publishing a vacancy will make it visible to all artists on the platform.
                </div>
                <form id="publishVacancyForm">
                    <div class="form-group">
                        <label for="vacancyDrama">Select Drama *</label>
                        <select id="vacancyDrama" required>
                            <option value="">Choose a drama...</option>
                            <option value="1">Maname</option>
                            <option value="2">Sinhabahu</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="vacancyRole">Select Role *</label>
                        <select id="vacancyRole" required>
                            <option value="">Choose a role...</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="applicationDeadline">Application Deadline *</label>
                        <input type="date" id="applicationDeadline" required>
                    </div>
                    <div class="form-group">
                        <label for="vacancyNotes">Additional Notes</label>
                        <textarea id="vacancyNotes" placeholder="Any additional information for applicants..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-actions">
                <button class="btn btn-success" onclick="submitPublishVacancy()">
                    <i class="fas fa-bullhorn"></i>
                    Publish Vacancy
                </button>
                <button class="btn btn-secondary" onclick="closePublishVacancyModal()">Cancel</button>
            </div>
        </div>
    </div>

    <script src="/Rangamadala/public/assets/JS/role-management.js"></script>
</body>
</html>
