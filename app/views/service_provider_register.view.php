<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= APP_NAME ?></title>
  <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/register.css">
  <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/service provider/service_provider_register.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">
  <link rel="shortcut icon" href="<?php echo ROOT; ?>/assets/images/Rangamadala logo.png" type="image/x-icon">
</head>

<body>
  <div class="back-container">
    <a href="<?= ROOT ?>/Signup" class="back-link">
      <button type="button" class="back-btn">
        <i class="bx bxs-arrow-left"></i> Back to Selection
      </button>
    </a>
  </div>

  <div class="signup-container">
    <div class="register-card">
      <div class="register-header">
        <h2>Service Provider Signup</h2>
      </div>

      <?php
        $fieldErrors = $data['fieldErrors'] ?? [];
        $firstErrorField = $data['firstErrorField'] ?? '';
        $fieldHasError = function ($field) use ($fieldErrors) {
          return isset($fieldErrors[$field]) && $fieldErrors[$field] !== '';
        };
        $fieldError = function ($field) use ($fieldErrors) {
          return $fieldErrors[$field] ?? '';
        };
      ?>

      <?php if (!empty($data['errors'])): ?>
        <div class="error-modal" style="margin: 20px 0 0;">
          <div class="error-modal-icon">!</div>
          <h3>Submission Error</h3>
          <ul class="error-list">
            <?php foreach ($data['errors'] as $error): ?>
              <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <div id="clientErrorBox" class="error-modal" style="display:none; margin: 20px 0 0;" aria-live="polite"></div>

      <div class="register-content">

        <div class="page-indicator">
          <div class="step active" data-step="1"><div class="step-number">1</div></div>
          <div class="step" data-step="2"><div class="step-number">2</div></div>
          <div class="step" data-step="3"><div class="step-number">3</div></div>
          <div class="step" data-step="4"><div class="step-number">4</div></div>
        </div>

        <?php
          $servicesData = $data['services'] ?? [];
          $projectsData = $data['projects'] ?? [];
          $formData = $data['formData'] ?? [];
          $existingFront = $data['uploadedPhotoFront'] ?? ($formData['nic_photo'] ?? ($formData['nic_photo_front'] ?? ''));
          $existingBack = $data['uploadedPhotoBack'] ?? ($formData['nic_photo_back'] ?? '');
          $avail = isset($formData['availability']) ? (int)$formData['availability'] : 1;
        ?>

        <form id="serviceForm" action="<?= ROOT ?>/ServiceProviderRegister/submit" method="POST" enctype="multipart/form-data" novalidate>
          <div class="form-page active">
            <div class="section">
              <h3 class="section-title">Basic Information</h3>

              <div class="form-row">
                <div class="form-group">
                  <label class="form-label">Full Name <span class="required">*</span></label>
                  <input type="text" name="full_name" class="form-input<?= $fieldHasError('full_name') ? ' input-error' : '' ?>" value="<?= htmlspecialchars($formData['full_name'] ?? '') ?>" required>
                  <?php if ($fieldHasError('full_name')): ?><div class="error-text"><?= htmlspecialchars($fieldError('full_name')) ?></div><?php endif; ?>
                </div>
                <div class="form-group">
                  <label class="form-label">Professional Title</label>
                  <input type="text" name="professional_title" class="form-input" value="<?= htmlspecialchars($formData['professional_title'] ?? '') ?>">
                </div>
              </div>

              <div class="form-row">
                <div class="form-group">
                  <label class="form-label">Email Address <span class="required">*</span></label>
                  <input type="email" name="email" class="form-input<?= $fieldHasError('email') ? ' input-error' : '' ?>" value="<?= htmlspecialchars($formData['email'] ?? '') ?>" required>
                  <?php if ($fieldHasError('email')): ?><div class="error-text"><?= htmlspecialchars($fieldError('email')) ?></div><?php endif; ?>
                </div>
                <div class="form-group">
                  <label class="form-label">Phone Number <span class="required">*</span></label>
                  <input type="tel" name="phone" class="form-input<?= $fieldHasError('phone') ? ' input-error' : '' ?>" value="<?= htmlspecialchars($formData['phone'] ?? '') ?>" pattern="(?:\+94|94|0)7\d{8}" title="Enter a valid Sri Lankan mobile number (07X XXX XXXX or +94 XXX XXX XXX)" inputmode="tel" required>
                  <?php if ($fieldHasError('phone')): ?><div class="error-text"><?= htmlspecialchars($fieldError('phone')) ?></div><?php endif; ?>
                </div>
              </div>

              <div class="form-row">
                <div class="form-group">
                  <label class="form-label">Password <span class="required">*</span></label>
                  <input type="password" name="password" class="form-input<?= $fieldHasError('password') ? ' input-error' : '' ?>" minlength="6" pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d]).{6,}" title="At least 6 characters with uppercase, lowercase, number, and symbol" required>
                  <?php if ($fieldHasError('password')): ?><div class="error-text"><?= htmlspecialchars($fieldError('password')) ?></div><?php endif; ?>
                </div>
                <div class="form-group">
                  <label class="form-label">Confirm Password <span class="required">*</span></label>
                  <input type="password" name="confirm_password" class="form-input<?= $fieldHasError('confirm_password') ? ' input-error' : '' ?>" minlength="6" required>
                  <?php if ($fieldHasError('confirm_password')): ?><div class="error-text"><?= htmlspecialchars($fieldError('confirm_password')) ?></div><?php endif; ?>
                </div>
              </div>

              <div class="form-group">
                <label class="form-label">NIC Number <span class="required">*</span></label>
                <input type="text" name="nic_number" class="form-input<?= $fieldHasError('nic_number') ? ' input-error' : '' ?>" placeholder="e.g., 200012345678 or 199512345V" value="<?= htmlspecialchars($formData['nic_number'] ?? '') ?>" pattern="(?:\d{12}|\d{9}[Vv])" title="Use 12 digits or old NIC ending with V" required>
                <?php if ($fieldHasError('nic_number')): ?><div class="error-text"><?= htmlspecialchars($fieldError('nic_number')) ?></div><?php endif; ?>
              </div>

              <div class="form-row">
                
                <div class="form-group">
                  <label class="form-label">Years of Experience <span class="required">*</span></label>
                  <input type="number" name="years_experience" class="form-input<?= $fieldHasError('years_experience') ? ' input-error' : '' ?>" placeholder="Enter your years of experience" value="<?= htmlspecialchars($formData['years_experience'] ?? '') ?>" min="0" step="1" required>
                  <?php if ($fieldHasError('years_experience')): ?><div class="error-text"><?= htmlspecialchars($fieldError('years_experience')) ?></div><?php endif; ?>
                </div>
              </div>

              <div class="form-row">
                <div class="form-group">
                  <label class="form-label">Social Media Link</label>
                  <input type="url" name="website" class="form-input" placeholder="https://www.facebook.com/yourprofile" value="<?= htmlspecialchars($formData['website'] ?? '') ?>">
                </div>
                <div class="form-group">
                  <label class="form-label">Availability Notes</label>
                  <input type="text" name="availability_notes" class="form-input" placeholder="e.g., Available weekends only" value="<?= htmlspecialchars($formData['availability_notes'] ?? '') ?>">
                </div>
              </div>

              <div class="form-group">
                <label class="form-label">Professional Summary</label>
                <textarea name="professional_summary" class="form-input textarea" placeholder="Describe your experience, expertise, and what makes you unique..."><?= htmlspecialchars($formData['professional_summary'] ?? '') ?></textarea>
              </div>
            </div>
          </div>

          <div class="form-page">
            <div class="section">
              <h3 class="section-title">NIC Upload</h3>

              <div style="margin-bottom: 24px;">
                <h4 style="color: #d4af37; margin-bottom: 10px;">Front Side of NIC</h4>

                <div id="filePreviewSectionFront" style="<?= !empty($existingFront) ? '' : 'display:none;' ?> background: rgba(245,240,232,0.06); border:1px solid rgba(212,175,55,0.3); border-radius:10px; padding:12px; margin-bottom:12px;">
                  <img id="certPreviewFront" src="<?= !empty($existingFront) ? ROOT . '/' . $existingFront : '' ?>" alt="NIC Front" style="width:100px;height:100px;object-fit:cover;border-radius:8px;display:block;margin-bottom:8px;">
                  <p id="certFileNameFront" style="color:#a89968;"><?= !empty($existingFront) ? basename($existingFront) : '' ?></p>
                  <button type="button" onclick="removeCertificateFront()" class="btn" style="margin-top:8px;">Remove</button>
                </div>

                <input type="hidden" name="existing_nic_photo" id="existingCertPathFront" value="<?= htmlspecialchars($existingFront) ?>">
                <div id="uploadSectionFront" style="<?= !empty($existingFront) ? 'display:none;' : '' ?>">
                  <input type="file" name="nic_photo" id="nicPhotoFrontInput" accept=".jpg,.jpeg,.png" class="form-input<?= $fieldHasError('nic_photo') ? ' input-error' : '' ?>" <?= empty($existingFront) ? 'required' : '' ?> onchange="previewCertificateFront(this)">
                  <?php if ($fieldHasError('nic_photo')): ?><div class="error-text"><?= htmlspecialchars($fieldError('nic_photo')) ?></div><?php endif; ?>
                </div>
              </div>

              <div>
                <h4 style="color: #d4af37; margin-bottom: 10px;">Back Side of NIC</h4>

                <div id="filePreviewSectionBack" style="<?= !empty($existingBack) ? '' : 'display:none;' ?> background: rgba(245,240,232,0.06); border:1px solid rgba(212,175,55,0.3); border-radius:10px; padding:12px; margin-bottom:12px;">
                  <img id="certPreviewBack" src="<?= !empty($existingBack) ? ROOT . '/' . $existingBack : '' ?>" alt="NIC Back" style="width:100px;height:100px;object-fit:cover;border-radius:8px;display:block;margin-bottom:8px;">
                  <p id="certFileNameBack" style="color:#a89968;"><?= !empty($existingBack) ? basename($existingBack) : '' ?></p>
                  <button type="button" onclick="removeCertificateBack()" class="btn" style="margin-top:8px;">Remove</button>
                </div>

                <input type="hidden" name="existing_nic_photo_back" id="existingCertPathBack" value="<?= htmlspecialchars($existingBack) ?>">
                <div id="uploadSectionBack" style="<?= !empty($existingBack) ? 'display:none;' : '' ?>">
                  <input type="file" name="nic_photo_back" id="nicPhotoBackInput" accept=".jpg,.jpeg,.png" class="form-input<?= $fieldHasError('nic_photo_back') ? ' input-error' : '' ?>" <?= empty($existingBack) ? 'required' : '' ?> onchange="previewCertificateBack(this)">
                  <?php if ($fieldHasError('nic_photo_back')): ?><div class="error-text"><?= htmlspecialchars($fieldError('nic_photo_back')) ?></div><?php endif; ?>
                </div>
              </div>

              <div style="margin-top: 24px;">
                <h4 style="color: #d4af37; margin-bottom: 10px;">Availability</h4>
                <div class="availability-toggle">
                  <span class="toggle-label">Currently Available for New Projects</span>
                  <input type="hidden" name="availability" id="availabilityInput" value="<?= $avail ?>">
                  <div id="availabilityToggle" class="toggle <?= $avail ? 'active' : '' ?>" onclick="toggleAvailability()"></div>
                </div>
              </div>
            </div>
          </div>

          <div class="form-page">
            <div class="section">
              <h3 class="section-title">Choose Service Type(s)</h3>
              <p style="margin-bottom:12px;color:#a89968;font-size:13px;">Select one or more service types. Detailed service information can be added after approval.</p>
              <?php if ($fieldHasError('services')): ?><div class="error-text" style="margin-bottom: 12px;"><?= htmlspecialchars($fieldError('services')) ?></div><?php endif; ?>

              <?php include __DIR__ . '/service_provider_register/services/service_1_theater_production.php'; ?>
              <?php include __DIR__ . '/service_provider_register/services/service_2_lighting_design.php'; ?>
              <?php include __DIR__ . '/service_provider_register/services/service_3_sound_systems.php'; ?>
              <?php include __DIR__ . '/service_provider_register/services/service_4_video_production.php'; ?>
              <?php include __DIR__ . '/service_provider_register/services/service_5_set_design.php'; ?>
              <?php include __DIR__ . '/service_provider_register/services/service_6_costume_design.php'; ?>
              <?php include __DIR__ . '/service_provider_register/services/service_7_makeup_hair.php'; ?>
              <?php include __DIR__ . '/service_provider_register/services/service_8_other.php'; ?>
            </div>
          </div>

          <div class="form-page">
            <div class="section">
              <h3 class="section-title">Past Engagements</h3>
              <p style="margin-bottom:12px;color:#a89968;font-size:13px;">Add your past engagements. This helps verify your experience.</p>

              <div id="projectList">
                <?php if (!empty($projectsData) && is_array($projectsData)): ?>
                  <?php foreach ($projectsData as $idx => $proj): ?>
                    <div class="project-item">
                      <button type="button" class="remove-btn" onclick="removeProject(this)">×</button>
                      <div class="form-row">
                        <div class="form-group">
                          <label class="form-label">Year <span class="required">*</span></label>
                          <input type="number" name="projects[<?= $idx ?>][year]" class="form-input" min="0" max="2030" step="1" placeholder="2024" value="<?= htmlspecialchars($proj['year'] ?? '') ?>">
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

              <button type="button" class="add-btn" onclick="addProject()">+ Add Project</button>
            </div>

            <div class="buttons-section" style="display:none;">
              <button type="submit" class="btn">Submit Registration</button>
            </div>
          </div>

          <div class="form-navigation">
            <button type="button" class="nav-btn prev-btn" onclick="prevPage()" style="display:none;">
              <i class="bx bx-arrow-back"></i> Previous
            </button>
            <button type="button" class="nav-btn next-btn" onclick="nextPage()">
              Next <i class="bx bx-right-arrow-alt"></i>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    let currentPage = 1;
    const totalPages = 4;

    function showValidationErrors(errors) {
      const box = document.getElementById('clientErrorBox');
      if (!box) return;

      if (!errors.length) {
        box.style.display = 'none';
        box.innerHTML = '';
        return;
      }

      box.innerHTML = `
        <div class="error-modal-icon">!</div>
        <h3>Submission Error</h3>
        <ul class="error-list">${errors.map(err => `<li>${err}</li>`).join('')}</ul>
      `;
      box.style.display = 'block';
      box.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function getFieldLabel(field) {
      const label = field.closest('.form-group')?.querySelector('label');
      if (!label) return field.name || 'This field';
      return (label.textContent || '').replace('*', '').trim() || field.name || 'This field';
    }

    function collectRequiredFieldErrors(currentPageElement) {
      const errors = [];
      const requiredFields = currentPageElement.querySelectorAll('[required]');

      for (const field of requiredFields) {
        const value = (field.value || '').trim();
        if (value === '') {
          errors.push(`${getFieldLabel(field)} is required.`);
          continue;
        }

        if (field.type === 'email' && !field.checkValidity()) {
          errors.push('Email format is invalid.');
          continue;
        }

        if (field.getAttribute('pattern') && !['password', 'phone', 'nic_number'].includes(field.name)) {
          const pattern = new RegExp('^' + field.getAttribute('pattern') + '$');
          if (!pattern.test(value)) {
            errors.push(field.title || `${getFieldLabel(field)} format is invalid.`);
          }
        }
      }

      return errors;
    }

    function validateCurrentPage() {
      const currentPageElement = document.querySelectorAll('.form-page')[currentPage - 1];
      const errors = collectRequiredFieldErrors(currentPageElement);

      if (currentPage === 1) {
        const password = document.querySelector('input[name="password"]').value;
        const confirmPassword = document.querySelector('input[name="confirm_password"]').value;
        const phoneInput = document.querySelector('input[name="phone"]');
        const nicInput = document.querySelector('input[name="nic_number"]');
        const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d]).{6,}$/;
        const phonePattern = /^(?:\+94|94|0)7\d{8}$/;
        const nicPattern = /^(?:\d{12}|\d{9}[Vv])$/;

        if (password && !passwordPattern.test(password)) {
          errors.push('Password must be at least 6 characters and include uppercase, lowercase, number, and symbol.');
        }

        if (password && confirmPassword && password !== confirmPassword) {
          errors.push('Password confirmation does not match.');
        }

        if (phoneInput && phoneInput.value.trim() && !phonePattern.test(phoneInput.value.trim())) {
          errors.push('Enter a valid Sri Lankan mobile number (e.g. 07X XXX XXXX or +94 XXX XXX XXX).');
        }

        if (nicInput && nicInput.value.trim() && !nicPattern.test(nicInput.value.trim())) {
          errors.push('Enter a valid Sri Lankan NIC (12 digits or old format ending with V).');
        }
      }

      if (errors.length > 0) {
        showValidationErrors([...new Set(errors)]);
        return false;
      }

      showValidationErrors([]);
      return true;
    }

    function showPage(pageNum) {
      document.querySelectorAll('.form-page').forEach(page => page.classList.remove('active'));
      document.querySelectorAll('.form-page')[pageNum - 1].classList.add('active');

      document.querySelectorAll('.step').forEach((step, index) => {
        step.classList.remove('active', 'completed');
        if (index + 1 < pageNum) {
          step.classList.add('completed');
        } else if (index + 1 === pageNum) {
          step.classList.add('active');
        }
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

        if (idx === 8) {
          const serviceType = document.querySelector('input[name="services[7][service_type]"]');
          if (serviceType) {
            serviceType.required = visible;
          }
        }
      }

      checkbox.addEventListener('change', updateVisibility);
      updateVisibility();
    }

    function previewCertificateFront(input) {
      const file = input.files[0];
      if (!file) return;

      const reader = new FileReader();
      reader.onload = function(e) {
        document.getElementById('certPreviewFront').src = e.target.result;
        document.getElementById('certFileNameFront').textContent = file.name;
        document.getElementById('filePreviewSectionFront').style.display = '';
        document.getElementById('uploadSectionFront').style.display = 'none';
      };
      reader.readAsDataURL(file);
    }

    function removeCertificateFront() {
      document.getElementById('nicPhotoFrontInput').value = '';
      document.getElementById('nicPhotoFrontInput').setAttribute('required', 'required');
      document.getElementById('existingCertPathFront').value = '';
      document.getElementById('filePreviewSectionFront').style.display = 'none';
      document.getElementById('uploadSectionFront').style.display = '';
    }

    function previewCertificateBack(input) {
      const file = input.files[0];
      if (!file) return;

      const reader = new FileReader();
      reader.onload = function(e) {
        document.getElementById('certPreviewBack').src = e.target.result;
        document.getElementById('certFileNameBack').textContent = file.name;
        document.getElementById('filePreviewSectionBack').style.display = '';
        document.getElementById('uploadSectionBack').style.display = 'none';
      };
      reader.readAsDataURL(file);
    }

    function removeCertificateBack() {
      document.getElementById('nicPhotoBackInput').value = '';
      document.getElementById('nicPhotoBackInput').setAttribute('required', 'required');
      document.getElementById('existingCertPathBack').value = '';
      document.getElementById('filePreviewSectionBack').style.display = 'none';
      document.getElementById('uploadSectionBack').style.display = '';
    }

    function toggleAvailability() {
      const toggle = document.getElementById('availabilityToggle');
      const input = document.getElementById('availabilityInput');
      toggle.classList.toggle('active');
      input.value = toggle.classList.contains('active') ? '1' : '0';
    }

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
            <input type="number" name="projects[${index}][year]" class="form-input" min="0" max="2030" step="1" placeholder="2024" required>
          </div>
          <div class="form-group">
            <label class="form-label">Project Name <span class="required">*</span></label>
            <input type="text" name="projects[${index}][project_name]" class="form-input" placeholder="e.g., Romeo & Juliet" required>
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
    }

    function removeProject(btn) {
      btn.parentElement.remove();
    }

    window.addEventListener('DOMContentLoaded', () => {
      const availabilityToggle = document.getElementById('availabilityToggle');
      const availabilityInput = document.getElementById('availabilityInput');
      const serviceForm = document.getElementById('serviceForm');
      if (availabilityToggle && availabilityInput) {
        if (availabilityInput.value === '1') {
          availabilityToggle.classList.add('active');
        } else {
          availabilityToggle.classList.remove('active');
        }
      }

      [1, 2, 3, 4, 5, 6, 7, 8].forEach(wireServiceToggle);

      const firstErrorField = <?= json_encode($firstErrorField) ?>;
      const pageByField = {
        full_name: 1,
        email: 1,
        phone: 1,
        password: 1,
        confirm_password: 1,
        nic_number: 1,
        years_experience: 1,
        nic_photo: 2,
        nic_photo_back: 2,
        services: 3
      };

      const startPage = firstErrorField && pageByField[firstErrorField] ? pageByField[firstErrorField] : 1;
      currentPage = startPage;
      showPage(startPage);

      if (firstErrorField) {
        const targetField = document.querySelector(`[name="${firstErrorField}"]`);
        if (targetField) {
          targetField.focus();
        }
      }

      if (serviceForm) {
        serviceForm.addEventListener('submit', (event) => {
          if (!validateCurrentPage()) {
            event.preventDefault();
          }
        });
      }
    });
  </script>
</body>

</html>
