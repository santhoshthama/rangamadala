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
                    <label class="form-label">Website</label>
                    <div class="form-input" style="background: #f8f9fa; cursor: default;">
                        <?php echo $data['provider']->website ? htmlspecialchars($data['provider']->website) : 'Not provided'; ?>
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
                    $serviceName = strtolower(trim($service->service_name));
                    ?>
                    <div class="service-item" style="display: flex; flex-direction: column; align-items: flex-start;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; width: 100%; margin-bottom: 15px;">
                            <div style="display: flex; align-items: center;">
                                <input type="checkbox" class="checkbox" checked disabled style="margin-right: 10px;">
                                <span style="font-size: 24px; font-weight: 600; color: #333;"><?php echo htmlspecialchars($service->service_name); ?></span>
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
                        <div style="margin-bottom: 10px;">
                            <label class="form-label">Rate per hour:</label>
                            <div class="form-input" style="background: #f8f9fa; display: inline-block; width: 200px;">
                                Rs. <?php echo number_format($service->rate_per_hour, 2); ?>
                            </div>
                        </div>
                        <?php if ($service->description): ?>
                            <div class="form-group" style="margin-bottom: 15px; width: 100%;">
                                <label class="form-label">Description</label>
                                <div class="form-input" style="background: #f8f9fa; cursor: default;">
                                    <?php echo nl2br(htmlspecialchars($service->description)); ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($serviceName === 'theater production' && $details): ?>
                        <div class="service-details" style="width: 100%; margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                            <h4 style="margin: 0 0 10px 0; color: #333;">Theater Production Details</h4>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Number of Actors</label>
                                    <div class="form-input" style="background: #fff; cursor: default;"><?php echo htmlspecialchars($details->num_actors ?? ''); ?></div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Expected Audience</label>
                                    <div class="form-input" style="background: #fff; cursor: default;"><?php echo htmlspecialchars($details->expected_audience ?? ''); ?></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Stage Requirements</label>
                                <div style="display: flex; gap: 15px; padding: 10px; background: #fff; border-radius: 6px;">
                                    <span><?php echo $details->stage_proscenium ? '✓' : '○'; ?> Proscenium</span>
                                    <span><?php echo $details->stage_black_box ? '✓' : '○'; ?> Black box</span>
                                    <span><?php echo $details->stage_open_floor ? '✓' : '○'; ?> Open floor</span>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Seating Requirement</label>
                                    <div class="form-input" style="background: #fff; cursor: default;"><?php echo htmlspecialchars($details->seating_requirement ?? ''); ?></div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Parking Requirement</label>
                                    <div class="form-input" style="background: #fff; cursor: default;"><?php echo htmlspecialchars($details->parking_requirement ?? ''); ?></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Special Technical Requirements</label>
                                <div class="form-input" style="background: #fff; cursor: default;"><?php echo nl2br(htmlspecialchars($details->special_tech ?? '')); ?></div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($serviceName === 'lighting design' && $details): ?>
                        <div class="service-details" style="width: 100%; margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                            <h4 style="margin: 0 0 10px 0; color: #333;">Lighting Design Details</h4>
                            <div class="form-group">
                                <label class="form-label">Lighting Services</label>
                                <div style="display: flex; flex-wrap: wrap; gap: 15px; padding: 10px; background: #fff; border-radius: 6px;">
                                    <span><?php echo $details->stage_lighting ? '✓' : '○'; ?> Stage Lighting</span>
                                    <span><?php echo $details->spotlights ? '✓' : '○'; ?> Spotlights</span>
                                    <span><?php echo $details->custom_programming ? '✓' : '○'; ?> Custom Programming</span>
                                    <span><?php echo $details->moving_heads ? '✓' : '○'; ?> Moving Heads</span>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Number of Lights</label>
                                    <div class="form-input" style="background: #fff; cursor: default;"><?php echo htmlspecialchars($details->num_lights ?? ''); ?></div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Lighting Effects</label>
                                    <div class="form-input" style="background: #fff; cursor: default;"><?php echo htmlspecialchars($details->effects ?? ''); ?></div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Technician Needed</label>
                                    <div class="form-input" style="background: #fff; cursor: default;"><?php echo htmlspecialchars($details->technician_needed ?? ''); ?></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Additional Notes</label>
                                <div class="form-input" style="background: #fff; cursor: default;"><?php echo nl2br(htmlspecialchars($details->notes ?? '')); ?></div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($serviceName === 'sound systems' && $details): ?>
                        <div class="service-details" style="width: 100%; margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                            <h4 style="margin: 0 0 10px 0; color: #333;">Sound Systems Details</h4>
                            <div class="form-group">
                                <label class="form-label">Sound Services</label>
                                <div style="display: flex; flex-wrap: wrap; gap: 15px; padding: 10px; background: #fff; border-radius: 6px;">
                                    <span><?php echo $details->pa_system ? '✓' : '○'; ?> PA system</span>
                                    <span><?php echo $details->microphones ? '✓' : '○'; ?> Microphones</span>
                                    <span><?php echo $details->sound_mixing ? '✓' : '○'; ?> Sound Mixing</span>
                                    <span><?php echo $details->background_music ? '✓' : '○'; ?> Background Music</span>
                                    <span><?php echo $details->special_effects ? '✓' : '○'; ?> Special Effects</span>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Number of Mics</label>
                                    <div class="form-input" style="background: #fff; cursor: default;"><?php echo htmlspecialchars($details->num_mics ?? ''); ?></div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Stage Monitor</label>
                                    <div class="form-input" style="background: #fff; cursor: default;"><?php echo htmlspecialchars($details->stage_monitor ?? ''); ?></div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Sound Engineer</label>
                                    <div class="form-input" style="background: #fff; cursor: default;"><?php echo htmlspecialchars($details->sound_engineer ?? ''); ?></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Additional Notes</label>
                                <div class="form-input" style="background: #fff; cursor: default;"><?php echo nl2br(htmlspecialchars($details->notes ?? '')); ?></div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($serviceName === 'video production' && $details): ?>
                        <div class="service-details" style="width: 100%; margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                            <h4 style="margin: 0 0 10px 0; color: #333;">Video Production Details</h4>
                            <div class="form-group">
                                <label class="form-label">Video Purpose</label>
                                <div style="display: flex; flex-wrap: wrap; gap: 15px; padding: 10px; background: #fff; border-radius: 6px;">
                                    <span><?php echo $details->full_event ? '✓' : '○'; ?> Full Event Recording</span>
                                    <span><?php echo $details->highlight_reel ? '✓' : '○'; ?> Highlight Reel</span>
                                    <span><?php echo $details->short_promo ? '✓' : '○'; ?> Short Promo</span>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Number of Cameras</label>
                                    <div class="form-input" style="background: #fff; cursor: default;"><?php echo htmlspecialchars($details->num_cameras ?? ''); ?></div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Drone Coverage</label>
                                    <div class="form-input" style="background: #fff; cursor: default;"><?php echo htmlspecialchars($details->drone_needed ?? ''); ?></div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Gimbals/Steadicams</label>
                                    <div class="form-input" style="background: #fff; cursor: default;"><?php echo htmlspecialchars($details->gimbals ?? ''); ?></div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Editing Required</label>
                                    <div class="form-input" style="background: #fff; cursor: default;"><?php echo htmlspecialchars($details->editing ?? ''); ?></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Delivery Format</label>
                                <div style="display: flex; flex-wrap: wrap; gap: 15px; padding: 10px; background: #fff; border-radius: 6px;">
                                    <span><?php echo $details->delivery_mp4 ? '✓' : '○'; ?> MP4</span>
                                    <span><?php echo $details->delivery_raw ? '✓' : '○'; ?> RAW files</span>
                                    <span><?php echo $details->delivery_social ? '✓' : '○'; ?> Social Media Format</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Additional Notes</label>
                                <div class="form-input" style="background: #fff; cursor: default;"><?php echo nl2br(htmlspecialchars($details->notes ?? '')); ?></div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($serviceName === 'set design' && $details): ?>
                        <div class="service-details" style="width: 100%; margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                            <h4 style="margin: 0 0 10px 0; color: #333;">Set Design Details</h4>
                            <div class="form-group">
                                <label class="form-label">Service Type</label>
                                <div style="display: flex; flex-wrap: wrap; gap: 15px; padding: 10px; background: #fff; border-radius: 6px;">
                                    <span><?php echo $details->set_design ? '✓' : '○'; ?> Set Design</span>
                                    <span><?php echo $details->set_construction ? '✓' : '○'; ?> Set Construction</span>
                                    <span><?php echo $details->set_rental ? '✓' : '○'; ?> Set Rental</span>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Production Stage</label>
                                    <div class="form-input" style="background: #fff; cursor: default;"><?php echo htmlspecialchars($details->production_stage ?? ''); ?></div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Materials</label>
                                    <div class="form-input" style="background: #fff; cursor: default;"><?php echo htmlspecialchars($details->materials ?? ''); ?></div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Dimensions</label>
                                    <div class="form-input" style="background: #fff; cursor: default;"><?php echo htmlspecialchars($details->dimensions ?? ''); ?></div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Budget Range</label>
                                    <div class="form-input" style="background: #fff; cursor: default;">Rs. <?php echo htmlspecialchars($details->budget_range ?? ''); ?></div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Deadline</label>
                                    <div class="form-input" style="background: #fff; cursor: default;"><?php echo htmlspecialchars($details->deadline ?? ''); ?></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Additional Notes</label>
                                <div class="form-input" style="background: #fff; cursor: default;"><?php echo nl2br(htmlspecialchars($details->notes ?? '')); ?></div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($serviceName === 'costume design' && $details): ?>
                        <div class="service-details" style="width: 100%; margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                            <h4 style="margin: 0 0 10px 0; color: #333;">Costume Design Details</h4>
                            <div class="form-group">
                                <label class="form-label">Service Type</label>
                                <div style="display: flex; flex-wrap: wrap; gap: 15px; padding: 10px; background: #fff; border-radius: 6px;">
                                    <span><?php echo $details->costume_design ? '✓' : '○'; ?> Costume Design</span>
                                    <span><?php echo $details->costume_creation ? '✓' : '○'; ?> Costume Creation</span>
                                    <span><?php echo $details->costume_rental ? '✓' : '○'; ?> Costume Rental</span>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Number of Characters</label>
                                    <div class="form-input" style="background: #fff; cursor: default;"><?php echo htmlspecialchars($details->num_characters ?? ''); ?></div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Number of Costumes</label>
                                    <div class="form-input" style="background: #fff; cursor: default;"><?php echo htmlspecialchars($details->num_costumes ?? ''); ?></div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Measurements Required</label>
                                    <div class="form-input" style="background: #fff; cursor: default;"><?php echo htmlspecialchars($details->measurements_required ?? ''); ?></div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Fitting Dates</label>
                                    <div class="form-input" style="background: #fff; cursor: default;"><?php echo htmlspecialchars($details->fitting_dates ?? ''); ?></div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Budget Range</label>
                                    <div class="form-input" style="background: #fff; cursor: default;">Rs. <?php echo htmlspecialchars($details->budget_range ?? ''); ?></div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Deadline</label>
                                    <div class="form-input" style="background: #fff; cursor: default;"><?php echo htmlspecialchars($details->deadline ?? ''); ?></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Additional Notes</label>
                                <div class="form-input" style="background: #fff; cursor: default;"><?php echo nl2br(htmlspecialchars($details->notes ?? '')); ?></div>
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
