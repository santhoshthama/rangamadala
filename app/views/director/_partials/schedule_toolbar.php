<?php
/**
 * Reusable schedule action toolbar.
 * Optional variables:
 * - $scheduleToolbarConfig (array)
 *   - showRehearsal (bool)
 *   - showInterview (bool)
 *   - showCreate (bool)
 *   - createLabel (string)
 *   - createIcon (string)
 *   - createType (string|null)
 *
 * Backward-compatible optional variables:
 * - $scheduleToolbarShowRehearsal (bool)
 * - $scheduleToolbarShowInterview (bool)
 * - $scheduleToolbarShowCreate (bool)
 * - $scheduleToolbarCreateLabel (string)
 * - $scheduleToolbarCreateIcon (string)
 * - $scheduleToolbarCreateType (string|null)
 */

$scheduleToolbarConfig = isset($scheduleToolbarConfig) && is_array($scheduleToolbarConfig)
    ? $scheduleToolbarConfig
    : [];

$scheduleToolbarShowRehearsal = array_key_exists('showRehearsal', $scheduleToolbarConfig)
    ? (bool)$scheduleToolbarConfig['showRehearsal']
    : (isset($scheduleToolbarShowRehearsal)
    ? (bool)$scheduleToolbarShowRehearsal
    : true);

$scheduleToolbarShowInterview = array_key_exists('showInterview', $scheduleToolbarConfig)
    ? (bool)$scheduleToolbarConfig['showInterview']
    : (isset($scheduleToolbarShowInterview)
    ? (bool)$scheduleToolbarShowInterview
    : true);

$scheduleToolbarShowCreate = array_key_exists('showCreate', $scheduleToolbarConfig)
    ? (bool)$scheduleToolbarConfig['showCreate']
    : (isset($scheduleToolbarShowCreate)
    ? (bool)$scheduleToolbarShowCreate
    : false);

$scheduleToolbarCreateLabelRaw = array_key_exists('createLabel', $scheduleToolbarConfig)
    ? (string)$scheduleToolbarConfig['createLabel']
    : (isset($scheduleToolbarCreateLabel) ? (string)$scheduleToolbarCreateLabel : '');

$scheduleToolbarCreateLabel = trim($scheduleToolbarCreateLabelRaw) !== ''
    ? trim($scheduleToolbarCreateLabelRaw)
    : 'Create Event';

$scheduleToolbarCreateIconRaw = array_key_exists('createIcon', $scheduleToolbarConfig)
    ? (string)$scheduleToolbarConfig['createIcon']
    : (isset($scheduleToolbarCreateIcon) ? (string)$scheduleToolbarCreateIcon : '');

$scheduleToolbarCreateIcon = trim($scheduleToolbarCreateIconRaw) !== ''
    ? trim($scheduleToolbarCreateIconRaw)
    : 'bx-plus';

$scheduleToolbarCreateType = null;
$scheduleToolbarCreateTypeRaw = array_key_exists('createType', $scheduleToolbarConfig)
    ? (string)$scheduleToolbarConfig['createType']
    : (isset($scheduleToolbarCreateType) ? (string)$scheduleToolbarCreateType : '');

if (trim($scheduleToolbarCreateTypeRaw) !== '') {
    $scheduleToolbarCreateType = trim($scheduleToolbarCreateTypeRaw);
}

$scheduleToolbarCreateOnclick = $scheduleToolbarCreateType !== null
    ? "openCreateModal('" . addslashes($scheduleToolbarCreateType) . "')"
    : 'openCreateModal()';
?>
<div class="schedule-toolbar">
    <?php if ($scheduleToolbarShowCreate): ?>
        <button class="schedule-action-btn rehearsal" onclick="<?= esc($scheduleToolbarCreateOnclick) ?>">
            <i class="bx <?= esc($scheduleToolbarCreateIcon) ?>"></i> <?= esc($scheduleToolbarCreateLabel) ?>
        </button>
    <?php endif; ?>

    <?php if ($scheduleToolbarShowRehearsal): ?>
        <button class="schedule-action-btn rehearsal" onclick="openCreateModal('rehearsal')">
            <i class="bx bx-theater-masks"></i> Schedule Rehearsal
        </button>
    <?php endif; ?>

    <?php if ($scheduleToolbarShowInterview): ?>
        <button class="schedule-action-btn interview" onclick="openCreateModal('interview')">
            <i class="bx bx-user-check"></i> Schedule Interview
        </button>
    <?php endif; ?>
</div>
