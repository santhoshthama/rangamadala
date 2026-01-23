<!-- Request Service Modal -->
<div id="requestModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Request Service<span id="serviceTypeName"></span></h2>
            <button class="close-modal" onclick="closeRequestModal()">&times;</button>
        </div>
        <form id="requestForm" method="POST" action="<?= ROOT ?>/ServiceProviderRequest/submit">
            <input type="hidden" name="provider_id" value="<?= $data['provider']->user_id ?>">
            <input type="hidden" name="requested_by" value="<?= $_SESSION['user_id'] ?? '' ?>">
            
            <!-- Production Manager Contact Details (Pre-filled) -->
            <div class="form-group">
                <label>Your Name <span class="required">*</span></label>
                <input type="text" name="requester_name" required class="form-input" value="<?= htmlspecialchars($_SESSION['full_name'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Your Email <span class="required">*</span></label>
                <input type="email" name="requester_email" required class="form-input" value="<?= htmlspecialchars($_SESSION['email'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Your Phone <span class="required">*</span></label>
                <input type="tel" name="requester_phone" required class="form-input" value="<?= htmlspecialchars($_SESSION['phone'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Drama/Production Name <span class="required">*</span></label>
                <input type="text" name="drama_name" required class="form-input">
            </div>

            <!-- Hidden field to store the service type -->
            <input type="hidden" id="serviceType" name="service_type">

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
                <div class="form-group">
                    <label>Venue Type</label>
                    <div style="display: flex; gap: 15px; margin-top: 8px;">
                        <label style="display: flex; align-items: center; gap: 6px; margin: 0;"><input type="radio" name="theater_venue_type" value="Indoor"> Indoor</label>
                        <label style="display: flex; align-items: center; gap: 6px; margin: 0;"><input type="radio" name="theater_venue_type" value="Outdoor"> Outdoor</label>
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
                <div class="form-group">
                    <label>Stage Size Requirement</label>
                    <input type="text" name="theater_stage_size" placeholder="e.g., 20ft x 15ft" class="form-input">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Number of Days Needed</label>
                        <input type="number" name="theater_num_days" class="form-input">
                    </div>
                    <div class="form-group">
                        <label>Time</label>
                        <input type="time" name="theater_time" class="form-input">
                    </div>
                </div>
                <div class="form-group">
                    <label>Budget Range</label>
                    <input type="text" name="theater_budget_range" placeholder="e.g., 100000-250000" class="form-input">
                </div>
                <div class="form-group">
                    <label>Upload Reference (script / layout / images – optional)</label>
                    <input type="file" name="theater_reference" class="form-input" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.zip">
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
                <div class="form-group">
                    <label>Additional Lighting Requirements</label>
                    <textarea name="lighting_additional_requirements" class="form-input" rows="3" placeholder="Specify any other lighting equipment or requirements..."></textarea>
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
                <div class="form-group">
                    <label>Budget Range</label>
                    <input type="text" name="lighting_budget_range" placeholder="e.g., 50000-150000" class="form-input">
                </div>
                <div class="form-group">
                    <label>Upload Reference (script / layout / images – optional)</label>
                    <input type="file" name="lighting_reference" class="form-input" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.zip">
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
                <div class="form-group">
                    <label>Additional Sound Services</label>
                    <textarea name="sound_additional_services" class="form-input" rows="3" placeholder="Specify any other sound equipment or services needed..."></textarea>
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
                <div class="form-group">
                    <label>Budget Range</label>
                    <input type="text" name="sound_budget_range" placeholder="e.g., 75000-200000" class="form-input">
                </div>
                <div class="form-group">
                    <label>Upload Reference (script / layout / images – optional)</label>
                    <input type="file" name="sound_reference" class="form-input" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.zip">
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
                <div class="form-group">
                    <label>Additional Video Requirements</label>
                    <textarea name="video_additional_requirements" class="form-input" rows="3" placeholder="Specify any other video production requirements..."></textarea>
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
                <div class="form-group">
                    <label>Budget Range</label>
                    <input type="text" name="video_budget_range" placeholder="e.g., 100000-300000" class="form-input">
                </div>
                <div class="form-group">
                    <label>Upload Reference (script / layout / images – optional)</label>
                    <input type="file" name="video_reference" class="form-input" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.zip">
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
                <div class="form-group">
                    <label>Upload Reference (script / layout / images – optional)</label>
                    <input type="file" name="set_reference" class="form-input" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.zip">
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
                <div class="form-group">
                    <label>Upload Reference (script / layout / images – optional)</label>
                    <input type="file" name="costume_reference" class="form-input" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.zip">
                </div>
            </div>

            <!-- Makeup & Hair Specific Fields -->
            <div id="makeupFields" class="service-specific-fields" style="display: none;">
                <h3>Makeup & Hair Details</h3>
                <div class="form-group">
                    <label>Makeup Type</label>
                    <div style="display: flex; gap: 15px; margin-top: 8px; flex-wrap: wrap;">
                        <label style="display: flex; align-items: center; gap: 6px; margin: 0;"><input type="checkbox" name="makeup_stage"> Stage Makeup</label>
                        <label style="display: flex; align-items: center; gap: 6px; margin: 0;"><input type="checkbox" name="makeup_character"> Character Makeup</label>
                        <label style="display: flex; align-items: center; gap: 6px; margin: 0;"><input type="checkbox" name="makeup_traditional"> Traditional/Cultural</label>
                        <label style="display: flex; align-items: center; gap: 6px; margin: 0;"><input type="checkbox" name="makeup_sfx"> Special Effects</label>
                        <label style="display: flex; align-items: center; gap: 6px; margin: 0;"><input type="checkbox" name="makeup_hair_styling"> Hair Styling</label>
                    </div>
                </div>
                <div class="form-group">
                    <label>Additional Makeup Services</label>
                    <textarea name="makeup_additional_services" class="form-input" rows="3" placeholder="Specify any other makeup or hair requirements..."></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Number of Artists/Actors</label>
                        <input type="number" name="makeup_num_artists" class="form-input">
                    </div>
                    <div class="form-group">
                        <label>Application Time Per Person</label>
                        <input type="text" name="makeup_time_per_person" placeholder="e.g., 30 minutes" class="form-input">
                    </div>
                </div>
                <div class="form-group">
                    <label>Touch-up Service During Show</label>
                    <select name="makeup_touchup_service" class="form-input">
                        <option value="">-- Select --</option>
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Number of Days Needed</label>
                        <input type="number" name="makeup_num_days" class="form-input">
                    </div>
                    <div class="form-group">
                        <label>Service Time</label>
                        <input type="time" name="makeup_service_time" class="form-input">
                    </div>
                </div>
                <div class="form-group">
                    <label>Budget Range</label>
                    <input type="text" name="makeup_budget_range" placeholder="e.g., 50000-150000" class="form-input">
                </div>
                <div class="form-group">
                    <label>Upload Reference (makeup photos – optional)</label>
                    <input type="file" name="makeup_reference" class="form-input" accept=".jpg,.jpeg,.png,.gif,.pdf,.zip">
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

<script>
    function openRequestModal(serviceName = '', rate = '') {
        // Pre-fill the service name if provided
        if (serviceName) {
            const serviceTypeInput = document.getElementById('serviceType');
            const serviceTypeDisplay = document.getElementById('serviceTypeName');
            if (serviceTypeInput) {
                serviceTypeInput.value = serviceName;
            }
            // Show service-specific fields based on the service type
            updateServiceFields(serviceName);
            if (serviceTypeDisplay) {
                serviceTypeDisplay.textContent = ' - ' + serviceName;
            }
        }
        const modal = document.getElementById('requestModal');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        
        // Scroll modal content to top
        const modalContent = modal.querySelector('.modal-content');
        if (modalContent) {
            modalContent.scrollTop = 0;
        }
    }

    function closeRequestModal() {
        document.getElementById('requestModal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    function updateServiceFields(serviceName = '') {
        // Use the passed serviceName or get from the input field
        if (!serviceName) {
            const serviceInput = document.getElementById('serviceType');
            serviceName = serviceInput ? serviceInput.value : '';
        }
        const selectedService = serviceName.toLowerCase();
        
        // Hide all service-specific fields
        document.getElementById('theaterFields').style.display = 'none';
        document.getElementById('lightingFields').style.display = 'none';
        document.getElementById('soundFields').style.display = 'none';
        document.getElementById('videoFields').style.display = 'none';
        document.getElementById('setFields').style.display = 'none';
        document.getElementById('costumeFields').style.display = 'none';
        document.getElementById('makeupFields').style.display = 'none';
        
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
        } else if (selectedService.includes('makeup') || selectedService.includes('hair')) {
            document.getElementById('makeupFields').style.display = 'block';
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
