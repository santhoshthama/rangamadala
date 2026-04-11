<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($data['drama']->title ?? 'Drama Details') ?> - <?= APP_NAME ?></title>
    <link rel="shortcut icon" href="<?= ROOT ?>/assets/images/Rangamadala logo.png" type="image/x-icon">
    <!-- Font Awesome -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-bg: #faf8f3;
            --secondary-bg: #f5f0e8;
            --accent-color: #d4af37;
            --accent-dark: #ba8e23;
            --accent-light: #e8d5a8;
            --text-dark: #3d2817;
            --text-light: #5c4a3a;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background: var(--primary-bg);
            min-height: 100vh;
        }
        
        /* Header */
        .header {
            background: linear-gradient(135deg, var(--text-dark) 0%, #1a0f0a 100%);
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 3px solid var(--accent-color);
        }
        
        .header-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }
        
        .header-logo img {
            width: 50px;
            height: 50px;
        }
        
        .header-logo span {
            color: var(--accent-color);
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 1px;
        }
        
        .header-actions {
            display: flex;
            gap: 15px;
        }
        
        .btn {
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }
        
        .btn-outline {
            border: 2px solid var(--accent-color);
            color: var(--accent-color);
            background: transparent;
        }
        
        .btn-outline:hover {
            background: var(--accent-color);
            color: var(--text-dark);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--accent-color), var(--accent-dark));
            color: var(--text-dark);
            border: none;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(212, 175, 55, 0.4);
        }
        
        /* Main Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        /* Back Button */
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--text-light);
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 30px;
            transition: color 0.3s ease;
        }
        
        .back-btn:hover {
            color: var(--accent-dark);
        }
        
        /* Drama Card */
        .drama-card {
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 40px rgba(212, 175, 55, 0.15);
            border: 2px solid rgba(212, 175, 55, 0.2);
        }
        
        .drama-hero {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }
        
        .drama-image-container {
            position: relative;
            height: 450px;
            overflow: hidden;
        }
        
        .drama-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .placeholder-image {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--secondary-bg) 0%, var(--accent-light) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 80px;
            color: var(--accent-dark);
        }
        
        .drama-info {
            padding: 40px 40px 40px 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .drama-category {
            display: inline-block;
            background: linear-gradient(135deg, var(--accent-light), rgba(212, 175, 55, 0.3));
            color: var(--text-dark);
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 15px;
            width: fit-content;
        }
        
        .drama-title {
            font-size: 36px;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 20px;
            line-height: 1.2;
        }
        
        .drama-meta {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 25px;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--text-light);
            font-size: 15px;
        }
        
        .meta-item i {
            color: var(--accent-dark);
            font-size: 18px;
            width: 24px;
        }
        
        /* Rating */
        .rating-display {
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(212, 175, 55, 0.1);
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            border: 1px solid rgba(212, 175, 55, 0.2);
        }
        
        .rating-stars {
            color: var(--accent-color);
            font-size: 24px;
        }
        
        .rating-value {
            font-size: 28px;
            font-weight: 700;
            color: var(--accent-dark);
        }
        
        .rating-count {
            color: var(--text-light);
            font-size: 14px;
        }
        
        /* Price */
        .drama-price {
            font-size: 28px;
            font-weight: 700;
            color: var(--accent-dark);
            margin-bottom: 25px;
        }
        
        .drama-price span {
            font-size: 16px;
            color: var(--text-light);
            font-weight: 400;
        }
        
        /* Actions */
        .drama-actions {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        /* Description Section */
        .drama-description {
            padding: 40px;
            border-top: 2px solid rgba(212, 175, 55, 0.15);
        }
        
        .drama-description h3 {
            font-size: 24px;
            color: var(--text-dark);
            margin-bottom: 15px;
            font-weight: 700;
        }
        
        .drama-description p {
            color: var(--text-light);
            font-size: 16px;
            line-height: 1.8;
        }
        
        /* CTA Section */
        .cta-section {
            background: linear-gradient(135deg, var(--text-dark) 0%, #1a0f0a 100%);
            padding: 50px 40px;
            text-align: center;
            margin-top: 40px;
            border-radius: 20px;
        }
        
        .cta-section h3 {
            color: var(--accent-color);
            font-size: 28px;
            margin-bottom: 15px;
        }
        
        .cta-section p {
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 25px;
            font-size: 16px;
        }
        
        .cta-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        /* Responsive */
        @media (max-width: 900px) {
            .drama-hero {
                grid-template-columns: 1fr;
            }
            
            .drama-image-container {
                height: 300px;
            }
            
            .drama-info {
                padding: 30px;
            }
            
            .drama-title {
                font-size: 28px;
            }
            
            .header {
                padding: 15px 20px;
            }
            
            .header-logo span {
                display: none;
            }
        }
        
        @media (max-width: 600px) {
            .header-actions {
                gap: 10px;
            }
            
            .btn {
                padding: 8px 16px;
                font-size: 13px;
            }
            
            .drama-actions {
                flex-direction: column;
            }
            
            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <a href="<?= ROOT ?>/Home" class="header-logo">
            <img src="<?= ROOT ?>/assets/images/Rangamadala logo.png" alt="Rangamadala">
            <span>RANGAMADALA</span>
        </a>
        <div class="header-actions">
            <a href="<?= ROOT ?>/Login" class="btn btn-outline">
                <i class="bx bx-sign-in-alt"></i> Log In
            </a>
            <a href="<?= ROOT ?>/Signup" class="btn btn-primary">
                <i class="bx bx-user-plus"></i> Sign Up
            </a>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container">
        <a href="<?= ROOT ?>/Home" class="back-btn">
            <i class="bx bx-arrow-left"></i> Back to Home
        </a>

        <?php if (!empty($data['drama'])): $d = $data['drama']; ?>
            <div class="drama-card">
                <div class="drama-hero">
                    <div class="drama-image-container">
                        <?php if (!empty($d->image)): ?>
                            <img class="drama-image" src="<?= ROOT ?>/uploads/dramas/<?= htmlspecialchars($d->image) ?>" alt="<?= htmlspecialchars($d->title) ?>">
                        <?php else: ?>
                            <div class="placeholder-image">
                                <i class="bx bx-theater-masks"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="drama-info">
                        <span class="drama-category"><?= htmlspecialchars($d->category_name ?? 'Drama') ?></span>
                        <h1 class="drama-title"><?= htmlspecialchars($d->title) ?></h1>
                        
                        <div class="drama-meta">
                            <?php if (!empty($d->event_date)): ?>
                                <div class="meta-item">
                                    <i class="bx bx-calendar-alt"></i>
                                    <span><?= date('F d, Y', strtotime($d->event_date)) ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($d->event_time)): ?>
                                <div class="meta-item">
                                    <i class="bx bx-clock"></i>
                                    <span><?= htmlspecialchars($d->event_time) ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($d->venue)): ?>
                                <div class="meta-item">
                                    <i class="bx bx-map-marker-alt"></i>
                                    <span><?= htmlspecialchars($d->venue) ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($d->creator_name)): ?>
                                <div class="meta-item">
                                    <i class="bx bx-user"></i>
                                    <span>By <?= htmlspecialchars($d->creator_name) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (!empty($data['rating_summary']) && $data['rating_summary']->total_ratings > 0): ?>
                            <div class="rating-display">
                                <span class="rating-stars"><i class="bx bx-star"></i></span>
                                <span class="rating-value"><?= number_format($data['rating_summary']->average_rating, 1) ?></span>
                                <span class="rating-count">(<?= $data['rating_summary']->total_ratings ?> <?= $data['rating_summary']->total_ratings == 1 ? 'rating' : 'ratings' ?>)</span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="drama-price">
                            LKR <?= number_format($d->ticket_price ?? 0, 2) ?>
                            <span>/ ticket</span>
                        </div>
                        <?php if (!empty($d->showing_prices)): ?>
                            <div class="form-hint" style="margin-top: 8px; color: var(--text-light);">
                                Showing prices: <?= htmlspecialchars($d->showing_prices) ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="drama-actions">
                            <a href="<?= ROOT ?>/Login" class="btn btn-primary">
                                <i class="bx bx-ticket-alt"></i> Book Tickets
                            </a>
                            <a href="<?= ROOT ?>/Signup" class="btn btn-outline">
                                <i class="bx bx-user-plus"></i> Create Account
                            </a>
                        </div>
                    </div>
                </div>
                
                <?php if (!empty($d->description)): ?>
                    <div class="drama-description">
                        <h3>About This Drama</h3>
                        <p><?= nl2br(htmlspecialchars($d->description)) ?></p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- CTA Section -->
            <div class="cta-section">
                <h3>Want to Book Tickets or Rate This Drama?</h3>
                <p>Create a free account to book tickets, rate dramas, and connect with Sri Lanka's drama community!</p>
                <div class="cta-buttons">
                    <a href="<?= ROOT ?>/Signup" class="btn btn-primary">
                        <i class="bx bx-user-plus"></i> Sign Up Free
                    </a>
                    <a href="<?= ROOT ?>/Login" class="btn btn-outline">
                        <i class="bx bx-sign-in-alt"></i> Already Have Account? Log In
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="drama-card" style="padding: 60px; text-align: center;">
                <i class="bx bx-theater-masks" style="font-size: 60px; color: var(--accent-light); margin-bottom: 20px;"></i>
                <h2 style="color: var(--text-dark); margin-bottom: 15px;">Drama Not Found</h2>
                <p style="color: var(--text-light); margin-bottom: 25px;">The drama you're looking for doesn't exist or has been removed.</p>
                <a href="<?= ROOT ?>/Home" class="btn btn-primary">
                    <i class="bx bx-home"></i> Back to Home
                </a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
