<?php $svc5 = $servicesData[5] ?? []; ?>
<div class="service-item">
  <div class="service-header">
    <div class="checkbox-group">
      <input type="checkbox" name="services[5][selected]" class="checkbox" id="service6" <?= !empty($svc5['selected']) ? 'checked' : '' ?>>
      <label for="service6" class="service-name">👗 Costume Design</label>
      <input type="hidden" name="services[5][name]" value="Costume Design">
    </div>
    <div class="rate-input-group" id="service6Rate" style="<?= !empty($svc5['selected']) ? '' : 'display:none;' ?>">
      <div style="display:flex; gap:15px; align-items:flex-end; flex-wrap:wrap;">
        <div>
          <label style="display:block; margin-bottom:6px; font-size:13px; color:#6b7280; font-weight:500;">Rate Type:</label>
          <select name="services[5][rate_type]" class="form-input" style="padding:8px 12px; font-size:14px; min-width:130px;">
            <option value="hourly" <?= isset($svc5['rate_type']) && $svc5['rate_type'] === 'daily' ? '' : 'selected' ?>>Per Hour</option>
            <option value="daily" <?= isset($svc5['rate_type']) && $svc5['rate_type'] === 'daily' ? 'selected' : '' ?>>Per Day</option>
          </select>
        </div>
        <div>
          <label style="display:block; margin-bottom:6px; font-size:13px; color:#6b7280; font-weight:500;">Rate (Rs):</label>
          <div class="input-wrapper">
            <span class="currency">Rs</span>
            <input type="number" name="services[5][rate]" class="service-rate" min="0" step="1" placeholder="0.00" value="<?= isset($svc5['rate']) ? htmlspecialchars($svc5['rate']) : '' ?>">
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="form-group" id="service6Desc" style="<?= !empty($svc5['selected']) ? '' : 'display:none;' ?>">
    <label class="form-label">Description</label>
    <textarea name="services[5][description]" class="form-input textarea" placeholder="Add a description about this service..."><?= isset($svc5['description']) ? htmlspecialchars($svc5['description']) : '' ?></textarea>
  </div>
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
        <input type="text" name="services[5][available_sizes]" class="form-input" placeholder="e.g., XS-XL, kids sizes" value="<?= isset($svc5['available_sizes']) ? htmlspecialchars($svc5['available_sizes']) : '' ?>">
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
        <input type="number" name="services[5][number_of_costumes_available]" class="form-input" min="0" step="1" placeholder="e.g., 50" value="<?= isset($svc5['number_of_costumes_available']) ? htmlspecialchars($svc5['number_of_costumes_available']) : '' ?>">
      </div>
    </div>
  </div>
</div>
