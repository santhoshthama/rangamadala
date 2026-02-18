<?php
/**
 * Schedule Event Card Partial
 * Variables expected: $evt, $dramaId, $isPast (optional)
 */
$isPastEvent = isset($isPast) && $isPast;
$evtDate = date('F d, Y', strtotime($evt->scheduled_date));
$startTime = date('g:i A', strtotime($evt->start_time));
$endTime = date('g:i A', strtotime($evt->end_time));
$typeIcon = eventTypeIcon($evt->event_type);
$badgeClass = statusBadgeClass($evt->status);
$badgeStyle = statusBadgeStyle($evt->status);
?>
<div class="event-card" data-event-id="<?= (int)$evt->id ?>">
    <div class="event-header">
        <div style="flex: 1;">
            <strong style="font-size: 15px;">
                <i class="fas <?= $typeIcon ?>"></i>
                <?= esc($evt->event_title) ?>
            </strong>
            <div class="event-meta">
                📅 <?= esc($evtDate) ?> | ⏰ <?= esc($startTime) ?> - <?= esc($endTime) ?> | 📍 <?= esc($evt->venue) ?>
            </div>
            <?php if (!empty($evt->role_name)): ?>
                <div class="event-meta" style="margin-top: 4px;">
                    <i class="fas fa-user-tag"></i> Role: <strong><?= esc($evt->role_name) ?></strong>
                </div>
            <?php endif; ?>
            <?php if (!empty($evt->event_description)): ?>
                <div class="event-meta" style="margin-top: 4px;">
                    <?= nl2br(esc($evt->event_description)) ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($evt->notes)): ?>
                <div class="event-meta" style="margin-top: 4px; font-style: italic;">
                    <i class="fas fa-sticky-note"></i> <?= esc($evt->notes) ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="event-actions">
            <span class="status-badge <?= $badgeClass ?>" style="<?= $badgeStyle ?>">
                <?= esc(ucfirst($evt->status)) ?>
            </span>
            <button class="btn btn-primary" style="font-size: 11px; padding: 6px 12px;" onclick="viewEventDetails(<?= (int)$evt->id ?>)" title="View Details">
                <i class="fas fa-eye"></i>
            </button>
            <?php if (!$isPastEvent && $evt->status !== 'cancelled'): ?>
                <button class="btn btn-secondary" style="font-size: 11px; padding: 6px 12px;" onclick="openEditModal(<?= (int)$evt->id ?>)" title="Edit">
                    <i class="fas fa-edit"></i>
                </button>
                <?php if ($evt->status === 'scheduled'): ?>
                    <form method="POST" action="<?= ROOT ?>/director/update_schedule_status?drama_id=<?= $dramaId ?>" style="display: inline;">
                        <input type="hidden" name="event_id" value="<?= (int)$evt->id ?>">
                        <input type="hidden" name="status" value="confirmed">
                        <button type="submit" class="btn btn-success" style="font-size: 11px; padding: 6px 12px;" title="Confirm">
                            <i class="fas fa-check"></i>
                        </button>
                    </form>
                <?php endif; ?>
                <?php if ($evt->status !== 'completed'): ?>
                    <form method="POST" action="<?= ROOT ?>/director/update_schedule_status?drama_id=<?= $dramaId ?>" style="display: inline;" onsubmit="return confirm('Cancel this event?');">
                        <input type="hidden" name="event_id" value="<?= (int)$evt->id ?>">
                        <input type="hidden" name="status" value="cancelled">
                        <button type="submit" class="btn btn-danger" style="font-size: 11px; padding: 6px 12px;" title="Cancel">
                            <i class="fas fa-times"></i>
                        </button>
                    </form>
                <?php endif; ?>
                <form method="POST" action="<?= ROOT ?>/director/delete_schedule?drama_id=<?= $dramaId ?>" style="display: inline;" onsubmit="return confirm('Permanently delete this event?');">
                    <input type="hidden" name="event_id" value="<?= (int)$evt->id ?>">
                    <button type="submit" class="btn btn-danger" style="font-size: 11px; padding: 6px 12px; background: #343a40;" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>
