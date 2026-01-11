<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Service</title>
    <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/service provider/service_provider_register.css">
    <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/service provider/service_provider_profile.css">
</head>
<body>
    <div class="container">
        <button class="back-button" onclick="window.location.href='<?= ROOT ?>/ServiceProviderProfile/index?id=<?= htmlspecialchars($data['provider_id'] ?? '') ?>'">
            <span>←</span>
            <span>Back to Profile</span>
        </button>

        <div class="register-card">
            <div class="register-header">
                <h2>Add New Service</h2>
                <p>Select your service and provide details</p>
            </div>

            <div class="register-content">
                <?php if (!empty($_SESSION['error'])): ?>
                    <div class="alert alert-danger">
                        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <?php 
                    $servicesData = $data['services'] ?? [];
                    $existingServiceTypes = $data['existingServiceTypes'] ?? [];
                    
                    // Service list with their indices
                    $serviceList = [
                        ['name' => 'Theater Production', 'icon' => '🎭', 'idx' => 0],
                        ['name' => 'Lighting Design', 'icon' => '💡', 'idx' => 1],
                        ['name' => 'Sound Systems', 'icon' => '🔊', 'idx' => 2],
                        ['name' => 'Video Production', 'icon' => '🎬', 'idx' => 3],
                        ['name' => 'Set Design', 'icon' => '🎨', 'idx' => 4],
                        ['name' => 'Costume Design', 'icon' => '👗', 'idx' => 5],
                        ['name' => 'Makeup & Hair', 'icon' => '💄', 'idx' => 6],
                        ['name' => 'Other', 'icon' => '📋', 'idx' => 7],
                    ];
                    
                    // Helper function to check if service type already exists
                    $serviceExists = function($name) use ($existingServiceTypes) {
                        return in_array($name, $existingServiceTypes);
                    };
                ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="section">
                        <h3 class="section-title">Services & Rates</h3>

                        <?php $svc0 = $servicesData[0] ?? []; 
                              $isAdded0 = $serviceExists('Theater Production');
                        ?>
                        <div class="service-item" style="<?= $isAdded0 ? 'opacity: 0.6; pointer-events: none;' : '' ?>">
                            <div class="service-header">
                                <div class="checkbox-group">
                                    <input type="checkbox" name="services[0][selected]" class="checkbox" id="service1" <?= !empty($svc0['selected']) ? 'checked' : '' ?> <?= $isAdded0 ? 'disabled' : '' ?>>
                                    <label for="service1" class="service-name">🎭 Theater Production <?= $isAdded0 ? '<span style="color:#999; font-weight:normal; font-size:13px;">(already added)</span>' : '' ?></label>
                                    <input type="hidden" name="services[0][name]" value="Theater Production">
                                </div>
                                <div class="rate-input-group" id="service1Rate" style="<?= !empty($svc0['selected']) ? '' : 'display:none;' ?>">
                                    <div style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
                                        <div>
                                            <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #6b7280; font-weight: 500;">Rate Type:</label>
                                            <select name="services[0][rate_type]" class="form-input" style="padding: 8px 12px; font-size: 14px; min-width: 130px;">
                                                <option value="hourly" <?= isset($svc0['rate_type']) && $svc0['rate_type'] === 'daily' ? '' : 'selected' ?>>Per Hour</option>
                                                <option value="daily" <?= isset($svc0['rate_type']) && $svc0['rate_type'] === 'daily' ? 'selected' : '' ?>>Per Day</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #6b7280; font-weight: 500;">Rate (Rs):</label>
                                            <div class="input-wrapper">
                                                <span class="currency">Rs</span>
                                                <input type="number" name="services[0][rate]" class="service-rate" placeholder="0.00" value="<?= isset($svc0['rate']) ? htmlspecialchars($svc0['rate']) : '' ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" id="service1Desc" style="<?= !empty($svc0['selected']) ? '' : 'display:none;' ?>">
                                <label class="form-label">Description </label>
                                <textarea name="services[0][description]" class="form-input textarea" placeholder="Add a description about this service..." ><?= isset($svc0['description']) ? htmlspecialchars($svc0['description']) : '' ?></textarea>
                            </div>
                            <div class="service-details" id="service1Details" style="<?= !empty($svc0['selected']) ? '' : 'display:none;' ?>">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Theatre Name</label>
                                        <input type="text" name="services[0][theatre_name]" class="form-input" placeholder="e.g., City Hall Theatre" value="<?= isset($svc0['theatre_name']) ? htmlspecialchars($svc0['theatre_name']) : '' ?>">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Seating Capacity</label>
                                        <input type="number" name="services[0][seating_capacity]" class="form-input" placeholder="e.g., 500" value="<?= isset($svc0['seating_capacity']) ? htmlspecialchars($svc0['seating_capacity']) : '' ?>">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Stage Dimensions (Width × Depth)</label>
                                        <input type="text" name="services[0][stage_dimensions]" class="form-input" placeholder="e.g., 30ft × 20ft" value="<?= isset($svc0['stage_dimensions']) ? htmlspecialchars($svc0['stage_dimensions']) : '' ?>">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Stage Type</label>
                                        <input type="text" name="services[0][stage_type]" class="form-input" placeholder="e.g., Proscenium, Black box" value="<?= isset($svc0['stage_type']) ? htmlspecialchars($svc0['stage_type']) : '' ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Available Facilities</label>
                                    <div class="checkbox-group" style="display:flex; flex-wrap:wrap; gap:10px;">
                                        <?php $af = (array)($svc0['available_facilities'] ?? []); ?>
                                        <label><input type="checkbox" name="services[0][available_facilities][]" value="Dressing rooms" <?= in_array('Dressing rooms', $af) ? 'checked' : '' ?>> Dressing rooms</label>
                                        <label><input type="checkbox" name="services[0][available_facilities][]" value="AC" <?= in_array('AC', $af) ? 'checked' : '' ?>> AC</label>
                                        <label><input type="checkbox" name="services[0][available_facilities][]" value="Parking" <?= in_array('Parking', $af) ? 'checked' : '' ?>> Parking</label>
                                        <label><input type="checkbox" name="services[0][available_facilities][]" value="Washrooms" <?= in_array('Washrooms', $af) ? 'checked' : '' ?>> Washrooms</label>
                                        <label><input type="checkbox" name="services[0][available_facilities][]" value="Green Room" <?= in_array('Green Room', $af) ? 'checked' : '' ?>> Green Room</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Technical Facilities</label>
                                    <div class="checkbox-group" style="display:flex; flex-wrap:wrap; gap:10px;">
                                        <?php $tf = (array)($svc0['technical_facilities'] ?? []); ?>
                                        <label><input type="checkbox" name="services[0][technical_facilities][]" value="Lighting system" <?= in_array('Lighting system', $tf) ? 'checked' : '' ?>> Lighting system</label>
                                        <label><input type="checkbox" name="services[0][technical_facilities][]" value="Sound system" <?= in_array('Sound system', $tf) ? 'checked' : '' ?>> Sound system</label>
                                        <label><input type="checkbox" name="services[0][technical_facilities][]" value="Projector" <?= in_array('Projector', $tf) ? 'checked' : '' ?>> Projector</label>
                                        <label><input type="checkbox" name="services[0][technical_facilities][]" value="Backdrops" <?= in_array('Backdrops', $tf) ? 'checked' : '' ?>> Backdrops</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Additional Equipment for Rent</label>
                                    <textarea name="services[0][equipment_rent]" class="form-input textarea" placeholder="Describe equipment"><?= isset($svc0['equipment_rent']) ? htmlspecialchars($svc0['equipment_rent']) : '' ?></textarea>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Stage Crew Available</label>
                                        <?php $crew = $svc0['stage_crew_available'] ?? ''; ?>
                                        <div class="checkbox-group" style="display:flex; flex-wrap:wrap; gap:10px;">
                                            <label><input type="radio" name="services[0][stage_crew_available]" value="Yes" <?= $crew === 'Yes' ? 'checked' : '' ?>> Yes</label>
                                            <label><input type="radio" name="services[0][stage_crew_available]" value="No" <?= $crew === 'No' ? 'checked' : '' ?>> No</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Location / Address</label>
                                    <textarea name="services[0][location_address]" class="form-input textarea" placeholder="Full address"><?= isset($svc0['location_address']) ? htmlspecialchars($svc0['location_address']) : '' ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Upload Theatre Photos</label>
                                    <input type="file" name="services[0][theatre_photos][]" class="form-input" multiple accept="image/*">
                                </div>
                            </div>
                        </div>

                        <?php $svc1 = $servicesData[1] ?? []; 
                              $isAdded1 = $serviceExists('Lighting Design');
                        ?>
                        <div class="service-item" style="<?= $isAdded1 ? 'opacity: 0.6; pointer-events: none;' : '' ?>">
                            <div class="service-header">
                                <div class="checkbox-group">
                                    <input type="checkbox" name="services[1][selected]" class="checkbox" id="service2" <?= !empty($svc1['selected']) ? 'checked' : '' ?> <?= $isAdded1 ? 'disabled' : '' ?>>
                                    <label for="service2" class="service-name">💡 Lighting Design <?= $isAdded1 ? '<span style="color:#999; font-weight:normal; font-size:13px;">(already added)</span>' : '' ?></label>
                                    <input type="hidden" name="services[1][name]" value="Lighting Design">
                                </div>
                                <div class="rate-input-group" id="service2Rate" style="<?= !empty($svc1['selected']) ? '' : 'display:none;' ?>">
                                    <div style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
                                        <div>
                                            <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #6b7280; font-weight: 500;">Rate Type:</label>
                                            <select name="services[1][rate_type]" class="form-input" style="padding: 8px 12px; font-size: 14px; min-width: 130px;">
                                                <option value="hourly" <?= isset($svc1['rate_type']) && $svc1['rate_type'] === 'daily' ? '' : 'selected' ?>>Per Hour</option>
                                                <option value="daily" <?= isset($svc1['rate_type']) && $svc1['rate_type'] === 'daily' ? 'selected' : '' ?>>Per Day</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #6b7280; font-weight: 500;">Rate (Rs):</label>
                                            <div class="input-wrapper">
                                                <span class="currency">Rs</span>
                                                <input type="number" name="services[1][rate]" class="service-rate" placeholder="0.00" value="<?= isset($svc1['rate']) ? htmlspecialchars($svc1['rate']) : '' ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" id="service2Desc" style="<?= !empty($svc1['selected']) ? '' : 'display:none;' ?>">
                                <label class="form-label">Description </label>
                                <textarea name="services[1][description]" class="form-input textarea" placeholder="Add a description about this service..."><?= isset($svc1['description']) ? htmlspecialchars($svc1['description']) : '' ?></textarea>
                            </div>
                            <div class="service-details" id="service2Details" style="<?= !empty($svc1['selected']) ? '' : 'display:none;' ?>">
                                <div class="form-group">
                                    <label class="form-label">Lighting Equipment Provided</label>
                                    <textarea name="services[1][lighting_equipment_provided]" class="form-input textarea" placeholder="Describe the equipment provided (e.g., fixtures, controllers, trussing)"><?= isset($svc1['lighting_equipment_provided']) ? htmlspecialchars($svc1['lighting_equipment_provided']) : '' ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Maximum Stage Size Supported</label>
                                    <input type="text" name="services[1][max_stage_size]" class="form-input" placeholder="e.g., 40ft × 30ft" value="<?= isset($svc1['max_stage_size']) ? htmlspecialchars($svc1['max_stage_size']) : '' ?>">
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Lighting Design Service</label>
                                        <?php $lds = $svc1['lighting_design_service'] ?? ''; ?>
                                        <div class="checkbox-group" style="display:flex; flex-wrap:wrap; gap:10px;">
                                            <label><input type="radio" name="services[1][lighting_design_service]" value="Yes" <?= $lds === 'Yes' ? 'checked' : '' ?>> Yes</label>
                                            <label><input type="radio" name="services[1][lighting_design_service]" value="No" <?= $lds === 'No' ? 'checked' : '' ?>> No</label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Lighting Crew Available</label>
                                        <?php $lca = $svc1['lighting_crew_available'] ?? ''; ?>
                                        <div class="checkbox-group" style="display:flex; flex-wrap:wrap; gap:10px;">
                                            <label><input type="radio" name="services[1][lighting_crew_available]" value="Yes" <?= $lca === 'Yes' ? 'checked' : '' ?>> Yes</label>
                                            <label><input type="radio" name="services[1][lighting_crew_available]" value="No" <?= $lca === 'No' ? 'checked' : '' ?>> No</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php $svc2 = $servicesData[2] ?? []; 
                              $isAdded2 = $serviceExists('Sound Systems');
                        ?>
                        <div class="service-item" style="<?= $isAdded2 ? 'opacity: 0.6; pointer-events: none;' : '' ?>">
                            <div class="service-header">
                                <div class="checkbox-group">
                                    <input type="checkbox" name="services[2][selected]" class="checkbox" id="service3" <?= !empty($svc2['selected']) ? 'checked' : '' ?> <?= $isAdded2 ? 'disabled' : '' ?>>
                                    <label for="service3" class="service-name">🔊 Sound Systems <?= $isAdded2 ? '<span style="color:#999; font-weight:normal; font-size:13px;">(already added)</span>' : '' ?></label>
                                    <input type="hidden" name="services[2][name]" value="Sound Systems">
                                </div>
                                <div class="rate-input-group" id="service3Rate" style="<?= !empty($svc2['selected']) ? '' : 'display:none;' ?>">
                                    <div style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
                                        <div>
                                            <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #6b7280; font-weight: 500;">Rate Type:</label>
                                            <select name="services[2][rate_type]" class="form-input" style="padding: 8px 12px; font-size: 14px; min-width: 130px;">
                                                <option value="hourly" <?= isset($svc2['rate_type']) && $svc2['rate_type'] === 'daily' ? '' : 'selected' ?>>Per Hour</option>
                                                <option value="daily" <?= isset($svc2['rate_type']) && $svc2['rate_type'] === 'daily' ? 'selected' : '' ?>>Per Day</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #6b7280; font-weight: 500;">Rate (Rs):</label>
                                            <div class="input-wrapper">
                                                <span class="currency">Rs</span>
                                                <input type="number" name="services[2][rate]" class="service-rate" placeholder="0.00" value="<?= isset($svc2['rate']) ? htmlspecialchars($svc2['rate']) : '' ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" id="service3Desc" style="<?= !empty($svc2['selected']) ? '' : 'display:none;' ?>">
                                <label class="form-label">Description</label>
                                <textarea name="services[2][description]" class="form-input textarea" placeholder="Add a description about this service..." ><?= isset($svc2['description']) ? htmlspecialchars($svc2['description']) : '' ?></textarea>
                            </div>
                            <div class="service-details" id="service3Details" style="<?= !empty($svc2['selected']) ? '' : 'display:none;' ?>">
                                <div class="form-group">
                                    <label class="form-label">Sound Equipment Provided</label>
                                    <textarea name="services[2][sound_equipment_provided]" class="form-input textarea" placeholder="Describe the equipment provided (e.g., PA, mixers, microphones)"><?= isset($svc2['sound_equipment_provided']) ? htmlspecialchars($svc2['sound_equipment_provided']) : '' ?></textarea>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Maximum Audience Size Supported</label>
                                        <input type="number" name="services[2][max_audience_size]" class="form-input" placeholder="e.g., 500" value="<?= isset($svc2['max_audience_size']) ? htmlspecialchars($svc2['max_audience_size']) : '' ?>">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Equipment Brands</label>
                                        <input type="text" name="services[2][equipment_brands]" class="form-input" placeholder="e.g., Yamaha, Shure, JBL" value="<?= isset($svc2['equipment_brands']) ? htmlspecialchars($svc2['equipment_brands']) : '' ?>">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Sound Effects Handling</label>
                                        <?php $seh = $svc2['sound_effects_handling'] ?? ''; ?>
                                        <div class="checkbox-group" style="display:flex; flex-wrap:wrap; gap:10px;">
                                            <label><input type="radio" name="services[2][sound_effects_handling]" value="Yes" <?= $seh === 'Yes' ? 'checked' : '' ?>> Yes</label>
                                            <label><input type="radio" name="services[2][sound_effects_handling]" value="No" <?= $seh === 'No' ? 'checked' : '' ?>> No</label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Sound Engineer Included</label>
                                        <?php $sei = $svc2['sound_engineer_included'] ?? ''; ?>
                                        <div class="checkbox-group" style="display:flex; flex-wrap:wrap; gap:10px;">
                                            <label><input type="radio" name="services[2][sound_engineer_included]" value="Yes" <?= $sei === 'Yes' ? 'checked' : '' ?>> Yes</label>
                                            <label><input type="radio" name="services[2][sound_engineer_included]" value="No" <?= $sei === 'No' ? 'checked' : '' ?>> No</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php $svc3 = $servicesData[3] ?? []; 
                              $isAdded3 = $serviceExists('Video Production');
                        ?>
                        <div class="service-item" style="<?= $isAdded3 ? 'opacity: 0.6; pointer-events: none;' : '' ?>">
                            <div class="service-header">
                                <div class="checkbox-group">
                                    <input type="checkbox" name="services[3][selected]" class="checkbox" id="service4" <?= !empty($svc3['selected']) ? 'checked' : '' ?> <?= $isAdded3 ? 'disabled' : '' ?>>
                                    <label for="service4" class="service-name">🎬 Video Production <?= $isAdded3 ? '<span style="color:#999; font-weight:normal; font-size:13px;">(already added)</span>' : '' ?></label>
                                    <input type="hidden" name="services[3][name]" value="Video Production">
                                </div>
                                <div class="rate-input-group" id="service4Rate" style="<?= !empty($svc3['selected']) ? '' : 'display:none;' ?>">
                                    <div style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
                                        <div>
                                            <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #6b7280; font-weight: 500;">Rate Type:</label>
                                            <select name="services[3][rate_type]" class="form-input" style="padding: 8px 12px; font-size: 14px; min-width: 130px;">
                                                <option value="hourly" <?= isset($svc3['rate_type']) && $svc3['rate_type'] === 'daily' ? '' : 'selected' ?>>Per Hour</option>
                                                <option value="daily" <?= isset($svc3['rate_type']) && $svc3['rate_type'] === 'daily' ? 'selected' : '' ?>>Per Day</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #6b7280; font-weight: 500;">Rate (Rs):</label>
                                            <div class="input-wrapper">
                                                <span class="currency">Rs</span>
                                                <input type="number" name="services[3][rate]" class="service-rate" placeholder="0.00" value="<?= isset($svc3['rate']) ? htmlspecialchars($svc3['rate']) : '' ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" id="service4Desc" style="<?= !empty($svc3['selected']) ? '' : 'display:none;' ?>">
                                <label class="form-label">Description </label>
                                <textarea name="services[3][description]" class="form-input textarea" placeholder="Add a description about this service..."><?= isset($svc3['description']) ? htmlspecialchars($svc3['description']) : '' ?></textarea>
                            </div>
                            <div class="service-details" id="service4Details" style="<?= !empty($svc3['selected']) ? '' : 'display:none;' ?>">
                                <div class="form-group">
                                    <label class="form-label">Services Offered (Video/Photography/etc.)</label>
                                    <textarea name="services[3][services_offered]" class="form-input textarea" placeholder="Describe services (e.g., event videography, product photography, aerial video)"><?= isset($svc3['services_offered']) ? htmlspecialchars($svc3['services_offered']) : '' ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Equipment Used (Cameras, lenses, lights)</label>
                                    <textarea name="services[3][equipment_used]" class="form-input textarea" placeholder="List cameras, lenses, lighting equipment used"><?= isset($svc3['equipment_used']) ? htmlspecialchars($svc3['equipment_used']) : '' ?></textarea>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Number of Crew Members</label>
                                        <input type="number" name="services[3][num_crew_members]" class="form-input" placeholder="e.g., 3" value="<?= isset($svc3['num_crew_members']) ? htmlspecialchars($svc3['num_crew_members']) : '' ?>">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Editing Software Used</label>
                                        <input type="text" name="services[3][editing_software]" class="form-input" placeholder="e.g., Adobe Premiere, DaVinci Resolve, Final Cut Pro" value="<?= isset($svc3['editing_software']) ? htmlspecialchars($svc3['editing_software']) : '' ?>">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Drone Service Available</label>
                                        <?php $dsa = $svc3['drone_service_available'] ?? ''; ?>
                                        <div class="checkbox-group" style="display:flex; flex-wrap:wrap; gap:10px;">
                                            <label><input type="radio" name="services[3][drone_service_available]" value="Yes" <?= $dsa === 'Yes' ? 'checked' : '' ?>> Yes</label>
                                            <label><input type="radio" name="services[3][drone_service_available]" value="No" <?= $dsa === 'No' ? 'checked' : '' ?>> No</label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Maximum Video Resolution</label>
                                        <select name="services[3][max_video_resolution]" class="form-input">
                                            <option value="">Select resolution</option>
                                            <option value="1080p" <?= isset($svc3['max_video_resolution']) && $svc3['max_video_resolution'] === '1080p' ? 'selected' : '' ?>>1080p</option>
                                            <option value="4K" <?= isset($svc3['max_video_resolution']) && $svc3['max_video_resolution'] === '4K' ? 'selected' : '' ?>>4K</option>
                                            <option value="6K" <?= isset($svc3['max_video_resolution']) && $svc3['max_video_resolution'] === '6K' ? 'selected' : '' ?>>6K</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Photo Editing Included</label>
                                        <?php $pei = $svc3['photo_editing_included'] ?? ''; ?>
                                        <div class="checkbox-group" style="display:flex; flex-wrap:wrap; gap:10px;">
                                            <label><input type="radio" name="services[3][photo_editing_included]" value="Yes" <?= $pei === 'Yes' ? 'checked' : '' ?>> Yes</label>
                                            <label><input type="radio" name="services[3][photo_editing_included]" value="No" <?= $pei === 'No' ? 'checked' : '' ?>> No</label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Delivery Time for Final Output</label>
                                        <input type="text" name="services[3][delivery_time]" class="form-input" placeholder="e.g., 5-7 business days, 2 weeks" value="<?= isset($svc3['delivery_time']) ? htmlspecialchars($svc3['delivery_time']) : '' ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Raw Footage/Photos Provided</label>
                                    <?php $rfp = $svc3['raw_footage_provided'] ?? ''; ?>
                                    <div class="checkbox-group" style="display:flex; flex-wrap:wrap; gap:10px;">
                                        <label><input type="radio" name="services[3][raw_footage_provided]" value="Yes" <?= $rfp === 'Yes' ? 'checked' : '' ?>> Yes</label>
                                        <label><input type="radio" name="services[3][raw_footage_provided]" value="No" <?= $rfp === 'No' ? 'checked' : '' ?>> No</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Portfolio / Sample Links</label>
                                    <input type="text" name="services[3][portfolio_links]" class="form-input" placeholder="e.g., https://yourportfolio.com or multiple URLs separated by commas" value="<?= isset($svc3['portfolio_links']) ? htmlspecialchars($svc3['portfolio_links']) : '' ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Upload Sample Photos/Videos</label>
                                    <input type="file" name="services[3][sample_videos]" class="form-input" accept="image/*,video/*">
                                </div>
                            </div>
                        </div>

                        <?php $svc4 = $servicesData[4] ?? []; 
                              $isAdded4 = $serviceExists('Set Design');
                        ?>
                        <div class="service-item" style="<?= $isAdded4 ? 'opacity: 0.6; pointer-events: none;' : '' ?>">
                            <div class="service-header">
                                <div class="checkbox-group">
                                    <input type="checkbox" name="services[4][selected]" class="checkbox" id="service5" <?= !empty($svc4['selected']) ? 'checked' : '' ?> <?= $isAdded4 ? 'disabled' : '' ?>>
                                    <label for="service5" class="service-name">🎨 Set Design <?= $isAdded4 ? '<span style="color:#999; font-weight:normal; font-size:13px;">(already added)</span>' : '' ?></label>
                                    <input type="hidden" name="services[4][name]" value="Set Design">
                                </div>
                                <div class="rate-input-group" id="service5Rate" style="<?= !empty($svc4['selected']) ? '' : 'display:none;' ?>">
                                    <div style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
                                        <div>
                                            <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #6b7280; font-weight: 500;">Rate Type:</label>
                                            <select name="services[4][rate_type]" class="form-input" style="padding: 8px 12px; font-size: 14px; min-width: 130px;">
                                                <option value="hourly" <?= isset($svc4['rate_type']) && $svc4['rate_type'] === 'daily' ? '' : 'selected' ?>>Per Hour</option>
                                                <option value="daily" <?= isset($svc4['rate_type']) && $svc4['rate_type'] === 'daily' ? 'selected' : '' ?>>Per Day</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #6b7280; font-weight: 500;">Rate (Rs):</label>
                                            <div class="input-wrapper">
                                                <span class="currency">Rs</span>
                                                <input type="number" name="services[4][rate]" class="service-rate" placeholder="0.00" value="<?= isset($svc4['rate']) ? htmlspecialchars($svc4['rate']) : '' ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" id="service5Desc" style="<?= !empty($svc4['selected']) ? '' : 'display:none;' ?>">
                                <label class="form-label">Description </label>
                                <textarea name="services[4][description]" class="form-input textarea" placeholder="Add a description about this service..."><?= isset($svc4['description']) ? htmlspecialchars($svc4['description']) : '' ?></textarea>
                            </div>
                            <div class="service-details" id="service5Details" style="<?= !empty($svc4['selected']) ? '' : 'display:none;' ?>">
                                <div class="form-group">
                                    <label class="form-label">Types of Sets Designed</label>
                                    <textarea name="services[4][types_of_sets_designed]" class="form-input textarea" placeholder="Describe set types (e.g., theatrical, exhibitions)"><?= isset($svc4['types_of_sets_designed']) ? htmlspecialchars($svc4['types_of_sets_designed']) : '' ?></textarea>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Set Construction Provided</label>
                                        <?php $scp = $svc4['set_construction_provided'] ?? ''; ?>
                                        <div class="checkbox-group" style="display:flex; flex-wrap:wrap; gap:10px;">
                                            <label><input type="radio" name="services[4][set_construction_provided]" value="Yes" <?= $scp === 'Yes' ? 'checked' : '' ?>> Yes</label>
                                            <label><input type="radio" name="services[4][set_construction_provided]" value="No" <?= $scp === 'No' ? 'checked' : '' ?>> No</label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Stage Installation Support</label>
                                        <?php $sis = $svc4['stage_installation_support'] ?? ''; ?>
                                        <div class="checkbox-group" style="display:flex; flex-wrap:wrap; gap:10px;">
                                            <label><input type="radio" name="services[4][stage_installation_support]" value="Yes" <?= $sis === 'Yes' ? 'checked' : '' ?>> Yes</label>
                                            <label><input type="radio" name="services[4][stage_installation_support]" value="No" <?= $sis === 'No' ? 'checked' : '' ?>> No</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Maximum Stage Size Supported</label>
                                        <input type="text" name="services[4][max_stage_size_supported]" class="form-input" placeholder="e.g., 40ft x 30ft" value="<?= isset($svc4['max_stage_size_supported']) ? htmlspecialchars($svc4['max_stage_size_supported']) : '' ?>">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Materials Used</label>
                                        <textarea name="services[4][materials_used]" class="form-input textarea" placeholder="e.g., Wood, metal, fabric"><?= isset($svc4['materials_used']) ? htmlspecialchars($svc4['materials_used']) : '' ?></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Sample Set Designs</label>
                                    <input type="file" name="services[4][sample_set_designs]" class="form-input" accept="image/*,application/pdf">
                                </div>
                            </div>
                        </div>

                        <?php $svc5 = $servicesData[5] ?? []; 
                              $isAdded5 = $serviceExists('Costume Design');
                        ?>
                        <div class="service-item" style="<?= $isAdded5 ? 'opacity: 0.6; pointer-events: none;' : '' ?>">
                            <div class="service-header">
                                <div class="checkbox-group">
                                    <input type="checkbox" name="services[5][selected]" class="checkbox" id="service6" <?= !empty($svc5['selected']) ? 'checked' : '' ?> <?= $isAdded5 ? 'disabled' : '' ?>>
                                    <label for="service6" class="service-name">👗 Costume Design <?= $isAdded5 ? '<span style="color:#999; font-weight:normal; font-size:13px;">(already added)</span>' : '' ?></label>
                                    <input type="hidden" name="services[5][name]" value="Costume Design">
                                </div>
                                <div class="rate-input-group" id="service6Rate" style="<?= !empty($svc5['selected']) ? '' : 'display:none;' ?>">
                                    <div style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
                                        <div>
                                            <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #6b7280; font-weight: 500;">Rate Type:</label>
                                            <select name="services[5][rate_type]" class="form-input" style="padding: 8px 12px; font-size: 14px; min-width: 130px;">
                                                <option value="hourly" <?= isset($svc5['rate_type']) && $svc5['rate_type'] === 'daily' ? '' : 'selected' ?>>Per Hour</option>
                                                <option value="daily" <?= isset($svc5['rate_type']) && $svc5['rate_type'] === 'daily' ? 'selected' : '' ?>>Per Day</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #6b7280; font-weight: 500;">Rate (Rs):</label>
                                            <div class="input-wrapper">
                                                <span class="currency">Rs</span>
                                                <input type="number" name="services[5][rate]" class="service-rate" placeholder="0.00" value="<?= isset($svc5['rate']) ? htmlspecialchars($svc5['rate']) : '' ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" id="service6Desc" style="<?= !empty($svc5['selected']) ? '' : 'display:none;' ?>">
                                <label class="form-label">Description </label>
                                <textarea name="services[5][description]" class="form-input textarea" placeholder="Add a description about this service..."><?= isset($svc5['description']) ? htmlspecialchars($svc5['description']) : '' ?></textarea>
                            </div>
                            <div class="service-details" id="service6Details" style="<?= !empty($svc5['selected']) ? '' : 'display:none;' ?>">
                                <div class="form-group">
                                    <label class="form-label">Types of Costumes Provided</label>
                                    <textarea name="services[5][types_of_costumes_provided]" class="form-input textarea" placeholder="Describe the types (e.g., traditional, modern, period, dance)"><?= isset($svc5['types_of_costumes_provided']) ? htmlspecialchars($svc5['types_of_costumes_provided']) : '' ?></textarea>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Custom Costume Design Available</label>
                                        <?php $ccd = $svc5['custom_costume_design_available'] ?? ''; ?>
                                        <div class="checkbox-group" style="display:flex; flex-wrap:wrap; gap:10px;">
                                            <label><input type="radio" name="services[5][custom_costume_design_available]" value="Yes" <?= $ccd === 'Yes' ? 'checked' : '' ?>> Yes</label>
                                            <label><input type="radio" name="services[5][custom_costume_design_available]" value="No" <?= $ccd === 'No' ? 'checked' : '' ?>> No</label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Available Sizes</label>
                                        <input type="text" name="services[5][available_sizes]" class="form-input" placeholder="e.g., XS–XL, kids sizes" value="<?= isset($svc5['available_sizes']) ? htmlspecialchars($svc5['available_sizes']) : '' ?>">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Alterations Provided</label>
                                        <?php $ap = $svc5['alterations_provided'] ?? ''; ?>
                                        <div class="checkbox-group" style="display:flex; flex-wrap:wrap; gap:10px;">
                                            <label><input type="radio" name="services[5][alterations_provided]" value="Yes" <?= $ap === 'Yes' ? 'checked' : '' ?>> Yes</label>
                                            <label><input type="radio" name="services[5][alterations_provided]" value="No" <?= $ap === 'No' ? 'checked' : '' ?>> No</label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Number of Costumes Available</label>
                                        <input type="number" name="services[5][number_of_costumes_available]" class="form-input" placeholder="e.g., 50" value="<?= isset($svc5['number_of_costumes_available']) ? htmlspecialchars($svc5['number_of_costumes_available']) : '' ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php $svc6 = $servicesData[6] ?? []; 
                              $isAdded6 = $serviceExists('Makeup & Hair');
                        ?>
                        <div class="service-item" style="<?= $isAdded6 ? 'opacity: 0.6; pointer-events: none;' : '' ?>">
                            <div class="service-header">
                                <div class="checkbox-group">
                                    <input type="checkbox" name="services[6][selected]" class="checkbox" id="service7" <?= !empty($svc6['selected']) ? 'checked' : '' ?> <?= $isAdded6 ? 'disabled' : '' ?>>
                                    <label for="service7" class="service-name">💄 Makeup & Hair <?= $isAdded6 ? '<span style="color:#999; font-weight:normal; font-size:13px;">(already added)</span>' : '' ?></label>
                                    <input type="hidden" name="services[6][name]" value="Makeup & Hair">
                                </div>
                                <div class="rate-input-group" id="service7Rate" style="<?= !empty($svc6['selected']) ? '' : 'display:none;' ?>">
                                    <div style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
                                        <div>
                                            <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #6b7280; font-weight: 500;">Rate Type:</label>
                                            <select name="services[6][rate_type]" class="form-input" style="padding: 8px 12px; font-size: 14px; min-width: 130px;">
                                                <option value="hourly" <?= isset($svc6['rate_type']) && $svc6['rate_type'] === 'daily' ? '' : 'selected' ?>>Per Hour</option>
                                                <option value="daily" <?= isset($svc6['rate_type']) && $svc6['rate_type'] === 'daily' ? 'selected' : '' ?>>Per Day</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #6b7280; font-weight: 500;">Rate (Rs):</label>
                                            <div class="input-wrapper">
                                                <span class="currency">Rs</span>
                                                <input type="number" name="services[6][rate]" class="service-rate" placeholder="0.00" value="<?= isset($svc6['rate']) ? htmlspecialchars($svc6['rate']) : '' ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" id="service7Desc" style="<?= !empty($svc6['selected']) ? '' : 'display:none;' ?>">
                                <label class="form-label">Description </label>
                                <textarea name="services[6][description]" class="form-input textarea" placeholder="Add a description about this service..."><?= isset($svc6['description']) ? htmlspecialchars($svc6['description']) : '' ?></textarea>
                            </div>
                            <div class="service-details" id="service7Details" style="<?= !empty($svc6['selected']) ? '' : 'display:none;' ?>">
                                <div class="form-group">
                                    <label class="form-label">Type of Make-up Services</label>
                                    <textarea name="services[6][type_of_makeup_services]" class="form-input textarea" placeholder="Describe the makeup services (e.g., bridal, stage, character)"><?= isset($svc6['type_of_makeup_services']) ? htmlspecialchars($svc6['type_of_makeup_services']) : '' ?></textarea>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Experience in Stage Make-up (years)</label>
                                        <input type="number" name="services[6][experience_stage_makeup_years]" class="form-input" placeholder="e.g., 5" value="<?= isset($svc6['experience_stage_makeup_years']) ? htmlspecialchars($svc6['experience_stage_makeup_years']) : '' ?>">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Maximum Actors Per Show</label>
                                        <input type="number" name="services[6][maximum_actors_per_show]" class="form-input" placeholder="e.g., 50" value="<?= isset($svc6['maximum_actors_per_show']) ? htmlspecialchars($svc6['maximum_actors_per_show']) : '' ?>">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Character-based Make-up Available</label>
                                        <?php $cbm = $svc6['character_based_makeup_available'] ?? ''; ?>
                                        <div class="checkbox-group" style="display:flex; flex-wrap:wrap; gap:10px;">
                                            <label><input type="radio" name="services[6][character_based_makeup_available]" value="Yes" <?= $cbm === 'Yes' ? 'checked' : '' ?>> Yes</label>
                                            <label><input type="radio" name="services[6][character_based_makeup_available]" value="No" <?= $cbm === 'No' ? 'checked' : '' ?>> No</label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Can Handle Full Cast</label>
                                        <?php $chf = $svc6['can_handle_full_cast'] ?? ''; ?>
                                        <div class="checkbox-group" style="display:flex; flex-wrap:wrap; gap:10px;">
                                            <label><input type="radio" name="services[6][can_handle_full_cast]" value="Yes" <?= $chf === 'Yes' ? 'checked' : '' ?>> Yes</label>
                                            <label><input type="radio" name="services[6][can_handle_full_cast]" value="No" <?= $chf === 'No' ? 'checked' : '' ?>> No</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Bring Own Make-up Kit</label>
                                        <?php $bomu = $svc6['bring_own_makeup_kit'] ?? ''; ?>
                                        <div class="checkbox-group" style="display:flex; flex-wrap:wrap; gap:10px;">
                                            <label><input type="radio" name="services[6][bring_own_makeup_kit]" value="Yes" <?= $bomu === 'Yes' ? 'checked' : '' ?>> Yes</label>
                                            <label><input type="radio" name="services[6][bring_own_makeup_kit]" value="No" <?= $bomu === 'No' ? 'checked' : '' ?>> No</label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">On-site Service Available</label>
                                        <?php $osa = $svc6['onsite_service_available'] ?? ''; ?>
                                        <div class="checkbox-group" style="display:flex; flex-wrap:wrap; gap:10px;">
                                            <label><input type="radio" name="services[6][onsite_service_available]" value="Yes" <?= $osa === 'Yes' ? 'checked' : '' ?>> Yes</label>
                                            <label><input type="radio" name="services[6][onsite_service_available]" value="No" <?= $osa === 'No' ? 'checked' : '' ?>> No</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Touch-up Service During Show</label>
                                    <?php $tds = $svc6['touchup_service_during_show'] ?? ''; ?>
                                    <div class="checkbox-group" style="display:flex; flex-wrap:wrap; gap:10px;">
                                        <label><input type="radio" name="services[6][touchup_service_during_show]" value="Yes" <?= $tds === 'Yes' ? 'checked' : '' ?>> Yes</label>
                                        <label><input type="radio" name="services[6][touchup_service_during_show]" value="No" <?= $tds === 'No' ? 'checked' : '' ?>> No</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Traditional/Cultural Make-up Expertise</label>
                                    <textarea name="services[6][traditional_cultural_makeup_expertise]" class="form-input textarea" placeholder="e.g., Kathakali, Bharatanatyam, classical makeup styles"><?= isset($svc6['traditional_cultural_makeup_expertise']) ? htmlspecialchars($svc6['traditional_cultural_makeup_expertise']) : '' ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Sample Make-up Photos</label>
                                    <input type="file" name="services[6][sample_makeup_photos]" class="form-input" accept="image/*">
                                </div>
                            </div>
                        </div>

                        <?php $svc7 = $servicesData[7] ?? []; 
                              $isAdded7 = $serviceExists('Other');
                        ?>
                        <div class="service-item" style="<?= $isAdded7 ? 'opacity: 0.6; pointer-events: none;' : '' ?>">
                            <div class="service-header">
                                <div class="checkbox-group">
                                    <input type="checkbox" name="services[7][selected]" class="checkbox" id="service8" <?= !empty($svc7['selected']) ? 'checked' : '' ?> <?= $isAdded7 ? 'disabled' : '' ?>>
                                    <label for="service8" class="service-name">📋 Other <?= $isAdded7 ? '<span style="color:#999; font-weight:normal; font-size:13px;">(already added)</span>' : '' ?></label>
                                    <input type="hidden" name="services[7][name]" value="Other">
                                </div>
                                <div class="rate-input-group" id="service8Rate" style="<?= !empty($svc7['selected']) ? '' : 'display:none;' ?>">
                                    <div style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
                                        <div>
                                            <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #6b7280; font-weight: 500;">Rate Type:</label>
                                            <select name="services[7][rate_type]" class="form-input" style="padding: 8px 12px; font-size: 14px; min-width: 130px;">
                                                <option value="hourly" <?= isset($svc7['rate_type']) && $svc7['rate_type'] === 'daily' ? '' : 'selected' ?>>Per Hour</option>
                                                <option value="daily" <?= isset($svc7['rate_type']) && $svc7['rate_type'] === 'daily' ? 'selected' : '' ?>>Per Day</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #6b7280; font-weight: 500;">Rate (Rs):</label>
                                            <div class="input-wrapper">
                                                <span class="currency">Rs</span>
                                                <input type="number" name="services[7][rate]" class="service-rate" placeholder="0.00" value="<?= isset($svc7['rate']) ? htmlspecialchars($svc7['rate']) : '' ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" id="service8Desc" style="<?= !empty($svc7['selected']) ? '' : 'display:none;' ?>">
                                <label class="form-label">Description </label>
                                <textarea name="services[7][description]" class="form-input textarea" placeholder="Add a description about this service..."><?= isset($svc7['description']) ? htmlspecialchars($svc7['description']) : '' ?></textarea>
                            </div>
                            
                            <!-- Other Service Specific Fields -->
                            <div class="service-details" id="service8Details" style="<?= !empty($svc7['selected']) ? '' : 'display:none;' ?>">
                                <div class="form-group">
                                    <label class="form-label">Service Type <span class="required">*</span></label>
                                    <input type="text" name="services[7][service_type]" class="form-input" placeholder="e.g., Photography, Catering, Transportation, etc." value="<?= isset($svc7['service_type']) ? htmlspecialchars($svc7['service_type']) : '' ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="buttons-section">
                        <button type="submit" class="btn">Add Selected Services</button>
                        <button type="button" class="btn btn-secondary" onclick="window.location.href='<?= ROOT ?>/ServiceProviderProfile/index?id=<?= htmlspecialchars($data['provider_id'] ?? '') ?>'">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Show/hide parts of each service card based on selection
        function wireServiceToggle(idx) {
            const checkbox = document.getElementById(`service${idx}`);
            if (!checkbox) return;
            function updateVisibility() {
                const visible = checkbox.checked;
                const rate = document.getElementById(`service${idx}Rate`);
                const desc = document.getElementById(`service${idx}Desc`);
                const details = document.getElementById(`service${idx}Details`);
                if (rate) rate.style.display = visible ? '' : 'none';
                if (desc) desc.style.display = visible ? '' : 'none';
                if (details) details.style.display = visible ? '' : 'none';
            }
            checkbox.addEventListener('change', updateVisibility);
            updateVisibility();
        }
        [1,2,3,4,5,6,7,8].forEach(wireServiceToggle);
    </script>
</body>
</html>
