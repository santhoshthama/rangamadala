<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Service Provider - Rangamadala</title>
    <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/ui-theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .provider-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 24px;
            margin-top: 24px;
        }
        
        .provider-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .provider-card:hover {
            box-shadow: 0 4px 16px rgba(0,0,0,0.12);
            transform: translateY(-2px);
        }
        
        .provider-header {
            display: flex;
            gap: 16px;
            margin-bottom: 16px;
        }
        
        .provider-avatar {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: bold;
        }
        
        .provider-info h3 {
            margin: 0 0 4px 0;
            color: var(--ink);
            font-size: 18px;
        }
        
        .provider-info p {
            margin: 0;
            color: var(--muted);
            font-size: 13px;
        }
        
        .provider-stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin: 16px 0;
            padding: 16px;
            background: var(--bg-soft);
            border-radius: 8px;
        }
        
        .stat-item {
            display: flex;
            flex-direction: column;
        }
        
        .stat-label {
            font-size: 11px;
            color: var(--muted);
            text-transform: uppercase;
            font-weight: 700;
            margin-bottom: 4px;
        }
        
        .stat-value {
            font-size: 16px;
            color: var(--ink);
            font-weight: 600;
        }
        
        .provider-services {
            margin: 16px 0;
        }
        
        .service-tag {
            display: inline-block;
            padding: 4px 12px;
            background: var(--primary)15;
            color: var(--primary);
            border-radius: 16px;
            font-size: 12px;
            margin: 4px 4px 4px 0;
        }
        
        .filter-sidebar {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            position: sticky;
            top: 20px;
        }
        
        .filter-group {
            margin-bottom: 20px;
        }
        
        .filter-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--ink);
            font-weight: 600;
            font-size: 13px;
        }
        
        .filter-input {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 14px;
        }
        
        .content-wrapper {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 24px;
            margin-top: 24px;
        }
        
        @media (max-width: 968px) {
            .content-wrapper {
                grid-template-columns: 1fr;
            }
            
            .filter-sidebar {
                position: relative;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="logo">
            <h2>🎭</h2>
        </div>
        <ul class="menu">
            <li>
                <a href="<?= ROOT ?>/production_manager/dashboard?drama_id=<?= $drama->id ?>">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="active">
                <a href="<?= ROOT ?>/production_manager/manage_services?drama_id=<?= $drama->id ?>">
                    <i class="fas fa-briefcase"></i>
                    <span>Manage Services</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/production_manager/manage_budget?drama_id=<?= $drama->id ?>">
                    <i class="fas fa-chart-bar"></i>
                    <span>Budget Management</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/production_manager/book_theater?drama_id=<?= $drama->id ?>">
                    <i class="fas fa-theater-masks"></i>
                    <span>Theater Bookings</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/artistdashboard">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Profile</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Bar -->
        <div class="top-bar">
            <div style="display: flex; align-items: center; gap: 12px;">
                <a href="<?= ROOT ?>/production_manager/manage_services?drama_id=<?= $drama->id ?>" style="color: var(--muted); font-size: 20px;">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 style="margin: 0; font-size: 24px; color: var(--ink);">Assign Service Provider</h1>
                    <p style="margin: 4px 0 0 0; color: var(--muted); font-size: 14px;">
                        <?= esc($drama->drama_name) ?> - <?= esc($serviceType) ?>
                    </p>
                </div>
            </div>
            <div class="user-menu">
                <span><?= $_SESSION['full_name'] ?? 'User' ?></span>
                <div class="avatar"><?= strtoupper(substr($_SESSION['full_name'] ?? 'U', 0, 1)) ?></div>
            </div>
        </div>

        <!-- Service Info Banner -->
        <div style="background: linear-gradient(135deg, var(--primary), var(--accent)); color: white; padding: 24px; border-radius: 12px; margin-bottom: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h3 style="margin: 0 0 8px 0; font-size: 20px;">
                        <i class="fas fa-briefcase"></i> <?= esc($service->service_type) ?>
                    </h3>
                    <p style="margin: 0; opacity: 0.9;">
                        Select a service provider and send them a request for this service
                    </p>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 12px; opacity: 0.8; margin-bottom: 4px;">Budget</div>
                    <div style="font-size: 24px; font-weight: bold;">
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
                <h3 style="margin: 0 0 20px 0; color: var(--ink);">
                    <i class="fas fa-filter"></i> Filters
                </h3>
                
                <form method="GET" action="<?= ROOT ?>/production_manager/browse_providers">
                    <input type="hidden" name="drama_id" value="<?= $drama->id ?>">
                    <input type="hidden" name="service_id" value="<?= $serviceId ?>">
                    <input type="hidden" name="service_type" value="<?= esc($serviceType) ?>">
                    
                    <!-- Location Filter -->
                    <div class="filter-group">
                        <label><i class="fas fa-map-marker-alt"></i> Location</label>
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
                        <label><i class="fas fa-dollar-sign"></i> Hourly Rate (Rs)</label>
                        <div style="display: grid; gap: 8px;">
                            <input type="number" name="min_rate" placeholder="Min" class="filter-input" 
                                value="<?= htmlspecialchars($filters['min_rate'] ?? '') ?>">
                            <input type="number" name="max_rate" placeholder="Max" class="filter-input" 
                                value="<?= htmlspecialchars($filters['max_rate'] ?? '') ?>">
                        </div>
                    </div>

                    <!-- Availability Filter -->
                    <div class="filter-group">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: normal;">
                            <input type="checkbox" name="availability" value="1" 
                                <?= !empty($filters['availability']) ? 'checked' : '' ?>>
                            <span><i class="fas fa-calendar-check"></i> Available Now Only</span>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; margin-bottom: 12px;">
                        <i class="fas fa-search"></i> Apply Filters
                    </button>
                    
                    <a href="<?= ROOT ?>/production_manager/browse_providers?drama_id=<?= $drama->id ?>&service_id=<?= $serviceId ?>&service_type=<?= urlencode($serviceType) ?>" 
                       class="btn btn-secondary" style="width: 100%; text-align: center; display: block; text-decoration: none;">
                        Clear All
                    </a>
                </form>
            </aside>

            <!-- Providers Grid -->
            <div>
                <div style="margin-bottom: 20px; color: var(--muted);">
                    <i class="fas fa-user-tie"></i> 
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
                                    <div class="provider-info" style="flex: 1;">
                                        <h3><?= esc($provider->full_name) ?></h3>
                                        <p><?= esc($provider->professional_title ?? 'Service Provider') ?></p>
                                        <?php if (!empty($provider->location)): ?>
                                            <p style="margin-top: 4px;">
                                                <i class="fas fa-map-marker-alt"></i> <?= esc($provider->location) ?>
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
                                        <span class="stat-value" style="color: <?= $provider->availability === 'available' ? 'var(--success)' : 'var(--warning)' ?>;">
                                            <?= ucfirst($provider->availability ?? 'N/A') ?>
                                        </span>
                                    </div>
                                </div>

                                <?php if (!empty($provider->professional_summary)): ?>
                                    <div style="margin: 16px 0; color: var(--ink); font-size: 13px; line-height: 1.5;">
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

                                <div style="margin-top: 16px; display: flex; gap: 8px;">
                                    <button class="btn btn-secondary" style="flex: 1;" onclick="viewProviderProfile(<?= $provider->user_id ?>)">
                                        <i class="fas fa-eye"></i> View Profile
                                    </button>
                                    <button class="btn btn-primary" style="flex: 1;" onclick="sendServiceRequest(<?= $provider->user_id ?>, '<?= esc($provider->full_name) ?>')">
                                        <i class="fas fa-paper-plane"></i> Send Request
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 80px 20px; background: white; border-radius: 12px;">
                        <i class="fas fa-users-slash" style="font-size: 64px; color: var(--muted); opacity: 0.3; margin-bottom: 20px;"></i>
                        <h3 style="color: var(--ink); margin-bottom: 8px;">No Service Providers Found</h3>
                        <p style="color: var(--muted); margin-bottom: 24px;">
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

            fetch('/Rangamadala/public/production_manager/send_service_request', {
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
</body>
</html>
