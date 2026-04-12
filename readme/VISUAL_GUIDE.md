# Visual Guide: Tabbed Interface Components

## Color Scheme
- **Brand Color**: #ba8e23 (Golden brown)
- **Active Text**: #ba8e23
- **Inactive Text**: #6b7280 (Muted gray)
- **Hover Text**: #1f2933 (Dark ink)
- **Border**: #e0e0e0
- **Background**: #ffffff (White)

## Tab Strip Layout

```
┌─────────────────────────────────────────────────────────┐
│ [🎬 Drama] [📥 Pending] [👥 Roles] [👔 Manager] [📅] [💰]  │
│    Overview  Applications         Production Manager  Schedule  Budget
│     active     inactive   inactive   inactive        inactive  inactive
│ ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ │
│ Border at bottom (golden on active)                   │
└─────────────────────────────────────────────────────────┘
```

## Director Dashboard Structure

```
┌─────────────────────────────────────────────┐
│         DIRECTOR DASHBOARD - Sinhabahu      │
│    Status: Active | Historical Drama        │
│  [Avatar] Director Role Badge               │
└─────────────────────────────────────────────┘

┌─────────────┬─────────────┬─────────────┬──────────────┐
│  15 Total  │  12/15 Filled │  1 Manager │ 8 Applications│
│   Roles    │   Roles      │            │   Pending     │
└─────────────┴─────────────┴─────────────┴──────────────┘

┌─────────────────────────────────────────────┐
│ 🎬 Drama | 📥 Pending | 👥 Roles | 👔 Prod | 📅 Sched | 💰 Budget
├─────────────────────────────────────────────┤
│                                             │
│        [Active Tab Content Here]            │
│                                             │
│        - Created: 2024-11-20                │
│        - Genre: Historical                  │
│        - Language: Sinhala                  │
│        - Budget: LKR 800,000               │
│        - Certificate: Verified             │
│                                             │
│  [Edit Details]                            │
│                                             │
└─────────────────────────────────────────────┘

┌─────────────────────────────────────────────┐
│              Quick Actions                  │
│  [Manage Roles] [Search Artists]           │
│  [Add Schedule] [Assign Manager]           │
└─────────────────────────────────────────────┘
```

## Production Manager Dashboard Structure

```
┌─────────────────────────────────────────────┐
│   PRODUCTION MANAGER DASHBOARD - Sinhabahu  │
│              Sinhabahu                      │
│  [Avatar] Production Manager Role Badge    │
└─────────────────────────────────────────────┘

┌─────────────┬──────────────┬─────────────┬──────────┐
│  LKR 800k  │  LKR 336k   │  12 Active │  4 Theater│
│   Total    │   Used (42%)  │  Services   │ Bookings │
└─────────────┴──────────────┴─────────────┴──────────┘

┌─────────────────────────────────────────────┐
│ 💰 Budget | 💼 Services | 🎭 Theater Bookings
├─────────────────────────────────────────────┤
│                                             │
│        [Active Tab Content Here]            │
│                                             │
│        Budget Progress:  ████░░░░░░ 42%    │
│                                             │
│        [Manage Budget Button]               │
│                                             │
└─────────────────────────────────────────────┘

┌─────────────────────────────────────────────┐
│              Quick Actions                  │
│  [Manage Services] [Manage Budget]         │
│  [Book Theater] [Service Schedule]         │
└─────────────────────────────────────────────┘
```

## Tab Button States

### Active Tab
```
[🎬 Drama Overview]
   │
   │ (golden underline)
───┴─────────────────
   ↑
 Underline: #ba8e23
 Text: #ba8e23
```

### Inactive Tab
```
[📥 Pending Applications]
────────────────────────
   ↑
 Underline: transparent
 Text: #6b7280 (muted)
```

### Hover State
```
[📥 Pending Applications]
────────────────────────
   ↑
 Underline: transparent
 Text: #1f2933 (dark)
```

## Tab Panel Animation

```
When switching tabs:

Frame 1 (50ms):  Content fades in, slides up slightly
  ├─ Opacity: 0 → 1
  └─ Transform: translateY(5px) → translateY(0)

Frame 2 (300ms): Animation completes smoothly
  └─ Transition: all 0.3s ease
```

## Responsive Behavior

### Desktop (1024px+)
- All tabs visible horizontally
- Full content width
- Smooth scrolling if overflow

### Tablet (768px - 1023px)
- Tabs may wrap or scroll
- Content adjusts width
- Touch-friendly tab size

### Mobile (< 768px)
- Horizontal scrolling for tabs
- Full-width content
- Larger touch targets (at least 44x44px)

## Icon Usage in Tabs

All tabs include Font Awesome icons for quick visual identification:

- 🎬 Drama Overview: `fas fa-film`
- 📥 Pending Applications: `fas fa-inbox`
- 👥 Artist Roles: `fas fa-users`
- 👔 Production Manager: `fas fa-user-tie`
- 📅 Schedule: `fas fa-calendar-alt`
- 💰 Services & Budget: `fas fa-dollar-sign`
- 💼 Services: `fas fa-briefcase`
- 🎭 Theater Bookings: `fas fa-theater-masks`

## Status Badges

Status badges appear in tab content with predefined colors:

```
[Assigned]     - Green (#d4edda, #155724)
[Unassigned]   - Red (#f8d7da, #721c24)
[Pending]      - Yellow (#fff3cd, #856404)
[Requested]    - Blue (#d1ecf1, #0c5460)
```

## Navigation Flow

```
User clicks tab
    ↓
JavaScript event listener triggered
    ↓
Remove 'active' class from all buttons/panels
    ↓
Add 'active' class to clicked button and content panel
    ↓
Update URL with tab parameter (?tab=drama-overview)
    ↓
Panel fades in with animation
    ↓
Browser history updated (can use back button)
```

## CSS Classes Reference

| Class | Purpose | Applied To |
|-------|---------|-----------|
| `.tab-strip` | Container for tab buttons | `<div>` |
| `.tab-btn` | Individual tab button | `<button>` |
| `.tab-btn.active` | Active tab styling | `<button>` |
| `.tab-panels` | Container for content panels | `<div>` |
| `.tab-panel` | Individual content panel | `<div>` |
| `.tab-panel.active` | Active panel display | `<div>` |

## Accessibility Features

- **ARIA Roles**: `tablist`, `tab`, `tabpanel`
- **ARIA Attributes**: `aria-selected`, `aria-controls`, `aria-label`
- **Semantic HTML**: Proper heading hierarchy maintained
- **Keyboard Support**: Ready for arrow key navigation (future enhancement)
- **Focus Indicators**: Default browser focus visible on tabs

## Code Examples

### HTML Tab Structure
```html
<div class="tab-strip" role="tablist" aria-label="Dashboard sections">
    <button class="tab-btn active" data-tab="overview" role="tab" 
            aria-selected="true" aria-controls="overview-panel">
        <i class="fas fa-film"></i> Overview
    </button>
</div>

<div class="tab-panels">
    <div class="tab-panel active" id="overview-panel" 
         role="tabpanel" data-tab="overview">
        <!-- Tab content -->
    </div>
</div>
```

### JavaScript Tab Handler
```javascript
tabButtons.forEach(button => {
    button.addEventListener('click', () => {
        const tabName = button.getAttribute('data-tab');
        
        // Hide all panels, show clicked one
        tabButtons.forEach(btn => btn.classList.remove('active'));
        tabPanels.forEach(panel => panel.classList.remove('active'));
        
        // Activate clicked
        button.classList.add('active');
        document.querySelector(`[data-tab="${tabName}"]`)
            .closest('.tab-panel').classList.add('active');
        
        // Update URL
        const url = new URL(window.location);
        url.searchParams.set('tab', tabName);
        window.history.replaceState({}, '', url);
    });
});
```

---

## Design Principles

1. **Clarity**: Clear tab labels with icons for quick recognition
2. **Consistency**: Same design applied to both dashboards
3. **Responsiveness**: Works on all screen sizes
4. **Accessibility**: Full ARIA support and semantic HTML
5. **Performance**: Smooth CSS animations, no heavy JavaScript
6. **Maintainability**: Simple class-based styling, easy to modify

