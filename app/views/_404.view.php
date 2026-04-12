<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | Rangamadala</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
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
            overflow: hidden;
        }

        .error-container {
            max-width: 600px;
            width: 100%;
            text-align: center;
            animation: fadeInUp 0.6s ease-out;
            position: relative;
            z-index: 1;
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

        .brand-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 40px;
        }

        .brand-logo i {
            font-size: 32px;
            color: var(--accent-dark);
        }

        .brand-logo span {
            font-size: 24px;
            font-weight: 700;
            color: var(--accent-dark);
            letter-spacing: 0.5px;
        }

        .error-illustration {
            position: relative;
            margin-bottom: 30px;
        }

        .error-code {
            font-size: 160px;
            font-weight: 700;
            color: var(--accent-light);
            line-height: 1;
            position: relative;
            text-shadow: 4px 4px 8px rgba(186, 142, 35, 0.15);
        }

        .error-code::before {
            content: '404';
            position: absolute;
            top: 5px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 160px;
            font-weight: 700;
            color: var(--accent-dark);
            opacity: 0.1;
            z-index: -1;
        }

        .lost-icon {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, var(--accent-color), var(--accent-dark));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 30px rgba(212, 175, 55, 0.3);
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translate(-50%, -50%) translateY(0); }
            50% { transform: translate(-50%, -50%) translateY(-10px); }
        }

        .lost-icon i {
            font-size: 40px;
            color: #fff;
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

        .suggestions {
            margin-top: 50px;
            padding: 30px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(212, 175, 55, 0.1);
            border: 1px solid var(--accent-light);
        }

        .suggestions h3 {
            font-size: 16px;
            color: var(--text-dark);
            margin-bottom: 20px;
            font-weight: 600;
        }

        .suggestions-list {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
        }

        .suggestion-link {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-light);
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .suggestion-link i {
            color: var(--accent-dark);
            font-size: 16px;
        }

        .suggestion-link:hover {
            color: var(--accent-dark);
        }

        .suggestion-link:hover i {
            transform: scale(1.2);
        }

        /* Decorative elements */
        .decoration {
            position: fixed;
            opacity: 0.03;
            pointer-events: none;
            color: var(--accent-dark);
        }

        .decoration-1 {
            top: -50px;
            left: -50px;
            font-size: 300px;
            transform: rotate(-15deg);
        }

        .decoration-2 {
            bottom: -50px;
            right: -50px;
            font-size: 250px;
            transform: rotate(15deg);
        }

        .decoration-3 {
            top: 20%;
            right: 10%;
            font-size: 80px;
            animation: pulse 4s ease-in-out infinite;
        }

        .decoration-4 {
            bottom: 20%;
            left: 10%;
            font-size: 60px;
            animation: pulse 4s ease-in-out infinite 2s;
        }

        @keyframes pulse {
            0%, 100% { opacity: 0.03; }
            50% { opacity: 0.08; }
        }

        @media (max-width: 480px) {
            .error-code {
                font-size: 100px;
            }
            
            .error-code::before {
                font-size: 100px;
            }
            
            .lost-icon {
                width: 70px;
                height: 70px;
            }
            
            .lost-icon i {
                font-size: 28px;
            }
            
            .error-title {
                font-size: 22px;
            }
            
            .error-actions {
                flex-direction: column;
            }
            
            .error-btn {
                width: 100%;
                justify-content: center;
            }
            
            .suggestions-list {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Decorative elements -->
    <i class="bx bx-theater-masks decoration decoration-1"></i>
    <i class="bx bx-masks-theater decoration decoration-2"></i>
    <i class="bx bx-star decoration decoration-3"></i>
    <i class="bx bx-sparkles decoration decoration-4"></i>

    <div class="error-container">
        <div class="brand-logo">
            <i class="bx bx-theater-masks"></i>
            <span>Rangamadala</span>
        </div>

        <div class="error-illustration">
            <div class="error-code">404</div>
            <div class="lost-icon">
                <i class="bx bx-compass"></i>
            </div>
        </div>
        
        <h1 class="error-title">Page Not Found</h1>
        
        <p class="error-message">
            The page you're looking for seems to have wandered off stage. 
            It might have been moved, deleted, or never existed in the first place.
        </p>

        <div class="error-actions">
            <a href="javascript:history.back()" class="error-btn btn-secondary">
                <i class="bx bx-arrow-left"></i>
                Go Back
            </a>
            <a href="<?= defined('ROOT') ? ROOT : '/' ?>" class="error-btn btn-primary">
                <i class="bx bx-home"></i>
                Back to Home
            </a>
        </div>

        <div class="suggestions">
            <h3>You might be looking for:</h3>
            <div class="suggestions-list">
                <a href="<?= defined('ROOT') ? ROOT : '/' ?>" class="suggestion-link">
                    <i class="bx bx-home"></i>
                    Home
                </a>
                <a href="<?= defined('ROOT') ? ROOT : '/' ?>/Login" class="suggestion-link">
                    <i class="bx bx-sign-in-alt"></i>
                    Login
                </a>
                <a href="<?= defined('ROOT') ? ROOT : '/' ?>/Signup" class="suggestion-link">
                    <i class="bx bx-user-plus"></i>
                    Sign Up
                </a>
            </div>
        </div>
    </div>
</body>
</html>
