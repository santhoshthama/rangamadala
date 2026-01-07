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
                                <label class="form-label">Location <span class="required">*</span></label>
                                <input type="text" name="location" class="form-input" placeholder="City, Country" value="<?= isset($data['formData']['location']) ? htmlspecialchars($data['formData']['location']) : '' ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Website</label>
                                <input type="url" name="website" class="form-input" placeholder="https://yourwebsite.com" value="<?= isset($data['formData']['website']) ? htmlspecialchars($data['formData']['website']) : '' ?>">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Years of Experience <span class="required">*</span></label>
                                <input type="number" name="years_experience" class="form-input" placeholder="Enter your years of experience" value="<?= isset($data['formData']['years_experience']) ? htmlspecialchars($data['formData']['years_experience']) : '' ?>" required> 
                            </div>

                        </div>

                        <div class="form-group">
                            <label class="form-label">Professional Summary <span class="required">*</span></label>
                            <textarea name="professional_summary" class="form-input textarea" placeholder="Describe your experience, expertise, and what makes you unique..." required><?= isset($data['formData']['professional_summary']) ? htmlspecialchars($data['formData']['professional_summary']) : '' ?></textarea>
                        </div>
                    </div>
                    </div>

                    <div class="form-page">
                    <div class="section">
                        <h3 class="section-title">Business Registration Certificate (Photo Upload)</h3>

                        <?php $existingCert = $data['uploadedPhoto'] ?? ($formData['business_cert_photo'] ?? ''); ?>
                        
                        <!-- File Preview Section -->
                        <div id="filePreviewSection" style="<?= !empty($existingCert) ? '' : 'display: none;' ?> background: #f8f9fa; border: 2px solid #e9ecef; border-radius: 12px; padding: 20px; margin-bottom: 20px;">
                            <div style="display: flex; align-items: center; gap: 20px;">
                                <img id="certPreview" src="<?= !empty($existingCert) ? ROOT . '/' . $existingCert : '' ?>" alt="Certificate" style="width: 120px; height: 120px; object-fit: cover; border-radius: 8px; border: 2px solid #dee2e6;">
                                <div style="flex: 1;">
                                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                        <span style="color: #28a745; font-size: 20px;">✓</span>
                                        <strong style="color: #28a745;">Certificate Uploaded</strong>
                                    </div>
                                    <p id="certFileName" style="margin: 0 0 12px 0; color: #6c757d; font-size: 14px;">
                                        <?= !empty($existingCert) ? basename($existingCert) : '' ?>
                                    </p>
                                    <button type="button" onclick="removeCertificate()" style="background: #dc3545; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: 600;">
                                        🗑️ Remove File
                                    </button>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="existing_business_cert_photo" id="existingCertPath" value="<?= htmlspecialchars($existingCert) ?>">

                        <div class="upload-instructions" id="uploadSection" style="<?= !empty($existingCert) ? 'display: none;' : '' ?>">
                            <div class="drag-drop-zone">
                                <input type="file" name="business_cert_photo" id="businessCertInput" accept=".jpg,.jpeg,.png" class="form-input" <?= empty($existingCert) ? 'required' : '' ?> onchange="previewCertificate(this)">
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
                                <div class="spec-item">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                    <span>Recommended: 400x400px</span>
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
                                    <input type="checkbox" name="services[0][selected]" class="checkbox" id="service1" <?= isset($svc0['selected']) ? 'checked' : '' ?>>
                                    <label for="service1" class="service-name">🎭 Theater Production</label>
                                    <input type="hidden" name="services[0][name]" value="Theater Production">
                                </div>
                                <div class="rate-input-group">
                                    <label>Rate per hour:</label>
                                    <div class="input-wrapper">
                                        <span class="currency">Rs</span>
                                        <input type="number" name="services[0][rate]" class="service-rate" placeholder="0.00" value="<?= isset($svc0['rate']) ? htmlspecialchars($svc0['rate']) : '' ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Description </label>
                                <textarea name="services[0][description]" class="form-input textarea" placeholder="Add a description about this service..." ><?= isset($svc0['description']) ? htmlspecialchars($svc0['description']) : '' ?></textarea>
                            </div>
                            
                        </div>

                        <?php $svc1 = $servicesData[1] ?? []; ?>
                        <div class="service-item">
                            <div class="service-header">
                                <div class="checkbox-group">
                                    <input type="checkbox" name="services[1][selected]" class="checkbox" id="service2" <?= isset($svc1['selected']) ? 'checked' : '' ?>>
                                    <label for="service2" class="service-name">💡 Lighting Design</label>
                                    <input type="hidden" name="services[1][name]" value="Lighting Design">
                                </div>
                                <div class="rate-input-group">
                                    <label>Rate per hour:</label>
                                    <div class="input-wrapper">
                                        <span class="currency">Rs</span>
                                        <input type="number" name="services[1][rate]" class="service-rate" placeholder="0.00" value="<?= isset($svc1['rate']) ? htmlspecialchars($svc1['rate']) : '' ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Description </label>
                                <textarea name="services[1][description]" class="form-input textarea" placeholder="Add a description about this service..."><?= isset($svc1['description']) ? htmlspecialchars($svc1['description']) : '' ?></textarea>
                            </div>

                        </div>

                        <?php $svc2 = $servicesData[2] ?? []; ?>
                        <div class="service-item">
                            <div class="service-header">
                                <div class="checkbox-group">
                                    <input type="checkbox" name="services[2][selected]" class="checkbox" id="service3" <?= isset($svc2['selected']) ? 'checked' : '' ?>>
                                    <label for="service3" class="service-name">🔊 Sound Systems</label>
                                    <input type="hidden" name="services[2][name]" value="Sound Systems">

                                </div>
                                <div class="rate-input-group">
                                    <label>Rate per hour:</label>
                                    <div class="input-wrapper">
                                        <span class="currency">Rs</span>
                                        <input type="number" name="services[2][rate]" class="service-rate" placeholder="0.00" value="<?= isset($svc2['rate']) ? htmlspecialchars($svc2['rate']) : '' ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Description</label>
                                <textarea name="services[2][description]" class="form-input textarea" placeholder="Add a description about this service..." ><?= isset($svc2['description']) ? htmlspecialchars($svc2['description']) : '' ?></textarea>
                            </div>
                        </div>

                        <?php $svc3 = $servicesData[3] ?? []; ?>
                        <div class="service-item">
                            <div class="service-header">
                                <div class="checkbox-group">
                                    <input type="checkbox" name="services[3][selected]" class="checkbox" id="service4" <?= isset($svc3['selected']) ? 'checked' : '' ?>>
                                    <label for="service4" class="service-name">🎬 Video Production</label>
                                    <input type="hidden" name="services[3][name]" value="Video Production">
                                </div>

                                <div class="rate-input-group">
                                    <label>Rate per hour:</label>
                                    <div class="input-wrapper">
                                        <span class="currency">Rs</span>
                                        <input type="number" name="services[3][rate]" class="service-rate" placeholder="0.00" value="<?= isset($svc3['rate']) ? htmlspecialchars($svc3['rate']) : '' ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Description </label>
                                <textarea name="services[3][description]" class="form-input textarea" placeholder="Add a description about this service..."><?= isset($svc3['description']) ? htmlspecialchars($svc3['description']) : '' ?></textarea>
                            </div>
                        </div>

                        <?php $svc4 = $servicesData[4] ?? []; ?>
                        <div class="service-item">
                            <div class="service-header">
                                <div class="checkbox-group">
                                    <input type="checkbox" name="services[4][selected]" class="checkbox" id="service5" <?= isset($svc4['selected']) ? 'checked' : '' ?>>
                                    <label for="service5" class="service-name">🎨 Set Design</label>
                                    <input type="hidden" name="services[4][name]" value="Set Design">
                                </div>

                                <div class="rate-input-group">
                                    <label>Rate per hour:</label>
                                    <div class="input-wrapper">
                                        <span class="currency">Rs</span>
                                        <input type="number" name="services[4][rate]" class="service-rate" placeholder="0.00" value="<?= isset($svc4['rate']) ? htmlspecialchars($svc4['rate']) : '' ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Description </label>
                                <textarea name="services[4][description]" class="form-input textarea" placeholder="Add a description about this service..."><?= isset($svc4['description']) ? htmlspecialchars($svc4['description']) : '' ?></textarea>
                            </div>
                        </div>

                        <?php $svc5 = $servicesData[5] ?? []; ?>
                        <div class="service-item">
                            <div class="service-header">
                                <div class="checkbox-group">
                                    <input type="checkbox" name="services[5][selected]" class="checkbox" id="service6" <?= isset($svc5['selected']) ? 'checked' : '' ?>>
                                    <label for="service6" class="service-name">👗 Costume Design</label>
                                    <input type="hidden" name="services[5][name]" value="Costume Design">
                                </div>
                                <div class="rate-input-group">
                                    <label>Rate per hour:</label>
                                    <div class="input-wrapper">
                                        <span class="currency">Rs</span>
                                        <input type="number" name="services[5][rate]" class="service-rate" placeholder="0.00" value="<?= isset($svc5['rate']) ? htmlspecialchars($svc5['rate']) : '' ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Description </label>
                                <textarea name="services[5][description]" class="form-input textarea" placeholder="Add a description about this service..."><?= isset($svc5['description']) ? htmlspecialchars($svc5['description']) : '' ?></textarea>
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
