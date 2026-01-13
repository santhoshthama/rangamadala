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
        <button class="back-button" onclick="window.location.href='<?php echo ROOT; ?>/ServiceProviderProfile/index?id=<?php echo $data['service']->provider_id; ?>'">
            <span>←</span>
            <span>Back to Profile</span>
        </button>

        <div class="register-card">
            <div class="register-header">
                <h2>Edit Service</h2>
                <p>Update service details and rates</p>
            </div>

            <div class="register-content">
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger">
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <?php
                $service = $data['service'];
                $details = $data['details'] ?? null;
                $serviceName = strtolower(trim($service->service_name));
                ?>

                <form method="POST">
                    <div class="section">
                        <div class="form-group">
                            <label class="form-label">Service Name <span class="required">*</span></label>
                            <input type="text" name="service_name" class="form-input" 
                                value="<?php echo htmlspecialchars($service->service_name); ?>" required readonly style="background: #f5f5f5;">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Rate per Hour (Rs) <span class="required">*</span></label>
                            <input type="number" step="0.01" name="rate_per_hour" class="form-input" 
                                value="<?php echo htmlspecialchars($service->rate_per_hour); ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-input textarea" rows="5"><?php echo htmlspecialchars($service->description ?? ''); ?></textarea>
                        </div>

                        <?php if ($serviceName === 'theater production' && $details): ?>
                        <h3 class="section-title" style="margin-top: 20px;">Theater Production Details</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Number of Actors/Participants</label>
                                <input type="number" name="num_actors" class="form-input" placeholder="e.g., 15" value="<?php echo htmlspecialchars($details->num_actors ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Expected Audience (for shows)</label>
                                <input type="number" name="expected_audience" class="form-input" placeholder="e.g., 200" value="<?php echo htmlspecialchars($details->expected_audience ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Stage Requirements</label>
                            <div class="checkbox-group-vertical">
                                <label><input type="checkbox" name="stage_req[]" value="Proscenium" <?php echo isset($details->stage_proscenium) && $details->stage_proscenium ? 'checked' : ''; ?>> Proscenium</label>
                                <label><input type="checkbox" name="stage_req[]" value="Black box" <?php echo isset($details->stage_black_box) && $details->stage_black_box ? 'checked' : ''; ?>> Black box</label>
                                <label><input type="checkbox" name="stage_req[]" value="Open floor" <?php echo isset($details->stage_open_floor) && $details->stage_open_floor ? 'checked' : ''; ?>> Open floor</label>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Seating Requirement</label>
                                <input type="text" name="seating" class="form-input" placeholder="e.g., 300 seats" value="<?php echo htmlspecialchars($details->seating_requirement ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Parking Requirement</label>
                                <input type="text" name="parking" class="form-input" placeholder="e.g., 50 vehicles" value="<?php echo htmlspecialchars($details->parking_requirement ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Special Technical Requirements</label>
                            <textarea name="special_tech" class="form-input textarea" placeholder="Describe any special technical needs..."><?php echo htmlspecialchars($details->special_tech ?? ''); ?></textarea>
                        </div>
                        <?php endif; ?>

                        <?php if ($serviceName === 'lighting design' && $details): ?>
                        <h3 class="section-title" style="margin-top: 20px;">Lighting Design Details</h3>
                        <div class="form-group">
                            <label class="form-label">Required Lighting Services</label>
                            <div class="checkbox-group-vertical">
                                <label><input type="checkbox" name="lighting_services[]" value="Stage Lighting" <?php echo isset($details->stage_lighting) && $details->stage_lighting ? 'checked' : ''; ?>> Stage Lighting</label>
                                <label><input type="checkbox" name="lighting_services[]" value="Spotlights" <?php echo isset($details->spotlights) && $details->spotlights ? 'checked' : ''; ?>> Spotlights</label>
                                <label><input type="checkbox" name="lighting_services[]" value="Custom Lighting Programming" <?php echo isset($details->custom_programming) && $details->custom_programming ? 'checked' : ''; ?>> Custom Lighting Programming</label>
                                <label><input type="checkbox" name="lighting_services[]" value="Moving Heads" <?php echo isset($details->moving_heads) && $details->moving_heads ? 'checked' : ''; ?>> Moving Heads</label>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Number of Lights Required</label>
                                <input type="number" name="num_lights" class="form-input" placeholder="e.g., 20" value="<?php echo htmlspecialchars($details->num_lights ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Required Lighting Effects</label>
                                <input type="text" name="lighting_effects" class="form-input" placeholder="e.g., Color changes, Strobe effects" value="<?php echo htmlspecialchars($details->effects ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">On-site Technician Needed?</label>
                            <select name="technician_needed" class="form-input">
                                <option value="">Select an option</option>
                                <option value="Yes" <?php echo (isset($details->technician_needed) && $details->technician_needed === 'Yes') ? 'selected' : ''; ?>>Yes</option>
                                <option value="No" <?php echo (isset($details->technician_needed) && $details->technician_needed === 'No') ? 'selected' : ''; ?>>No</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Additional Notes</label>
                            <textarea name="additional_notes" class="form-input textarea" placeholder="Any additional requirements or notes..."><?php echo htmlspecialchars($details->notes ?? ''); ?></textarea>
                        </div>
                        <?php endif; ?>

                        <?php if ($serviceName === 'sound systems' && $details): ?>
                        <h3 class="section-title" style="margin-top: 20px;">Sound Systems Details</h3>
                        <div class="form-group">
                            <label class="form-label">Required Sound Services</label>
                            <div class="checkbox-group-vertical">
                                <label><input type="checkbox" name="sound_services[]" value="PA system" <?php echo isset($details->pa_system) && $details->pa_system ? 'checked' : ''; ?>> PA system</label>
                                <label><input type="checkbox" name="sound_services[]" value="Microphones" <?php echo isset($details->microphones) && $details->microphones ? 'checked' : ''; ?>> Microphones</label>
                                <label><input type="checkbox" name="sound_services[]" value="Sound Mixing" <?php echo isset($details->sound_mixing) && $details->sound_mixing ? 'checked' : ''; ?>> Sound Mixing</label>
                                <label><input type="checkbox" name="sound_services[]" value="Background Music" <?php echo isset($details->background_music) && $details->background_music ? 'checked' : ''; ?>> Background Music</label>
                                <label><input type="checkbox" name="sound_services[]" value="Special Effects" <?php echo isset($details->special_effects) && $details->special_effects ? 'checked' : ''; ?>> Special Effects</label>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Number of Wireless Mics</label>
                                <input type="number" name="num_mics" class="form-input" placeholder="e.g., 8" value="<?php echo htmlspecialchars($details->num_mics ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Stage Monitor Requirement?</label>
                                <select name="stage_monitor" class="form-input">
                                    <option value="">Select an option</option>
                                    <option value="Yes" <?php echo (isset($details->stage_monitor) && $details->stage_monitor === 'Yes') ? 'selected' : ''; ?>>Yes</option>
                                    <option value="No" <?php echo (isset($details->stage_monitor) && $details->stage_monitor === 'No') ? 'selected' : ''; ?>>No</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Sound Engineer Needed?</label>
                            <select name="sound_engineer" class="form-input">
                                <option value="">Select an option</option>
                                <option value="Yes" <?php echo (isset($details->sound_engineer) && $details->sound_engineer === 'Yes') ? 'selected' : ''; ?>>Yes</option>
                                <option value="No" <?php echo (isset($details->sound_engineer) && $details->sound_engineer === 'No') ? 'selected' : ''; ?>>No</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Additional Notes</label>
                            <textarea name="additional_notes" class="form-input textarea" placeholder="Any additional requirements..."><?php echo htmlspecialchars($details->notes ?? ''); ?></textarea>
                        </div>
                        <?php endif; ?>

                        <?php if ($serviceName === 'video production' && $details): ?>
                        <h3 class="section-title" style="margin-top: 20px;">Video Production Details</h3>
                        <div class="form-group">
                            <label class="form-label">Video Production Purpose</label>
                            <div class="checkbox-group-vertical">
                                <label><input type="checkbox" name="video_purpose[]" value="Full Event Recording" <?php echo isset($details->full_event) && $details->full_event ? 'checked' : ''; ?>> Full Event Recording</label>
                                <label><input type="checkbox" name="video_purpose[]" value="Highlight Reel" <?php echo isset($details->highlight_reel) && $details->highlight_reel ? 'checked' : ''; ?>> Highlight Reel</label>
                                <label><input type="checkbox" name="video_purpose[]" value="Short Promo" <?php echo isset($details->short_promo) && $details->short_promo ? 'checked' : ''; ?>> Short Promo</label>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Number of Cameras Required</label>
                                <input type="number" name="num_cameras" class="form-input" placeholder="e.g., 3" value="<?php echo htmlspecialchars($details->num_cameras ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Drone Coverage?</label>
                                <select name="drone_needed" class="form-input">
                                    <option value="">Select an option</option>
                                    <option value="Yes" <?php echo (isset($details->drone_needed) && $details->drone_needed === 'Yes') ? 'selected' : ''; ?>>Yes</option>
                                    <option value="No" <?php echo (isset($details->drone_needed) && $details->drone_needed === 'No') ? 'selected' : ''; ?>>No</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Gimbals/Steadicams?</label>
                                <select name="gimbals" class="form-input">
                                    <option value="">Select an option</option>
                                    <option value="Yes" <?php echo (isset($details->gimbals) && $details->gimbals === 'Yes') ? 'selected' : ''; ?>>Yes</option>
                                    <option value="No" <?php echo (isset($details->gimbals) && $details->gimbals === 'No') ? 'selected' : ''; ?>>No</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Editing Required?</label>
                                <select name="editing" class="form-input">
                                    <option value="">Select an option</option>
                                    <option value="Yes" <?php echo (isset($details->editing) && $details->editing === 'Yes') ? 'selected' : ''; ?>>Yes</option>
                                    <option value="No" <?php echo (isset($details->editing) && $details->editing === 'No') ? 'selected' : ''; ?>>No</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Preferred Delivery Format</label>
                            <div class="checkbox-group-vertical">
                                <label><input type="checkbox" name="delivery_format[]" value="MP4" <?php echo isset($details->delivery_mp4) && $details->delivery_mp4 ? 'checked' : ''; ?>> MP4</label>
                                <label><input type="checkbox" name="delivery_format[]" value="RAW files" <?php echo isset($details->delivery_raw) && $details->delivery_raw ? 'checked' : ''; ?>> RAW files</label>
                                <label><input type="checkbox" name="delivery_format[]" value="Social Media Format" <?php echo isset($details->delivery_social) && $details->delivery_social ? 'checked' : ''; ?>> Social Media Format</label>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Additional Notes</label>
                            <textarea name="additional_notes" class="form-input textarea" placeholder="Any additional crew or requirements..."><?php echo htmlspecialchars($details->notes ?? ''); ?></textarea>
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
                            <label class="form-label">Required Service Type</label>
                            <div class="checkbox-group-vertical">
                                <label><input type="checkbox" name="costume_service[]" value="Costume Design" <?php echo isset($details->costume_design) && $details->costume_design ? 'checked' : ''; ?>> Costume Design</label>
                                <label><input type="checkbox" name="costume_service[]" value="Costume Creation" <?php echo isset($details->costume_creation) && $details->costume_creation ? 'checked' : ''; ?>> Costume Creation</label>
                                <label><input type="checkbox" name="costume_service[]" value="Costume Rental" <?php echo isset($details->costume_rental) && $details->costume_rental ? 'checked' : ''; ?>> Costume Rental</label>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Number of Characters</label>
                                <input type="number" name="num_characters" class="form-input" placeholder="e.g., 8" value="<?php echo htmlspecialchars($details->num_characters ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Number of Costumes</label>
                                <input type="number" name="num_costumes" class="form-input" placeholder="e.g., 12" value="<?php echo htmlspecialchars($details->num_costumes ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Measurements Required?</label>
                                <select name="measurements" class="form-input">
                                    <option value="">Select an option</option>
                                    <option value="Yes" <?php echo (isset($details->measurements_required) && $details->measurements_required === 'Yes') ? 'selected' : ''; ?>>Yes</option>
                                    <option value="No" <?php echo (isset($details->measurements_required) && $details->measurements_required === 'No') ? 'selected' : ''; ?>>No</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Fitting Dates</label>
                                <input type="date" name="fitting_dates" class="form-input" value="<?php echo htmlspecialchars($details->fitting_dates ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Budget Range (Rs)</label>
                                <input type="text" name="budget" class="form-input" placeholder="e.g., 30,000 - 80,000" value="<?php echo htmlspecialchars($details->budget_range ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Delivery Deadline</label>
                                <input type="date" name="deadline" class="form-input" value="<?php echo htmlspecialchars($details->deadline ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Additional Notes</label>
                            <textarea name="additional_notes" class="form-input textarea" placeholder="Character descriptions, style preferences, any special requirements..."><?php echo htmlspecialchars($details->notes ?? ''); ?></textarea>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="buttons-section">
                        <button type="submit" class="btn">Save Changes</button>
                        <button type="button" class="btn btn-secondary" 
                            onclick="window.location.href='<?php echo ROOT; ?>/ServiceProviderProfile/index?id=<?php echo $data['service']->provider_id; ?>'">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
