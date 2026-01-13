<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Artists - Rangamadala</title>
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
            <li class="active">
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
            <li>
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
        <a href="dashboard.php" class="back-button">
            <i class="fas fa-arrow-left"></i>
            Back to Dashboard
        </a>

        <!-- Header -->
        <div class="header--wrapper">
            <div class="header--title">
                <span>Director Panel</span>
                <h2>Search Artists</h2>
            </div>
        </div>

        <!-- Search Section -->
        <div class="search-section">
            <h3 style="margin-bottom: 20px; color: var(--ink);">
                <i class="fas fa-search"></i>
                Find Talent for Your Drama
            </h3>
            <div class="search-filters">
                <div class="filter-group">
                    <label for="searchName">Artist Name</label>
                    <input type="text" id="searchName" placeholder="Search by name...">
                </div>
                <div class="filter-group">
                    <label for="filterExperience">Experience Level</label>
                    <select id="filterExperience">
                        <option value="">All Levels</option>
                        <option value="beginner">Beginner (0-2 years)</option>
                        <option value="intermediate">Intermediate (2-5 years)</option>
                        <option value="experienced">Experienced (5-10 years)</option>
                        <option value="expert">Expert (10+ years)</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="filterSpecialization">Specialization</label>
                    <select id="filterSpecialization">
                        <option value="">All Specializations</option>
                        <option value="lead_actor">Lead Actor</option>
                        <option value="supporting_actor">Supporting Actor</option>
                        <option value="comedian">Comedian</option>
                        <option value="dancer">Dancer</option>
                        <option value="singer">Singer</option>
                        <option value="choreographer">Choreographer</option>
                        <option value="music_director">Music Director</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="filterAvailability">Availability</label>
                    <select id="filterAvailability">
                        <option value="">All</option>
                        <option value="available">Available</option>
                        <option value="busy">Busy</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="filterRating">Minimum Rating</label>
                    <select id="filterRating">
                        <option value="">Any Rating</option>
                        <option value="4">4+ Stars</option>
                        <option value="3">3+ Stars</option>
                        <option value="2">2+ Stars</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="filterLocation">Location</label>
                    <input type="text" id="filterLocation" placeholder="City or District...">
                </div>
            </div>
            <button class="btn btn-primary search-button" onclick="searchArtists()">
                <i class="fas fa-search"></i>
                Search Artists
            </button>
        </div>

        <!-- Results Count -->
        <div style="margin-bottom: 20px; color: var(--muted);" id="resultsCount">
            Showing <strong>6</strong> artists
        </div>

        <!-- Artists Grid -->
        <div class="artists-grid" id="artistsGrid">
            <!-- Artist Card 1 -->
            <div class="artist-card">
                <div class="artist-header">
                    <img src="../../assets/images/default-avatar.jpg" alt="Artist" class="artist-avatar">
                    <h3 class="artist-name">Kasun Perera</h3>
                    <p class="artist-experience">8 Years Experience</p>
                </div>
                <div class="artist-body">
                    <div class="info-row">
                        <span class="info-label">Specialization</span>
                        <span class="info-value">Lead Actor</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Location</span>
                        <span class="info-value">Colombo</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Dramas Done</span>
                        <span class="info-value">25</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Rating</span>
                        <span class="rating">★★★★★ 4.8</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Status</span>
                        <span class="status-badge assigned">Available</span>
                    </div>
                </div>
                <div class="artist-footer">
                    <button class="btn btn-primary" style="flex: 1;" onclick="viewArtistProfile(1)">
                        <i class="fas fa-user"></i>
                        View Profile
                    </button>
                    <button class="btn btn-success" onclick="sendRoleRequest(1)">
                        <i class="fas fa-paper-plane"></i>
                        Request
                    </button>
                </div>
            </div>

            <!-- Artist Card 2 -->
            <div class="artist-card">
                <div class="artist-header">
                    <img src="../../assets/images/default-avatar.jpg" alt="Artist" class="artist-avatar">
                    <h3 class="artist-name">Nimal Silva</h3>
                    <p class="artist-experience">5 Years Experience</p>
                </div>
                <div class="artist-body">
                    <div class="info-row">
                        <span class="info-label">Specialization</span>
                        <span class="info-value">Supporting Actor</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Location</span>
                        <span class="info-value">Kandy</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Dramas Done</span>
                        <span class="info-value">18</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Rating</span>
                        <span class="rating">★★★★☆ 4.5</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Status</span>
                        <span class="status-badge assigned">Available</span>
                    </div>
                </div>
                <div class="artist-footer">
                    <button class="btn btn-primary" style="flex: 1;" onclick="viewArtistProfile(2)">
                        <i class="fas fa-user"></i>
                        View Profile
                    </button>
                    <button class="btn btn-success" onclick="sendRoleRequest(2)">
                        <i class="fas fa-paper-plane"></i>
                        Request
                    </button>
                </div>
            </div>

            <!-- Artist Card 3 -->
            <div class="artist-card">
                <div class="artist-header">
                    <img src="../../assets/images/default-avatar.jpg" alt="Artist" class="artist-avatar">
                    <h3 class="artist-name">Samantha Fernando</h3>
                    <p class="artist-experience">12 Years Experience</p>
                </div>
                <div class="artist-body">
                    <div class="info-row">
                        <span class="info-label">Specialization</span>
                        <span class="info-value">Dancer</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Location</span>
                        <span class="info-value">Galle</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Dramas Done</span>
                        <span class="info-value">42</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Rating</span>
                        <span class="rating">★★★★★ 4.9</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Status</span>
                        <span class="status-badge pending">Busy</span>
                    </div>
                </div>
                <div class="artist-footer">
                    <button class="btn btn-primary" style="flex: 1;" onclick="viewArtistProfile(3)">
                        <i class="fas fa-user"></i>
                        View Profile
                    </button>
                    <button class="btn btn-success" onclick="sendRoleRequest(3)">
                        <i class="fas fa-paper-plane"></i>
                        Request
                    </button>
                </div>
            </div>

            <!-- Artist Card 4 -->
            <div class="artist-card">
                <div class="artist-header">
                    <img src="../../assets/images/default-avatar.jpg" alt="Artist" class="artist-avatar">
                    <h3 class="artist-name">Ruwan Jayasinghe</h3>
                    <p class="artist-experience">3 Years Experience</p>
                </div>
                <div class="artist-body">
                    <div class="info-row">
                        <span class="info-label">Specialization</span>
                        <span class="info-value">Comedian</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Location</span>
                        <span class="info-value">Colombo</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Dramas Done</span>
                        <span class="info-value">12</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Rating</span>
                        <span class="rating">★★★★☆ 4.3</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Status</span>
                        <span class="status-badge assigned">Available</span>
                    </div>
                </div>
                <div class="artist-footer">
                    <button class="btn btn-primary" style="flex: 1;" onclick="viewArtistProfile(4)">
                        <i class="fas fa-user"></i>
                        View Profile
                    </button>
                    <button class="btn btn-success" onclick="sendRoleRequest(4)">
                        <i class="fas fa-paper-plane"></i>
                        Request
                    </button>
                </div>
            </div>

            <!-- Artist Card 5 -->
            <div class="artist-card">
                <div class="artist-header">
                    <img src="../../assets/images/default-avatar.jpg" alt="Artist" class="artist-avatar">
                    <h3 class="artist-name">Dilini Wickramasinghe</h3>
                    <p class="artist-experience">6 Years Experience</p>
                </div>
                <div class="artist-body">
                    <div class="info-row">
                        <span class="info-label">Specialization</span>
                        <span class="info-value">Singer</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Location</span>
                        <span class="info-value">Kurunegala</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Dramas Done</span>
                        <span class="info-value">20</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Rating</span>
                        <span class="rating">★★★★★ 4.7</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Status</span>
                        <span class="status-badge assigned">Available</span>
                    </div>
                </div>
                <div class="artist-footer">
                    <button class="btn btn-primary" style="flex: 1;" onclick="viewArtistProfile(5)">
                        <i class="fas fa-user"></i>
                        View Profile
                    </button>
                    <button class="btn btn-success" onclick="sendRoleRequest(5)">
                        <i class="fas fa-paper-plane"></i>
                        Request
                    </button>
                </div>
            </div>

            <!-- Artist Card 6 -->
            <div class="artist-card">
                <div class="artist-header">
                    <img src="../../assets/images/default-avatar.jpg" alt="Artist" class="artist-avatar">
                    <h3 class="artist-name">Tharaka Rathnayake</h3>
                    <p class="artist-experience">10 Years Experience</p>
                </div>
                <div class="artist-body">
                    <div class="info-row">
                        <span class="info-label">Specialization</span>
                        <span class="info-value">Music Director</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Location</span>
                        <span class="info-value">Colombo</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Dramas Done</span>
                        <span class="info-value">35</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Rating</span>
                        <span class="rating">★★★★★ 4.9</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Status</span>
                        <span class="status-badge assigned">Available</span>
                    </div>
                </div>
                <div class="artist-footer">
                    <button class="btn btn-primary" style="flex: 1;" onclick="viewArtistProfile(6)">
                        <i class="fas fa-user"></i>
                        View Profile
                    </button>
                    <button class="btn btn-success" onclick="sendRoleRequest(6)">
                        <i class="fas fa-paper-plane"></i>
                        Request
                    </button>
                </div>
            </div>
        </div>

        <!-- No Results (hidden by default) -->
        <div class="no-results" style="display: none;" id="noResults">
            <i class="fas fa-user-slash"></i>
            <h3>No Artists Found</h3>
            <p>Try adjusting your search filters</p>
        </div>
    </main>

    <!-- Send Role Request Modal -->
    <div id="roleRequestModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeRoleRequestModal()">&times;</span>
            <h2><i class="fas fa-paper-plane"></i> Send Role Request</h2>
            <div class="modal-body">
                <form id="roleRequestForm">
                    <div class="form-group">
                        <label for="selectDrama">Select Drama *</label>
                        <select id="selectDrama" required>
                            <option value="">Choose a drama...</option>
                            <option value="1">Maname</option>
                            <option value="2">Sinhabahu</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="selectRole">Select Role *</label>
                        <select id="selectRole" required>
                            <option value="">Choose a role...</option>
                            <option value="1">Lead Actor</option>
                            <option value="2">Supporting Actor</option>
                            <option value="3">Villain</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="roleDescription">Role Description</label>
                        <textarea id="roleDescription" placeholder="Provide details about the role..."></textarea>
                    </div>
                    <div class="form-group">
                        <label for="expectedSalary">Expected Salary (LKR)</label>
                        <input type="number" id="expectedSalary" placeholder="e.g., 50000">
                    </div>
                    <div class="form-group">
                        <label for="message">Personal Message</label>
                        <textarea id="message" placeholder="Write a message to the artist..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-actions">
                <button class="btn btn-primary" onclick="submitRoleRequest()">
                    <i class="fas fa-paper-plane"></i>
                    Send Request
                </button>
                <button class="btn btn-secondary" onclick="closeRoleRequestModal()">Cancel</button>
            </div>
        </div>
    </div>

    <!-- Artist Profile Modal -->
    <div id="artistProfileModal" class="modal">
        <div class="modal-content" style="max-width: 800px;">
            <span class="close" onclick="closeArtistProfileModal()">&times;</span>
            <h2><i class="fas fa-user"></i> Artist Profile</h2>
            <div class="modal-body" id="artistProfileBody">
                <!-- Artist profile details will be loaded here -->
            </div>
            <div class="modal-actions">
                <button class="btn btn-success" onclick="sendRoleRequestFromProfile()">
                    <i class="fas fa-paper-plane"></i>
                    Send Role Request
                </button>
                <button class="btn btn-secondary" onclick="closeArtistProfileModal()">Close</button>
            </div>
        </div>
    </div>

    <script src="/Rangamadala/public/assets/JS/search-artists.js"></script>
</body>
</html>
