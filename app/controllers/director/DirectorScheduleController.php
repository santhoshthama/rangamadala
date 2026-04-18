<?php

require_once __DIR__ . '/DirectorFeatureControllerTrait.php';

class DirectorScheduleController
{
    use Controller, DirectorFeatureControllerTrait;

    protected $dramaModel;
    protected $roleModel;
    protected $scheduleModel;
    protected $notificationModel;
    protected $profileModel;

    public function __construct()
    {
        $this->dramaModel = $this->getModel('M_drama');
        $this->roleModel = $this->getModel('M_role');
        $this->scheduleModel = $this->getModel('M_schedule');
        $this->notificationModel = $this->getModel('M_notification');
        $this->profileModel = $this->getModel('M_universal_profile');
    }

    public function schedule_management()
    {
        $this->renderDramaView('schedule_management', [], function ($drama) {
            $dramaId = (int)$drama->id;

            $upcomingRaw = $this->scheduleModel ? $this->scheduleModel->getUpcomingEvents($dramaId) : [];
            $pastRaw = $this->scheduleModel ? $this->scheduleModel->getPastEvents($dramaId) : [];
            $allRaw = $this->scheduleModel ? $this->scheduleModel->getEventsByDrama($dramaId) : [];
            $stats = $this->scheduleModel ? $this->scheduleModel->getScheduleStats($dramaId) : null;

            $interviewEvents = $this->scheduleModel ? $this->scheduleModel->getInterviewsFromApplications($dramaId) : [];
            $roles = $this->roleModel ? $this->roleModel->getRolesByDrama($dramaId) : [];

            $upcomingEvents = $this->decorateScheduleEvents($upcomingRaw);
            $pastEvents = $this->decorateScheduleEvents($pastRaw);
            $allEvents = $this->decorateScheduleEvents($allRaw);
            [$thisWeek, $nextWeek, $laterEvents] = $this->groupUpcomingEventsByWindow($upcomingEvents);
            $calendarEvents = $this->buildScheduleCalendarEvents($allEvents, $interviewEvents);

            return [
                'upcomingEvents' => $upcomingEvents,
                'pastEvents' => $pastEvents,
                'allEvents' => $allEvents,
                'thisWeek' => $thisWeek,
                'nextWeek' => $nextWeek,
                'laterEvents' => $laterEvents,
                'calendarEvents' => $calendarEvents,
                'interviewEvents' => $interviewEvents,
                'scheduleStats' => $stats,
                'roles' => $roles,
            ];
        });
    }

    public function create_schedule()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $dramaId = $this->getQueryParam('drama_id');
            if ($dramaId) {
                header('Location: ' . ROOT . '/director/schedule_management?drama_id=' . $dramaId);
                exit;
            }
            header('Location: ' . ROOT . '/director/dashboard');
            exit;
        }

        $drama = $this->authorizeDrama();

        if (!$this->scheduleModel) {
            $_SESSION['message'] = 'Schedule management is currently unavailable.';
            $_SESSION['message_type'] = 'error';
            header('Location: ' . ROOT . '/director/schedule_management?drama_id=' . $drama->id);
            exit;
        }

        $eventType = trim($_POST['event_type'] ?? '');
        $eventTitle = trim($_POST['event_title'] ?? '');
        $eventDescription = trim($_POST['event_description'] ?? '');
        $scheduledDate = trim($_POST['scheduled_date'] ?? '');
        $startTime = trim($_POST['start_time'] ?? '');
        $endTime = trim($_POST['end_time'] ?? '');
        $venue = trim($_POST['venue'] ?? '');
        $roleId = !empty($_POST['role_id']) ? (int)$_POST['role_id'] : null;
        $notes = trim($_POST['notes'] ?? '');

        $errors = [];
        if (!in_array($eventType, ['rehearsal', 'interview', 'meeting', 'performance'])) {
            $errors[] = 'Invalid event type.';
        }
        if (empty($eventTitle)) {
            $errors[] = 'Event title is required.';
        }
        if (empty($scheduledDate)) {
            $errors[] = 'Date is required.';
        }
        if (empty($startTime) || empty($endTime)) {
            $errors[] = 'Start and end times are required.';
        }
        if ($startTime >= $endTime) {
            $errors[] = 'End time must be after start time.';
        }
        if (empty($venue)) {
            $errors[] = 'Venue is required.';
        }

        if (!empty($errors)) {
            $_SESSION['message'] = implode(' ', $errors);
            $_SESSION['message_type'] = 'error';
            header('Location: ' . ROOT . '/director/schedule_management?drama_id=' . $drama->id);
            exit;
        }

        $isAvailable = $this->scheduleModel->isTimeSlotAvailable((int)$drama->id, $scheduledDate, $startTime, $endTime);
        if (!$isAvailable) {
            $_SESSION['message'] = 'This time slot conflicts with an existing schedule on ' . $scheduledDate . '. Please choose a different time.';
            $_SESSION['message_type'] = 'error';
            header('Location: ' . ROOT . '/director/schedule_management?drama_id=' . $drama->id);
            exit;
        }

        $data = [
            'drama_id' => (int)$drama->id,
            'event_type' => $eventType,
            'event_title' => $eventTitle,
            'event_description' => $eventDescription ?: null,
            'scheduled_date' => $scheduledDate,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'venue' => $venue,
            'role_id' => $roleId,
            'notes' => $notes ?: null,
            'created_by' => (int)$_SESSION['user_id'],
        ];

        $eventId = $this->scheduleModel->createEvent($data);

        if ($eventId) {
            $_SESSION['message'] = 'Schedule event created successfully.';
            $_SESSION['message_type'] = 'success';

            if ($this->notificationModel) {
                $eventLink = ROOT . '/artistdashboard/event_detail?event_id=' . $eventId . '&drama_id=' . $drama->id;
                $this->notificationModel->notifyDramaArtists(
                    (int)$drama->id,
                    'event_scheduled',
                    'New ' . ucfirst($eventType) . ' Scheduled: ' . $eventTitle,
                    ucfirst($eventType) . ' "' . $eventTitle . '" has been scheduled for ' . date('M d, Y', strtotime($scheduledDate)) . ' at ' . $venue . ' (' . date('h:i A', strtotime($startTime)) . ' - ' . date('h:i A', strtotime($endTime)) . ').',
                    $eventLink,
                    (int)$_SESSION['user_id']
                );
            }
        } else {
            $_SESSION['message'] = 'Failed to create schedule event.';
            $_SESSION['message_type'] = 'error';
        }

        header('Location: ' . ROOT . '/director/schedule_management?drama_id=' . $drama->id);
        exit;
    }

    public function update_schedule()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $dramaId = $this->getQueryParam('drama_id');
            if ($dramaId) {
                header('Location: ' . ROOT . '/director/schedule_management?drama_id=' . $dramaId);
                exit;
            }
            header('Location: ' . ROOT . '/director/dashboard');
            exit;
        }

        $drama = $this->authorizeDrama();

        if (!$this->scheduleModel) {
            $_SESSION['message'] = 'Schedule management is currently unavailable.';
            $_SESSION['message_type'] = 'error';
            header('Location: ' . ROOT . '/director/schedule_management?drama_id=' . $drama->id);
            exit;
        }

        $eventId = (int)($_POST['event_id'] ?? 0);
        if (!$eventId) {
            $_SESSION['message'] = 'Invalid event.';
            $_SESSION['message_type'] = 'error';
            header('Location: ' . ROOT . '/director/schedule_management?drama_id=' . $drama->id);
            exit;
        }

        $event = $this->scheduleModel->getEventById($eventId);
        if (!$event || (int)$event->drama_id !== (int)$drama->id) {
            $_SESSION['message'] = 'Event not found or inaccessible.';
            $_SESSION['message_type'] = 'error';
            header('Location: ' . ROOT . '/director/schedule_management?drama_id=' . $drama->id);
            exit;
        }

        $eventType = trim($_POST['event_type'] ?? '');
        $eventTitle = trim($_POST['event_title'] ?? '');
        $eventDescription = trim($_POST['event_description'] ?? '');
        $scheduledDate = trim($_POST['scheduled_date'] ?? '');
        $startTime = trim($_POST['start_time'] ?? '');
        $endTime = trim($_POST['end_time'] ?? '');
        $venue = trim($_POST['venue'] ?? '');
        $roleId = !empty($_POST['role_id']) ? (int)$_POST['role_id'] : null;
        $notes = trim($_POST['notes'] ?? '');

        $errors = [];
        if (!in_array($eventType, ['rehearsal', 'interview', 'meeting', 'performance'])) {
            $errors[] = 'Invalid event type.';
        }
        if (empty($eventTitle)) {
            $errors[] = 'Event title is required.';
        }
        if (empty($scheduledDate)) {
            $errors[] = 'Date is required.';
        }
        if (empty($startTime) || empty($endTime)) {
            $errors[] = 'Start and end times are required.';
        }
        if ($startTime >= $endTime) {
            $errors[] = 'End time must be after start time.';
        }
        if (empty($venue)) {
            $errors[] = 'Venue is required.';
        }

        if (!empty($errors)) {
            $_SESSION['message'] = implode(' ', $errors);
            $_SESSION['message_type'] = 'error';
            header('Location: ' . ROOT . '/director/schedule_management?drama_id=' . $drama->id);
            exit;
        }

        $isAvailable = $this->scheduleModel->isTimeSlotAvailable((int)$drama->id, $scheduledDate, $startTime, $endTime, $eventId);
        if (!$isAvailable) {
            $_SESSION['message'] = 'This time slot conflicts with an existing schedule on ' . $scheduledDate . '. Please choose a different time.';
            $_SESSION['message_type'] = 'error';
            header('Location: ' . ROOT . '/director/schedule_management?drama_id=' . $drama->id);
            exit;
        }

        $data = [
            'event_type' => $eventType,
            'event_title' => $eventTitle,
            'event_description' => $eventDescription ?: null,
            'scheduled_date' => $scheduledDate,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'venue' => $venue,
            'role_id' => $roleId,
            'notes' => $notes ?: null,
        ];

        $updated = $this->scheduleModel->updateEvent($eventId, $data);

        if ($updated) {
            $_SESSION['message'] = 'Schedule event updated successfully.';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Failed to update schedule event.';
            $_SESSION['message_type'] = 'error';
        }

        header('Location: ' . ROOT . '/director/schedule_management?drama_id=' . $drama->id);
        exit;
    }

    public function delete_schedule()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $dramaId = $this->getQueryParam('drama_id');
            if ($dramaId) {
                header('Location: ' . ROOT . '/director/schedule_management?drama_id=' . $dramaId);
                exit;
            }
            header('Location: ' . ROOT . '/director/dashboard');
            exit;
        }

        $drama = $this->authorizeDrama();

        if (!$this->scheduleModel) {
            $_SESSION['message'] = 'Schedule management is currently unavailable.';
            $_SESSION['message_type'] = 'error';
            header('Location: ' . ROOT . '/director/schedule_management?drama_id=' . $drama->id);
            exit;
        }

        $eventId = (int)($_POST['event_id'] ?? 0);

        $event = $this->scheduleModel->getEventById($eventId);
        if (!$event || (int)$event->drama_id !== (int)$drama->id) {
            $_SESSION['message'] = 'Event not found or inaccessible.';
            $_SESSION['message_type'] = 'error';
            header('Location: ' . ROOT . '/director/schedule_management?drama_id=' . $drama->id);
            exit;
        }

        $deleted = $this->scheduleModel->deleteEvent($eventId);

        if ($deleted) {
            $_SESSION['message'] = 'Schedule event deleted successfully.';
            $_SESSION['message_type'] = 'success';

            if ($this->notificationModel) {
                $dramaLink = ROOT . '/artistdashboard/view_drama?drama_id=' . $drama->id;
                $this->notificationModel->notifyDramaArtists(
                    (int)$drama->id,
                    'event_cancelled',
                    'Event Cancelled: ' . ($event->event_title ?? 'Event'),
                    'The ' . ($event->event_type ?? 'event') . ' "' . ($event->event_title ?? 'Event') . '" on ' . date('M d, Y', strtotime($event->scheduled_date)) . ' has been removed from the schedule.',
                    $dramaLink,
                    (int)$_SESSION['user_id']
                );
            }
        } else {
            $_SESSION['message'] = 'Failed to delete schedule event.';
            $_SESSION['message_type'] = 'error';
        }

        header('Location: ' . ROOT . '/director/schedule_management?drama_id=' . $drama->id);
        exit;
    }

    public function update_schedule_status()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $dramaId = $this->getQueryParam('drama_id');
            if ($dramaId) {
                header('Location: ' . ROOT . '/director/schedule_management?drama_id=' . $dramaId);
                exit;
            }
            header('Location: ' . ROOT . '/director/dashboard');
            exit;
        }

        $drama = $this->authorizeDrama();

        if (!$this->scheduleModel) {
            $_SESSION['message'] = 'Schedule management is currently unavailable.';
            $_SESSION['message_type'] = 'error';
            header('Location: ' . ROOT . '/director/schedule_management?drama_id=' . $drama->id);
            exit;
        }

        $eventId = (int)($_POST['event_id'] ?? 0);
        $status = trim($_POST['status'] ?? '');

        $event = $this->scheduleModel->getEventById($eventId);
        if (!$event || (int)$event->drama_id !== (int)$drama->id) {
            $_SESSION['message'] = 'Event not found or inaccessible.';
            $_SESSION['message_type'] = 'error';
            header('Location: ' . ROOT . '/director/schedule_management?drama_id=' . $drama->id);
            exit;
        }

        $updated = $this->scheduleModel->updateEventStatus($eventId, $status);

        if ($updated) {
            $_SESSION['message'] = 'Event status updated to ' . ucfirst($status) . '.';
            $_SESSION['message_type'] = 'success';

            if ($this->notificationModel && in_array($status, ['cancelled', 'confirmed'])) {
                $notifType = $status === 'cancelled' ? 'event_cancelled' : 'event_updated';
                $eventLink = ROOT . '/artistdashboard/event_detail?event_id=' . $eventId . '&drama_id=' . $drama->id;
                $this->notificationModel->notifyDramaArtists(
                    (int)$drama->id,
                    $notifType,
                    'Event ' . ucfirst($status) . ': ' . ($event->event_title ?? 'Event'),
                    'The ' . ($event->event_type ?? 'event') . ' "' . ($event->event_title ?? 'Event') . '" on ' . date('M d, Y', strtotime($event->scheduled_date)) . ' has been ' . $status . '.',
                    $eventLink,
                    (int)$_SESSION['user_id']
                );
            }
        } else {
            $_SESSION['message'] = 'Failed to update event status.';
            $_SESSION['message_type'] = 'error';
        }

        header('Location: ' . ROOT . '/director/schedule_management?drama_id=' . $drama->id);
        exit;
    }

    public function check_date_availability()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['available' => false, 'message' => 'Not authenticated.']);
            exit;
        }

        $dramaId = (int)($this->getQueryParam('drama_id') ?? 0);
        $date = $this->getQueryParam('date') ?? '';
        $startTime = $this->getQueryParam('start_time') ?? '';
        $endTime = $this->getQueryParam('end_time') ?? '';
        $excludeId = (int)($this->getQueryParam('exclude_id') ?? 0);

        if (!$dramaId || !$date) {
            echo json_encode(['available' => false, 'message' => 'Missing parameters.']);
            exit;
        }

        if (!$this->scheduleModel) {
            echo json_encode(['available' => false, 'message' => 'Schedule service unavailable.']);
            exit;
        }

        $eventsOnDate = $this->scheduleModel->getEventsByDate($dramaId, $date);

        $timeAvailable = true;
        if ($startTime && $endTime) {
            $timeAvailable = $this->scheduleModel->isTimeSlotAvailable($dramaId, $date, $startTime, $endTime, $excludeId ?: null);
        }

        $eventList = [];
        foreach ($eventsOnDate as $evt) {
            $eventList[] = [
                'id' => $evt->id,
                'title' => $evt->event_title,
                'type' => $evt->event_type,
                'start_time' => $evt->start_time,
                'end_time' => $evt->end_time,
                'venue' => $evt->venue,
                'status' => $evt->status,
            ];
        }

        echo json_encode([
            'available' => $timeAvailable,
            'date' => $date,
            'events_count' => count($eventsOnDate),
            'events' => $eventList,
            'message' => $timeAvailable
                ? (count($eventsOnDate) > 0
                    ? 'Date has ' . count($eventsOnDate) . ' event(s) but the selected time slot is available.'
                    : 'Date is completely free. You can schedule your event.')
                : 'Time slot conflicts with an existing event. Please choose a different time.',
        ]);
        exit;
    }

    protected function decorateScheduleEvents(array $events): array
    {
        $decorated = [];

        foreach ($events as $event) {
            if (!is_object($event)) {
                continue;
            }

            $clone = clone $event;
            $status = strtolower((string)($clone->status ?? 'pending'));
            $type = strtolower((string)($clone->event_type ?? ''));

            $clone->type_icon = $this->mapScheduleEventTypeIcon($type);
            $clone->badge_class = $this->mapScheduleStatusBadgeClass($status);
            $clone->badge_variant_class = $this->mapScheduleStatusBadgeVariantClass($status);

            $decorated[] = $clone;
        }

        return $decorated;
    }

    protected function mapScheduleEventTypeIcon(string $type): string
    {
        switch ($type) {
            case 'rehearsal':
                return 'bx-theater-masks';
            case 'interview':
                return 'bx-user-check';
            case 'meeting':
                return 'bx-users';
            case 'performance':
                return 'bx-star';
            default:
                return 'bx-calendar';
        }
    }

    protected function mapScheduleStatusBadgeClass(string $status): string
    {
        switch ($status) {
            case 'confirmed':
                return 'assigned';
            case 'scheduled':
                return 'pending';
            case 'completed':
                return '';
            case 'cancelled':
                return 'unassigned';
            default:
                return 'pending';
        }
    }

    protected function mapScheduleStatusBadgeVariantClass(string $status): string
    {
        if ($status === 'completed') {
            return 'status-badge-completed';
        }

        if ($status === 'cancelled') {
            return 'status-badge-cancelled';
        }

        return 'status-badge-default';
    }

    protected function groupUpcomingEventsByWindow(array $upcomingEvents): array
    {
        $thisWeek = [];
        $nextWeek = [];
        $laterEvents = [];

        $now = new DateTime();
        $endOfThisWeek = (clone $now)->modify('sunday this week');
        $endOfNextWeek = (clone $endOfThisWeek)->modify('+7 days');

        foreach ($upcomingEvents as $event) {
            if (!is_object($event) || empty($event->scheduled_date)) {
                $laterEvents[] = $event;
                continue;
            }

            try {
                $eventDate = new DateTime((string)$event->scheduled_date);
            } catch (Exception $e) {
                $laterEvents[] = $event;
                continue;
            }

            if ($eventDate <= $endOfThisWeek) {
                $thisWeek[] = $event;
            } elseif ($eventDate <= $endOfNextWeek) {
                $nextWeek[] = $event;
            } else {
                $laterEvents[] = $event;
            }
        }

        return [$thisWeek, $nextWeek, $laterEvents];
    }

    protected function buildScheduleCalendarEvents(array $allEvents, array $interviewEvents): array
    {
        $calendarEvents = [];

        foreach ($allEvents as $event) {
            if (!is_object($event)) {
                continue;
            }

            $calendarEvents[] = [
                'id' => $event->id,
                'date' => $event->scheduled_date,
                'title' => $event->event_title,
                'type' => $event->event_type,
                'start_time' => isset($event->start_time) ? substr((string)$event->start_time, 0, 5) : '',
                'end_time' => isset($event->end_time) ? substr((string)$event->end_time, 0, 5) : '',
                'venue' => $event->venue,
                'status' => $event->status,
                'description' => $event->event_description ?? '',
                'notes' => $event->notes ?? '',
                'role_id' => $event->role_id ?? null,
                'role_name' => $event->role_name ?? '',
                'editable' => true,
            ];
        }

        foreach ($interviewEvents as $interview) {
            if (!is_object($interview) || empty($interview->interview_at)) {
                continue;
            }

            $calendarEvents[] = [
                'id' => 'interview_' . ($interview->application_id ?? ''),
                'date' => date('Y-m-d', strtotime((string)$interview->interview_at)),
                'title' => 'Interview - ' . ($interview->role_name ?? 'Role') . ' (' . ($interview->artist_name ?? 'Artist') . ')',
                'type' => 'interview',
                'start_time' => date('H:i', strtotime((string)$interview->interview_at)),
                'end_time' => '',
                'venue' => '',
                'status' => $interview->interview_status ?? 'pending',
                'description' => '',
                'notes' => '',
                'role_id' => null,
                'role_name' => $interview->role_name ?? '',
                'editable' => false,
            ];
        }

        return $calendarEvents;
    }

}
