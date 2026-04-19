<div class="dashboard-view" id="content">
  <!-- Content Management Tabs -->
  <div class="content-tabs">
    <button class="content-tab active" data-content-tab="swiper">
      <span class="bx bx-mask"></span>
      Drama Slides
    </button>
    <button class="content-tab" data-content-tab="gallery">
      <span class="bx bx-image"></span>
      Stage Highlights
    </button>
    <button class="content-tab" data-content-tab="testimonials">
      <span class="bx bx-comment"></span>
      Testimonials
    </button>
  </div>

  <!-- Swiper/Drama Slides Section -->
  <div class="content-section active" id="swiperSection">
    <div class="dashboard-table-header">
      <h3 class="dashboard-table-title">Drama Slides (Swiper)</h3>
      <button class="btn btn-primary" onclick="showAddSwiperModal()">
        <span class="bx bx-mask"></span>
        Add Slide
      </button>
    </div>
    <div class="content-grid" id="swiperGrid">
      <div class="loading-state" id="swiperLoading">
        <span class="bx bx-loader-circle"></span>
        <p>Loading slides...</p>
      </div>
    </div>
  </div>

  <!-- Gallery Section -->
  <div class="content-section" id="gallerySection">
    <div class="dashboard-table-header">
      <h3 class="dashboard-table-title">Stage Highlights (Gallery)</h3>
      <button class="btn btn-primary" onclick="showAddGalleryModal()">
        <span class="bx bx-image"></span>
        Add Image
      </button>
    </div>
    <div class="content-grid" id="galleryGrid">
      <!-- Content loads when tab is clicked -->
    </div>
  </div>

  <!-- Testimonials Section -->
  <div class="content-section" id="testimonialsSection">
    <div class="dashboard-table-header">
      <h3 class="dashboard-table-title">Testimonials</h3>
      <button class="btn btn-primary" onclick="showAddTestimonialModal()">
        <span class="bx bx-comment"></span>
        Add Testimonial
      </button>
    </div>
    <div class="testimonials-list" id="testimonialsList">
      <!-- Content loads when tab is clicked -->
    </div>
  </div>
</div>
