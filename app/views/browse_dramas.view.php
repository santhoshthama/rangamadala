<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Dramas - <?= APP_NAME ?></title>
    <link rel="shortcut icon" href="<?= ROOT ?>/assets/images/Rangamadala logo.png" type="image/x-icon">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/browse_dramas.css">
</head>
<body>

<div class="container">
    <!-- Header -->
    <div class="header">
        <div class="back-container">
            <a href="<?= ROOT ?>/Audiencedashboard" class="back-link">
                <button class="back-btn" type="button">
                    <i class='bx bx-arrow-back'></i> Back to Dashboard
                </button>
            </a>
        </div>
        <h1 class="page-title">Browse Dramas</h1>
        <p class="subtitle">Discover amazing theatrical performances</p>
    </div>

    <!-- Search & Filter -->
    <div class="search-filter-container">
        <form method="GET" class="search-form">
            <div class="search-box">
                <i class='bx bx-search'></i>
                <input type="text" name="search" placeholder="Search dramas..." value="<?= htmlspecialchars($data['search']) ?>">
            </div>

            <div class="filter-box">
                <i class='bx bx-sort'></i>
                <select name="sort">
                    <?php $sort = $data['sort'] ?? 'latest'; ?>
                    <option value="latest" <?= $sort==='latest'?'selected':'' ?>>Latest</option>
                    <option value="name_asc" <?= $sort==='name_asc'?'selected':'' ?>>Name: A → Z</option>
                    <option value="name_desc" <?= $sort==='name_desc'?'selected':'' ?>>Name: Z → A</option>
                </select>
            </div>

            <div class="filter-box">
                <i class='bx bx-list-check'></i>
                <select name="per_page">
                    <?php $per = isset($data['per_page']) ? (int)$data['per_page'] : 12; ?>
                    <?php foreach ([8,12,16,20,24,32,40,50] as $opt): ?>
                        <option value="<?= $opt ?>" <?= $per===$opt?'selected':'' ?>><?= $opt ?> per page</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn-search">Search</button>
            <a href="<?= ROOT ?>/BrowseDramas" class="btn-reset">Clear</a>
        </form>
    </div>

    <!-- Results Count -->
    <div class="results-info">
        <p>Found <strong><?= $data['total_dramas'] ?></strong> drama(s)</p>
    </div>

    <!-- Dramas Grid -->
    <div class="dramas-grid">
        <?php if (!empty($data['dramas'])): ?>
            <?php foreach ($data['dramas'] as $drama): ?>
                <div class="drama-card">
                    <div class="drama-image">
                        <?php 
                            $certificateFile = $drama->certificate_image ?? '';
                            $extension = strtolower(pathinfo($certificateFile, PATHINFO_EXTENSION));
                            $isImage = !empty($certificateFile) && in_array($extension, ['jpg','jpeg','png','gif','webp']);
                        ?>
                        <?php if ($isImage): ?>
                            <img src="<?= ROOT ?>/uploads/certificates/<?= htmlspecialchars(rawurlencode($certificateFile)) ?>" alt="Certificate for <?= htmlspecialchars($drama->drama_name ?? 'Drama') ?>" onerror="this.onerror=null;this.parentElement.innerHTML='<div class=\'placeholder-image\'><i class=\'bx bx-id-card\'></i></div>';">
                        <?php else: ?>
                            <div class="placeholder-image">
                                <i class='bx bx-id-card'></i>
                            </div>
                        <?php endif; ?>
                        <div class="category-badge"><?= htmlspecialchars($drama->certificate_number ?? 'No Certificate') ?></div>
                    </div>

                    <div class="drama-content">
                        <h3 class="drama-title"><?= htmlspecialchars($drama->drama_name ?? 'Registered Drama') ?></h3>
                        <p class="drama-description">Owner: <?= htmlspecialchars($drama->owner_name ?? 'Not recorded') ?></p>

                        <div class="drama-info">
                            <div class="info-item">
                                <i class='bx bx-calendar'></i>
                                <span><?= !empty($drama->created_at) ? date('M d, Y', strtotime($drama->created_at)) : 'N/A' ?></span>
                            </div>
                            <div class="info-item">
                                <i class='bx bx-file'></i>
                                <span><?= htmlspecialchars($drama->certificate_number ?? 'Pending') ?></span>
                            </div>
                        </div>

                        <div class="drama-footer">
                            <div class="price">
                                <i class='bx bx-user'></i>
                                <span><?= htmlspecialchars($drama->owner_name ?? 'Not recorded') ?></span>
                            </div>
                            <?php if (!empty($certificateFile)): ?>
                                <a href="<?= ROOT ?>/uploads/certificates/<?= htmlspecialchars(rawurlencode($certificateFile)) ?>" class="btn-view" target="_blank" rel="noopener">View Certificate</a>
                            <?php else: ?>
                                <span class="btn-view" style="pointer-events: none; opacity: 0.6;">Certificate Pending</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
                <?php else: ?>
            <div class="empty-state">
                <i class='bx bx-sad'></i>
                <h3>No Dramas Found</h3>
                <p>Try adjusting your search or filter criteria</p>
            </div>
        <?php endif; ?>
    </div>

        <!-- Pagination -->
        <?php if (!empty($data['total_pages']) && $data['total_pages'] > 1): ?>
            <div class="pagination">
                <?php 
                    $current = isset($data['page']) ? (int)$data['page'] : 1;
                    $per = isset($data['per_page']) ? (int)$data['per_page'] : 12;
                    $totalPages = (int)$data['total_pages'];
                    $queryBase = [];
                    if (!empty($data['search'])) $queryBase['search'] = $data['search'];
                    if (!empty($data['sort'])) $queryBase['sort'] = $data['sort'];
                    $queryBase['per_page'] = $per;
                    if (!function_exists('buildUrl')) {
                        function buildUrl($page, $base) {
                            $base['page'] = $page;
                            $qs = http_build_query($base);
                            return ROOT . '/BrowseDramas' . (!empty($qs) ? ('?' . $qs) : '');
                        }
                    }
                    $window = 2; // pages around current
                ?>
                <a class="page-btn" href="<?= buildUrl(max(1, $current-1), $queryBase) ?>" aria-label="Previous">&laquo; Prev</a>
                <a class="page-link <?= $current===1?'active':'' ?>" href="<?= buildUrl(1, $queryBase) ?>">1</a>
                <?php if ($current - $window > 2): ?>
                    <span class="page-ellipsis">…</span>
                <?php endif; ?>
                <?php for ($p = max(2, $current-$window); $p <= min($totalPages-1, $current+$window); $p++): ?>
                    <a class="page-link <?= $current===$p?'active':'' ?>" href="<?= buildUrl($p, $queryBase) ?>"><?= $p ?></a>
                <?php endfor; ?>
                <?php if ($current + $window < $totalPages-1): ?>
                    <span class="page-ellipsis">…</span>
                <?php endif; ?>
                <?php if ($totalPages > 1): ?>
                    <a class="page-link <?= $current===$totalPages?'active':'' ?>" href="<?= buildUrl($totalPages, $queryBase) ?>"><?= $totalPages ?></a>
                <?php endif; ?>
                <a class="page-btn" href="<?= buildUrl(min($totalPages, $current+1), $queryBase) ?>" aria-label="Next">Next &raquo;</a>
            </div>
        <?php endif; ?>
</div>

</body>
</html>
