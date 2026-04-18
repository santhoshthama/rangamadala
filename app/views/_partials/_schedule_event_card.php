<?php
/**
 * Schedule Event Card Partial
 * Variables expected: $evt, $dramaId, $isPast (optional)
 */
$isPastEvent = isset($isPast) && $isPast;
$evtDate = date('F d, Y', strtotime($evt->scheduled_date));
$startTime = date('g:i A', strtotime($evt->start_time));
$endTime = date('g:i A', strtotime($evt->end_time));
$typeIcon = $evt->type_icon ?? 'bx-calendar';
$badgeClass = $evt->badge_class ?? 'pending';
$badgeVariantClass = $evt->badge_variant_class ?? 'status-badge-default';
?>
<div class="event-card" data-event-id="<?= (int)$evt->id ?>">
    <div class="event-header">
        <div class="event-main">
            <strong class="event-title">
                <i class="bx <?= $typeIcon ?>"></i>
                <?= esc($evt->event_title) ?>
            </strong>
            <div class="event-meta">
                <i class="bx bx-calendar"></i> <?= esc($evtDate) ?> | <i class="bx bx-time"></i> <?= esc($startTime) ?> - <?= esc($endTime) ?> | <i class="bx bx-map"></i> <?= esc($evt->venue) ?>
            </div>
            <?php if (!empty($evt->role_name)): ?>
                <div class="event-meta event-meta-tight">
                    <i class="bx bx-user-tag"></i> Role: <strong><?= esc($evt->role_name) ?></strong>
                </div>
            <?php endif; ?>
            <?php if (!empty($evt->event_description)): ?>
                <div class="event-meta event-meta-tight">
                    <?= nl2br(esc($evt->event_description)) ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($evt->notes)): ?>
                <div class="event-meta event-meta-notes">
                    <i class="bx bx-sticky-note"></i> <?= esc($evt->notes) ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="event-actions">
            <span class="status-badge <?= $badgeClass ?> <?= esc($badgeVariantClass) ?>">
                <?= esc(ucfirst($evt->status)) ?>
            </span>
            <button class="btn btn-primary btn-event-compact" onclick="viewEventDetails(<?= (int)$evt->id ?>)" title="View Details">
                <i class="bx bx-show"></i> View
            </button>
            <?php if (!$isPastEvent && $evt->status !== 'cancelled'): ?>
                <button class="btn btn-secondary btn-event-compact" onclick="openEditModal(<?= (int)$evt->id ?>)" title="Edit Event">
                    <i class="bx bx-edit"></i> Edit
                </button>
                <?php if ($evt->status === 'scheduled'): ?>
                    <form method="POST" action="<?= ROOT ?>/director/update_schedule_status?drama_id=<?= $dramaId ?>" class="inline-form">
                        <input type="hidden" name="event_id" value="<?= (int)$evt->id ?>">
                        <input type="hidden" name="status" value="confirmed">
                        <button type="submit" class="btn btn-success btn-event-compact" title="Confirm Event">
                            <i class="bx bx-check"></i> Confirm
                        </button>
                    </form>
                <?php endif; ?>
                <?php if ($evt->status !== 'completed'): ?>
                    <form method="POST" action="<?= ROOT ?>/director/update_schedule_status?drama_id=<?= $dramaId ?>" class="inline-form" onsubmit="return confirm('Cancel this event?');">
                        <input type="hidden" name="event_id" value="<?= (int)$evt->id ?>">
                        <input type="hidden" name="status" value="cancelled">
                        <button type="submit" class="btn btn-danger btn-event-compact" title="Cancel Event">
                            <i class="bx bx-x"></i> Cancel
                        </button>
                    </form>
                <?php endif; ?>
                <form method="POST" action="<?= ROOT ?>/director/delete_schedule?drama_id=<?= $dramaId ?>" class="inline-form" onsubmit="return confirm('Permanently delete this event?');">
                    <input type="hidden" name="event_id" value="<?= (int)$evt->id ?>">
                    <button type="submit" class="btn btn-danger btn-delete-event btn-event-compact" title="Delete Event">
                        <i class="bx bx-trash"></i> Delete
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>
