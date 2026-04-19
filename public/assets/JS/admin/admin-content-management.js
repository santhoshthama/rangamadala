// ===================================
// CONTENT MANAGEMENT FUNCTIONALITY
// ===================================

let swiperSlidesData = [];

// Initialize content management
document.addEventListener('DOMContentLoaded', function() {
  initContentManagement();
});

function initContentManagement() {
  // Tab switching
  const contentTabs = document.querySelectorAll('.content-tab');
  contentTabs.forEach(tab => {
    tab.addEventListener('click', () => {
      contentTabs.forEach(t => t.classList.remove('active'));
      tab.classList.add('active');
      
      const tabName = tab.getAttribute('data-content-tab');
      document.querySelectorAll('.content-section').forEach(s => s.classList.remove('active'));
      document.getElementById(tabName + 'Section').classList.add('active');
      
      // Load content for the selected tab
      if (tabName === 'swiper') loadSwiperSlides();
      else if (tabName === 'gallery') loadGalleryImages();
      else if (tabName === 'testimonials') loadTestimonials();
    });
  });

  // Load content when Content nav is clicked
  const contentNav = document.querySelector('[data-view="content"]');
  if (contentNav) {
    contentNav.addEventListener('click', () => {
      loadSwiperSlides();
    });
  }
}

// ===================================
// SWIPER SLIDES MANAGEMENT
// ===================================

function loadSwiperSlides() {
  const grid = document.getElementById('swiperGrid');
  grid.classList.remove('is-empty');
  grid.innerHTML = `<div class="loading-state" id="swiperLoading">
    <span class="bx bx-loader-alt spinning"></span>
    <p>Loading slides...</p>
  </div>`;

  fetch(ROOT + '/admindashboard/getSwiperSlides')
    .then(response => response.json())
    .then(data => {
      if (data.error) {
        grid.innerHTML = '<p class="error-message">Failed to load slides</p>';
        return;
      }
      renderSwiperSlides(data);
    })
    .catch(error => {
      console.error('Error:', error);
      grid.innerHTML = '<p class="error-message">Failed to load slides</p>';
    });
}

function renderSwiperSlides(slides) {
  const grid = document.getElementById('swiperGrid');
  swiperSlidesData = Array.isArray(slides) ? slides : [];
  
  if (swiperSlidesData.length === 0) {
    grid.classList.add('is-empty');
    grid.innerHTML = `<div class="empty-state">
      <div class="empty-state-icon"><span class="bx bxs-carousel"></span></div>
      <h3 class="empty-state-title">No Slides Yet</h3>
      <p class="empty-state-description">Add drama slides to display on the home page swiper.</p>
    </div>`;
    return;
  }

  grid.classList.remove('is-empty');

  grid.innerHTML = swiperSlidesData.map(slide => `
    ${(() => {
      const isActive = slide.is_active == 1;
      const isDramaSubmission = !!slide.drama_id;
      const moderationLabel = isDramaSubmission
        ? (isActive ? 'Visible on Home' : 'Pending Admin Approval')
        : (isActive ? 'Active' : 'Hidden');
      const moderationBadgeClass = isActive ? 'success' : 'warning';
      const toggleTitle = isActive ? 'Hide from Home' : 'Show on Home';
      const cardTitle = slide.title || slide.linked_drama_name || 'Untitled Slide';
      const sourceLine = isDramaSubmission
        ? `<div class="content-card-subtitle">From drama: ${escapeHtml(slide.linked_drama_name || 'Artist submission')}</div>`
        : '';

      return `
    <div class="content-card ${slide.is_active == 1 ? '' : 'inactive'}" data-id="${slide.id}">
      <div class="content-card-image">
        <img src="${ROOT}/${slide.image_path}" alt="${slide.title || 'Slide'}">
        <div class="content-card-overlay">
          <button class="btn btn-sm" onclick="toggleStatus('swiper', ${slide.id}, ${slide.is_active == 1 ? 0 : 1})" title="${toggleTitle}">
            <span class="material-symbols-rounded">${isActive ? 'visibility_off' : 'visibility'}</span>
          </button>
          <button class="btn btn-sm" onclick="showEditSwiperModal(${slide.id})" title="Edit Slide">
            <span class="bx bx-edit"></span>
          </button>
          <button class="btn btn-sm btn-danger" onclick="deleteSwiper(${slide.id})" title="Delete">
            <span class="bx bx-trash"></span>
          </button>
        </div>
      </div>
      <div class="content-card-info">
        <h4>${escapeHtml(cardTitle)}</h4>
        ${sourceLine}
        <span class="status-badge ${moderationBadgeClass}">${moderationLabel}</span>
      </div>
    </div>
      `;
    })()}
  `).join('');
}

function showEditSwiperModal(slideId) {
  const slide = swiperSlidesData.find(item => Number(item.id) === Number(slideId));
  if (!slide) {
    toastError('Unable to load selected slide details.');
    return;
  }

  const isDramaLinked = !!slide.drama_id;
  const linkedDramaName = slide.linked_drama_name || '';
  const linkedDramaDescription = slide.linked_drama_description || '';
  const currentImagePath = slide.image_path || '';

  const modalHTML = `
    <div class="modal-overlay active" id="editSwiperModal">
      <div class="modal-content user-form-modal">
        <div class="modal-header">
          <h3>Edit Drama Slide</h3>
          <button class="modal-close" onclick="closeModal('editSwiperModal')">
            <span class="bx bx-x"></span>
          </button>
        </div>
        <div class="modal-body">
          <form id="editSwiperForm" enctype="multipart/form-data">
            <input type="hidden" id="editSwiperId" value="${slide.id}">

            <div class="input-box">
              <input type="text" id="editSwiperTitle" placeholder="Slide Title" value="${escapeHtml(slide.title || '')}" />
              <i class="bx bx-font"></i>
            </div>

            ${isDramaLinked ? `
            <div class="input-box">
              <input type="text" id="editSwiperDramaName" placeholder="Drama Name" value="${escapeHtml(linkedDramaName)}" />
              <i class="bx bx-theater"></i>
            </div>
            <div class="input-box">
              <textarea id="editSwiperDramaDescription" placeholder="Drama public description..." rows="3" style="padding-right:40px;">${escapeHtml(linkedDramaDescription)}</textarea>
              <i class="bx bx-description" style="top: 15px;"></i>
            </div>
            ` : ''}

            <div class="form-hint" style="margin-bottom: 10px; color: #8a6a1f;">
              ${isDramaLinked
                ? 'Upload a new poster to change both the linked drama poster and slide image.'
                : 'Upload a new image if you want to replace this slide image.'}
            </div>

            <div class="file-upload-box">
              <input type="file" id="editSwiperImage" accept="image/*" />
              <label for="editSwiperImage">
                <span class="bx bx-cloud-upload"></span>
                <span>Choose New Image (optional)</span>
              </label>
              <div class="file-preview" id="editSwiperPreview">
                ${currentImagePath ? `<img src="${ROOT}/${escapeHtml(currentImagePath)}" alt="Current slide image">` : ''}
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" onclick="closeModal('editSwiperModal')">Cancel</button>
          <button class="btn btn-primary" onclick="submitEditSwiper()">
            <span class="bx bx-save"></span>
            Save Changes
          </button>
        </div>
      </div>
    </div>
  `;

  document.body.insertAdjacentHTML('beforeend', modalHTML);

  const imageInput = document.getElementById('editSwiperImage');
  if (imageInput) {
    imageInput.addEventListener('change', function (e) {
      previewFile(e.target, 'editSwiperPreview');
    });
  }
}

function submitEditSwiper() {
  const slideId = document.getElementById('editSwiperId')?.value;
  const title = document.getElementById('editSwiperTitle')?.value || '';
  const dramaName = document.getElementById('editSwiperDramaName')?.value || '';
  const dramaDescription = document.getElementById('editSwiperDramaDescription')?.value || '';
  const imageInput = document.getElementById('editSwiperImage');

  if (!slideId) {
    toastError('Invalid slide selected.');
    return;
  }

  const formData = new FormData();
  formData.append('slide_id', slideId);
  formData.append('title', title);
  formData.append('drama_name', dramaName);
  formData.append('drama_description', dramaDescription);

  if (imageInput && imageInput.files && imageInput.files.length > 0) {
    formData.append('image', imageInput.files[0]);
  }

  fetch(ROOT + '/admindashboard/updateSwiperSlide', {
    method: 'POST',
    body: formData
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        closeModal('editSwiperModal');
        loadSwiperSlides();
        showToast(data.message || 'Slide updated successfully!', 'success');
      } else {
        toastError(data.message || 'Failed to update slide');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      toastError('An error occurred while updating the slide');
    });
}

function showAddSwiperModal() {
  const modalHTML = `
    <div class="modal-overlay active" id="addSwiperModal">
      <div class="modal-content user-form-modal">
        <div class="modal-header">
          <h3>Add Drama Slide</h3>
          <button class="modal-close" onclick="closeModal('addSwiperModal')">
            <span class="bx bx-x"></span>
          </button>
        </div>
        <div class="modal-body">
          <form id="addSwiperForm" enctype="multipart/form-data">
            <div class="input-box select-box">
              <select id="swiperDramaSelect">
                <option value="">Select published drama (optional)</option>
              </select>
              <i class="bx bx-theater"></i>
            </div>
            <div class="form-hint" style="margin-bottom: 12px; color: #8a6a1f;">Choose a published drama to use its poster image and title.</div>
            <div class="input-box">
              <input type="text" id="swiperTitle" placeholder="Slide Title (optional)" />
              <i class="bx bx-font"></i>
            </div>
            <div class="file-upload-box">
              <input type="file" id="swiperImage" accept="image/*" />
              <label for="swiperImage">
                <span class="bx bx-cloud-upload"></span>
                <span>Choose Image</span>
              </label>
              <div class="file-preview" id="swiperPreview"></div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" onclick="closeModal('addSwiperModal')">Cancel</button>
          <button class="btn btn-primary" onclick="submitAddSwiper()">
            <span class="bx bx-add"></span>
            Add Slide
          </button>
        </div>
      </div>
    </div>
  `;
  document.body.insertAdjacentHTML('beforeend', modalHTML);

  loadPublishedDramaOptions();
  
  // File preview handler
  document.getElementById('swiperImage').addEventListener('change', function(e) {
    previewFile(e.target, 'swiperPreview');
  });
}

function loadPublishedDramaOptions() {
  const select = document.getElementById('swiperDramaSelect');
  if (!select) {
    return;
  }

  fetch(ROOT + '/admindashboard/getPublishedDramasForSlides')
    .then(response => response.json())
    .then(data => {
      if (!Array.isArray(data)) {
        return;
      }

      const options = data.map(drama => {
        const alreadyAddedTag = drama.already_added ? ' (already in slides)' : '';
        return `<option value="${drama.id}">${escapeHtml(drama.drama_name)}${alreadyAddedTag}</option>`;
      }).join('');

      select.insertAdjacentHTML('beforeend', options);
    })
    .catch(error => {
      console.error('Failed to load published dramas:', error);
    });
}

function submitAddSwiper() {
  const title = document.getElementById('swiperTitle').value;
  const dramaSelect = document.getElementById('swiperDramaSelect');
  const selectedDramaId = dramaSelect ? dramaSelect.value : '';
  const imageInput = document.getElementById('swiperImage');
  
  if (!selectedDramaId && !imageInput.files.length) {
    alert('Please select a published drama or upload an image');
    return;
  }

  const formData = new FormData();
  formData.append('title', title);
  if (selectedDramaId) {
    formData.append('drama_id', selectedDramaId);
  }
  if (imageInput.files.length) {
    formData.append('image', imageInput.files[0]);
  }

  fetch(ROOT + '/admindashboard/addSwiperSlide', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      closeModal('addSwiperModal');
      loadSwiperSlides();
      showToast(data.message || 'Slide added successfully!', 'success');
    } else {
      alert(data.message || 'Failed to add slide');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('An error occurred');
  });
}

async function deleteSwiper(id) {
  const confirmed = await showConfirm(
    'Are you sure you want to delete this slide?',
    { title: 'Delete Drama Slide', confirmText: 'Delete Slide', type: 'warning' }
  );
  if (!confirmed) return;
  
  fetch(ROOT + '/admindashboard/deleteSwiperSlide', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id: id })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      loadSwiperSlides();
      showToast('Slide deleted!', 'success');
    } else {
      toastError(data.message || 'Failed to delete slide');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    toastError('An error occurred while deleting the slide');
  });
}

// ===================================
// GALLERY IMAGES MANAGEMENT
// ===================================

function loadGalleryImages() {
  const grid = document.getElementById('galleryGrid');
  grid.innerHTML = `<div class="loading-state">
    <span class="bx bx-loader-alt spinning"></span>
    <p>Loading images...</p>
  </div>`;

  fetch(ROOT + '/admindashboard/getGalleryImages')
    .then(response => response.json())
    .then(data => {
      if (data.error) {
        grid.innerHTML = '<p class="error-message">Failed to load images</p>';
        return;
      }
      renderGalleryImages(data);
    })
    .catch(error => {
      console.error('Error:', error);
      grid.innerHTML = '<p class="error-message">Failed to load images</p>';
    });
}

function renderGalleryImages(images) {
  const grid = document.getElementById('galleryGrid');
  
  if (images.length === 0) {
    grid.innerHTML = `<div class="empty-state">
      <div class="empty-state-icon"><span class="bx bx-photo-library"></span></div>
      <h3 class="empty-state-title">No Images Yet</h3>
      <p class="empty-state-description">Add stage highlight images for the gallery.</p>
    </div>`;
    return;
  }

  grid.innerHTML = images.map(image => `
    <div class="content-card ${image.is_active == 1 ? '' : 'inactive'}" data-id="${image.id}">
      <div class="content-card-image">
        <img src="${ROOT}/${image.image_path}" alt="${image.alt_text || 'Gallery'}">
        <div class="content-card-overlay">
          <button class="btn btn-sm" onclick="toggleStatus('gallery', ${image.id}, ${image.is_active == 1 ? 0 : 1})" title="${image.is_active == 1 ? 'Hide' : 'Show'}">
            <span class="material-symbols-rounded">${image.is_active == 1 ? 'visibility_off' : 'visibility'}</span>
          </button>
          <button class="btn btn-sm btn-danger" onclick="deleteGallery(${image.id})" title="Delete">
            <span class="bx bx-trash"></span>
          </button>
        </div>
      </div>
      <div class="content-card-info">
        <h4>${image.title || 'Untitled Image'}</h4>
        <span class="status-badge ${image.is_active == 1 ? 'success' : 'warning'}">${image.is_active == 1 ? 'Active' : 'Hidden'}</span>
      </div>
    </div>
  `).join('');
}

function showAddGalleryModal() {
  const modalHTML = `
    <div class="modal-overlay active" id="addGalleryModal">
      <div class="modal-content user-form-modal">
        <div class="modal-header">
          <h3>Add Gallery Image</h3>
          <button class="modal-close" onclick="closeModal('addGalleryModal')">
            <span class="bx bx-x"></span>
          </button>
        </div>
        <div class="modal-body">
          <form id="addGalleryForm" enctype="multipart/form-data">
            <div class="input-box">
              <input type="text" id="galleryTitle" placeholder="Image Title (optional)" />
              <i class="bx bx-font"></i>
            </div>
            <div class="file-upload-box">
              <input type="file" id="galleryImage" accept="image/*" required />
              <label for="galleryImage">
                <span class="bx bx-cloud-upload"></span>
                <span>Choose Image</span>
              </label>
              <div class="file-preview" id="galleryPreview"></div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" onclick="closeModal('addGalleryModal')">Cancel</button>
          <button class="btn btn-primary" onclick="submitAddGallery()">
            <span class="bx bx-add"></span>
            Add Image
          </button>
        </div>
      </div>
    </div>
  `;
  document.body.insertAdjacentHTML('beforeend', modalHTML);
  
  document.getElementById('galleryImage').addEventListener('change', function(e) {
    previewFile(e.target, 'galleryPreview');
  });
}

function submitAddGallery() {
  const title = document.getElementById('galleryTitle').value;
  const imageInput = document.getElementById('galleryImage');
  
  if (!imageInput.files.length) {
    alert('Please select an image');
    return;
  }

  const formData = new FormData();
  formData.append('title', title);
  formData.append('image', imageInput.files[0]);

  fetch(ROOT + '/admindashboard/addGalleryImage', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      closeModal('addGalleryModal');
      loadGalleryImages();
      showToast('Image added successfully!', 'success');
    } else {
      alert(data.message || 'Failed to add image');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('An error occurred');
  });
}

async function deleteGallery(id) {
  const confirmed = await showConfirm(
    'Are you sure you want to delete this stage highlight image?',
    { title: 'Delete Stage Highlight', confirmText: 'Delete Image', type: 'warning' }
  );
  if (!confirmed) return;
  
  fetch(ROOT + '/admindashboard/deleteGalleryImage', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id: id })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      loadGalleryImages();
      showToast('Image deleted!', 'success');
    } else {
      toastError(data.message || 'Failed to delete image');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    toastError('An error occurred while deleting the image');
  });
}

// ===================================
// TESTIMONIALS MANAGEMENT
// ===================================

function loadTestimonials() {
  const list = document.getElementById('testimonialsList');
  list.innerHTML = `<div class="loading-state">
    <span class="bx bx-loader-alt spinning"></span>
    <p>Loading testimonials...</p>
  </div>`;

  fetch(ROOT + '/admindashboard/getTestimonials')
    .then(response => response.json())
    .then(data => {
      if (data.error) {
        list.innerHTML = '<p class="error-message">Failed to load testimonials</p>';
        return;
      }
      renderTestimonials(data);
    })
    .catch(error => {
      console.error('Error:', error);
      list.innerHTML = '<p class="error-message">Failed to load testimonials</p>';
    });
}

function renderTestimonials(testimonials) {
  const list = document.getElementById('testimonialsList');
  
  if (testimonials.length === 0) {
    list.innerHTML = `<div class="empty-state">
      <div class="empty-state-icon"><span class="bx bx-reviews"></span></div>
      <h3 class="empty-state-title">No Testimonials Yet</h3>
      <p class="empty-state-description">Add testimonials from your community members.</p>
    </div>`;
    return;
  }

  list.innerHTML = testimonials.map(t => `
    <div class="testimonial-admin-card ${t.is_active == 1 ? '' : 'inactive'}" data-id="${t.id}">
      <div class="testimonial-admin-avatar">
        ${t.image_path ? `<img src="${t.image_path.startsWith('http') ? t.image_path : ROOT + '/' + t.image_path}" alt="${t.name}">` : `<span>${t.name.charAt(0)}</span>`}
      </div>
      <div class="testimonial-admin-content">
        <div class="testimonial-admin-header">
          <h4>${escapeHtml(t.name)} <span class="role-tag">${escapeHtml(t.role)}</span></h4>
          <div class="testimonial-rating">${'★'.repeat(t.rating)}${'☆'.repeat(5 - t.rating)}</div>
        </div>
        <p class="testimonial-message">"${escapeHtml(t.message)}"</p>
      </div>
      <div class="testimonial-admin-actions">
        <button class="btn btn-sm" onclick="toggleStatus('testimonial', ${t.id}, ${t.is_active == 1 ? 0 : 1})" title="${t.is_active == 1 ? 'Hide' : 'Show'}">
          <span class="material-symbols-rounded">${t.is_active == 1 ? 'visibility_off' : 'visibility'}</span>
        </button>
        <button class="btn btn-sm btn-danger" onclick="deleteTestimonial(${t.id})" title="Delete">
          <span class="bx bx-trash"></span>
        </button>
        <span class="status-badge ${t.is_active == 1 ? 'success' : 'warning'}">${t.is_active == 1 ? 'Active' : 'Hidden'}</span>
      </div>
    </div>
  `).join('');
}

function showAddTestimonialModal() {
  const modalHTML = `
    <div class="modal-overlay active" id="addTestimonialModal">
      <div class="modal-content user-form-modal">
        <div class="modal-header">
          <h3>Add Testimonial</h3>
          <button class="modal-close" onclick="closeModal('addTestimonialModal')">
            <span class="bx bx-x"></span>
          </button>
        </div>
        <div class="modal-body">
          <form id="addTestimonialForm" enctype="multipart/form-data">
            <div class="input-box">
              <input type="text" id="testimonialName" placeholder="Person's Name" required />
              <i class="bx bx-font"></i>
            </div>
            <div class="input-box select-box">
              <select id="testimonialRole" required>
                <option value="">Select Role</option>
                <option value="Artist">Artist</option>
                <option value="Director">Director</option>
                <option value="Audience">Audience</option>
                <option value="Service Provider">Service Provider</option>
              </select>
              <i class="bx bx-badge"></i>
            </div>
            <div class="input-box">
              <textarea id="testimonialMessage" placeholder="Testimonial message..." rows="3" required style="padding-right: 40px;"></textarea>
              <i class="bx bx-chat" style="top: 15px;"></i>
            </div>
            <div class="input-box">
              <label style="display: block; margin-bottom: 8px; color: var(--text-color);">Rating</label>
              <div class="star-rating" id="starRating">
                <span class="star" data-value="1">★</span>
                <span class="star" data-value="2">★</span>
                <span class="star" data-value="3">★</span>
                <span class="star" data-value="4">★</span>
                <span class="star active" data-value="5">★</span>
              </div>
              <input type="hidden" id="testimonialRating" value="5" />
            </div>
            <div class="file-upload-box">
              <input type="file" id="testimonialImage" accept="image/*" />
              <label for="testimonialImage">
                <span class="bx bx-cloud-upload"></span>
                <span>Choose Photo (optional)</span>
              </label>
              <div class="file-preview" id="testimonialPreview"></div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" onclick="closeModal('addTestimonialModal')">Cancel</button>
          <button class="btn btn-primary" onclick="submitAddTestimonial()">
            <span class="bx bx-add"></span>
            Add Testimonial
          </button>
        </div>
      </div>
    </div>
  `;
  document.body.insertAdjacentHTML('beforeend', modalHTML);
  
  // Star rating handler
  document.querySelectorAll('#starRating .star').forEach(star => {
    star.addEventListener('click', function() {
      const value = this.getAttribute('data-value');
      document.getElementById('testimonialRating').value = value;
      document.querySelectorAll('#starRating .star').forEach((s, i) => {
        s.classList.toggle('active', i < value);
      });
    });
  });
  
  document.getElementById('testimonialImage').addEventListener('change', function(e) {
    previewFile(e.target, 'testimonialPreview');
  });
}

function submitAddTestimonial() {
  const name = document.getElementById('testimonialName').value.trim();
  const role = document.getElementById('testimonialRole').value;
  const message = document.getElementById('testimonialMessage').value.trim();
  const rating = document.getElementById('testimonialRating').value;
  const imageInput = document.getElementById('testimonialImage');

  if (!name || !role || !message) {
    alert('Please fill in all required fields');
    return;
  }

  const formData = new FormData();
  formData.append('name', name);
  formData.append('role', role);
  formData.append('message', message);
  formData.append('rating', rating);
  if (imageInput.files.length) {
    formData.append('image', imageInput.files[0]);
  }

  fetch(ROOT + '/admindashboard/addTestimonial', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      closeModal('addTestimonialModal');
      loadTestimonials();
      showToast('Testimonial added successfully!', 'success');
    } else {
      alert(data.message || 'Failed to add testimonial');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('An error occurred');
  });
}

async function deleteTestimonial(id) {
  const confirmed = await showConfirm(
    'Are you sure you want to delete this testimonial?',
    { title: 'Delete Testimonial', confirmText: 'Delete Testimonial', type: 'warning' }
  );
  if (!confirmed) return;
  
  fetch(ROOT + '/admindashboard/deleteTestimonial', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id: id })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      loadTestimonials();
      showToast('Testimonial deleted!', 'success');
    } else {
      toastError(data.message || 'Failed to delete testimonial');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    toastError('An error occurred while deleting the testimonial');
  });
}

// ===================================
// UTILITY FUNCTIONS
// ===================================

function toggleStatus(type, id, isActive) {
  fetch(ROOT + '/admindashboard/toggleContentStatus', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ type: type, id: id, is_active: isActive })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      if (type === 'swiper') loadSwiperSlides();
      else if (type === 'gallery') loadGalleryImages();
      else if (type === 'testimonial') loadTestimonials();
      if (type === 'swiper') {
        showToast(isActive == 1 ? 'Slide is now visible on home page.' : 'Slide hidden from home page.', 'success');
      } else {
        showToast('Status updated!', 'success');
      }
    } else {
      alert(data.message || 'Failed to update');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('An error occurred');
  });
}

function closeModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) modal.remove();
}

function previewFile(input, previewId) {
  const preview = document.getElementById(previewId);
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = function(e) {
      preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
    };
    reader.readAsDataURL(input.files[0]);
  }
}

function showToast(message, type = 'success') {
  const existing = document.querySelector('.admin-toast');
  if (existing) existing.remove();

  const toast = document.createElement('div');
  toast.className = `admin-toast ${type}`;
  toast.innerHTML = `
    <span class="bx ${type === 'success' ? 'bx-check-circle' : 'bx-error'}"></span>
    <span>${message}</span>
  `;
  document.body.appendChild(toast);

  setTimeout(() => {
    toast.classList.add('fade-out');
    setTimeout(() => toast.remove(), 300);
  }, 3000);
}

function escapeHtml(text) {
  if (!text) return '';
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

// Make functions global
window.loadSwiperSlides = loadSwiperSlides;
window.loadGalleryImages = loadGalleryImages;
window.loadTestimonials = loadTestimonials;
window.showAddSwiperModal = showAddSwiperModal;
window.showEditSwiperModal = showEditSwiperModal;
window.showAddGalleryModal = showAddGalleryModal;
window.showAddTestimonialModal = showAddTestimonialModal;
window.deleteSwiper = deleteSwiper;
window.deleteGallery = deleteGallery;
window.deleteTestimonial = deleteTestimonial;
window.toggleStatus = toggleStatus;
window.closeModal = closeModal;
