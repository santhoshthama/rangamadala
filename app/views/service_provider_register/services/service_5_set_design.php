<?php $svc4 = $servicesData[4] ?? []; ?>
<div class="service-item">
  <div class="service-header">
    <div class="checkbox-group">
      <input type="checkbox" name="services[4][selected]" class="checkbox" id="service5" <?= !empty($svc4['selected']) ? 'checked' : '' ?>>
      <label for="service5" class="service-name">🎨 Set Design</label>
      <input type="hidden" name="services[4][name]" value="Set Design">
    </div>
    <div class="rate-input-group" id="service5Rate" style="<?= !empty($svc4['selected']) ? '' : 'display:none;' ?>">
      <div style="display:flex; gap:15px; align-items:flex-end; flex-wrap:wrap;">
        <div>
          <label style="display:block; margin-bottom:6px; font-size:13px; color:#6b7280; font-weight:500;">Rate Type:</label>
          <select name="services[4][rate_type]" class="form-input" style="padding:8px 12px; font-size:14px; min-width:130px;">
            <option value="hourly" <?= isset($svc4['rate_type']) && $svc4['rate_type'] === 'daily' ? '' : 'selected' ?>>Per Hour</option>
            <option value="daily" <?= isset($svc4['rate_type']) && $svc4['rate_type'] === 'daily' ? 'selected' : '' ?>>Per Day</option>
          </select>
        </div>
        <div>
          <label style="display:block; margin-bottom:6px; font-size:13px; color:#6b7280; font-weight:500;">Rate (Rs):</label>
          <div class="input-wrapper">
            <span class="currency">Rs</span>
            <input type="number" name="services[4][rate]" class="service-rate" min="0" step="1" placeholder="0.00" value="<?= isset($svc4['rate']) ? htmlspecialchars($svc4['rate']) : '' ?>">
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="form-group" id="service5Desc" style="<?= !empty($svc4['selected']) ? '' : 'display:none;' ?>">
    <label class="form-label">Description</label>
    <textarea name="services[4][description]" class="form-input textarea" placeholder="Add a description about this service..."><?= isset($svc4['description']) ? htmlspecialchars($svc4['description']) : '' ?></textarea>
  </div>
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
