# Quick Start: Tabbed Interface Guide

## What Changed?

Both dashboards now use **horizontal tabs** instead of showing all content at once. This creates a cleaner, more organized interface.

---

## Director Dashboard Tabs

### 1. Drama Overview 🎬
Shows basic drama information:
- Created date
- Genre
- Language  
- Budget
- Certificate status
- [Edit Details button]

### 2. Pending Role Applications 📥
Lists artists waiting for role approval:
- Applicant name
- Role applied for
- Application date
- Action buttons: View, Accept, Reject
- [Review All link]

### 3. Artist Roles Summary 👥
Shows all roles and their assignment status:
- Role name
- Assigned artist or status
- Salary information
- [Manage All Roles button]

### 4. Production Manager 👔
Information about the assigned production manager:
- Manager name
- Responsibilities
- Assignment date
- Status badge
- [View Profile button]
- [Change Manager button]

### 5. Upcoming Schedule 📅
Scheduled events and meetings:
- Event name
- Date and time
- Location/venue
- Status (Confirmed/Pending)
- [Manage Schedule button]

### 6. Services & Budget Overview 💰
Financial summary:
- Total budget
- Budget used (with percentage)
- Services booked count
- Pending payments
- View-only notice

---

## Production Manager Dashboard Tabs

### 1. Budget Overview 💰
Budget management:
- Visual progress bar showing budget usage (%)
- Current budget allocated
- Budget used
- [Manage Budget button]

### 2. Services 💼
Service request management:
- Recent service requests
- Service provider names
- Status badges (Pending/Confirmed)
- [View All Services button]

### 3. Theater Bookings 🎭
Theater reservation information:
- Theater name
- Booking date and time
- Booking status
- [Book Theater button]

---

## How to Use Tabs

### Click a Tab
Simply click any tab button at the top to view its content.

### Current Tab
The active tab has:
- **Golden/brand color text**
- **Golden underline**
- **Highlighted appearance**

### Navigation
The tabs remain at the top while scrolling through content, so you can always easily switch between sections.

### Direct Link to Tab
Share or bookmark specific tabs:
- `dashboard.php?drama_id=1&tab=drama-overview`
- `dashboard.php?drama_id=1&tab=pending-applications`
- `dashboard.php?drama_id=1&tab=services-budget`

### Browser Back Button
Use browser back/forward buttons to navigate between tabs you've viewed.

---

## Visual Indicators

### Tab States

**Active Tab:**
```
[🎬 Drama Overview] ← Golden text and underline
```

**Inactive Tab:**
```
[📥 Pending Applications] ← Gray text, no underline
```

**Hover:**
```
[👥 Artist Roles] ← Dark text on hover
```

---

## Mobile Experience

On smaller screens:
- Tabs may scroll horizontally if there are many
- Smooth horizontal scrolling is enabled
- All tabs remain accessible with touch
- Content remains responsive and readable

---

## Quick Actions

Both dashboards have a **Quick Actions** section below the tabs for:
- Common tasks
- Button-based navigation
- Easy access to related pages

---

## Status Badges

Content may include status badges with colors:

| Badge | Color | Meaning |
|-------|-------|---------|
| Assigned | Green | Role/item is assigned |
| Unassigned | Red | No assignment yet |
| Pending | Yellow | Awaiting decision/confirmation |
| Requested | Blue | Request has been sent |
| Confirmed | Green | Confirmed/approved |

---

## Tips & Tricks

1. **Sticky Header**: Statistics cards stay visible while scrolling
2. **Smooth Animation**: Tab content fades in smoothly
3. **Keyboard Ready**: Tab structure is keyboard-accessible (future keyboard nav support)
4. **Responsive**: Works on desktop, tablet, and mobile
5. **Bookmarkable**: Each tab can be bookmarked with its URL parameter

---

## Troubleshooting

### Tab Not Switching?
- Make sure JavaScript is enabled in your browser
- Try refreshing the page
- Check browser console for errors (F12 > Console)

### Tab Underline Not Showing?
- Clear browser cache and refresh
- Check if browser supports CSS animations

### Content Not Loading in Tab?
- Check browser network tab for failed requests (F12 > Network)
- Verify all link paths are correct

---

## Browser Support

✅ Chrome/Edge (v88+)
✅ Firefox (v78+)
✅ Safari (v12+)
✅ Mobile browsers (iOS Safari, Chrome Mobile)

---

## Features

- **Responsive Design**: Works on all screen sizes
- **Accessible**: ARIA labels and semantic HTML
- **Smooth Animations**: CSS fade-in effect when switching
- **URL Integration**: Tabs are reflected in browser URL
- **Icon Support**: Visual icons for quick recognition
- **Performance**: Lightweight CSS-based implementation

---

## Future Enhancements

Planned improvements:
- Keyboard arrow navigation between tabs
- Tab notifications/badges
- Lazy loading for better performance
- Swipe gestures for mobile

---

## Questions?

Refer to the detailed documentation:
- `TABBED_INTERFACE_IMPLEMENTATION.md` - Full technical details
- `VISUAL_GUIDE.md` - Design patterns and color schemes

