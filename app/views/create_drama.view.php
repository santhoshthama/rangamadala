<?php 
// Extract data array for easier access
if(isset($data) && is_array($data)) {
    extract($data);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Drama - Rangamadala</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --brand: #ba8e23;
            --brand-strong: #a0781e;
            --brand-soft: rgba(186, 142, 35, 0.12);
            --ink: #1f2933;
            --muted: #6b7280;
            --card: #ffffff;
            --bg: #f5f5f5;
            --border: #e0e0e0;
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
            --info: #17a2b8;
            --radius: 12px;
            --shadow-sm: 0 2px 10px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 8px 32px rgba(0, 0, 0, 0.12);
            --shadow-lg: 0 15px 40px rgba(0, 0, 0, 0.16);
            --transition: all 0.25s ease;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', 'Arial', sans-serif;
            color: var(--ink);
            background: var(--bg);
            min-height: 100vh;
        }

        .container {
            max-width: 900px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .header h1 {
            font-size: 36px;
            color: var(--brand);
            margin-bottom: 10px;
        }

        .header p {
            color: var(--muted);
            font-size: 16px;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--brand);
            text-decoration: none;
            margin-bottom: 20px;
            font-weight: 600;
            transition: var(--transition);
        }

        .back-link:hover {
            color: var(--brand-strong);
            gap: 12px;
        }

        .form-card {
            background: var(--card);
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
            padding: 40px;
        }

        .message {
            padding: 16px;
            border-radius: var(--radius);
            margin-bottom: 20px;
            font-weight: 500;
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

        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--ink);
            font-size: 14px;
        }

        .form-group label .required {
            color: var(--danger);
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            transition: var(--transition);
            background: #fff;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--brand);
            box-shadow: 0 0 0 3px var(--brand-soft);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 120px;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }

        .form-hint {
            font-size: 12px;
            color: var(--muted);
            margin-top: 4px;
        }

        .file-upload {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .file-upload input[type="file"] {
            display: none;
        }

        .file-upload-label {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 12px 16px;
            border: 2px dashed var(--border);
            border-radius: 8px;
            cursor: pointer;
            transition: var(--transition);
            background: #fafafa;
        }

        .file-upload-label:hover {
            border-color: var(--brand);
            background: var(--brand-soft);
        }

        .file-upload-label i {
            font-size: 20px;
            color: var(--brand);
        }

        .file-name {
            margin-top: 8px;
            font-size: 13px;
            color: var(--muted);
        }

        .btn-group {
            display: flex;
            gap: 12px;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid var(--border);
        }

        .btn {
            padding: 14px 28px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            flex: 1;
        }

        .btn-primary {
            background: linear-gradient(45deg, var(--brand), var(--brand-strong));
            color: #fff;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn-secondary {
            background: #f1f1f1;
            color: var(--ink);
        }

        .btn-secondary:hover {
            background: #e0e0e0;
        }

        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--ink);
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid var(--brand);
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="<?=ROOT?>/artistdashboard" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>

        <div class="header">
            <h1><i class="fas fa-certificate"></i> Register Drama with Certificate</h1>
            <p>Register your drama production using the Public Performance Board Certificate</p>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="message <?= $_SESSION['message_type'] ?? 'info' ?>">
                <i class="fas fa-<?= $_SESSION['message_type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
                <?= esc($_SESSION['message']) ?>
            </div>
            <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
        <?php endif; ?>

        <div class="form-card">
            <form action="<?=ROOT?>/createDrama" method="POST" enctype="multipart/form-data">
                
                <div class="section-title">
                    <i class="fas fa-file-certificate"></i> Public Performance Board Certificate Details
                </div>

                <div class="form-group">
                    <label for="drama_name">Drama Name (as in certificate) <span class="required">*</span></label>
                    <input 
                        type="text" 
                        id="drama_name" 
                        name="drama_name" 
                        class="form-control" 
                        placeholder="Enter drama name exactly as shown in certificate"
                        value="<?= isset($form_data['drama_name']) ? esc($form_data['drama_name']) : '' ?>"
                        required
                    >
                    <div class="form-hint">Enter the exact drama name from your Public Performance Board Certificate</div>
                </div>

                <div class="form-group">
                    <label for="certificate_number">Public Performance Certificate Number <span class="required">*</span></label>
                    <input 
                        type="text" 
                        id="certificate_number" 
                        name="certificate_number" 
                        class="form-control" 
                        placeholder="e.g., PPB/2026/0123"
                        value="<?= isset($form_data['certificate_number']) ? esc($form_data['certificate_number']) : '' ?>"
                        required
                    >
                    <div class="form-hint">Enter the unique certificate number issued by the Public Performance Board</div>
                </div>

                <div class="form-group">
                    <label for="owner_name">Owner's Name <span class="required">*</span></label>
                    <input 
                        type="text" 
                        id="owner_name" 
                        name="owner_name" 
                        class="form-control" 
                        placeholder="Enter the owner's full name"
                        value="<?= isset($form_data['owner_name']) ? esc($form_data['owner_name']) : '' ?>"
                        required
                    >
                    <div class="form-hint">Enter the name of the drama production owner</div>
                </div>

                <div class="section-title" style="margin-top: 32px;">
                    <i class="fas fa-image"></i> Certificate Image
                </div>

                <div class="form-group">
                    <label for="certificate_image">Upload Certificate Image <span class="required">*</span></label>
                    <div class="file-upload">
                        <input 
                            type="file" 
                            id="certificate_image" 
                            name="certificate_image" 
                            accept="image/*"
                            onchange="displayFileName(this)"
                            required
                        >
                        <label for="certificate_image" class="file-upload-label">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span>Click to upload certificate image</span>
                        </label>
                        <div class="file-name" id="file-name"></div>
                    </div>
                    <div class="form-hint">Upload a clear image/photo of your Public Performance Board Certificate. Max size: 5MB (JPG, PNG, PDF)</div>
                </div>

                <div class="btn-group">
                    <a href="<?=ROOT?>/artistdashboard" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check-circle"></i> Register Drama
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function displayFileName(input) {
            const fileNameDiv = document.getElementById('file-name');
            if (input.files && input.files[0]) {
                fileNameDiv.textContent = '📄 ' + input.files[0].name;
            } else {
                fileNameDiv.textContent = '';
            }
        }
    </script>
</body>
</html>
