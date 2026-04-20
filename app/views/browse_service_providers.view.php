<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Service Providers - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/browse_service_providers.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="shortcut icon" href="<?= ROOT ?>/assets/images/Rangamadala logo.png" type="image/x-icon">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <?php
            $currentDramaId = (int)($data['drama_id'] ?? ($_GET['drama_id'] ?? 0));
            $currentServiceId = (int)($_GET['service_id'] ?? 0);

            $backToManageServices = ROOT . '/production_manager/manage_services';
            if ($currentDramaId > 0) {
                $backToManageServices .= '?drama_id=' . $currentDramaId;
            }

            $clearFiltersUrl = ROOT . '/BrowseServiceProviders';
            $clearParams = [];
            if ($currentDramaId > 0) {
                $clearParams['drama_id'] = $currentDramaId;
            }
            if ($currentServiceId > 0) {
                $clearParams['service_id'] = $currentServiceId;
            }
            if (!empty($clearParams)) {
                $clearFiltersUrl .= '?' . http_build_query($clearParams);
            }
        ?>
        <a href="<?= $backToManageServices ?>" class="back-link">
            <i class="bx bx-arrow-back"></i>
            Back to Manage Services
        </a>

        <div class="page-header">
            <h1>Browse Service Providers</h1>
            <p>Find the perfect professional for your drama production needs</p>
        </div>

        <div class="content-wrapper">
            <!-- Filter Sidebar -->
            <aside class="filter-sidebar">
                <div class="filter-card">
                    <h3><i class="bx bx-filter"></i> Filters</h3>
                    
                    <form method="GET" action="<?= ROOT ?>/BrowseServiceProviders">
                        <?php if ($currentDramaId > 0): ?>
                            <input type="hidden" name="drama_id" value="<?= $currentDramaId ?>">
                        <?php endif; ?>
                        <?php if ($currentServiceId > 0): ?>
                            <input type="hidden" name="service_id" value="<?= $currentServiceId ?>">
                        <?php endif; ?>

                        <!-- Service Type Filter -->
                        <div class="filter-group">
                            <label><i class="bx bx-briefcase"></i> Service Type</label>
                            <select name="service_type" class="filter-input">
                                <option value="">All Services</option>
                                <option value="Theater Production" <?= ($data['filters']['service_type'] ?? '') === 'Theater Production' ? 'selected' : '' ?>>Theater Production</option>
                                <option value="Lighting Design" <?= ($data['filters']['service_type'] ?? '') === 'Lighting Design' ? 'selected' : '' ?>>Lighting Design</option>
                                <option value="Sound Systems" <?= ($data['filters']['service_type'] ?? '') === 'Sound Systems' ? 'selected' : '' ?>>Sound Systems</option>
                                <option value="Video Production" <?= ($data['filters']['service_type'] ?? '') === 'Video Production' ? 'selected' : '' ?>>Video Production</option>
                                <option value="Set Design" <?= ($data['filters']['service_type'] ?? '') === 'Set Design' ? 'selected' : '' ?>>Set Design</option>
                                <option value="Costume Design" <?= ($data['filters']['service_type'] ?? '') === 'Costume Design' ? 'selected' : '' ?>>Costume Design</option>
                                <option value="Makeup &amp; Hair" <?= ($data['filters']['service_type'] ?? '') === 'Makeup & Hair' ? 'selected' : '' ?>>Makeup &amp; Hair</option>
                                <option value="Other" <?= ($data['filters']['service_type'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>

                        <!-- Rate Range Filter -->
                        <div class="filter-group">
                            <label><i class="bx bx-dollar"></i> Hourly Rate (Rs)</label>
                            <div class="rate-range">
                                <input type="number" name="min_rate" min="0" step="1" placeholder="Min" class="filter-input" value="<?= htmlspecialchars($data['filters']['min_rate'] ?? '') ?>">
                                <span>to</span>
                                <input type="number" name="max_rate" min="0" step="1" placeholder="Max" class="filter-input" value="<?= htmlspecialchars($data['filters']['max_rate'] ?? '') ?>">
                            </div>
                        </div>

                        <!-- Availability Filter -->
                        <div class="filter-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="availability" value="1" <?= !empty($data['filters']['availability']) ? 'checked' : '' ?>>
                                <span><i class="bx bx-calendar-check"></i> Available Now Only</span>
                            </label>
                        </div>

                        <button type="submit" class="btn-filter"><i class="bx bx-search"></i> Apply Filters</button>
                        <a href="<?= $clearFiltersUrl ?>" class="btn-clear">Clear All</a>
                    </form>
                </div>
            </aside>

            <!-- Providers Grid -->
            <main class="providers-content">
                <div class="results-info">
                    <span><?= count($data['providers']) ?> Provider(s) Found</span>
                </div>

                <div class="providers-grid">
                    <?php if (empty($data['providers'])): ?>
                        <div class="no-results">
                            <i class="bx bx-search" style="font-size: 64px; color: #ccc; margin-bottom: 20px;"></i>
                            <h3>No service providers found</h3>
                            <p>Try adjusting your filters or browse all providers</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($data['providers'] as $provider): ?>
                            <div class="provider-card">
                                <?php 
                                // Determine service image
                                $services = !empty($provider->services) ? explode(', ', $provider->services) : [];
                                $serviceCount = count($services);
                                
                                $serviceIcons = [
                                    'Theater Production' => 'theater-production.png',
                                    'Lighting Design' => 'lighting-design.png',
                                    'Sound Systems' => 'sound-systems.png',
                                    'Video Production' => 'video-production.png',
                                    'Set Design' => 'set-design.png',
                                    'Costume Design' => 'costume-design.png',
                                    'Makeup' => 'makeup.png',
                                    'Makeup & Hair' => 'makeup.png'
                                ];
                                
                                // Use multi-service image if provider offers more than one service
                                if ($serviceCount > 1) {
                                    $serviceImage = 'multi-services.png';
                                    $serviceLabel = 'Multiple Services';
                                } elseif ($serviceCount === 1) {
                                    $firstService = trim($services[0]);
                                    $serviceImage = $serviceIcons[$firstService] ?? 'default-service.png';
                                    $serviceLabel = $firstService;
                                } else {
                                    $serviceImage = 'default-service.png';
                                    $serviceLabel = 'Service Provider';
                                }
                                ?>
                                
                                <div class="provider-service-banner">
                                     <img src="<?= ROOT ?>/assets/images/services/<?= $serviceImage ?>" 
                                         alt="<?= htmlspecialchars($serviceLabel) ?>" 
                                         onerror="this.src='<?= ROOT ?>/assets/images/services/default-avatar.png'">
                                    
                                    <?php if ($provider->availability == 1): ?>
                                        <span class="badge-available">Available</span>
                                    <?php endif; ?>
                                </div>

                                <div class="provider-body">
                                    <h3><?= htmlspecialchars($provider->full_name) ?></h3>
                                    <p class="provider-title"><?= htmlspecialchars($provider->professional_title) ?></p>
                                    
                                    <div class="provider-meta">
                                        <span><i class="bx bx-map"></i> <?= htmlspecialchars($provider->location) ?></span>
                                        <span><i class="bx bx-briefcase"></i> <?= (int)$provider->years_experience ?> years exp.</span>
                                    </div>

                                    <?php if (!empty($provider->services)): ?>
                                        <div class="provider-services">
                                            <div class="services-label"><i class="bx bx-wrench"></i> Services</div>
                                            <p class="services-text"><?= htmlspecialchars($provider->services) ?></p>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($provider->rates)): ?>
                                        <?php
                                        $rateItems = array_filter(array_map('trim', explode(',', (string)$provider->rates)), static fn($v) => $v !== '');
                                        $numericRates = [];
                                        foreach ($rateItems as $rateItem) {
                                            $parts = explode('|', $rateItem);
                                            $rawRate = trim((string)($parts[0] ?? ''));
                                            if ($rawRate !== '' && is_numeric($rawRate)) {
                                                $numericRates[] = (float)$rawRate;
                                            }
                                        }
                                        ?>
                                        <?php if (!empty($numericRates)): ?>
                                            <div class="provider-rate">
                                                <span class="rate-label">From</span>
                                                <span class="rate-value">Rs <?= number_format(min($numericRates), 0) ?>/hr</span>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>

                                <div class="provider-footer">
                                    <?php
                                        $profileParams = [];
                                        if (!empty($data['drama_id'])) {
                                            $profileParams['drama_id'] = (int)$data['drama_id'];
                                        }
                                        if (!empty($data['service_id'])) {
                                            $profileParams['service_id'] = (int)$data['service_id'];
                                        }
                                        $profileUrl = ROOT . '/BrowseServiceProviders/viewProfile/' . (int)$provider->user_id;
                                        if (!empty($profileParams)) {
                                            $profileUrl .= '?' . http_build_query($profileParams);
                                        }
                                    ?>
                                    <a href="<?= $profileUrl ?>" class="btn-view-profile">
                                        View Profile <i class="bx bx-right-arrow-alt"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
