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
                    
                    <?php if (!empty($data['provider']->social_media_link)): ?>
                        <div class="website-link">
                            <i class="fas fa-share-alt"></i>
                            <a href="<?= htmlspecialchars($data['provider']->social_media_link) ?>" target="_blank" rel="noopener">
                                <?= htmlspecialchars($data['provider']->social_media_link) ?>
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
                                    $serviceName = strtolower(trim($service->service_type ?? ''));
                                    ?>
                                    <div class="service-item">
                                        <div class="service-header">
                                            <h3><?= htmlspecialchars($service->service_type ?? '') ?></h3>
                                            <button class="btn-primary" onclick="openRequestModal('<?= htmlspecialchars($service->service_type ?? '') ?>', '<?= number_format($service->rate_per_hour ?? 0) ?>')">
                                                <i class="fas fa-paper-plane"></i> Request
                                            </button>
                                        </div>
                                        <div class="detail-item" style="margin-bottom: 12px;">
                                            <label>Rate per hour:</label>
                                            <span>Rs <?= number_format($service->rate_per_hour ?? 0) ?>/hr</span>
                                        </div>
                                        <?php if (!empty($service->description)): ?>
                                            <p class="service-description"><?= nl2br(htmlspecialchars($service->description)) ?></p>
                                        <?php endif; ?>
                                        
                                        <!-- Theater Production Details -->
                                        <?php if ($serviceName === 'theater production' && $details): ?>
                                            <div class="service-details-grid">
                                                <div class="detail-item">
                                                    <label>Theatre Name:</label>
                                                    <span><?= htmlspecialchars($details->theatre_name ?? '-') ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Seating Capacity:</label>
                                                    <span><?= htmlspecialchars($details->seating_capacity ?? '-') ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Stage Dimensions:</label>
                                                    <span><?= htmlspecialchars($details->stage_dimensions ?? '-') ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Stage Type:</label>
                                                    <span><?= htmlspecialchars($details->stage_type ?? '-') ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Available Facilities:</label>
                                                    <span>
                                                        <?php 
                                                        $afArr = !empty($details->available_facilities) ? json_decode($details->available_facilities, true) : [];
                                                        echo htmlspecialchars(!empty($afArr) ? implode(', ', $afArr) : '-');
                                                        ?>
                                                    </span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Technical Facilities:</label>
                                                    <span>
                                                        <?php 
                                                        $tfArr = !empty($details->technical_facilities) ? json_decode($details->technical_facilities, true) : [];
                                                        echo htmlspecialchars(!empty($tfArr) ? implode(', ', $tfArr) : '-');
                                                        ?>
                                                    </span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Equipment for Rent:</label>
                                                    <span><?= htmlspecialchars($details->equipment_rent ?? '-') ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Stage Crew Available:</label>
                                                    <span><?= htmlspecialchars($details->stage_crew_available ?? '-') ?></span>
                                                </div>
                                                <div class="detail-item" style="grid-column: 1 / -1;">
                                                    <label>Location / Address:</label>
                                                    <span><?= htmlspecialchars($details->location_address ?? '-') ?></span>
                                                </div>
                                                <div class="detail-item" style="grid-column: 1 / -1;">
                                                    <label>Theatre Photos:</label>
                                                    <?php if (!empty($details->theatre_photos)): ?>
                                                    <span><a href="<?= ROOT . '/' . htmlspecialchars($details->theatre_photos) ?>" target="_blank" style="color: #3b82f6;">View Photos</a></span>
                                                    <?php else: ?>
                                                    <span>-</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <!-- Lighting Design Details -->
                                        <?php if ($serviceName === 'lighting design' && $details): ?>
                                            <div class="service-details-grid">
                                                <div class="detail-item full-width">
                                                    <label>Lighting Equipment Provided:</label>
                                                    <span><?= nl2br(htmlspecialchars($details->lighting_equipment_provided ?? '-')) ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Maximum Stage Size Supported:</label>
                                                    <span><?= htmlspecialchars($details->max_stage_size ?? '-') ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Lighting Design Service:</label>
                                                    <span><?= htmlspecialchars($details->lighting_design_service ?? '-') ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Lighting Crew Available:</label>
                                                    <span><?= htmlspecialchars($details->lighting_crew_available ?? '-') ?></span>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <!-- Sound Systems Details -->
                                        <?php if ($serviceName === 'sound systems' && $details): ?>
                                            <div class="service-details-grid">
                                                <div class="detail-item full-width">
                                                    <label>Sound Equipment Provided:</label>
                                                    <span><?= nl2br(htmlspecialchars($details->sound_equipment_provided ?? '-')) ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Max Audience Size:</label>
                                                    <span><?= htmlspecialchars($details->max_audience_size ?? '-') ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Equipment Brands:</label>
                                                    <span><?= htmlspecialchars($details->equipment_brands ?? '-') ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Sound Effects Handling:</label>
                                                    <span><?= htmlspecialchars($details->sound_effects_handling ?? '-') ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Sound Engineer Included:</label>
                                                    <span><?= htmlspecialchars($details->sound_engineer_included ?? '-') ?></span>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <!-- Video Production Details -->
                                        <?php if ($serviceName === 'video production' && $details): ?>
                                            <div class="service-details-grid">
                                                <div class="detail-item full-width">
                                                    <label>Services Offered:</label>
                                                    <span><?= nl2br(htmlspecialchars($details->services_offered ?? '-')) ?></span>
                                                </div>
                                                <div class="detail-item full-width">
                                                    <label>Equipment Used:</label>
                                                    <span><?= nl2br(htmlspecialchars($details->equipment_used ?? '-')) ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Number of Crew Members:</label>
                                                    <span><?= htmlspecialchars($details->num_crew_members ?? '-') ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Editing Software Used:</label>
                                                    <span><?= htmlspecialchars($details->editing_software ?? '-') ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Drone Service Available:</label>
                                                    <span><?= htmlspecialchars($details->drone_service_available ?? '-') ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Maximum Video Resolution:</label>
                                                    <span><?= htmlspecialchars($details->max_video_resolution ?? '-') ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Photo Editing Included:</label>
                                                    <span><?= htmlspecialchars($details->photo_editing_included ?? '-') ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Delivery Time for Final Output:</label>
                                                    <span><?= htmlspecialchars($details->delivery_time ?? '-') ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Raw Footage/Photos Provided:</label>
                                                    <span><?= htmlspecialchars($details->raw_footage_provided ?? '-') ?></span>
                                                </div>
                                                <div class="detail-item full-width">
                                                    <label>Portfolio / Sample Links:</label>
                                                    <span>
                                                        <?php if (!empty($details->portfolio_links)): ?>
                                                            <a href="<?= htmlspecialchars($details->portfolio_links) ?>" target="_blank" style="color: #3b82f6;">View Portfolio</a>
                                                        <?php else: ?>
                                                            -
                                                        <?php endif; ?>
                                                    </span>
                                                </div>
                                                <?php if (!empty($details->sample_videos)): ?>
                                                <div class="detail-item full-width">
                                                    <label>Sample Photos/Videos:</label>
                                                    <span><a href="<?= ROOT . '/' . htmlspecialchars($details->sample_videos) ?>" target="_blank" style="color: #3b82f6;">View Sample</a></span>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <!-- Set Design Details -->
                                        <?php if ($serviceName === 'set design' && $details): ?>
                                            <div class="service-details-grid">
                                                <div class="detail-item full-width">
                                                    <label>Types of Sets Designed:</label>
                                                    <span><?= nl2br(htmlspecialchars($details->types_of_sets_designed ?? '-')) ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Set Construction Provided:</label>
                                                    <span><?= htmlspecialchars($details->set_construction_provided ?? '-') ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Stage Installation Support:</label>
                                                    <span><?= htmlspecialchars($details->stage_installation_support ?? '-') ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Maximum Stage Size Supported:</label>
                                                    <span><?= htmlspecialchars($details->max_stage_size_supported ?? '-') ?></span>
                                                </div>
                                                <div class="detail-item full-width">
                                                    <label>Materials Used:</label>
                                                    <span><?= nl2br(htmlspecialchars($details->materials_used ?? '-')) ?></span>
                                                </div>
                                                <?php if (!empty($details->sample_set_designs)): ?>
                                                <div class="detail-item full-width">
                                                    <label>Sample Set Designs:</label>
                                                    <span><a href="<?= ROOT . '/' . htmlspecialchars($details->sample_set_designs) ?>" target="_blank">View sample</a></span>
                                                </div>
                                                <?php endif; ?>
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
                                                <div class="detail-item full-width">
                                                    <label>Types of Costumes Provided:</label>
                                                    <span><?= nl2br(htmlspecialchars($details->types_of_costumes_provided ?? '-')) ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Custom Costume Design Available:</label>
                                                    <span><?= htmlspecialchars($details->custom_costume_design_available ?? '-') ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Available Sizes:</label>
                                                    <span><?= htmlspecialchars($details->available_sizes ?? '-') ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Alterations Provided:</label>
                                                    <span><?= htmlspecialchars($details->alterations_provided ?? '-') ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Number of Costumes Available:</label>
                                                    <span><?= htmlspecialchars($details->number_of_costumes_available ?? '-') ?></span>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Makeup & Hair Details -->
                                        <?php if ($serviceName === 'makeup & hair' && $details): ?>
                                            <div class="service-details-grid">
                                                <div class="detail-item full-width">
                                                    <label>Type of Make-up Services:</label>
                                                    <span><?= nl2br(htmlspecialchars($details->type_of_makeup_services ?? '-')) ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Experience in Stage Make-up (years):</label>
                                                    <span><?= htmlspecialchars($details->experience_stage_makeup_years ?? '-') ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Maximum Actors Per Show:</label>
                                                    <span><?= htmlspecialchars($details->maximum_actors_per_show ?? '-') ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Character-based Make-up Available:</label>
                                                    <span><?= htmlspecialchars($details->character_based_makeup_available ?? '-') ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Can Handle Full Cast:</label>
                                                    <span><?= htmlspecialchars($details->can_handle_full_cast ?? '-') ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Bring Own Make-up Kit:</label>
                                                    <span><?= htmlspecialchars($details->bring_own_makeup_kit ?? '-') ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>On-site Service Available:</label>
                                                    <span><?= htmlspecialchars($details->onsite_service_available ?? '-') ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Touch-up Service During Show:</label>
                                                    <span><?= htmlspecialchars($details->touchup_service_during_show ?? '-') ?></span>
                                                </div>
                                                <div class="detail-item full-width">
                                                    <label>Traditional/Cultural Make-up Expertise:</label>
                                                    <span><?= nl2br(htmlspecialchars($details->traditional_cultural_makeup_expertise ?? '-')) ?></span>
                                                </div>
                                                <?php if (!empty($details->sample_makeup_photos)): ?>
                                                <div class="detail-item full-width">
                                                    <label>Sample Make-up Photos:</label>
                                                    <span><a href="<?= ROOT . '/' . htmlspecialchars($details->sample_makeup_photos) ?>" target="_blank" style="color: #3b82f6;">View Photos</a></span>
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
                
                <!-- Common Fields -->
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
                    <select id="serviceSelect" name="service_required" required class="form-input" onchange="updateServiceFields()">
                        <option value="">Select a service</option>
                        <?php foreach ($data['services'] as $service): ?>
                            <option value="<?= htmlspecialchars($service->service_type ?? '') ?>">
                                <?= htmlspecialchars($service->service_type ?? '') ?>
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

                <!-- Theater Production Specific Fields -->
                <div id="theaterFields" class="service-specific-fields" style="display: none;">
                    <h3>Theater Production Details</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Number of Actors</label>
                            <input type="number" name="theater_num_actors" class="form-input">
                        </div>
                        <div class="form-group">
                            <label>Expected Audience</label>
                            <input type="number" name="theater_expected_audience" class="form-input">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Stage Type</label>
                        <div style="display: flex; gap: 15px; margin-top: 8px;">
                            <label style="display: flex; align-items: center; gap: 6px; margin: 0;"><input type="checkbox" name="theater_stage_proscenium"> Proscenium</label>
                            <label style="display: flex; align-items: center; gap: 6px; margin: 0;"><input type="checkbox" name="theater_stage_black_box"> Black Box</label>
                            <label style="display: flex; align-items: center; gap: 6px; margin: 0;"><input type="checkbox" name="theater_stage_open_floor"> Open Floor</label>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Seating Requirement</label>
                            <input type="text" name="theater_seating_requirement" class="form-input">
                        </div>
                        <div class="form-group">
                            <label>Parking Requirement</label>
                            <input type="text" name="theater_parking_requirement" class="form-input">
                        </div>
                    </div>
                </div>

                <!-- Lighting Design Specific Fields -->
                <div id="lightingFields" class="service-specific-fields" style="display: none;">
                    <h3>Lighting Design Details</h3>
                    <div class="form-group">
                        <label>Lighting Services</label>
                        <div style="display: flex; gap: 15px; margin-top: 8px; flex-wrap: wrap;">
                            <label style="display: flex; align-items: center; gap: 6px; margin: 0;"><input type="checkbox" name="lighting_stage_lighting"> Stage Lighting</label>
                            <label style="display: flex; align-items: center; gap: 6px; margin: 0;"><input type="checkbox" name="lighting_spotlights"> Spotlights</label>
                            <label style="display: flex; align-items: center; gap: 6px; margin: 0;"><input type="checkbox" name="lighting_custom_programming"> Custom Programming</label>
                            <label style="display: flex; align-items: center; gap: 6px; margin: 0;"><input type="checkbox" name="lighting_moving_heads"> Moving Heads</label>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Number of Lights</label>
                            <input type="number" name="lighting_num_lights" class="form-input">
                        </div>
                        <div class="form-group">
                            <label>Effects</label>
                            <input type="text" name="lighting_effects" class="form-input">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Technician Needed</label>
                        <select name="lighting_technician_needed" class="form-input">
                            <option value="">-- Select --</option>
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                    </div>
                </div>

                <!-- Sound Systems Specific Fields -->
                <div id="soundFields" class="service-specific-fields" style="display: none;">
                    <h3>Sound Systems Details</h3>
                    <div class="form-group">
                        <label>Sound Services</label>
                        <div style="display: flex; gap: 15px; margin-top: 8px; flex-wrap: wrap;">
                            <label style="display: flex; align-items: center; gap: 6px; margin: 0;"><input type="checkbox" name="sound_pa_system"> PA System</label>
                            <label style="display: flex; align-items: center; gap: 6px; margin: 0;"><input type="checkbox" name="sound_microphones"> Microphones</label>
                            <label style="display: flex; align-items: center; gap: 6px; margin: 0;"><input type="checkbox" name="sound_sound_mixing"> Sound Mixing</label>
                            <label style="display: flex; align-items: center; gap: 6px; margin: 0;"><input type="checkbox" name="sound_background_music"> Background Music</label>
                            <label style="display: flex; align-items: center; gap: 6px; margin: 0;"><input type="checkbox" name="sound_special_effects"> Special Effects</label>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Number of Mics</label>
                            <input type="number" name="sound_num_mics" class="form-input">
                        </div>
                        <div class="form-group">
                            <label>Stage Monitor</label>
                            <select name="sound_stage_monitor" class="form-input">
                                <option value="">-- Select --</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Sound Engineer</label>
                        <select name="sound_sound_engineer" class="form-input">
                            <option value="">-- Select --</option>
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                    </div>
                </div>

                <!-- Video Production Specific Fields -->
                <div id="videoFields" class="service-specific-fields" style="display: none;">
                    <h3>Video Production Details</h3>
                    <div class="form-group">
                        <label>Video Type</label>
                        <div style="display: flex; gap: 15px; margin-top: 8px;">
                            <label style="display: flex; align-items: center; gap: 6px; margin: 0;"><input type="checkbox" name="video_full_event"> Full Event</label>
                            <label style="display: flex; align-items: center; gap: 6px; margin: 0;"><input type="checkbox" name="video_highlight_reel"> Highlight Reel</label>
                            <label style="display: flex; align-items: center; gap: 6px; margin: 0;"><input type="checkbox" name="video_short_promo"> Short Promo</label>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Number of Cameras</label>
                            <input type="number" name="video_num_cameras" class="form-input">
                        </div>
                        <div class="form-group">
                            <label>Drone Coverage</label>
                            <select name="video_drone_needed" class="form-input">
                                <option value="">-- Select --</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Gimbals/Steadicams</label>
                            <select name="video_gimbals" class="form-input">
                                <option value="">-- Select --</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Editing Required</label>
                            <select name="video_editing" class="form-input">
                                <option value="">-- Select --</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Delivery Format</label>
                        <div style="display: flex; gap: 15px; margin-top: 8px;">
                            <label style="display: flex; align-items: center; gap: 6px; margin: 0;"><input type="checkbox" name="video_delivery_mp4"> MP4</label>
                            <label style="display: flex; align-items: center; gap: 6px; margin: 0;"><input type="checkbox" name="video_delivery_raw"> RAW</label>
                            <label style="display: flex; align-items: center; gap: 6px; margin: 0;"><input type="checkbox" name="video_delivery_social"> Social Media</label>
                        </div>
                    </div>
                </div>

                <!-- Set Design Specific Fields -->
                <div id="setFields" class="service-specific-fields" style="display: none;">
                    <h3>Set Design Details</h3>
                    <div class="form-group">
                        <label>Service Type</label>
                        <div style="display: flex; gap: 15px; margin-top: 8px;">
                            <label style="display: flex; align-items: center; gap: 6px; margin: 0;"><input type="checkbox" name="set_set_design"> Design</label>
                            <label style="display: flex; align-items: center; gap: 6px; margin: 0;"><input type="checkbox" name="set_set_construction"> Construction</label>
                            <label style="display: flex; align-items: center; gap: 6px; margin: 0;"><input type="checkbox" name="set_set_rental"> Rental</label>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Production Stage</label>
                            <input type="text" name="set_production_stage" class="form-input">
                        </div>
                        <div class="form-group">
                            <label>Materials</label>
                            <input type="text" name="set_materials" class="form-input">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Dimensions</label>
                            <input type="text" name="set_dimensions" placeholder="e.g., 20ft x 15ft x 12ft" class="form-input">
                        </div>
                        <div class="form-group">
                            <label>Budget Range</label>
                            <input type="text" name="set_budget_range" placeholder="e.g., 100000-250000" class="form-input">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Deadline</label>
                        <input type="date" name="set_deadline" class="form-input">
                    </div>
                </div>

                <!-- Costume Design Specific Fields -->
                <div id="costumeFields" class="service-specific-fields" style="display: none;">
                    <h3>Costume Design Details</h3>
                    <div class="form-group">
                        <label>Service Type</label>
                        <div style="display: flex; gap: 15px; margin-top: 8px;">
                            <label style="display: flex; align-items: center; gap: 6px; margin: 0;"><input type="checkbox" name="costume_costume_design"> Design</label>
                            <label style="display: flex; align-items: center; gap: 6px; margin: 0;"><input type="checkbox" name="costume_costume_creation"> Creation</label>
                            <label style="display: flex; align-items: center; gap: 6px; margin: 0;"><input type="checkbox" name="costume_costume_rental"> Rental</label>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Number of Characters</label>
                            <input type="number" name="costume_num_characters" class="form-input">
                        </div>
                        <div class="form-group">
                            <label>Number of Costumes</label>
                            <input type="number" name="costume_num_costumes" class="form-input">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Measurements Required</label>
                        <select name="costume_measurements_required" class="form-input">
                            <option value="">-- Select --</option>
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Fitting Dates</label>
                            <input type="text" name="costume_fitting_dates" placeholder="e.g., 2026-02-01 to 2026-02-15" class="form-input">
                        </div>
                        <div class="form-group">
                            <label>Budget Range</label>
                            <input type="text" name="costume_budget_range" placeholder="e.g., 150000-350000" class="form-input">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Deadline</label>
                        <input type="date" name="costume_deadline" class="form-input">
                    </div>
                </div>

                <!-- Notes -->
                <div class="form-group">
                    <label>Special Requirements / Notes</label>
                    <textarea name="notes" class="form-input" rows="3" placeholder="Any specific requirements or additional information..."></textarea>
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
                    updateServiceFields();
                }
            }
            document.getElementById('requestModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeRequestModal() {
            document.getElementById('requestModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function updateServiceFields() {
            const serviceSelect = document.getElementById('serviceSelect');
            const selectedService = serviceSelect.value.toLowerCase();
            
            // Hide all service-specific fields
            document.getElementById('theaterFields').style.display = 'none';
            document.getElementById('lightingFields').style.display = 'none';
            document.getElementById('soundFields').style.display = 'none';
            document.getElementById('videoFields').style.display = 'none';
            document.getElementById('setFields').style.display = 'none';
            document.getElementById('costumeFields').style.display = 'none';
            
            // Show the relevant fields based on selection
            if (selectedService.includes('theater')) {
                document.getElementById('theaterFields').style.display = 'block';
            } else if (selectedService.includes('lighting')) {
                document.getElementById('lightingFields').style.display = 'block';
            } else if (selectedService.includes('sound')) {
                document.getElementById('soundFields').style.display = 'block';
            } else if (selectedService.includes('video')) {
                document.getElementById('videoFields').style.display = 'block';
            } else if (selectedService.includes('set')) {
                document.getElementById('setFields').style.display = 'block';
            } else if (selectedService.includes('costume')) {
                document.getElementById('costumeFields').style.display = 'block';
            }
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
