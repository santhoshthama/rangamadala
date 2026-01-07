<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($data['drama']->title ?? 'Drama Details') ?> - <?= APP_NAME ?></title>
  <link rel="shortcut icon" href="<?= ROOT ?>/assets/images/Rangamadala logo.png" type="image/x-icon">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/browse_dramas.css">
  <style>
    .details-container{max-width:1100px;margin:20px auto;padding:20px}
    .details-card{background:rgba(212,175,55,.03);border:2px solid rgba(212,175,55,.2);border-radius:16px;overflow:hidden}
    .details-hero{display:grid;grid-template-columns: 1.4fr 1fr;gap:24px;padding:24px}
    .details-img{width:100%;height:360px;object-fit:cover;border-radius:12px;border:2px solid rgba(212,175,55,.2)}
    .details-title{font-size:36px;color:#d4af37;margin:0 0 10px;font-weight:800}
    .details-meta{color:#c9b896;display:grid;gap:10px;margin:14px 0}
    .details-meta i{color:#d4af37;margin-right:8px}
    .details-desc{color:#f5f0e8;line-height:1.7;margin-top:10px}
    .details-actions{display:flex;gap:12px;margin-top:16px}
    .btn{padding:12px 18px;border-radius:8px;text-decoration:none;font-weight:700;display:inline-flex;align-items:center;gap:8px}
    .btn-primary{background:linear-gradient(135deg,#d4af37,#aa8c2c);color:#1a1410}
    .btn-outline{border:2px solid #d4af37;color:#d4af37}
    .badge{display:inline-block;background:rgba(212,175,55,.12);border:1px solid rgba(212,175,55,.3);color:#f5f0e8;padding:6px 10px;border-radius:999px;font-size:12px}
    @media(max-width:900px){.details-hero{grid-template-columns:1fr}.details-img{height:260px}}
  </style>
</head>
<body>
  <div class="container">
    <div class="back-container">
      <a href="<?= ROOT ?>/BrowseDramas" class="back-link"><button class="back-btn" type="button"><i class='bx bx-arrow-back'></i> Back to Browse</button></a>
    </div>
  </div>

  <div class="details-container">
    <?php if (!empty($data['drama'])): $d=$data['drama']; ?>
      <div class="details-card">
        <div class="details-hero">
          <div>
            <?php if (!empty($d->image)): ?>
              <img class="details-img" src="<?= ROOT ?>/uploads/dramas/<?= htmlspecialchars($d->image) ?>" alt="<?= htmlspecialchars($d->title) ?>" onerror="this.onerror=null;this.replaceWith(document.createElement('div'))">
            <?php else: ?>
              <div class="placeholder-image" style="height:360px;border-radius:12px"><i class='bx bx-movie-play'></i></div>
            <?php endif; ?>
          </div>
          <div>
            <h1 class="details-title"><?= htmlspecialchars($d->title) ?></h1>
            <div class="badge"><?= htmlspecialchars($d->category_name ?? 'Uncategorized') ?></div>
            <div class="details-meta">
              <div><i class='bx bx-calendar'></i><?= !empty($d->event_date)?date('M d, Y', strtotime($d->event_date)):'TBA' ?></div>
              <div><i class='bx bx-time'></i><?= htmlspecialchars($d->event_time ?? 'TBA') ?></div>
              <div><i class='bx bx-map'></i><?= htmlspecialchars($d->venue ?? 'TBA') ?></div>
              <div><i class='bx bx-purchase-tag'></i>LKR <?= number_format($d->ticket_price ?? 0, 2) ?></div>
              <?php if (!empty($d->creator_name)): ?><div><i class='bx bx-user'></i>By <?= htmlspecialchars($d->creator_name) ?></div><?php endif; ?>
            </div>
            <div class="details-actions">
              <a class="btn btn-primary" href="#"><i class='bx bx-cart-add'></i> Book Ticket</a>
              <a class="btn btn-outline" href="<?= ROOT ?>/Audiencedashboard"><i class='bx bx-home'></i> Dashboard</a>
            </div>
          </div>
        </div>
        <div style="padding:0 24px 24px">
          <h3 style="color:#d4af37;margin:0 0 8px">About</h3>
          <p class="details-desc"><?= nl2br(htmlspecialchars($d->description ?? 'No description available.')) ?></p>
        </div>
      </div>
    <?php else: ?>
      <div class="empty-state">
        <i class='bx bx-sad'></i>
        <h3>Drama Not Found</h3>
        <p>The requested drama could not be found.</p>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>
