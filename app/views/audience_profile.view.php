<?php
$profile = $data['profile'] ?? null;
$signup = $data['signup_details'] ?? null;
$bio = $data['bio'] ?? null;
$profileImage = $data['profile_image'] ?? null;

$profileImageSrc = ROOT . '/uploads/profile_images/default_user.png';
if (!empty($profileImage)) {
    $profileImageSrc = ROOT . '/uploads/profile_images/' . rawurlencode($profileImage);
}

$displayName = $profile->full_name ?? ($signup->full_name ?? 'Audience');
$displayEmail = $profile->email ?? ($signup->email ?? 'N/A');
$displayPhone = $profile->phone ?? ($signup->phone ?? 'N/A');
$displayLocation = !empty($profile->location) ? (string)$profile->location : '';
$displayBio = !empty($bio) ? (string)$bio : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Audience Profile</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        :root {
            --brand: #ba8e23;
            --brand-strong: #a0781e;
            --bg: #f5f5f5;
            --card: #ffffff;
            --ink: #1f2933;
            --muted: #6b7280;
            --border: #e5e7eb;
            --radius: 16px;
            --shadow: 0 12px 40px rgba(31, 41, 51, 0.12);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: 'Inter', Arial, sans-serif;
            background: linear-gradient(135deg, rgba(186, 142, 35, 0.12), rgba(160, 120, 30, 0.08));
            color: var(--ink);
            min-height: 100vh;
            padding: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        a { color: inherit; text-decoration: none; }

        .page-wrapper {
            width: min(1100px, 100%);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            color: var(--brand-strong);
            margin-bottom: 16px;
        }

        .profile-card {
            background: var(--card);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            display: grid;
            grid-template-columns: 320px 1fr;
            overflow: hidden;
        }

        .profile-summary {
            background: linear-gradient(180deg, rgba(186, 142, 35, 0.95), rgba(160, 120, 30, 0.92));
            color: #fff;
            padding: 40px 32px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .profile-summary img {
            width: 160px;
            height: 160px;
            border-radius: 20px;
            object-fit: cover;
            border: 6px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.24);
            align-self: center;
        }

        .profile-summary h2 {
            margin-top: 28px;
            margin-bottom: 8px;
            font-size: 28px;
        }

        .profile-summary p {
            margin: 4px 0;
            font-size: 15px;
            color: rgba(255, 255, 255, 0.9);
        }

        .summary-actions {
            margin-top: 28px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .summary-actions a {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 18px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.14);
            color: #fff;
            font-weight: 600;
        }

        .profile-details {
            padding: 40px;
        }

        .profile-details h1 {
            margin: 0 0 16px;
            font-size: 30px;
            font-weight: 700;
            color: var(--ink);
        }

        .profile-details p.subtitle {
            margin: 0 0 28px;
            color: var(--muted);
            font-size: 15px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px 24px;
        }

        .item {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .item.full {
            grid-column: 1 / -1;
        }

        .item label {
            font-weight: 600;
            font-size: 14px;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 1.2px;
        }

        .value-box {
            padding: 14px 16px;
            border-radius: 12px;
            border: 1px solid var(--border);
            background: #fafafa;
            font-size: 15px;
            min-height: 50px;
            display: flex;
            align-items: center;
        }

        .value-box.textarea {
            min-height: 120px;
            align-items: flex-start;
            white-space: pre-wrap;
            line-height: 1.5;
        }

        @media (max-width: 960px) {
            .profile-card {
                grid-template-columns: 1fr;
            }

            .profile-summary {
                text-align: center;
            }

            .summary-actions {
                align-items: center;
            }

            .grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="page-wrapper">
        <a class="back-link" href="<?= ROOT ?>/Audiencedashboard">
            <i class="bx bx-arrow-left"></i>
            <span>Back to Dashboard</span>
        </a>

        <div class="profile-card">
            <aside class="profile-summary">
                <img src="<?= esc($profileImageSrc) ?>" alt="Audience profile" onerror="this.src='<?= ROOT ?>/uploads/profile_images/default_user.png'">

                <div>
                    <h2><?= esc($displayName) ?></h2>
                    <p><i class="bx bx-envelope"></i> <?= esc($displayEmail) ?></p>
                    <p><i class="bx bx-phone"></i> <?= esc($displayPhone) ?></p>
                    <?php if ($displayLocation !== ''): ?>
                        <p><i class="bx bx-map-marker-alt"></i> <?= esc($displayLocation) ?></p>
                    <?php endif; ?>

                    <div class="summary-actions">
                        <a href="<?= ROOT ?>/AudienceProfileEdit">
                            <i class="bx bx-edit-alt"></i>
                            Edit Profile
                        </a>
                    </div>
                </div>
            </aside>

            <section class="profile-details">
                <h1>Profile Details</h1>
                <p class="subtitle">Keep your information up to date.</p>

                <div class="grid">
                    <div class="item">
                        <label>Full Name</label>
                        <div class="value-box"><?= esc($displayName) ?></div>
                    </div>

                    <div class="item">
                        <label>Email</label>
                        <div class="value-box"><?= esc($displayEmail) ?></div>
                    </div>

                    <div class="item">
                        <label>Phone</label>
                        <div class="value-box"><?= esc($displayPhone) ?></div>
                    </div>

                    <?php if ($displayBio !== ''): ?>
                        <div class="item full">
                            <label>Bio / About Me</label>
                            <div class="value-box textarea"><?= esc($displayBio) ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>
</body>
</html>
