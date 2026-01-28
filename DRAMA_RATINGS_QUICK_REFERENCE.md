## Drama Rating System - Quick Reference Guide

### 🚀 Quick Start

**For Users:**
1. Login to the platform
2. Click "Browse Dramas"
3. Click any drama to view details
4. Click "Rate Drama" button
5. Select 1-5 stars and optionally add comment
6. Click "Submit Rating"

**For Developers:**

### API Endpoints

#### 1. Submit Rating (POST)
```
POST /BrowseDramas/submitRating

Request Body:
{
  "drama_id": 123,
  "rating": 5,
  "comment": "Amazing!"
}

Response:
{
  "success": true,
  "message": "Rating submitted successfully",
  "summary": {
    "average_rating": 4.5,
    "total_ratings": 10
  }
}
```

#### 2. Get Ratings (GET)
```
GET /BrowseDramas/getRatings/123?limit=10&offset=0

Response:
{
  "success": true,
  "ratings": [...],
  "total": 25,
  "limit": 10,
  "offset": 0
}
```

#### 3. Mark Helpful (POST)
```
POST /BrowseDramas/markHelpful/456

Response:
{
  "success": true,
  "message": "Rating marked as helpful"
}
```

---

### Model Methods Quick Reference

```php
// In your controller:
$ratingModel = $this->getModel('M_rating');

// Submit or update rating
$ratingModel->submitRating($drama_id, $user_id, $rating, $comment);

// Get summary
$summary = $ratingModel->getDramaRatingSummary($drama_id);
echo $summary->average_rating;  // 4.5
echo $summary->total_ratings;   // 10

// Get all ratings
$ratings = $ratingModel->getDramaRatings($drama_id, $limit=10, $offset=0);

// Check if user rated
$hasRated = $ratingModel->hasUserRated($drama_id, $user_id);

// Get user's rating
$userRating = $ratingModel->getUserDramaRating($drama_id, $user_id);
echo $userRating->rating;  // 5
echo $userRating->comment; // "Amazing!"

// Mark helpful
$ratingModel->markAsHelpful($rating_id);

// Get top rated dramas
$topDramas = $ratingModel->getTopRatedDramas($limit=10);

// Admin stats
$stats = $ratingModel->getRatingStatistics();
```

---

### Frontend JavaScript

```javascript
// Already loaded when drama_details.view.php is displayed

// Open rating modal
openRatingModal();

// Select rating (1-5)
selectRating(4);

// Show toast message
showSuccessToast("Rating submitted!");

// Mark as helpful
markAsHelpful(ratingId, buttonElement);
```

---

### Database Queries

**Get drama ratings summary:**
```sql
SELECT 
  COUNT(id) as total_ratings,
  ROUND(AVG(rating), 2) as average_rating,
  COUNT(CASE WHEN rating = 5 THEN 1 END) as five_star_count,
  COUNT(CASE WHEN rating = 4 THEN 1 END) as four_star_count,
  COUNT(CASE WHEN rating = 3 THEN 1 END) as three_star_count,
  COUNT(CASE WHEN rating = 2 THEN 1 END) as two_star_count,
  COUNT(CASE WHEN rating = 1 THEN 1 END) as one_star_count
FROM drama_ratings
WHERE drama_id = ?;
```

**Get all ratings for drama:**
```sql
SELECT 
  dr.*,
  u.full_name,
  u.email
FROM drama_ratings dr
JOIN users u ON dr.user_id = u.id
WHERE dr.drama_id = ?
ORDER BY dr.created_at DESC;
```

**Top rated dramas:**
```sql
SELECT 
  d.id, d.title, d.image,
  ROUND(AVG(dr.rating), 2) as avg_rating,
  COUNT(dr.id) as total_ratings
FROM dramas d
LEFT JOIN drama_ratings dr ON d.id = dr.drama_id
GROUP BY d.id
ORDER BY avg_rating DESC
LIMIT 10;
```

---

### Key Features

✅ **1-5 Star Rating System**
- Visual star picker with hover effects
- Emoji feedback for each rating level

✅ **Optional Comments**
- Rich text support
- 500 character limit
- Real-time character counter

✅ **Update Existing Ratings**
- One rating per user per drama
- Edit previous rating
- Shows "Already Rated" notice

✅ **Rating Summary**
- Average rating display
- Total rating count
- Star distribution

✅ **Community Feedback**
- Mark ratings as helpful
- Helpful count tracking

✅ **Admin Features**
- Rating statistics
- Top rated dramas
- Recent ratings feed

✅ **Security**
- User authentication required
- Input validation
- XSS prevention
- SQL injection prevention

---

### CSS Classes

```css
/* Modal */
.rating-modal-overlay  /* Modal background */
.rating-modal          /* Modal container */

/* Star Picker */
.star-picker           /* Star selection area */
.star-btn              /* Individual star button */
.star-btn.selected     /* Selected star */

/* Comments */
.comment-section       /* Comment area */
.comment-textarea      /* Comment input */
.comment-counter       /* Character count */

/* Ratings List */
.ratings-list          /* All ratings container */
.rating-item           /* Individual rating */
.rating-header         /* Rating header with user */
.rating-comment        /* Rating comment text */
.rating-date           /* Posted date */

/* Buttons */
.submit-btn            /* Submit rating button */
.close-btn             /* Close modal button */
.helpful-btn           /* Mark helpful button */

/* Notifications */
.toast-notification    /* Toast message */
.toast-notification.show  /* Visible toast */
```

---

### Common Tasks

**Display rating on product listing:**
```php
$ratingModel = $this->getModel('M_rating');
$summary = $ratingModel->getDramaRatingSummary($drama_id);

echo "★ " . $summary->average_rating . " (" . $summary->total_ratings . " ratings)";
```

**Show top rated dramas:**
```php
$ratingModel = $this->getModel('M_rating');
$topDramas = $ratingModel->getTopRatedDramas(10);

foreach ($topDramas as $drama) {
  echo $drama['title'] . " - " . $drama['average_rating'] . " stars";
}
```

**Check if user rated:**
```php
if ($ratingModel->hasUserRated($drama_id, $user_id)) {
  echo "You have already rated this drama";
}
```

**Get user's rating:**
```php
$userRating = $ratingModel->getUserDramaRating($drama_id, $user_id);
if ($userRating) {
  echo "Your rating: " . $userRating->rating . " stars";
  echo "Your comment: " . $userRating->comment;
}
```

---

### Troubleshooting

**Q: Rating not saving?**
- Check user is logged in (session check)
- Verify drama exists
- Check database connection
- Look at browser console for JS errors

**Q: Average rating not updating?**
- Check database for duplicate records
- Verify ON DUPLICATE KEY UPDATE in INSERT
- Clear browser cache

**Q: Modal not opening?**
- Check drama_ratings.js is loaded
- Check console for JS errors
- Verify #rateBtn exists in HTML
- Check Z-index conflicts

**Q: Comments not showing?**
- Check comment textarea is populated
- Verify htmlspecialchars() on output
- Check max 500 chars not exceeded

---

### Browser Compatibility

✅ Chrome 90+
✅ Firefox 88+
✅ Safari 14+
✅ Edge 90+
✅ Mobile browsers (iOS Safari, Chrome Android)

---

### Performance Tips

1. **Pagination**: Use `getDramaRatings()` with limit/offset
2. **Caching**: Cache summary on drama pages (5 min)
3. **Indexes**: Already optimized on drama_id, user_id, rating
4. **Queries**: Use prepared statements (built-in)

---

### File Structure

```
app/
├── models/
│   └── M_rating.php              (Rating model - 11 methods)
├── controllers/
│   └── BrowseDramas.php          (4 new endpoints)
└── views/
    └── drama_details.view.php    (Updated with rating UI)

public/assets/
├── JS/
│   └── drama-ratings.js          (Frontend logic)
└── CSS/
    └── drama_ratings.css         (Component styles)

Database/
├── COMPLETE_DATABASE_SETUP.sql   (Includes ratings table)
└── DRAMA_RATINGS_DATABASE_SETUP.sql  (Standalone setup)

Documentation/
└── DRAMA_RATINGS_IMPLEMENTATION.md   (Full guide)
```

---

### Security Checklist

✅ User authentication required
✅ SQL injection prevention (prepared statements)
✅ XSS prevention (htmlspecialchars)
✅ Unique rating constraint (DB level)
✅ Input validation (rating range 1-5)
✅ Comment length validation (max 500)
✅ Drama existence verification
✅ User ID in delete operations (no ID injection)

---

**Last Updated**: January 28, 2026  
**Status**: Production Ready ✅  
**Support**: Refer to DRAMA_RATINGS_IMPLEMENTATION.md for detailed guide
