<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($data['drama']->title ?? 'Rate & Review') ?> - <?= APP_NAME ?></title>
  <link rel="shortcut icon" href="<?= ROOT ?>/assets/images/Rangamadala logo.png" type="image/x-icon">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/ui-theme.css">
  <style>
    body {
      display: block;
      margin: 0;
      background: linear-gradient(180deg, #fffdf7 0%, #f8f0d8 100%);
      color: #4a3a14;
    }

    .rate-page {
      max-width: 1180px;
      margin: 28px auto 48px;
      padding: 0 20px;
    }

    .top-links {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      margin-bottom: 18px;
    }

    .rate-card {
      background: rgba(255, 255, 255, 0.96);
      border: 1px solid rgba(186, 142, 35, 0.22);
      border-radius: 18px;
      box-shadow: 0 14px 40px rgba(186, 142, 35, 0.12);
      overflow: hidden;
    }

    .rate-hero {
      display: grid;
      grid-template-columns: 280px 1fr;
      gap: 24px;
      padding: 24px;
      background: linear-gradient(180deg, #fffdf7 0%, #fff7e8 100%);
    }

    .rate-poster {
      width: 100%;
      height: 380px;
      object-fit: cover;
      border-radius: 14px;
      border: 1px solid rgba(186, 142, 35, 0.2);
      box-shadow: 0 10px 24px rgba(186, 142, 35, 0.14);
    }

    .rate-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 14px;
      padding: 8px 14px;
      border-radius: 999px;
      background: rgba(186, 142, 35, 0.12);
      color: #7a6121;
      font-weight: 700;
      font-size: 13px;
      letter-spacing: 0.2px;
    }

    .rate-title {
      margin: 0 0 10px;
      color: #4a3a14;
      font-size: 36px;
      line-height: 1.1;
    }

    .rate-meta {
      display: grid;
      gap: 10px;
      color: #5f4b23;
      margin: 14px 0 18px;
    }

    .rate-meta i {
      color: #ba8e23;
      margin-right: 8px;
    }

    .rate-description {
      color: #5f4b23;
      line-height: 1.7;
      margin: 0;
    }

    .page-section {
      padding: 22px 24px 24px;
      border-top: 1px solid rgba(186, 142, 35, 0.12);
    }

    .section-title {
      margin: 0 0 12px;
      color: #4a3a14;
      font-size: 22px;
    }

    .stars { display: flex; gap: 8px; margin-bottom: 10px; }
    .star {
      border: 0;
      background: transparent;
      color: #c0b497;
      font-size: 30px;
      line-height: 1;
      cursor: pointer;
      padding: 2px;
    }
    .star.selected { color: #d4af37; }
    textarea {
      width: 100%;
      box-sizing: border-box;
      min-height: 110px;
      border-radius: 10px;
      border: 1px solid #e6d7b2;
      background: #fffdfa;
      color: #4a3a14;
      padding: 10px;
    }
    textarea:focus {
      border-color: #ba8e23;
      outline: none;
      box-shadow: 0 0 0 3px rgba(186, 142, 35, 0.18);
    }
    .row { display: flex; justify-content: space-between; align-items: center; gap: 8px; margin-top: 8px; flex-wrap: wrap; }
    .hint { color: #7a6121; font-size: 13px; }
    .btn-gold { background: linear-gradient(135deg, #ba8e23, #a0781e); color: #fff; }
    .message { display: none; margin-top: 8px; padding: 10px 12px; border-radius: 8px; }
    .message.ok { display: block; background: rgba(29, 148, 88, 0.2); border: 1px solid rgba(29, 148, 88, 0.5); }
    .message.err { display: block; background: rgba(214, 58, 74, 0.2); border: 1px solid rgba(214, 58, 74, 0.5); }
    .review-list { display: grid; gap: 10px; }
    .review { border: 1px solid #efdcb0; border-radius: 12px; padding: 12px; background: #fffaf0; }
    .review-top { display: flex; justify-content: space-between; gap: 10px; flex-wrap: wrap; }
    .review-name { margin: 0; color: #4a3a14; }
    .review-date { color: #7a6121; font-size: 13px; }
    .review-comment { margin: 8px 0 0; color: #5f4b23; line-height: 1.55; }
    .empty {
      padding: 14px;
      border-radius: 12px;
      background: #fffaf0;
      border: 1px dashed #efdcb0;
      color: #7a6121;
    }

    @media (max-width: 820px) {
      .rate-page { padding: 0 14px; }
      .rate-hero { grid-template-columns: 1fr; }
      .rate-poster { height: 280px; }
      .rate-title { font-size: 28px; }
    }
  </style>
</head>
<body>
  <div class="rate-page">
    <div class="top-links">
      <a class="btn btn-secondary" href="<?= ROOT ?>/BrowseDramas"><i class='bx bx-arrow-back'></i> Back to Browse Dramas</a>
    </div>

    <?php if (!empty($data['drama'])): $d = $data['drama']; ?>
      <div class="rate-card">
        <div class="rate-hero">
        <div>
          <?php if (!empty($d->image)): ?>
            <img class="rate-poster" src="<?= ROOT ?>/uploads/dramas/<?= htmlspecialchars($d->image) ?>" alt="<?= htmlspecialchars($d->title) ?>">
          <?php else: ?>
            <div class="rate-poster" style="display:flex;align-items:center;justify-content:center;background:#f1e4bf;color:#9b7a28;">
              <i class='bx bx-movie-play' style="font-size:72px;"></i>
            </div>
          <?php endif; ?>
        </div>
        <div>
          <div class="rate-badge"><i class='bx bx-star'></i> Rate and Review</div>
          <h1 class="rate-title"><?= htmlspecialchars($d->title) ?></h1>
          <div class="rate-meta">
            <div><i class='bx bx-category'></i>Category: <?= htmlspecialchars($d->category_name ?? 'N/A') ?></div>
            <div><i class='bx bx-user'></i>Producer: <?= htmlspecialchars($d->owner_name ?? 'N/A') ?></div>
            <div><i class='bx bx-time-five'></i>Duration: <?= !empty($d->duration_minutes) ? (int)$d->duration_minutes . ' min' : 'N/A' ?></div>
          </div>
          <p class="rate-description"><?= nl2br(htmlspecialchars($d->description ?? 'No description available.')) ?></p>
        </div>
      </div>

      <div class="page-section">
        <h2 class="section-title">Rate and Comment</h2>
        <div class="stars" id="starPicker" aria-label="Rate this drama">
          <button class="star" data-value="1" type="button" aria-label="1 star">★</button>
          <button class="star" data-value="2" type="button" aria-label="2 stars">★</button>
          <button class="star" data-value="3" type="button" aria-label="3 stars">★</button>
          <button class="star" data-value="4" type="button" aria-label="4 stars">★</button>
          <button class="star" data-value="5" type="button" aria-label="5 stars">★</button>
        </div>
        <textarea id="comment" maxlength="500" placeholder="Write your review (optional)"><?php if (!empty($data['user_rating']->comment)) echo htmlspecialchars($data['user_rating']->comment); ?></textarea>
        <div class="row">
          <span class="hint"><span id="charCount">0</span>/500 characters</span>
          <button class="btn btn-gold" id="submitBtn" type="button"><i class='bx bx-send'></i> Submit Comment</button>
        </div>
        <div id="message" class="message"></div>
      </div>

      <div class="page-section">
        <h2 class="section-title">Other Comments</h2>
        <div class="review-list">
          <?php if (!empty($data['ratings'])): ?>
            <?php foreach ($data['ratings'] as $rating): ?>
              <div class="review">
                <div class="review-top">
                  <div>
                    <h3 class="review-name"><?= htmlspecialchars($rating['full_name'] ?? 'Anonymous') ?></h3>
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
      </div>
    <?php else: ?>
      <div class="rate-card page-section">
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
          showMessage('Please select a star rating from 1 to 5.', false);
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
            showMessage('Comment submitted successfully. Reloading...', true);
            setTimeout(() => window.location.reload(), 900);
          } else {
            showMessage(data.message || 'Failed to submit comment.', false);
          }
        } catch (e) {
          showMessage('Network error while submitting comment.', false);
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
