<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($data['drama']->title ?? 'Drama Details') ?> - <?= APP_NAME ?></title>
  <link rel="shortcut icon" href="<?= ROOT ?>/assets/images/Rangamadala logo.png" type="image/x-icon">
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/browse_dramas.css">
  <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/drama_ratings.css">
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
            
            <!-- Drama Rating Summary -->
            <?php if (!empty($data['rating_summary'])): ?>
              <div class="rating-summary" style="margin-top: 16px; padding-top: 16px; border-top: 1px solid rgba(212,175,55,.2)">
                <div class="rating-stars" style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                  <span style="font-size: 24px; color: #d4af37;">★</span>
                  <span style="font-size: 18px; font-weight: bold; color: #d4af37;">
                    <?= round($data['rating_summary']->average_rating, 1) ?>
                  </span>
                  <span style="color: #c9b896; font-size: 14px;">
                    (<?= $data['rating_summary']->total_ratings ?> <?= $data['rating_summary']->total_ratings == 1 ? 'rating' : 'ratings' ?>)
                  </span>
                </div>
              </div>
            <?php endif; ?>
            
            <div class="details-actions">
              <a class="btn btn-primary" href="#"><i class='bx bx-cart-add'></i> Book Ticket</a>
              <button class="btn btn-outline" id="rateBtn" type="button"><span class="material-symbols-rounded">star</span> Rate Drama</button>
            </div>
          </div>
        </div>
        <div style="padding:0 24px 24px">
          <h3 style="color:#d4af37;margin:0 0 8px">About</h3>
          <p class="details-desc"><?= nl2br(htmlspecialchars($d->description ?? 'No description available.')) ?></p>
        </div>
      </div>

      <!-- Ratings Section -->
      <div class="ratings-container">
        <h2 class="ratings-title">Audience Ratings & Reviews</h2>
        
        <?php if (empty($data['ratings'])): ?>
          <div class="no-ratings">
            <p>No ratings yet. Be the first to rate this drama!</p>
          </div>
        <?php else: ?>
          <div class="ratings-list">
            <?php foreach ($data['ratings'] as $rating): ?>
              <div class="rating-item">
                <div class="rating-header">
                  <div class="rating-user-info">
                    <h4 class="rating-user-name"><?= htmlspecialchars($rating['full_name']) ?></h4>
                    <div class="rating-stars-display">
                      <?php for ($i = 0; $i < 5; $i++): ?>
                        <span class="star <?= $i < $rating['rating'] ? 'filled' : 'empty' ?>">★</span>
                      <?php endfor; ?>
                      <span class="rating-value"><?= $rating['rating'] ?>.0</span>
                    </div>
                  </div>
                  <span class="rating-date"><?= date('M d, Y', strtotime($rating['created_at'])) ?></span>
                </div>
                <?php if (!empty($rating['comment'])): ?>
                  <p class="rating-comment"><?= htmlspecialchars($rating['comment']) ?></p>
                <?php endif; ?>
                <div class="rating-footer">
                  <button class="helpful-btn" data-rating-id="<?= $rating['id'] ?>" type="button">
                    <i class='bx bx-thumb-up'></i> Helpful (<?= $rating['helpful_count'] ?>)
                  </button>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

    <?php else: ?>
      <div class="empty-state">
        <i class='bx bx-sad'></i>
        <h3>Drama Not Found</h3>
        <p>The requested drama could not be found.</p>
      </div>
    <?php endif; ?>
  </div>

  <!-- Rating Modal -->
  <div class="rating-modal-overlay" id="ratingModal">
    <div class="rating-modal">
      <div class="rating-modal-header">
        <h2>Rate This Drama</h2>
        <button class="close-btn" type="button" id="closeRatingModal">&times;</button>
      </div>
      
      <div class="rating-modal-content">
        <!-- Star Rating Selection -->
        <div class="star-selection">
          <label class="star-label">Your Rating</label>
          <div class="star-picker" id="starPicker">
            <?php for ($i = 1; $i <= 5; $i++): ?>
              <button class="star-btn" data-value="<?= $i ?>" type="button">★</button>
            <?php endfor; ?>
          </div>
          <input type="hidden" id="selectedRating" value="0">
          <div class="rating-feedback" id="ratingFeedback"></div>
        </div>

        <!-- Comment Section -->
        <div class="comment-section">
          <label for="ratingComment" class="comment-label">Add a Comment (Optional)</label>
          <textarea id="ratingComment" class="comment-textarea" placeholder="Share your thoughts about this drama..." maxlength="500"></textarea>
          <div class="comment-counter">
            <span id="charCount">0</span>/500
          </div>
        </div>

        <!-- Already Rated Notice -->
        <?php if ($data['has_rated'] && !empty($data['user_rating'])): ?>
          <div class="already-rated-notice">
            <span class="material-symbols-rounded">info</span>
            <p>You already rated this drama with <strong><?= $data['user_rating']->rating ?> stars</strong>. Updating your rating will replace your previous review.</p>
          </div>
        <?php endif; ?>

        <!-- Submit Button -->
        <button class="submit-btn" id="submitRating" type="button">Submit Rating</button>
      </div>
    </div>
  </div>

  <!-- Success Message Toast -->
  <div class="toast-notification" id="successToast">
    <span class="material-symbols-rounded">check_circle</span>
    <p id="toastMessage">Rating submitted successfully!</p>
  </div>

  <script>
    const ROOT = '<?= ROOT ?>';
    const DRAMA_ID = <?= $data['drama']->id ?? 'null' ?>;
    const HAS_RATED = <?= $data['has_rated'] ? 'true' : 'false' ?>;
  </script>
  <script src="<?= ROOT ?>/assets/JS/drama-ratings.js"></script>
</body>
</html>

