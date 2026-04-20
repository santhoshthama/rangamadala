<!-- Location Filter -->
                        <div class="filter-group">
                            <label><i class="bx bx-map"></i> Location</label>
                            <select name="location" class="filter-input">
                                <option value="">All Locations</option>
                                <?php foreach ($data['locations'] as $loc): ?>
                                    <option value="<?= htmlspecialchars($loc->location) ?>" <?= ($data['filters']['location'] ?? '') === $loc->location ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($loc->location) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

<!-- in service provider detail view page-->
                                <label>Location</label>
                                <!-- Location is displayed here so visitors can see where the provider operates. -->
                                <span><?= htmlspecialchars($data['provider']->location) ?></span>

<!-- in service provider registration page -->
                  <label class="form-label">Location</label>
                  <input type="text" name="location" class="form-input" placeholder="City, Country" value="<?= htmlspecialchars($formData['location'] ?? '') ?>">