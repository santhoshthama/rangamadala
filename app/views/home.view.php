<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>
    <!-- Main CSS -->
    <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/Home.css">
    <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/toast.css">
    <!-- Font Awesome -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<link rel="shortcut icon" href="<?= ROOT ?>/assets/images/Rangamadala logo.png" type="image/x-icon">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
</head>

<body>
    <!-- Toast Notification Script -->
    <script src="<?= ROOT ?>/assets/JS/toast.js"></script>
    <?php if (!empty($_SESSION['success_message'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            toastSuccess('<?= addslashes($_SESSION['success_message']); ?>');
        });
    </script>
    <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['error_message'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            toastError('<?= addslashes($_SESSION['error_message']); ?>');
        });
    </script>
    <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <!-- Header Section -->
    <section class="header">
        <nav>
            <a href="#"><img src="<?= ROOT ?>/assets/images/Rangamadala logo.png" alt="Rangamadala Logo"></a>
            <div class="nav-links">
                <ul>
                    <li><a href="#about">ABOUT US</a></li>
                    <li><a href="#stage-highlights">STAGE HIGHLIGHTS</a></li>
                    <li><a href="#member-comments">COMMENTS</a></li>
                    <li><a href="#contact-bottom">CONTACT US</a></li>
                    <li><a href="<?= ROOT ?>/Login" class="btn login-btn">Log In</a></li>
                    <li><a href="<?= ROOT ?>/Signup" class="btn signup-btn">Sign Up</a></li>
                </ul>
            </div>
        </nav>

        <div class="text-box">
            <h1>RANGAMADALA</h1>
            <h1>Drama Connectivity Platform</h1>
            <p>Connecting Sri Lanka's Drama Talent in One Place</p>
        </div>

        <!-- Swiper Section -->
        <div class="drama-swiper">
            <div class="swiper-container">
                <div class="swiper-wrapper">
                    <?php if (!empty($swiperSlides)): ?>
                        <?php foreach ($swiperSlides as $slide): ?>
                            <div class="swiper-slide">
                                <img src="<?= ROOT ?>/<?= htmlspecialchars($slide->image_path) ?>" alt="<?= htmlspecialchars($slide->title ?? 'Drama') ?>">
                                <div class="slide-overlay">
                                    <div class="slide-content">
                                        <h3 class="slide-title"><?= htmlspecialchars($slide->title ?? 'Drama') ?></h3>
                                        <?php if (!empty($slide->drama_id)): ?>
                                            <a href="<?= ROOT ?>/Home/drama/<?= $slide->drama_id ?>" class="view-more-btn">
                                                <i class="bx bx-play-circle"></i> View More
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Fallback to default images if no slides in database -->
                        <div class="swiper-slide"><img src="<?= ROOT ?>/assets/images/drama1.png" alt="Drama 1"></div>
                        <div class="swiper-slide"><img src="<?= ROOT ?>/assets/images/drama2.png" alt="Drama 2"></div>
                        <div class="swiper-slide"><img src="<?= ROOT ?>/assets/images/drama3.png" alt="Drama 3"></div>
                        <div class="swiper-slide"><img src="<?= ROOT ?>/assets/images/drama4.png" alt="Drama 4"></div>
                        <div class="swiper-slide"><img src="<?= ROOT ?>/assets/images/drama5.png" alt="Drama 5"></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about-section" id="about">
        <div class="about-container">
            <h2>About Rangamadala</h2>
            <p>
                Rangamadala is Sri Lanka's first all-in-one drama connectivity platform that brings together artists, directors, audiences, and production teams. 
                Our goal is to build a thriving community where creativity meets opportunity from performing on stage to managing behind the curtain.
            </p>
            <a href="<?= ROOT ?>/Home/learnMore" class="about-btn">Learn More</a>
        </div>
    </section>

    <!-- Community Section -->
    <section class="community-section" id="drama-class">
        <h2>Join Our Drama Community</h2>
        <div class="community-grid">
            <div class="community-card">
                <i class="bx bx-masks-theater"></i>
                <h3>Weekly Acting Workshops</h3>
                <p>Improve your acting and stage presence with our hands-on training sessions.</p>
            </div>
            <div class="community-card">
                <i class="bx bx-lightbulb"></i>
                <h3>Creative Collaboration</h3>
                <p>Directors and artists can collaborate on new scripts and live performances.</p>
            </div>
            <div class="community-card">
                <i class="bx bx-handshake"></i>
                <h3>Networking Events</h3>
                <p>Connect with producers, costume designers, and stage service providers.</p>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section class="gallery-section" id="stage-highlights">
        <h2>Stage Highlights</h2>
        <div class="gallery-grid">
            <?php if (!empty($galleryImages)): ?>
                <?php foreach ($galleryImages as $image): ?>
                    <img src="<?= ROOT ?>/<?= htmlspecialchars($image->image_path) ?>" alt="<?= htmlspecialchars($image->alt_text ?? 'Gallery') ?>">
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Fallback to default images -->
                <img src="<?= ROOT ?>/assets/images/stagePer.png" alt="Stage Performance">
                <img src="<?= ROOT ?>/assets/images/Rehersal.png" alt="Rehearsal">
                <img src="<?= ROOT ?>/assets/images/AudienceView.png" alt="Audience View">
            <?php endif; ?>
        </div>
    </section>

        <!-- Testimonials Section -->
    <section class="testimonials-section" id="member-comments">
        <h2 class="testimonials-title">What Our Members Say</h2>
        <div class="testimonials-container">
            <?php if (!empty($testimonials)): ?>
                <?php foreach ($testimonials as $t): ?>
                    <div class="testimonial-card">
                        <div class="testimonial-img">
                            <?php if (!empty($t->image_path)): ?>
                                <img src="<?= strpos($t->image_path, 'http') === 0 ? $t->image_path : ROOT . '/' . htmlspecialchars($t->image_path) ?>" alt="<?= htmlspecialchars($t->name) ?>">
                            <?php else: ?>
                                <div class="testimonial-avatar-placeholder"><?= strtoupper(substr($t->name, 0, 1)) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="testimonial-content">
                            <h3 class="t-name"><?= htmlspecialchars($t->name) ?> <span><?= htmlspecialchars($t->role) ?></span></h3>
                            <p>"<?= htmlspecialchars($t->message) ?>"</p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="testimonial-card">
                    <div class="testimonial-img">
                        <img src="https://i.postimg.cc/VNs6dtw4/profile2.jpg" alt="Nuwan">
                    </div>
                    <div class="testimonial-content">
                        <h3 class="t-name">Nuwan <span>Artist</span></h3>
                        <p>"Rangamadala helped me find my first acting opportunity. The platform is amazing for upcoming artists!"</p>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-img">
                        <img src="https://i.postimg.cc/XYkqj8Rp/profile3.jpg" alt="Sahan">
                    </div>
                    <div class="testimonial-content">
                        <h3 class="t-name">Nirahsha <span>Director</span></h3>
                        <p>"Managing my stage team became so much easier. Great platform for directors and managers!"</p>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-img">
                        <img src="https://i.postimg.cc/g0M0R0kp/profile1.jpg" alt="Tharindu">
                    </div>
                    <div class="testimonial-content">
                        <h3 class="t-name">Tharindu <span>Audience</span></h3>
                        <p>"As an audience member, I can easily book shows and discover new performances every week."</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <div id="contact-bottom">
        <?php require APPROOT."/views/includes/footer.php"?>
    </div>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script>
        const swiper = new Swiper('.swiper-container', {
            loop: true,
            slidesPerView: 3,
            spaceBetween: 20,
            centeredSlides: true,
            autoplay: { delay: 2500, disableOnInteraction: false },
            breakpoints: { 0: { slidesPerView: 1 }, 768: { slidesPerView: 2 }, 1024: { slidesPerView: 3 } }
        });
    </script>

</body>
</html>

