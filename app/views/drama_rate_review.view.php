<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($data['drama']->title ?? 'Rate & Review') ?> - <?= APP_NAME ?></title>
  <link rel="shortcut icon" href="<?= ROOT ?>/assets/images/Rangamadala logo.png" type="image/x-icon">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <style>
    body { margin: 0; font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; background: #121212; color: #f2efe8; }
    .container { max-width: 1100px; margin: 24px auto; padding: 0 16px 28px; }
    .top-links { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 14px; }
    .btn { display: inline-flex; align-items: center; gap: 7px; text-decoration: none; border: 0; border-radius: 8px; padding: 10px 14px; cursor: pointer; font-weight: 600; }
    .btn-muted { background: #2e2e2e; color: #fff; }
    .btn-gold { background: #d4af37; color: #1b160f; }
    .card { background: #1b1b1b; border: 1px solid rgba(212, 175, 55, 0.22); border-radius: 14px; }
    .header { display: grid; grid-template-columns: 260px 1fr; gap: 18px; padding: 16px; }
    .poster { width: 100%; height: 330px; object-fit: cover; border-radius: 10px; border: 1px solid rgba(212, 175, 55, 0.2); }
    .title { margin: 0 0 8px; color: #d4af37; font-size: 30px; }
    .meta { color: #cfbf98; display: grid; gap: 7px; margin-top: 8px; }
    .meta i { color: #d4af37; margin-right: 8px; }
    .rating-overview { margin-top: 14px; padding: 12px; background: rgba(212, 175, 55, 0.08); border: 1px solid rgba(212, 175, 55, 0.25); border-radius: 10px; }
    .rating-overview strong { color: #f9e3a7; }
    .section { margin-top: 16px; padding: 16px; }
    .section h2 { margin: 0 0 12px; color: #f2d57f; font-size: 21px; }
    .stars { display: flex; gap: 6px; margin-bottom: 8px; }
    .star { font-size: 28px; color: #5d5645; border: 0; background: transparent; cursor: pointer; }
    .star.selected { color: #d4af37; }
    textarea { width: 100%; box-sizing: border-box; min-height: 110px; border-radius: 8px; border: 1px solid #454545; background: #111; color: #fff; padding: 10px; }
    .row { display: flex; justify-content: space-between; align-items: center; gap: 8px; margin-top: 8px; flex-wrap: wrap; }
    .hint { color: #b2a689; font-size: 13px; }
    .message { display: none; margin-top: 8px; padding: 10px 12px; border-radius: 8px; }
    .message.ok { display: block; background: rgba(29, 148, 88, 0.2); border: 1px solid rgba(29, 148, 88, 0.5); }
    .message.err { display: block; background: rgba(214, 58, 74, 0.2); border: 1px solid rgba(214, 58, 74, 0.5); }
    .review-list { display: grid; gap: 10px; }
    .review { border: 1px solid rgba(212, 175, 55, 0.2); border-radius: 10px; padding: 12px; background: #191919; }
    .review-top { display: flex; justify-content: space-between; gap: 10px; flex-wrap: wrap; }
    .review-name { margin: 0; color: #fff; }
    .review-stars { color: #d4af37; letter-spacing: 1px; }
    .review-date { color: #b1a58a; font-size: 13px; }
    .review-comment { margin: 8px 0 0; color: #e7dfcf; line-height: 1.55; }
    .empty { color: #b1a58a; }
    @media (max-width: 820px) {
      .header { grid-template-columns: 1fr; }
      .poster { height: 250px; }
      .title { font-size: 24px; }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="top-links">
      <a class="btn btn-muted" href="<?= ROOT ?>/Audiencedashboard"><i class='bx bx-arrow-back'></i> Back to Dashboard</a>
      <?php if (!empty($data['drama'])): ?>
        <a class="btn btn-muted" href="<?= ROOT ?>/BrowseDramas/view/<?= (int)$data['drama']->id ?>"><i class='bx bx-show'></i> View Details Page</a>
      <?php endif; ?>
    </div>

    <?php if (!empty($data['drama'])): $d = $data['drama']; ?>
      <div class="card header">
        <div>
          <?php if (!empty($d->image)): ?>
            <img class="poster" src="<?= ROOT ?>/uploads/dramas/<?= htmlspecialchars($d->image) ?>" alt="<?= htmlspecialchars($d->title) ?>">
          <?php else: ?>
            <div class="poster" style="display:flex;align-items:center;justify-content:center;background:#242424;color:#9d9276;">
              <i class='bx bx-movie-play' style="font-size:72px;"></i>
            </div>
          <?php endif; ?>
        </div>
        <div>
          <h1 class="title"><?= htmlspecialchars($d->title) ?></h1>
          <div class="meta">
            <div><i class='bx bx-calendar'></i><?= !empty($d->event_date) ? date('M d, Y', strtotime($d->event_date)) : 'TBA' ?></div>
            <div><i class='bx bx-time-five'></i><?= htmlspecialchars($d->event_time ?? 'TBA') ?></div>
            <div><i class='bx bx-map'></i><?= htmlspecialchars($d->venue ?? 'TBA') ?></div>
            <div><i class='bx bx-purchase-tag'></i>LKR <?= number_format($d->ticket_price ?? 0, 2) ?></div>
          </div>

          <?php $summary = $data['rating_summary'] ?? null; ?>
          <div class="rating-overview">
            <?php if (!empty($summary) && (int)($summary->total_ratings ?? 0) > 0): ?>
              <div><strong>Average Rating:</strong> <?= number_format((float)$summary->average_rating, 1) ?> / 5</div>
              <div><strong>Total Reviews:</strong> <?= (int)$summary->total_ratings ?></div>
            <?php else: ?>
              <div class="empty">No ratings yet for this drama.</div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="card section">
        <h2>Rate This Drama</h2>
        <div class="stars" id="starPicker">
          <button class="star" data-value="1" type="button">★</button>
          <button class="star" data-value="2" type="button">★</button>
          <button class="star" data-value="3" type="button">★</button>
          <button class="star" data-value="4" type="button">★</button>
          <button class="star" data-value="5" type="button">★</button>
        </div>
        <textarea id="comment" maxlength="500" placeholder="Write your review (optional)"><?php if (!empty($data['user_rating']->comment)) echo htmlspecialchars($data['user_rating']->comment); ?></textarea>
        <div class="row">
          <span class="hint"><span id="charCount">0</span>/500 characters</span>
          <button class="btn btn-gold" id="submitBtn" type="button"><i class='bx bx-send'></i> Submit Review</button>
        </div>
        <div id="message" class="message"></div>
      </div>

      <div class="card section">
        <h2>Audience Reviews</h2>
        <div class="review-list">
          <?php if (!empty($data['ratings'])): ?>
            <?php foreach ($data['ratings'] as $rating): ?>
              <div class="review">
                <div class="review-top">
                  <div>
                    <h3 class="review-name"><?= htmlspecialchars($rating['full_name'] ?? 'Anonymous') ?></h3>
                    <div class="review-stars"><?= str_repeat('★', (int)$rating['rating']) . str_repeat('☆', 5 - (int)$rating['rating']) ?></div>
                  </div>
                  <div class="review-date"><?= !empty($rating['created_at']) ? date('M d, Y', strtotime($rating['created_at'])) : '' ?></div>
                </div>
                <?php if (!empty($rating['comment'])): ?>
                  <p class="review-comment"><?= nl2br(htmlspecialchars($rating['comment'])) ?></p>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="empty">No reviews yet. Be the first reviewer.</div>
          <?php endif; ?>
        </div>
      </div>
    <?php else: ?>
      <div class="card section">
        <h2>Drama Not Found</h2>
        <p class="empty">The requested drama is not available.</p>
      </div>
    <?php endif; ?>
  </div>

  <script>
    const ROOT = '<?= ROOT ?>';
    const DRAMA_ID = <?= !empty($data['drama']) ? (int)$data['drama']->id : 'null' ?>;
    const EXISTING_RATING = <?= !empty($data['user_rating']->rating) ? (int)$data['user_rating']->rating : 0 ?>;

    const stars = Array.from(document.querySelectorAll('.star'));
    const commentEl = document.getElementById('comment');
    const charCount = document.getElementById('charCount');
    const submitBtn = document.getElementById('submitBtn');
    const messageEl = document.getElementById('message');

    let selected = EXISTING_RATING || 0;

    function renderStars() {
      stars.forEach((star, idx) => {
        if (idx < selected) {
          star.classList.add('selected');
        } else {
          star.classList.remove('selected');
        }
      });
    }

    function showMessage(text, ok) {
      messageEl.textContent = text;
      messageEl.className = ok ? 'message ok' : 'message err';
    }

    stars.forEach((star) => {
      star.addEventListener('click', () => {
        selected = parseInt(star.dataset.value, 10);
        renderStars();
      });
    });

    if (commentEl) {
      charCount.textContent = commentEl.value.length;
      commentEl.addEventListener('input', () => {
        charCount.textContent = commentEl.value.length;
      });
    }

    if (submitBtn) {
      submitBtn.addEventListener('click', async () => {
        if (!DRAMA_ID) {
          showMessage('Invalid drama selected.', false);
          return;
        }

        if (selected < 1 || selected > 5) {
          showMessage('Please select a rating between 1 and 5 stars.', false);
          return;
        }

        submitBtn.disabled = true;
        const oldText = submitBtn.textContent;
        submitBtn.textContent = 'Submitting...';

        try {
          const res = await fetch(`${ROOT}/BrowseDramas/submitRating`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              drama_id: DRAMA_ID,
              rating: selected,
              comment: commentEl ? commentEl.value.trim() : ''
            })
          });

          const data = await res.json();
          if (data.success) {
            showMessage('Review submitted successfully. Reloading...', true);
            setTimeout(() => window.location.reload(), 900);
          } else {
            showMessage(data.message || 'Failed to submit review.', false);
          }
        } catch (e) {
          showMessage('Network error while submitting review.', false);
        } finally {
          submitBtn.disabled = false;
          submitBtn.textContent = oldText;
        }
      });
    }

    renderStars();
  </script>
</body>
</html>
