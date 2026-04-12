<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learn More - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/Home.css">
    <link rel="shortcut icon" href="<?= ROOT ?>/assets/images/Rangamadala logo.png" type="image/x-icon">
    <style>
        .learn-more-wrapper {
            background: var(--primary-bg);
            padding: 52px 20px;
        }

        .learn-more-container {
            max-width: 1080px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(212, 175, 55, 0.12);
            padding: 40px;
        }

        .learn-eyebrow {
            display: inline-block;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--accent-dark);
            background: var(--gold-100);
            border: 1px solid rgba(212, 175, 55, 0.25);
            padding: 6px 12px;
            border-radius: 999px;
            margin-bottom: 16px;
        }

        .learn-title {
            color: var(--text-dark);
            font-size: 36px;
            margin-bottom: 10px;
            font-weight: 700;
            line-height: 1.25;
        }

        .learn-subtitle {
            color: var(--text-light);
            line-height: 1.9;
            margin-bottom: 28px;
            max-width: 900px;
        }

        .learn-section {
            margin-top: 34px;
        }

        .learn-section h2 {
            color: var(--text-dark);
            font-size: 24px;
            margin-bottom: 14px;
        }

        .learn-section p {
            color: var(--text-light);
            line-height: 1.85;
            margin-bottom: 14px;
        }

        .learn-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
            margin-top: 14px;
        }

        .learn-card {
            background: #fff;
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 12px;
            padding: 16px;
        }

        .learn-card h3 {
            color: var(--text-dark);
            font-size: 17px;
            margin-bottom: 8px;
        }

        .learn-card p {
            margin: 0;
            font-size: 14px;
            color: var(--text-light);
            line-height: 1.7;
        }

        .learn-list {
            color: var(--text-light);
            padding-left: 20px;
            line-height: 1.8;
            display: grid;
            gap: 8px;
        }

        .learn-actions {
            margin-top: 30px;
            display: flex;
            justify-content: flex-start;
        }

        .learn-back,
        .learn-home {
            display: inline-block;
            text-decoration: none;
            background: linear-gradient(135deg, var(--accent-color), var(--accent-light));
            color: var(--text-dark);
            padding: 11px 20px;
            border-radius: 8px;
            font-weight: 600;
            border: 1px solid rgba(212, 175, 55, 0.35);
        }

        .learn-back:hover,
        .learn-home:hover {
            background: var(--accent-dark);
            color: #fff;
        }

        @media (max-width: 900px) {
            .learn-more-container {
                padding: 28px;
            }

            .learn-grid {
                grid-template-columns: 1fr;
            }

            .learn-title {
                font-size: 30px;
            }
        }
    </style>
</head>

<body>
    <div class="learn-more-wrapper">
        <div class="learn-more-container">
            <span class="learn-eyebrow">Project Overview</span>
            <h1 class="learn-title">Rangamadala: Drama Connectivity Platform</h1>
            <p class="learn-subtitle">
                Rangamadala is a web-based platform designed to modernize collaboration in the Sri Lankan drama and
                theater industry. It provides one trusted digital space where directors, artists, production managers,
                service providers, audiences, and administrators can coordinate production work more efficiently.
            </p>

            <div class="learn-section">
                <h2>Industry Problem</h2>
                <p>
                    Sri Lankan stage drama productions currently rely on fragmented communication channels such as
                    social media, phone calls, and in-person coordination. This creates delays, weak visibility, and
                    inconsistent collaboration between stakeholders.
                </p>
                <p>
                    As a result, talented artists struggle to access opportunities, directors struggle to identify
                    suitable talent and service providers, and production quality can decline. Over time, this directly
                    affects audience engagement and the popularity of stage drama.
                </p>
            </div>

            <div class="learn-section">
                <h2>Proposed Solution</h2>
                <p>
                    The platform introduces a centralized workflow for casting, scheduling, service coordination,
                    communication, and feedback. It improves transparency and trust while reducing manual overhead.
                </p>

                <div class="learn-grid">
                    <div class="learn-card">
                        <h3>For Artists</h3>
                        <p>Create portfolios, apply for roles, and respond to role requests through a verified profile.</p>
                    </div>
                    <div class="learn-card">
                        <h3>For Directors</h3>
                        <p>Discover talent, publish opportunities, assign roles, and schedule interviews and rehearsals.</p>
                    </div>
                    <div class="learn-card">
                        <h3>For Production Managers</h3>
                        <p>Coordinate theater bookings and service operations from one workspace.</p>
                    </div>
                    <div class="learn-card">
                        <h3>For Service Providers</h3>
                        <p>Publish services, maintain availability, and respond quickly to booking requests.</p>
                    </div>
                    <div class="learn-card">
                        <h3>For Audiences</h3>
                        <p>Stay informed about shows and submit ratings and reviews to support quality improvements.</p>
                    </div>
                    <div class="learn-card">
                        <h3>For Administrators</h3>
                        <p>Maintain quality, trust, and compliance through user approvals and platform monitoring.</p>
                    </div>
                </div>
            </div>

            <div class="learn-section">
                <h2>Project Goals</h2>
                <p>
                    The project aims to improve production quality, strengthen talent visibility, and revive audience
                    interest in Sri Lankan stage drama through an integrated digital ecosystem.
                </p>
                <ul class="learn-list">
                    <li>Centralize communication among directors, artists, managers, service providers, and theater stakeholders.</li>
                    <li>Reduce delays in casting, scheduling, and service coordination.</li>
                    <li>Increase opportunity access for skilled and emerging artists.</li>
                    <li>Support reliable planning and execution for production teams.</li>
                    <li>Enable audience engagement through timely updates and feedback tools.</li>
                    <li>Provide secure governance through role-based management and approvals.</li>
                </ul>
            </div>

            <div class="learn-actions">
                <a href="<?= ROOT ?>/Home" class="learn-back">Back to Home</a>
            </div>
        </div>
    </div>

    <?php require APPROOT . "/views/includes/footer.php" ?>
</body>

</html>
