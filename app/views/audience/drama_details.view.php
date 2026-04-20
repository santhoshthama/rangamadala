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
  <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/audience-drama-details.css">
</head>
<body>
  <?php
    $successMessage = $_SESSION['success_message'] ?? '';
    $errorMessage = $_SESSION['error_message'] ?? '';
    unset($_SESSION['success_message'], $_SESSION['error_message']);
    $canRate = !empty($data['can_rate']);
  ?>

  <div class="container">
    <div class="back-container">
      <a href="<?= ROOT ?>/BrowseDramas" class="back-link"><button class="back-btn" type="button"><i class='bx bx-arrow-back'></i> Back to Browse</button></a>
    </div>
  </div>

  <div class="details-container">
    <?php if (!empty($successMessage)): ?>
      <div class="status-alert success">
        <?= htmlspecialchars($successMessage) ?>
      </div>
    <?php endif; ?>
    <?php if (!empty($errorMessage)): ?>
      <div class="status-alert error">
        <?= htmlspecialchars($errorMessage) ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($data['drama'])): $d=$data['drama']; ?>
      <div class="details-card">
        <div class="details-hero">
          <div>
            <?php if (!empty($d->image)): ?>
              <img class="details-img" src="<?= ROOT ?>/uploads/dramas/<?= htmlspecialchars($d->image) ?>" alt="<?= htmlspecialchars($d->title) ?>" onerror="this.onerror=null;this.replaceWith(document.createElement('div'))">
            <?php else: ?>
              <div class="details-placeholder"><i class='bx bx-movie-play'></i></div>
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
              <?php if (!empty($d->showing_prices)): ?><div><i class='bx bx-list-ul'></i><?= htmlspecialchars($d->showing_prices) ?></div><?php endif; ?>
              <?php if (!empty($d->creator_name)): ?><div><i class='bx bx-user'></i>By <?= htmlspecialchars($d->creator_name) ?></div><?php endif; ?>
            </div>
            
            <!-- Drama Rating Summary -->
            <?php if (!empty($data['rating_summary'])): ?>
              <div class="rating-summary">
                <div class="rating-summary-stars">
                  <span class="rating-summary-star">★</span>
                  <span class="rating-summary-score">
                    <?= round($data['rating_summary']->average_rating, 1) ?>
                  </span>
                  <span class="rating-summary-count">
                    (<?= $data['rating_summary']->total_ratings ?> <?= $data['rating_summary']->total_ratings == 1 ? 'rating' : 'ratings' ?>)
                  </span>
                </div>
              </div>
            <?php endif; ?>
            
            <div class="details-actions">
              <a class="btn btn-primary" href="<?= ROOT ?>/BrowseDramas/bookShowings/<?= (int)$d->id ?>"><i class='bx bx-cart-add'></i> Buy Show Ticket</a>
              <?php if ($canRate): ?>
                <a class="btn btn-outline" href="<?= ROOT ?>/BrowseDramas/rateReview/<?= (int)$d->id ?>"><span class="bx bx-revision">reviews</span> Rate &amp; Review Page</a>
                <button class="btn btn-outline" id="rateBtn" type="button"><span class="bx bx-star"></span> Rate Drama</button>
              <?php else: ?>
                <button class="btn btn-outline" type="button" disabled title="Available after the drama is watched"><span class="bx bx-star"></span> Rate Drama</button>
              <?php endif; ?>
            </div>
            <?php if (isset($_GET['book']) && $_GET['book'] === '1'): ?>
              <div class="details-selected-note">
                Showing selected. Confirm the schedule details below and proceed with booking steps.
              </div>
            <?php endif; ?>
          </div>
        </div>
        <div class="details-about">
          <h3 class="details-about-title">About</h3>
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
            <span class="bx bx-info-circle"></span>
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
    <span class="bx bx-check-circle"></span>
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

