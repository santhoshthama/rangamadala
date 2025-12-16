
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title><?= APP_NAME ?></title>
    <!-- Main CSS -->
    <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/Home.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" />
    <link rel="shortcut icon" href="<?php echo ROOT;?>/assets/images/Rangamadala logo.png" type="image/x-icon">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
</head>

<body>

    <!-- Header Section -->
    <section class="header">
        <nav>
            <a href="#"><img src="<?= ROOT ?>/assets/images/Rangamadala logo.png" alt="Rangamadala Logo"></a>

            <div class="nav-links">
                <ul>
                    <li><a href="index.html">HOME</a></li>
                    <li><a href="#about">ABOUT US</a></li>
                    <li><a href="#drama-class">DRAMA CLASS</a></li>
                    <li><a href="#blog">BLOG</a></li>
                    <li><a href="#contact">CONTACT US</a></li>
                    <li><a href="<?= ROOT ?>/Login" class="btn login-btn">Log In</a></li>
                    <li><a href="<?= ROOT ?>/Signup" class="btn signup-btn">Sign Up</a></li>
                </ul>

            </div>
        </nav>

        <div class="text-box">
            <h1>RANGAMADALA</h1>
            <h1>Drama Connectivity Platform</h1>
            <p>Connecting Sri Lanka’s Drama Talent in One Place</p>
            <a href="<?= ROOT ?>/Audiencedashboard" class="hero-btn">Visit Us To Know More</a>
        </div>
        <section class="drama-swiper">
            <div class="swiper-container">
                <div class="swiper-wrapper">
                    <div class="swiper-slide"><img src="<?= ROOT ?>/assets/images/drama1.png" alt="Drama 1"></div>
                    <div class="swiper-slide"><img src="<?= ROOT ?>/assets/images/drama2.png" alt="Drama 2"></div>
                    <div class="swiper-slide"><img src="<?= ROOT ?>/assets/images/drama3.png" alt="Drama 3"></div>
                    <div class="swiper-slide"><img src="<?= ROOT ?>/assets/images/drama4.png" alt="Drama 4"></div>
                    <div class="swiper-slide"><img src="<?= ROOT ?>/assets/images/drama5.png" alt="Drama 5"></div>
                </div>
            </div>
        </section>
    </section>

    <!-- Drama Posters Card Swiper Section -->



        <!-- About Section -->
    <section class="about-section" id="about">
        <div class="about-container">
            <h2>About Ragamadala</h2>
            <p>
                Ragamadala is Sri Lanka’s first all-in-one drama connectivity platform that brings together artists, directors, audiences, and production teams. 
                Our goal is to build a thriving community where creativity meets opportunity from performing on stage to managing behind the curtain.
            </p>
            <a href="<?= ROOT ?>/Admindashboard" class="about-btn">Learn More</a>
        </div>
    </section>

    <!-- Community Section -->
    <section class="community-section" id="drama-class">
        <h2>Join Our Drama Community</h2>
        <div class="community-grid">
            <div class="community-card">
                <i class="fas fa-masks-theater"></i>
                <h3>Weekly Acting Workshops</h3>
                <p>Improve your acting and stage presence with our hands-on training sessions.</p>
            </div>
            <div class="community-card">
                <i class="fas fa-lightbulb"></i>
                <h3>Creative Collaboration</h3>
                <p>Directors and artists can collaborate on new scripts and live performances.</p>
            </div>
            <div class="community-card">
                <i class="fas fa-handshake"></i>
                <h3>Networking Events</h3>
                <p>Connect with producers, costume designers, and stage service providers.</p>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section class="gallery-section" id="gallery">
        <h2>Stage Highlights</h2>
        <div class="gallery-grid">
            <img src="<?= ROOT ?>/assets/images/stagePer.png" alt="Stage Performance">
            <img src="<?= ROOT ?>/assets/images/Rehersal.png" alt="Rehearsal">
            <img src="<?= ROOT ?>/assets/images/AudienceView.png" alt="Audience View">
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials-section">
        <h2>What Our Members Say</h2>
        <div class="testimonials-container">
            <div class="testimonial">
                <p>“Ragamadala helped me find my first acting opportunity. The platform is amazing for upcoming artists!”</p>
                <h4>- Nuwani, Artist</h4>
            </div>
            <div class="testimonial">
                <p>“Managing my stage team became so much easier. Great platform for directors and managers!”</p>
                <h4>- Sahan, Director</h4>
            </div>
            <div class="testimonial">
                <p>“As an audience member, I can easily book shows and discover new performances every week.”</p>
                <h4>- Tharindu, Audience</h4>
            </div>
        </div>
    </section>

    <!-- Join Now CTA Section -->



    <!-- Footer Section -->
    <?php require APPROOT."/views/includes/footer.php"?>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>

    <!-- Initialize Swiper -->
    <script>
        const swiper = new Swiper('.swiper-container', {
            loop: true,
            slidesPerView: 3,
            spaceBetween: 20,
            centeredSlides: true,
            autoplay: {
                delay: 2500,
                disableOnInteraction: false,
            },
            breakpoints: {
                0: {
                    slidesPerView: 1,
                },
                768: {
                    slidesPerView: 2,
                },
                1024: {
                    slidesPerView: 3,
                }
            }
        });
    </script>

</body>

</html>
