<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($data['provider']->full_name) ?> - Service Provider</title>
    <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/service_provider_detail.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="<?= ROOT ?>/assets/images/Rangamadala logo.png" type="image/x-icon">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <!-- Back Button -->
        <a href="<?= ROOT ?>/BrowseServiceProviders" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Browse
        </a>

        <!-- Provider Header Section -->
        <div class="provider-header-card">
            <div class="provider-header-top">
                <div class="provider-identity">
                    <div class="provider-avatar-large">
                        <?php 
                        // Use uploaded profile image if available; otherwise show default
                        $profileImage = !empty($data['provider']->profile_image)
                            ? ROOT . '/uploads/profile_images/' . $data['provider']->profile_image
                            : ROOT . '/uploads/profile_images/default_user.jpg';
                        ?>
                        <img src="<?= $profileImage ?>" alt="<?= htmlspecialchars($data['provider']->full_name) ?>" onerror="this.src='<?= ROOT ?>/uploads/profile_images/default_user.jpg'">
                    </div>
                    <div class="provider-basic-info">
                        <div class="name-and-badge">
                            <h1><?= htmlspecialchars($data['provider']->full_name) ?></h1>
                            <?php if ($data['provider']->availability == 1): ?>
                                <span class="availability-badge available">
                                    <i class="fas fa-circle"></i> Available
                                </span>
                            <?php else: ?>
                                <span class="availability-badge unavailable">
                                    <i class="fas fa-circle"></i> Unavailable
                                </span>
                            <?php endif; ?>
                        </div>
                        <p class="provider-title"><?= htmlspecialchars($data['provider']->professional_title) ?></p>
                        
                        <div class="provider-quick-info">
                            <span class="info-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <?= htmlspecialchars($data['provider']->location) ?>
                            </span>
                            <span class="info-item">
                                <i class="fas fa-briefcase"></i>
                                <?= (int)$data['provider']->years_experience ?> Years Experience
                            </span>
                        </div>
                    </div>
                </div>

                <div class="provider-actions">
                    <button class="btn-primary btn-request" onclick="openRequestModal()">
                        <i class="fas fa-paper-plane"></i> Request Service
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            
            <!-- About Section -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-user"></i>
                    <h2>About</h2>
                </div>
                <div class="card-body">
                    <p class="provider-summary"><?= nl2br(htmlspecialchars($data['provider']->professional_summary)) ?></p>
                    
                    <?php if (!empty($data['provider']->website)): ?>
                        <div class="website-link">
                            <i class="fas fa-globe"></i>
                            <a href="<?= htmlspecialchars($data['provider']->website) ?>" target="_blank" rel="noopener">
                                <?= htmlspecialchars($data['provider']->website) ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-address-card"></i>
                    <h2>Contact Information</h2>
                </div>
                <div class="card-body">
                    <div class="contact-grid">
                        <div class="contact-item">
                            <i class="fas fa-envelope"></i>
                            <div>
                                <label>Email</label>
                                <a href="mailto:<?= htmlspecialchars($data['provider']->email) ?>">
                                    <?= htmlspecialchars($data['provider']->email) ?>
                                </a>
                            </div>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-phone"></i>
                            <div>
                                <label>Phone</label>
                                <a href="tel:<?= htmlspecialchars($data['provider']->phone) ?>">
                                    <?= htmlspecialchars($data['provider']->phone) ?>
                                </a>
                            </div>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <div>
                                <label>Location</label>
                                <span><?= htmlspecialchars($data['provider']->location) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Services Offered Section -->
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-tools"></i>
                        <h2>Services Offered</h2>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($data['services'])): ?>
                            <div class="services-list">
                                <?php foreach ($data['services'] as $service): ?>
                                    <?php 
                                    $details = $service->details ?? null;
                                    $serviceName = strtolower(trim($service->service_name));
                                    ?>
                                    <div class="service-item">
                                        <div class="service-header">
                                            <h3><?= htmlspecialchars($service->service_name) ?></h3>
                                            <button class="btn-primary" onclick="openRequestModal('<?= htmlspecialchars($service->service_name) ?>', '<?= number_format($service->rate_per_hour) ?>')">
                                                <i class="fas fa-paper-plane"></i> Request
                                            </button>
                                        </div>
                                        <div class="detail-item" style="margin-bottom: 12px;">
                                            <label>Rate per hour:</label>
                                            <span>Rs <?= number_format($service->rate_per_hour) ?>/hr</span>
                                        </div>
                                        <?php if (!empty($service->description)): ?>
                                            <p class="service-description"><?= nl2br(htmlspecialchars($service->description)) ?></p>
                                        <?php endif; ?>
                                        
                                        <!-- Theater Production Details -->
                                        <?php if ($serviceName === 'theater production' && $details): ?>
                                            <div class="service-details-grid">
                                                <div class="detail-item">
                                                    <label>Actors:</label>
                                                    <span><?= htmlspecialchars($details->num_actors ?? '-') ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Expected Audience:</label>
                                                    <span><?= htmlspecialchars($details->expected_audience ?? '-') ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Stage Type:</label>
                                                    <span>
                                                        <?php 
                                                        $stages = [];
                                                        if ($details->stage_proscenium) $stages[] = 'Proscenium';
                                                        if ($details->stage_black_box) $stages[] = 'Black Box';
                                                        if ($details->stage_open_floor) $stages[] = 'Open Floor';
                                                        echo htmlspecialchars(implode(', ', $stages) ?: '-');
                                                        ?>
                                                    </span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Seating:</label>
                                                    <span><?= htmlspecialchars($details->seating_requirement ?? '-') ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Parking:</label>
                                                    <span><?= htmlspecialchars($details->parking_requirement ?? '-') ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Special Tech:</label>
                                                    <span><?= htmlspecialchars($details->special_tech ?? '-') ?></span>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <!-- Lighting Design Details -->
                                        <?php if ($serviceName === 'lighting design' && $details): ?>
                                            <div class="service-details-grid">
                                                <div class="detail-item">
                                                    <label>Services:</label>
                                                    <span>
                                                        <?php 
                                                        $services_list = [];
                                                        if ($details->stage_lighting) $services_list[] = 'Stage Lighting';
                                                        if ($details->spotlights) $services_list[] = 'Spotlights';
                                                        if ($details->custom_programming) $services_list[] = 'Custom Programming';
                                                        if ($details->moving_heads) $services_list[] = 'Moving Heads';
                                                        echo htmlspecialchars(implode(', ', $services_list) ?: '-');
                                                        ?>
                                                    </span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Number of Lights:</label>
                                                    <span><?= htmlspecialchars($details->num_lights ?? '-') ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Effects:</label>
                                                    <span><?= htmlspecialchars($details->effects ?? '-') ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Technician Needed:</label>
                                                    <span><?= htmlspecialchars($details->technician_needed ?? '-') ?></span>
                                                </div>
                                                <?php if ($details->notes): ?>
                                                <div class="detail-item full-width">
                                                    <label>Notes:</label>
                                                    <span><?= nl2br(htmlspecialchars($details->notes)) ?></span>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <!-- Sound Systems Details -->
                                        <?php if ($serviceName === 'sound systems' && $details): ?>
                                            <div class="service-details-grid">
                                                <div class="detail-item">
                                                    <label>Services:</label>
                                                    <span>
                                                        <?php 
                                                        $services_list = [];
                                                        if ($details->pa_system) $services_list[] = 'PA System';
                                                        if ($details->microphones) $services_list[] = 'Microphones';
                                                        if ($details->sound_mixing) $services_list[] = 'Sound Mixing';
                                                        if ($details->background_music) $services_list[] = 'Background Music';
                                                        if ($details->special_effects) $services_list[] = 'Special Effects';
                                                        echo htmlspecialchars(implode(', ', $services_list) ?: '-');
                                                        ?>
                                                    </span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Number of Mics:</label>
                                                    <span><?= htmlspecialchars($details->num_mics ?? '-') ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Stage Monitor:</label>
                                                    <span><?= htmlspecialchars($details->stage_monitor ?? '-') ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Sound Engineer:</label>
                                                    <span><?= htmlspecialchars($details->sound_engineer ?? '-') ?></span>
                                                </div>
                                                <?php if ($details->notes): ?>
                                                <div class="detail-item full-width">
                                                    <label>Notes:</label>
                                                    <span><?= nl2br(htmlspecialchars($details->notes)) ?></span>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <!-- Video Production Details -->
                                        <?php if ($serviceName === 'video production' && $details): ?>
                                            <div class="service-details-grid">
                                                <div class="detail-item">
                                                    <label>Video Type:</label>
                                                    <span>
                                                        <?php 
                                                        $video_types = [];
                                                        if ($details->full_event) $video_types[] = 'Full Event';
                                                        if ($details->highlight_reel) $video_types[] = 'Highlight Reel';
                                                        if ($details->short_promo) $video_types[] = 'Short Promo';
                                                        echo htmlspecialchars(implode(', ', $video_types) ?: '-');
                                                        ?>
                                                    </span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Cameras:</label>
                                                    <span><?= htmlspecialchars($details->num_cameras ?? '-') ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Drone Coverage:</label>
                                                    <span><?= htmlspecialchars($details->drone_needed ?? '-') ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Gimbals/Steadicams:</label>
                                                    <span><?= htmlspecialchars($details->gimbals ?? '-') ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Editing:</label>
                                                    <span><?= htmlspecialchars($details->editing ?? '-') ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Delivery Format:</label>
                                                    <span>
                                                        <?php 
                                                        $formats = [];
                                                        if ($details->delivery_mp4) $formats[] = 'MP4';
                                                        if ($details->delivery_raw) $formats[] = 'RAW';
                                                        if ($details->delivery_social) $formats[] = 'Social Media';
                                                        echo htmlspecialchars(implode(', ', $formats) ?: '-');
                                                        ?>
                                                    </span>
                                                </div>
                                                <?php if ($details->notes): ?>
                                                <div class="detail-item full-width">
                                                    <label>Notes:</label>
                                                    <span><?= nl2br(htmlspecialchars($details->notes)) ?></span>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <!-- Set Design Details -->
                                        <?php if ($serviceName === 'set design' && $details): ?>
                                            <div class="service-details-grid">
                                                <div class="detail-item">
                                                    <label>Service Type:</label>
                                                    <span>
                                                        <?php 
                                                        $set_types = [];
                                                        if ($details->set_design) $set_types[] = 'Design';
                                                        if ($details->set_construction) $set_types[] = 'Construction';
                                                        if ($details->set_rental) $set_types[] = 'Rental';
                                                        echo htmlspecialchars(implode(', ', $set_types) ?: '-');
                                                        ?>
                                                    </span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Production Stage:</label>
                                                    <span><?= htmlspecialchars($details->production_stage ?? '-') ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Materials:</label>
                                                    <span><?= htmlspecialchars($details->materials ?? '-') ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Dimensions:</label>
                                                    <span><?= htmlspecialchars($details->dimensions ?? '-') ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Budget Range:</label>
                                                    <span><?= htmlspecialchars($details->budget_range ?? '-') ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Deadline:</label>
                                                    <span><?= htmlspecialchars($details->deadline ?? '-') ?></span>
                                                </div>
                                                <?php if ($details->notes): ?>
                                                <div class="detail-item full-width">
                                                    <label>Notes:</label>
                                                    <span><?= nl2br(htmlspecialchars($details->notes)) ?></span>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <!-- Costume Design Details -->
                                        <?php if ($serviceName === 'costume design' && $details): ?>
                                            <div class="service-details-grid">
                                                <div class="detail-item">
                                                    <label>Service Type:</label>
                                                    <span>
                                                        <?php 
                                                        $costume_types = [];
                                                        if ($details->costume_design) $costume_types[] = 'Design';
                                                        if ($details->costume_creation) $costume_types[] = 'Creation';
                                                        if ($details->costume_rental) $costume_types[] = 'Rental';
                                                        echo htmlspecialchars(implode(', ', $costume_types) ?: '-');
                                                        ?>
                                                    </span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Characters:</label>
                                                    <span><?= htmlspecialchars($details->num_characters ?? '-') ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Costumes:</label>
                                                    <span><?= htmlspecialchars($details->num_costumes ?? '-') ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Measurements Required:</label>
                                                    <span><?= htmlspecialchars($details->measurements_required ?? '-') ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Fitting Dates:</label>
                                                    <span><?= htmlspecialchars($details->fitting_dates ?? '-') ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Budget Range:</label>
                                                    <span><?= htmlspecialchars($details->budget_range ?? '-') ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Deadline:</label>
                                                    <span><?= htmlspecialchars($details->deadline ?? '-') ?></span>
                                                </div>
                                                <?php if ($details->notes): ?>
                                                <div class="detail-item full-width">
                                                    <label>Notes:</label>
                                                    <span><?= nl2br(htmlspecialchars($details->notes)) ?></span>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="no-data">No services listed yet.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Recent Projects (Past Engagements) Section -->
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-project-diagram"></i>
                        <h2>Recent Projects</h2>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($data['projects'])): ?>
                            <div class="projects-list">
                                <?php foreach ($data['projects'] as $project): ?>
                                    <div class="project-item">
                                        <div class="project-header">
                                            <h3><?= htmlspecialchars($project->project_name) ?></h3>
                                            <span class="project-year"><?= htmlspecialchars($project->year) ?></span>
                                        </div>
                                        <?php if (!empty($project->role ?? null)): ?>
                                            <p class="project-role">
                                                <i class="fas fa-user-tag"></i>
                                                <?= htmlspecialchars($project->role) ?>
                                            </p>
                                        <?php endif; ?>
                                        <?php if (!empty($project->services_provided)): ?>
                                            <p class="project-services">
                                                <i class="fas fa-tasks"></i>
                                                Services: <?= htmlspecialchars($project->services_provided) ?>
                                            </p>
                                        <?php endif; ?>
                                        <?php if (!empty($project->description)): ?>
                                            <p class="project-description"><?= nl2br(htmlspecialchars($project->description)) ?></p>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="no-data">No projects listed yet.</p>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Request Service Modal -->
    <div id="requestModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Request Service</h2>
                <button class="close-modal" onclick="closeRequestModal()">&times;</button>
            </div>
            <form id="requestForm" method="POST" action="<?= ROOT ?>/ServiceRequest/submit">
                <input type="hidden" name="provider_id" value="<?= $data['provider']->user_id ?>">
                
                <div class="form-group">
                    <label>Your Name <span class="required">*</span></label>
                    <input type="text" name="requester_name" required class="form-input">
                </div>

                <div class="form-group">
                    <label>Your Email <span class="required">*</span></label>
                    <input type="email" name="requester_email" required class="form-input">
                </div>

                <div class="form-group">
                    <label>Your Phone <span class="required">*</span></label>
                    <input type="tel" name="requester_phone" required class="form-input">
                </div>

                <div class="form-group">
                    <label>Drama/Production Name <span class="required">*</span></label>
                    <input type="text" name="drama_name" required class="form-input">
                </div>

                <div class="form-group">
                    <label>Service Required <span class="required">*</span></label>
                    <select name="service_required" required class="form-input">
                        <option value="">Select a service</option>
                        <?php foreach ($data['services'] as $service): ?>
                            <option value="<?= htmlspecialchars($service->service_name) ?>">
                                <?= htmlspecialchars($service->service_name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Start Date <span class="required">*</span></label>
                        <input type="date" name="start_date" required class="form-input">
                    </div>
                    <div class="form-group">
                        <label>End Date <span class="required">*</span></label>
                        <input type="date" name="end_date" required class="form-input">
                    </div>
                </div>

                <div class="form-group">
                    <label>Special Requirements / Notes</label>
                    <textarea name="notes" class="form-input" rows="4" placeholder="Any specific requirements or additional information..."></textarea>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeRequestModal()">Cancel</button>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-paper-plane"></i> Send Request
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        function openRequestModal(serviceName = '', rate = '') {
            // Pre-fill the service name if provided
            if (serviceName) {
                const selectElement = document.querySelector('select[name="service_required"]');
                if (selectElement) {
                    selectElement.value = serviceName;
                }
            }
            document.getElementById('requestModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeRequestModal() {
            document.getElementById('requestModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('requestModal');
            if (event.target == modal) {
                closeRequestModal();
            }
        }
    </script>
</body>
</html>
