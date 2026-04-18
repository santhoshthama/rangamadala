<?php $svc3 = $servicesData[3] ?? []; ?>
<div class="service-item">
  <div class="service-header">
    <div class="checkbox-group">
      <input type="checkbox" name="services[3][selected]" class="checkbox" id="service4" <?= !empty($svc3['selected']) ? 'checked' : '' ?>>
      <label for="service4" class="service-name">🎬 Video Production</label>
      <input type="hidden" name="services[3][name]" value="Video Production">
    </div>
    <div class="rate-input-group" id="service4Rate" style="<?= !empty($svc3['selected']) ? '' : 'display:none;' ?>">
      <div style="display:flex; gap:15px; align-items:flex-end; flex-wrap:wrap;">
        <div>
          <label style="display:block; margin-bottom:6px; font-size:13px; color:#6b7280; font-weight:500;">Rate Type:</label>
          <select name="services[3][rate_type]" class="form-input" style="padding:8px 12px; font-size:14px; min-width:130px;">
            <option value="hourly" <?= isset($svc3['rate_type']) && $svc3['rate_type'] === 'daily' ? '' : 'selected' ?>>Per Hour</option>
            <option value="daily" <?= isset($svc3['rate_type']) && $svc3['rate_type'] === 'daily' ? 'selected' : '' ?>>Per Day</option>
          </select>
        </div>
        <div>
          <label style="display:block; margin-bottom:6px; font-size:13px; color:#6b7280; font-weight:500;">Rate (Rs):</label>
          <div class="input-wrapper">
            <span class="currency">Rs</span>
            <input type="number" name="services[3][rate]" class="service-rate" min="0" step="1" placeholder="0.00" value="<?= isset($svc3['rate']) ? htmlspecialchars($svc3['rate']) : '' ?>">
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="form-group" id="service4Desc" style="<?= !empty($svc3['selected']) ? '' : 'display:none;' ?>">
    <label class="form-label">Description</label>
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
        <input type="number" name="services[3][num_crew_members]" class="form-input" min="0" step="1" placeholder="e.g., 3" value="<?= isset($svc3['num_crew_members']) ? htmlspecialchars($svc3['num_crew_members']) : '' ?>">
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
