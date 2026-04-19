<?php
$userDisplayName = isset($userDisplayName) && is_string($userDisplayName) && trim($userDisplayName) !== ''
    ? trim($userDisplayName)
    : 'User';
$userDisplayInitial = strtoupper(substr($userDisplayName, 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Service Provider - Rangamadala</title>
    <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/ui-theme.css">
    <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/production_manager/browse_providers.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body class="director-dashboard-page">
    <?php $dramaId = isset($drama->id) ? (int)$drama->id : 0; ?>
    <?php $currentPage = 'manage_services'; require __DIR__ . '/_partials/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Bar -->
        <div class="top-bar">
            <div class="bp-top-left">
                <a href="<?= ROOT ?>/production_manager/manage_services?drama_id=<?= $drama->id ?>" class="bp-back-link">
                    <i class="bx bx-arrow-left"></i>
                </a>
                <div>
                    <h1 class="bp-page-title">Assign Service Provider</h1>
                    <p class="bp-page-subtitle">
                        <?= esc($drama->drama_name) ?> - <?= esc($serviceType) ?>
                    </p>
                </div>
            </div>
            <?php
                $pmProfileImageSrc = isset($profileImageSrc) && is_string($profileImageSrc) && $profileImageSrc !== ''
                    ? $profileImageSrc
                    : (ROOT . '/assets/images/default-avatar.jpg');
                $pmRoleLabel = 'Production Manager';
                $pmProfileUrl = ROOT . '/profile';
                $pmLogoutUrl = ROOT . '/logout';
                require __DIR__ . '/_partials/user_menu.php';
            ?>
        </div>

        <!-- Service Info Banner -->
        <div class="bp-banner">
            <div class="bp-banner-inner">
                <div>
                    <h3 class="bp-banner-title">
                        <i class="bx bx-briefcase"></i> <?= esc($service->service_type) ?>
                    </h3>
                    <p class="bp-banner-text">
                        Select a service provider and send them a request for this service
                    </p>
                </div>
                <div class="bp-budget-side">
                    <div class="bp-budget-label">Budget</div>
                    <div class="bp-budget-value">
                        <?php 
                        $budgetParts = explode('|', $service->budget_range);
                        echo 'LKR ' . number_format((float)str_replace(',', '', trim($budgetParts[0])));
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-wrapper">
            <!-- Filter Sidebar -->
            <aside class="filter-sidebar">
                <h3 class="bp-filter-title">
                    <i class="bx bx-filter"></i> Filters
                </h3>
                
                <form method="GET" action="<?= ROOT ?>/BrowseServiceProviders">
                    <input type="hidden" name="drama_id" value="<?= $drama->id ?>">
                    <input type="hidden" name="service_id" value="<?= $serviceId ?>">
                    <input type="hidden" name="service_type" value="<?= esc($serviceType) ?>">
                    
                    <!-- Location Filter -->
                    <div class="filter-group">
                        <label><i class="bx bx-map-marker-alt"></i> Location</label>
                        <select name="location" class="filter-input">
                            <option value="">All Locations</option>
                            <?php if (!empty($locations)): ?>
                                <?php foreach ($locations as $loc): ?>
                                    <option value="<?= htmlspecialchars($loc->location) ?>" 
                                        <?= ($filters['location'] ?? '') === $loc->location ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($loc->location) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Rate Range Filter -->
                    <div class="filter-group">
                        <label><i class="bx bx-dollar-sign"></i> Hourly Rate (Rs)</label>
                        <div style="display: grid; gap: 8px;">
                            <input type="number" name="min_rate" min="0" step="1" placeholder="Min" class="filter-input" 
                                value="<?= htmlspecialchars($filters['min_rate'] ?? '') ?>">
                            <input type="number" name="max_rate" min="0" step="1" placeholder="Max" class="filter-input" 
                                value="<?= htmlspecialchars($filters['max_rate'] ?? '') ?>">
                        </div>
                    </div>

                    <!-- Availability Filter -->
                    <div class="filter-group">
                        <label class="bp-availability-label">
                            <input type="checkbox" name="availability" value="1" 
                                <?= !empty($filters['availability']) ? 'checked' : '' ?>>
                            <span><i class="bx bx-calendar-check"></i> Available Now Only</span>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary bp-btn-full bp-btn-filter">
                        <i class="bx bx-search"></i> Apply Filters
                    </button>
                    
                    <a href="<?= ROOT ?>/BrowseServiceProviders?drama_id=<?= $drama->id ?>&service_id=<?= $serviceId ?>&service_type=<?= urlencode($serviceType) ?>" 
                       class="btn btn-secondary bp-btn-full bp-clear-link">
                        Clear All
                    </a>
                </form>
            </aside>

            <!-- Providers Grid -->
            <div>
                <div class="bp-result-count">
                    <i class="bx bx-user-tie"></i> 
                    <?= count($providers) ?> service provider<?= count($providers) !== 1 ? 's' : '' ?> found
                </div>

                <?php if (!empty($providers)): ?>
                    <div class="provider-grid">
                        <?php foreach ($providers as $provider): ?>
                            <div class="provider-card">
                                <div class="provider-header">
                                    <div class="provider-avatar">
                                        <?= strtoupper(substr($provider->full_name ?? 'P', 0, 1)) ?>
                                    </div>
                                    <div class="provider-info bp-provider-info">
                                        <h3><?= esc($provider->full_name) ?></h3>
                                        <p><?= esc($provider->professional_title ?? 'Service Provider') ?></p>
                                        <?php if (!empty($provider->location)): ?>
                                            <p class="bp-location-row">
                                                <i class="bx bx-map-marker-alt"></i> <?= esc($provider->location) ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="provider-stats">
                                    <div class="stat-item">
                                        <span class="stat-label">Experience</span>
                                        <span class="stat-value">
                                            <?= $provider->years_experience ?? 0 ?> years
                                        </span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">Status</span>
                                        <span class="stat-value bp-availability<?= ($provider->availability ?? '') === 'available' ? ' bp-availability--available' : '' ?>">
                                            <?= ucfirst($provider->availability ?? 'N/A') ?>
                                        </span>
                                    </div>
                                </div>

                                <?php if (!empty($provider->professional_summary)): ?>
                                    <div class="bp-summary">
                                        <?= nl2br(esc(substr($provider->professional_summary, 0, 120))) ?>
                                        <?= strlen($provider->professional_summary) > 120 ? '...' : '' ?>
                                    </div>
                                <?php endif; ?>

                                <div class="provider-services">
                                    <?php 
                                    // Get provider services
                                    $providerServices = [];
                                    if (!empty($provider->services)) {
                                        $services = is_string($provider->services) ? explode(',', $provider->services) : [$provider->services];
                                        foreach ($services as $svc): 
                                            if (!empty(trim($svc))):
                                    ?>
                                        <span class="service-tag"><?= esc(trim($svc)) ?></span>
                                    <?php 
                                            endif;
                                        endforeach;
                                    }
                                    ?>
                                </div>

                                <div class="bp-actions">
                                    <button class="btn btn-secondary bp-btn-grow" onclick="viewProviderProfile(<?= $provider->user_id ?>)">
                                        <i class="bx bx-eye"></i> View Profile
                                    </button>
                                    <button class="btn btn-primary bp-btn-grow" onclick="sendServiceRequest(<?= $provider->user_id ?>, '<?= esc($provider->full_name) ?>')">
                                        <i class="bx bx-paper-plane"></i> Send Request
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="bp-empty">
                        <i class="bx bx-users-slash bp-empty-icon"></i>
                        <h3 class="bp-empty-title">No Service Providers Found</h3>
                        <p class="bp-empty-text">
                            Try adjusting your filters or search criteria
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script>
        const serviceId = <?= $serviceId ?>;
        const dramaId = <?= $drama->id ?>;

        function viewProviderProfile(providerId) {
            window.open('<?= ROOT ?>/BrowseServiceProviders/viewProfile/' + providerId, '_blank');
        }

        function sendServiceRequest(providerId, providerName) {
            if (!confirm(`Send service request to ${providerName}?`)) {
                return;
            }

            fetch('<?= ROOT ?>/production_manager/send_service_request', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    service_id: serviceId,
                    provider_id: providerId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Service request sent successfully!\n\nThe service provider will be notified and can accept or reject the request.');
                    window.location.href = '<?= ROOT ?>/production_manager/manage_services?drama_id=' + dramaId;
                } else {
                    alert('Error: ' + (data.message || 'Failed to send request'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while sending the request. Please try again.');
            });
        }
    </script>
    <script src="/Rangamadala/public/assets/JS/director-user-menu.js"></script>
</body>
</html>
