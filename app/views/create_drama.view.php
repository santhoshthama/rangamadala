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
            <h1><i class="fas fa-theater-masks"></i> Create New Drama</h1>
            <p>Fill in the details below to create your new drama production</p>
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
                    <i class="fas fa-info-circle"></i> Basic Information
                </div>

                <div class="form-group">
                    <label for="title">Drama Title <span class="required">*</span></label>
                    <input 
                        type="text" 
                        id="title" 
                        name="title" 
                        class="form-control" 
                        placeholder="Enter drama title"
                        value="<?= isset($form_data['title']) ? esc($form_data['title']) : '' ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="description">Description <span class="required">*</span></label>
                    <textarea 
                        id="description" 
                        name="description" 
                        class="form-control" 
                        placeholder="Describe your drama production"
                        required
                    ><?= isset($form_data['description']) ? esc($form_data['description']) : '' ?></textarea>
                    <div class="form-hint">Provide a brief overview of the drama's story and themes</div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="category_id">Category <span class="required">*</span></label>
                        <select id="category_id" name="category_id" class="form-control" required>
                            <option value="">Select category</option>
                            <?php if (isset($categories) && !empty($categories)): ?>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category->id ?>" 
                                        <?= (isset($form_data['category_id']) && $form_data['category_id'] == $category->id) ? 'selected' : '' ?>>
                                        <?= esc($category->name) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="venue">Venue <span class="required">*</span></label>
                        <input 
                            type="text" 
                            id="venue" 
                            name="venue" 
                            class="form-control" 
                            placeholder="e.g., Lionel Wendt Theatre, Colombo"
                            value="<?= isset($form_data['venue']) ? esc($form_data['venue']) : '' ?>"
                            required
                        >
                    </div>
                </div>

                <div class="section-title" style="margin-top: 32px;">
                    <i class="fas fa-calendar-alt"></i> Event Details
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="event_date">Event Date <span class="required">*</span></label>
                        <input 
                            type="date" 
                            id="event_date" 
                            name="event_date" 
                            class="form-control"
                            value="<?= isset($form_data['event_date']) ? esc($form_data['event_date']) : '' ?>"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="event_time">Event Time <span class="required">*</span></label>
                        <input 
                            type="time" 
                            id="event_time" 
                            name="event_time" 
                            class="form-control"
                            value="<?= isset($form_data['event_time']) ? esc($form_data['event_time']) : '' ?>"
                            required
                        >
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="duration">Duration (minutes)</label>
                        <input 
                            type="number" 
                            id="duration" 
                            name="duration" 
                            class="form-control" 
                            placeholder="e.g., 120"
                            value="<?= isset($form_data['duration']) ? esc($form_data['duration']) : '' ?>"
                            min="1"
                        >
                        <div class="form-hint">Estimated duration of the performance</div>
                    </div>

                    <div class="form-group">
                        <label for="ticket_price">Ticket Price (LKR)</label>
                        <input 
                            type="number" 
                            id="ticket_price" 
                            name="ticket_price" 
                            class="form-control" 
                            placeholder="e.g., 1500.00"
                            value="<?= isset($form_data['ticket_price']) ? esc($form_data['ticket_price']) : '' ?>"
                            min="0"
                            step="0.01"
                        >
                    </div>
                </div>

                <div class="section-title" style="margin-top: 32px;">
                    <i class="fas fa-image"></i> Drama Poster
                </div>

                <div class="form-group">
                    <label for="image">Upload Drama Poster/Image</label>
                    <div class="file-upload">
                        <input 
                            type="file" 
                            id="image" 
                            name="image" 
                            accept="image/*"
                            onchange="displayFileName(this)"
                        >
                        <label for="image" class="file-upload-label">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span>Click to upload image</span>
                        </label>
                        <div class="file-name" id="file-name"></div>
                    </div>
                    <div class="form-hint">Recommended: 1200x800px, Max size: 5MB (JPG, PNG, GIF)</div>
                </div>

                <div class="btn-group">
                    <a href="<?=ROOT?>/artistdashboard" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check"></i> Create Drama
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
