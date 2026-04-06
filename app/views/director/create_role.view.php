<?php
if (isset($data) && is_array($data)) {
    extract($data);
}

$formData = isset($formData) && is_array($formData) ? $formData : [];
$formErrors = isset($formErrors) && is_array($formErrors) ? $formErrors : [];

$roleTypes = [
    'lead' => 'Lead',
    'supporting' => 'Supporting',
    'ensemble' => 'Ensemble',
    'dancer' => 'Dancer',
    'musician' => 'Musician',
    'other' => 'Other',
];

$dramaId = isset($drama->id) ? (int)$drama->id : (int)($_GET['drama_id'] ?? 0);
$dramaName = isset($drama->drama_name) ? $drama->drama_name : 'Drama';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Role - <?= esc($dramaName) ?> - Rangamadala</title>
    <link rel="stylesheet" href="/Rangamadala/public/assets/CSS/ui-theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .form-card {
            background: #fff;
            border-radius: 18px;
            padding: 28px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm, 0 12px 30px rgba(15, 23, 42, 0.12));
            max-width: 740px;
            margin: 0 auto;
        }
        .form-grid { display: grid; gap: 18px; }
        .form-row { display: flex; gap: 18px; flex-wrap: wrap; }
        .form-group { flex: 1 1 220px; display: flex; flex-direction: column; }
        label { font-weight: 600; margin-bottom: 6px; }
        .form-error { color: var(--danger); font-size: 12px; margin-top: 6px; }
        .form-footer { display: flex; gap: 12px; margin-top: 24px; }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="logo"><h2>🎭</h2></div>
        <ul class="menu">
            <li><a href="<?= ROOT ?>/director/dashboard?drama_id=<?= esc($dramaId) ?>"><i class="fas fa-home"></i><span>Dashboard</span></a></li>
            <li><a href="<?= ROOT ?>/director/drama_details?drama_id=<?= esc($dramaId) ?>"><i class="fas fa-film"></i><span>Drama Details</span></a></li>
            <li class="active"><a href="<?= ROOT ?>/director/manage_roles?drama_id=<?= esc($dramaId) ?>"><i class="fas fa-users"></i><span>Artist Roles</span></a></li>
            <li><a href="<?= ROOT ?>/artistdashboard"><i class="fas fa-arrow-left"></i><span>Back to Profile</span></a></li>
        </ul>
    </aside>

    <main class="main--content">
        <a href="<?= ROOT ?>/director/manage_roles?drama_id=<?= esc($dramaId) ?>" class="back-button"><i class="fas fa-arrow-left"></i>Back to Manage Roles</a>

        <div class="header--wrapper">
            <div class="header--title">
                <span><?= esc($dramaName) ?></span>
                <h2>Create a New Role</h2>
                <p style="color: var(--muted); font-size: 14px; margin-top: 8px;">Describe the character, requirements, and opportunities to invite the right artists.</p>
            </div>
        </div>

        <div class="form-card">
            <form action="<?= ROOT ?>/director/create_role?drama_id=<?= esc($dramaId) ?>" method="POST" class="form-grid">
                <div class="form-group">
                    <label for="role_name">Role Name <span class="required">*</span></label>
                    <input type="text" id="role_name" name="role_name" class="form-control" value="<?= esc($formData['role_name'] ?? '') ?>" required>
                    <?php if (isset($formErrors['role_name'])): ?><div class="form-error"><?= esc($formErrors['role_name']) ?></div><?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="role_type">Role Type <span class="required">*</span></label>
                    <select id="role_type" name="role_type" class="form-control" required>
                        <?php foreach ($roleTypes as $typeKey => $typeLabel): ?>
                            <option value="<?= esc($typeKey) ?>" <?= (($formData['role_type'] ?? 'supporting') === $typeKey) ? 'selected' : '' ?>><?= esc($typeLabel) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($formErrors['role_type'])): ?><div class="form-error"><?= esc($formErrors['role_type']) ?></div><?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="role_description">Description <span class="required">*</span></label>
                    <textarea id="role_description" name="role_description" class="form-control" rows="4" required><?= esc($formData['role_description'] ?? '') ?></textarea>
                    <?php if (isset($formErrors['role_description'])): ?><div class="form-error"><?= esc($formErrors['role_description']) ?></div><?php endif; ?>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="salary">Salary (LKR)</label>
                        <input type="number" step="0.01" min="0" id="salary" name="salary" class="form-control" value="<?= esc($formData['salary'] ?? '') ?>">
                        <?php if (isset($formErrors['salary'])): ?><div class="form-error"><?= esc($formErrors['salary']) ?></div><?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="positions_available">Positions Available <span class="required">*</span></label>
                        <input type="number" min="1" id="positions_available" name="positions_available" class="form-control" value="<?= esc($formData['positions_available'] ?? '1') ?>" required>
                        <?php if (isset($formErrors['positions_available'])): ?><div class="form-error"><?= esc($formErrors['positions_available']) ?></div><?php endif; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="requirements">Special Requirements</label>
                    <textarea id="requirements" name="requirements" class="form-control" rows="3" placeholder="Optional notes on experience, language, physique, etc."><?= esc($formData['requirements'] ?? '') ?></textarea>
                    <?php if (isset($formErrors['requirements'])): ?><div class="form-error"><?= esc($formErrors['requirements']) ?></div><?php endif; ?>
                </div>

                <div class="form-footer">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i>Create Role</button>
                    <a href="<?= ROOT ?>/director/manage_roles?drama_id=<?= esc($dramaId) ?>" class="btn btn-secondary"><i class="fas fa-times"></i>Cancel</a>
                </div>
            </form>
        </div>
    </main>

    <script src="/Rangamadala/public/assets/JS/manage-roles.js"></script>
</body>
</html>
