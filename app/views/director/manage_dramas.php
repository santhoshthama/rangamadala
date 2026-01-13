<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Dramas - Rangamadala</title>
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
                <a href="<?= ROOT ?>/director/dashboard">
=======
                <a href="dashboard.php">
>>>>>>> 4a7cd2f6e0c168dac5fa3f8773e569dc3f8aba4b
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="active">
<<<<<<< HEAD
                <a href="<?= ROOT ?>/director/manage_dramas">
=======
                <a href="manage_dramas.php">
>>>>>>> 4a7cd2f6e0c168dac5fa3f8773e569dc3f8aba4b
                    <i class="fas fa-film"></i>
                    <span>My Dramas</span>
                </a>
            </li>
            <li>
<<<<<<< HEAD
                <a href="<?= ROOT ?>/director/create_drama">
=======
                <a href="create_drama.php">
>>>>>>> 4a7cd2f6e0c168dac5fa3f8773e569dc3f8aba4b
                    <i class="fas fa-plus-circle"></i>
                    <span>Create Drama</span>
                </a>
            </li>
            <li>
<<<<<<< HEAD
                <a href="<?= ROOT ?>/director/search_artists">
=======
                <a href="search_artists.php">
>>>>>>> 4a7cd2f6e0c168dac5fa3f8773e569dc3f8aba4b
                    <i class="fas fa-search"></i>
                    <span>Search Artists</span>
                </a>
            </li>
            <li>
<<<<<<< HEAD
                <a href="<?= ROOT ?>/director/role_management">
=======
                <a href="role_management.php">
>>>>>>> 4a7cd2f6e0c168dac5fa3f8773e569dc3f8aba4b
                    <i class="fas fa-users"></i>
                    <span>Role Management</span>
                </a>
            </li>
            <li>
<<<<<<< HEAD
                <a href="<?= ROOT ?>/director/schedule_management">
=======
                <a href="schedule_management.php">
>>>>>>> 4a7cd2f6e0c168dac5fa3f8773e569dc3f8aba4b
                    <i class="fas fa-calendar-alt"></i>
                    <span>Schedule</span>
                </a>
            </li>
            <li>
<<<<<<< HEAD
                <a href="<?= ROOT ?>/director/assign_managers">
=======
                <a href="assign_managers.php">
>>>>>>> 4a7cd2f6e0c168dac5fa3f8773e569dc3f8aba4b
                    <i class="fas fa-user-tie"></i>
                    <span>Assign Managers</span>
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
        <a href="<?= ROOT ?>/director/dashboard" class="back-button">
=======
        <a href="dashboard.php" class="back-button">
>>>>>>> 4a7cd2f6e0c168dac5fa3f8773e569dc3f8aba4b
            <i class="fas fa-arrow-left"></i>
            Back to Dashboard
        </a>

        <!-- Header -->
        <div class="header--wrapper">
            <div class="header--title">
                <span>Director Panel</span>
                <h2>My Dramas</h2>
            </div>
            <div class="header-controls">
<<<<<<< HEAD
                <a href="<?= ROOT ?>/director/create_drama" class="btn btn-primary">
=======
                <a href="create_drama.php" class="btn btn-primary">
>>>>>>> 4a7cd2f6e0c168dac5fa3f8773e569dc3f8aba4b
                    <i class="fas fa-plus-circle"></i>
                    Create New Drama
                </a>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="search-section">
            <h3 style="margin-bottom: 20px; color: var(--ink);">
                <i class="fas fa-filter"></i>
                Filter Dramas
            </h3>
            <div class="search-filters">
                <div class="filter-group">
                    <label for="filterStatus">Status</label>
                    <select id="filterStatus">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="pending">Pending Approval</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="filterGenre">Genre</label>
                    <select id="filterGenre">
                        <option value="">All Genres</option>
                        <option value="tragedy">Tragedy</option>
                        <option value="comedy">Comedy</option>
                        <option value="historical">Historical</option>
                        <option value="social">Social Drama</option>
                        <option value="musical">Musical</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="searchTitle">Search by Title</label>
                    <input type="text" id="searchTitle" placeholder="Enter drama title...">
                </div>
            </div>
            <button class="btn btn-primary search-button">
                <i class="fas fa-search"></i>
                Apply Filters
            </button>
        </div>

        <!-- Dramas Grid -->
        <div class="artists-grid" id="dramasGrid">
            <!-- Drama Card 1 - Active -->
            <div class="artist-card">
                <div class="artist-header">
                    <h3 class="artist-name">Maname</h3>
                    <p class="artist-experience">Tragedy | Sinhala</p>
                </div>
                <div class="artist-body">
                    <div class="info-row">
                        <span class="info-label">Status</span>
                        <span class="status-badge assigned">Active</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Created</span>
                        <span class="info-value">2024-12-01</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Total Roles</span>
                        <span class="info-value">12</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Filled Roles</span>
                        <span class="info-value">8/12</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Managers</span>
                        <span class="info-value">2</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Budget Status</span>
                        <span class="info-value">65% Used</span>
                    </div>
                </div>
                <div class="artist-footer">
                    <button class="btn btn-primary" style="flex: 1;" onclick="viewDramaDetails(1)">
                        <i class="fas fa-eye"></i>
                        View Details
                    </button>
                    <button class="btn btn-secondary" onclick="editDrama(1)">
                        <i class="fas fa-edit"></i>
                    </button>
                </div>
            </div>

            <!-- Drama Card 2 - Active -->
            <div class="artist-card">
                <div class="artist-header">
                    <h3 class="artist-name">Sinhabahu</h3>
                    <p class="artist-experience">Historical | Sinhala</p>
                </div>
                <div class="artist-body">
                    <div class="info-row">
                        <span class="info-label">Status</span>
                        <span class="status-badge assigned">Active</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Created</span>
                        <span class="info-value">2024-11-20</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Total Roles</span>
                        <span class="info-value">15</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Filled Roles</span>
                        <span class="info-value">15/15</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Managers</span>
                        <span class="info-value">3</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Budget Status</span>
                        <span class="info-value">42% Used</span>
                    </div>
                </div>
                <div class="artist-footer">
                    <button class="btn btn-primary" style="flex: 1;" onclick="viewDramaDetails(2)">
                        <i class="fas fa-eye"></i>
                        View Details
                    </button>
                    <button class="btn btn-secondary" onclick="editDrama(2)">
                        <i class="fas fa-edit"></i>
                    </button>
                </div>
            </div>

            <!-- Drama Card 3 - Pending -->
            <div class="artist-card">
                <div class="artist-header" style="background: linear-gradient(135deg, rgba(255,193,7,0.9), rgba(255,193,7,0.75));">
                    <h3 class="artist-name">Mora</h3>
                    <p class="artist-experience">Social Drama | Sinhala</p>
                </div>
                <div class="artist-body">
                    <div class="info-row">
                        <span class="info-label">Status</span>
                        <span class="status-badge pending">Pending Approval</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Created</span>
                        <span class="info-value">2024-11-15</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Total Roles</span>
                        <span class="info-value">0</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Filled Roles</span>
                        <span class="info-value">0/0</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Managers</span>
                        <span class="info-value">0</span>
                    </div>
                </div>
                <div class="artist-footer">
                    <button class="btn btn-secondary" style="flex: 1;" onclick="viewDramaDetails(3)">
                        <i class="fas fa-eye"></i>
                        View Details
                    </button>
                    <button class="btn btn-danger" onclick="deleteDrama(3)">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>

            <!-- Drama Card 4 - Completed -->
            <div class="artist-card">
                <div class="artist-header" style="background: linear-gradient(135deg, rgba(108,117,125,0.9), rgba(108,117,125,0.75));">
                    <h3 class="artist-name">Pemato Jayathi</h3>
                    <p class="artist-experience">Comedy | Sinhala</p>
                </div>
                <div class="artist-body">
                    <div class="info-row">
                        <span class="info-label">Status</span>
                        <span class="status-badge" style="background: #6c757d; color: #fff;">Completed</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Created</span>
                        <span class="info-value">2024-06-10</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Completed</span>
                        <span class="info-value">2024-10-25</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Total Shows</span>
                        <span class="info-value">25</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Average Rating</span>
                        <span class="rating">★★★★☆ 4.5</span>
                    </div>
                </div>
                <div class="artist-footer">
                    <button class="btn btn-secondary" style="flex: 1;" onclick="viewDramaDetails(4)">
                        <i class="fas fa-eye"></i>
                        View Details
                    </button>
                </div>
            </div>
        </div>

        <!-- No Results Message (hidden by default) -->
        <div class="no-results" style="display: none;" id="noResults">
            <i class="fas fa-film"></i>
            <h3>No Dramas Found</h3>
            <p>Try adjusting your filters or create a new drama</p>
        </div>
    </main>

    <!-- View Drama Details Modal -->
    <div id="dramaDetailsModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2><i class="fas fa-film"></i> Drama Details</h2>
            <div class="modal-body" id="modalBody">
                <!-- Drama details will be loaded here dynamically -->
            </div>
            <div class="modal-actions">
                <button class="btn btn-secondary" onclick="closeModal()">Close</button>
            </div>
        </div>
    </div>

    <script src="/Rangamadala/public/assets/JS/manage-dramas.js"></script>
</body>
</html>
