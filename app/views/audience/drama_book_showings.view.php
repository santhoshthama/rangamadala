<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($data['drama']->title ?? 'Book Showings') ?> - <?= APP_NAME ?></title>
  <link rel="shortcut icon" href="<?= ROOT ?>/assets/images/Rangamadala logo.png" type="image/x-icon">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <script type="text/javascript" src="https://www.payhere.lk/lib/payhere.js"></script>
  <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/audience-drama-book-showings.css">
</head>
<body class="audience-book-showings-page">
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
    $editBookingId = (int)($_GET['booking_id'] ?? 0);
    $editMode = $editBookingId > 0 && $bookingStatus === 'pending';
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
    $requestSenderNameValue = trim((string)($requestDetails['request_sender_name'] ?? $audienceFullName));
    $requestContactPhoneValue = trim((string)($requestDetails['request_contact_phone'] ?? ($audienceUser->phone ?? '')));
    $requestContactEmailValue = trim((string)($requestDetails['request_contact_email'] ?? ($audienceUser->email ?? '')));
    $requestVenueValue = trim((string)($requestDetails['request_venue'] ?? ''));
    $showDateValue = trim((string)($requestDetails['show_date'] ?? ''));
    $presentCountValue = (int)($requestDetails['present_count'] ?? 0);
    $requestNotesValue = trim((string)($requestDetails['request_notes'] ?? ''));
  ?>

  <div class="container <?= $editMode ? 'edit-mode' : '' ?>">
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
        <?php if (!$editMode): ?>
          <div class="card hero section">
            <div>
              <?php if (!empty($d->image)): ?>
                <img class="poster" src="<?= ROOT ?>/uploads/dramas/<?= htmlspecialchars($d->image) ?>" alt="<?= htmlspecialchars($d->title) ?>">
              <?php else: ?>
                <div class="poster-placeholder">
                  <i class='bx bx-movie-play'></i>
                </div>
              <?php endif; ?>
            </div>
            <div>
              <div class="section-head">
                <h2 class="description-title">Drama Description</h2>
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
        <?php endif; ?>

        <div class="card section">
          <div class="section-head">
            <h2 class="section-title"><?= $editMode ? 'Edit Show Request' : 'Request Drama' ?></h2>
            <span class="section-badge"><i class='bx bx-edit-alt'></i> <?= $editMode ? 'Update request details' : 'Submit booking request' ?></span>
          </div>

          <?php if ($bookingStatus === 'rejected'): ?>
            <p class="hint rejection-strong">Your previous request was rejected.</p>
            <p class="hint rejection-reason">Reason: <?= htmlspecialchars($rejectionReason !== '' ? $rejectionReason : 'No reason provided by artist.') ?></p>
          <?php endif; ?>

          <div class="booking-box">
            <form method="POST" action="<?= ROOT ?>/BrowseDramas/bookShowings/<?= (int)$d->id ?>">
              <?php if ($editBookingId > 0 && $bookingStatus === 'pending'): ?>
                <input type="hidden" name="booking_id" value="<?= (int)$editBookingId ?>">
              <?php endif; ?>
              <div class="booking-grid">
                <div class="booking-field">
                  <label for="request_sender_name">Request Sender Name *</label>
                  <input id="request_sender_name" type="text" name="request_sender_name" required <?= $editMode ? 'placeholder="' . htmlspecialchars($requestSenderNameValue) . '" value=""' : 'value="' . htmlspecialchars($requestSenderNameValue) . '"' ?>>
                </div>
                <div class="booking-field">
                  <label for="request_contact_phone">Contact Phone *</label>
                  <input id="request_contact_phone" type="text" name="request_contact_phone" required <?= $editMode ? 'placeholder="' . htmlspecialchars($requestContactPhoneValue) . '" value=""' : 'placeholder="Example: 0771234567" value="' . htmlspecialchars($requestContactPhoneValue) . '"' ?>>
                </div>
                <div class="booking-field">
                  <label for="request_contact_email">Contact Email</label>
                  <input id="request_contact_email" type="email" name="request_contact_email" <?= $editMode ? 'placeholder="' . htmlspecialchars($requestContactEmailValue) . '" value=""' : 'placeholder="Example: name@email.com" value="' . htmlspecialchars($requestContactEmailValue) . '"' ?>>
                </div>
                <div class="booking-field">
                  <label for="request_venue">Expected Place of Show *</label>
                  <input id="request_venue" type="text" name="request_venue" required <?= $editMode ? 'placeholder="' . htmlspecialchars($requestVenueValue) . '" value=""' : 'value="' . htmlspecialchars($requestVenueValue) . '"' ?>>
                </div>
                <div class="booking-field">
                  <label for="show_date">Show Date *</label>
                  <input id="show_date" type="date" name="show_date" required value="<?= htmlspecialchars($showDateValue) ?>">
                </div>
                <div class="booking-field">
                  <label for="show_time">Show Time *</label>
                  <div class="time-input-grid">
                    <input id="show_time_start" type="time" name="show_time_start" required value="<?= htmlspecialchars($showTimeStartValue) ?>">
                    <input id="show_time_end" type="time" name="show_time_end" required value="<?= htmlspecialchars($showTimeEndValue) ?>">
                  </div>
                  <input id="show_time" type="hidden" name="show_time" value="<?= htmlspecialchars($showTimeTextValue) ?>">
                  <div class="time-range-preview" id="show_time_preview"><?= htmlspecialchars($showTimeTextValue !== '' ? $showTimeTextValue : 'Select start and end time.') ?></div>
                </div>
                <div class="booking-field">
                  <label for="present_count">Expected Present Count</label>
                  <input id="present_count" type="number" name="present_count" min="0" <?= $editMode ? 'placeholder="' . (int)$presentCountValue . '" value=""' : 'value="' . (int)$presentCountValue . '"' ?>>
                </div>
                <div class="booking-field booking-field-full">
                  <label for="request_notes">Other Required Details</label>
                  <textarea id="request_notes" name="request_notes" rows="3" <?= $editMode ? 'placeholder="' . htmlspecialchars($requestNotesValue !== '' ? $requestNotesValue : 'Mention any additional requirements for the show.') . '"' : 'placeholder="Mention any additional requirements for the show."' ?>><?= $editMode ? '' : htmlspecialchars($requestNotesValue) ?></textarea>
                </div>
              </div>
              <button class="btn btn-primary" type="submit"><i class='bx bx-send'></i> <?= $editMode ? 'Update Request' : 'Send Request to Artist' ?></button>
            </form>
            <?php if (!$editMode): ?>
              <p class="hint">After artist approval, card payment with PayHere will be enabled for accepted requests.</p>
            <?php endif; ?>

            <?php if (!$editMode && $bookingStatus === 'pending'): ?>
              <p class="hint pending-strong">Your request is pending artist approval.</p>
              <p class="hint">You can pay only after the artist accepts your request.</p>
            <?php elseif (!$editMode && $bookingStatus === 'accepted'): ?>
              <p class="hint accepted-strong">Your request has been accepted by the artist.</p>
              <p class="hint">Complete payment to confirm your showing.</p>
            <?php elseif (!$editMode && $bookingStatus === 'confirmed'): ?>
              <p class="hint accepted-strong">Payment completed. Your showing request is confirmed.</p>
            <?php elseif (!$editMode && $bookingStatus !== 'none'): ?>
              <p class="hint">Current request status: <?= htmlspecialchars(ucfirst($bookingStatus)) ?></p>
            <?php endif; ?>

            <?php if (!$editMode && $hasBooking && $bookingStatus !== 'none'): ?>
              <p class="hint">Latest request status: <?= htmlspecialchars(ucfirst($bookingStatus)) ?>.</p>
            <?php endif; ?>
          </div>
        </div>

        <?php if (!$editMode): ?>
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
                  <button class="btn btn-primary pay-now-btn" id="payNowBtn" type="button"><i class='bx bx-credit-card'></i> Pay with PayHere</button>
                <p class="payment-note">Complete payment to confirm your showing.</p>
              <?php elseif ($bookingStatus === 'confirmed'): ?>
                  <p class="payment-note success">Payment completed. Your showing request is confirmed.</p>
              <?php else: ?>
                <p class="payment-note">Payment becomes available after the artist accepts your request.</p>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <?php endif; ?>
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
