<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Error') ?> | Rangamadala</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-bg: #faf8f3;
            --secondary-bg: #f5f0e8;
            --accent-color: #d4af37;
            --accent-dark: #ba8e23;
            --accent-light: #e8d5a8;
            --text-dark: #3d2817;
            --text-light: #5c4a3a;
            --text-muted: #8b7355;
            --error-color: #dc3545;
            --error-light: rgba(220, 53, 69, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--primary-bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .error-container {
            max-width: 600px;
            width: 100%;
            text-align: center;
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .error-icon {
            width: 120px;
            height: 120px;
            margin: 0 auto 30px;
            background: linear-gradient(135deg, var(--error-light) 0%, rgba(220, 53, 69, 0.05) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .error-icon::before {
            content: '';
            position: absolute;
            width: 140px;
            height: 140px;
            border: 2px dashed var(--accent-light);
            border-radius: 50%;
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .error-icon i {
            font-size: 48px;
            color: var(--error-color);
        }

        .error-code {
            font-size: 80px;
            font-weight: 700;
            color: var(--accent-dark);
            line-height: 1;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(186, 142, 35, 0.2);
        }

        .error-title {
            font-size: 28px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 15px;
        }

        .error-message {
            font-size: 16px;
            color: var(--text-light);
            line-height: 1.7;
            margin-bottom: 35px;
            padding: 0 20px;
        }

        .error-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .error-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 28px;
            font-size: 14px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent-color), var(--accent-dark));
            color: #fff;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(212, 175, 55, 0.4);
        }

        .btn-secondary {
            background: #fff;
            color: var(--text-dark);
            border: 2px solid var(--accent-light);
        }

        .btn-secondary:hover {
            background: var(--accent-light);
            border-color: var(--accent-color);
            transform: translateY(-3px);
        }

        .error-footer {
            margin-top: 50px;
            padding-top: 30px;
            border-top: 1px solid var(--accent-light);
        }

        .error-footer p {
            font-size: 14px;
            color: var(--text-muted);
        }

        .error-footer a {
            color: var(--accent-dark);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .error-footer a:hover {
            color: var(--accent-color);
        }

        .brand-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 40px;
        }

        .brand-logo img {
            height: 50px;
        }

        .brand-logo span {
            font-size: 24px;
            font-weight: 700;
            color: var(--accent-dark);
            letter-spacing: 0.5px;
        }

        /* Decorative elements */
        .decoration {
            position: fixed;
            opacity: 0.05;
            pointer-events: none;
        }

        .decoration-1 {
            top: 10%;
            left: 5%;
            font-size: 150px;
            color: var(--accent-color);
        }

        .decoration-2 {
            bottom: 10%;
            right: 5%;
            font-size: 120px;
            color: var(--accent-dark);
        }

        @media (max-width: 480px) {
            .error-code {
                font-size: 60px;
            }
            
            .error-title {
                font-size: 22px;
            }
            
            .error-icon {
                width: 100px;
                height: 100px;
            }
            
            .error-icon i {
                font-size: 36px;
            }
            
            .error-actions {
                flex-direction: column;
            }
            
            .error-btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- Decorative elements -->
    <i class="fas fa-theater-masks decoration decoration-1"></i>
    <i class="fas fa-masks-theater decoration decoration-2"></i>

    <div class="error-container">
        <div class="brand-logo">
            <i class="fas fa-theater-masks" style="font-size: 32px; color: var(--accent-dark);"></i>
            <span>Rangamadala</span>
        </div>

        <div class="error-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>

        <div class="error-code">500</div>
        
        <h1 class="error-title"><?= htmlspecialchars($title ?? 'Something went wrong') ?></h1>
        
        <p class="error-message">
            <?= htmlspecialchars($message ?? 'We encountered an unexpected error while processing your request. Please try again later or contact support if the problem persists.') ?>
        </p>

        <div class="error-actions">
            <a href="javascript:history.back()" class="error-btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Go Back
            </a>
            <a href="<?= defined('ROOT') ? ROOT : '/' ?>" class="error-btn btn-primary">
                <i class="fas fa-home"></i>
                Back to Home
            </a>
        </div>

        <div class="error-footer">
            <p>Need help? <a href="<?= defined('ROOT') ? ROOT : '/' ?>/contact">Contact Support</a></p>
        </div>
    </div>
</body>
</html>
