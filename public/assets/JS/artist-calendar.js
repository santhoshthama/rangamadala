(function () {
    const container = document.getElementById('artistCalendarContainer');
    const titleEl = document.getElementById('artistCalendarTitle');
    const syncInfoEl = document.getElementById('artistCalendarSyncInfo');
    const filterForm = document.getElementById('artistCalendarFilters');
    const filterDrama = document.getElementById('filterDramaId');
    const filterParticipation = document.getElementById('filterParticipation');
    const filterStart = document.getElementById('filterStartDate');
    const filterEnd = document.getElementById('filterEndDate');
    const prevBtn = document.getElementById('calendarPrevBtn');
    const nextBtn = document.getElementById('calendarNextBtn');
    const todayBtn = document.getElementById('calendarTodayBtn');
    const viewButtons = Array.prototype.slice.call(document.querySelectorAll('.artist-calendar-view-btn'));

    if (!container || !titleEl) {
        return;
    }

    const now = new Date();
    let currentDate = new Date(now.getFullYear(), now.getMonth(), now.getDate());
    let viewMode = (ARTIST_CALENDAR_INITIAL_FILTERS && ARTIST_CALENDAR_INITIAL_FILTERS.view) || 'month';
    let events = normalizeEvents(Array.isArray(ARTIST_CALENDAR_INITIAL_EVENTS) ? ARTIST_CALENDAR_INITIAL_EVENTS : []);
    let pollTimer = null;

    function pad(value) {
        return String(value).padStart(2, '0');
    }

    function formatDate(date) {
        return date.getFullYear() + '-' + pad(date.getMonth() + 1) + '-' + pad(date.getDate());
    }

    function parseDate(dateString) {
        const parts = (dateString || '').split('-');
        if (parts.length !== 3) return null;
        const y = parseInt(parts[0], 10);
        const m = parseInt(parts[1], 10) - 1;
        const d = parseInt(parts[2], 10);
        const parsed = new Date(y, m, d);
        return Number.isNaN(parsed.getTime()) ? null : parsed;
    }

    function normalizeEvents(input) {
        return input.map(function (event) {
            return {
                id: Number(event.id || 0),
                drama_id: Number(event.drama_id || 0),
                drama_name: event.drama_name || 'Drama',
                event_type: (event.event_type || '').toLowerCase(),
                event_title: event.event_title || 'Untitled Event',
                event_description: event.event_description || '',
                scheduled_date: event.scheduled_date || '',
                start_time: (event.start_time || '').substring(0, 5),
                end_time: (event.end_time || '').substring(0, 5),
                venue: event.venue || '',
                status: event.status || '',
                role_name: event.role_name || '',
                is_director_drama: !!event.is_director_drama,
                is_pm_drama: !!event.is_pm_drama,
                is_actor_drama: !!event.is_actor_drama,
                participation_type: event.participation_type || 'actor'
            };
        });
    }

    function getParticipationLabel(event) {
        if (event.participation_type === 'director') return 'Director';
        if (event.participation_type === 'pm') return 'PM';
        return 'Actor';
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.appendChild(document.createTextNode(text == null ? '' : String(text)));
        return div.innerHTML;
    }

    function sameDate(dateA, dateB) {
        return dateA.getFullYear() === dateB.getFullYear() && dateA.getMonth() === dateB.getMonth() && dateA.getDate() === dateB.getDate();
    }

    function toMinutes(hhmm) {
        if (!hhmm || hhmm.indexOf(':') === -1) return null;
        const parts = hhmm.split(':');
        return (parseInt(parts[0], 10) * 60) + parseInt(parts[1], 10);
    }

    function detectConflictIds(sourceEvents) {
        const byDate = {};
        const conflictIds = new Set();

        sourceEvents.forEach(function (event) {
            if (!byDate[event.scheduled_date]) {
                byDate[event.scheduled_date] = [];
            }
            byDate[event.scheduled_date].push(event);
        });

        Object.keys(byDate).forEach(function (dateKey) {
            const dayEvents = byDate[dateKey].slice().sort(function (a, b) {
                return String(a.start_time).localeCompare(String(b.start_time));
            });

            for (let i = 0; i < dayEvents.length; i++) {
                for (let j = i + 1; j < dayEvents.length; j++) {
                    const aStart = toMinutes(dayEvents[i].start_time);
                    const aEnd = toMinutes(dayEvents[i].end_time);
                    const bStart = toMinutes(dayEvents[j].start_time);
                    const bEnd = toMinutes(dayEvents[j].end_time);

                    if (aStart == null || aEnd == null || bStart == null || bEnd == null) {
                        continue;
                    }

                    if (aStart < bEnd && aEnd > bStart) {
                        conflictIds.add(dayEvents[i].id);
                        conflictIds.add(dayEvents[j].id);
                    }
                }
            }
        });

        return conflictIds;
    }

    function getFilteredEvents() {
        const dramaId = Number((filterDrama && filterDrama.value) || 0);
        const participation = (filterParticipation && filterParticipation.value) || 'all';
        let result = events;
        if (dramaId > 0) {
            result = result.filter(function (event) {
                return Number(event.drama_id) === dramaId;
            });
        }

        if (participation !== 'all') {
            result = result.filter(function (event) {
                return event.participation_type === participation;
            });
        }

        return result;
    }

    function renderMonthView(sourceEvents) {
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const weekdayStart = firstDay.getDay();
        const daysInMonth = lastDay.getDate();
        const weekdays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

        titleEl.textContent = currentDate.toLocaleString('en-US', { month: 'long', year: 'numeric' });
        const conflictIds = detectConflictIds(sourceEvents);

        let html = '<div class="artist-calendar-month-grid">';
        weekdays.forEach(function (dayName) {
            html += '<div class="artist-calendar-weekday">' + dayName + '</div>';
        });

        for (let i = 0; i < weekdayStart; i++) {
            html += '<div class="artist-calendar-day-cell"></div>';
        }

        for (let day = 1; day <= daysInMonth; day++) {
            const dateObj = new Date(year, month, day);
            const dateKey = formatDate(dateObj);
            const dayEvents = sourceEvents.filter(function (event) {
                return event.scheduled_date === dateKey;
            });
            const isToday = sameDate(dateObj, new Date());

            html += '<div class="artist-calendar-day-cell' + (isToday ? ' is-today' : '') + '">';
            html += '<div class="artist-calendar-day-number">' + day + '</div>';

            dayEvents.slice(0, 4).forEach(function (event) {
                const conflict = conflictIds.has(event.id);
                html += '<div class="artist-calendar-chip ' + escapeHtml(event.event_type) + (conflict ? ' conflict' : '') + '">';
                html += escapeHtml('[' + getParticipationLabel(event) + '] ' + event.start_time + ' ' + event.event_title + ' · ' + event.drama_name);
                html += '</div>';
            });

            if (dayEvents.length > 4) {
                html += '<div class="artist-calendar-chip">+' + (dayEvents.length - 4) + ' more</div>';
            }

            html += '</div>';
        }

        html += '</div>';
        container.innerHTML = html;
    }

    function renderListView(sourceEvents, mode) {
        const conflictIds = detectConflictIds(sourceEvents);
        const baseDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), currentDate.getDate());
        let start = new Date(baseDate);
        let end = new Date(baseDate);

        if (mode === 'week') {
            start.setDate(baseDate.getDate() - baseDate.getDay());
            end = new Date(start);
            end.setDate(start.getDate() + 6);
            titleEl.textContent = 'Week of ' + start.toLocaleDateString();
        } else {
            titleEl.textContent = baseDate.toLocaleDateString(undefined, { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });
        }

        const startKey = formatDate(start);
        const endKey = formatDate(end);

        const filtered = sourceEvents.filter(function (event) {
            return event.scheduled_date >= startKey && event.scheduled_date <= endKey;
        });

        if (filtered.length === 0) {
            container.innerHTML = '<div class="artist-calendar-empty">No scheduled events for this ' + mode + '.</div>';
            return;
        }

        const grouped = {
            director: [],
            actor: [],
            pm: []
        };

        filtered.forEach(function (event) {
            const key = event.participation_type === 'director' || event.participation_type === 'pm' ? event.participation_type : 'actor';
            grouped[key].push(event);
        });

        const sections = [
            { key: 'director', title: 'Director Dramas' },
            { key: 'actor', title: 'Actor Dramas' },
            { key: 'pm', title: 'PM Dramas' }
        ];

        const sectionHtml = sections.map(function (section) {
            const items = grouped[section.key];
            if (!items || items.length === 0) {
                return '';
            }

            const cards = items.map(function (event) {
                const conflict = conflictIds.has(event.id);
                const timeRange = (event.start_time || '--:--') + ' - ' + (event.end_time || '--:--');
                return '<div class="artist-calendar-list-item ' + (conflict ? 'conflict' : '') + '">' +
                    '<h4 class="artist-calendar-list-item-title">' + escapeHtml(event.event_title) + '</h4>' +
                    '<div class="artist-calendar-list-meta">' +
                        '<span><i class="bx bx-calendar"></i> ' + escapeHtml(event.scheduled_date) + '</span>' +
                        '<span><i class="bx bx-time"></i> ' + escapeHtml(timeRange) + '</span>' +
                        '<span><i class="bx bx-film"></i> ' + escapeHtml(event.drama_name) + '</span>' +
                    '</div>' +
                    (conflict ? '<div class="artist-calendar-list-meta"><span><i class="bx bx-error-circle"></i> Conflict detected with another event in this range.</span></div>' : '') +
                '</div>';
            }).join('');

            return '<div class="artist-calendar-group">' +
                '<h4 class="artist-calendar-group-title">' + escapeHtml(section.title) + '</h4>' +
                '<div class="artist-calendar-list">' + cards + '</div>' +
            '</div>';
        }).join('');

        container.innerHTML = sectionHtml !== ''
            ? '<div class="artist-calendar-group-wrap">' + sectionHtml + '</div>'
            : '<div class="artist-calendar-empty">No scheduled events for this ' + mode + '.</div>';
    }

    function renderCurrentView() {
        viewButtons.forEach(function (button) {
            button.classList.toggle('active', button.getAttribute('data-view') === viewMode);
        });

        const filtered = getFilteredEvents();
        if (viewMode === 'month') {
            renderMonthView(filtered);
        } else if (viewMode === 'week') {
            renderListView(filtered, 'week');
        } else {
            renderListView(filtered, 'day');
        }
    }

    function getNavigationRange() {
        if (viewMode === 'month') {
            const start = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
            const end = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
            return { start: formatDate(start), end: formatDate(end) };
        }

        if (viewMode === 'week') {
            const start = new Date(currentDate.getFullYear(), currentDate.getMonth(), currentDate.getDate());
            start.setDate(start.getDate() - start.getDay());
            const end = new Date(start);
            end.setDate(start.getDate() + 6);
            return { start: formatDate(start), end: formatDate(end) };
        }

        const day = formatDate(currentDate);
        return { start: day, end: day };
    }

    function updateSyncInfo(message) {
        if (!syncInfoEl) return;
        syncInfoEl.textContent = message;
    }

    function fetchFeed() {
        const dramaId = Number((filterDrama && filterDrama.value) || 0);
        const participation = (filterParticipation && filterParticipation.value) || 'all';
        const range = getNavigationRange();

        if (filterStart && filterStart.value) {
            range.start = filterStart.value;
        }
        if (filterEnd && filterEnd.value) {
            range.end = filterEnd.value;
        }

        const params = new URLSearchParams();
        params.set('start_date', range.start);
        params.set('end_date', range.end);
        if (dramaId > 0) params.set('drama_id', String(dramaId));
        if (participation !== 'all') params.set('participation', participation);

        fetch(ROOT + '/artistdashboard/calendar_feed?' + params.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(function (response) { return response.json(); })
            .then(function (payload) {
                if (!payload || payload.success !== true || !Array.isArray(payload.events)) {
                    throw new Error('Invalid calendar payload');
                }
                events = normalizeEvents(payload.events);
                renderCurrentView();
                updateSyncInfo('Synced at ' + new Date().toLocaleTimeString());
            })
            .catch(function () {
                updateSyncInfo('Could not refresh calendar just now.');
            });
    }

    function moveCalendar(delta) {
        if (viewMode === 'month') {
            currentDate.setMonth(currentDate.getMonth() + delta);
        } else if (viewMode === 'week') {
            currentDate.setDate(currentDate.getDate() + (7 * delta));
        } else {
            currentDate.setDate(currentDate.getDate() + delta);
        }
        fetchFeed();
    }

    function bindEvents() {
        viewButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                viewMode = button.getAttribute('data-view') || 'month';
                fetchFeed();
            });
        });

        if (prevBtn) {
            prevBtn.addEventListener('click', function () {
                moveCalendar(-1);
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', function () {
                moveCalendar(1);
            });
        }

        if (todayBtn) {
            todayBtn.addEventListener('click', function () {
                currentDate = new Date();
                if (filterStart) filterStart.value = formatDate(new Date(currentDate.getFullYear(), currentDate.getMonth(), 1));
                if (filterEnd) filterEnd.value = formatDate(new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0));
                fetchFeed();
            });
        }

        if (filterForm) {
            filterForm.addEventListener('submit', function (event) {
                event.preventDefault();
                fetchFeed();
            });
        }
    }

    function startPolling() {
        if (pollTimer) {
            clearInterval(pollTimer);
        }

        pollTimer = setInterval(function () {
            fetchFeed();
        }, 45000);
    }

    function init() {
        if (ARTIST_CALENDAR_INITIAL_FILTERS && ARTIST_CALENDAR_INITIAL_FILTERS.view) {
            viewMode = ARTIST_CALENDAR_INITIAL_FILTERS.view;
        }

        bindEvents();
        renderCurrentView();
        updateSyncInfo('Loaded at ' + new Date().toLocaleTimeString());
        startPolling();
    }

    init();
})();
