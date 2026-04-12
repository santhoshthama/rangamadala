<?php
if (isset($data) && is_array($data)) {
    extract($data);
}

if (!isset($drama) && isset($data['drama'])) {
    $drama = $data['drama'];
}

$dramaId = isset($drama->id) ? (int)$drama->id : (isset($_GET['drama_id']) ? (int)$_GET['drama_id'] : 0);

$formValues = [
    'drama_name' => $form_data['drama_name'] ?? ($drama->drama_name ?? ''),
    'certificate_number' => $form_data['certificate_number'] ?? ($drama->certificate_number ?? ''),
    'owner_name' => $form_data['owner_name'] ?? ($drama->owner_name ?? ''),
    'description' => $form_data['description'] ?? ($drama->description ?? ''),
];

$publishFormValues = [
    'category_id' => $publish_form_data['category_id'] ?? ($drama->category_id ?? ''),
    'public_description' => $publish_form_data['public_description'] ?? ($drama->public_description ?? ''),
    'genre' => $publish_form_data['genre'] ?? ($drama->genre ?? ''),
    'language' => $publish_form_data['language'] ?? ($drama->language ?? ''),
    'duration_minutes' => $publish_form_data['duration_minutes'] ?? ($drama->duration_minutes ?? ''),
    'venue' => $publish_form_data['venue'] ?? ($drama->venue ?? ''),
    'event_date' => $publish_form_data['event_date'] ?? ($drama->event_date ?? ''),
    'ticket_price' => $publish_form_data['ticket_price'] ?? ($drama->ticket_price ?? ''),
    'showing_prices' => $publish_form_data['showing_prices'] ?? ($drama->showing_prices ?? ''),
];

$isPublished = !empty($drama->is_published);

// Get current user profile image
$userModel = new M_universal_profile();
$currentUser = $userModel->getUserById($_SESSION['user_id']);
$profileImageSrc = ROOT . '/assets/images/default-avatar.jpg';
if ($currentUser && !empty($currentUser->profile_image)) {
    $imageValue = str_replace('\\', '/', $currentUser->profile_image);
    if (strpos($imageValue, '/') !== false) {
        $profileImageSrc = ROOT . '/' . ltrim($imageValue, '/');
    } else {
        $profileImageSrc = ROOT . '/uploads/profile_images/' . rawurlencode($imageValue);
    }
} elseif ($currentUser && !empty($currentUser->nic_photo)) {
    $profileImageSrc = ROOT . '/' . ltrim(str_replace('\\', '/', $currentUser->nic_photo), '/');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drama Details - Rangamadala</title>
    <link rel="stylesheet" href="/Rangamadala/public/assets/CSS/ui-theme.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<style>
        .message {
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .message.info {
            background: #e9ecef;
            color: #383d41;
            border: 1px solid #d6d8db;
        }

        .form-hint {
            font-size: 12px;
            color: var(--muted);
            margin-top: 6px;
        }

        .publish-status-card {
            border: 1px solid #d6d8db;
            border-radius: 12px;
            padding: 16px;
            margin: 0 0 24px;
            background: #f8f9fa;
        }

        .publish-status-card strong {
            font-size: 15px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="logo">
            <h2>🎭</h2>
        </div>
        <ul class="menu">
            <li>
                <a href="<?= ROOT ?>/director/dashboard?drama_id=<?= $dramaId ?>">
                    <i class="bx bx-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="active">
                <a href="<?= ROOT ?>/director/drama_details?drama_id=<?= $dramaId ?>">
                    <i class="bx bx-film"></i>
                    <span>Drama Details</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/director/manage_roles?drama_id=<?= $dramaId ?>">
                    <i class="bx bx-users"></i>
                    <span>Artist Roles</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/director/assign_managers?drama_id=<?= $dramaId ?>">
                    <i class="bx bx-user-tie"></i>
                    <span>Production Manager</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/director/schedule_management?drama_id=<?= $dramaId ?>">
                    <i class="bx bx-calendar-alt"></i>
                    <span>Schedule</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/director/view_services_budget?drama_id=<?= $dramaId ?>">
                    <i class="bx bx-dollar-sign"></i>
                    <span>Services & Budget</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/artistdashboard">
                    <i class="bx bx-arrow-left"></i>
                    <span>Back to Profile</span>
                </a>
            </li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main--content">
        <a href="<?= ROOT ?>/director/dashboard?drama_id=<?= $dramaId ?>" class="back-button">
            <i class="bx bx-arrow-left"></i>
            Back to Dashboard
        </a>

        <!-- Header -->
        <div class="header--wrapper">
            <div class="header--title">
                <span>Drama Details</span>
                <h2><?= isset($drama->drama_name) ? esc($drama->drama_name) : 'Drama' ?></h2>
            </div>
            <div class="user--info">
                <div class="role-badge">
                    <i class="bx bx-video"></i> Director
                </div>
                <img src="<?= esc($profileImageSrc) ?>" alt="Director Avatar" onerror="this.src='<?= ROOT ?>/assets/images/default-avatar.jpg'">
                <a href="<?= ROOT ?>/logout" class="logout-btn" title="Logout">
                    <i class="bx bx-sign-out-alt"></i>
                </a>
            </div>
        </div>

        <!-- Drama Information -->
        <div class="content">
            <div class="container" style="max-width: 900px;">
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="message <?= $_SESSION['message_type'] ?? 'info' ?>">
                        <i class="bx bx-<?= ($_SESSION['message_type'] ?? '') === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
                        <?= esc($_SESSION['message']) ?>
                    </div>
                    <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
                <?php endif; ?>

                <form id="dramaDetailsForm">
                    <div class="publish-status-card">
                        <strong>
                            <i class="bx bx-bullhorn"></i>
                            Public Status: <?= $isPublished ? 'Published' : 'Not Published Yet' ?>
                        </strong>
                        <p class="form-hint" style="margin-top:8px;">
                            <?= $isPublished
                                ? 'This drama is visible to audience browsing. You can update details and publish again to refresh poster submission.'
                                : 'Publish this drama with poster and public details to make it visible in audience browsing.' ?>
                        </p>
                        <a href="#publish-section" class="btn btn-primary" style="margin-top:10px; display:inline-flex; width:auto;">
                            <i class="bx bx-upload"></i>
                            <?= $isPublished ? 'Update Published Details' : 'Publish Drama' ?>
                        </a>
                    </div>

                    <!-- Basic Information -->
                    <h3 style="color: var(--brand); margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                        <i class="bx bx-film"></i>
                        Basic Information
                    </h3>

                    <div class="form-group">
                        <label for="drama_name">Drama Title <span class="required">*</span></label>
                        <input type="text" class="form-control" id="drama_name" name="drama_name" value="<?= esc($formValues['drama_name']) ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label for="owner_name">Producer Name <span class="required">*</span></label>
                        <input type="text" class="form-control" id="owner_name" name="owner_name" value="<?= esc($formValues['owner_name']) ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label for="description">Drama Description</label>
                        <textarea class="form-control" id="description" name="description" rows="5" readonly><?= esc($formValues['description']) ?></textarea>
                        <div class="form-hint">Keep this synopsis in sync with what was provided when registering the drama.</div>
                    </div>

                    <!-- Certificate Information -->
                    <h3 style="color: var(--brand); margin: 30px 0 20px; display: flex; align-items: center; gap: 10px;">
                        <i class="bx bx-certificate"></i>
                        Public Performance Board Certificate
                    </h3>

                    <div class="form-group">
                        <label for="certificate_number">Certificate Number</label>
                        <input type="text" class="form-control" id="certificate_number" name="certificate_number" value="<?= esc($formValues['certificate_number']) ?>" readonly>
                        <div class="form-hint">Must match the Public Performance Board certificate number.</div>
                    </div>

                    <div class="form-group">
                        <label>Certificate Document</label>
                        <?php if (!empty($drama->certificate_image)): ?>
                            <div class="form-hint">
                                <a href="<?= ROOT ?>/uploads/certificates/<?= esc($drama->certificate_image) ?>" target="_blank" rel="noopener">View certificate</a>
                            </div>
                        <?php else: ?>
                            <div class="form-hint">No certificate uploaded yet.</div>
                        <?php endif; ?>
                    </div>

                    <!-- Status Information -->
                    <h3 style="color: var(--brand); margin: 30px 0 20px; display: flex; align-items: center; gap: 10px;">
                        <i class="bx bx-info-circle"></i>
                        Record Information
                    </h3>

                    <div class="drama-info">
                        <div class="service-info-item">
                            <span class="service-info-label">Created On</span>
                            <span class="service-info-value"><?= isset($drama->created_at) ? esc(date('Y-m-d H:i', strtotime($drama->created_at))) : 'N/A' ?></span>
                        </div>
                        <div class="service-info-item">
                            <span class="service-info-label">Last Updated</span>
                            <span class="service-info-value"><?= isset($drama->updated_at) ? esc(date('Y-m-d H:i', strtotime($drama->updated_at))) : 'N/A' ?></span>
                        </div>
                        <div class="service-info-item">
                            <span class="service-info-label">Registered By</span>
                            <span class="service-info-value"><?= isset($drama->creator_name) ? esc($drama->creator_name) : 'N/A' ?></span>
                        </div>
                    </div>

                </form>

                <form action="<?= ROOT ?>/director/publish_drama?drama_id=<?= $dramaId ?>" method="POST" enctype="multipart/form-data" id="publish-section" style="margin-top: 32px;">
                    <h3 style="color: var(--brand); margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                        <i class="bx bx-bullhorn"></i>
                        <?= $isPublished ? 'Update Published Drama Details' : 'Publish Drama For Audience' ?>
                    </h3>

                    <div class="form-group">
                        <label for="category_id">Drama Category <span class="required">*</span></label>
                        <select class="form-control" id="category_id" name="category_id" required>
                            <option value="">Select a category</option>
                            <?php if (!empty($categories)): ?>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= (int)$category->id ?>" <?= (string)$publishFormValues['category_id'] === (string)$category->id ? 'selected' : '' ?>>
                                        <?= esc($category->name) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="public_description">Public Description <span class="required">*</span></label>
                        <textarea class="form-control" id="public_description" name="public_description" rows="4" required><?= esc($publishFormValues['public_description']) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="language">Language <span class="required">*</span></label>
                        <input type="text" class="form-control" id="language" name="language" value="<?= esc($publishFormValues['language']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="duration_minutes">Duration (minutes) <span class="required">*</span></label>
                        <input type="number" class="form-control" id="duration_minutes" name="duration_minutes" min="1" value="<?= esc((string)$publishFormValues['duration_minutes']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="showing_prices">Showing Prices <span class="required">*</span></label>
                        <textarea class="form-control" id="showing_prices" name="showing_prices" rows="3" maxlength="500" required><?= esc($publishFormValues['showing_prices']) ?></textarea>
                        <div class="form-hint">Example: Normal - LKR 1500, Balcony - LKR 2500, VIP - LKR 4000.</div>
                    </div>

                    <div class="form-group">
                        <label for="poster_image">Drama Poster <?= $isPublished ? '(optional if already uploaded)' : '<span class="required">*</span>' ?></label>
                        <input type="file" class="form-control" id="poster_image" name="poster_image" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" <?= empty($drama->poster_image) ? 'required' : '' ?>>
                        <div class="form-hint">Poster will be sent to admin and can be added to the home page slides after review.</div>
                        <?php if (!empty($drama->poster_image)): ?>
                            <div class="form-hint" style="margin-top: 8px;">
                                Current poster: <a href="<?= ROOT ?>/uploads/dramas/<?= esc($drama->poster_image) ?>" target="_blank" rel="noopener">View poster</a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-primary" style="margin-top: 8px; width: 100%;">
                        <i class="bx bx-paper-plane"></i>
                        <?= $isPublished ? 'Update & Re-submit To Admin' : 'Publish Drama' ?>
                    </button>
                </form>
            </div>
        </div>
    </main>

    
</body>
</html>
