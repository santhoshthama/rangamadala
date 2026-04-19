<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($data['drama']->title ?? 'Book Showings') ?> - <?= APP_NAME ?></title>
  <link rel="shortcut icon" href="<?= ROOT ?>/assets/images/Rangamadala logo.png" type="image/x-icon">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <script type="text/javascript" src="https://www.payhere.lk/lib/payhere.js"></script>
  <style>
    body { margin: 0; font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(180deg, #fffdf7 0%, #f8f0d8 100%); color: #3f2f12; }
    .container { max-width: 1180px; margin: 24px auto 40px; padding: 0 20px; }
    .top-links { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 16px; }
    .btn { display: inline-flex; align-items: center; gap: 7px; border: 0; border-radius: 12px; padding: 10px 14px; font-weight: 700; text-decoration: none; cursor: pointer; }
    .btn-muted { background: #fff; color: #7a6121; border: 1px solid #e1c37f; }
    .btn-primary { background: linear-gradient(135deg, #ba8e23, #9b761d); color: #fff; }
    .btn-gold { background: linear-gradient(135deg, #d4af37, #aa8c2c); color: #201a11; }
    .card { background: linear-gradient(180deg, #fffefb 0%, #fff7e7 100%); border: 1px solid #efdcb0; border-radius: 18px; box-shadow: 0 10px 26px rgba(186, 142, 35, 0.12); overflow: hidden; }
    .stack { display: grid; gap: 16px; }
    .hero { display: grid; grid-template-columns: 280px 1fr; gap: 18px; padding: 18px; }
    .poster { width: 100%; height: 340px; object-fit: cover; border-radius: 14px; border: 1px solid #e8cd8f; }
    .title { margin: 0 0 8px; color: #3f2f12; font-size: 34px; line-height: 1.1; }
    .meta { display: grid; gap: 8px; color: #6f5a2e; margin: 10px 0 14px; }
    .meta i { color: #d4af37; margin-right: 8px; }
    .about { color: #5f4b23; line-height: 1.7; margin: 0; }
    .section { padding: 18px; }
    .section h2 { margin: 0 0 12px; color: #5a4415; font-size: 22px; }
    .section-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 14px; }
    .section-badge { display: inline-flex; align-items: center; gap: 8px; padding: 7px 12px; border-radius: 999px; background: rgba(186, 142, 35, 0.12); color: #7a6121; font-size: 13px; font-weight: 700; }
    .summary-box { margin-top: 0; padding: 12px 14px; border-radius: 12px; background: #fff9ec; border: 1px solid #efdcb0; color: #5f4b23; }
    .message { margin-bottom: 12px; padding: 12px 14px; border-radius: 10px; display: flex; align-items: center; gap: 8px; font-weight: 600; }
    .message i { font-size: 18px; }
    .message.ok { border: 1px solid rgba(21, 128, 61, 0.35); border-left: 4px solid #15803d; background: rgba(21, 128, 61, 0.12); color: #166534; }
    .message.err { border: 1px solid rgba(220, 53, 69, .45); background: rgba(220, 53, 69, .16); color: #ffe8e8; }
    .booking-box { margin-top: 14px; padding: 14px; border-radius: 14px; background: #fff9ec; border: 1px solid #efdcb0; }
    .booking-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 10px; margin-bottom: 10px; }
    .booking-field { display: flex; flex-direction: column; gap: 5px; }
    .booking-field label { font-size: 13px; color: #6f5a2e; font-weight: 600; }
    .booking-field input, .booking-field textarea { border: 1px solid #e4cd95; border-radius: 8px; padding: 8px 10px; font-size: 14px; }
    .time-range-preview { font-size: 12px; color: #8b6a21; margin-top: 2px; }
    .hint { color: #7a6121; margin: 8px 0 0; font-size: 14px; }
    .detail-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
    .info-panel { padding: 14px; border-radius: 12px; background: #fffdf7; border: 1px solid #efdcb0; }
    .info-panel h3 { margin: 0 0 10px; color: #5a4415; font-size: 17px; }
    .info-list { display: grid; gap: 10px; color: #5f4b23; font-size: 14px; }
    .info-row { display: flex; justify-content: space-between; gap: 14px; border-bottom: 1px dashed rgba(186, 142, 35, 0.18); padding-bottom: 8px; }
    .info-row:last-child { border-bottom: 0; padding-bottom: 0; }
    .info-row strong { color: #4a3a14; flex: 0 0 auto; }
    .payment-note { margin: 10px 0 0; color: #7a6121; font-size: 14px; line-height: 1.6; }
    .empty { color: #8a7442; }
    @media (max-width: 860px) {
      .hero { grid-template-columns: 1fr; }
      .poster { height: 250px; }
      .title { font-size: 24px; }
      .detail-grid { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>
  <?php
    $successMessage = $_SESSION['success_message'] ?? '';
    $errorMessage = $_SESSION['error_message'] ?? '';
    unset($_SESSION['success_message'], $_SESSION['error_message']);
    $d = $data['drama'] ?? null;
    $hasBooking = !empty($data['has_booking']);
    $bookingRequest = $data['booking_request'] ?? null;
    $bookingStatus = strtolower((string)($data['booking_status'] ?? 'none'));
    $canMakePayment = !empty($data['can_make_payment']);
    $payhereConfig = $data['payhere_config'] ?? [];
    $audienceUser = $data['audience_user'] ?? null;
    $requestDetails = [];
    if (!empty($bookingRequest->request_details_json)) {
      $decodedRequestDetails = json_decode((string)$bookingRequest->request_details_json, true);
      if (is_array($decodedRequestDetails)) {
        $requestDetails = $decodedRequestDetails;
      }
    }
    $showTimeStartValue = trim((string)($requestDetails['show_time_start'] ?? ''));
    $showTimeEndValue = trim((string)($requestDetails['show_time_end'] ?? ''));
    $showTimeTextValue = trim((string)($requestDetails['show_time'] ?? ''));
    if (($showTimeStartValue === '' || $showTimeEndValue === '') && $showTimeTextValue !== '') {
      if (preg_match('/(\d{1,2}:\d{2})\s*(AM|PM)?\s*to\s*(\d{1,2}:\d{2})\s*(AM|PM)?/i', $showTimeTextValue, $timeMatch)) {
        $startRaw = $timeMatch[1] . (!empty($timeMatch[2]) ? (' ' . strtoupper($timeMatch[2])) : '');
        $endRaw = $timeMatch[3] . (!empty($timeMatch[4]) ? (' ' . strtoupper($timeMatch[4])) : '');
        $startTs = strtotime($startRaw);
        $endTs = strtotime($endRaw);
        if ($startTs !== false && $endTs !== false) {
          if ($showTimeStartValue === '') {
            $showTimeStartValue = date('H:i', $startTs);
          }
          if ($showTimeEndValue === '') {
            $showTimeEndValue = date('H:i', $endTs);
          }
        }
      }
    }
    $rejectionReason = trim((string)($bookingRequest->rejection_reason ?? ''));
    $bookingId = (int)($bookingRequest->id ?? 0);
    $paymentAmountSource = trim((string)($bookingRequest->showing_prices ?? ''));
    if ($paymentAmountSource === '') {
      $paymentAmountSource = trim((string)($d->showing_prices ?? ''));
    }
    $paymentAmountValue = 0.0;
    if ($paymentAmountSource !== '') {
      $paymentAmountSource = str_replace(',', '', $paymentAmountSource);
      if (preg_match('/\d+(?:\.\d+)?/', $paymentAmountSource, $amountMatch)) {
        $paymentAmountValue = (float)$amountMatch[0];
      }
    }
    $paymentAmountDisplay = number_format($paymentAmountValue, 2);
    $audienceFullName = trim((string)($audienceUser->full_name ?? 'Audience User'));
    $audienceNameParts = preg_split('/\s+/', $audienceFullName, 2);
    $audienceFirstName = $audienceNameParts[0] ?? 'Audience';
    $audienceLastName = $audienceNameParts[1] ?? 'User';
  ?>

  <div class="container">
    <div class="top-links">
      <a class="btn btn-muted" href="<?= ROOT ?>/Audiencedashboard"><i class='bx bx-arrow-back'></i> Back to Dashboard</a>
    </div>

    <?php if (!empty($successMessage)): ?>
      <div class="message ok"><i class='bx bx-check-circle'></i> <?= htmlspecialchars($successMessage) ?></div>
    <?php endif; ?>
    <?php if (!empty($errorMessage)): ?>
      <div class="message err"><?= htmlspecialchars($errorMessage) ?></div>
    <?php endif; ?>

    <?php if (!empty($d)): ?>
      <div class="stack">
        <div class="card hero section">
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
            <div class="section-head">
              <h2 style="margin:0; font-size:22px; color:#7a6121;">Drama Description</h2>
              <span class="section-badge"><i class='bx bx-info-circle'></i> Book Showings</span>
            </div>
            <h1 class="title"><?= htmlspecialchars($d->title) ?></h1>
            <div class="meta">
              <div><i class='bx bx-user'></i>Producer: <?= htmlspecialchars($d->owner_name ?? 'N/A') ?></div>
              <div><i class='bx bx-category'></i>Category: <?= htmlspecialchars($d->category_name ?? 'N/A') ?></div>
              <div><i class='bx bx-globe'></i>Language: <?= htmlspecialchars($d->language ?? 'N/A') ?></div>
              <div><i class='bx bx-time-five'></i>Duration: <?= !empty($d->duration_minutes) ? (int)$d->duration_minutes . ' min' : 'N/A' ?></div>
              <div><i class='bx bx-list-ul'></i>Showing prices: <?= !empty($d->showing_prices) ? htmlspecialchars($d->showing_prices) : 'Not specified' ?></div>
            </div>
            <p class="about"><?= nl2br(htmlspecialchars($d->description ?? 'No description available.')) ?></p>
          </div>
        </div>

        <div class="card section">
          <div class="section-head">
            <h2>Request Drama</h2>
            <span class="section-badge"><i class='bx bx-edit-alt'></i> Submit booking request</span>
          </div>

          <?php if ($bookingStatus === 'rejected'): ?>
            <p class="hint" style="color:#8a1f1f; font-weight:600;">Your previous request was rejected.</p>
            <p class="hint" style="color:#8a1f1f;">Reason: <?= htmlspecialchars($rejectionReason !== '' ? $rejectionReason : 'No reason provided by artist.') ?></p>
          <?php endif; ?>

          <div class="booking-box">
            <form method="POST" action="<?= ROOT ?>/BrowseDramas/bookShowings/<?= (int)$d->id ?>">
              <div class="booking-grid">
                <div class="booking-field">
                  <label for="request_sender_name">Request Sender Name *</label>
                  <input id="request_sender_name" type="text" name="request_sender_name" required value="">
                </div>
                <div class="booking-field">
                  <label for="request_contact_phone">Contact Phone *</label>
                  <input id="request_contact_phone" type="text" name="request_contact_phone" required placeholder="Example: 0771234567" value="">
                </div>
                <div class="booking-field">
                  <label for="request_contact_email">Contact Email</label>
                  <input id="request_contact_email" type="email" name="request_contact_email" placeholder="Example: name@email.com" value="">
                </div>
                <div class="booking-field">
                  <label for="request_venue">Expected Place of Show *</label>
                  <input id="request_venue" type="text" name="request_venue" required value="">
                </div>
                <div class="booking-field">
                  <label for="show_date">Show Date *</label>
                  <input id="show_date" type="date" name="show_date" required value="">
                </div>
                <div class="booking-field">
                  <label for="show_time">Show Time *</label>
                  <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                    <input id="show_time_start" type="time" name="show_time_start" required value="">
                    <input id="show_time_end" type="time" name="show_time_end" required value="">
                  </div>
                  <input id="show_time" type="hidden" name="show_time" value="">
                  <div class="time-range-preview" id="show_time_preview">Select start and end time.</div>
                </div>
                <div class="booking-field">
                  <label for="present_count">Expected Present Count</label>
                  <input id="present_count" type="number" name="present_count" min="0" value="">
                </div>
                <div class="booking-field" style="grid-column: 1 / -1;">
                  <label for="request_notes">Other Required Details</label>
                  <textarea id="request_notes" name="request_notes" rows="3" placeholder="Mention any additional requirements for the show."></textarea>
                </div>
              </div>
              <button class="btn btn-primary" type="submit"><i class='bx bx-send'></i> Send Request to Artist</button>
            </form>
            <p class="hint">After artist approval, card payment with PayHere will be enabled for accepted requests.</p>

            <?php if ($bookingStatus === 'pending'): ?>
              <p class="hint" style="font-weight:600;">Your request is pending artist approval.</p>
              <p class="hint">You can pay only after the artist accepts your request.</p>
            <?php elseif ($bookingStatus === 'accepted'): ?>
              <p class="hint" style="color:#176e2a; font-weight:600;">Your request has been accepted by the artist.</p>
              <p class="hint">Complete payment to confirm your showing.</p>
            <?php elseif ($bookingStatus === 'confirmed'): ?>
              <p class="hint" style="color:#176e2a; font-weight:600;">Payment completed. Your showing request is confirmed.</p>
            <?php elseif ($bookingStatus !== 'none'): ?>
              <p class="hint">Current request status: <?= htmlspecialchars(ucfirst($bookingStatus)) ?></p>
            <?php endif; ?>

            <?php if ($hasBooking && $bookingStatus !== 'none'): ?>
              <p class="hint">Latest request status: <?= htmlspecialchars(ucfirst($bookingStatus)) ?>.</p>
            <?php endif; ?>
          </div>
        </div>

        <div class="card section">
          <div class="section-head">
            <h2>Payment</h2>
            <span class="section-badge"><i class='bx bx-wallet'></i> PayHere</span>
          </div>
          <div class="detail-grid">
            <div class="info-panel">
              <h3>Charge Summary</h3>
              <div class="info-list">
                <div class="info-row"><strong>Amount</strong><span>LKR <?= htmlspecialchars($paymentAmountDisplay) ?></span></div>
                <div class="info-row"><strong>Payment Status</strong><span><?= $bookingStatus === 'confirmed' ? 'Completed' : 'Pending' ?></span></div>
                <div class="info-row"><strong>Method</strong><span>PayHere</span></div>
              </div>
            </div>
            <div class="info-panel">
              <h3>Action</h3>
              <?php if ($bookingStatus === 'accepted'): ?>
                <button class="btn btn-primary" id="payNowBtn" type="button" style="margin-top: 8px; width: 100%; justify-content: center;"><i class='bx bx-credit-card'></i> Pay with PayHere</button>
                <p class="payment-note">Complete payment to confirm your showing.</p>
              <?php elseif ($bookingStatus === 'confirmed'): ?>
                <p class="payment-note" style="color:#176e2a; font-weight:600;">Payment completed. Your showing request is confirmed.</p>
              <?php else: ?>
                <p class="payment-note">Payment becomes available after the artist accepts your request.</p>
              <?php endif; ?>
            </div>
          </div>
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
    (function () {
      const startInput = document.getElementById('show_time_start');
      const endInput = document.getElementById('show_time_end');
      const hiddenInput = document.getElementById('show_time');
      const preview = document.getElementById('show_time_preview');
      const defaultDurationMinutes = <?= (int)($d->duration_minutes ?? 0) ?>;
      let endWasAutoSet = false;

      if (!startInput || !endInput || !hiddenInput || !preview) {
        return;
      }

      const addMinutesToTime = function (timeValue, minutesToAdd) {
        if (!timeValue || !/^\d{2}:\d{2}$/.test(timeValue) || minutesToAdd <= 0) {
          return '';
        }

        const parts = timeValue.split(':');
        const hour = parseInt(parts[0], 10);
        const minute = parseInt(parts[1], 10);
        const totalMinutes = ((hour * 60) + minute + minutesToAdd) % (24 * 60);
        const endHour = Math.floor(totalMinutes / 60);
        const endMinute = totalMinutes % 60;
        const hh = String(endHour).padStart(2, '0');
        const mm = String(endMinute).padStart(2, '0');

        return hh + ':' + mm;
      };

      const toHuman = function (timeValue) {
        if (!timeValue || !/^\d{2}:\d{2}$/.test(timeValue)) {
          return '';
        }

        const parts = timeValue.split(':');
        let hh = parseInt(parts[0], 10);
        const mm = parts[1];
        const suffix = hh >= 12 ? 'PM' : 'AM';

        if (hh === 0) {
          hh = 12;
        } else if (hh > 12) {
          hh -= 12;
        }

        return hh + ':' + mm + ' ' + suffix;
      };

      const syncShowTime = function () {
        const start = startInput.value;
        const end = endInput.value;

        if (start && end) {
          const startLabel = toHuman(start);
          const endLabel = toHuman(end);
          hiddenInput.value = startLabel + ' to ' + endLabel;
          preview.textContent = 'Selected: ' + hiddenInput.value;
          preview.style.color = '#1f6f35';
        } else {
          hiddenInput.value = '';
          preview.textContent = 'Select start and end time.';
          preview.style.color = '#8b6a21';
        }
      };

      startInput.addEventListener('input', function () {
        if (defaultDurationMinutes > 0 && startInput.value && (!endInput.value || endWasAutoSet)) {
          const computedEnd = addMinutesToTime(startInput.value, defaultDurationMinutes);
          if (computedEnd) {
            endInput.value = computedEnd;
            endWasAutoSet = true;
          }
        }

        syncShowTime();
      });

      endInput.addEventListener('input', function () {
        endWasAutoSet = false;
        syncShowTime();
      });

      if (defaultDurationMinutes > 0 && startInput.value && !endInput.value) {
        const initialEnd = addMinutesToTime(startInput.value, defaultDurationMinutes);
        if (initialEnd) {
          endInput.value = initialEnd;
          endWasAutoSet = true;
        }
      }

      syncShowTime();

      const form = startInput.closest('form');
      if (form) {
        form.addEventListener('submit', function (event) {
          if (startInput.value && endInput.value && startInput.value >= endInput.value) {
            event.preventDefault();
            preview.textContent = 'End time must be later than start time.';
            preview.style.color = '#a3202c';
            endInput.focus();
          }
        });
      }
    })();
  </script>

  <?php if ($canMakePayment && $bookingId > 0): ?>
  <script>
    (function () {
      const payBtn = document.getElementById('payNowBtn');
      if (!payBtn) {
        return;
      }

      payBtn.addEventListener('click', function () {
        if (typeof payhere === 'undefined') {
          alert('PayHere is not available right now. Please refresh and try again.');
          return;
        }

        payBtn.disabled = true;

        const payload = 'booking_id=<?= (int)$bookingId ?>';
        fetch('<?= ROOT ?>/BrowseDramas/createShowPayment', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: payload
        })
          .then(function (response) {
            return response.json();
          })
          .then(function (data) {
            if (!data.success) {
              alert(data.error || 'Unable to initialize payment.');
              payBtn.disabled = false;
              return;
            }

            const payment = {
              sandbox: <?= json_encode((bool)($payhereConfig['sandbox'] ?? false)) ?>,
              merchant_id: <?= json_encode((string)($payhereConfig['merchant_id'] ?? '')) ?>,
              return_url: '<?= ROOT ?>/BrowseDramas/payment_return?order_id=' + encodeURIComponent(data.order_id) + '&drama_id=<?= (int)$d->id ?>',
              cancel_url: '<?= ROOT ?>/BrowseDramas/bookShowings/<?= (int)$d->id ?>',
              notify_url: '<?= ROOT ?>/BrowseDramas/payment_notify',
              order_id: data.order_id,
              items: data.title,
              amount: data.amount,
              currency: 'LKR',
              hash: data.hash,
              first_name: <?= json_encode($audienceFirstName) ?>,
              last_name: <?= json_encode($audienceLastName) ?>,
              email: <?= json_encode((string)($audienceUser->email ?? 'audience@example.com')) ?>,
              phone: <?= json_encode((string)($audienceUser->phone ?? '0770000000')) ?>,
              address: 'Sri Lanka',
              city: 'Colombo',
              country: 'Sri Lanka'
            };

            payhere.onCompleted = function () {
              window.location.href = payment.return_url;
            };

            payhere.onDismissed = function () {
              payBtn.disabled = false;
            };

            payhere.onError = function (error) {
              alert('Payment error: ' + error);
              payBtn.disabled = false;
            };

            payhere.startPayment(payment);
          })
          .catch(function () {
            alert('Payment initialization failed. Please try again.');
            payBtn.disabled = false;
          });
      });
    })();
  </script>
  <?php endif; ?>
</body>
</html>
