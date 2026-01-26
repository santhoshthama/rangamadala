<!-- Request Service Modal -->
<div id="requestModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Request Service<span id="serviceTypeName"></span></h2>
            <button class="close-modal" onclick="closeRequestModal()">&times;</button>
        </div>
        <style>
            .calendar-widget {
                margin: 15px auto;
                padding: 15px;
                background: #ffffff;
                border-radius: 8px;
                border: 1px solid #dee2e6;
                box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                max-width: 450px;
            }
            .calendar-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 12px;
            }
            .calendar-btn {
                padding: 6px 12px;
                background: #6c757d;
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                font-size: 12px;
                font-weight: 500;
                transition: background 0.2s;
            }
            .calendar-btn:hover {
                background: #5a6268;
            }
            .calendar-month-year {
                margin: 0;
                color: #333;
                font-size: 15px;
                font-weight: 600;
            }
            .calendar-legend {
                display: flex;
                gap: 12px;
                justify-content: center;
                margin-bottom: 10px;
                font-size: 12px;
                color: #333;
                font-weight: 500;
            }
            .calendar-legend-item {
                display: flex;
                align-items: center;
                gap: 5px;
            }
            .calendar-legend-color {
                display: inline-block;
                width: 14px;
                height: 14px;
                border-radius: 3px;
            }
            .calendar-grid {
                display: grid;
                grid-template-columns: repeat(7, 1fr);
                gap: 4px;
                margin-bottom: 12px;
            }
            .calendar-day-header {
                text-align: center;
                font-weight: 600;
                padding: 6px 4px;
                color: #495057;
                font-size: 11px;
            }
            .calendar-day-cell {
                padding: 8px 6px;
                text-align: center;
                border-radius: 5px;
                cursor: pointer;
                transition: all 0.2s;
                font-size: 12px;
                font-weight: 500;
                min-height: 32px;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .calendar-day-available {
                background: #28a745;
                color: white;
            }
            .calendar-day-available:hover {
                background: #218838;
                transform: scale(1.05);
            }
            .calendar-day-booked {
                background: #dc3545;
                color: white;
                cursor: not-allowed;
            }
            .calendar-day-selected {
                background: #007bff;
                color: white;
                font-weight: 600;
            }
            .calendar-day-selected:hover {
                background: #0056b3;
                transform: scale(1.05);
            }
            .calendar-day-past {
                background: #e9ecef;
                color: #adb5bd;
                cursor: not-allowed;
            }
            .calendar-selection-info {
                margin-top: 12px;
                padding: 8px 10px;
                background: #f8f9fa;
                border-radius: 5px;
                text-align: center;
                font-size: 12px;
                color: #666;
                border: 1px solid #dee2e6;
            }
        </style>
        <form id="requestForm" method="POST" action="<?= ROOT ?>/ServiceProviderRequest/submit" enctype="multipart/form-data" data-booked-dates="<?= htmlspecialchars(json_encode($data['booked_dates'] ?? [])) ?>">
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

            <!-- Availability Calendar Section -->
            <div style="margin: 20px 0;">
                <label style="display: block; font-weight: 600; color: #333; margin-bottom: 10px; font-size: 15px;">
                    Check Availability & Select Dates
                </label>
                <p style="color: #666; font-size: 13px; margin-bottom: 15px;">View available dates and select your preferred date range for the service.</p>
            </div>

            <!-- Calendar Widget -->
            <div class="calendar-widget">
                <div class="calendar-header">
                    <button type="button" id="prevMonth" class="calendar-btn">&lt; Prev</button>
                    <h3 id="calendarMonthYear" class="calendar-month-year"></h3>
                    <button type="button" id="nextMonth" class="calendar-btn">Next &gt;</button>
                </div>
                <div class="calendar-legend">
                    <div class="calendar-legend-item">
                        <span class="calendar-legend-color" style="background: #dc3545;"></span>
                        <span>Booked</span>
                    </div>
                    <div class="calendar-legend-item">
                        <span class="calendar-legend-color" style="background: #28a745;"></span>
                        <span>Available</span>
                    </div>
                    <div class="calendar-legend-item">
                        <span class="calendar-legend-color" style="background: #007bff;"></span>
                        <span>Selected</span>
                    </div>
                </div>
                <div class="calendar-grid" id="calendarGrid"></div>
                <div class="calendar-selection-info" id="selectionInfo">Click dates to select your date range</div>
            </div>

            <!-- Hidden date inputs for form submission -->
            <input type="hidden" name="start_date" id="startDateInput" required>
            <input type="hidden" name="end_date" id="endDateInput" required>

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

    // Calendar Widget
    const CalendarWidget = {
        currentMonth: new Date().getMonth(),
        currentYear: new Date().getFullYear(),
        selectedStartDate: null,
        selectedEndDate: null,
        bookedDates: [],
        
        init() {
            const form = document.getElementById('requestForm');
            const bookedDatesJSON = form.getAttribute('data-booked-dates');
            this.bookedDates = bookedDatesJSON ? JSON.parse(bookedDatesJSON) : [];
            
            this.startDateInput = document.getElementById('startDateInput');
            this.endDateInput = document.getElementById('endDateInput');
            this.selectionInfo = document.getElementById('selectionInfo');
            
            this.attachEventListeners();
            this.generate(this.currentMonth, this.currentYear);
        },
        
        attachEventListeners() {
            document.getElementById('prevMonth').addEventListener('click', (e) => {
                e.preventDefault();
                this.currentMonth--;
                if (this.currentMonth < 0) {
                    this.currentMonth = 11;
                    this.currentYear--;
                }
                this.generate(this.currentMonth, this.currentYear);
            });
            
            document.getElementById('nextMonth').addEventListener('click', (e) => {
                e.preventDefault();
                this.currentMonth++;
                if (this.currentMonth > 11) {
                    this.currentMonth = 0;
                    this.currentYear++;
                }
                this.generate(this.currentMonth, this.currentYear);
            });
        },
        
        generate(month, year) {
            const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                              'July', 'August', 'September', 'October', 'November', 'December'];
            const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            
            document.getElementById('calendarMonthYear').textContent = monthNames[month] + ' ' + year;
            
            const calendarGrid = document.getElementById('calendarGrid');
            calendarGrid.innerHTML = '';
            
            // Day headers
            dayNames.forEach(day => {
                const header = document.createElement('div');
                header.className = 'calendar-day-header';
                header.textContent = day;
                calendarGrid.appendChild(header);
            });
            
            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            
            // Empty cells
            for (let i = 0; i < firstDay; i++) {
                calendarGrid.appendChild(document.createElement('div'));
            }
            
            // Day cells
            for (let day = 1; day <= daysInMonth; day++) {
                const dateStr = year + '-' + String(month + 1).padStart(2, '0') + '-' + String(day).padStart(2, '0');
                const dayCell = document.createElement('div');
                dayCell.className = 'calendar-day-cell';
                dayCell.textContent = day;
                dayCell.setAttribute('data-date', dateStr);
                
                const isBooked = this.bookedDates.includes(dateStr);
                const isPast = new Date(dateStr) < new Date(new Date().setHours(0, 0, 0, 0));
                const isSelected = this.isDateSelected(dateStr);
                
                if (isPast) {
                    dayCell.className += ' calendar-day-past';
                } else if (isBooked) {
                    dayCell.className += ' calendar-day-booked';
                    dayCell.title = 'This date is already booked';
                } else if (isSelected) {
                    dayCell.className += ' calendar-day-selected';
                    dayCell.addEventListener('click', () => this.selectDate(dateStr));
                } else {
                    dayCell.className += ' calendar-day-available';
                    dayCell.addEventListener('click', () => this.selectDate(dateStr));
                }
                
                calendarGrid.appendChild(dayCell);
            }
        },
        
        isDateSelected(dateStr) {
            if (!this.selectedStartDate || !this.selectedEndDate) return false;
            const cellDate = new Date(dateStr);
            const start = new Date(this.selectedStartDate);
            const end = new Date(this.selectedEndDate);
            const isBooked = this.bookedDates.includes(dateStr);
            const isPast = new Date(dateStr) < new Date(new Date().setHours(0, 0, 0, 0));
            return cellDate >= start && cellDate <= end && !isBooked && !isPast;
        },
        
        selectDate(dateStr) {
            if (this.bookedDates.includes(dateStr)) {
                alert('This date is already booked. Please select another date.');
                return;
            }
            
            if (!this.selectedStartDate || (this.selectedStartDate && this.selectedEndDate)) {
                this.selectedStartDate = dateStr;
                this.selectedEndDate = null;
                this.updateInfo('Selected: ' + this.formatDate(dateStr) + ' (Click another date for end date)');
            } else {
                if (new Date(dateStr) < new Date(this.selectedStartDate)) {
                    this.selectedEndDate = this.selectedStartDate;
                    this.selectedStartDate = dateStr;
                } else {
                    this.selectedEndDate = dateStr;
                }
                
                if (this.validateRange(this.selectedStartDate, this.selectedEndDate)) {
                    this.startDateInput.value = this.selectedStartDate;
                    this.endDateInput.value = this.selectedEndDate;
                    this.updateInfo('Selected: ' + this.formatDate(this.selectedStartDate) + ' to ' + this.formatDate(this.selectedEndDate));
                    this.validateDates();
                } else {
                    this.selectedStartDate = null;
                    this.selectedEndDate = null;
                    this.updateInfo('Selection contains booked dates. Please try again.');
                    setTimeout(() => this.updateInfo('Click dates to select your date range'), 3000);
                }
            }
            
            this.generate(this.currentMonth, this.currentYear);
        },
        
        validateRange(start, end) {
            const startDate = new Date(start);
            const endDate = new Date(end);
            
            for (let d = new Date(startDate); d <= endDate; d.setDate(d.getDate() + 1)) {
                const checkDate = d.getFullYear() + '-' + 
                                String(d.getMonth() + 1).padStart(2, '0') + '-' + 
                                String(d.getDate()).padStart(2, '0');
                if (this.bookedDates.includes(checkDate)) return false;
            }
            return true;
        },
        
        formatDate(dateStr) {
            const date = new Date(dateStr);
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            return months[date.getMonth()] + ' ' + date.getDate() + ', ' + date.getFullYear();
        },
        
        updateInfo(text) {
            this.selectionInfo.textContent = text;
        },
        
        validateDates() {
            const startDate = this.startDateInput.value;
            const endDate = this.endDateInput.value;
            
            if (!startDate || !endDate) return true;
            
            const conflicts = this.getConflicts(startDate, endDate);
            if (conflicts.length > 0) {
                const conflictText = conflicts.map(d => this.formatDate(d)).join(', ');
                console.error('Date conflict: ' + conflictText);
                return false;
            }
            return true;
        },
        
        getConflicts(start, end) {
            const startDate = new Date(start);
            const endDate = new Date(end);
            return this.bookedDates.filter(bookedDate => {
                const booked = new Date(bookedDate);
                return booked >= startDate && booked <= endDate;
            });
        },
        
        reset() {
            this.selectedStartDate = null;
            this.selectedEndDate = null;
            this.currentMonth = new Date().getMonth();
            this.currentYear = new Date().getFullYear();
        }
    };
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', () => {
        CalendarWidget.init();
        
        // Validate on form submit
        document.getElementById('requestForm').addEventListener('submit', (e) => {
            if (!CalendarWidget.validateDates()) {
                e.preventDefault();
                alert('Please select a valid date range without booked dates.');
            }
        });
    });
    
    // Reset calendar when modal opens
    const originalOpenRequestModal = window.openRequestModal;
    window.openRequestModal = function(serviceName, rate) {
        CalendarWidget.reset();
        if (originalOpenRequestModal) {
            originalOpenRequestModal(serviceName, rate);
        }
        setTimeout(() => {
            const form = document.getElementById('requestForm');
            const bookedDatesJSON = form.getAttribute('data-booked-dates');
            CalendarWidget.bookedDates = bookedDatesJSON ? JSON.parse(bookedDatesJSON) : [];
            CalendarWidget.generate(CalendarWidget.currentMonth, CalendarWidget.currentYear);
        }, 100);
    };
</script>
