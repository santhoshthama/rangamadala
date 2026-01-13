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
                <a href="dashboard.php?drama_id=1">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="manage_services.php?drama_id=1">
                    <i class="fas fa-briefcase"></i>
                    <span>Manage Services</span>
                </a>
            </li>
            <li>
                <a href="manage_budget.php?drama_id=1">
                    <i class="fas fa-chart-bar"></i>
                    <span>Budget Management</span>
                </a>
            </li>
            <li class="active">
                <a href="book_theater.php?drama_id=1">
                    <i class="fas fa-theater-masks"></i>
                    <span>Theater Bookings</span>
                </a>
            </li>
            <li>
                <a href="../artist/profile.php">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Profile</span>
                </a>
            </li>
            <li>
                <a href="../../public/index.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main--content">
        <a href="dashboard.php?drama_id=1" class="back-button">
            <i class="fas fa-arrow-left"></i>
            Back to Dashboard
        </a>

        <!-- Header -->
        <div class="header--wrapper">
            <div class="header--title">
                <span>Sinhabahu</span>
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
                <h3>4</h3>
                <p>Total Bookings</p>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, var(--success), #1f9b3b);">
                <h3>3</h3>
                <p>Confirmed</p>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, var(--warning), #e0a800);">
                <h3>1</h3>
                <p>Pending</p>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, var(--info), #138496);">
                <h3>LKR 1,200,000</h3>
                <p>Total Theater Cost</p>
            </div>
        </div>

        <!-- Theater Bookings List -->
        <div class="content" style="padding: 28px;">
            <h3 style="margin-bottom: 16px;">Your Theater Bookings</h3>
            
            <!-- Booking Item 1 -->
            <div class="card-section" style="margin-bottom: 20px; background: #f0f7f4; border-left-color: var(--success);">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                    <div>
                        <h3 style="color: var(--ink); margin-bottom: 4px;">
                            <i class="fas fa-theater-masks" style="color: var(--success);"></i>
                            Elphinstone Theatre
                        </h3>
                        <p style="font-size: 12px; color: var(--muted);">Premium venue with 500+ capacity</p>
                    </div>
                    <span class="status-badge assigned">Confirmed</span>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 16px;">
                    <div>
                        <p style="font-size: 11px; color: var(--muted); text-transform: uppercase; font-weight: 700; margin-bottom: 6px;">Location</p>
                        <p style="color: var(--ink); font-weight: 600;">🏢 Colombo, Sri Lanka</p>
                    </div>
                    <div>
                        <p style="font-size: 11px; color: var(--muted); text-transform: uppercase; font-weight: 700; margin-bottom: 6px;">Booking Date</p>
                        <p style="color: var(--ink); font-weight: 600;">📅 January 15, 2025</p>
                    </div>
                    <div>
                        <p style="font-size: 11px; color: var(--muted); text-transform: uppercase; font-weight: 700; margin-bottom: 6px;">Time</p>
                        <p style="color: var(--ink); font-weight: 600;">🕐 7:00 PM - 10:00 PM</p>
                    </div>
                    <div>
                        <p style="font-size: 11px; color: var(--muted); text-transform: uppercase; font-weight: 700; margin-bottom: 6px;">Capacity</p>
                        <p style="color: var(--ink); font-weight: 600;">👥 500 Seats</p>
                    </div>
                    <div>
                        <p style="font-size: 11px; color: var(--muted); text-transform: uppercase; font-weight: 700; margin-bottom: 6px;">Booking Cost</p>
                        <p style="color: var(--brand); font-weight: 700;">LKR 300,000</p>
                    </div>
                    <div>
                        <p style="font-size: 11px; color: var(--muted); text-transform: uppercase; font-weight: 700; margin-bottom: 6px;">Facilities</p>
                        <p style="color: var(--ink); font-weight: 600;">✓ Full A/C, Sound, Lighting</p>
                    </div>
                </div>

                <div style="display: flex; gap: 8px;">
                    <button class="btn btn-secondary" style="padding: 8px 14px; font-size: 12px;" onclick="viewBookingDetails(1)">
                        <i class="fas fa-eye"></i>
                        View Details
                    </button>
                    <button class="btn btn-secondary" style="padding: 8px 14px; font-size: 12px;" onclick="editBooking(1)">
                        <i class="fas fa-pencil-alt"></i>
                        Edit
                    </button>
                    <button class="btn btn-danger" style="padding: 8px 14px; font-size: 12px;" onclick="cancelBooking(1)">
                        <i class="fas fa-trash"></i>
                        Cancel
                    </button>
                </div>
            </div>

            <!-- Booking Item 2 -->
            <div class="card-section" style="margin-bottom: 20px; background: #f0f7f4; border-left-color: var(--success);">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                    <div>
                        <h3 style="color: var(--ink); margin-bottom: 4px;">
                            <i class="fas fa-theater-masks" style="color: var(--success);"></i>
                            Colombo Auditorium
                        </h3>
                        <p style="font-size: 12px; color: var(--muted);">State-of-the-art auditorium with modern amenities</p>
                    </div>
                    <span class="status-badge assigned">Confirmed</span>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 16px;">
                    <div>
                        <p style="font-size: 11px; color: var(--muted); text-transform: uppercase; font-weight: 700; margin-bottom: 6px;">Location</p>
                        <p style="color: var(--ink); font-weight: 600;">🏢 Colombo City Center</p>
                    </div>
                    <div>
                        <p style="font-size: 11px; color: var(--muted); text-transform: uppercase; font-weight: 700; margin-bottom: 6px;">Booking Date</p>
                        <p style="color: var(--ink); font-weight: 600;">📅 January 22, 2025</p>
                    </div>
                    <div>
                        <p style="font-size: 11px; color: var(--muted); text-transform: uppercase; font-weight: 700; margin-bottom: 6px;">Time</p>
                        <p style="color: var(--ink); font-weight: 600;">🕐 6:30 PM - 9:30 PM</p>
                    </div>
                    <div>
                        <p style="font-size: 11px; color: var(--muted); text-transform: uppercase; font-weight: 700; margin-bottom: 6px;">Capacity</p>
                        <p style="color: var(--ink); font-weight: 600;">👥 1000 Seats</p>
                    </div>
                    <div>
                        <p style="font-size: 11px; color: var(--muted); text-transform: uppercase; font-weight: 700; margin-bottom: 6px;">Booking Cost</p>
                        <p style="color: var(--brand); font-weight: 700;">LKR 400,000</p>
                    </div>
                    <div>
                        <p style="font-size: 11px; color: var(--muted); text-transform: uppercase; font-weight: 700; margin-bottom: 6px;">Facilities</p>
                        <p style="color: var(--ink); font-weight: 600;">✓ Premium amenities included</p>
                    </div>
                </div>

                <div style="display: flex; gap: 8px;">
                    <button class="btn btn-secondary" style="padding: 8px 14px; font-size: 12px;" onclick="viewBookingDetails(2)">
                        <i class="fas fa-eye"></i>
                        View Details
                    </button>
                    <button class="btn btn-secondary" style="padding: 8px 14px; font-size: 12px;" onclick="editBooking(2)">
                        <i class="fas fa-pencil-alt"></i>
                        Edit
                    </button>
                    <button class="btn btn-danger" style="padding: 8px 14px; font-size: 12px;" onclick="cancelBooking(2)">
                        <i class="fas fa-trash"></i>
                        Cancel
                    </button>
                </div>
            </div>

            <!-- Booking Item 3 - Pending -->
            <div class="card-section" style="background: #fffbf0; border-left-color: var(--warning);">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                    <div>
                        <h3 style="color: var(--ink); margin-bottom: 4px;">
                            <i class="fas fa-theater-masks" style="color: var(--warning);"></i>
                            Galle Face Hotel Theatre
                        </h3>
                        <p style="font-size: 12px; color: var(--muted);">Historic heritage venue with iconic charm</p>
                    </div>
                    <span class="status-badge pending">Pending Confirmation</span>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 16px;">
                    <div>
                        <p style="font-size: 11px; color: var(--muted); text-transform: uppercase; font-weight: 700; margin-bottom: 6px;">Location</p>
                        <p style="color: var(--ink); font-weight: 600;">🏢 Colombo, Beachfront</p>
                    </div>
                    <div>
                        <p style="font-size: 11px; color: var(--muted); text-transform: uppercase; font-weight: 700; margin-bottom: 6px;">Booking Date</p>
                        <p style="color: var(--ink); font-weight: 600;">📅 February 1, 2025</p>
                    </div>
                    <div>
                        <p style="font-size: 11px; color: var(--muted); text-transform: uppercase; font-weight: 700; margin-bottom: 6px;">Time</p>
                        <p style="color: var(--ink); font-weight: 600;">🕐 7:30 PM - 10:30 PM</p>
                    </div>
                    <div>
                        <p style="font-size: 11px; color: var(--muted); text-transform: uppercase; font-weight: 700; margin-bottom: 6px;">Capacity</p>
                        <p style="color: var(--ink); font-weight: 600;">👥 300 Seats</p>
                    </div>
                    <div>
                        <p style="font-size: 11px; color: var(--muted); text-transform: uppercase; font-weight: 700; margin-bottom: 6px;">Requested Cost</p>
                        <p style="color: var(--brand); font-weight: 700;">LKR 500,000</p>
                    </div>
                    <div>
                        <p style="font-size: 11px; color: var(--muted); text-transform: uppercase; font-weight: 700; margin-bottom: 6px;">Request Sent</p>
                        <p style="color: var(--ink); font-weight: 600;">⏱️ Jan 12, 2025</p>
                    </div>
                </div>

                <div style="display: flex; gap: 8px;">
                    <button class="btn btn-secondary" style="padding: 8px 14px; font-size: 12px;" onclick="viewBookingDetails(3)">
                        <i class="fas fa-eye"></i>
                        View Details
                    </button>
                    <button class="btn btn-secondary" style="padding: 8px 14px; font-size: 12px;" onclick="editBooking(3)">
                        <i class="fas fa-pencil-alt"></i>
                        Edit
                    </button>
                    <button class="btn btn-danger" style="padding: 8px 14px; font-size: 12px;" onclick="cancelBooking(3)">
                        <i class="fas fa-trash"></i>
                        Cancel
                    </button>
                </div>
            </div>
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
