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
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="<?=ROOT?>/assets/CSS/create_drama.css">
</head>
<body>
    <div class="container">
        <a href="<?=ROOT?>/artistdashboard" class="back-link">
            <i class="bx bx-arrow-left"></i> Back to Dashboard
        </a>

        <div class="header">
            <h1><i class="bx bx-certificate"></i> Register Drama with Certificate</h1>
            <p>Step 1: Submit certificate details for admin approval. <br> Step 2: Publish to audience from your dashboard.</p>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="message <?= $_SESSION['message_type'] ?? 'info' ?>">
                <i class="bx bx-<?= $_SESSION['message_type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
                <?= esc($_SESSION['message']) ?>
            </div>
            <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
        <?php endif; ?>

        <div class="form-card">
            <form action="<?=ROOT?>/createDrama" method="POST" enctype="multipart/form-data">
                
                <div class="section-title">
                    <i class="bx bx-file-certificate"></i> Public Performance Board Certificate Details
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
                    <label for="owner_name">Producer Name <span class="required">*</span></label>
                    <input 
                        type="text" 
                        id="owner_name" 
                        name="owner_name" 
                        class="form-control" 
                        placeholder="Enter the producer's full name"
                        value="<?= isset($form_data['owner_name']) ? esc($form_data['owner_name']) : '' ?>"
                        required
                    >
                    <div class="form-hint">Enter the name of the drama producer</div>
                </div>

                <div class="form-group">
                    <label for="description">Drama Description <span class="required">*</span></label>
                    <textarea
                        id="description"
                        name="description"
                        class="form-control"
                        placeholder="Provide a short synopsis for your drama"
                        required><?= isset($form_data['description']) ? esc($form_data['description']) : '' ?></textarea>
                    <div class="form-hint">Share the storyline, themes, or highlights that describe your production.</div>
                </div>

                <div class="section-title" style="margin-top: 32px;">
                    <i class="bx bx-image"></i> Certificate Image
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
                            <i class="bx bx-cloud-upload-alt"></i>
                            <span>Click to upload certificate image</span>
                        </label>
                        <div class="file-name" id="file-name"></div>
                    </div>
                    <div class="form-hint">Upload a clear image/photo of your Public Performance Board Certificate. Max size: 5MB (JPG, PNG, PDF)</div>
                </div>

                <div class="btn-group">
                    <a href="<?=ROOT?>/artistdashboard" class="btn btn-secondary">
                        <i class="bx bx-times"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-paper-plane"></i> Submit for Approval
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
