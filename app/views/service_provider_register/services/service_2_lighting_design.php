<?php $svc1 = $servicesData[1] ?? []; ?>
<div class="service-item">
  <div class="service-header">
    <div class="checkbox-group">
      <input type="checkbox" name="services[1][selected]" class="checkbox" id="service2" <?= !empty($svc1['selected']) ? 'checked' : '' ?>>
      <label for="service2" class="service-name">💡 Lighting Design</label>
      <input type="hidden" name="services[1][name]" value="Lighting Design">
    </div>
    <div class="rate-input-group" id="service2Rate" style="<?= !empty($svc1['selected']) ? '' : 'display:none;' ?>">
      <div style="display:flex; gap:15px; align-items:flex-end; flex-wrap:wrap;">
        <div>
          <label style="display:block; margin-bottom:6px; font-size:13px; color:#6b7280; font-weight:500;">Rate Type:</label>
          <select name="services[1][rate_type]" class="form-input" style="padding:8px 12px; font-size:14px; min-width:130px;">
            <option value="hourly" <?= isset($svc1['rate_type']) && $svc1['rate_type'] === 'daily' ? '' : 'selected' ?>>Per Hour</option>
            <option value="daily" <?= isset($svc1['rate_type']) && $svc1['rate_type'] === 'daily' ? 'selected' : '' ?>>Per Day</option>
          </select>
        </div>
        <div>
          <label style="display:block; margin-bottom:6px; font-size:13px; color:#6b7280; font-weight:500;">Rate (Rs):</label>
          <div class="input-wrapper">
            <span class="currency">Rs</span>
            <input type="number" name="services[1][rate]" class="service-rate" min="0" step="1" placeholder="0.00" value="<?= isset($svc1['rate']) ? htmlspecialchars($svc1['rate']) : '' ?>">
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="form-group" id="service2Desc" style="<?= !empty($svc1['selected']) ? '' : 'display:none;' ?>">
    <label class="form-label">Description</label>
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
