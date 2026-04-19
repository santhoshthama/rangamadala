<?php
if (isset($data) && is_array($data)) {
    extract($data);
}

$errors = $errors ?? [];
$success = $success ?? '';
$entries = $entries ?? [];
$edit_item = $edit_item ?? null;
$form = $form ?? [
    'past_dramas' => '',
    'position_worked' => '',
    'years_in_industry' => '',
    'specialized_fields' => '',
    'education_qualifications' => ''
];

$isEditing = $edit_item && isset($edit_item->id);
$editIdValue = 0;
if ($isEditing && isset($edit_item->id)) {
    $editIdValue = (int)$edit_item->id;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artist Portfolio</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/artist-portfolio.css">
</head>
<body>
    <div class="portfolio-page">
        <div class="portfolio-header">
            <a class="back-link" href="<?= ROOT ?>/profile">
                <i class="bx bx-arrow-back"></i>
                <span>Back to Profile</span>
            </a>
            <h1>Artist Portfolio</h1>
            <p>Show your past dramas, positions, industry experience, specializations, and education details.</p>
        </div>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                <i class="bx bx-check-circle"></i>
                <span><?= esc($success) ?></span>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alerts">
                <?php foreach ($errors as $error): ?>
                    <div class="alert alert-error">
                        <i class="bx bx-error-circle"></i>
                        <span><?= esc($error) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <section class="card">
            <div class="card-title">
                <h2><?= $isEditing ? 'Update Portfolio Entry' : 'Add Portfolio Entry' ?></h2>
            </div>

            <form method="post" class="portfolio-form">
                <input type="hidden" name="action" value="<?= $isEditing ? 'update' : 'create' ?>">
                <?php if ($isEditing): ?>
                    <input type="hidden" name="id" value="<?= $editIdValue ?>">
                <?php endif; ?>

                <div class="field full">
                    <label for="past_dramas">Past Dramas</label>
                    <textarea id="past_dramas" name="past_dramas" rows="3" placeholder="List the dramas you worked in" required><?= esc($form['past_dramas'] ?? '') ?></textarea>
                </div>

                <div class="field">
                    <label for="position_worked">Position Worked</label>
                    <input id="position_worked" name="position_worked" type="text" placeholder="e.g. Lead Actor, Assistant Director" value="<?= esc($form['position_worked'] ?? '') ?>" required>
                </div>

                <div class="field">
                    <label for="years_in_industry">Years in Industry</label>
                    <input id="years_in_industry" name="years_in_industry" type="number" min="0" placeholder="e.g. 8" value="<?= esc($form['years_in_industry'] ?? '') ?>" required>
                </div>

                <div class="field full">
                    <label for="specialized_fields">Fields Specialized In</label>
                    <textarea id="specialized_fields" name="specialized_fields" rows="3" placeholder="e.g. Stage acting, vocal performance, script analysis" required><?= esc($form['specialized_fields'] ?? '') ?></textarea>
                </div>

                <div class="field full">
                    <label for="education_qualifications">Education Qualifications</label>
                    <textarea id="education_qualifications" name="education_qualifications" rows="3" placeholder="Your education or training related to drama" required><?= esc($form['education_qualifications'] ?? '') ?></textarea>
                </div>

                <div class="actions">
                    <?php if ($isEditing): ?>
                        <a class="secondary-btn" href="<?= ROOT ?>/artistportfolio">Cancel Edit</a>
                    <?php endif; ?>
                    <button type="submit" class="primary-btn">
                        <i class="bx bx-save"></i>
                        <span><?= $isEditing ? 'Update Entry' : 'Add Entry' ?></span>
                    </button>
                </div>
            </form>
        </section>

        <section class="card">
            <div class="card-title">
                <h2>Saved Portfolio Entries</h2>
            </div>

            <?php if (empty($entries)): ?>
                <p class="empty-state">No portfolio entries yet. Add your first one above.</p>
            <?php else: ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Past Dramas</th>
                                <th>Position</th>
                                <th>Years</th>
                                <th>Specializations</th>
                                <th>Education</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($entries as $item): ?>
                                <tr>
                                    <td><?= nl2br(esc($item->past_dramas ?? '')) ?></td>
                                    <td><?= esc($item->position_worked ?? '') ?></td>
                                    <td><?= isset($item->years_in_industry) ? esc((string)$item->years_in_industry) : '' ?></td>
                                    <td><?= nl2br(esc($item->specialized_fields ?? '')) ?></td>
                                    <td><?= nl2br(esc($item->education_qualifications ?? '')) ?></td>
                                    <td>
                                        <div class="row-actions">
                                            <a class="edit-btn" href="<?= ROOT ?>/artistportfolio?edit=<?= (int)$item->id ?>">
                                                <i class="bx bx-edit"></i>
                                                Edit
                                            </a>
                                            <form method="post" onsubmit="return confirm('Delete this portfolio entry?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?= (int)$item->id ?>">
                                                <button type="submit" class="delete-btn">
                                                    <i class="bx bx-trash"></i>
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </div>
</body>
</html>