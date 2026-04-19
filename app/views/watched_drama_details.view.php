<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($data['drama']->title ?? 'Watched Drama Details') ?> - <?= APP_NAME ?></title>
  <link rel="shortcut icon" href="<?= ROOT ?>/assets/images/Rangamadala logo.png" type="image/x-icon">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/ui-theme.css">
  <style>
    body {
      display: block;
      background: linear-gradient(180deg, #fffdf7 0%, #f8f0d8 100%);
    }

    .watched-page {
      max-width: 1180px;
      margin: 28px auto 48px;
      padding: 0 20px;
    }

    .top-links {
      margin-bottom: 18px;
    }

    .watched-card {
      background: rgba(255, 255, 255, 0.96);
      border: 1px solid rgba(186, 142, 35, 0.22);
      border-radius: 18px;
      box-shadow: 0 14px 40px rgba(186, 142, 35, 0.12);
      overflow: hidden;
    }

    .watched-hero {
      display: grid;
      grid-template-columns: 280px 1fr;
      gap: 24px;
      padding: 24px;
      background: linear-gradient(180deg, #fffdf7 0%, #fff7e8 100%);
    }

    .watched-poster {
      width: 100%;
      height: 380px;
      object-fit: cover;
      border-radius: 14px;
      border: 1px solid rgba(186, 142, 35, 0.2);
      box-shadow: 0 10px 24px rgba(186, 142, 35, 0.14);
    }

    .watched-title {
      margin: 0 0 10px;
      color: #4a3a14;
      font-size: 36px;
      line-height: 1.1;
    }

    .watched-badge {
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

    .watched-meta {
      display: grid;
      gap: 10px;
      color: #5f4b23;
      margin: 14px 0 18px;
    }

    .watched-meta i {
      color: #ba8e23;
      margin-right: 8px;
    }

    .watched-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 14px;
      margin-top: 18px;
    }

    .info-panel {
      padding: 16px;
      border-radius: 14px;
      background: #fffaf0;
      border: 1px solid #efdcb0;
    }

    .info-panel h3 {
      margin: 0 0 10px;
      color: #5a4415;
      font-size: 18px;
    }

    .info-list {
      display: grid;
      gap: 10px;
      color: #5f4b23;
      font-size: 14px;
    }

    .info-row {
      display: flex;
      justify-content: space-between;
      gap: 14px;
      border-bottom: 1px dashed rgba(186, 142, 35, 0.18);
      padding-bottom: 8px;
    }

    .info-row:last-child {
      border-bottom: 0;
      padding-bottom: 0;
    }

    .info-row strong {
      color: #4a3a14;
      flex: 0 0 auto;
    }

    .watched-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      margin-top: 20px;
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

    .summary-box {
      padding: 16px;
      border-radius: 14px;
      background: linear-gradient(180deg, #fffdf7 0%, #fff7e6 100%);
      border: 1px solid #f0dfb4;
      color: #4a3a14;
      box-shadow: 0 4px 12px rgba(186, 142, 35, 0.08);
    }

    .summary-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 12px 20px;
    }

    .summary-item {
      display: flex;
      justify-content: space-between;
      gap: 16px;
      font-size: 14px;
      color: #5f4b23;
      border-bottom: 1px solid rgba(186, 142, 35, 0.14);
      padding-bottom: 8px;
    }

    .summary-item:last-child {
      border-bottom: 0;
      padding-bottom: 0;
    }

    .summary-item strong {
      color: #4a3a14;
    }

    .subtle-note {
      margin-top: 12px;
      color: #7a6121;
      font-size: 14px;
      line-height: 1.6;
    }

    .review-preview {
      margin-top: 10px;
      padding: 14px 16px;
      border-radius: 14px;
      background: #fff;
      border: 1px solid rgba(186, 142, 35, 0.16);
      color: #5f4b23;
    }

    .review-editor {
      margin-top: 10px;
      padding: 16px;
      border-radius: 14px;
      background: #fff;
      border: 1px solid rgba(186, 142, 35, 0.18);
    }

    .review-editor-top {
      display: flex;
      justify-content: space-between;
      gap: 12px;
      align-items: center;
      margin-bottom: 12px;
      flex-wrap: wrap;
    }

    .review-editor-title {
      margin: 0;
      font-size: 18px;
      color: #4a3a14;
    }

    .review-actions {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }

    .btn-outline-danger {
      background: #fff;
      color: #b42318;
      border: 1px solid rgba(180, 35, 24, 0.28);
    }

    .btn-outline-danger:hover {
      background: rgba(180, 35, 24, 0.08);
    }

    .review-editor .stars {
      margin-bottom: 12px;
    }

    .review-editor textarea {
      width: 100%;
      min-height: 120px;
      border-radius: 10px;
      border: 1px solid #e6d7b2;
      background: #fffdfa;
      color: #4a3a14;
      padding: 10px;
      box-sizing: border-box;
    }

    .review-editor textarea:focus {
      border-color: #ba8e23;
      outline: none;
      box-shadow: 0 0 0 3px rgba(186, 142, 35, 0.18);
    }

    .review-status {
      margin-top: 10px;
      font-size: 13px;
      color: #7a6121;
    }

    .review-preview strong {
      color: #4a3a14;
    }

    .empty-state-inline {
      padding: 16px;
      border-radius: 14px;
      background: #fffaf0;
      border: 1px dashed #efdcb0;
      color: #7a6121;
    }

    @media (max-width: 900px) {
      .watched-page {
        padding: 0 14px;
      }

      .watched-hero {
        grid-template-columns: 1fr;
      }

      .watched-poster {
        height: 280px;
      }

      .watched-grid,
      .summary-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>
  <?php
    $drama = $data['drama'] ?? null;
    $booking = $data['booking'] ?? null;
    $requestDetails = $data['request_details'] ?? [];
    $bookingStatus = strtolower((string)($data['booking_status'] ?? ''));
    $ratingSummary = $data['rating_summary'] ?? null;
    $canRate = !empty($data['can_rate']);
    $hasRated = !empty($data['has_rated']);
    $userRating = $data['user_rating'] ?? null;
    $ratings = $data['ratings'] ?? [];
    $myRating = $userRating ?? null;

    $showDateRaw = trim((string)($requestDetails['show_date'] ?? ''));
    $showDateDisplay = $showDateRaw !== '' && strtotime($showDateRaw) !== false ? date('M d, Y', strtotime($showDateRaw)) : (!empty($drama->event_date) ? date('M d, Y', strtotime($drama->event_date)) : 'N/A');
    $showTimeDisplay = trim((string)($requestDetails['show_time'] ?? '')) !== '' ? trim((string)$requestDetails['show_time']) : (!empty($drama->event_time) ? (string)$drama->event_time : 'N/A');
    $venueDisplay = trim((string)($requestDetails['request_venue'] ?? '')) !== '' ? trim((string)$requestDetails['request_venue']) : (!empty($drama->venue) ? (string)$drama->venue : 'N/A');
    $orderIdDisplay = trim((string)($booking->payhere_order_id ?? '')) !== '' ? (string)$booking->payhere_order_id : 'N/A';
    $paidAtDisplay = !empty($booking->paid_at) ? date('M d, Y h:i A', strtotime((string)$booking->paid_at)) : 'N/A';
    $contactNameDisplay = trim((string)($requestDetails['request_sender_name'] ?? '')) !== '' ? trim((string)$requestDetails['request_sender_name']) : 'N/A';
    $contactPhoneDisplay = trim((string)($requestDetails['request_contact_phone'] ?? '')) !== '' ? trim((string)$requestDetails['request_contact_phone']) : 'N/A';
    $contactEmailDisplay = trim((string)($requestDetails['request_contact_email'] ?? '')) !== '' ? trim((string)$requestDetails['request_contact_email']) : 'N/A';
    $presentCountDisplay = isset($requestDetails['present_count']) && $requestDetails['present_count'] !== '' ? (string)$requestDetails['present_count'] : 'N/A';
    $notesDisplay = trim((string)($requestDetails['request_notes'] ?? '')) !== '' ? trim((string)$requestDetails['request_notes']) : 'None';
  ?>

  <div class="watched-page">
    <div class="top-links">
      <a class="btn btn-secondary" href="<?= ROOT ?>/BrowseDramas"><i class='bx bx-arrow-back'></i> Back to Browse Dramas</a>
    
    </div>

    <?php if (!empty($drama) && !empty($booking)): ?>
      <div class="watched-card">
        <div class="watched-hero">
          <div>
            <?php if (!empty($drama->image)): ?>
              <img class="watched-poster" src="<?= ROOT ?>/uploads/dramas/<?= htmlspecialchars($drama->image) ?>" alt="<?= htmlspecialchars($drama->title) ?>">
            <?php else: ?>
              <div class="watched-poster" style="display:flex;align-items:center;justify-content:center;background:#f1e4bf;color:#9b7a28;">
                <i class='bx bx-movie-play' style="font-size:78px;"></i>
              </div>
            <?php endif; ?>
          </div>

          <div>
            <div class="watched-badge"><i class='bx bx-check-circle'></i> Watched Drama Details</div>
            <h1 class="watched-title"><?= htmlspecialchars($drama->title ?? 'Drama') ?></h1>
            <div class="watched-meta">
              <div><i class='bx bx-category'></i>Category: <?= htmlspecialchars($drama->category_name ?? 'N/A') ?></div>
              <div><i class='bx bx-user'></i>Producer: <?= htmlspecialchars($drama->owner_name ?? 'N/A') ?></div>
              <div><i class='bx bx-time-five'></i>Duration: <?= !empty($drama->duration_minutes) ? (int)$drama->duration_minutes . ' min' : 'N/A' ?></div>
            </div>
            <p style="color:#5f4b23; line-height:1.7; margin:0;">
              <?= nl2br(htmlspecialchars($drama->description ?? 'No description available.')) ?>
            </p>

    
          </div>
        </div>

        <div class="page-section">
          <h2 class="section-title">Payment and Booking Summary</h2>
          <div class="summary-box">
            <div class="summary-grid">
              <div class="summary-item"><strong>Booking Status</strong><span><?= htmlspecialchars(ucfirst($bookingStatus !== '' ? $bookingStatus : 'Watched')) ?></span></div>
              <div class="summary-item"><strong>Order ID</strong><span><?= htmlspecialchars($orderIdDisplay) ?></span></div>
              <div class="summary-item"><strong>Paid At</strong><span><?= htmlspecialchars($paidAtDisplay) ?></span></div>
              <div class="summary-item"><strong>Show Date</strong><span><?= htmlspecialchars($showDateDisplay) ?></span></div>
              <div class="summary-item"><strong>Show Time</strong><span><?= htmlspecialchars($showTimeDisplay) ?></span></div>
              <div class="summary-item"><strong>Venue</strong><span><?= htmlspecialchars($venueDisplay) ?></span></div>
            </div>
          </div>
        </div>

        <div class="page-section">
          <h2 class="section-title">Request Details</h2>
          <div class="watched-grid">
            <div class="info-panel">
              <h3>Contact Information</h3>
              <div class="info-list">
                <div class="info-row"><strong>Sender Name</strong><span><?= htmlspecialchars($contactNameDisplay) ?></span></div>
                <div class="info-row"><strong>Phone</strong><span><?= htmlspecialchars($contactPhoneDisplay) ?></span></div>
                <div class="info-row"><strong>Email</strong><span><?= htmlspecialchars($contactEmailDisplay) ?></span></div>
              </div>
            </div>

            <div class="info-panel">
              <h3>Show Requirements</h3>
              <div class="info-list">
                <div class="info-row"><strong>Expected Present Count</strong><span><?= htmlspecialchars($presentCountDisplay) ?></span></div>
                <div class="info-row"><strong>Payment Record</strong><span><?= !empty($booking->paid_at) || !empty($booking->payhere_order_id) ? 'Available' : 'Not available' ?></span></div>
                <div class="info-row"><strong>Watched Access</strong><span><?= $canRate ? 'Eligible to review' : 'Review locked until watched' ?></span></div>
              </div>
            </div>
          </div>
          <div class="subtle-note">
            <?php if ($notesDisplay !== 'None'): ?>
              <strong>Notes:</strong> <?= htmlspecialchars($notesDisplay) ?>
            <?php else: ?>
              No additional notes were provided for this booking.
            <?php endif; ?>
          </div>
        </div>


        <?php if (!empty($ratings)): ?>
          <div class="page-section">
            <h2 class="section-title">Recent Audience Reviews</h2>
            <div class="review-preview" style="display:grid; gap:12px;">
              <?php foreach (array_slice($ratings, 0, 3) as $rating): ?>
                <div style="padding-bottom:12px; border-bottom:1px solid rgba(186,142,35,0.14);">
                  <strong><?= htmlspecialchars($rating['full_name'] ?? 'Anonymous') ?></strong>
                  <div style="color:#ba8e23; margin-top:4px;"><?= str_repeat('★', (int)$rating['rating']) . str_repeat('☆', 5 - (int)$rating['rating']) ?></div>
                  <?php if (!empty($rating['comment'])): ?>
                    <div style="margin-top:6px; color:#5f4b23; line-height:1.6;"><?= nl2br(htmlspecialchars($rating['comment'])) ?></div>
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>

        <div class="page-section">
          <h2 class="section-title">My Comment</h2>
          <?php if (!empty($myRating)): ?>
            <div class="review-editor" data-rating-id="<?= (int)$myRating->id ?>" data-drama-id="<?= (int)$drama->id ?>">
              <div class="review-editor-top">
                <p class="review-editor-title">Edit your review for this watched drama</p>
                <div class="review-actions">
                  <button type="button" class="btn btn-secondary" id="saveMyReviewBtn"><i class='bx bx-save'></i> Save Changes</button>
                  <button type="button" class="btn btn-outline-danger" id="deleteMyReviewBtn"><i class='bx bx-trash'></i> Delete Comment</button>
                </div>
              </div>
              <div class="stars" id="myStarPicker" aria-label="Edit your rating">
                <button class="star" data-value="1" type="button" aria-label="1 star">★</button>
                <button class="star" data-value="2" type="button" aria-label="2 stars">★</button>
                <button class="star" data-value="3" type="button" aria-label="3 stars">★</button>
                <button class="star" data-value="4" type="button" aria-label="4 stars">★</button>
                <button class="star" data-value="5" type="button" aria-label="5 stars">★</button>
              </div>
              <textarea id="myComment" maxlength="500" placeholder="Write your review"><?= htmlspecialchars($myRating->comment ?? '') ?></textarea>
              <div class="row">
                <span class="hint"><span id="myCharCount">0</span>/500 characters</span>
                <span class="review-status" id="myReviewStatus">You can update or delete only your own review.</span>
              </div>
            </div>
          <?php else: ?>
            <div class="empty-state-inline">
              <i class='bx bx-message-square-dots' style="font-size: 42px; display:block; margin-bottom: 10px; color:#ba8e23;"></i>
              <h3 style="margin:0 0 8px; color:#4a3a14;">No comment yet</h3>
              <p style="margin:0; color:#7a6121;">Use the review section above to add your comment after watching this drama.</p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    <?php else: ?>
      <div class="watched-card" style="padding: 28px; text-align: center;">
        <div class="empty-state-inline">
          <i class='bx bx-sad' style="font-size: 42px; display:block; margin-bottom: 10px; color:#ba8e23;"></i>
          <h2 style="margin:0 0 8px; color:#4a3a14;">Watched Drama Not Found</h2>
          <p style="margin:0; color:#7a6121;">The selected watched booking could not be loaded.</p>
        </div>
      </div>
    <?php endif; ?>
  </div>

  <script>
    const ROOT = '<?= ROOT ?>';
    const myReviewEditor = document.querySelector('.review-editor');
    const myStarPicker = document.getElementById('myStarPicker');
    const myCommentEl = document.getElementById('myComment');
    const myCharCount = document.getElementById('myCharCount');
    const saveMyReviewBtn = document.getElementById('saveMyReviewBtn');
    const deleteMyReviewBtn = document.getElementById('deleteMyReviewBtn');
    const myReviewStatus = document.getElementById('myReviewStatus');

    let mySelectedRating = <?= !empty($myRating->rating) ? (int)$myRating->rating : 0 ?>;
    const myReviewId = myReviewEditor ? parseInt(myReviewEditor.dataset.ratingId || '0', 10) : 0;
    const myStars = myStarPicker ? Array.from(myStarPicker.querySelectorAll('.star')) : [];

    function renderMyStars() {
      myStars.forEach((star, index) => {
        if (index < mySelectedRating) {
          star.classList.add('selected');
        } else {
          star.classList.remove('selected');
        }
      });
    }

    function setReviewStatus(message, isError = false) {
      if (!myReviewStatus) {
        return;
      }

      myReviewStatus.textContent = message;
      myReviewStatus.style.color = isError ? '#b42318' : '#7a6121';
    }

    if (myCommentEl && myCharCount) {
      myCharCount.textContent = myCommentEl.value.length;
      myCommentEl.addEventListener('input', () => {
        myCharCount.textContent = myCommentEl.value.length;
      });
    }

    myStars.forEach((star) => {
      star.addEventListener('click', () => {
        mySelectedRating = parseInt(star.dataset.value, 10);
        renderMyStars();
      });
    });

    if (saveMyReviewBtn) {
      saveMyReviewBtn.addEventListener('click', async () => {
        if (!myReviewId) {
          setReviewStatus('No review found to update.', true);
          return;
        }

        if (mySelectedRating < 1 || mySelectedRating > 5) {
          setReviewStatus('Please select a star rating before saving.', true);
          return;
        }

        saveMyReviewBtn.disabled = true;
        const previousText = saveMyReviewBtn.innerHTML;
        saveMyReviewBtn.innerHTML = '<i class="bx bx-loader-alt"></i> Saving...';

        try {
          const response = await fetch(`${ROOT}/BrowseDramas/submitRating`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              drama_id: <?= !empty($drama) ? (int)$drama->id : 0 ?>,
              rating: mySelectedRating,
              comment: myCommentEl ? myCommentEl.value.trim() : ''
            })
          });

          const data = await response.json();
          if (data.success) {
            setReviewStatus('Your review was updated successfully.');
            window.location.reload();
          } else {
            setReviewStatus(data.message || 'Unable to update your review.', true);
          }
        } catch (error) {
          setReviewStatus('Network error while updating your review.', true);
        } finally {
          saveMyReviewBtn.disabled = false;
          saveMyReviewBtn.innerHTML = previousText;
        }
      });
    }

    if (deleteMyReviewBtn) {
      deleteMyReviewBtn.addEventListener('click', async () => {
        if (!myReviewId) {
          setReviewStatus('No review found to delete.', true);
          return;
        }

        if (!confirm('Delete your comment and rating for this drama?')) {
          return;
        }

        deleteMyReviewBtn.disabled = true;
        const previousText = deleteMyReviewBtn.innerHTML;
        deleteMyReviewBtn.innerHTML = '<i class="bx bx-loader-alt"></i> Deleting...';

        try {
          const response = await fetch(`${ROOT}/BrowseDramas/deleteRating`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ rating_id: myReviewId })
          });

          const data = await response.json();
          if (data.success) {
            setReviewStatus('Your review was deleted.');
            window.location.reload();
          } else {
            setReviewStatus(data.message || 'Unable to delete your review.', true);
          }
        } catch (error) {
          setReviewStatus('Network error while deleting your review.', true);
        } finally {
          deleteMyReviewBtn.disabled = false;
          deleteMyReviewBtn.innerHTML = previousText;
        }
      });
    }

    renderMyStars();
  </script>
</body>
</html>
