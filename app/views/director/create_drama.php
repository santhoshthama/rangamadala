<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Drama - Rangamadala</title>
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
            <li>
<<<<<<< HEAD
                <a href="<?= ROOT ?>/director/manage_dramas">
=======
                <a href="manage_dramas.php">
>>>>>>> 4a7cd2f6e0c168dac5fa3f8773e569dc3f8aba4b
                    <i class="fas fa-film"></i>
                    <span>My Dramas</span>
                </a>
            </li>
            <li class="active">
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

        <div class="content">
            <div class="container" style="max-width: 900px;">
                <div class="header">
                    <h1>Create New Drama</h1>
                    <p>Fill in the details to create your drama production. Admin approval required.</p>
                </div>

                <div class="info-box">
                    <i class="fas fa-info-circle"></i>
                    <strong>Important:</strong> You must upload a valid Public Performance Board Certificate. Your drama will be pending until admin approves it.
                </div>

                <form id="createDramaForm" enctype="multipart/form-data">
                    <!-- Basic Information -->
                    <h3 style="color: var(--brand); margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-film"></i>
                        Basic Information
                    </h3>

                    <div class="form-group">
                        <label for="dramaTitle">Drama Title *</label>
                        <input type="text" id="dramaTitle" name="dramaTitle" required placeholder="Enter drama title">
                        <span class="error-message" id="titleError">Please enter a drama title</span>
                    </div>

                    <div class="form-group">
                        <label for="dramaDescription">Description *</label>
                        <textarea id="dramaDescription" name="dramaDescription" required placeholder="Provide a detailed description of your drama"></textarea>
                        <span class="error-message" id="descriptionError">Please enter a description</span>
                    </div>

                    <div class="form-group">
                        <label for="genre">Genre *</label>
                        <select id="genre" name="genre" required>
                            <option value="">Select Genre</option>
                            <option value="tragedy">Tragedy</option>
                            <option value="comedy">Comedy</option>
                            <option value="historical">Historical</option>
                            <option value="social">Social Drama</option>
                            <option value="musical">Musical</option>
                            <option value="experimental">Experimental</option>
                            <option value="other">Other</option>
                        </select>
                        <span class="error-message" id="genreError">Please select a genre</span>
                    </div>

                    <div class="form-group">
                        <label for="language">Language *</label>
                        <select id="language" name="language" required>
                            <option value="">Select Language</option>
                            <option value="sinhala">Sinhala</option>
                            <option value="tamil">Tamil</option>
                            <option value="english">English</option>
                            <option value="mixed">Mixed</option>
                        </select>
                        <span class="error-message" id="languageError">Please select a language</span>
                    </div>

                    <div class="form-group">
                        <label for="duration">Estimated Duration (minutes) *</label>
                        <input type="number" id="duration" name="duration" required min="30" max="300" placeholder="e.g., 120">
                        <span class="info-text">Enter duration in minutes (30-300)</span>
                    </div>

                    <!-- Certificate Upload -->
                    <h3 style="color: var(--brand); margin: 30px 0 20px; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-certificate"></i>
                        Public Performance Board Certificate
                    </h3>

                    <div class="form-group">
                        <label for="certificate">Upload Certificate *</label>
                        <input type="file" id="certificate" name="certificate" required accept=".pdf,.jpg,.jpeg,.png">
                        <span class="info-text">Accepted formats: PDF, JPG, PNG (Max 5MB)</span>
                        <span class="error-message" id="certificateError">Please upload a valid certificate</span>
                    </div>

                    <div class="form-group">
                        <label for="certificateNumber">Certificate Number *</label>
                        <input type="text" id="certificateNumber" name="certificateNumber" required placeholder="Enter certificate number">
                    </div>

                    <div class="form-group">
                        <label for="issueDate">Certificate Issue Date *</label>
                        <input type="date" id="issueDate" name="issueDate" required>
                    </div>

                    <!-- Production Details -->
                    <h3 style="color: var(--brand); margin: 30px 0 20px; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-cog"></i>
                        Production Details
                    </h3>

                    <div class="form-group">
                        <label for="expectedBudget">Expected Budget (LKR)</label>
                        <input type="number" id="expectedBudget" name="expectedBudget" min="0" placeholder="e.g., 500000">
                        <span class="info-text">Optional - estimated total budget for the production</span>
                    </div>

                    <div class="form-group">
                        <label for="rehearsalStartDate">Expected Rehearsal Start Date</label>
                        <input type="date" id="rehearsalStartDate" name="rehearsalStartDate">
                    </div>

                    <div class="form-group">
                        <label for="targetAudience">Target Audience</label>
                        <select id="targetAudience" name="targetAudience">
                            <option value="">Select Target Audience</option>
                            <option value="general">General Audience</option>
                            <option value="family">Family Friendly</option>
                            <option value="adult">Adult (18+)</option>
                            <option value="children">Children</option>
                            <option value="youth">Youth</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="notes">Additional Notes</label>
                        <textarea id="notes" name="notes" placeholder="Any additional information about the drama"></textarea>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="button-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check"></i>
                            Submit for Approval
                        </button>
                        <button type="reset" class="btn btn-secondary">
                            <i class="fas fa-undo"></i>
                            Reset Form
                        </button>
<<<<<<< HEAD
                        <a href="<?= ROOT ?>/director/dashboard" class="btn btn-danger">
=======
                        <a href="dashboard.php" class="btn btn-danger">
>>>>>>> 4a7cd2f6e0c168dac5fa3f8773e569dc3f8aba4b
                            <i class="fas fa-times"></i>
                            Cancel
                        </a>
                    </div>

                    <div class="success-message" id="successMessage" style="display: none; text-align: center; padding: 15px; background: #d4edda; color: #155724; border-radius: 10px; margin-top: 20px;">
                        Drama created successfully! Pending admin approval.
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script src="/Rangamadala/public/assets/JS/create-drama.js"></script>
</body>
</html>
