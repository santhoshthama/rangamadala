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
                                            <h3>
                                                <?php 
                                                if ($serviceName === 'other' && isset($details->service_type)) {
                                                    echo 'Other (' . htmlspecialchars($details->service_type) . ')';
                                                } else {
                                                    echo htmlspecialchars($service->service_type ?? '');
                                                }
                                                ?>
                                            </h3>
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

    <!-- Include Service Request Form -->
    <?php include 'service_request_form.view.php'; ?>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
