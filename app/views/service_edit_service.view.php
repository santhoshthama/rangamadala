<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Service</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/CSS/service provider/service_provider_profile.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/CSS/service provider/service_provider_register.css">
</head>
<body>
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

            <div class="profile-section">
                <h2 class="section-title">Edit Service</h2>
                
                <form method="POST" enctype="multipart/form-data">
                    <!-- Service Type and Basic Info -->
                    <div class="form-group">
                        <label class="form-label">Service Type</label>
                        <input type="text" class="form-input" value="<?php echo htmlspecialchars($data['service']->service_type ?? ''); ?>" disabled style="background: #f3f4f6; cursor: not-allowed;">
                        <input type="hidden" name="service_name" value="<?php echo htmlspecialchars($data['service']->service_type ?? ''); ?>">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Rate Type</label>
                            <select name="rate_type" class="form-input">
                                <option value="hourly" <?php echo (isset($data['service']->rate_type) && $data['service']->rate_type === 'hourly') ? 'selected' : ''; ?>>Per Hour</option>
                                <option value="daily" <?php echo (isset($data['service']->rate_type) && $data['service']->rate_type === 'daily') ? 'selected' : ''; ?>>Per Day</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Rate (Rs)</label>
                            <input type="number" name="rate" class="form-input" placeholder="0.00" step="0.01" value="<?php echo htmlspecialchars($data['service']->rate_per_hour ?? ''); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-input textarea" placeholder="Describe your service..."><?php echo htmlspecialchars($data['details']->description ?? ''); ?></textarea>
                    </div>

                    <?php 
                    $serviceName = strtolower(trim($data['service']->service_type ?? ''));
                    $details = $data['details'] ?? null;
                    ?>

                    <!-- Service-Specific Fields -->
                    <?php if ($serviceName === 'theater production' && $details): ?>
                    <h3 class="section-title" style="margin-top: 20px;">Theater Production Details</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Theatre Name</label>
                            <input type="text" name="theatre_name" class="form-input" placeholder="e.g., City Hall Theatre" value="<?php echo htmlspecialchars($details->theatre_name ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Seating Capacity</label>
                            <input type="number" name="seating_capacity" class="form-input" placeholder="Total audience capacity" value="<?php echo htmlspecialchars($details->seating_capacity ?? ''); ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Stage Dimensions (Width × Depth)</label>
                            <input type="text" name="stage_dimensions" class="form-input" placeholder="e.g., 30ft × 20ft" value="<?php echo htmlspecialchars($details->stage_dimensions ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Stage Type</label>
                            <select name="stage_type" class="form-input">
                                <option value="Proscenium" <?php echo ($details->stage_type ?? '') === 'Proscenium' ? 'selected' : ''; ?>>Proscenium</option>
                                <option value="Thrust" <?php echo ($details->stage_type ?? '') === 'Thrust' ? 'selected' : ''; ?>>Thrust</option>
                                <option value="Black Box" <?php echo ($details->stage_type ?? '') === 'Black Box' ? 'selected' : ''; ?>>Black Box</option>
                                <option value="Open Stage" <?php echo ($details->stage_type ?? '') === 'Open Stage' ? 'selected' : ''; ?>>Open Stage</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Available Facilities</label>
                        <?php $afArr = !empty($details->available_facilities) ? (json_decode($details->available_facilities, true) ?? []) : []; ?>
                        <div class="checkbox-group" style="display:flex; flex-wrap:wrap; gap:10px;">
                            <label><input type="checkbox" name="available_facilities[]" value="Dressing rooms" <?php echo in_array('Dressing rooms', $afArr) ? 'checked' : ''; ?>> Dressing rooms</label>
                            <label><input type="checkbox" name="available_facilities[]" value="AC" <?php echo in_array('AC', $afArr) ? 'checked' : ''; ?>> AC</label>
                            <label><input type="checkbox" name="available_facilities[]" value="Parking" <?php echo in_array('Parking', $afArr) ? 'checked' : ''; ?>> Parking</label>
                            <label><input type="checkbox" name="available_facilities[]" value="Washrooms" <?php echo in_array('Washrooms', $afArr) ? 'checked' : ''; ?>> Washrooms</label>
                            <label><input type="checkbox" name="available_facilities[]" value="Green Room" <?php echo in_array('Green Room', $afArr) ? 'checked' : ''; ?>> Green Room</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Technical Facilities</label>
                        <?php $tfArr = !empty($details->technical_facilities) ? (json_decode($details->technical_facilities, true) ?? []) : []; ?>
                        <div class="checkbox-group" style="display:flex; flex-wrap:wrap; gap:10px;">
                            <label><input type="checkbox" name="technical_facilities[]" value="Lighting system" <?php echo in_array('Lighting system', $tfArr) ? 'checked' : ''; ?>> Lighting system</label>
                            <label><input type="checkbox" name="technical_facilities[]" value="Sound system" <?php echo in_array('Sound system', $tfArr) ? 'checked' : ''; ?>> Sound system</label>
                            <label><input type="checkbox" name="technical_facilities[]" value="Projector" <?php echo in_array('Projector', $tfArr) ? 'checked' : ''; ?>> Projector</label>
                            <label><input type="checkbox" name="technical_facilities[]" value="Backdrops" <?php echo in_array('Backdrops', $tfArr) ? 'checked' : ''; ?>> Backdrops</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Additional Equipment for Rent</label>
                        <textarea name="equipment_rent" class="form-input textarea" placeholder="Describe equipment"><?php echo htmlspecialchars($details->equipment_rent ?? ''); ?></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Stage Crew Available</label>
                            <div class="checkbox-group" style="display:flex; flex-wrap:wrap; gap:10px;">
                                <label><input type="radio" name="stage_crew_available" value="Yes" <?php echo ($details->stage_crew_available ?? '') === 'Yes' ? 'checked' : ''; ?>> Yes</label>
                                <label><input type="radio" name="stage_crew_available" value="No" <?php echo ($details->stage_crew_available ?? '') === 'No' ? 'checked' : ''; ?>> No</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Location / Address</label>
                        <textarea name="location_address" class="form-input textarea" placeholder="Full address"><?php echo htmlspecialchars($details->location_address ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Theatre Photos</label>
                        <input type="file" name="theatre_photos" class="form-input" accept="image/*">
                        <?php if (!empty($details->theatre_photos)): ?>
                            <p style="margin-top: 10px; font-size: 14px; color: #6b7280;">Current: <a href="<?php echo ROOT; ?>/public/<?php echo htmlspecialchars($details->theatre_photos); ?>" target="_blank" style="color: #3b82f6;">View Photo</a></p>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                        <?php if ($serviceName === 'set design' && $details): ?>
                        <h3 class="section-title" style="margin-top: 20px;">Set Design Details</h3>
                        <div class="form-group">
                            <label class="form-label">Types of Sets Designed</label>
                            <textarea name="types_of_sets_designed" class="form-input textarea" placeholder="Describe set types (e.g., theatrical, exhibitions)"><?php echo htmlspecialchars($details->types_of_sets_designed ?? ''); ?></textarea>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Set Construction Provided</label>
                                <div class="checkbox-group" style="display:flex; gap:10px;">
                                    <label><input type="radio" name="set_construction_provided" value="Yes" <?php echo ($details->set_construction_provided ?? '') === 'Yes' ? 'checked' : ''; ?>> Yes</label>
                                    <label><input type="radio" name="set_construction_provided" value="No" <?php echo ($details->set_construction_provided ?? '') === 'No' ? 'checked' : ''; ?>> No</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Stage Installation Support</label>
                                <div class="checkbox-group" style="display:flex; gap:10px;">
                                    <label><input type="radio" name="stage_installation_support" value="Yes" <?php echo ($details->stage_installation_support ?? '') === 'Yes' ? 'checked' : ''; ?>> Yes</label>
                                    <label><input type="radio" name="stage_installation_support" value="No" <?php echo ($details->stage_installation_support ?? '') === 'No' ? 'checked' : ''; ?>> No</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Maximum Stage Size Supported</label>
                                <input type="text" name="max_stage_size_supported" class="form-input" placeholder="e.g., 40ft x 30ft" value="<?php echo htmlspecialchars($details->max_stage_size_supported ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Materials Used</label>
                                <textarea name="materials_used" class="form-input textarea" placeholder="e.g., Wood, metal, fabric"><?php echo htmlspecialchars($details->materials_used ?? ''); ?></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Sample Set Designs (file)</label>
                            <input type="file" name="sample_set_designs" class="form-input" accept="image/*,application/pdf">
                            <?php if (!empty($details->sample_set_designs)): ?>
                                <p style="margin-top: 10px; font-size: 14px; color: #6b7280;">Current: <a href="<?php echo ROOT; ?>/public/<?php echo htmlspecialchars($details->sample_set_designs); ?>" target="_blank" style="color: #3b82f6;">View File</a></p>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <?php if ($serviceName === 'lighting design' && $details): ?>
                        <h3 class="section-title" style="margin-top: 20px;">Lighting Design Details</h3>

                        <div class="form-group">
                            <label class="form-label">Lighting Equipment Provided</label>
                            <textarea name="lighting_equipment_provided" class="form-input textarea" placeholder="Describe the equipment provided (e.g., fixtures, controllers, trussing)"><?php echo htmlspecialchars($details->lighting_equipment_provided ?? ''); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Maximum Stage Size Supported</label>
                            <input type="text" name="max_stage_size" class="form-input" placeholder="e.g., 40ft × 30ft" value="<?php echo htmlspecialchars($details->max_stage_size ?? ''); ?>">
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Lighting Design Service</label>
                                <div class="checkbox-group" style="display:flex; flex-wrap:wrap; gap:10px;">
                                    <label><input type="radio" name="lighting_design_service" value="Yes" <?php echo ($details->lighting_design_service ?? '') === 'Yes' ? 'checked' : ''; ?>> Yes</label>
                                    <label><input type="radio" name="lighting_design_service" value="No" <?php echo ($details->lighting_design_service ?? '') === 'No' ? 'checked' : ''; ?>> No</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Lighting Crew Available</label>
                                <div class="checkbox-group" style="display:flex; flex-wrap:wrap; gap:10px;">
                                    <label><input type="radio" name="lighting_crew_available" value="Yes" <?php echo ($details->lighting_crew_available ?? '') === 'Yes' ? 'checked' : ''; ?>> Yes</label>
                                    <label><input type="radio" name="lighting_crew_available" value="No" <?php echo ($details->lighting_crew_available ?? '') === 'No' ? 'checked' : ''; ?>> No</label>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($serviceName === 'sound systems' && $details): ?>
                        <h3 class="section-title" style="margin-top: 20px;">Sound Systems Details</h3>
                        <div class="form-group">
                            <label class="form-label">Sound Equipment Provided</label>
                            <textarea name="sound_equipment_provided" class="form-input textarea" placeholder="Describe the equipment provided (e.g., PA, mixers, microphones)"><?php echo htmlspecialchars($details->sound_equipment_provided ?? ''); ?></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Maximum Audience Size Supported</label>
                                <input type="number" name="max_audience_size" class="form-input" placeholder="e.g., 500" value="<?php echo htmlspecialchars($details->max_audience_size ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Equipment Brands</label>
                                <input type="text" name="equipment_brands" class="form-input" placeholder="e.g., Yamaha, Shure, JBL" value="<?php echo htmlspecialchars($details->equipment_brands ?? ''); ?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Sound Effects Handling</label>
                                <div class="checkbox-group" style="display:flex; flex-wrap:wrap; gap:10px;">
                                    <label><input type="radio" name="sound_effects_handling" value="Yes" <?php echo ($details->sound_effects_handling ?? '') === 'Yes' ? 'checked' : ''; ?>> Yes</label>
                                    <label><input type="radio" name="sound_effects_handling" value="No" <?php echo ($details->sound_effects_handling ?? '') === 'No' ? 'checked' : ''; ?>> No</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Sound Engineer Included</label>
                                <div class="checkbox-group" style="display:flex; flex-wrap:wrap; gap:10px;">
                                    <label><input type="radio" name="sound_engineer_included" value="Yes" <?php echo ($details->sound_engineer_included ?? '') === 'Yes' ? 'checked' : ''; ?>> Yes</label>
                                    <label><input type="radio" name="sound_engineer_included" value="No" <?php echo ($details->sound_engineer_included ?? '') === 'No' ? 'checked' : ''; ?>> No</label>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($serviceName === 'video production' && $details): ?>
                        <h3 class="section-title" style="margin-top: 20px;">Video Production Details</h3>
                        <div class="form-group">
                            <label class="form-label">Services Offered (Video/Photography/etc.)</label>
                            <textarea name="services_offered" class="form-input textarea" placeholder="Describe services (e.g., event videography, product photography, aerial video)"><?php echo htmlspecialchars($details->services_offered ?? ''); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Equipment Used (Cameras, lenses, lights)</label>
                            <textarea name="equipment_used" class="form-input textarea" placeholder="List cameras, lenses, lighting equipment used"><?php echo htmlspecialchars($details->equipment_used ?? ''); ?></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Number of Crew Members</label>
                                <input type="number" name="num_crew_members" class="form-input" placeholder="e.g., 3" value="<?php echo htmlspecialchars($details->num_crew_members ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Editing Software Used</label>
                                <input type="text" name="editing_software" class="form-input" placeholder="e.g., Adobe Premiere, DaVinci Resolve, Final Cut Pro" value="<?php echo htmlspecialchars($details->editing_software ?? ''); ?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Drone Service Available</label>
                                <div class="checkbox-group" style="display:flex; flex-wrap:wrap; gap:10px;">
                                    <label><input type="radio" name="drone_service_available" value="Yes" <?php echo ($details->drone_service_available ?? '') === 'Yes' ? 'checked' : ''; ?>> Yes</label>
                                    <label><input type="radio" name="drone_service_available" value="No" <?php echo ($details->drone_service_available ?? '') === 'No' ? 'checked' : ''; ?>> No</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Maximum Video Resolution</label>
                                <select name="max_video_resolution" class="form-input">
                                    <option value="">Select resolution</option>
                                    <option value="1080p" <?php echo isset($details->max_video_resolution) && $details->max_video_resolution === '1080p' ? 'selected' : ''; ?>>1080p</option>
                                    <option value="4K" <?php echo isset($details->max_video_resolution) && $details->max_video_resolution === '4K' ? 'selected' : ''; ?>>4K</option>
                                    <option value="6K" <?php echo isset($details->max_video_resolution) && $details->max_video_resolution === '6K' ? 'selected' : ''; ?>>6K</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Photo Editing Included</label>
                                <div class="checkbox-group" style="display:flex; flex-wrap:wrap; gap:10px;">
                                    <label><input type="radio" name="photo_editing_included" value="Yes" <?php echo ($details->photo_editing_included ?? '') === 'Yes' ? 'checked' : ''; ?>> Yes</label>
                                    <label><input type="radio" name="photo_editing_included" value="No" <?php echo ($details->photo_editing_included ?? '') === 'No' ? 'checked' : ''; ?>> No</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Delivery Time for Final Output</label>
                                <input type="text" name="delivery_time" class="form-input" placeholder="e.g., 5-7 business days, 2 weeks" value="<?php echo htmlspecialchars($details->delivery_time ?? ''); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Raw Footage/Photos Provided</label>
                            <div class="checkbox-group" style="display:flex; flex-wrap:wrap; gap:10px;">
                                <label><input type="radio" name="raw_footage_provided" value="Yes" <?php echo ($details->raw_footage_provided ?? '') === 'Yes' ? 'checked' : ''; ?>> Yes</label>
                                <label><input type="radio" name="raw_footage_provided" value="No" <?php echo ($details->raw_footage_provided ?? '') === 'No' ? 'checked' : ''; ?>> No</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Portfolio / Sample Links</label>
                            <input type="text" name="portfolio_links" class="form-input" placeholder="e.g., https://yourportfolio.com or multiple URLs separated by commas" value="<?php echo htmlspecialchars($details->portfolio_links ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Upload Sample Photos/Videos</label>
                            <input type="file" name="sample_videos" class="form-input" accept="image/*,video/*">
                            <?php if (!empty($details->sample_videos)): ?>
                                <p style="margin-top: 10px; font-size: 14px; color: #6b7280;">Current: <a href="<?php echo ROOT; ?>/public/<?php echo htmlspecialchars($details->sample_videos); ?>" target="_blank" style="color: #3b82f6;">View File</a></p>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <?php if ($serviceName === 'set design' && $details): ?>
                        <h3 class="section-title" style="margin-top: 20px;">Set Design Details</h3>
                        <div class="form-group">
                            <label class="form-label">Required Service Type</label>
                            <div class="checkbox-group-vertical">
                                <label><input type="checkbox" name="set_service[]" value="Set Design" <?php echo isset($details->set_design) && $details->set_design ? 'checked' : ''; ?>> Set Design</label>
                                <label><input type="checkbox" name="set_service[]" value="Set Construction" <?php echo isset($details->set_construction) && $details->set_construction ? 'checked' : ''; ?>> Set Construction</label>
                                <label><input type="checkbox" name="set_service[]" value="Set Rental" <?php echo isset($details->set_rental) && $details->set_rental ? 'checked' : ''; ?>> Set Rental</label>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Production Stage</label>
                            <select name="production_stage" class="form-input">
                                <option value="">Select a stage</option>
                                <option value="Early concept" <?php echo (isset($details->production_stage) && $details->production_stage === 'Early concept') ? 'selected' : ''; ?>>Early concept</option>
                                <option value="Ready for fabrication" <?php echo (isset($details->production_stage) && $details->production_stage === 'Ready for fabrication') ? 'selected' : ''; ?>>Ready for fabrication</option>
                            </select>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Preferred Materials</label>
                                <input type="text" name="materials" class="form-input" placeholder="e.g., Wood, Metal, Fabric" value="<?php echo htmlspecialchars($details->materials ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Stage Dimensions</label>
                                <input type="text" name="dimensions" class="form-input" placeholder="e.g., 20ft x 15ft x 12ft" value="<?php echo htmlspecialchars($details->dimensions ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Budget Range (Rs)</label>
                                <input type="text" name="budget" class="form-input" placeholder="e.g., 50,000 - 100,000" value="<?php echo htmlspecialchars($details->budget_range ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Delivery/Completion Deadline</label>
                                <input type="date" name="deadline" class="form-input" value="<?php echo htmlspecialchars($details->deadline ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Additional Notes</label>
                            <textarea name="additional_notes" class="form-input textarea" placeholder="Script notes, design references, special requirements..."><?php echo htmlspecialchars($details->notes ?? ''); ?></textarea>
                        </div>
                        <?php endif; ?>

                        <?php if ($serviceName === 'costume design' && $details): ?>
                        <h3 class="section-title" style="margin-top: 20px;">Costume Design Details</h3>
                        <div class="form-group">
                            <label class="form-label">Types of Costumes Provided</label>
                            <textarea name="types_of_costumes_provided" class="form-input textarea" placeholder="Describe the types (e.g., traditional, modern, period, dance)"><?php echo htmlspecialchars($details->types_of_costumes_provided ?? ''); ?></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Custom Costume Design Available</label>
                                <div class="checkbox-group" style="display:flex; flex-wrap:wrap; gap:10px;">
                                    <label><input type="radio" name="custom_costume_design_available" value="Yes" <?php echo ($details->custom_costume_design_available ?? '') === 'Yes' ? 'checked' : ''; ?>> Yes</label>
                                    <label><input type="radio" name="custom_costume_design_available" value="No" <?php echo ($details->custom_costume_design_available ?? '') === 'No' ? 'checked' : ''; ?>> No</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Available Sizes</label>
                                <input type="text" name="available_sizes" class="form-input" placeholder="e.g., XS–XL, kids sizes" value="<?php echo htmlspecialchars($details->available_sizes ?? ''); ?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Alterations Provided</label>
                                <div class="checkbox-group" style="display:flex; flex-wrap:wrap; gap:10px;">
                                    <label><input type="radio" name="alterations_provided" value="Yes" <?php echo ($details->alterations_provided ?? '') === 'Yes' ? 'checked' : ''; ?>> Yes</label>
                                    <label><input type="radio" name="alterations_provided" value="No" <?php echo ($details->alterations_provided ?? '') === 'No' ? 'checked' : ''; ?>> No</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Number of Costumes Available</label>
                                <input type="number" name="number_of_costumes_available" class="form-input" placeholder="e.g., 50" value="<?php echo htmlspecialchars($details->number_of_costumes_available ?? ''); ?>">
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($serviceName === 'makeup & hair' && $details): ?>
                        <h3 class="section-title" style="margin-top: 20px;">Makeup & Hair Details</h3>
                        <div class="form-group">
                            <label class="form-label">Type of Make-up Services</label>
                            <textarea name="type_of_makeup_services" class="form-input textarea" placeholder="Describe the makeup services (e.g., bridal, stage, character)"><?php echo htmlspecialchars($details->type_of_makeup_services ?? ''); ?></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Experience in Stage Make-up (years)</label>
                                <input type="number" name="experience_stage_makeup_years" class="form-input" placeholder="e.g., 5" value="<?php echo htmlspecialchars($details->experience_stage_makeup_years ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Maximum Actors Per Show</label>
                                <input type="number" name="maximum_actors_per_show" class="form-input" placeholder="e.g., 50" value="<?php echo htmlspecialchars($details->maximum_actors_per_show ?? ''); ?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Character-based Make-up Available</label>
                                <div class="checkbox-group" style="display:flex; flex-wrap:wrap; gap:10px;">
                                    <label><input type="radio" name="character_based_makeup_available" value="Yes" <?php echo ($details->character_based_makeup_available ?? '') === 'Yes' ? 'checked' : ''; ?>> Yes</label>
                                    <label><input type="radio" name="character_based_makeup_available" value="No" <?php echo ($details->character_based_makeup_available ?? '') === 'No' ? 'checked' : ''; ?>> No</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Can Handle Full Cast</label>
                                <div class="checkbox-group" style="display:flex; flex-wrap:wrap; gap:10px;">
                                    <label><input type="radio" name="can_handle_full_cast" value="Yes" <?php echo ($details->can_handle_full_cast ?? '') === 'Yes' ? 'checked' : ''; ?>> Yes</label>
                                    <label><input type="radio" name="can_handle_full_cast" value="No" <?php echo ($details->can_handle_full_cast ?? '') === 'No' ? 'checked' : ''; ?>> No</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Bring Own Make-up Kit</label>
                                <div class="checkbox-group" style="display:flex; flex-wrap:wrap; gap:10px;">
                                    <label><input type="radio" name="bring_own_makeup_kit" value="Yes" <?php echo ($details->bring_own_makeup_kit ?? '') === 'Yes' ? 'checked' : ''; ?>> Yes</label>
                                    <label><input type="radio" name="bring_own_makeup_kit" value="No" <?php echo ($details->bring_own_makeup_kit ?? '') === 'No' ? 'checked' : ''; ?>> No</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">On-site Service Available</label>
                                <div class="checkbox-group" style="display:flex; flex-wrap:wrap; gap:10px;">
                                    <label><input type="radio" name="onsite_service_available" value="Yes" <?php echo ($details->onsite_service_available ?? '') === 'Yes' ? 'checked' : ''; ?>> Yes</label>
                                    <label><input type="radio" name="onsite_service_available" value="No" <?php echo ($details->onsite_service_available ?? '') === 'No' ? 'checked' : ''; ?>> No</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Touch-up Service During Show</label>
                            <div class="checkbox-group" style="display:flex; flex-wrap:wrap; gap:10px;">
                                <label><input type="radio" name="touchup_service_during_show" value="Yes" <?php echo ($details->touchup_service_during_show ?? '') === 'Yes' ? 'checked' : ''; ?>> Yes</label>
                                <label><input type="radio" name="touchup_service_during_show" value="No" <?php echo ($details->touchup_service_during_show ?? '') === 'No' ? 'checked' : ''; ?>> No</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Traditional/Cultural Make-up Expertise</label>
                            <textarea name="traditional_cultural_makeup_expertise" class="form-input textarea" placeholder="e.g., Kathakali, Bharatanatyam, classical makeup styles"><?php echo htmlspecialchars($details->traditional_cultural_makeup_expertise ?? ''); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Sample Make-up Photos</label>
                            <input type="file" name="sample_makeup_photos" class="form-input" accept="image/*">
                            <?php if (!empty($details->sample_makeup_photos)): ?>
                                <p style="margin-top: 10px; font-size: 14px; color: #6b7280;">Current: <a href="<?php echo ROOT; ?>/public/<?php echo htmlspecialchars($details->sample_makeup_photos); ?>" target="_blank" style="color: #3b82f6;">View Photo</a></p>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    <div class="buttons-section">
                        <button type="submit" class="btn">Save Changes</button>
                        <button type="button" class="btn btn-secondary" 
                            onclick="window.location.href='<?php echo ROOT; ?>/ServiceProviderProfile/index?id=<?php echo $data['service']->provider_id; ?>'">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
</body>
</html>
