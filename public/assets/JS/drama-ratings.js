/**
 * Drama Ratings System - JavaScript
 * Handles star rating selection, form submission, and dynamic interactions
 */

// DOM Elements
const ratingModal = document.getElementById('ratingModal');
const rateBtn = document.getElementById('rateBtn');
const closeRatingModal = document.getElementById('closeRatingModal');
const starPicker = document.getElementById('starPicker');
const selectedRatingInput = document.getElementById('selectedRating');
const ratingComment = document.getElementById('ratingComment');
const submitRating = document.getElementById('submitRating');
const successToast = document.getElementById('successToast');
const charCount = document.getElementById('charCount');
const ratingFeedback = document.getElementById('ratingFeedback');

// Rating feedback messages
const feedbackMessages = {
  1: '😞 Poor - The drama needs improvement',
  2: '😐 Fair - It was okay',
  3: '🙂 Good - I enjoyed it',
  4: '😊 Very Good - Excellent experience',
  5: '🤩 Excellent - Absolutely loved it!'
};

// Initialize event listeners
document.addEventListener('DOMContentLoaded', function() {
  initializeRatingModal();
  initializeStarPicker();
  initializeCommentCounter();
  initializeSubmitButton();
  initializeCloseButton();
  initializeHelpfulButtons();
});

/**
 * Initialize Rating Modal
 */
function initializeRatingModal() {
  if (rateBtn) {
    rateBtn.addEventListener('click', function() {
      openRatingModal();
    });
  }
}

/**
 * Open Rating Modal
 */
function openRatingModal() {
  if (ratingModal) {
    ratingModal.classList.add('active');
    ratingModal.style.display = 'flex';
    
    // Reset form if user hasn't rated yet
    if (!HAS_RATED) {
      resetRatingForm();
    } else {
      // Load existing rating
      loadExistingRating();
    }
  }
}

/**
 * Close Rating Modal
 */
function closeRatingModalFunc() {
  if (ratingModal) {
    ratingModal.classList.remove('active');
    setTimeout(() => {
      ratingModal.style.display = 'none';
    }, 300);
  }
}

/**
 * Close button event
 */
if (closeRatingModal) {
  closeRatingModal.addEventListener('click', closeRatingModalFunc);
}

/**
 * Close modal on overlay click
 */
if (ratingModal) {
  ratingModal.addEventListener('click', function(e) {
    if (e.target === ratingModal) {
      closeRatingModalFunc();
    }
  });
}

/**
 * Initialize Star Picker
 */
function initializeStarPicker() {
  const starBtns = document.querySelectorAll('.star-btn');
  
  starBtns.forEach(btn => {
    btn.addEventListener('click', function() {
      selectRating(this.dataset.value);
    });
    
    btn.addEventListener('mouseenter', function() {
      previewRating(this.dataset.value);
    });
  });
  
  // Reset to selected rating on mouse leave
  if (starPicker) {
    starPicker.addEventListener('mouseleave', function() {
      const selectedValue = selectedRatingInput.value;
      if (selectedValue) {
        displaySelectedStars(selectedValue);
      } else {
        clearStarDisplay();
      }
    });
  }
}

/**
 * Preview rating on hover
 */
function previewRating(value) {
  displaySelectedStars(value);
  if (ratingFeedback) {
    ratingFeedback.textContent = feedbackMessages[value] || '';
  }
}

/**
 * Select a rating
 */
function selectRating(value) {
  value = parseInt(value);
  
  if (value < 1 || value > 5) {
    return;
  }
  
  selectedRatingInput.value = value;
  displaySelectedStars(value);
  
  if (ratingFeedback) {
    ratingFeedback.textContent = feedbackMessages[value];
  }
}

/**
 * Display selected stars visually
 */
function displaySelectedStars(value) {
  const starBtns = document.querySelectorAll('.star-btn');
  
  starBtns.forEach((btn, index) => {
    if (index < value) {
      btn.classList.add('selected');
    } else {
      btn.classList.remove('selected');
    }
  });
}

/**
 * Clear star display
 */
function clearStarDisplay() {
  document.querySelectorAll('.star-btn').forEach(btn => {
    btn.classList.remove('selected');
  });
  if (ratingFeedback) {
    ratingFeedback.textContent = '';
  }
}

/**
 * Initialize Comment Counter
 */
function initializeCommentCounter() {
  if (ratingComment) {
    ratingComment.addEventListener('input', function() {
      charCount.textContent = this.value.length;
    });
  }
}

/**
 * Reset Rating Form
 */
function resetRatingForm() {
  selectedRatingInput.value = 0;
  if (ratingComment) {
    ratingComment.value = '';
  }
  charCount.textContent = '0';
  clearStarDisplay();
  if (ratingFeedback) {
    ratingFeedback.textContent = '';
  }
}

/**
 * Load Existing Rating (if user has already rated)
 */
function loadExistingRating() {
  // Get user's existing rating from data attributes or via AJAX
  // For now, we'll just reset and let them re-rate
  resetRatingForm();
}

/**
 * Initialize Submit Button
 */
function initializeSubmitButton() {
  if (submitRating) {
    submitRating.addEventListener('click', submitRatingForm);
  }
}

/**
 * Submit Rating Form
 */
async function submitRatingForm() {
  const rating = parseInt(selectedRatingInput.value);
  const comment = ratingComment ? ratingComment.value.trim() : '';
  
  // Validate rating
  if (!rating || rating < 1 || rating > 5) {
    showError('Please select a star rating');
    return;
  }
  
  // Disable submit button and show loading state
  const submitBtn = document.getElementById('submitRating');
  const originalText = submitBtn.textContent;
  submitBtn.disabled = true;
  submitBtn.textContent = 'Submitting...';
  
  try {
    const response = await fetch(`${ROOT}/BrowseDramas/submitRating`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        drama_id: DRAMA_ID,
        rating: rating,
        comment: comment || null
      })
    });
    
    const data = await response.json();
    
    if (data.success) {
      showSuccessToast('Rating submitted successfully!');
      closeRatingModalFunc();
      
      // Update rating summary if available
      if (data.summary) {
        updateRatingSummary(data.summary);
      }
      
      // Reload ratings list
      setTimeout(() => {
        location.reload();
      }, 1500);
    } else {
      showError(data.message || 'Failed to submit rating. Please try again.');
    }
  } catch (error) {
    console.error('Error submitting rating:', error);
    showError('An error occurred while submitting your rating');
  } finally {
    // Re-enable submit button
    submitBtn.disabled = false;
    submitBtn.textContent = originalText;
  }
}

/**
 * Update Rating Summary Display
 */
function updateRatingSummary(summary) {
  const ratingElement = document.querySelector('.rating-summary');
  
  if (ratingElement) {
    const avg = parseFloat(summary.average_rating).toFixed(1);
    const total = summary.total_ratings;
    
    ratingElement.innerHTML = `
      <div class="rating-stars" style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
        <span style="font-size: 24px; color: #d4af37;">★</span>
        <span style="font-size: 18px; font-weight: bold; color: #d4af37;">${avg}</span>
        <span style="color: #c9b896; font-size: 14px;">
          (${total} ${total == 1 ? 'rating' : 'ratings'})
        </span>
      </div>
    `;
  }
}

/**
 * Show Error Message
 */
function showError(message) {
  // Create a temporary error toast
  const errorToast = document.createElement('div');
  errorToast.className = 'toast-notification error';
  errorToast.innerHTML = `
    <span class="material-symbols-rounded">error</span>
    <p>${message}</p>
  `;
  
  document.body.appendChild(errorToast);
  
  setTimeout(() => {
    errorToast.classList.add('show');
  }, 10);
  
  setTimeout(() => {
    errorToast.classList.remove('show');
    setTimeout(() => {
      errorToast.remove();
    }, 300);
  }, 4000);
}

/**
 * Show Success Toast
 */
function showSuccessToast(message) {
  if (successToast) {
    const messageEl = document.getElementById('toastMessage');
    if (messageEl) {
      messageEl.textContent = message;
    }
    
    successToast.classList.add('show');
    
    setTimeout(() => {
      successToast.classList.remove('show');
    }, 3600);
  }
}

/**
 * Initialize Helpful Buttons
 */
function initializeHelpfulButtons() {
  const helpfulBtns = document.querySelectorAll('.helpful-btn');
  
  helpfulBtns.forEach(btn => {
    btn.addEventListener('click', async function() {
      const ratingId = this.dataset.ratingId;
      
      if (!ratingId) {
        showError('Rating ID not found');
        return;
      }
      
      await markAsHelpful(ratingId, this);
    });
  });
}

/**
 * Mark Rating as Helpful
 */
async function markAsHelpful(ratingId, button) {
  // Disable button to prevent double-click
  button.disabled = true;
  const originalText = button.innerHTML;
  
  try {
    const response = await fetch(`${ROOT}/BrowseDramas/markHelpful/${ratingId}`);
    const data = await response.json();
    
    if (data.success) {
      // Update button appearance
      button.classList.add('marked');
      button.style.opacity = '0.6';
      showSuccessToast('Thank you! This rating has been marked as helpful');
    } else {
      showError(data.message || 'Failed to mark as helpful');
      button.disabled = false;
    }
  } catch (error) {
    console.error('Error marking as helpful:', error);
    showError('An error occurred');
    button.disabled = false;
  }
}

/**
 * Keyboard shortcuts
 */
document.addEventListener('keydown', function(e) {
  // Close modal with Escape key
  if (e.key === 'Escape' && ratingModal && ratingModal.classList.contains('active')) {
    closeRatingModalFunc();
  }
  
  // Quick rating with number keys (1-5)
  if (ratingModal && ratingModal.classList.contains('active') && e.key >= '1' && e.key <= '5') {
    selectRating(e.key);
  }
});

// Log that script is loaded
console.log('Drama Ratings JavaScript loaded successfully');
