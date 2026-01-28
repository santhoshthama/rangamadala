<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Role - <?= htmlspecialchars($data['role']->role_name ?? 'Role') ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 700px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #ba8e23, #d4af37);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .header p {
            opacity: 0.9;
            font-size: 16px;
        }

        .role-info {
            background: #f8f9fa;
            padding: 25px 30px;
            border-bottom: 1px solid #e0e0e0;
        }

        .role-info h2 {
            color: #333;
            margin-bottom: 15px;
            font-size: 22px;
        }

        .role-details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-top: 15px;
        }

        .role-detail-item {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #555;
        }

        .role-detail-item i {
            color: #ba8e23;
            width: 20px;
        }

        .form-section {
            padding: 30px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }

        .form-group label .required {
            color: #dc3545;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #ba8e23;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }

        .form-group input[readonly] {
            background: #f5f5f5;
            cursor: not-allowed;
        }

        .help-text {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
            display: block;
        }

        .btn-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn {
            flex: 1;
            padding: 14px 24px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #ba8e23, #d4af37);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(186, 142, 35, 0.3);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @media (max-width: 600px) {
            .role-details {
                grid-template-columns: 1fr;
            }

            .btn-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-file-alt"></i> Role Application</h1>
            <p>Complete the form below to apply for this role</p>
        </div>

        <div class="role-info">
            <h2><?= htmlspecialchars($data['role']->role_name ?? 'Unknown Role') ?></h2>
            <div class="role-details">
                <div class="role-detail-item">
                    <i class="fas fa-theater-masks"></i>
                    <span><strong>Drama:</strong> <?= htmlspecialchars($data['role']->drama_name ?? 'N/A') ?></span>
                </div>
                <div class="role-detail-item">
                    <i class="fas fa-tag"></i>
                    <span><strong>Type:</strong> <?= ucfirst(htmlspecialchars($data['role']->role_type ?? 'N/A')) ?></span>
                </div>
                <div class="role-detail-item">
                    <i class="fas fa-money-bill-wave"></i>
                    <span><strong>Salary:</strong> LKR <?= isset($data['role']->salary) && $data['role']->salary ? number_format($data['role']->salary) : 'Negotiable' ?></span>
                </div>
                <div class="role-detail-item">
                    <i class="fas fa-users"></i>
                    <span><strong>Openings:</strong> <?= $data['role']->positions_available - $data['role']->positions_filled ?></span>
                </div>
            </div>
        </div>

        <div class="form-section">
            <?php if (!empty($data['success'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?= htmlspecialchars($data['success']) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($data['errors'])): ?>
                <?php foreach ($data['errors'] as $error): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <form method="POST" action="<?= ROOT ?>/artistdashboard/submit_application">
                <input type="hidden" name="role_id" value="<?= $data['role']->id ?? '' ?>">

                <div class="form-group">
                    <label>Full Name <span class="required">*</span></label>
                    <input type="text" name="artist_name" value="<?= htmlspecialchars($data['artist']->full_name ?? '') ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Email Address <span class="required">*</span></label>
                    <input type="email" name="artist_email" value="<?= htmlspecialchars($data['artist']->email ?? '') ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Mobile Number <span class="required">*</span></label>
                    <input type="tel" name="artist_phone" value="<?= htmlspecialchars($data['artist']->phone ?? '') ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Media Links (Optional)</label>
                    <textarea name="media_links" placeholder="Add links to your portfolio, YouTube videos, social media profiles, etc.&#10;&#10;Example:&#10;YouTube: https://youtube.com/channel/...&#10;Instagram: https://instagram.com/...&#10;Portfolio: https://mywebsite.com"></textarea>
                    <span class="help-text">Share links to showcase your work (YouTube, Instagram, portfolio website, etc.)</span>
                </div>

                <div class="form-group">
                    <label>Cover Letter / Message <span class="required">*</span></label>
                    <textarea name="cover_letter" required placeholder="Tell the director why you're the perfect fit for this role...&#10;&#10;Example:&#10;- Your relevant experience&#10;- Why you're interested in this role&#10;- What makes you a good fit"></textarea>
                    <span class="help-text">Introduce yourself and explain why you're interested in this role</span>
                </div>

                <div class="btn-group">
                    <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                        <i class="fas fa-arrow-left"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Submit Application
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
