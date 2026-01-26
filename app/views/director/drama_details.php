<?php
if (isset($data) && is_array($data)) {
    extract($data);
}

if (!isset($drama) && isset($data['drama'])) {
    $drama = $data['drama'];
}

$formValues = [
    'drama_name' => $form_data['drama_name'] ?? ($drama->drama_name ?? ''),
    'certificate_number' => $form_data['certificate_number'] ?? ($drama->certificate_number ?? ''),
    'owner_name' => $form_data['owner_name'] ?? ($drama->owner_name ?? ''),
    'description' => $form_data['description'] ?? ($drama->description ?? ''),
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drama Details - Rangamadala</title>
    <link rel="stylesheet" href="/Rangamadala/public/assets/CSS/ui-theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                <a href="<?= ROOT ?>/director/dashboard?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="active">
                <a href="<?= ROOT ?>/director/drama_details?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>">
                    <i class="fas fa-film"></i>
                    <span>Drama Details</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/director/manage_roles?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>">
                    <i class="fas fa-users"></i>
                    <span>Artist Roles</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/director/assign_managers?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>">
                    <i class="fas fa-user-tie"></i>
                    <span>Production Manager</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/director/schedule_management?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Schedule</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/director/view_services_budget?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>">
                    <i class="fas fa-dollar-sign"></i>
                    <span>Services & Budget</span>
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
        <a href="<?= ROOT ?>/director/dashboard?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>" class="back-button">
            <i class="fas fa-arrow-left"></i>
            Back to Dashboard
        </a>

        <!-- Header -->
        <div class="header--wrapper">
            <div class="header--title">
                <span>Drama Details</span>
                <h2><?= isset($drama->drama_name) ? esc($drama->drama_name) : 'Drama' ?></h2>
            </div>
            
        </div>

        <!-- Drama Information -->
        <div class="content">
            <div class="container" style="max-width: 900px;">
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="message <?= $_SESSION['message_type'] ?? 'info' ?>">
                        <i class="fas fa-<?= ($_SESSION['message_type'] ?? '') === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
                        <?= esc($_SESSION['message']) ?>
                    </div>
                    <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
                <?php endif; ?>

                <form id="dramaDetailsForm">
                    <!-- Basic Information -->
                    <h3 style="color: var(--brand); margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-film"></i>
                        Basic Information
                    </h3>

                    <div class="form-group">
                        <label for="drama_name">Drama Title <span class="required">*</span></label>
                        <input type="text" class="form-control" id="drama_name" name="drama_name" value="<?= esc($formValues['drama_name']) ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label for="owner_name">Owner Name <span class="required">*</span></label>
                        <input type="text" class="form-control" id="owner_name" name="owner_name" value="<?= esc($formValues['owner_name']) ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label for="description">Drama Description</label>
                        <textarea class="form-control" id="description" name="description" rows="5" readonly><?= esc($formValues['description']) ?></textarea>
                        <div class="form-hint">Keep this synopsis in sync with what was provided when registering the drama.</div>
                    </div>

                    <!-- Certificate Information -->
                    <h3 style="color: var(--brand); margin: 30px 0 20px; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-certificate"></i>
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
                        <i class="fas fa-info-circle"></i>
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
            </div>
        </div>
    </main>

    
</body>
</html>
