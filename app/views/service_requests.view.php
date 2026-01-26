<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle : 'Service Requests' ?> - Rangamadala</title>
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/service provider/service_provider_dashboard.css">
    <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/service provider/service_requests.css">
    <link rel="shortcut icon" href="<?= ROOT ?>/assets/images/Rangamadala logo.png" type="image/x-icon">
</head>
<body>
    <?php $activePage = 'requests'; include 'includes/service_provider/sidebar.php'; ?>
    
    <div class="main--content">
        <?php include 'includes/service_provider/header.php'; ?>

        <div class="container">
        
        <?php
            // Prepare requests array
            $requests = isset($requests) && is_array($requests) ? $requests : [];
            // Count by status
            $counts = ['all' => 0, 'pending' => 0, 'accepted' => 0, 'completed' => 0, 'rejected' => 0, 'cancelled' => 0];
            foreach ($requests as $r) {
                $st = isset($r->status) ? strtolower($r->status) : 'pending';
                if (!isset($counts[$st])) { $counts[$st] = 0; }
                $counts[$st]++;
                $counts['all']++;
            }
            // Initial tab: show All if anything exists, otherwise pending
            $initialTab = $counts['all'] > 0 ? 'all' : 'pending';
        ?>
        <div class="tabs">
            <button class="tab" id="allTab" onclick="switchTab('all')"><?=$counts['all']?> All</button>
            <button class="tab" id="pendingTab" onclick="switchTab('pending')"><?=$counts['pending']?> Pending</button>
            <button class="tab" id="acceptedTab" onclick="switchTab('accepted')"><?=$counts['accepted']?> Accepted</button>
            <button class="tab" id="completedTab" onclick="switchTab('completed')"><?=$counts['completed']?> Completed</button>
            <button class="tab" id="rejectedTab" onclick="switchTab('rejected')"><?=$counts['rejected']?> Rejected</button>
        </div>

        <div class="requests-list" id="requestsList">
            <?php if (empty($requests)): ?>
                <div class="empty-state">
                    <h3>No requests yet</h3>
                    <p>New service requests will appear here. Keep your profile updated and available to be discoverable by production managers.</p>
                </div>
            <?php else: ?>
                <?php foreach ($requests as $req): 
                    $status = isset($req->status) ? strtolower($req->status) : 'pending';
                    $budget = isset($req->budget) && $req->budget !== null ? number_format((float)$req->budget, 2) : null;
                    $dateLabel = '';
                    if (!empty($req->service_date)) {
                        $dateLabel = 'Service Date: ' . htmlspecialchars($req->service_date);
                    } elseif (!empty($req->start_date) || !empty($req->end_date)) {
                        $dateLabel = 'Schedule: ' . htmlspecialchars($req->start_date) . ' to ' . htmlspecialchars($req->end_date);
                    }
                    $title = (isset($req->drama_name) ? htmlspecialchars($req->drama_name) : '') . ' — ' . (isset($req->service_type) ? htmlspecialchars($req->service_type) : '');
                    $requester = (isset($req->requester_name) ? htmlspecialchars($req->requester_name) : '') . (isset($req->requester_phone) ? ' • ' . htmlspecialchars($req->requester_phone) : '');
                ?>
                <div class="request-item" data-category="<?= htmlspecialchars($status) ?>" style="display: none;">
                    <div class="request-info">
                        <h3><?= $title ?></h3>
                        <div class="request-details">
                            Requested by <?= isset($req->requester_name) ? htmlspecialchars($req->requester_name) : 'Unknown' ?>
                        </div>
                        <?php if ($dateLabel): ?><div class="service-date"><?= $dateLabel ?></div><?php endif; ?>
                        <?php if (!empty($req->service_required)): ?><div class="request-snippet"><?= htmlspecialchars($req->service_required) ?></div><?php endif; ?>
                    </div>
                    <div class="request-actions">
                        <span class="status-badge status-<?= htmlspecialchars($status) ?>"><?= htmlspecialchars($status) ?></span>
                        <?php if ($budget !== null): ?><span class="price">Rs <?= $budget ?></span><?php endif; ?>
                        <button class="btn btn-details" onclick="openDetails(event, <?= htmlspecialchars(json_encode((array)$req)) ?>)">View Details</button>
                        <?php if ($status === 'pending'): ?>
                            <button class="btn btn-reject" onclick="rejectRequest(this)" data-id="<?= (int)$req->id ?>">Reject</button>
                            <button class="btn btn-accept" onclick="acceptRequest(this)" data-id="<?= (int)$req->id ?>">Accept</button>
                        <?php elseif ($status === 'accepted'): ?>
                            <button class="btn btn-update" onclick="updatePayment(this)" data-id="<?= (int)$req->id ?>">Update Payment</button>
                            <button class="btn btn-complete" onclick="markCompleted(this)" data-id="<?= (int)$req->id ?>">Mark Complete</button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        let currentTab = '<?= htmlspecialchars($initialTab) ?>';
        const ENDPOINTS = {
            updateStatus: '<?= ROOT ?>/ServiceRequests/updateStatus',
            updatePayment: '<?= ROOT ?>/ServiceRequests/updatePayment',
        };

        function switchTab(category) {
            // Remove active class from all tabs
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });

            // Add active class to clicked tab
            const tabEl = document.getElementById(category + 'Tab');
            if (tabEl) { tabEl.classList.add('active'); }

            // Hide all requests
            document.querySelectorAll('.request-item').forEach(item => {
                item.style.display = 'none';
            });

            if (category === 'all') {
                document.querySelectorAll('.request-item').forEach(item => {
                    item.style.display = 'flex';
                });
            } else {
                // Show requests for selected category
                document.querySelectorAll(`[data-category="${category}"]`).forEach(item => {
                    item.style.display = 'flex';
                });
            }

            currentTab = category;
        }

        async function acceptRequest(button) {
            const id = button.getAttribute('data-id');
            try {
                const res = await fetch(ENDPOINTS.updateStatus, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({ id, status: 'accepted' }),
                });
                const json = await res.json();
                if (json.success) {
                    button.classList.add('selected');
                    button.textContent = 'Accepted';
                    const badge = button.parentElement.querySelector('.status-badge');
                    if (badge) { badge.className = 'status-badge status-accepted'; badge.textContent = 'accepted'; }
                    showMessage('Request accepted successfully!', 'success');
                } else {
                    showMessage(json.error || 'Failed to accept', 'error');
                }
            } catch (e) {
                showMessage('Network error while accepting', 'error');
            }
        }

        async function rejectRequest(button) {
            const id = button.getAttribute('data-id');
            const reason = prompt('Enter rejection reason:');
            if (reason === null) return; // cancelled
            try {
                const res = await fetch(ENDPOINTS.updateStatus, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({ id, status: 'rejected', reason }),
                });
                const json = await res.json();
                if (json.success) {
                    button.classList.add('selected');
                    button.textContent = 'Rejected';
                    const badge = button.parentElement.querySelector('.status-badge');
                    if (badge) { badge.className = 'status-badge status-rejected'; badge.textContent = 'rejected'; }
                    showMessage('Request rejected', 'error');
                } else {
                    showMessage(json.error || 'Failed to reject', 'error');
                }
            } catch (e) {
                showMessage('Network error while rejecting', 'error');
            }
        }

        async function updatePayment(button) {
            const id = button.getAttribute('data-id');
            const payment_status = prompt('Update payment status to: unpaid/partially_paid/paid', 'paid');
            if (!payment_status) return;
            try {
                const res = await fetch(ENDPOINTS.updatePayment, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({ id, payment_status }),
                });
                const json = await res.json();
                if (json.success) {
                    button.classList.add('selected');
                    button.textContent = 'Payment Updated';
                    showMessage('Payment information updated!', 'success');
                } else {
                    showMessage(json.error || 'Failed to update payment', 'error');
                }
            } catch (e) {
                showMessage('Network error while updating payment', 'error');
            }
        }

        async function markCompleted(button) {
            const id = button.getAttribute('data-id');
            try {
                const res = await fetch(ENDPOINTS.updateStatus, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({ id, status: 'completed' }),
                });
                const json = await res.json();
                if (json.success) {
                    button.classList.add('selected');
                    button.textContent = 'Completed';
                    const badge = button.parentElement.querySelector('.status-badge');
                    if (badge) { badge.className = 'status-badge status-completed'; badge.textContent = 'completed'; }
                    showMessage('Request marked completed', 'success');
                } else {
                    showMessage(json.error || 'Failed to mark completed', 'error');
                }
            } catch (e) {
                showMessage('Network error while completing', 'error');
            }
        }

        function goToDashboard() {
            showMessage('Navigating to Dashboard...', 'info');
        }

        function showMessage(text, type) {
            // Create message element
            const message = document.createElement('div');
            message.className = `message message-${type}`;
            message.textContent = text;
            
            // Style the message
            message.style.position = 'fixed';
            message.style.top = '20px';
            message.style.right = '20px';
            message.style.padding = '12px 20px';
            message.style.borderRadius = '6px';
            message.style.zIndex = '1000';
            message.style.fontWeight = '500';
            message.style.transition = 'all 0.3s ease';
            
            if (type === 'success') {
                message.style.background = '#28a745';
                message.style.color = 'white';
            } else if (type === 'error') {
                message.style.background = '#dc3545';
                message.style.color = 'white';
            } else {
                message.style.background = '#17a2b8';
                message.style.color = 'white';
            }
            
            document.body.appendChild(message);
            
            // Remove message after 3 seconds
            setTimeout(() => {
                message.style.opacity = '0';
                message.style.transform = 'translateY(-20px)';
                setTimeout(() => {
                    document.body.removeChild(message);
                }, 300);
            }, 3000);
        }

        // Initialize - show initial tab based on data
        document.addEventListener('DOMContentLoaded', function() {
            switchTab(currentTab);
        });

        function openDetails(event, req) {
            event.stopPropagation();
            const modal = document.getElementById('detailsModal');
            
            // Build service-specific fields HTML
            let serviceSpecificHTML = '';
            
            if (req.service_type === 'Theater Production') {
                serviceSpecificHTML = `
                    <div style="margin-bottom: 20px;">
                        <strong>Theater Production Details:</strong>
                        <div style="background: #f9f9f9; padding: 12px; border-radius: 4px; margin-top: 8px;">
                            <p style="margin: 5px 0;"><strong>Venue Type:</strong> ${req.theater_venue_type || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Stage Type:</strong> ${[req.theater_stage_proscenium && 'Proscenium', req.theater_stage_black_box && 'Black Box', req.theater_stage_open_floor && 'Open Floor'].filter(Boolean).join(', ') || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Stage Size:</strong> ${req.theater_stage_size || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Number of Days:</strong> ${req.theater_num_days || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Time:</strong> ${req.theater_time || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Budget Range:</strong> ${req.theater_budget_range || 'N/A'}</p>
                        </div>
                    </div>
                `;
            } else if (req.service_type === 'Lighting Design') {
                serviceSpecificHTML = `
                    <div style="margin-bottom: 20px;">
                        <strong>Lighting Design Details:</strong>
                        <div style="background: #f9f9f9; padding: 12px; border-radius: 4px; margin-top: 8px;">
                            <p style="margin: 5px 0;"><strong>Lighting Services:</strong> ${[req.lighting_stage_lighting && 'Stage Lighting', req.lighting_spotlights && 'Spotlights', req.lighting_custom_programming && 'Custom Programming', req.lighting_moving_heads && 'Moving Heads'].filter(Boolean).join(', ') || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Number of Lights:</strong> ${req.lighting_num_lights || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Effects:</strong> ${req.lighting_effects || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Technician Needed:</strong> ${req.lighting_technician_needed || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Budget Range:</strong> ${req.lighting_budget_range || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Additional Requirements:</strong> ${req.lighting_additional_requirements || 'N/A'}</p>
                        </div>
                    </div>
                `;
            } else if (req.service_type === 'Sound Systems') {
                serviceSpecificHTML = `
                    <div style="margin-bottom: 20px;">
                        <strong>Sound Systems Details:</strong>
                        <div style="background: #f9f9f9; padding: 12px; border-radius: 4px; margin-top: 8px;">
                            <p style="margin: 5px 0;"><strong>Sound Services:</strong> ${[req.sound_pa_system && 'PA System', req.sound_microphones && 'Microphones', req.sound_sound_mixing && 'Sound Mixing', req.sound_background_music && 'Background Music', req.sound_special_effects && 'Special Effects'].filter(Boolean).join(', ') || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Number of Mics:</strong> ${req.sound_num_mics || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Stage Monitor:</strong> ${req.sound_stage_monitor || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Sound Engineer:</strong> ${req.sound_sound_engineer || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Budget Range:</strong> ${req.sound_budget_range || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Additional Services:</strong> ${req.sound_additional_services || 'N/A'}</p>
                        </div>
                    </div>
                `;
            } else if (req.service_type === 'Video Production') {
                serviceSpecificHTML = `
                    <div style="margin-bottom: 20px;">
                        <strong>Video Production Details:</strong>
                        <div style="background: #f9f9f9; padding: 12px; border-radius: 4px; margin-top: 8px;">
                            <p style="margin: 5px 0;"><strong>Video Type:</strong> ${[req.video_full_event && 'Full Event', req.video_highlight_reel && 'Highlight Reel', req.video_short_promo && 'Short Promo'].filter(Boolean).join(', ') || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Number of Cameras:</strong> ${req.video_num_cameras || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Drone Coverage:</strong> ${req.video_drone_coverage || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Editors:</strong> ${req.video_editors || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Budget Range:</strong> ${req.video_budget_range || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Additional Requirements:</strong> ${req.video_additional_requirements || 'N/A'}</p>
                        </div>
                    </div>
                `;
            } else if (req.service_type === 'Makeup Services') {
                serviceSpecificHTML = `
                    <div style="margin-bottom: 20px;">
                        <strong>Makeup Services Details:</strong>
                        <div style="background: #f9f9f9; padding: 12px; border-radius: 4px; margin-top: 8px;">
                            <p style="margin: 5px 0;"><strong>Makeup Type:</strong> ${[req.makeup_stage_makeup && 'Stage Makeup', req.makeup_character_makeup && 'Character Makeup', req.makeup_special_effects && 'Special Effects'].filter(Boolean).join(', ') || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Number of Artists:</strong> ${req.makeup_num_artists || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Number of Actors:</strong> ${req.makeup_num_actors || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Special Requirements:</strong> ${req.makeup_special_requirements || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Budget Range:</strong> ${req.makeup_budget_range || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Additional Details:</strong> ${req.makeup_additional_details || 'N/A'}</p>
                        </div>
                    </div>
                `;
            } else if (req.service_type === 'Costume Design') {
                serviceSpecificHTML = `
                    <div style="margin-bottom: 20px;">
                        <strong>Costume Design Details:</strong>
                        <div style="background: #f9f9f9; padding: 12px; border-radius: 4px; margin-top: 8px;">
                            <p style="margin: 5px 0;"><strong>Costume Style:</strong> ${[req.costume_period && 'Period', req.costume_contemporary && 'Contemporary', req.costume_fantasy && 'Fantasy'].filter(Boolean).join(', ') || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Number of Costumes:</strong> ${req.costume_num_costumes || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Number of Actors:</strong> ${req.costume_num_actors || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Alterations Needed:</strong> ${req.costume_alterations_needed || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Budget Range:</strong> ${req.costume_budget_range || 'N/A'}</p>
                            <p style="margin: 5px 0;"><strong>Additional Requirements:</strong> ${req.costume_additional_requirements || 'N/A'}</p>
                        </div>
                    </div>
                `;
            }
            
            document.getElementById('detailsContent').innerHTML = `
                <div style="padding: 20px; background: #fff; border-radius: 8px; max-height: 70vh; overflow-y: auto;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h2 style="margin: 0; color: #333;">${req.service_type || 'Request'} - ${req.drama_name || 'N/A'}</h2>
                        <button onclick="closeDetails()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #666;">&times;</button>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div>
                            <strong>Status:</strong> <span style="padding: 5px 10px; border-radius: 4px; background: #f0f0f0; text-transform: capitalize;">${req.status}</span>
                        </div>
                        <div>
                            <strong>Payment Status:</strong> <span style="padding: 5px 10px; border-radius: 4px; background: #f0f0f0; text-transform: capitalize;">${req.payment_status || 'unpaid'}</span>
                        </div>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <strong>Requester Information:</strong>
                        <div style="background: #f9f9f9; padding: 12px; border-radius: 4px; margin-top: 8px;">
                            <p style="margin: 5px 0;"><strong>Name:</strong> ${req.requester_name}</p>
                            <p style="margin: 5px 0;"><strong>Email:</strong> <a href="mailto:${req.requester_email}">${req.requester_email}</a></p>
                            <p style="margin: 5px 0;"><strong>Phone:</strong> ${req.requester_phone}</p>
                        </div>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <strong>Schedule:</strong>
                        <div style="background: #f9f9f9; padding: 12px; border-radius: 4px; margin-top: 8px;">
                            ${req.service_date ? `<p style="margin: 5px 0;"><strong>Service Date:</strong> ${req.service_date}</p>` : ''}
                            <p style="margin: 5px 0;"><strong>Start Date:</strong> ${req.start_date}</p>
                            <p style="margin: 5px 0;"><strong>End Date:</strong> ${req.end_date}</p>
                        </div>
                    </div>

                    ${serviceSpecificHTML}

                    <div style="margin-bottom: 20px;">
                        <strong>Description:</strong>
                        <div style="background: #f9f9f9; padding: 12px; border-radius: 4px; margin-top: 8px; word-wrap: break-word;">
                            ${req.description || 'No description provided'}
                        </div>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <strong>Notes from Requester:</strong>
                        <div style="background: #f9f9f9; padding: 12px; border-radius: 4px; margin-top: 8px; word-wrap: break-word;">
                            ${req.notes || 'No notes provided'}
                        </div>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <strong>Uploaded References:</strong>
                        <div style="background: #f9f9f9; padding: 12px; border-radius: 4px; margin-top: 8px;">
                            ${req.uploaded_files && Object.keys(req.uploaded_files).length > 0 ? Object.entries(req.uploaded_files).map(([fieldName, fileInfo]) => {
                                return `
                                    <p style="margin: 6px 0; font-size: 12px;">
                                        <a href="${'<?= ROOT ?>'}/${fileInfo.relative_path}" target="_blank" style="color: #007bff; text-decoration: none;">
                                            <i class="fas fa-link"></i> View reference (${fieldName}${fileInfo.original_name ? ' - ' + fileInfo.original_name : ''})
                                        </a>
                                    </p>
                                `;
                            }).join('') : '<p style="margin: 5px 0; font-size: 12px; color: #666;">No files uploaded</p>'}
                        </div>
                    </div>

                    ${req.provider_notes ? `
                    <div style="margin-bottom: 20px;">
                        <strong>Your Notes:</strong>
                        <div style="background: #f0f8ff; padding: 12px; border-radius: 4px; margin-top: 8px; word-wrap: break-word;">
                            ${req.provider_notes}
                        </div>
                    </div>
                    ` : ''}

                    ${req.rejection_reason ? `
                    <div style="margin-bottom: 20px;">
                        <strong>Rejection Reason:</strong>
                        <div style="background: #ffe6e6; padding: 12px; border-radius: 4px; margin-top: 8px; word-wrap: break-word;">
                            ${req.rejection_reason}
                        </div>
                    </div>
                    ` : ''}

                    <div style="border-top: 1px solid #ddd; padding-top: 15px; margin-top: 20px; font-size: 12px; color: #666;">
                        <p style="margin: 5px 0;"><strong>Created:</strong> ${new Date(req.created_at).toLocaleString()}</p>
                        ${req.accepted_at ? `<p style="margin: 5px 0;"><strong>Accepted:</strong> ${new Date(req.accepted_at).toLocaleString()}</p>` : ''}
                        ${req.completed_at ? `<p style="margin: 5px 0;"><strong>Completed:</strong> ${new Date(req.completed_at).toLocaleString()}</p>` : ''}
                    </div>
                </div>
            `;
            modal.style.display = 'flex';
        }

        function closeDetails() {
            document.getElementById('detailsModal').style.display = 'none';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('detailsModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        };
    </script>

    <!-- Details Modal -->
    <div id="detailsModal" style="display: none; position: fixed; z-index: 999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.4); align-items: center; justify-content: center;">
        <div style="background-color: #fefefe; padding: 0; border-radius: 8px; width: 90%; max-width: 700px; box-shadow: 0 4px 6px rgba(0,0,0,0.15);" id="detailsContent">
        </div>
    </div>
    </div>
</body>
</html>
