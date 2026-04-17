# Artist Dashboard View: Line-by-Line Walkthrough

Target file: app/views/artistdashboard.view.php

This guide explains the file in the same order it runs, using tight line ranges so you can map each explanation back to the source quickly.

## 1) Bootstrapping and Data Extraction

- Lines 1-5: Opens PHP and safely extracts the incoming data array so keys become local variables.
- Lines 7-15: Builds the profile image source with fallback logic.
  - Default image is used first.
  - If the user has a custom image, path separators are normalized.
  - If the stored value already has a slash, it is treated as a path.
  - Otherwise it is treated as a filename inside uploads/profile_images.

## 2) Request State and Active Tab Resolution

- Lines 17-20: Reads current request path and tab query param.
- Lines 21-26: Supports tab alias mapping and validates against allowed dashboard tabs.
- Lines 28-31: Resolves active showings subtab and validates allowed values.
- Lines 33-35: Reads accepted-showings filter query params (date, start time, end time).

## 3) Time Parsing Helper

- Lines 37-70: Defines parseTimeToMinutes closure.
  - Accepts 24-hour format HH:MM.
  - Accepts 12-hour format HH:MM AM/PM.
  - Returns integer minutes from midnight or null when invalid.

## 4) Accepted Showings Preprocessing and Filtering

- Lines 72-74: Initializes accepted list, filtered list, and slot counter map.
- Lines 76-117: Iterates through accepted showings and does two jobs.
  - Job A: Builds acceptedSlotCounts keyed by date|start|end for conflict checks.
  - Job B: Applies GET filters and stores only matched items in filteredAcceptedShowRequests.

## 5) Sidebar Active State (Server-Side)

- Lines 119-125: Initializes sidebar state map.
- Lines 127-136: Sets active sidebar entry using request path and active tab.
  - Notifications, vacancies, classes use URL path checks.
  - Showings uses activeTab check.
  - Dashboard is fallback.

## 6) HTML Head and Inline Styles

- Lines 138-140: Loads external CSS resources and favicon.
- Lines 141-736: Inline style block.
  - Header profile badge and menu styling.
  - Card, stats, tabs, and showings visual styles.
  - showings-only body mode that hides top dashboard sections.
  - Responsive rules for mobile layout in showings cards.

## 7) Body Start and Toast Scripts

- Line 737: Opens body with conditional class showings-only when showings tab is active.
- Lines 738-739: Loads toast.js.
- Lines 740-754: Outputs success/error toast calls only when session messages exist, then unsets them.

## 8) Sidebar Markup

- Lines 757-795: Sidebar menu.
  - Each list item uses PHP class switching via sidebarActive.
  - Showings link points to server-driven tab/subtab query params.

## 9) Header and User Menu

- Lines 800-834: Header content.
  - Shows dashboard title and user full name fallback.
  - Uses details/summary for profile menu without custom JS.
  - Profile image keeps inline onerror fallback to default image.

## 10) Session Message Box

- Lines 836-840: Renders a classic info-box for session message and clears it.

## 11) Statistics Section

- Lines 842-875: Four stat cards.
  - total_dramas, as_director, as_manager, as_actor.
  - Each uses zero fallback if missing.

## 12) Vacancy Banner

- Lines 877-892: Banner encouraging role discovery with browse vacancies link.

## 13) Main Tab Navigation (PHP-Driven)

- Lines 894-912: Tab links rendered as normal anchors with query param tab=...
  - Active class is decided entirely in PHP.
  - No JS needed to switch top-level tabs.

## 14) Director Tab Content

- Lines 919-992: Director tab panel.
  - Active class controlled by activeTab.
  - Empty state includes create drama CTA.
  - Non-empty state loops dramas_as_director with metadata.
  - Manage and Publish actions are plain links.

## 15) Production Manager Tab Content

- Lines 994-1042: PM tab panel.
  - Empty state when no assignments.
  - Non-empty loop for dramas_as_manager with status badge.
  - Manage link targets production manager dashboard.

## 16) Actor Tab Content

- Lines 1044-1107: Actor tab panel.
  - Empty state invites browsing vacancies.
  - Non-empty loop displays role, drama, director, salary, assigned date, active status.
  - View drama action is a regular link.

## 17) Interview Schedules Tab Content

- Lines 1109-1187: Interview panel.
  - Shows upcoming interview cards when available.
  - Status badge style is selected by confirmation status.
  - Optional director notes are shown when present.
  - Pending interviews show confirm/decline form.
  - Non-pending shows response timestamp and optional note.
  - Empty state includes browse vacancies CTA.

## 18) My Showings Tab (Server-Driven Subtabs)

- Lines 1189-1418: Showings panel with requests, accepted, rejected.

### Requests subtab

- Lines 1195-1205: Subtab links are plain anchors with showings_tab query.
- Lines 1207-1308: Pending show request cards.
  - Parses request_details_json.
  - Normalizes display fields (venue, date, time, sender, contact, schedule).
  - Builds pending slot key and checks conflict count using acceptedSlotCounts.
  - Shows conflict notice when overlap exists.
  - Accept form and reject-with-reason form are both server posts.

### Accepted subtab

- Lines 1310-1372: Accepted showings panel.
  - GET form controls accepted_date, accepted_start_time, accepted_end_time.
  - Apply filter submits to same endpoint.
  - Clear filter link resets query params.
  - Uses filteredAcceptedShowRequests prepared at top of file.
  - Shows empty-filter-result message when no matches.

### Rejected subtab

- Lines 1374-1417: Rejected showings panel.
  - Loops rejected items and displays sender/contact/date/time.
  - Shows rejection reason or default fallback text.

## 19) Requests Tab (PM + Actor)

- Lines 1420-1598: Requests panel with two categories.

### PM requests

- Lines 1423-1511: PM request cards.
  - Displays drama, director, certificate, optional message, requested date.
  - Accept and decline are POST forms.

### Actor requests

- Lines 1513-1597: Actor request cards.
  - Displays drama/director/role with optional description and salary.
  - Accept and decline are POST forms.

## 20) Closing Layout Markup

- Lines 1599-1608: Closes nested containers, main tag, body, and html.

---

## Quick Summary of Runtime Flow

1. PHP resolves request state and active tabs.
2. PHP pre-filters accepted showings and computes conflict counters.
3. Server renders the exact active tab/subtab and filtered results.
4. Forms post back to controller routes for actions.
5. Minimal JS remains only for toast rendering and image fallback behavior.
