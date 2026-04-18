<?php $svc6 = $servicesData[6] ?? []; ?>
<div class="service-item">
  <div class="service-header">
    <div class="checkbox-group">
      <input type="checkbox" name="services[6][selected]" class="checkbox" id="service7" <?= !empty($svc6['selected']) ? 'checked' : '' ?>>
      <label for="service7" class="service-name">💄 Makeup & Hair</label>
      <input type="hidden" name="services[6][name]" value="Makeup & Hair">
    </div>
    <div class="rate-input-group" id="service7Rate" style="<?= !empty($svc6['selected']) ? '' : 'display:none;' ?>">
      <div style="display:flex; gap:15px; align-items:flex-end; flex-wrap:wrap;">
        <div>
          <label style="display:block; margin-bottom:6px; font-size:13px; color:#6b7280; font-weight:500;">Rate Type:</label>
          <select name="services[6][rate_type]" class="form-input" style="padding:8px 12px; font-size:14px; min-width:130px;">
            <option value="hourly" <?= isset($svc6['rate_type']) && $svc6['rate_type'] === 'daily' ? '' : 'selected' ?>>Per Hour</option>
            <option value="daily" <?= isset($svc6['rate_type']) && $svc6['rate_type'] === 'daily' ? 'selected' : '' ?>>Per Day</option>
          </select>
        </div>
        <div>
          <label style="display:block; margin-bottom:6px; font-size:13px; color:#6b7280; font-weight:500;">Rate (Rs):</label>
          <div class="input-wrapper">
            <span class="currency">Rs</span>
            <input type="number" name="services[6][rate]" class="service-rate" min="0" step="1" placeholder="0.00" value="<?= isset($svc6['rate']) ? htmlspecialchars($svc6['rate']) : '' ?>">
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="form-group" id="service7Desc" style="<?= !empty($svc6['selected']) ? '' : 'display:none;' ?>">
    <label class="form-label">Description</label>
    <textarea name="services[6][description]" class="form-input textarea" placeholder="Add a description about this service..."><?= isset($svc6['description']) ? htmlspecialchars($svc6['description']) : '' ?></textarea>
  </div>
  <div class="service-details" id="service7Details" style="<?= !empty($svc6['selected']) ? '' : 'display:none;' ?>">
    <div class="form-group">
      <label class="form-label">Type of Make-up Services</label>
      <textarea name="services[6][type_of_makeup_services]" class="form-input textarea" placeholder="Describe the makeup services (e.g., bridal, stage, character)"><?= isset($svc6['type_of_makeup_services']) ? htmlspecialchars($svc6['type_of_makeup_services']) : '' ?></textarea>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Experience in Stage Make-up (years)</label>
        <input type="number" name="services[6][experience_stage_makeup_years]" class="form-input" min="0" step="1" placeholder="e.g., 5" value="<?= isset($svc6['experience_stage_makeup_years']) ? htmlspecialchars($svc6['experience_stage_makeup_years']) : '' ?>">
      </div>
      <div class="form-group">
        <label class="form-label">Maximum Actors Per Show</label>
        <input type="number" name="services[6][maximum_actors_per_show]" class="form-input" min="0" step="1" placeholder="e.g., 50" value="<?= isset($svc6['maximum_actors_per_show']) ? htmlspecialchars($svc6['maximum_actors_per_show']) : '' ?>">
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
