<?php $svc0 = $servicesData[0] ?? []; ?>
<div class="service-item">
  <div class="service-header">
    <div class="checkbox-group">
      <input type="checkbox" name="services[0][selected]" class="checkbox" id="service1" <?= !empty($svc0['selected']) ? 'checked' : '' ?>>
      <label for="service1" class="service-name">🎭 Theater Production</label>
      <input type="hidden" name="services[0][name]" value="Theater Production">
    </div>
    <div class="rate-input-group" id="service1Rate" style="<?= !empty($svc0['selected']) ? '' : 'display:none;' ?>">
      <div style="display:flex; gap:15px; align-items:flex-end; flex-wrap:wrap;">
        <div>
          <label style="display:block; margin-bottom:6px; font-size:13px; color:#6b7280; font-weight:500;">Rate Type:</label>
          <select name="services[0][rate_type]" class="form-input" style="padding:8px 12px; font-size:14px; min-width:130px;">
            <option value="hourly" <?= isset($svc0['rate_type']) && $svc0['rate_type'] === 'daily' ? '' : 'selected' ?>>Per Hour</option>
            <option value="daily" <?= isset($svc0['rate_type']) && $svc0['rate_type'] === 'daily' ? 'selected' : '' ?>>Per Day</option>
          </select>
        </div>
        <div>
          <label style="display:block; margin-bottom:6px; font-size:13px; color:#6b7280; font-weight:500;">Rate (Rs):</label>
          <div class="input-wrapper">
            <span class="currency">Rs</span>
            <input type="number" name="services[0][rate]" class="service-rate" min="0" step="1" placeholder="0.00" value="<?= isset($svc0['rate']) ? htmlspecialchars($svc0['rate']) : '' ?>">
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="form-group" id="service1Desc" style="<?= !empty($svc0['selected']) ? '' : 'display:none;' ?>">
    <label class="form-label">Description</label>
    <textarea name="services[0][description]" class="form-input textarea" placeholder="Add a description about this service..."><?= isset($svc0['description']) ? htmlspecialchars($svc0['description']) : '' ?></textarea>
  </div>
  <div class="service-details" id="service1Details" style="<?= !empty($svc0['selected']) ? '' : 'display:none;' ?>">
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Theatre Name</label>
        <input type="text" name="services[0][theatre_name]" class="form-input" placeholder="e.g., City Hall Theatre" value="<?= isset($svc0['theatre_name']) ? htmlspecialchars($svc0['theatre_name']) : '' ?>">
      </div>
      <div class="form-group">
        <label class="form-label">Seating Capacity</label>
        <input type="number" name="services[0][seating_capacity]" class="form-input" min="0" step="1" placeholder="e.g., 500" value="<?= isset($svc0['seating_capacity']) ? htmlspecialchars($svc0['seating_capacity']) : '' ?>">
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
