<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drama Details - Rangamadala</title>
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
            <li class="active">
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
        <a href="dashboard.php?drama_id=1" class="back-button">
            <i class="fas fa-arrow-left"></i>
            Back to Dashboard
        </a>

        <!-- Header -->
        <div class="header--wrapper">
            <div class="header--title">
                <span>Drama Details</span>
                <h2>Sinhabahu</h2>
            </div>
            <div class="header-controls">
                <button class="btn btn-primary" onclick="enableEdit()">
                    <i class="fas fa-edit"></i>
                    Edit Details
                </button>
            </div>
        </div>

        <!-- Drama Information -->
        <div class="content">
            <div class="container" style="max-width: 900px;">
                <form id="dramaDetailsForm">
                    <!-- Basic Information -->
                    <h3 style="color: var(--brand); margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-film"></i>
                        Basic Information
                    </h3>

                    <div class="form-group">
                        <label for="dramaTitle">Drama Title *</label>
                        <input type="text" id="dramaTitle" name="dramaTitle" value="Sinhabahu" readonly>
                    </div>

                    <div class="form-group">
                        <label for="dramaDescription">Description *</label>
                        <textarea id="dramaDescription" name="dramaDescription" readonly>A historical drama depicting the legendary story of King Sinhabahu and Princess Suppadevi, exploring themes of courage, love, and the founding of the Sinhala dynasty.</textarea>
                    </div>

                    <div class="form-group">
                        <label for="genre">Genre *</label>
                        <select id="genre" name="genre" disabled>
                            <option value="">Select Genre</option>
                            <option value="tragedy">Tragedy</option>
                            <option value="comedy">Comedy</option>
                            <option value="historical" selected>Historical</option>
                            <option value="social">Social Drama</option>
                            <option value="musical">Musical</option>
                            <option value="experimental">Experimental</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="language">Language *</label>
                        <select id="language" name="language" disabled>
                            <option value="">Select Language</option>
                            <option value="sinhala" selected>Sinhala</option>
                            <option value="tamil">Tamil</option>
                            <option value="english">English</option>
                            <option value="mixed">Mixed</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="duration">Duration (minutes)</label>
                        <input type="number" id="duration" name="duration" value="150" readonly>
                    </div>

                    <!-- Certificate Information -->
                    <h3 style="color: var(--brand); margin: 30px 0 20px; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-certificate"></i>
                        Public Performance Board Certificate
                    </h3>

                    <div class="drama-info">
                        <div class="service-info-item">
                            <span class="service-info-label">Certificate Number</span>
                            <span class="service-info-value">PPB/2024/11/1845</span>
                        </div>
                        <div class="service-info-item">
                            <span class="service-info-label">Issue Date</span>
                            <span class="service-info-value">2024-11-15</span>
                        </div>
                        <div class="service-info-item">
                            <span class="service-info-label">Status</span>
                            <span class="service-info-value"><span class="status-badge assigned">Verified by Admin</span></span>
                        </div>
                        <div class="service-info-item">
                            <span class="service-info-label">Certificate File</span>
                            <span class="service-info-value">
                                <a href="#" class="btn btn-secondary" style="font-size: 11px; padding: 6px 12px;">
                                    <i class="fas fa-download"></i>
                                    Download Certificate
                                </a>
                            </span>
                        </div>
                    </div>

                    <!-- Production Details -->
                    <h3 style="color: var(--brand); margin: 30px 0 20px; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-cog"></i>
                        Production Details
                    </h3>

                    <div class="form-group">
                        <label for="expectedBudget">Expected Budget (LKR)</label>
                        <input type="number" id="expectedBudget" name="expectedBudget" value="800000" readonly>
                    </div>

                    <div class="form-group">
                        <label for="rehearsalStartDate">Rehearsal Start Date</label>
                        <input type="date" id="rehearsalStartDate" name="rehearsalStartDate" value="2024-11-25" readonly>
                    </div>

                    <div class="form-group">
                        <label for="targetAudience">Target Audience</label>
                        <select id="targetAudience" name="targetAudience" disabled>
                            <option value="">Select Target Audience</option>
                            <option value="general" selected>General Audience</option>
                            <option value="family">Family Friendly</option>
                            <option value="adult">Adult (18+)</option>
                            <option value="children">Children</option>
                            <option value="youth">Youth</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="notes">Additional Notes</label>
                        <textarea id="notes" name="notes" readonly>This historical drama requires traditional costumes and authentic cultural representation. Special attention to dance choreography and period-accurate set design.</textarea>
                    </div>

                    <!-- Status Information -->
                    <h3 style="color: var(--brand); margin: 30px 0 20px; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-info-circle"></i>
                        Status Information
                    </h3>

                    <div class="drama-info">
                        <div class="service-info-item">
                            <span class="service-info-label">Created On</span>
                            <span class="service-info-value">2024-11-20</span>
                        </div>
                        <div class="service-info-item">
                            <span class="service-info-label">Current Status</span>
                            <span class="service-info-value"><span class="status-badge assigned">Active</span></span>
                        </div>
                        <div class="service-info-item">
                            <span class="service-info-label">Total Roles</span>
                            <span class="service-info-value">15</span>
                        </div>
                        <div class="service-info-item">
                            <span class="service-info-label">Filled Roles</span>
                            <span class="service-info-value">12/15</span>
                        </div>
                        <div class="service-info-item">
                            <span class="service-info-label">Production Managers</span>
                            <span class="service-info-value">3</span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="button-group" id="editButtons" style="display: none;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Save Changes
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="cancelEdit()">
                            <i class="fas fa-times"></i>
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const dramaId = urlParams.get('drama_id') || 1;

        function enableEdit() {
            // Enable form fields
            document.getElementById('dramaTitle').readOnly = false;
            document.getElementById('dramaDescription').readOnly = false;
            document.getElementById('genre').disabled = false;
            document.getElementById('language').disabled = false;
            document.getElementById('duration').readOnly = false;
            document.getElementById('expectedBudget').readOnly = false;
            document.getElementById('rehearsalStartDate').readOnly = false;
            document.getElementById('targetAudience').disabled = false;
            document.getElementById('notes').readOnly = false;
            
            // Show edit buttons
            document.getElementById('editButtons').style.display = 'flex';
        }

        function cancelEdit() {
            // Reload page to reset
            location.reload();
        }

        document.getElementById('dramaDetailsForm').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Drama details updated successfully!');
            location.reload();
        });
    </script>
    <script src="/Rangamadala/public/assets/JS/drama-details.js"></script>
</body>
</html>
