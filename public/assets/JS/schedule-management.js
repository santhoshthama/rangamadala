/**
 * Schedule Management - Full CRUD + Calendar
 * Uses globals: ROOT, DRAMA_ID, CALENDAR_EVENTS (injected by PHP view)
 */

// ═══════════════════════════════════════════════
// Calendar state
// ═══════════════════════════════════════════════
let currentMonth = new Date().getMonth();
let currentYear = new Date().getFullYear();
let availabilityTimeout = null;

function setAvailabilityState(type, message) {
    var avail = document.getElementById('dateAvailability');
    if (!avail) return;

    avail.className = 'date-availability ' + type;
    avail.innerHTML = message;
    avail.style.display = 'block';
}

function hasInvalidTimeRange(startTime, endTime) {
    return !!(startTime && endTime && startTime >= endTime);
}

// ═══════════════════════════════════════════════
// Tab switching
// ═══════════════════════════════════════════════
function showScheduleTab(tabName, btn) {
    // Hide all tab content
    document.querySelectorAll('.tab-content').forEach(function(t) {
        t.classList.remove('active');
    });
    // Deactivate all tab buttons
    document.querySelectorAll('.tab-button').forEach(function(b) {
        b.classList.remove('active');
    });

    // Show selected tab
    var tab = document.getElementById(tabName + 'Tab');
    if (tab) tab.classList.add('active');

    // Activate clicked button
    if (btn) btn.classList.add('active');

    // Render calendar when switching to calendar tab
    if (tabName === 'calendar') {
        generateCalendar(currentMonth, currentYear);
    }
}

// ═══════════════════════════════════════════════
// Create Modal
// ═══════════════════════════════════════════════
function openCreateModal(eventType) {
    var form = document.getElementById('scheduleForm');
    form.reset();
    document.getElementById('formEventId').value = '';
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-calendar-plus"></i> Schedule Event';
    document.getElementById('formSubmitBtn').innerHTML = '<i class="fas fa-check"></i> Create Schedule';
    form.action = ROOT + '/director/create_schedule?drama_id=' + DRAMA_ID;

    // Pre-select event type if provided
    if (eventType) {
        document.getElementById('formEventType').value = eventType;
        toggleRoleSelect(eventType);
    } else {
        toggleRoleSelect('');
    }

    // Clear availability indicator
    var avail = document.getElementById('dateAvailability');
    if (avail) { avail.style.display = 'none'; avail.className = 'date-availability'; avail.textContent = ''; }

    document.getElementById('scheduleModal').style.display = 'block';
}

// ═══════════════════════════════════════════════
// Edit Modal — populate from CALENDAR_EVENTS
// ═══════════════════════════════════════════════
function openEditModal(eventId) {
    var evt = findEvent(eventId);
    if (!evt) { alert('Event not found.'); return; }
    if (evt.editable === false) { alert('This event cannot be edited from here.'); return; }

    var form = document.getElementById('scheduleForm');
    form.reset();

    document.getElementById('formEventId').value = evt.id;
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit"></i> Edit Schedule Event';
    document.getElementById('formSubmitBtn').innerHTML = '<i class="fas fa-save"></i> Update Schedule';
    form.action = ROOT + '/director/update_schedule?drama_id=' + DRAMA_ID;

    // Populate fields
    document.getElementById('formEventType').value = evt.type || '';
    document.getElementById('formEventTitle').value = evt.title || '';
    document.getElementById('formScheduledDate').value = evt.date || '';
    document.getElementById('formStartTime').value = evt.start_time || '';
    document.getElementById('formEndTime').value = evt.end_time || '';
    document.getElementById('formVenue').value = evt.venue || '';
    document.getElementById('formDescription').value = evt.description || '';
    document.getElementById('formNotes').value = evt.notes || '';

    // Role select
    toggleRoleSelect(evt.type);
    if (evt.role_id) {
        document.getElementById('formRoleId').value = evt.role_id;
    }

    // Clear availability indicator
    var avail = document.getElementById('dateAvailability');
    if (avail) { avail.style.display = 'none'; }

    document.getElementById('scheduleModal').style.display = 'block';

    // Trigger availability check after a brief delay
    if (evt.date && evt.start_time && evt.end_time) {
        setTimeout(function() { checkDateAvailability(); }, 300);
    }
}

// ═══════════════════════════════════════════════
// Close Modals
// ═══════════════════════════════════════════════
function closeModal() {
    document.getElementById('scheduleModal').style.display = 'none';
}

function closeDetailsModal() {
    document.getElementById('detailsModal').style.display = 'none';
}

// ═══════════════════════════════════════════════
// View Event Details
// ═══════════════════════════════════════════════
function viewEventDetails(eventId) {
    var evt = findEvent(eventId);
    if (!evt) { alert('Event details not available.'); return; }

    var typeLabels = { rehearsal: 'Rehearsal', interview: 'Interview', meeting: 'Production Meeting' };
    var typeColors = { rehearsal: '#007bff', interview: '#28a745', meeting: '#ffc107' };
    var statusColors = { scheduled: '#ffc107', confirmed: '#28a745', completed: '#6c757d', cancelled: '#dc3545', pending: '#ffc107' };

    var html = '';
    html += '<div style="margin-bottom: 16px;">';
    html += '<span style="background: ' + (typeColors[evt.type] || '#888') + '; color: #fff; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">' + (typeLabels[evt.type] || evt.type) + '</span> ';
    html += '<span style="background: ' + (statusColors[evt.status] || '#888') + '; color: #fff; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">' + capitalize(evt.status) + '</span>';
    html += '</div>';

    html += '<h3 style="margin: 0 0 12px;">' + escapeHtml(evt.title) + '</h3>';

    html += '<table style="width: 100%; font-size: 14px; border-collapse: collapse;">';
    html += detailRow('Date', formatDate(evt.date));
    if (evt.start_time) html += detailRow('Time', evt.start_time + (evt.end_time ? ' — ' + evt.end_time : ''));
    if (evt.venue) html += detailRow('Venue', escapeHtml(evt.venue));
    if (evt.role_name) html += detailRow('Role', escapeHtml(evt.role_name));
    if (evt.description) html += detailRow('Description', escapeHtml(evt.description));
    if (evt.notes) html += detailRow('Notes', escapeHtml(evt.notes));
    html += '</table>';

    document.getElementById('detailsBody').innerHTML = html;
    document.getElementById('detailsModal').style.display = 'block';
}

function detailRow(label, value) {
    return '<tr><td style="padding: 8px 4px; font-weight: 600; color: var(--muted); width: 120px; vertical-align: top;">' + label + '</td><td style="padding: 8px 4px;">' + value + '</td></tr>';
}

// ═══════════════════════════════════════════════
// Calendar Rendering
// ═══════════════════════════════════════════════
function generateCalendar(month, year) {
    var monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    var header = document.getElementById('calendarMonthYear');
    if (header) header.textContent = monthNames[month] + ' ' + year;

    var container = document.getElementById('calendarDays');
    if (!container) return;
    container.innerHTML = '';

    var firstDay = new Date(year, month, 1).getDay();
    var daysInMonth = new Date(year, month + 1, 0).getDate();
    var today = new Date();

    var typeColors = { rehearsal: '#007bff', interview: '#28a745', meeting: '#ffc107' };

    // Empty cells for days before the 1st
    for (var i = 0; i < firstDay; i++) {
        var empty = document.createElement('div');
        empty.style.cssText = 'background: #f8f9fa; padding: 10px; min-height: 90px;';
        container.appendChild(empty);
    }

    // Day cells
    for (var day = 1; day <= daysInMonth; day++) {
        var dateStr = year + '-' + String(month + 1).padStart(2, '0') + '-' + String(day).padStart(2, '0');
        var isToday = (today.getDate() === day && today.getMonth() === month && today.getFullYear() === year);

        var dayCell = document.createElement('div');
        dayCell.style.cssText = 'background: ' + (isToday ? '#fff3cd' : 'white') + '; padding: 8px; min-height: 90px; cursor: pointer; border: ' + (isToday ? '2px solid var(--brand)' : 'none') + '; position: relative;';
        dayCell.setAttribute('data-date', dateStr);

        // Day number
        var dayNum = document.createElement('div');
        dayNum.style.cssText = 'font-weight: ' + (isToday ? 'bold' : 'normal') + '; color: ' + (isToday ? 'var(--brand)' : 'var(--ink)') + '; margin-bottom: 4px; font-size: 14px;';
        dayNum.textContent = day;
        dayCell.appendChild(dayNum);

        // Events for this day (non-cancelled)
        var dayEvents = CALENDAR_EVENTS.filter(function(e) { return e.date === dateStr && e.status !== 'cancelled'; });
        dayEvents.forEach(function(evt) {
            var chip = document.createElement('div');
            chip.style.cssText = 'background: ' + (typeColors[evt.type] || '#888') + '; color: white; font-size: 10px; padding: 2px 5px; margin: 1px 0; border-radius: 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; cursor: pointer;';
            chip.title = evt.title + (evt.start_time ? ' (' + evt.start_time + ')' : '');
            chip.textContent = (evt.start_time ? evt.start_time + ' ' : '') + evt.title;
            chip.onclick = function(e) {
                e.stopPropagation();
                viewEventDetails(evt.id);
            };
            dayCell.appendChild(chip);
        });

        // Click on empty area to add event on that date
        (function(ds) {
            dayCell.addEventListener('click', function(e) {
                if (e.target === dayCell || e.target === dayNum) {
                    addEventToDate(ds);
                }
            });
        })(dateStr);

        container.appendChild(dayCell);
    }
}

function previousMonth() {
    currentMonth--;
    if (currentMonth < 0) { currentMonth = 11; currentYear--; }
    generateCalendar(currentMonth, currentYear);
}

function nextMonth() {
    currentMonth++;
    if (currentMonth > 11) { currentMonth = 0; currentYear++; }
    generateCalendar(currentMonth, currentYear);
}

// Click on a calendar date → open create modal with date pre-filled
function addEventToDate(dateString) {
    openCreateModal();
    document.getElementById('formScheduledDate').value = dateString;
    // Trigger availability check
    setTimeout(function() { checkDateAvailability(); }, 100);
}

// ═══════════════════════════════════════════════
// Date & Time Availability Check (AJAX)
// ═══════════════════════════════════════════════
function checkDateAvailability() {
    var date = document.getElementById('formScheduledDate').value;
    var startTime = document.getElementById('formStartTime').value;
    var endTime = document.getElementById('formEndTime').value;
    var eventType = document.getElementById('formEventType').value;
    var roleId = document.getElementById('formRoleId').value;
    var avail = document.getElementById('dateAvailability');
    var excludeId = document.getElementById('formEventId').value || '';

    if (!date) { avail.style.display = 'none'; return; }

    if (hasInvalidTimeRange(startTime, endTime)) {
        setAvailabilityState('conflict', '<i class="fas fa-exclamation-triangle"></i> End time must be after start time.');
        return;
    }

    // Show checking state
    avail.className = 'date-availability checking';
    avail.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Checking availability...';
    avail.style.display = 'block';

    // Build URL
    var url = ROOT + '/director/check_date_availability?drama_id=' + DRAMA_ID + '&date=' + encodeURIComponent(date);
    if (startTime) url += '&start_time=' + encodeURIComponent(startTime);
    if (endTime) url += '&end_time=' + encodeURIComponent(endTime);
    if (eventType) url += '&event_type=' + encodeURIComponent(eventType);
    if (roleId) url += '&role_id=' + encodeURIComponent(roleId);
    if (excludeId) url += '&exclude_id=' + encodeURIComponent(excludeId);

    // Debounce
    if (availabilityTimeout) clearTimeout(availabilityTimeout);
    availabilityTimeout = setTimeout(function() {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', url, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onreadystatechange = function() {
            if (xhr.readyState !== 4) return;
            if (xhr.status === 200) {
                try {
                    var data = JSON.parse(xhr.responseText);
                    if (data.available) {
                        avail.className = 'date-availability available';
                        avail.innerHTML = '<i class="fas fa-check-circle"></i> ' + escapeHtml(data.message);
                    } else {
                        avail.className = 'date-availability conflict';
                        avail.innerHTML = '<i class="fas fa-exclamation-triangle"></i> ' + escapeHtml(data.message);
                        // Show conflicting events if any
                        if (data.events && data.events.length > 0) {
                            avail.innerHTML += '<ul style="margin: 6px 0 0 16px; padding: 0;">';
                            data.events.forEach(function(ev) {
                                avail.innerHTML += '<li style="font-size: 12px;">' + escapeHtml(ev.title) + ' (' + ev.start_time.substring(0, 5) + ' - ' + ev.end_time.substring(0, 5) + ')</li>';
                            });
                            avail.innerHTML += '</ul>';
                        }

                        if (data.artist_conflicts && data.artist_conflicts.length > 0) {
                            avail.innerHTML += '<ul style="margin: 6px 0 0 16px; padding: 0;">';
                            data.artist_conflicts.slice(0, 5).forEach(function(conflict) {
                                avail.innerHTML += '<li style="font-size: 12px;">' +
                                    escapeHtml(conflict.artist_name || 'Artist') +
                                    ' already has ' +
                                    escapeHtml(conflict.title || 'an event') +
                                    ' in ' +
                                    escapeHtml(conflict.drama_name || 'another drama') +
                                    ' (' +
                                    escapeHtml((conflict.start_time || '--:--') + ' - ' + (conflict.end_time || '--:--')) +
                                    ')</li>';
                            });
                            avail.innerHTML += '</ul>';
                        }
                    }
                } catch (e) {
                    avail.className = 'date-availability conflict';
                    avail.innerHTML = '<i class="fas fa-times-circle"></i> Could not check availability.';
                }
            } else {
                avail.className = 'date-availability conflict';
                avail.innerHTML = '<i class="fas fa-times-circle"></i> Error checking availability.';
            }
        };
        xhr.send();
    }, 300);
}

// ═══════════════════════════════════════════════
// Role dropdown toggle for interview type
// ═══════════════════════════════════════════════
function toggleRoleSelect(eventType) {
    var group = document.getElementById('roleSelectGroup');
    if (group) {
        group.style.display = (eventType === 'interview') ? 'block' : 'none';
    }
}

// ═══════════════════════════════════════════════
// Helpers
// ═══════════════════════════════════════════════
function findEvent(eventId) {
    for (var i = 0; i < CALENDAR_EVENTS.length; i++) {
        // Support both numeric and string IDs (e.g. "interview_5")
        if (CALENDAR_EVENTS[i].id == eventId) return CALENDAR_EVENTS[i];
    }
    return null;
}

function escapeHtml(str) {
    if (!str) return '';
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
}

function capitalize(str) {
    if (!str) return '';
    return str.charAt(0).toUpperCase() + str.slice(1);
}

function formatDate(dateStr) {
    if (!dateStr) return '';
    var parts = dateStr.split('-');
    var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    return months[parseInt(parts[1], 10) - 1] + ' ' + parseInt(parts[2], 10) + ', ' + parts[0];
}

// ═══════════════════════════════════════════════
// Initialization
// ═══════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', function() {
    // Generate calendar if calendar tab exists
    generateCalendar(currentMonth, currentYear);

    // Event type change → toggle role dropdown
    var eventTypeSelect = document.getElementById('formEventType');
    if (eventTypeSelect) {
        eventTypeSelect.addEventListener('change', function() {
            toggleRoleSelect(this.value);
        });
    }

    // Date/time change → check availability
    var dateInput = document.getElementById('formScheduledDate');
    var startInput = document.getElementById('formStartTime');
    var endInput = document.getElementById('formEndTime');
    var form = document.getElementById('scheduleForm');

    if (dateInput) dateInput.addEventListener('change', checkDateAvailability);
    if (startInput) startInput.addEventListener('change', checkDateAvailability);
    if (endInput) endInput.addEventListener('change', checkDateAvailability);

    if (form) {
        form.addEventListener('submit', function(e) {
            var startTime = document.getElementById('formStartTime').value;
            var endTime = document.getElementById('formEndTime').value;
            var avail = document.getElementById('dateAvailability');

            if (hasInvalidTimeRange(startTime, endTime)) {
                e.preventDefault();
                setAvailabilityState('conflict', '<i class="fas fa-exclamation-triangle"></i> End time must be after start time.');
                alert('End time must be after start time.');
                return;
            }

            if (avail && avail.classList.contains('conflict')) {
                e.preventDefault();
                alert('Please resolve the date/time conflict before submitting.');
            }
        });
    }
});

// Close modals when clicking outside
window.addEventListener('click', function(e) {
    var scheduleModal = document.getElementById('scheduleModal');
    var detailsModal = document.getElementById('detailsModal');
    if (scheduleModal && e.target === scheduleModal) closeModal();
    if (detailsModal && e.target === detailsModal) closeDetailsModal();
});
