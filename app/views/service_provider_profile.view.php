<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <!-- Admin Design Library CSS -->
    <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/admindashboard.css">
    <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/Button.css">
    <!-- Service Provider Styles -->
    <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/service provider/service_provider_dashboard.css">
    <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/service provider/service_provider_profile.css">
    <link rel="shortcut icon" href="<?= ROOT ?>/assets/images/Rangamadala logo.png" type="image/x-icon">
    <title><?= isset($pageTitle) ? $pageTitle : 'Profile' ?> - <?php echo htmlspecialchars($provider->full_name ?? 'Rangamadala'); ?></title>
</head>
<body class="service-provider-profile-no-sidebar">

    <div class="main--content">
        <?php include 'includes/service_provider/header.php'; ?>

        <a class="back-link" href="<?= ROOT ?>/ServiceProviderDashboard">
            <i class="bx bx-arrow-left"></i>
            <span>Back to Dashboard</span>
        </a>

        <div class="profile-card">
            <!-- Profile Summary (Left Sidebar) -->
            <aside class="profile-summary">
                <?php 
                // Profile image (SEPARATE from business certificate)
                $profileImage = ROOT . '/uploads/profile_images/user_profile.png';
                if (!empty($data['provider']->profile_image)) {
                    $profileImage = ROOT . '/uploads/profile_images/' . $data['provider']->profile_image;
                }
                ?>
                <div class="profile-image-wrap" style="position: relative; display: inline-block; width: 100%; text-align: center;">
                    <img id="currentProfileImage" src="<?php echo $profileImage; ?>" 
                         alt="Profile Picture"
                         onerror="this.src='<?= ROOT ?>/uploads/profile_images/user_profile.png'">
                </div>

                <div style="margin-top: 28px;">
                    <h2><?php echo htmlspecialchars($data['provider']->full_name); ?></h2>
                    <p><i class="bx bx-envelope"></i> <?php echo htmlspecialchars($data['provider']->email); ?></p>
                    <p><i class="bx bx-phone"></i> <?php echo htmlspecialchars($data['provider']->phone); ?></p>
                    <?php if (!empty($data['provider']->location)): ?>
                    <p><i class="bx bx-map-marker-alt"></i> <?php echo htmlspecialchars($data['provider']->location); ?></p>
                    <?php endif; ?>

                    <div class="summary-item">
                        <span>Professional Title</span>
                        <strong><?php echo htmlspecialchars($data['provider']->professional_title); ?></strong>
                    </div>
                </div>
            </aside>

            <!-- Profile Form (Right Section) -->
            <section class="profile-form">
                <h1>Profile Details</h1>
                <p class="subtitle">Keep your information up to date.</p>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alerts">
                        <div class="alert alert-success">
                            <i class="bx bx-check-circle"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alerts">
                        <div class="alert alert-danger">
                            <i class="bx bx-exclamation-triangle"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Profile Image Upload Section -->
                <div class="section profile-image-section" style="margin-top: 32px;">
                    <h3>Profile Image</h3>
                    <form id="profileInlineImageForm" class="profile-image-upload-form" method="POST" enctype="multipart/form-data" action="<?= ROOT ?>/ServiceProviderProfile/uploadProfileImage">
                        <div class="profile-image-upload-row">
                            <label for="profileImageUploadInput" class="upload-new-image-btn">
                                <i class="bx bx-upload"></i>
                                <span>Upload new image</span>
                            </label>
                            <span id="selectedProfileImageName" class="selected-profile-image-name"><?php echo !empty($data['provider']->profile_image) ? htmlspecialchars($data['provider']->profile_image) : 'user_profile.png'; ?></span>
                        </div>
                        <input type="file" id="profileImageUploadInput" name="profile_image" accept="image/jpeg,image/jpg,image/png,image/gif" onchange="handleProfileImageSelect(event)">
                        <div id="profileImageError" class="alert alert-danger profile-image-error" style="display: none;"></div>
                    </form>
                </div>

                <!-- Basic Information Section -->
                <div class="section">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h3>Basic Information</h3>
                        <a href="<?php echo ROOT; ?>/ServiceProviderProfile/editBasicInfo?id=<?php echo $data['provider_id']; ?>" class="btn" style="text-decoration: none; padding: 10px 16px; font-size: 14px;">
                            <i class="bx bxs-edit"></i> Edit
                        </a>
                    </div>
                    <form style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" value="<?php echo htmlspecialchars($data['provider']->full_name); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label>Professional Title</label>
                            <input type="text" value="<?php echo htmlspecialchars($data['provider']->professional_title); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" value="<?php echo htmlspecialchars($data['provider']->email); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="tel" value="<?php echo htmlspecialchars($data['provider']->phone); ?>" readonly>
                        </div>
                    </form>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label>Location</label>
                            <input type="text" value="<?php echo htmlspecialchars($data['provider']->location); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label>Social Media Link</label>
                            <input type="text" value="<?php echo $data['provider']->social_media_link ? htmlspecialchars($data['provider']->social_media_link) : 'Not provided'; ?>" readonly>
                        </div>
                    </div>
                    <div class="form-group" style="margin-top: 16px;">
                        <label>Professional Summary</label>
                        <textarea style="resize: vertical; min-height: 100px;" readonly><?php
                            $professionalSummary = $data['provider']->professional_summary ?? ($data['provider']->bio ?? '');
                            echo htmlspecialchars((string)$professionalSummary);
                        ?></textarea>
                    </div>

                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                        <label style="margin: 0; min-width: 220px;">Currently Available for New Projects</label>
                        <div class="toggle <?php echo $data['provider']->availability ? 'active' : ''; ?>" style="pointer-events: none; width: 50px; height: 25px; background: <?php echo $data['provider']->availability ? '#10b981' : '#ccc'; ?>; border-radius: 25px; position: relative;">
                            <div style="content: ''; width: 20px; height: 20px; background: white; border-radius: 50%; position: absolute; top: 2.5px; left: <?php echo $data['provider']->availability ? '27.5px' : '2.5px'; ?>; transition: 0.3s;"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Availability Notes</label>
                        <textarea readonly><?php echo $data['provider']->availability_notes ? htmlspecialchars($data['provider']->availability_notes) : 'No notes provided'; ?></textarea>
                    </div>
                </div>

                <!-- Change Password -->
                <div class="section">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                        <h3>Security</h3>
                        <a href="<?php echo ROOT; ?>/ServiceProviderProfile/changePassword" class="btn" style="text-decoration: none; padding: 8px 16px; font-size: 13px;">
                            <i class="bx bxs-key"></i> Change Password
                        </a>
                    </div>
                </div>

                <!-- Services & Rates Section -->
                <div class="section">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                        <h3>Services & Rates</h3>
                        <a href="<?php echo ROOT; ?>/ServiceProviderProfile/addService?provider_id=<?php echo $data['provider_id']; ?>" class="btn" style="text-decoration: none; padding: 8px 16px; font-size: 13px;">
                            <i class="bx bxs-plus"></i> Add Service
                        </a>
            </div>
            
                    <?php if (empty($data['services'])): ?>
                        <p style="color: var(--muted); padding: 20px; text-align: center; background: linear-gradient(180deg, #fffdf7, #fff7e6); border-radius: 12px;">No services added yet.</p>
                    <?php else: ?>
                <?php foreach ($data['services'] as $service): ?>
                    <?php 
                    $details = $service->details ?? null;
                    $resolvedType = $service->service_type ?? ($details->service_type ?? '');
                    $serviceName = strtolower(trim($resolvedType));
                    ?>
                    <div class="service-item" style="display: flex; flex-direction: column; align-items: flex-start;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; width: 100%; margin-bottom: 15px;">
                            <div style="display: flex; align-items: center;">
                                <input type="checkbox" class="checkbox" checked disabled style="margin-right: 10px;">
                                <span style="font-size: 24px; font-weight: 600; color: #333;">
                                    <?php 
                                    if ($serviceName === 'other' && isset($details->service_type)) {
                                        echo 'Other (' . htmlspecialchars($details->service_type) . ')';
                                    } else {
                                        echo htmlspecialchars($resolvedType ?: 'Unknown Service');
                                    }
                                    ?>
                                </span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <a href="<?php echo ROOT; ?>/ServiceProviderProfile/editService?id=<?php echo $service->id; ?>" class="btn btn-secondary" style="text-decoration: none; padding: 5px 10px; font-size: 14px;">
                                    <i class="bx bxs-edit"></i>
                                </a>
                                <button onclick="deleteService(<?php echo $service->id; ?>)" class="btn btn-danger" style="padding: 5px 10px; font-size: 14px;">
                                    <i class="bx bxs-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="form-row" style="margin-bottom: 10px;">
                            <div class="form-group">
                                <label class="form-label">Rate:</label>
                                <div class="form-input" style="background: #f8f9fa; cursor: default;">
                                    Rs. <?php echo number_format($details->rate_per_hour ?? 0, 2); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Rate Type:</label>
                                <div class="form-input" style="background: #f8f9fa; cursor: default;">
                                    <?php echo ucfirst($details->rate_type ?? 'hourly'); ?>
                                </div>
                            </div>
                        </div>
                        <?php if (isset($details->description) && $details->description): ?>
                            <div class="form-group" style="margin-bottom: 15px; width: 100%;">
                                <label class="form-label">Description</label>
                                <div class="form-input" style="background: #f8f9fa; cursor: default;">
                                    <?php echo nl2br(htmlspecialchars($details->description)); ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($serviceName === 'theater production' && $details): ?>
                        <?php
                            $afRaw = $details->available_facilities ?? '';
                            $afDisplay = is_array($afRaw) ? implode(', ', $afRaw) : $afRaw;
                            $tfRaw = $details->technical_facilities ?? '';
                            $tfDisplay = is_array($tfRaw) ? implode(', ', $tfRaw) : $tfRaw;
                        ?>
                        <div class="service-details" style="width: 100%; margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                            <h4 style="margin: 0 0 10px 0; color: #333;">Theater Production Details</h4>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Theatre Name</label>
                                    <div class="form-input" style="background: #fff; cursor: default;">
                                        <?php echo htmlspecialchars($details->theatre_name ?? ''); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Seating Capacity</label>
                                    <div class="form-input" style="background: #fff; cursor: default;">
                                        <?php echo htmlspecialchars($details->seating_capacity ?? ''); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Stage Dimensions (Width × Depth)</label>
                                    <div class="form-input" style="background: #fff; cursor: default;">
                                        <?php echo htmlspecialchars($details->stage_dimensions ?? ''); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Stage Type</label>
                                    <div class="form-input" style="background: #fff; cursor: default;">
                                        <?php echo htmlspecialchars($details->stage_type ?? ''); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Available Facilities</label>
                                    <div class="form-input" style="background: #fff; cursor: default;">
                                        <?php echo htmlspecialchars($afDisplay); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Technical Facilities</label>
                                    <div class="form-input" style="background: #fff; cursor: default;">
                                        <?php echo htmlspecialchars($tfDisplay); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Additional Equipment for Rent</label>
                                <div class="form-input" style="background: #fff; cursor: default;">
                                    <?php echo nl2br(htmlspecialchars($details->equipment_rent ?? '')); ?>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Stage Crew Available</label>
                                    <div class="form-input" style="background: #fff; cursor: default;">
                                        <?php echo htmlspecialchars($details->stage_crew_available ?? ''); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Location / Address</label>
                                <div class="form-input" style="background: #fff; cursor: default;">
                                    <?php echo nl2br(htmlspecialchars($details->location_address ?? '')); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Theatre Photos</label>
                                <?php if (!empty($details->theatre_photos)): ?>
                                    <p style="margin-top: 10px; font-size: 14px; color: #6b7280;">
                                        <a href="<?php echo ROOT . '/' . htmlspecialchars($details->theatre_photos); ?>" target="_blank" style="color: #3b82f6;">View Photos</a>
                                    </p>
                                <?php else: ?>
                                    <p style="margin-top: 10px; font-size: 14px; color: #6b7280;">No theatre photos uploaded</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($serviceName === 'lighting design' && $details): ?>
                        <div class="service-details" style="width: 100%; margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                            <h4 style="margin: 0 0 10px 0; color: #333;">Lighting Design Details</h4>
                            <div class="form-group">
                                <label class="form-label">Lighting Equipment Provided</label>
                                <div class="form-input" style="background: #fff; cursor: default;">
                                    <?php echo nl2br(htmlspecialchars($details->lighting_equipment_provided ?? '')); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Maximum Stage Size Supported</label>
                                <div class="form-input" style="background: #fff; cursor: default;">
                                    <?php echo htmlspecialchars($details->max_stage_size ?? ''); ?>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Lighting Design Service</label>
                                    <div class="form-input" style="background: #fff; cursor: default;">
                                        <?php echo htmlspecialchars($details->lighting_design_service ?? ''); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Lighting Crew Available</label>
                                    <div class="form-input" style="background: #fff; cursor: default;">
                                        <?php echo htmlspecialchars($details->lighting_crew_available ?? ''); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($serviceName === 'sound systems' && $details): ?>
                        <div class="service-details" style="width: 100%; margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                            <h4 style="margin: 0 0 10px 0; color: #333;">Sound Systems Details</h4>
                            <div class="form-group">
                                <label class="form-label">Sound Equipment Provided</label>
                                <div class="form-input" style="background: #fff; cursor: default;"><?php echo nl2br(htmlspecialchars($details->sound_equipment_provided ?? '')); ?></div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Maximum Audience Size Supported</label>
                                    <div class="form-input" style="background: #fff; cursor: default;"><?php echo htmlspecialchars($details->max_audience_size ?? ''); ?></div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Equipment Brands</label>
                                    <div class="form-input" style="background: #fff; cursor: default;"><?php echo htmlspecialchars($details->equipment_brands ?? ''); ?></div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Sound Effects Handling</label>
                                    <div class="form-input" style="background: #fff; cursor: default;"><?php echo htmlspecialchars($details->sound_effects_handling ?? ''); ?></div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Sound Engineer Included</label>
                                    <div class="form-input" style="background: #fff; cursor: default;"><?php echo htmlspecialchars($details->sound_engineer_included ?? ''); ?></div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($serviceName === 'video production' && $details): ?>
                        <div class="service-details" style="width: 100%; margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                            <h4 style="margin: 0 0 10px 0; color: #333;">Video Production Details</h4>
                            <div class="form-group">
                                <label class="form-label">Services Offered</label>
                                <div class="form-input" style="background: #fff; cursor: default;">
                                    <?php echo nl2br(htmlspecialchars($details->services_offered ?? '')); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Equipment Used</label>
                                <div class="form-input" style="background: #fff; cursor: default;">
                                    <?php echo nl2br(htmlspecialchars($details->equipment_used ?? '')); ?>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Number of Crew Members</label>
                                    <div class="form-input" style="background: #fff; cursor: default;"><?php echo htmlspecialchars($details->num_crew_members ?? ''); ?></div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Editing Software Used</label>
                                    <div class="form-input" style="background: #fff; cursor: default;"><?php echo htmlspecialchars($details->editing_software ?? ''); ?></div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Drone Service Available</label>
                                    <div class="form-input" style="background: #fff; cursor: default;"><?php echo htmlspecialchars($details->drone_service_available ?? ''); ?></div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Maximum Video Resolution</label>
                                    <div class="form-input" style="background: #fff; cursor: default;"><?php echo htmlspecialchars($details->max_video_resolution ?? ''); ?></div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Photo Editing Included</label>
                                    <div class="form-input" style="background: #fff; cursor: default;"><?php echo htmlspecialchars($details->photo_editing_included ?? ''); ?></div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Delivery Time for Final Output</label>
                                    <div class="form-input" style="background: #fff; cursor: default;"><?php echo htmlspecialchars($details->delivery_time ?? ''); ?></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Raw Footage/Photos Provided</label>
                                <div class="form-input" style="background: #fff; cursor: default;"><?php echo htmlspecialchars($details->raw_footage_provided ?? ''); ?></div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Portfolio / Sample Links</label>
                                <div class="form-input" style="background: #fff; cursor: default;">
                                    <?php if (!empty($details->portfolio_links)): ?>
                                        <a href="<?php echo htmlspecialchars($details->portfolio_links); ?>" target="_blank" style="color: #3b82f6;">View Portfolio</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if (!empty($details->sample_videos)): ?>
                            <div class="form-group">
                                <label class="form-label">Sample Photos/Videos</label>
                                <div style="margin-top: 8px;">
                                    <a href="<?php echo ROOT . '/' . htmlspecialchars($details->sample_videos); ?>" target="_blank">View sample</a>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <?php if ($serviceName === 'set design' && $details): ?>
                        <div class="service-details" style="width: 100%; margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                            <h4 style="margin: 0 0 10px 0; color: #333;">Set Design Details</h4>
                            <div class="form-group">
                                <label class="form-label">Types of Sets Designed</label>
                                <div class="form-input" style="background: #fff; cursor: default;"><?php echo nl2br(htmlspecialchars($details->types_of_sets_designed ?? '')); ?></div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Set Construction Provided</label>
                                    <div class="form-input" style="background: #fff; cursor: default;"><?php echo htmlspecialchars($details->set_construction_provided ?? ''); ?></div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Stage Installation Support</label>
                                    <div class="form-input" style="background: #fff; cursor: default;"><?php echo htmlspecialchars($details->stage_installation_support ?? ''); ?></div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Maximum Stage Size Supported</label>
                                    <div class="form-input" style="background: #fff; cursor: default;"><?php echo htmlspecialchars($details->max_stage_size_supported ?? ''); ?></div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Materials Used</label>
                                    <div class="form-input" style="background: #fff; cursor: default;"><?php echo nl2br(htmlspecialchars($details->materials_used ?? '')); ?></div>
                                </div>
                            </div>
                            <?php if (!empty($details->sample_set_designs)): ?>
                            <div class="form-group">
                                <label class="form-label">Sample Set Designs</label>
                                <div class="form-input" style="background: #fff; cursor: default;">
                                    <a href="<?php echo ROOT . '/' . htmlspecialchars($details->sample_set_designs); ?>" target="_blank">View sample</a>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <?php if ($serviceName === 'costume design' && $details): ?>
                        <div class="service-details" style="width: 100%; margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                            <h4 style="margin: 0 0 10px 0; color: #333;">Costume Design Details</h4>
                            <div class="form-group">
                                <label class="form-label">Types of Costumes Provided</label>
                                <div class="form-input" style="background: #fff; cursor: default;">
                                    <?php echo nl2br(htmlspecialchars($details->types_of_costumes_provided ?? '')); ?>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Custom Costume Design Available</label>
                                    <div class="form-input" style="background: #fff; cursor: default;">
                                        <?php echo htmlspecialchars($details->custom_costume_design_available ?? ''); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Available Sizes</label>
                                    <div class="form-input" style="background: #fff; cursor: default;">
                                        <?php echo htmlspecialchars($details->available_sizes ?? ''); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Alterations Provided</label>
                                    <div class="form-input" style="background: #fff; cursor: default;">
                                        <?php echo htmlspecialchars($details->alterations_provided ?? ''); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Number of Costumes Available</label>
                                    <div class="form-input" style="background: #fff; cursor: default;">
                                        <?php echo htmlspecialchars($details->number_of_costumes_available ?? ''); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($serviceName === 'makeup & hair' && $details): ?>
                        <div class="service-details" style="width: 100%; margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                            <h4 style="margin: 0 0 10px 0; color: #333;">Makeup & Hair Details</h4>
                            <div class="form-group">
                                <label class="form-label">Type of Make-up Services</label>
                                <div class="form-input" style="background: #fff; cursor: default;">
                                    <?php echo nl2br(htmlspecialchars($details->type_of_makeup_services ?? '')); ?>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Experience in Stage Make-up (years)</label>
                                    <div class="form-input" style="background: #fff; cursor: default;">
                                        <?php echo htmlspecialchars($details->experience_stage_makeup_years ?? ''); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Maximum Actors Per Show</label>
                                    <div class="form-input" style="background: #fff; cursor: default;">
                                        <?php echo htmlspecialchars($details->maximum_actors_per_show ?? ''); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Character-based Make-up Available</label>
                                    <div class="form-input" style="background: #fff; cursor: default;">
                                        <?php echo htmlspecialchars($details->character_based_makeup_available ?? ''); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Can Handle Full Cast</label>
                                    <div class="form-input" style="background: #fff; cursor: default;">
                                        <?php echo htmlspecialchars($details->can_handle_full_cast ?? ''); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Bring Own Make-up Kit</label>
                                    <div class="form-input" style="background: #fff; cursor: default;">
                                        <?php echo htmlspecialchars($details->bring_own_makeup_kit ?? ''); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">On-site Service Available</label>
                                    <div class="form-input" style="background: #fff; cursor: default;">
                                        <?php echo htmlspecialchars($details->onsite_service_available ?? ''); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Touch-up Service During Show</label>
                                <div class="form-input" style="background: #fff; cursor: default;">
                                    <?php echo htmlspecialchars($details->touchup_service_during_show ?? ''); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Traditional/Cultural Make-up Expertise</label>
                                <div class="form-input" style="background: #fff; cursor: default;">
                                    <?php echo nl2br(htmlspecialchars($details->traditional_cultural_makeup_expertise ?? '')); ?>
                                </div>
                            </div>
                            <?php if (!empty($details->sample_makeup_photos)): ?>
                            <div class="form-group">
                                <label class="form-label">Sample Make-up Photos</label>
                                <div style="margin-top: 8px;">
                                    <a href="<?php echo ROOT . '/' . htmlspecialchars($details->sample_makeup_photos); ?>" target="_blank">View sample</a>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <?php if ($serviceName === 'other' && $details): ?>
                        <div class="service-details" style="width: 100%; margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                            <h4 style="margin: 0 0 10px 0; color: #333;">Other Service Details</h4>
                            <div class="form-group">
                                <label class="form-label">Service Type</label>
                                <div class="form-input" style="background: #fff; cursor: default;">
                                    <?php echo htmlspecialchars($details->service_type ?? ''); ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
                </div>

                <!-- Recent Projects Section -->
                <div class="section">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                        <h3>Recent Projects</h3>
                        <a href="<?php echo ROOT; ?>/ServiceProviderProfile/addProject?provider_id=<?php echo $data['provider_id']; ?>" class="btn" style="text-decoration: none; padding: 8px 16px; font-size: 13px;">
                            <i class="bx bxs-plus"></i> Add Project
                        </a>
                    </div>
                    
                    <?php if (empty($data['projects'])): ?>
                        <p style="color: var(--muted); padding: 20px; text-align: center; background: linear-gradient(180deg, #fffdf7, #fff7e6); border-radius: 12px;">No projects added yet.</p>
                    <?php else: ?>
                <?php foreach ($data['projects'] as $project): ?>
                    <div class="project-item">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                            <div style="flex: 1;">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Year</label>
                                        <div class="form-input" style="background: #f8f9fa; cursor: default;">
                                            <?php echo htmlspecialchars($project->year); ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Project Name</label>
                                        <div class="form-input" style="background: #f8f9fa; cursor: default;">
                                            <?php echo htmlspecialchars($project->project_name); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div style="display: flex; gap: 10px; margin-left: 15px;">
                                <a href="<?php echo ROOT; ?>/ServiceProviderProfile/editProject?id=<?php echo $project->id; ?>" class="btn btn-secondary" style="text-decoration: none; padding: 5px 10px; font-size: 14px;">
                                    <i class="bx bxs-edit"></i> Edit
                                </a>
                                <button onclick="deleteProject(<?php echo $project->id; ?>)" class="btn btn-danger" style="padding: 5px 10px; font-size: 14px;">
                                    <i class="bx bxs-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                        <?php if ($project->services_provided): ?>
                            <div class="form-group">
                                <label class="form-label">Services Provided</label>
                                <div class="form-input" style="background: #f8f9fa; cursor: default;">
                                    <?php echo htmlspecialchars($project->services_provided); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if ($project->description): ?>
                            <div class="form-group">
                                <label class="form-label">Description</label>
                                <div class="form-input textarea" style="background: #f8f9fa; cursor: default;">
                                    <?php echo nl2br(htmlspecialchars($project->description)); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
                </div>

                <!-- Profile Statistics Section -->
                <div class="section">
                    <h3>Profile Statistics</h3>
                    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px;">
                        <div style="background: linear-gradient(180deg, #fffdf7, #fff7e6); border: 1px solid #f0dfb4; border-radius: 12px; padding: 16px; text-align: center;">
                            <div style="font-size: 24px; font-weight: 700; color: #5a4415;"><?php echo $data['total_projects']; ?></div>
                            <div style="font-size: 13px; color: #7a6121; margin-top: 6px;">Total Projects</div>
                        </div>
                        <div style="background: linear-gradient(180deg, #fffdf7, #fff7e6); border: 1px solid #f0dfb4; border-radius: 12px; padding: 16px; text-align: center;">
                            <div style="font-size: 24px; font-weight: 700; color: #5a4415;"><?php echo $data['provider']->years_experience; ?></div>
                            <div style="font-size: 13px; color: #7a6121; margin-top: 6px;">Years Experience</div>
                        </div>
                        <div style="background: linear-gradient(180deg, #fffdf7, #fff7e6); border: 1px solid #f0dfb4; border-radius: 12px; padding: 16px; text-align: center;">
                            <div style="font-size: 24px; font-weight: 700; color: #5a4415;"><?php echo count($data['services']); ?></div>
                            <div style="font-size: 13px; color: #7a6121; margin-top: 6px;">Services Offered</div>
                        </div>
                        <div style="background: linear-gradient(180deg, #fffdf7, #fff7e6); border: 1px solid #f0dfb4; border-radius: 12px; padding: 16px; text-align: center;">
                            <div style="font-size: 24px; font-weight: 700; color: #5a4415;"><?php echo $data['provider']->availability ? 'Available' : 'Unavailable'; ?></div>
                            <div style="font-size: 13px; color: #7a6121; margin-top: 6px;">Current Status</div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

    </div>

    <script src="<?= ROOT ?>/assets/JS/service_provider_profile.js"></script>
    <script>
        // Inline profile image upload validation
        const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB
        const ALLOWED_TYPES = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];

        function handleProfileImageSelect(event) {
            const file = event.target.files[0];
            const errorDiv = document.getElementById('profileImageError');
            const fileNameEl = document.getElementById('selectedProfileImageName');
            
            errorDiv.style.display = 'none';
            errorDiv.textContent = '';
            
            if (!file) {
                fileNameEl.textContent = '<?php echo !empty($data['provider']->profile_image) ? htmlspecialchars($data['provider']->profile_image) : 'user_profile.png'; ?>';
                return;
            }

            fileNameEl.textContent = file.name;

            if (!ALLOWED_TYPES.includes(file.type)) {
                errorDiv.textContent = 'Invalid file type. Only JPG, PNG and GIF images are allowed.';
                errorDiv.style.display = 'block';
                event.target.value = '';
                fileNameEl.textContent = '<?php echo !empty($data['provider']->profile_image) ? htmlspecialchars($data['provider']->profile_image) : 'user_profile.png'; ?>';
                return;
            }

            if (file.size > MAX_FILE_SIZE) {
                errorDiv.textContent = 'File too large. Maximum size is 5MB.';
                errorDiv.style.display = 'block';
                event.target.value = '';
                fileNameEl.textContent = '<?php echo !empty($data['provider']->profile_image) ? htmlspecialchars($data['provider']->profile_image) : 'user_profile.png'; ?>';
                return;
            }

            document.getElementById('profileInlineImageForm').submit();
        }
    </script>
</body>
</html>
