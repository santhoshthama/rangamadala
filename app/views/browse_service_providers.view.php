<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Service Providers - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/browse_service_providers.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="<?= ROOT ?>/assets/images/Rangamadala logo.png" type="image/x-icon">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <div class="page-header">
            <h1>Browse Service Providers</h1>
            <p>Find the perfect professional for your drama production needs</p>
        </div>

        <div class="content-wrapper">
            <!-- Filter Sidebar -->
            <aside class="filter-sidebar">
                <div class="filter-card">
                    <h3><i class="fas fa-filter"></i> Filters</h3>
                    
                    <form method="GET" action="<?= ROOT ?>/BrowseServiceProviders">
                        <!-- Service Type Filter -->
                        <div class="filter-group">
                            <label><i class="fas fa-briefcase"></i> Service Type</label>
                            <select name="service_type" class="filter-input">
                                <option value="">All Services</option>
                                <option value="Theater Production" <?= ($data['filters']['service_type'] ?? '') === 'Theater Production' ? 'selected' : '' ?>>Theater Production</option>
                                <option value="Lighting Design" <?= ($data['filters']['service_type'] ?? '') === 'Lighting Design' ? 'selected' : '' ?>>Lighting Design</option>
                                <option value="Sound Systems" <?= ($data['filters']['service_type'] ?? '') === 'Sound Systems' ? 'selected' : '' ?>>Sound Systems</option>
                                <option value="Video Production" <?= ($data['filters']['service_type'] ?? '') === 'Video Production' ? 'selected' : '' ?>>Video Production</option>
                                <option value="Set Design" <?= ($data['filters']['service_type'] ?? '') === 'Set Design' ? 'selected' : '' ?>>Set Design</option>
                                <option value="Costume Design" <?= ($data['filters']['service_type'] ?? '') === 'Costume Design' ? 'selected' : '' ?>>Costume Design</option>
                                <option value="Makeup &amp; Hair" <?= ($data['filters']['service_type'] ?? '') === 'Makeup &amp; Hair' ? 'selected' : '' ?>>Makeup &amp; Hair</option>
                                <option value="Other" <?= ($data['filters']['service_type'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>

                        <!-- Location Filter -->
                        <div class="filter-group">
                            <label><i class="fas fa-map-marker-alt"></i> Location</label>
                            <select name="location" class="filter-input">
                                <option value="">All Locations</option>
                                <?php foreach ($data['locations'] as $loc): ?>
                                    <option value="<?= htmlspecialchars($loc->location) ?>" <?= ($data['filters']['location'] ?? '') === $loc->location ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($loc->location) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Rate Range Filter -->
                        <div class="filter-group">
                            <label><i class="fas fa-dollar-sign"></i> Hourly Rate (Rs)</label>
                            <div class="rate-range">
                                <input type="number" name="min_rate" placeholder="Min" class="filter-input" value="<?= htmlspecialchars($data['filters']['min_rate'] ?? '') ?>">
                                <span>to</span>
                                <input type="number" name="max_rate" placeholder="Max" class="filter-input" value="<?= htmlspecialchars($data['filters']['max_rate'] ?? '') ?>">
                            </div>
                        </div>

                        <!-- Availability Filter -->
                        <div class="filter-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="availability" value="1" <?= !empty($data['filters']['availability']) ? 'checked' : '' ?>>
                                <span><i class="fas fa-calendar-check"></i> Available Now Only</span>
                            </label>
                        </div>

                        <button type="submit" class="btn-filter"><i class="fas fa-search"></i> Apply Filters</button>
                        <a href="<?= ROOT ?>/BrowseServiceProviders" class="btn-clear">Clear All</a>
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
                            <i class="fas fa-search" style="font-size: 64px; color: #ccc; margin-bottom: 20px;"></i>
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
                                        <span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($provider->location) ?></span>
                                        <span><i class="fas fa-briefcase"></i> <?= (int)$provider->years_experience ?> years exp.</span>
                                    </div>

                                    <?php if (!empty($provider->services)): ?>
                                        <div class="provider-services">
                                            <div class="services-label"><i class="fas fa-tools"></i> Services</div>
                                            <p class="services-text"><?= htmlspecialchars($provider->services) ?></p>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($provider->rates)): ?>
                                        <div class="provider-rate">
                                            <span class="rate-label">From</span>
                                            <span class="rate-value">Rs <?= number_format(min(array_map('floatval', explode(',', $provider->rates)))) ?>/hr</span>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="provider-footer">
                                    <a href="<?= ROOT ?>/BrowseServiceProviders/viewProfile/<?= $provider->user_id ?>" class="btn-view-profile">
                                        View Profile <i class="fas fa-arrow-right"></i>
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
