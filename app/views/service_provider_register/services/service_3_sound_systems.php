<?php $svc2 = $servicesData[2] ?? []; ?>
<div class="service-item">
  <div class="service-header">
    <div class="checkbox-group">
      <input type="checkbox" name="services[2][selected]" class="checkbox" id="service3" <?= !empty($svc2['selected']) ? 'checked' : '' ?>>
      <label for="service3" class="service-name">🔊 Sound Systems</label>
      <input type="hidden" name="services[2][name]" value="Sound Systems">
    </div>
    <div class="rate-input-group" id="service3Rate" style="<?= !empty($svc2['selected']) ? '' : 'display:none;' ?>">
      <div style="display:flex; gap:15px; align-items:flex-end; flex-wrap:wrap;">
        <div>
          <label style="display:block; margin-bottom:6px; font-size:13px; color:#6b7280; font-weight:500;">Rate Type:</label>
          <select name="services[2][rate_type]" class="form-input" style="padding:8px 12px; font-size:14px; min-width:130px;">
            <option value="hourly" <?= isset($svc2['rate_type']) && $svc2['rate_type'] === 'daily' ? '' : 'selected' ?>>Per Hour</option>
            <option value="daily" <?= isset($svc2['rate_type']) && $svc2['rate_type'] === 'daily' ? 'selected' : '' ?>>Per Day</option>
          </select>
        </div>
        <div>
          <label style="display:block; margin-bottom:6px; font-size:13px; color:#6b7280; font-weight:500;">Rate (Rs):</label>
          <div class="input-wrapper">
            <span class="currency">Rs</span>
            <input type="number" name="services[2][rate]" class="service-rate" min="0" step="1" placeholder="0.00" value="<?= isset($svc2['rate']) ? htmlspecialchars($svc2['rate']) : '' ?>">
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="form-group" id="service3Desc" style="<?= !empty($svc2['selected']) ? '' : 'display:none;' ?>">
    <label class="form-label">Description</label>
    <textarea name="services[2][description]" class="form-input textarea" placeholder="Add a description about this service..."><?= isset($svc2['description']) ? htmlspecialchars($svc2['description']) : '' ?></textarea>
  </div>
  <div class="service-details" id="service3Details" style="<?= !empty($svc2['selected']) ? '' : 'display:none;' ?>">
    <div class="form-group">
      <label class="form-label">Sound Equipment Provided</label>
      <textarea name="services[2][sound_equipment_provided]" class="form-input textarea" placeholder="Describe the equipment provided (e.g., PA, mixers, microphones)"><?= isset($svc2['sound_equipment_provided']) ? htmlspecialchars($svc2['sound_equipment_provided']) : '' ?></textarea>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Maximum Audience Size Supported</label>
        <input type="number" name="services[2][max_audience_size]" class="form-input" min="0" step="1" placeholder="e.g., 500" value="<?= isset($svc2['max_audience_size']) ? htmlspecialchars($svc2['max_audience_size']) : '' ?>">
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
