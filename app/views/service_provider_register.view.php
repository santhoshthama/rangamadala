<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= APP_NAME ?></title>
  <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/service provider/service_provider_register.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
      <link rel="shortcut icon" href="<?php echo ROOT;?>/assets/images/Rangamadala logo.png" type="image/x-icon">

</head>

<body>
  <div class="signup-container signup-service">
        <div class="back-container">
      <a href="<?= ROOT ?>/Signup" class="back-link">
        <button type="button" class="back-btn">
          <i class="fas fa-arrow-left"></i> Back to Selection
        </button>
      </a>
    
    <div class="register-card">
            <div class="register-header">
                <h2>Register as Service Provider</h2>
                <p>Complete the form below to create your professional profile</p>
            </div>

            <?php if (!empty($data['errors'])): ?>
                <div class="error-modal-overlay" id="errorModal">
                    <div class="error-modal">
                        <div class="error-modal-icon">!</div>
                        <h3>Submission Error</h3>
                        <ul class="error-list">
                            <?php foreach ($data['errors'] as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button class="error-modal-button" onclick="document.getElementById('errorModal').style.display='none'">Try Again</button>
                    </div>
                </div>
                <style>
                    .error-modal-overlay {
                        display: flex;
                        position: fixed;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        background: rgba(0, 0, 0, 0.5);
                        backdrop-filter: blur(4px);
                        justify-content: center;
                        align-items: center;
                        padding: 20px;
                        z-index: 9999;
                    }
                    .error-modal {
                        background: #fff;
                        padding: 36px;
                        border-radius: 16px;
                        text-align: center;
                        max-width: 480px;
                        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
                    }
                    .error-modal-icon {
                        width: 72px;
                        height: 72px;
                        background: linear-gradient(135deg, #e53e3e 0%, #f56565 100%);
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        margin: 0 auto 20px;
                        font-size: 36px;
                        color: white;
                    }
                    .error-modal h3 {
                        font-size: 24px;
                        font-weight: 700;
                        color: #1a1a1a;
                        margin-bottom: 12px;
                    }
                    .error-list {
                        color: #6b7280;
                        text-align: left;
                        margin: 0 auto 24px;
                        padding-left: 20px;
                        font-size: 16px;
                        line-height: 1.6;
                    }
                    .error-modal-button {
                        background: linear-gradient(135deg, #e53e3e 0%, #f56565 100%);
                        color: white;
                        border: none;
                        padding: 12px 36px;
                        border-radius: 8px;
                        font-size: 15px;
                        font-weight: 600;
                        cursor: pointer;
                        transition: all 0.2s ease;
                    }
                    .error-modal-button:hover {
                        box-shadow: 0 6px 20px rgba(229, 62, 62, 0.3);
                        transform: translateY(-2px);
                    }
                </style>
            <?php endif; ?>

            <div class="register-content">

                <div class="alert-info">
                    <span class="alert-info-icon">ℹ️</span>
                    <div class="alert-info-text">
                        All fields marked with <strong style="color: #b8860b;">*</strong> are required. Provide accurate information to help clients find and trust you.
                    </div>
                </div>
   
                <!-- Page Indicator -->
                <div class="page-indicator">
                    <div class="step active" data-step="1">
                        <div class="step-number">1</div>
                    </div>
                    <div class="step" data-step="2">
                        <div class="step-number">2</div>
                    </div>
                    <div class="step" data-step="3">
                        <div class="step-number">3</div>
                    </div>
                    <div class="step" data-step="4">
                        <div class="step-number">4</div>
                    </div>
                </div>

                <!-- Basic Information -->
                <?php 
                    $servicesData = $data['services'] ?? [];
                    $projectsData = $data['projects'] ?? [];
                    $formData     = $data['formData'] ?? [];
                ?>
                <form id="serviceForm" action="<?= ROOT ?>/ServiceProviderRegister/submit" method="POST" enctype="multipart/form-data">
                    <div class="form-page active">
                    <div class="section">
                        <h3 class="section-title">Basic Information</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Full Name <span class="required">*</span></label>
                                <input type="text" name="full_name" class="form-input" placeholder="Enter your full name" value="<?= isset($data['formData']['full_name']) ? htmlspecialchars($data['formData']['full_name']) : '' ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Professional Title <span class="required">*</span></label>
                                <input type="text" name="professional_title" class="form-input" placeholder="e.g., Drama Production Specialist" value="<?= isset($data['formData']['professional_title']) ? htmlspecialchars($data['formData']['professional_title']) : '' ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Email Address <span class="required">*</span></label>
                                <input type="email" name="email" class="form-input" placeholder="your.email@example.com" value="<?= isset($data['formData']['email']) ? htmlspecialchars($data['formData']['email']) : '' ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Phone Number <span class="required">*</span></label>
                                <input type="tel" name="phone" class="form-input" placeholder="(+94) 000-000-0000" value="<?= isset($data['formData']['phone']) ? htmlspecialchars($data['formData']['phone']) : '' ?>" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Password <span class="required">*</span></label>
                                <input type="password" name="password" class="form-input" placeholder="At least 6 characters" minlength="6" value="<?= isset($data['password']) ? htmlspecialchars($data['password']) : '' ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Confirm Password <span class="required">*</span></label>
                                <input type="password" name="confirm_password" class="form-input" placeholder="Re-enter your password" minlength="6" value="<?= isset($data['confirm_password']) ? htmlspecialchars($data['confirm_password']) : '' ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">NIC Number <span class="required">*</span></label>
                                <input type="text" name="nic_number" class="form-input" placeholder="e.g., 200012345678 or 199512345V" value="<?= isset($data['formData']['nic_number']) ? htmlspecialchars($data['formData']['nic_number']) : '' ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Location</label>
                                <input type="text" name="location" class="form-input" placeholder="City, Country" value="<?= isset($data['formData']['location']) ? htmlspecialchars($data['formData']['location']) : '' ?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Years of Experience</label>
                                <input type="number" name="years_experience" class="form-input" placeholder="Enter your years of experience" value="<?= isset($data['formData']['years_experience']) ? htmlspecialchars($data['formData']['years_experience']) : '' ?>"> 
                            </div>

                            <div class="form-group">
                                <label class="form-label">Social Media Link</label>
                                <input type="url" name="website" class="form-input" placeholder="https://www.facebook.com/yourprofile" value="<?= isset($data['formData']['website']) ? htmlspecialchars($data['formData']['website']) : '' ?>">
                            </div>

                        </div>

                        <div class="form-group">
                            <label class="form-label">Professional Summary</label>
                            <textarea name="professional_summary" class="form-input textarea" placeholder="Describe your experience, expertise, and what makes you unique..."><?= isset($data['formData']['professional_summary']) ? htmlspecialchars($data['formData']['professional_summary']) : '' ?></textarea>
                        </div>
                    </div>
                    </div>

                    <div class="form-page">
                    <div class="section">
                        <h3 class="section-title">NIC (Both Sides Photo Upload)</h3>

                        <?php 
                        $existingFront = $data['uploadedPhotoFront'] ?? ($formData['nic_photo_front'] ?? '');
                        $existingBack = $data['uploadedPhotoBack'] ?? ($formData['nic_photo_back'] ?? '');
                        ?>
                        
                        <!-- Front Side Upload -->
                        <div style="margin-bottom: 30px;">
                            <h4 style="color: #333; font-weight: 600; margin-bottom: 15px;">Front Side of NIC</h4>
                            
                            <!-- File Preview Section -->
                            <div id="filePreviewSectionFront" style="<?= !empty($existingFront) ? '' : 'display: none;' ?> background: #f8f9fa; border: 2px solid #e9ecef; border-radius: 12px; padding: 20px; margin-bottom: 20px;">
                                <div style="display: flex; align-items: center; gap: 20px;">
                                    <img id="certPreviewFront" src="<?= !empty($existingFront) ? ROOT . '/' . $existingFront : '' ?>" alt="NIC Front" style="width: 120px; height: 120px; object-fit: cover; border-radius: 8px; border: 2px solid #dee2e6;">
                                    <div style="flex: 1;">
                                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                            <span style="color: #28a745; font-size: 20px;">✓</span>
                                            <strong style="color: #28a745;">Front Side Uploaded</strong>
                                        </div>
                                        <p id="certFileNameFront" style="margin: 0 0 12px 0; color: #6c757d; font-size: 14px;">
                                            <?= !empty($existingFront) ? basename($existingFront) : '' ?>
                                        </p>
                                        <button type="button" onclick="removeCertificateFront()" style="background: #dc3545; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: 600;">
                                            🗑️ Remove File
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="existing_nic_photo_front" id="existingCertPathFront" value="<?= htmlspecialchars($existingFront) ?>">

                            <div class="upload-instructions" id="uploadSectionFront" style="<?= !empty($existingFront) ? 'display: none;' : '' ?>">
                                <div class="drag-drop-zone">
                                    <input type="file" name="nic_photo_front" id="nicPhotoFrontInput" accept=".jpg,.jpeg,.png" class="form-input" <?= empty($existingFront) ? 'required' : '' ?> onchange="previewCertificateFront(this)">
                                </div>
                                <div class="upload-specs">
                                    <div class="spec-item">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <line x1="12" y1="16" x2="12" y2="12"></line>
                                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                        </svg>
                                        <span>Max size: 5MB</span>
                                    </div>
                                    <div class="spec-item">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                            <polyline points="21 15 16 10 5 21"></polyline>
                                        </svg>
                                        <span>JPG, PNG formats</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Back Side Upload -->
                        <div>
                            <h4 style="color: #333; font-weight: 600; margin-bottom: 15px;">Back Side of NIC</h4>
                            
                            <!-- File Preview Section -->
                            <div id="filePreviewSectionBack" style="<?= !empty($existingBack) ? '' : 'display: none;' ?> background: #f8f9fa; border: 2px solid #e9ecef; border-radius: 12px; padding: 20px; margin-bottom: 20px;">
                                <div style="display: flex; align-items: center; gap: 20px;">
                                    <img id="certPreviewBack" src="<?= !empty($existingBack) ? ROOT . '/' . $existingBack : '' ?>" alt="NIC Back" style="width: 120px; height: 120px; object-fit: cover; border-radius: 8px; border: 2px solid #dee2e6;">
                                    <div style="flex: 1;">
                                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                            <span style="color: #28a745; font-size: 20px;">✓</span>
                                            <strong style="color: #28a745;">Back Side Uploaded</strong>
                                        </div>
                                        <p id="certFileNameBack" style="margin: 0 0 12px 0; color: #6c757d; font-size: 14px;">
                                            <?= !empty($existingBack) ? basename($existingBack) : '' ?>
                                        </p>
                                        <button type="button" onclick="removeCertificateBack()" style="background: #dc3545; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: 600;">
                                            🗑️ Remove File
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="existing_nic_photo_back" id="existingCertPathBack" value="<?= htmlspecialchars($existingBack) ?>">

                            <div class="upload-instructions" id="uploadSectionBack" style="<?= !empty($existingBack) ? 'display: none;' : '' ?>">
                                <div class="drag-drop-zone">
                                    <input type="file" name="nic_photo_back" id="nicPhotoBackInput" accept=".jpg,.jpeg,.png" class="form-input" <?= empty($existingBack) ? 'required' : '' ?> onchange="previewCertificateBack(this)">
                                </div>
                                <div class="upload-specs">
                                    <div class="spec-item">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <line x1="12" y1="16" x2="12" y2="12"></line>
                                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                        </svg>
                                        <span>Max size: 5MB</span>
                                    </div>
                                    <div class="spec-item">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                            <polyline points="21 15 16 10 5 21"></polyline>
                                        </svg>
                                        <span>JPG, PNG formats</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Availability -->
                    <div class="section">
                        <h3 class="section-title">Availability</h3>
                        <div class="availability-toggle">
                            <span class="toggle-label">Currently Available for New Projects</span>
                            <?php $avail = isset($formData['availability']) ? (int)$formData['availability'] : 1; ?>
                            <input type="hidden" name="availability" id="availabilityInput" value="<?= $avail ?>">
                            <div id="availabilityToggle" class="toggle <?= $avail ? 'active' : '' ?>" onclick="toggleAvailability()"></div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Availability Notes</label>
                            <input type="text" name="availability_notes" class="form-input" placeholder="e.g., Available weekdays, weekends only, etc." value="<?= isset($formData['availability_notes']) ? htmlspecialchars($formData['availability_notes']) : '' ?>">
                        </div>
                    </div>
                    </div>

                    <div class="form-page">
                    <!-- Services & Rates -->
                    <div class="section">
                        <h3 class="section-title">Services & Rates</h3>
                        
                        <?php $svc0 = $servicesData[0] ?? []; ?>
                        <div class="service-item">
                            <div class="service-header">
                                <div class="checkbox-group">
                                    <input type="checkbox" name="services[0][selected]" class="checkbox" id="service1" <?= !empty($svc0['selected']) ? 'checked' : '' ?>>
                                    <label for="service1" class="service-name">🎭 Theater Production</label>
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
                            
                            <!-- Theatre-specific fields -->
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

                        <?php $svc1 = $servicesData[1] ?? []; ?>
                        <div class="service-item">
                            <div class="service-header">
                                <div class="checkbox-group">
                                    <input type="checkbox" name="services[1][selected]" class="checkbox" id="service2" <?= !empty($svc1['selected']) ? 'checked' : '' ?>>
                                    <label for="service2" class="service-name">💡 Lighting Design</label>
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
                            
                            <!-- Lighting-specific fields -->
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

                        <?php $svc2 = $servicesData[2] ?? []; ?>
                        <div class="service-item">
                            <div class="service-header">
                                <div class="checkbox-group">
                                    <input type="checkbox" name="services[2][selected]" class="checkbox" id="service3" <?= !empty($svc2['selected']) ? 'checked' : '' ?>>
                                    <label for="service3" class="service-name">🔊 Sound Systems</label>
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
                            
                            <!-- Sound-specific fields -->
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

                        <?php $svc3 = $servicesData[3] ?? []; ?>
                        <div class="service-item">
                            <div class="service-header">
                                <div class="checkbox-group">
                                    <input type="checkbox" name="services[3][selected]" class="checkbox" id="service4" <?= !empty($svc3['selected']) ? 'checked' : '' ?>>
                                    <label for="service4" class="service-name">🎬 Video Production</label>
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
                            
                            <!-- Video-specific fields -->
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

                        <?php $svc4 = $servicesData[4] ?? []; ?>
                        <div class="service-item">
                            <div class="service-header">
                                <div class="checkbox-group">
                                    <input type="checkbox" name="services[4][selected]" class="checkbox" id="service5" <?= !empty($svc4['selected']) ? 'checked' : '' ?>>
                                    <label for="service5" class="service-name">🎨 Set Design</label>
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
                            
                            <!-- Set Design-specific fields -->
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

                        <?php $svc5 = $servicesData[5] ?? []; ?>
                        <div class="service-item">
                            <div class="service-header">
                                <div class="checkbox-group">
                                    <input type="checkbox" name="services[5][selected]" class="checkbox" id="service6" <?= !empty($svc5['selected']) ? 'checked' : '' ?>>
                                    <label for="service6" class="service-name">👗 Costume Design</label>
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
                            
                            <!-- Costume Design-specific fields -->
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

                        <?php $svc6 = $servicesData[6] ?? []; ?>
                        <div class="service-item">
                            <div class="service-header">
                                <div class="checkbox-group">
                                    <input type="checkbox" name="services[6][selected]" class="checkbox" id="service7" <?= !empty($svc6['selected']) ? 'checked' : '' ?>>
                                    <label for="service7" class="service-name">💄 Makeup & Hair</label>
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

                            <!-- Makeup & Hair-specific fields -->
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

                        <?php $svc7 = $servicesData[7] ?? []; ?>
                        <div class="service-item">
                            <div class="service-header">
                                <div class="checkbox-group">
                                    <input type="checkbox" name="services[7][selected]" class="checkbox" id="service8" <?= !empty($svc7['selected']) ? 'checked' : '' ?>>
                                    <label for="service8" class="service-name">📋 Other</label>
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
                        </div>
                    </div>
                    </div>

                    <div class="form-page">
                    <!-- Recent Projects -->
                    <div class="section">
                        <h3 class="section-title">
                            Recent Projects
                            <button type="button" class="add-btn" onclick="addProject()">+ Add Project</button>
                        </h3>
                        <div id="projectList">
                            <?php if (!empty($projectsData) && is_array($projectsData)): ?>
                                <?php foreach ($projectsData as $idx => $proj): ?>
                                    <div class="project-item">
                                        <button type="button" class="remove-btn" onclick="removeProject(this)">×</button>
                                        <div class="form-row">
                                            <div class="form-group">
                                                <label class="form-label">Year <span class="required">*</span></label>
                                                <input type="number" name="projects[<?= $idx ?>][year]" class="form-input" min="1970" max="2030" placeholder="2024" value="<?= htmlspecialchars($proj['year'] ?? '') ?>">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">Project Name <span class="required">*</span></label>
                                                <input type="text" name="projects[<?= $idx ?>][project_name]" class="form-input" placeholder="e.g., Romeo & Juliet" value="<?= htmlspecialchars($proj['project_name'] ?? '') ?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Services Provided</label>
                                            <input type="text" name="projects[<?= $idx ?>][services_provided]" class="form-input" placeholder="e.g., Lighting Design, Sound Systems" value="<?= htmlspecialchars($proj['services_provided'] ?? '') ?>">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Description</label>
                                            <textarea name="projects[<?= $idx ?>][description]" class="form-input textarea" placeholder="Brief project description..."><?= htmlspecialchars($proj['description'] ?? '') ?></textarea>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="buttons-section" style="display: none;">
                        <button type="submit" class="btn">Submit Registration</button>
                        <span class="save-status" id="saveStatus" style="display: none;">✓ Saved</span>
                    </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="form-navigation">
                        <button type="button" class="nav-btn prev-btn" onclick="prevPage()" style="display: none;">
                            <i class="fas fa-arrow-left"></i> Previous
                        </button>
                        <button type="button" class="nav-btn next-btn" onclick="nextPage()">
                            Next <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </form>
    </div>
      
  </div>

  <script>
        let projectCount = document.querySelectorAll('#projectList .project-item').length;
        let currentPage = 1;
        const totalPages = 4;

        // Validation using browser's native validation
        function validateCurrentPage() {
            const currentPageElement = document.querySelectorAll('.form-page')[currentPage - 1];
            const requiredFields = currentPageElement.querySelectorAll('[required]');
            
            // Check password match on page 1
            if (currentPage === 1) {
                const password = document.querySelector('input[name="password"]').value;
                const confirmPassword = document.querySelector('input[name="confirm_password"]').value;
                
                if (password !== confirmPassword) {
                    alert('⚠️ Passwords do not match. Please ensure both password fields are identical.');
                    return false;
                }
            }
            
            for (let field of requiredFields) {
                if (!field.reportValidity()) {
                    return false;
                }
            }
            return true;
        }

        // Page Navigation
        function showPage(pageNum) {
            document.querySelectorAll('.form-page').forEach(page => {
                page.classList.remove('active');
            });
            document.querySelectorAll('.form-page')[pageNum - 1].classList.add('active');
            
            document.querySelectorAll('.step').forEach((step, index) => {
                step.classList.remove('active', 'completed');
                if (index + 1 < pageNum) step.classList.add('completed');
                else if (index + 1 === pageNum) step.classList.add('active');
            });
            
            const prevBtn = document.querySelector('.prev-btn');
            const nextBtn = document.querySelector('.next-btn');
            const submitSection = document.querySelector('.buttons-section');
            
            prevBtn.style.display = pageNum === 1 ? 'none' : 'inline-flex';
            if (pageNum === totalPages) {
                nextBtn.style.display = 'none';
                submitSection.style.display = 'block';
            } else {
                nextBtn.style.display = 'inline-flex';
                submitSection.style.display = 'none';
            }
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function nextPage() {
            if (currentPage < totalPages && validateCurrentPage()) {
                currentPage++;
                showPage(currentPage);
            }
        }

        function prevPage() {
            if (currentPage > 1) {
                currentPage--;
                showPage(currentPage);
            }
        }

        window.addEventListener('DOMContentLoaded', () => {
            // Initialize toggle based on preserved value
            const toggle = document.getElementById('availabilityToggle');
            const input = document.getElementById('availabilityInput');
            if (input.value === '1') {
                toggle.classList.add('active');
            } else {
                toggle.classList.remove('active');
            }

            showPage(1);

            // Initialize collapse/expand for all service cards
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

            [1,2,3,4,5,6,7].forEach(wireServiceToggle);
            
            // Wire the makeup service (service6b)
            const checkboxMakeup = document.getElementById('service6b');
            if (checkboxMakeup) {
                function updateMakeupVisibility() {
                    const visible = checkboxMakeup.checked;
                    const rate = document.getElementById('service6bRate');
                    const desc = document.getElementById('service6bDesc');
                    const details = document.getElementById('service6bDetails');
                    if (rate) rate.style.display = visible ? '' : 'none';
                    if (desc) desc.style.display = visible ? '' : 'none';
                    if (details) details.style.display = visible ? '' : 'none';
                }
                checkboxMakeup.addEventListener('change', updateMakeupVisibility);
                updateMakeupVisibility();
            }
        });

        // Photo upload handler
        // Toggle availability
        function toggleAvailability() {
            const toggle = document.getElementById('availabilityToggle');
            toggle.classList.toggle('active');
            const input = document.getElementById('availabilityInput');
            input.value = toggle.classList.contains('active')?'1':'0';
        }

        

        // Add project
        function addProject() {
            const list = document.getElementById('projectList');
            const index = list.children.length;
            const item = document.createElement('div');
            item.className = 'project-item';
            item.innerHTML = `
                <button type="button" class="remove-btn" onclick="removeProject(this)">×</button>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Year <span class="required">*</span></label>
                        <input type="number" name="projects[${index}][year]" class="form-input" min="1970" max="2025" placeholder="2024">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Project Name <span class="required">*</span></label>
                        <input type="text" name="projects[${index}][project_name]" class="form-input" placeholder="e.g., Romeo & Juliet">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Services Provided</label>
                    <input type="text" name="projects[${index}][services_provided]" class="form-input" placeholder="e.g., Lighting Design, Sound Systems">
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="projects[${index}][description]" class="form-input textarea" placeholder="Brief project description..."></textarea>
                </div>
            `;
            list.appendChild(item);
            projectCount++;
        }

        function removeProject(btn) {
            btn.parentElement.remove();
        }

        // Certificate file preview and removal
        function previewCertificate(input) {
            const file = input.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('certPreview').src = e.target.result;
                    document.getElementById('certFileName').textContent = file.name;
                    document.getElementById('filePreviewSection').style.display = 'block';
                    document.getElementById('uploadSection').style.display = 'none';
                    document.getElementById('businessCertInput').removeAttribute('required');
                };
                reader.readAsDataURL(file);
            }
        }

        function removeCertificate() {
            document.getElementById('businessCertInput').value = '';
            document.getElementById('existingCertPath').value = '';
            document.getElementById('filePreviewSection').style.display = 'none';
            document.getElementById('uploadSection').style.display = 'block';
            document.getElementById('businessCertInput').setAttribute('required', 'required');
        }

        

        function saveAsDraft() {
            const status = document.getElementById('saveStatus');
            status.style.display = 'inline';
            status.textContent = '✓ Draft Saved';
            status.style.color = '#6c757d';
            
            setTimeout(() => {
                status.style.display = 'none';
            }, 3000);
        }

        function goBack() {
            if (confirm('Are you sure you want to go back? Unsaved changes will be lost.')) {
                window.history.back();
            }
        }
    </script>
    
</body>

</html>
