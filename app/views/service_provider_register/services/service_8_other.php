<?php $svc7 = $servicesData[7] ?? []; ?>
<div class="service-item">
  <div class="service-header">
    <div class="checkbox-group">
      <input type="checkbox" name="services[7][selected]" class="checkbox" id="service8" <?= !empty($svc7['selected']) ? 'checked' : '' ?>>
      <label for="service8" class="service-name">📋 Other</label>
      <input type="hidden" name="services[7][name]" value="Other">
    </div>
    <div class="rate-input-group" id="service8Rate" style="<?= !empty($svc7['selected']) ? '' : 'display:none;' ?>">
      <div style="display:flex; gap:15px; align-items:flex-end; flex-wrap:wrap;">
        <div>
          <label style="display:block; margin-bottom:6px; font-size:13px; color:#6b7280; font-weight:500;">Rate Type:</label>
          <select name="services[7][rate_type]" class="form-input" style="padding:8px 12px; font-size:14px; min-width:130px;">
            <option value="hourly" <?= isset($svc7['rate_type']) && $svc7['rate_type'] === 'daily' ? '' : 'selected' ?>>Per Hour</option>
            <option value="daily" <?= isset($svc7['rate_type']) && $svc7['rate_type'] === 'daily' ? 'selected' : '' ?>>Per Day</option>
          </select>
        </div>
        <div>
          <label style="display:block; margin-bottom:6px; font-size:13px; color:#6b7280; font-weight:500;">Rate (Rs):</label>
          <div class="input-wrapper">
            <span class="currency">Rs</span>
            <input type="number" name="services[7][rate]" class="service-rate" min="0" step="1" placeholder="0.00" value="<?= isset($svc7['rate']) ? htmlspecialchars($svc7['rate']) : '' ?>">
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="service-details" id="service8Details" style="<?= !empty($svc7['selected']) ? '' : 'display:none;' ?>">
    <div class="form-group">
      <label class="form-label">Service Type</label>
      <input type="text" name="services[7][service_type]" class="form-input" placeholder="e.g., Photography, Catering, Transportation, etc." value="<?= isset($svc7['service_type']) ? htmlspecialchars($svc7['service_type']) : '' ?>">
    </div>
  </div>
  <div class="form-group" id="service8Desc" style="<?= !empty($svc7['selected']) ? '' : 'display:none;' ?>">
    <label class="form-label">Description</label>
    <textarea name="services[7][description]" class="form-input textarea" placeholder="Add a description about this service..."><?= isset($svc7['description']) ? htmlspecialchars($svc7['description']) : '' ?></textarea>
  </div>
</div>
