<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/service provider/service_provider_dashboard.css">
    <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/service provider/service_provider_profile.css">
    <link rel="shortcut icon" href="<?= ROOT ?>/assets/images/Rangamadala logo.png" type="image/x-icon">
    <title><?= isset($pageTitle) ? $pageTitle : 'Profile' ?> - <?php echo htmlspecialchars($provider->full_name ?? 'Rangamadala'); ?></title>
</head>
<body>
    <?php $activePage = 'profile'; include 'includes/service_provider/sidebar.php'; ?>

    <div class="main--content">
        <?php include 'includes/service_provider/header.php'; ?>

        <div class="container">
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <!-- Header -->
        <div class="header">
            <?php 
            // Profile image (SEPARATE from business certificate)
            $profileImage = ROOT . '/uploads/profile_images/default_user.jpg';
            if (!empty($data['provider']->profile_image)) {
                $profileImage = ROOT . '/uploads/profile_images/' . $data['provider']->profile_image;
            }
            ?>
            <div style="position: relative; display: inline-block; margin-bottom: 20px;">
                <img id="currentProfileImage" src="<?php echo $profileImage; ?>" 
                     alt="Profile Picture" 
                     style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid #fff; box-shadow: 0 4px 8px rgba(0,0,0,0.1);"
                     onerror="this.src='<?= ROOT ?>/uploads/profile_images/default_user.jpg'">
                <button onclick="openImageUploadModal()" 
                        class="btn" 
                        title="Change profile picture"
                        style="position: absolute; bottom: 0; right: 0; padding: 8px; border-radius: 50%; width: 36px; height: 36px;">
                    <i class="fas fa-camera"></i>
                </button>
            </div>
            
            <h1 class="page-title"><?php echo htmlspecialchars($data['provider']->full_name); ?></h1>
            <p class="title"><?php echo htmlspecialchars($data['provider']->professional_title); ?></p>
            <p class="last-updated">Last updated: <?php echo date('F j, Y'); ?></p>
        </div>

        <!-- Profile Image Upload Modal -->
        <div id="imageUploadModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
            <div style="background: white; padding: 30px; border-radius: 10px; max-width: 500px; width: 90%; box-shadow: 0 4px 20px rgba(0,0,0,0.3);">
                <h3 style="margin: 0 0 20px 0; color: #333;">Change Profile Picture</h3>
                
                <!-- Preview Area -->
                <div style="text-align: center; margin-bottom: 20px;">
                    <img id="imagePreview" src="" alt="Preview" style="max-width: 100%; max-height: 300px; border-radius: 10px; display: none; border: 2px solid #ddd;">
                    <p id="noImageText" style="color: #999; padding: 40px;">No image selected</p>
                </div>

                <!-- File Info -->
                <div id="fileInfo" style="display: none; background: #f8f9fa; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 14px;">
                    <p style="margin: 5px 0;"><strong>File:</strong> <span id="fileName"></span></p>
                    <p style="margin: 5px 0;"><strong>Size:</strong> <span id="fileSize"></span></p>
                </div>

                <!-- Error Message -->
                <div id="modalError" style="display: none; background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px; border: 1px solid #f5c6cb;"></div>

                <!-- File Input -->
                <form id="profileImageForm" method="POST" enctype="multipart/form-data" action="<?= ROOT ?>/ServiceProviderProfile/uploadProfileImage">
                    <input type="file" id="profileImageUpload" name="profile_image" accept="image/jpeg,image/jpg,image/png,image/gif" style="display: block; width: 100%; padding: 10px; margin-bottom: 20px; border: 2px dashed #ddd; border-radius: 5px; cursor: pointer;" onchange="handleImageSelect(event)">
                    
                    <!-- Buttons -->
                    <div style="display: flex; gap: 10px; justify-content: flex-end;">
                        <button type="button" onclick="closeImageUploadModal()" class="btn btn-secondary" style="padding: 10px 20px;">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="button" id="uploadBtn" onclick="confirmUpload()" class="btn" style="padding: 10px 20px;" disabled>
                            <i class="fas fa-upload"></i> Upload
                        </button>
                    </div>
                </form>

                <!-- Loading Overlay -->
                <div id="uploadingOverlay" style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.9); align-items: center; justify-content: center; border-radius: 10px;">
                    <div style="text-align: center;">
                        <i class="fas fa-spinner fa-spin" style="font-size: 48px; color: #007bff; margin-bottom: 15px;"></i>
                        <p style="color: #333; font-size: 16px;">Uploading...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Basic Information -->
        <div class="section">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 class="section-title" style="margin: 0;">Basic Information</h2>
                <a href="<?php echo ROOT; ?>/ServiceProviderProfile/editBasicInfo?id=<?php echo $data['provider_id']; ?>" class="btn" style="text-decoration: none;">
                    <i class="fas fa-edit"></i> Edit
                </a>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <div class="form-input" style="background: #f8f9fa; cursor: default;">
                        <?php echo htmlspecialchars($data['provider']->full_name); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Professional Title</label>
                    <div class="form-input" style="background: #f8f9fa; cursor: default;">
                        <?php echo htmlspecialchars($data['provider']->professional_title); ?>
                    </div>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <div class="form-input" style="background: #f8f9fa; cursor: default;">
                        <?php echo htmlspecialchars($data['provider']->email); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Phone Number</label>
                    <div class="form-input" style="background: #f8f9fa; cursor: default;">
                        <?php echo htmlspecialchars($data['provider']->phone); ?>
                    </div>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Location</label>
                    <div class="form-input" style="background: #f8f9fa; cursor: default;">
                        <?php echo htmlspecialchars($data['provider']->location); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Social Media Link</label>
                    <div class="form-input" style="background: #f8f9fa; cursor: default;">
                        <?php echo $data['provider']->social_media_link ? htmlspecialchars($data['provider']->social_media_link) : 'Not provided'; ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Professional Summary</label>
                <div class="form-input textarea" style="background: #f8f9fa; cursor: default; min-height: 100px;">
                    <?php echo nl2br(htmlspecialchars($data['provider']->professional_summary)); ?>
                </div>
            </div>
        </div>

        <!-- Availability -->
        <div class="section">
            <h2 class="section-title">Availability</h2>
            <div class="availability-toggle">
                <span>Currently Available for New Projects</span>
                <div class="toggle <?php echo $data['provider']->availability ? 'active' : ''; ?>" style="pointer-events: none;"></div>
            </div>
            <div class="form-group">
                <label class="form-label">Availability Notes</label>
                <div class="form-input" style="background: #f8f9fa; cursor: default;">
                    <?php echo $data['provider']->availability_notes ? htmlspecialchars($data['provider']->availability_notes) : 'No notes provided'; ?>
                </div>
            </div>
        </div>

        <!-- Change Password -->
        <div class="section">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 class="section-title" style="margin: 0;">Password</h2>
                <a href="<?php echo ROOT; ?>/ServiceProviderProfile/changePassword" class="btn" style="text-decoration: none;">
                    <i class="fas fa-key"></i> Change Password
                </a>
            </div>
            <p style="color: #6c757d; padding: 20px; background: #f8f9fa; border-radius: 5px;">
                Keep your account secure by changing your password regularly.
            </p>
        </div>

        <!-- Services & Rates -->
        <div class="section">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 class="section-title" style="margin: 0;">Services & Rates</h2>
                <a href="<?php echo ROOT; ?>/ServiceProviderProfile/addService?provider_id=<?php echo $data['provider_id']; ?>" class="btn" style="text-decoration: none;">
                    <i class="fas fa-plus"></i> Add Service
                </a>
            </div>
            
            <?php if (empty($data['services'])): ?>
                <p style="color: #6c757d; padding: 20px; text-align: center;">No services added yet.</p>
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
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="deleteService(<?php echo $service->id; ?>)" class="btn btn-danger" style="padding: 5px 10px; font-size: 14px;">
                                    <i class="fas fa-trash"></i>
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

        <!-- Recent Projects -->
        <div class="section">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 class="section-title" style="margin: 0;">Recent Projects</h2>
                <a href="<?php echo ROOT; ?>/ServiceProviderProfile/addProject?provider_id=<?php echo $data['provider_id']; ?>" class="btn" style="text-decoration: none;">
                    <i class="fas fa-plus"></i> Add Project
                </a>
            </div>
            
            <?php if (empty($data['projects'])): ?>
                <p style="color: #6c757d; padding: 20px; text-align: center;">No projects added yet.</p>
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
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <button onclick="deleteProject(<?php echo $project->id; ?>)" class="btn btn-danger" style="padding: 5px 10px; font-size: 14px;">
                                    <i class="fas fa-trash"></i> Delete
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

        <!-- Quick Stats -->
        <div class="section">
            <h2 class="section-title">Profile Statistics</h2>
            <div class="stats-grid">
                <div class="stat-box">
                    <div class="stat-value"><?php echo $data['total_projects']; ?></div>
                    <div class="stat-label">Total Projects</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value"><?php echo $data['provider']->years_experience; ?></div>
                    <div class="stat-label">Years Experience</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value"><?php echo count($data['services']); ?></div>
                    <div class="stat-label">Services Offered</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value"><?php echo $data['provider']->availability ? 'Available' : 'Unavailable'; ?></div>
                    <div class="stat-label">Current Status</div>
                </div>
            </div>
            </div>
        </div>
    </div>

    <script src="<?= ROOT ?>/assets/JS/service_provider_profile.js"></script>
    <script>
        // Profile Image Upload Functions
        let selectedFile = null;
        const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB
        const ALLOWED_TYPES = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];

        function openImageUploadModal() {
            document.getElementById('imageUploadModal').style.display = 'flex';
            resetModal();
        }

        function closeImageUploadModal() {
            document.getElementById('imageUploadModal').style.display = 'none';
            resetModal();
        }

        function resetModal() {
            selectedFile = null;
            document.getElementById('profileImageUpload').value = '';
            document.getElementById('imagePreview').style.display = 'none';
            document.getElementById('noImageText').style.display = 'block';
            document.getElementById('fileInfo').style.display = 'none';
            document.getElementById('modalError').style.display = 'none';
            document.getElementById('uploadBtn').disabled = true;
        }

        function handleImageSelect(event) {
            const file = event.target.files[0];
            const errorDiv = document.getElementById('modalError');
            
            errorDiv.style.display = 'none';
            
            if (!file) {
                resetModal();
                return;
            }

            // Validate file type
            const fileType = file.type;
            if (!ALLOWED_TYPES.includes(fileType)) {
                showError('Invalid file type. Only JPG, PNG and GIF images are allowed.');
                event.target.value = '';
                return;
            }

            // Validate file size
            if (file.size > MAX_FILE_SIZE) {
                showError('File too large. Maximum size is 5MB.');
                event.target.value = '';
                return;
            }

            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('imagePreview').src = e.target.result;
                document.getElementById('imagePreview').style.display = 'block';
                document.getElementById('noImageText').style.display = 'none';
                
                // Show file info
                document.getElementById('fileName').textContent = file.name;
                document.getElementById('fileSize').textContent = formatFileSize(file.size);
                document.getElementById('fileInfo').style.display = 'block';
                
                // Enable upload button
                document.getElementById('uploadBtn').disabled = false;
                selectedFile = file;
            };
            reader.readAsDataURL(file);
        }

        function showError(message) {
            const errorDiv = document.getElementById('modalError');
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
            document.getElementById('uploadBtn').disabled = true;
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }

        function confirmUpload() {
            if (!selectedFile) {
                showError('Please select an image first.');
                return;
            }

            if (confirm('Are you sure you want to upload this image as your profile picture?')) {
                // Show loading overlay
                document.getElementById('uploadingOverlay').style.display = 'flex';
                
                // Submit form
                document.getElementById('profileImageForm').submit();
            }
        }

        // Close modal on outside click
        document.getElementById('imageUploadModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeImageUploadModal();
            }
        });
    </script>
</body>
</html>
