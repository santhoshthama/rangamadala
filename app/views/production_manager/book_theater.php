<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theater Bookings - Rangamadala</title>
    <link rel="stylesheet" href="/Rangamadala/public/assets/CSS/ui-theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="logo">
            <h2>🎭</h2>
        </div>
        <ul class="menu">
            <li>
                <a href="<?= ROOT ?>/production_manager/dashboard?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/production_manager/manage_services?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>">
                    <i class="fas fa-briefcase"></i>
                    <span>Manage Services</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/production_manager/manage_budget?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>">
                    <i class="fas fa-chart-bar"></i>
                    <span>Budget Management</span>
                </a>
            </li>
            <li class="active">
                <a href="<?= ROOT ?>/production_manager/book_theater?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>">
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
    <main class="main--content">
        <a href="<?= ROOT ?>/production_manager/dashboard?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>" class="back-button">
            <i class="fas fa-arrow-left"></i>
            Back to Dashboard
        </a>

        <!-- Header -->
        <div class="header--wrapper">
            <div class="header--title">
                <span><?= isset($drama->drama_name) ? esc($drama->drama_name) : 'Drama' ?></span>
                <h2>Theater Bookings</h2>
            </div>
            <div class="header-controls">
                <button class="btn btn-primary" onclick="openBookTheaterModal()">
                    <i class="fas fa-plus"></i>
                    Book Theater
                </button>
            </div>
        </div>

        <!-- Theater Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3><?= isset($totalBookings) ? $totalBookings : '0' ?></h3>
                <p>Total Bookings</p>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, var(--success), #1f9b3b);">
                <h3><?= isset($confirmedCount) ? $confirmedCount : '0' ?></h3>
                <p>Confirmed</p>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, var(--warning), #e0a800);">
                <h3><?= isset($pendingCount) ? $pendingCount : '0' ?></h3>
                <p>Pending</p>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, var(--info), #138496);">
                <h3>LKR <?= isset($totalCost) ? number_format($totalCost) : '0' ?></h3>
                <p>Total Theater Cost</p>
            </div>
        </div>

        <!-- Theater Bookings List -->
        <div class="content" style="padding: 28px;">
            <h3 style="margin-bottom: 16px;">Your Theater Bookings</h3>
            
            <?php if (isset($theaterBookings) && is_array($theaterBookings) && !empty($theaterBookings)): ?>
                <?php foreach ($theaterBookings as $booking): ?>
                    <?php 
                        $statusClass = 'pending';
                        $statusText = 'Pending Confirmation';
                        $bgColor = '#fffbf0';
                        $borderColor = 'var(--warning)';
                        
                        if (isset($booking->status)) {
                            if ($booking->status === 'confirmed') {
                                $statusClass = 'assigned';
                                $statusText = 'Confirmed';
                                $bgColor = '#f0f7f4';
                                $borderColor = 'var(--success)';
                            } elseif ($booking->status === 'cancelled') {
                                $statusClass = 'rejected';
                                $statusText = 'Cancelled';
                                $bgColor = '#fef0f0';
                                $borderColor = 'var(--danger)';
                            }
                        }
                    ?>
                    <div class="card-section" style="margin-bottom: 20px; background: <?= $bgColor ?>; border-left-color: <?= $borderColor ?>;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                            <div>
                                <h3 style="color: var(--ink); margin-bottom: 4px;">
                                    <i class="fas fa-theater-masks" style="color: <?= $borderColor ?>>;"></i>
                                    <?= isset($booking->theater_name) ? esc($booking->theater_name) : 'Theater' ?>
                                </h3>
                                <p style="font-size: 12px; color: var(--muted);"><?= isset($booking->venue) ? esc($booking->venue) : 'Venue TBD' ?></p>
                            </div>
                            <span class="status-badge <?= $statusClass ?>"><?= $statusText ?></span>
                        </div>

                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 16px;">
                            <div>
                                <p style="font-size: 11px; color: var(--muted); text-transform: uppercase; font-weight: 700; margin-bottom: 6px;">Booking Date</p>
                                <p style="color: var(--ink); font-weight: 600;">📅 <?= isset($booking->booking_date) ? date('F d, Y', strtotime($booking->booking_date)) : 'TBD' ?></p>
                            </div>
                            <div>
                                <p style="font-size: 11px; color: var(--muted); text-transform: uppercase; font-weight: 700; margin-bottom: 6px;">Time</p>
                                <p style="color: var(--ink); font-weight: 600;">🕐 <?= isset($booking->start_time) && isset($booking->end_time) ? date('g:i A', strtotime($booking->start_time)) . ' - ' . date('g:i A', strtotime($booking->end_time)) : 'TBD' ?></p>
                            </div>
                            <div>
                                <p style="font-size: 11px; color: var(--muted); text-transform: uppercase; font-weight: 700; margin-bottom: 6px;">Capacity</p>
                                <p style="color: var(--ink); font-weight: 600;">👥 <?= isset($booking->capacity) ? number_format($booking->capacity) : 'N/A' ?> Seats</p>
                            </div>
                            <div>
                                <p style="font-size: 11px; color: var(--muted); text-transform: uppercase; font-weight: 700; margin-bottom: 6px;">Rental Cost</p>
                                <p style="color: var(--brand); font-weight: 700;">LKR <?= isset($booking->rental_cost) ? number_format($booking->rental_cost) : '0' ?></p>
                            </div>
                        </div>

                        <div style="display: flex; gap: 8px;">
                            <button class="btn btn-secondary" style="padding: 8px 14px; font-size: 12px;" onclick="viewBookingDetails(<?= isset($booking->id) ? $booking->id : 'null' ?>)">
                                <i class="fas fa-eye"></i>
                                View Details
                            </button>
                            <button class="btn btn-secondary" style="padding: 8px 14px; font-size: 12px;" onclick="editBooking(<?= isset($booking->id) ? $booking->id : 'null' ?>)">
                                <i class="fas fa-pencil-alt"></i>
                                Edit
                            </button>
                            <button class="btn btn-danger" style="padding: 8px 14px; font-size: 12px;" onclick="cancelBooking(<?= isset($booking->id) ? $booking->id : 'null' ?>)">
                                <i class="fas fa-trash"></i>
                                Cancel
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="text-align: center; padding: 60px 30px; color: var(--muted);">
                    <i class="fas fa-calendar-times" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
                    <p>No theater bookings yet. Book a theater to start planning your event.</p>
                    <button class="btn btn-primary" style="margin-top: 20px;" onclick="openBookTheaterModal()">
                        <i class="fas fa-plus"></i> Book Theater
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Book Theater Modal -->
    <div id="bookTheaterModal" class="modal">
        <div class="modal-content" style="max-width: 800px;">
            <span class="close" onclick="closeBookTheaterModal()">&times;</span>
            <h2><i class="fas fa-plus"></i> Book Theater</h2>
            
            <div class="form-group">
                <label for="theaterName">Theater</label>
                <select id="theaterName" onchange="updateTheaterDetails()">
                    <option value="">Select Theater</option>
                    <option value="elphinstone">Elphinstone Theatre - Colombo</option>
                    <option value="colombo_aud">Colombo Auditorium</option>
                    <option value="galle_face">Galle Face Hotel Theatre</option>
                    <option value="kandy_art">Kandy Arts Centre</option>
                    <option value="peradeniya">Peradeniya Open Air Theatre</option>
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label for="bookingDate">Performance Date</label>
                    <input type="date" id="bookingDate">
                </div>
                <div class="form-group">
                    <label for="bookingTime">Performance Time</label>
                    <input type="time" id="bookingTime">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label for="endTime">End Time</label>
                    <input type="time" id="endTime">
                </div>
                <div class="form-group">
                    <label for="estimatedAttendance">Estimated Attendance</label>
                    <input type="number" id="estimatedAttendance" placeholder="Expected number of audience" min="0">
                </div>
            </div>

            <div class="form-group">
                <label for="specialRequests">Special Requests / Needs</label>
                <textarea id="specialRequests" placeholder="Any special setup, equipment, or other requirements" style="min-height: 100px;"></textarea>
            </div>

            <div class="info-box">
                <strong>Theater Details</strong>
                <div id="theaterInfoDisplay" style="margin-top: 8px; display: grid; grid-template-columns: 1fr 1fr; gap: 16px; font-size: 12px;">
                    <div>
                        <p style="color: var(--muted); font-weight: 700; margin-bottom: 4px;">CAPACITY</p>
                        <p id="theaterCapacity" style="color: var(--ink); font-weight: 600;">-</p>
                    </div>
                    <div>
                        <p style="color: var(--muted); font-weight: 700; margin-bottom: 4px;">COST PER HOUR</p>
                        <p id="theaterCost" style="color: var(--brand); font-weight: 700;">-</p>
                    </div>
                    <div>
                        <p style="color: var(--muted); font-weight: 700; margin-bottom: 4px;">ESTIMATED TOTAL</p>
                        <p id="estimatedTotal" style="color: var(--brand); font-weight: 700;">-</p>
                    </div>
                    <div>
                        <p style="color: var(--muted); font-weight: 700; margin-bottom: 4px;">FACILITIES</p>
                        <p id="theaterFacilities" style="color: var(--ink); font-weight: 600;">-</p>
                    </div>
                </div>
            </div>

            <div class="modal-actions">
                <button class="btn btn-secondary" onclick="closeBookTheaterModal()">Cancel</button>
                <button class="btn btn-primary" onclick="submitTheaterBooking()">Request Booking</button>
            </div>
        </div>
    </div>

    <script src="/Rangamadala/public/assets/JS/manage-theater.js"></script>
</body>
</html>
