<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> - Rejected Users</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<link rel="shortcut icon" href="<?= ROOT ?>/assets/images/Rangamadala logo.png" type="image/x-icon">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #faf8f3 0%, #f5f0e8 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.1);
            margin-bottom: 30px;
            border-left: 5px solid #dc3545;
        }

        .header h1 {
            color: #3d2817;
            margin-bottom: 10px;
            font-size: 28px;
        }

        .header p {
            color: #8b7355;
            font-size: 14px;
        }

        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .tab-btn {
            padding: 12px 24px;
            border: 2px solid #d4af37;
            background: white;
            color: #ba8e23;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .tab-btn:hover,
        .tab-btn.active {
            background: #d4af37;
            color: white;
        }

        .users-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
        }

        .user-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(212, 175, 55, 0.12);
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            border: 1px solid rgba(212, 175, 55, 0.15);
        }

        .user-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 28px rgba(220, 53, 69, 0.25);
            border-color: #dc3545;
        }

        .user-card-header {
            background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);
            padding: 20px;
            color: white;
        }

        .user-card-header h3 {
            margin-bottom: 8px;
            font-size: 18px;
        }

        .user-card-header p {
            font-size: 13px;
            opacity: 0.9;
            text-transform: capitalize;
        }

        .user-card-body {
            padding: 20px;
        }

        .user-info {
            margin-bottom: 15px;
        }

        .user-info label {
            display: block;
            font-size: 12px;
            color: #8b7355;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .user-info value {
            display: block;
            font-size: 14px;
            color: #3d2817;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            margin-top: 8px;
        }

        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }

        .rejection-reason {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            padding: 12px;
            margin-top: 10px;
        }

        .rejection-reason label {
            color: #721c24;
        }

        .rejection-reason value {
            color: #856404;
            font-style: italic;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.1);
        }

        .empty-state i {
            font-size: 48px;
            color: #d4af37;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            color: #3d2817;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: #8b7355;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #ba8e23;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .back-link:hover {
            color: #d4af37;
        }

        .role-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            margin-left: 8px;
        }

        .role-badge.artist {
            background: rgba(255,255,255,0.2);
            color: white;
        }

        .role-badge.service_provider {
            background: rgba(255,255,255,0.2);
            color: white;
        }

        @media (max-width: 768px) {
            .users-grid {
                grid-template-columns: 1fr;
            }

            .header h1 {
                font-size: 22px;
            }

            .tabs {
                flex-direction: column;
            }

            .tab-btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="<?= ROOT ?>/Admindashboard" class="back-link">
            <i class="bx bx-arrow-left"></i> Back to Admin Dashboard
        </a>

        <div class="header">
            <h1><i class="bx bx-times-circle" style="color: #dc3545;"></i> Rejected Users</h1>
            <p>Artists and Service Providers whose registrations were rejected</p>
        </div>

        <div class="tabs">
            <a href="<?= ROOT ?>/UserVerification/pending" class="tab-btn">
                <i class="bx bx-hourglass-half"></i> Pending
            </a>
            <a href="<?= ROOT ?>/UserVerification/verified" class="tab-btn">
                <i class="bx bx-check-circle"></i> Verified
            </a>
            <a href="<?= ROOT ?>/UserVerification/rejected" class="tab-btn active">
                <i class="bx bx-times-circle"></i> Rejected
            </a>
        </div>

        <?php if (!empty($rejected_users)): ?>
            <div class="users-grid">
                <?php foreach ($rejected_users as $user): ?>
                    <div class="user-card">
                        <div class="user-card-header">
                            <h3>
                                <?= htmlspecialchars($user->full_name) ?>
                                <span class="role-badge <?= $user->role ?>"><?= ucfirst(str_replace('_', ' ', $user->role)) ?></span>
                            </h3>
                            <p><i class="bx bx-<?= $user->role === 'artist' ? 'palette' : 'briefcase' ?>"></i> <?= ucfirst(str_replace('_', ' ', $user->role)) ?></p>
                            <span class="status-badge status-rejected">Rejected</span>
                        </div>
                        <div class="user-card-body">
                            <div class="user-info">
                                <label>Email</label>
                                <value><?= htmlspecialchars($user->email) ?></value>
                            </div>
                            <div class="user-info">
                                <label>Phone</label>
                                <value><?= htmlspecialchars($user->phone ?? 'N/A') ?></value>
                            </div>
                            <div class="user-info">
                                <label>Rejected On</label>
                                <value><?= !empty($user->verified_at) ? date('d M Y - H:i', strtotime($user->verified_at)) : 'N/A' ?></value>
                            </div>
                            <div class="rejection-reason">
                                <div class="user-info" style="margin-bottom: 0;">
                                    <label><i class="bx bx-exclamation-triangle"></i> Rejection Reason</label>
                                    <value><?= htmlspecialchars($user->rejection_reason ?? 'No reason provided') ?></value>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="bx bx-times-circle"></i>
                <h3>No Rejected Users</h3>
                <p>Rejected registrations will appear here.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
