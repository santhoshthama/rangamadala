<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> - User Details</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
            max-width: 900px;
            margin: 0 auto;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            color: #ba8e23;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .back-link:hover {
            color: #d4af37;
        }

        .user-detail-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(212, 175, 55, 0.12);
            border: 1px solid rgba(212, 175, 55, 0.15);
        }

        .user-detail-header {
            background: linear-gradient(135deg, #ba8e23 0%, #d4af37 100%);
            padding: 30px;
            color: white;
            text-align: center;
        }

        .user-avatar {
            width: 100px;
            height: 100px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 48px;
        }

        .user-detail-header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .user-role-badge {
            display: inline-block;
            padding: 6px 16px;
            background: rgba(255,255,255,0.2);
            border-radius: 20px;
            font-size: 14px;
            text-transform: capitalize;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            margin-top: 10px;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-approved {
            background: #d4edda;
            color: #155724;
        }

        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }

        .user-detail-body {
            padding: 30px;
        }

        .section-title {
            font-size: 18px;
            color: #3d2817;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #d4af37;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-item {
            background: #faf8f3;
            padding: 15px;
            border-radius: 8px;
        }

        .info-item label {
            display: block;
            font-size: 12px;
            color: #8b7355;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .info-item value {
            display: block;
            font-size: 15px;
            color: #3d2817;
            font-weight: 500;
        }

        .nic-section {
            margin-top: 30px;
        }

        .nic-images-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .nic-image-container {
            background: #f5f0e8;
            border-radius: 12px;
            padding: 15px;
        }

        .nic-image-container h4 {
            color: #3d2817;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .nic-image-preview {
            width: 100%;
            height: 250px;
            background: white;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .nic-image-preview:hover {
            transform: scale(1.02);
        }

        .nic-image-preview img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .nic-image-placeholder {
            color: #ba8e23;
            font-size: 14px;
            text-align: center;
        }

        .nic-image-placeholder i {
            font-size: 48px;
            display: block;
            margin-bottom: 10px;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #f0e6d3;
        }

        .btn {
            flex: 1;
            padding: 15px 25px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: center;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-approve {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }

        .btn-approve:hover {
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
            transform: translateY(-2px);
        }

        .btn-reject {
            background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);
            color: white;
        }

        .btn-reject:hover {
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #868e96 100%);
            color: white;
        }

        .btn-secondary:hover {
            box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
            transform: translateY(-2px);
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.3s ease;
        }

        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 30px;
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            margin-bottom: 20px;
            border-bottom: 2px solid #d4af37;
            padding-bottom: 15px;
        }

        .modal-header h2 {
            color: #3d2817;
            font-size: 22px;
        }

        .modal-close {
            color: #ba8e23;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .modal-close:hover {
            color: #d4af37;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #3d2817;
            font-weight: 600;
        }

        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #d4af37;
            border-radius: 6px;
            font-family: 'Poppins', sans-serif;
            resize: vertical;
            min-height: 120px;
        }

        .form-group textarea:focus {
            outline: none;
            border-color: #ba8e23;
            box-shadow: 0 0 8px rgba(212, 175, 55, 0.3);
        }

        .modal-actions {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }

        .modal-actions .btn {
            flex: 1;
        }

        /* Image Preview Modal */
        .image-modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.9);
            animation: fadeIn 0.3s ease;
        }

        .image-modal-content {
            max-width: 90%;
            max-height: 90%;
            margin: auto;
            display: block;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .image-modal-close {
            position: absolute;
            top: 20px;
            right: 30px;
            color: white;
            font-size: 40px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .image-modal-close:hover {
            color: #d4af37;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .rejection-reason-box {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
        }

        .rejection-reason-box h4 {
            color: #721c24;
            margin-bottom: 10px;
        }

        .rejection-reason-box p {
            color: #856404;
        }

        @media (max-width: 768px) {
            .action-buttons {
                flex-direction: column;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .nic-images-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="<?= ROOT ?>/UserVerification/pending" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Verification List
        </a>

        <?php if (!empty($user)): ?>
            <div class="user-detail-card">
                <div class="user-detail-header">
                    <div class="user-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <h1><?= htmlspecialchars($user->full_name) ?></h1>
                    <span class="user-role-badge">
                        <i class="fas fa-<?= $user->role === 'artist' ? 'palette' : 'briefcase' ?>"></i>
                        <?= htmlspecialchars(str_replace('_', ' ', $user->role)) ?>
                    </span>
                    <?php 
                        $status = $user->verification_status ?? 'pending';
                        $statusClass = 'status-pending';
                        if ($status === 'approved') $statusClass = 'status-approved';
                        elseif ($status === 'rejected') $statusClass = 'status-rejected';
                    ?>
                    <br>
                    <span class="status-badge <?= $statusClass ?>"><?= ucfirst($status) ?></span>
                </div>

                <div class="user-detail-body">
                    <h3 class="section-title"><i class="fas fa-info-circle"></i> Basic Information</h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <label>Full Name</label>
                            <value><?= htmlspecialchars($user->full_name) ?></value>
                        </div>
                        <div class="info-item">
                            <label>Email Address</label>
                            <value><?= htmlspecialchars($user->email) ?></value>
                        </div>
                        <div class="info-item">
                            <label>Phone Number</label>
                            <value><?= htmlspecialchars($user->phone ?? 'Not provided') ?></value>
                        </div>
                        <div class="info-item">
                            <label>Role</label>
                            <value><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $user->role))) ?></value>
                        </div>
                        <div class="info-item">
                            <label>Registration Date</label>
                            <value><?= date('d M Y - H:i A', strtotime($user->created_at)) ?></value>
                        </div>
                        <div class="info-item">
                            <label>Verification Status</label>
                            <value><span class="status-badge <?= $statusClass ?>"><?= ucfirst($status) ?></span></value>
                        </div>
                    </div>

                    <?php if (!empty($user->rejection_reason)): ?>
                        <div class="rejection-reason-box">
                            <h4><i class="fas fa-exclamation-triangle"></i> Rejection Reason</h4>
                            <p><?= htmlspecialchars($user->rejection_reason) ?></p>
                        </div>
                    <?php endif; ?>

                    <div class="nic-section">
                        <h3 class="section-title"><i class="fas fa-id-card"></i> NIC Verification Documents</h3>
                        <div class="nic-images-grid">
                            <div class="nic-image-container">
                                <h4>NIC Photo</h4>
                                <div class="nic-image-preview" onclick="openImageModal('<?= ROOT ?>/<?= htmlspecialchars($user->nic_photo ?? '') ?>')">
                                    <?php if (!empty($user->nic_photo)): ?>
                                        <img src="<?= ROOT ?>/<?= htmlspecialchars($user->nic_photo) ?>" alt="NIC Photo">
                                    <?php else: ?>
                                        <div class="nic-image-placeholder">
                                            <i class="fas fa-id-card"></i>
                                            No NIC Image Uploaded
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php 
                        $showActions = ($user->verification_status ?? 'pending') === 'pending';
                    ?>
                    <?php if ($showActions): ?>
                        <div class="action-buttons">
                            <button class="btn btn-approve" onclick="approveUser(<?= $user->id ?>, '<?= htmlspecialchars($user->full_name) ?>')">
                                <i class="fas fa-check"></i> Approve User
                            </button>
                            <button class="btn btn-reject" onclick="showRejectModal(<?= $user->id ?>, '<?= htmlspecialchars($user->full_name) ?>')">
                                <i class="fas fa-times"></i> Reject User
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="user-detail-card">
                <div class="user-detail-body" style="text-align: center; padding: 60px;">
                    <i class="fas fa-user-slash" style="font-size: 48px; color: #d4af37; margin-bottom: 20px;"></i>
                    <h2 style="color: #3d2817;">User Not Found</h2>
                    <p style="color: #8b7355;">The requested user could not be found or may have been deleted.</p>
                    <a href="<?= ROOT ?>/UserVerification/pending" class="btn btn-secondary" style="margin-top: 20px; display: inline-flex;">
                        <i class="fas fa-arrow-left"></i> Go Back
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="modal-close" onclick="closeRejectModal()">&times;</span>
                <h2>Reject User Registration</h2>
            </div>
            <form id="rejectForm" method="POST" action="<?= ROOT ?>/UserVerification/reject">
                <input type="hidden" name="user_id" id="rejectUserId">
                
                <div class="form-group">
                    <label>Rejection Reason <span style="color: #dc3545;">*</span></label>
                    <textarea name="rejection_reason" id="rejectionReason" placeholder="Please provide a detailed reason for rejection. This will be shown to the user when they try to login." required></textarea>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeRejectModal()">Cancel</button>
                    <button type="submit" class="btn btn-reject">Confirm Rejection</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Image Preview Modal -->
    <div id="imageModal" class="image-modal" onclick="closeImageModal()">
        <span class="image-modal-close">&times;</span>
        <img id="modalImage" class="image-modal-content" src="" alt="NIC Preview">
    </div>

    <script>
        function approveUser(userId, userName) {
            if (confirm(`Are you sure you want to approve ${userName}?\n\nOnce approved, they will be able to login and access their dashboard.`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?= ROOT ?>/UserVerification/approve';
                form.innerHTML = '<input type="hidden" name="user_id" value="' + userId + '">';
                document.body.appendChild(form);
                form.submit();
            }
        }

        function showRejectModal(userId, userName) {
            document.getElementById('rejectUserId').value = userId;
            document.getElementById('rejectModal').style.display = 'block';
            document.getElementById('rejectionReason').focus();
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').style.display = 'none';
            document.getElementById('rejectionReason').value = '';
        }

        function openImageModal(imageSrc) {
            if (imageSrc && !imageSrc.includes('undefined') && !imageSrc.endsWith('/')) {
                document.getElementById('modalImage').src = imageSrc;
                document.getElementById('imageModal').style.display = 'block';
            }
        }

        function closeImageModal() {
            document.getElementById('imageModal').style.display = 'none';
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            const rejectModal = document.getElementById('rejectModal');
            if (event.target === rejectModal) {
                rejectModal.style.display = 'none';
            }
        };

        // Close image modal on Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeImageModal();
                closeRejectModal();
            }
        });
    </script>
</body>
</html>
