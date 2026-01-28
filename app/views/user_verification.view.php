<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> - User Verification</title>
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
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.1);
            margin-bottom: 30px;
            border-left: 5px solid #ba8e23;
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
            box-shadow: 0 12px 28px rgba(212, 175, 55, 0.25);
            border-color: #ba8e23;
        }

        .user-card-header {
            background: linear-gradient(135deg, #ba8e23 0%, #d4af37 100%);
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

        .nic-image-preview {
            width: 100%;
            height: 200px;
            background: #f5f0e8;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            overflow: hidden;
        }

        .nic-image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .nic-image-placeholder {
            color: #ba8e23;
            font-size: 13px;
            text-align: center;
        }

        .card-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .btn {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
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

        .btn-view {
            background: linear-gradient(135deg, #d4af37 0%, #e8d5a8 100%);
            color: #3d2817;
        }

        .btn-view:hover {
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.3);
            transform: translateY(-2px);
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

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            margin-top: 8px;
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

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
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
            <i class="fas fa-arrow-left"></i> Back to Admin Dashboard
        </a>

        <div class="header">
            <h1>User Verification Management</h1>
            <p>Review and verify user NIC uploads for account activation</p>
        </div>

        <div class="tabs">
            <a href="<?= ROOT ?>/UserVerification/pending" class="tab-btn <?= strpos($_SERVER['REQUEST_URI'], 'pending') !== false ? 'active' : '' ?>">
                <i class="fas fa-hourglass-half"></i> Pending
            </a>
            <a href="<?= ROOT ?>/UserVerification/verified" class="tab-btn <?= strpos($_SERVER['REQUEST_URI'], 'verified') !== false ? 'active' : '' ?>">
                <i class="fas fa-check-circle"></i> Verified
            </a>
            <a href="<?= ROOT ?>/UserVerification/rejected" class="tab-btn <?= strpos($_SERVER['REQUEST_URI'], 'rejected') !== false ? 'active' : '' ?>">
                <i class="fas fa-times-circle"></i> Rejected
            </a>
        </div>

        <?php if (!empty($pending_users)): ?>
            <div class="users-grid">
                <?php foreach ($pending_users as $user): ?>
                    <div class="user-card">
                        <div class="user-card-header">
                            <h3><?= htmlspecialchars($user->full_name) ?></h3>
                            <p><?= htmlspecialchars($user->role) ?></p>
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
                                <label>Applied On</label>
                                <value><?= date('d M Y - H:i', strtotime($user->created_at)) ?></value>
                            </div>

                            <div class="nic-image-preview">
                                <?php if (!empty($user->nic_photo) && file_exists(ROOT_PATH . '/public/' . $user->nic_photo)): ?>
                                    <img src="<?= ROOT ?>/<?= htmlspecialchars($user->nic_photo) ?>" alt="NIC Photo">
                                <?php else: ?>
                                    <div class="nic-image-placeholder">
                                        <i class="fas fa-id-card"></i><br>
                                        No NIC Image Uploaded
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="card-actions">
                                <button class="btn btn-view" onclick="viewUserDetails(<?= $user->id ?>)">
                                    <i class="fas fa-eye"></i> View
                                </button>
                                <button class="btn btn-approve" onclick="approveUser(<?= $user->id ?>, '<?= htmlspecialchars($user->full_name) ?>')">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                                <button class="btn btn-reject" onclick="showRejectModal(<?= $user->id ?>, '<?= htmlspecialchars($user->full_name) ?>')">
                                    <i class="fas fa-times"></i> Reject
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php elseif (!empty($verified_users)): ?>
            <div class="users-grid">
                <?php foreach ($verified_users as $user): ?>
                    <div class="user-card">
                        <div class="user-card-header" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                            <h3><?= htmlspecialchars($user->full_name) ?></h3>
                            <p><?= htmlspecialchars($user->role) ?></p>
                            <span class="status-badge status-approved">Verified</span>
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
                                <label>Verified On</label>
                                <value><?= date('d M Y - H:i', strtotime($user->verified_at)) ?></value>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php elseif (!empty($rejected_users)): ?>
            <div class="users-grid">
                <?php foreach ($rejected_users as $user): ?>
                    <div class="user-card">
                        <div class="user-card-header" style="background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);">
                            <h3><?= htmlspecialchars($user->full_name) ?></h3>
                            <p><?= htmlspecialchars($user->role) ?></p>
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
                                <label>Rejection Reason</label>
                                <value><?= htmlspecialchars($user->rejection_reason ?? 'No reason provided') ?></value>
                            </div>
                            <div class="user-info">
                                <label>Rejected On</label>
                                <value><?= date('d M Y - H:i', strtotime($user->verified_at)) ?></value>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h3>No Users Found</h3>
                <p>There are no users to display in this section.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="modal-close" onclick="closeRejectModal()">&times;</span>
                <h2>Reject User</h2>
            </div>
            <form id="rejectForm" method="POST" action="<?= ROOT ?>/UserVerification/reject">
                <input type="hidden" name="user_id" id="rejectUserId">
                
                <div class="form-group">
                    <label>Rejection Reason</label>
                    <textarea name="rejection_reason" id="rejectionReason" placeholder="Provide a detailed reason for rejection..." required></textarea>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn btn-view" onclick="closeRejectModal()">Cancel</button>
                    <button type="submit" class="btn btn-reject">Confirm Rejection</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function approveUser(userId, userName) {
            if (confirm(`Are you sure you want to approve ${userName}?`)) {
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

        function viewUserDetails(userId) {
            window.location.href = '<?= ROOT ?>/UserVerification/viewUser?id=' + userId;
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('rejectModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        };
    </script>
</body>
</html>
