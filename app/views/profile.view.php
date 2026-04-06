<?php
if (isset($data) && is_array($data)) {
    extract($data);
}

$user = $user ?? null;
$form = $form ?? ['full_name' => '', 'phone' => '', 'years_experience' => '', 'bio' => '', 'location' => '', 'website' => ''];
$errors = $errors ?? [];
$success = $success ?? '';

$role = $_SESSION['user_role'] ?? 'artist';
$dashboardLinks = [
    'artist' => ROOT . '/artistdashboard',
    'director' => ROOT . '/director',
    'admin' => ROOT . '/admindashboard',
    'audience' => ROOT . '/audiencedashboard',
    'service_provider' => ROOT . '/serviceproviderdashboard'
];
$dashboardLink = $dashboardLinks[$role] ?? ROOT . '/artistdashboard';
$pageTitle = ucfirst(str_replace('_', ' ', $role)) . ' Profile';

$profileImageSrc = ROOT . '/assets/images/default-avatar.jpg';
if ($user && !empty($user->profile_image)) {
    $imageValue = str_replace('\\', '/', $user->profile_image);
    if (strpos($imageValue, '/') !== false) {
        $profileImageSrc = ROOT . '/' . ltrim($imageValue, '/');
    } else {
        $profileImageSrc = ROOT . '/uploads/profile_images/' . rawurlencode($imageValue);
    }
} elseif ($user && !empty($user->nic_photo)) {
    $profileImageSrc = ROOT . '/' . ltrim(str_replace('\\', '/', $user->nic_photo), '/');
}

$nicDownload = $user && !empty($user->nic_photo)
    ? ROOT . '/' . ltrim(str_replace('\\', '/', $user->nic_photo), '/')
    : '';

$currentImageLabel = $user && !empty($user->profile_image)
    ? basename(str_replace('\\', '/', $user->profile_image))
    : 'Recommended 800x800 JPG/PNG';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($pageTitle) ?></title>
    <style>
        :root { --brand:#ba8e23; --brand-strong:#a0781e; --card:#fff; --ink:#1f2933; --muted:#6b7280; --danger:#dc2626; --success:#15803d; --border:#e5e7eb; --shadow:0 12px 40px rgba(31,41,51,.12); }
        * { box-sizing: border-box; }
        body { margin:0; font-family: Arial, sans-serif; background: linear-gradient(135deg, rgba(186,142,35,.12), rgba(160,120,30,.08)); color:var(--ink); min-height:100vh; padding:24px; }
        a { color:inherit; text-decoration:none; }
        .page-wrapper { width:min(1100px, 100%); margin:0 auto; }
        .back-link { display:inline-flex; align-items:center; gap:8px; font-weight:600; color:var(--brand-strong); margin-bottom:16px; }
        .profile-card { background:var(--card); border-radius:16px; box-shadow:var(--shadow); display:grid; grid-template-columns:320px 1fr; overflow:hidden; }
        .profile-summary { background: linear-gradient(180deg, rgba(186,142,35,.95), rgba(160,120,30,.92)); color:#fff; padding:40px 32px; display:flex; flex-direction:column; justify-content:space-between; }
        .profile-summary img { width:160px; height:160px; border-radius:20px; object-fit:cover; border:6px solid rgba(255,255,255,.15); box-shadow:0 18px 40px rgba(0,0,0,.24); align-self:center; }
        .profile-summary h2 { margin:28px 0 8px; font-size:28px; }
        .profile-summary p { margin:4px 0; font-size:15px; color:rgba(255,255,255,.85); }
        .summary-item { margin-top:24px; }
        .summary-item span { display:block; font-size:13px; text-transform:uppercase; letter-spacing:1.6px; color:rgba(255,255,255,.6); }
        .summary-item strong { font-size:18px; font-weight:600; }
        .summary-actions { margin-top:32px; display:flex; flex-direction:column; gap:12px; }
        .summary-actions a { display:inline-flex; align-items:center; gap:10px; padding:12px 18px; border-radius:12px; background:rgba(255,255,255,.12); color:#fff; font-weight:600; }
        .profile-form { padding:40px; }
        .profile-form h1 { margin:0 0 16px; font-size:30px; }
        .profile-form p.subtitle { margin:0 0 32px; color:var(--muted); font-size:15px; }
        .alerts { margin-bottom:24px; display:flex; flex-direction:column; gap:12px; }
        .alert { padding:14px 18px; border-radius:12px; font-size:14px; font-weight:500; }
        .alert-success { background:rgba(21,128,61,.12); color:var(--success); border:1px solid rgba(21,128,61,.35); }
        .alert-error { background:rgba(220,38,38,.1); color:var(--danger); border:1px solid rgba(220,38,38,.3); }
        form { display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:20px 24px; }
        .form-group { display:flex; flex-direction:column; gap:8px; }
        .form-group.full { grid-column:1 / -1; }
        label { font-weight:600; font-size:14px; color:var(--muted); text-transform:uppercase; letter-spacing:1.2px; }
        input[type="text"], input[type="email"], input[type="tel"], input[type="url"], input[type="number"], textarea { padding:14px 16px; border-radius:12px; border:1px solid var(--border); font-size:15px; font-family:inherit; }
        input:focus, textarea:focus { outline:none; border-color:var(--brand); box-shadow:0 0 0 4px rgba(186,142,35,.18); }
        textarea { resize:vertical; min-height:100px; }
        input[readonly] { background:#f9fafb; color:#9ca3af; }
        .file-input { display:flex; align-items:center; gap:16px; }
        .file-input label { cursor:pointer; padding:12px 18px; border-radius:12px; background:rgba(186,142,35,.12); color:var(--brand-strong); font-weight:600; text-transform:none; letter-spacing:normal; }
        .file-input span { font-size:14px; color:var(--muted); }
        input[type="file"] { display:none; }
        .form-actions { grid-column:1 / -1; margin-top:12px; display:flex; justify-content:flex-end; }
        button[type="submit"] { padding:14px 26px; border-radius:12px; border:none; background:linear-gradient(135deg, var(--brand), var(--brand-strong)); color:#fff; font-size:15px; font-weight:700; cursor:pointer; }
        @media (max-width:960px) { .profile-card { grid-template-columns:1fr; } .profile-summary { text-align:center; } .summary-actions { align-items:center; } }
        @media (max-width:768px) { form { grid-template-columns:1fr; } .form-actions { justify-content:center; } }
    </style>
</head>
<body>
    <div class="page-wrapper">
        <a class="back-link" href="<?= esc($dashboardLink) ?>"><i class="fas fa-arrow-left"></i><span>Back to Dashboard</span></a>
        <div class="profile-card">
            <aside class="profile-summary">
                <img src="<?= esc($profileImageSrc) ?>" alt="Profile" onerror="this.src='<?= ROOT ?>/assets/images/default-avatar.jpg'">
                <div>
                    <h2><?= $user ? esc($user->full_name ?? 'User') : 'User' ?></h2>
                    <p><i class="fas fa-envelope"></i> <?= $user ? esc($user->email ?? 'N/A') : 'N/A' ?></p>
                    <p><i class="fas fa-phone"></i> <?= $user ? esc($user->phone ?? 'N/A') : 'N/A' ?></p>
                    <?php if ($user && !empty($user->location)): ?><p><i class="fas fa-map-marker-alt"></i> <?= esc($user->location) ?></p><?php endif; ?>
                    <div class="summary-item">
                        <span>Years of Experience</span>
                        <strong><?= $user && isset($user->years_experience) && $user->years_experience !== null ? esc($user->years_experience) . ' years' : 'Not added yet' ?></strong>
                    </div>
                    <?php if ($nicDownload): ?><div class="summary-actions"><a href="<?= esc($nicDownload) ?>" target="_blank" rel="noopener"><i class="fas fa-id-card"></i> View NIC Upload</a></div><?php endif; ?>
                </div>
            </aside>
            <section class="profile-form">
                <h1>Profile Details</h1>
                <p class="subtitle">Keep your information up to date.</p>
                <?php if (!empty($success)): ?><div class="alerts"><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= esc($success) ?></div></div><?php endif; ?>
                <?php if (!empty($errors)): ?><div class="alerts"><?php foreach ($errors as $error): ?><div class="alert alert-error"><i class="fas fa-exclamation-triangle"></i> <?= esc($error) ?></div><?php endforeach; ?></div><?php endif; ?>
                <form method="post" enctype="multipart/form-data">
                    <div class="form-group"><label for="full_name">Full Name</label><input id="full_name" name="full_name" type="text" value="<?= esc($form['full_name'] ?? '') ?>" required></div>
                    <div class="form-group"><label for="email">Email</label><input id="email" type="email" value="<?= $user ? esc($user->email ?? '') : '' ?>" readonly></div>
                    <div class="form-group"><label for="phone">Phone</label><input id="phone" name="phone" type="tel" value="<?= esc($form['phone'] ?? '') ?>" required></div>
                    <div class="form-group"><label for="years_experience">Years of Experience</label><input id="years_experience" name="years_experience" type="number" min="0" value="<?= esc($form['years_experience'] ?? '') ?>"></div>
                    <div class="form-group"><label for="location">Location</label><input id="location" name="location" type="text" placeholder="e.g. Colombo, Sri Lanka" value="<?= esc($form['location'] ?? '') ?>"></div>
                    <div class="form-group full"><label for="bio">Bio / About Me</label><textarea id="bio" name="bio" rows="4" placeholder="Tell us about yourself..."> <?= esc($form['bio'] ?? '') ?></textarea></div>
                    <div class="form-group full"><label for="website">Website / Portfolio URL</label><input id="website" name="website" type="url" placeholder="https://yourwebsite.com" value="<?= esc($form['website'] ?? '') ?>"></div>
                    <div class="form-group full"><label>Profile Image</label><div class="file-input"><label for="profile_image"><i class="fas fa-upload"></i> <span>Upload new image</span></label><input id="profile_image" name="profile_image" type="file" accept="image/*"><span><?= esc($currentImageLabel) ?></span></div></div>
                    <div class="form-actions"><button type="submit"><i class="fas fa-save"></i> Save Changes</button></div>
                </form>
            </section>
        </div>
    </div>
</body>
</html>
