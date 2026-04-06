// DOM Elements
const dashboardSidebar = document.getElementById("dashboardSidebar");
const userMenu = document.getElementById("userMenu");
const userMenuTrigger = document.getElementById("user-menu-trigger");
const userMenuDropdown = document.querySelector(".user-menu-dropdown");
const themeToggle = document.getElementById("theme-toggle");
const dashboardViews = document.querySelectorAll(".dashboard-view");
const dashboardNavItems = document.querySelectorAll(".dashboard-nav-item");
const dashboardTitle = document.getElementById("dashboardTitle");
const dashboardSidebarOverlay = document.getElementById("dashboardSidebarOverlay");
const searchContainer = document.getElementById("searchContainer");
const searchInput = document.getElementById("searchInput");
const searchClose = document.getElementById("searchClose");
const mobileSearchBtn = document.getElementById("mobileSearchBtn");
const adminProfileMenuItem = document.getElementById("adminProfileMenuItem");

// State
let sidebarCollapsed = false;
let currentView = "overview";
let pendingRegistrations = [];
// ===================================
// INITIALIZATION
// ===================================
document.addEventListener("DOMContentLoaded", function () {
  initTheme();
  initThemeToggle();
  initSidebar();
  initUserMenu();
  initNavigation();
  initSearch();
  initCharts();
  initRegistrationsView();
  initAdminProfile();
});
// ===================================
// SIDEBAR FUNCTIONALITY
// ===================================
function initSidebar() {
  // Load saved sidebar state
  sidebarCollapsed = localStorage.getItem("dashboard-sidebar-collapsed") === "true";
  dashboardSidebar.classList.toggle("collapsed", sidebarCollapsed);
  // Sidebar toggle functionality
  document.querySelectorAll(".dashboard-sidebar-toggle").forEach((toggle) => {
    toggle.addEventListener("click", toggleSidebar);
  });
  // Sidebar overlay functionality
  dashboardSidebarOverlay?.addEventListener("click", closeSidebar);
}
function toggleSidebar() {
  sidebarCollapsed = !sidebarCollapsed;
  const isMobile = window.innerWidth <= 1024;
  if (isMobile) {
    // Mobile behavior - toggle sidebar and overlay together
    const isOpen = dashboardSidebar.classList.contains("collapsed");
    dashboardSidebar.classList.toggle("collapsed", !isOpen);
    dashboardSidebarOverlay?.classList.toggle("active", !isOpen);
  } else {
    // Desktop behavior
    dashboardSidebar.classList.toggle("collapsed", sidebarCollapsed);
  }
  localStorage.setItem("dashboard-sidebar-collapsed", sidebarCollapsed.toString());
}
function closeSidebar() {
  if (window.innerWidth <= 1024) {
    dashboardSidebar.classList.remove("collapsed");
    dashboardSidebarOverlay?.classList.remove("active");
  }
}
// ===================================
// USER MENU FUNCTIONALITY
// ===================================
function initUserMenu() {
  if (!userMenuTrigger || !userMenu) return;
  userMenuTrigger.addEventListener("click", (e) => {
    e.stopPropagation();
    userMenu.classList.toggle("active");
  });
  // Close menu when clicking outside or pressing escape
  document.addEventListener("click", (e) => {
    if (!userMenu.contains(e.target)) {
      userMenu.classList.remove("active");
    }
  });
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && userMenu.classList.contains("active")) {
      userMenu.classList.remove("active");
    }
  });
}
// ===================================
// NAVIGATION FUNCTIONALITY
// ===================================
function initNavigation() {
  dashboardNavItems.forEach((item) => {
    item.addEventListener("click", (e) => {
      e.preventDefault();
      const viewId = item.getAttribute("data-view");
      if (viewId) switchView(viewId);
    });
  });
}
function switchView(viewId) {
  // Update active nav item
  dashboardNavItems.forEach((item) => {
    item.classList.toggle("active", item.getAttribute("data-view") === viewId);
  });
  // Hide all views and show selected one
  dashboardViews.forEach((view) => view.classList.remove("active"));
  const targetView = document.getElementById(viewId);
  if (targetView) {
    targetView.classList.add("active");
    currentView = viewId;
    updatePageTitle(viewId);
  }
  // Close sidebar on mobile after navigation
  if (window.innerWidth <= 1024) closeSidebar();
}
function updatePageTitle(viewId) {
  const titles = {
    overview: "Overview",
    users: "User Management",
    registrations: "Registrations",
    permissions: "Permissions",
    content: "Content",
  };
  if (dashboardTitle) {
    dashboardTitle.textContent = titles[viewId] || "Dashboard";
  }
}

// ===================================
// ADMIN PROFILE FUNCTIONALITY
// ===================================
function initAdminProfile() {
  if (!adminProfileMenuItem) return;

  adminProfileMenuItem.addEventListener("click", (e) => {
    e.preventDefault();
    openAdminProfileModal();
    userMenu?.classList.remove("active");
  });
}

function openAdminProfileModal() {
  const existingModal = document.getElementById("adminProfileModal");
  if (existingModal) {
    existingModal.remove();
  }

  const modalHtml = `
    <div class="modal-overlay active" id="adminProfileModal">
      <div class="modal-content user-form-modal">
        <div class="modal-header">
          <h3>Edit Profile</h3>
          <button class="modal-close" onclick="closeAdminProfileModal()">
            <span class="material-symbols-rounded">close</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="adminProfileForm">
            <div class="input-box">
              <input type="text" id="adminProfileName" placeholder="Full Name" required />
              <i class="material-symbols-rounded">person</i>
            </div>
            <div class="input-box">
              <input type="email" id="adminProfileEmail" placeholder="Email Address" required />
              <i class="material-symbols-rounded">mail</i>
            </div>
            <div class="input-box">
              <input type="tel" id="adminProfilePhone" placeholder="Phone Number" />
              <i class="material-symbols-rounded">phone</i>
            </div>
            <div class="input-box">
              <input type="password" id="adminProfileNewPassword" placeholder="New Password (optional)" minlength="6" />
              <i class="material-symbols-rounded">lock</i>
            </div>
            <div class="input-box">
              <input type="password" id="adminProfileConfirmPassword" placeholder="Confirm New Password" minlength="6" />
              <i class="material-symbols-rounded">lock_reset</i>
            </div>
            <p class="form-note">
              <span class="material-symbols-rounded">info</span>
              Leave password fields empty if you do not want to change the password.
            </p>
          </form>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" onclick="closeAdminProfileModal()">Cancel</button>
          <button class="btn btn-primary" onclick="submitAdminProfile()">
            <span class="material-symbols-rounded">save</span>
            Save Profile
          </button>
        </div>
      </div>
    </div>
  `;

  document.body.insertAdjacentHTML("beforeend", modalHtml);
  loadAdminProfile();
}

function closeAdminProfileModal() {
  const modal = document.getElementById("adminProfileModal");
  if (modal) {
    modal.remove();
  }
}

function loadAdminProfile() {
  fetch(ROOT + "/Admindashboard/getAdminProfile")
    .then((response) => response.json())
    .then((data) => {
      if (!data.success || !data.admin) {
        if (typeof toastError === "function") {
          toastError(data.message || "Failed to load profile");
        }
        return;
      }

      document.getElementById("adminProfileName").value = data.admin.full_name || "";
      document.getElementById("adminProfileEmail").value = data.admin.email || "";
      document.getElementById("adminProfilePhone").value = data.admin.phone || "";
    })
    .catch((error) => {
      console.error("Error loading admin profile:", error);
      if (typeof toastError === "function") {
        toastError("An error occurred while loading profile details");
      }
    });
}

function submitAdminProfile() {
  const fullName = document.getElementById("adminProfileName").value.trim();
  const email = document.getElementById("adminProfileEmail").value.trim();
  const phone = document.getElementById("adminProfilePhone").value.trim();
  const newPassword = document.getElementById("adminProfileNewPassword").value;
  const confirmPassword = document.getElementById("adminProfileConfirmPassword").value;

  if (!fullName || !email) {
    if (typeof toastError === "function") {
      toastError("Full name and email are required");
    }
    return;
  }

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) {
    if (typeof toastError === "function") {
      toastError("Please enter a valid email address");
    }
    return;
  }

  if (newPassword || confirmPassword) {
    if (newPassword.length < 6) {
      if (typeof toastError === "function") {
        toastError("New password must be at least 6 characters");
      }
      return;
    }

    if (newPassword !== confirmPassword) {
      if (typeof toastError === "function") {
        toastError("New password and confirm password do not match");
      }
      return;
    }
  }

  fetch(ROOT + "/Admindashboard/updateAdminProfile", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      full_name: fullName,
      email,
      phone,
      new_password: newPassword,
      confirm_password: confirmPassword,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (!data.success) {
        if (typeof toastError === "function") {
          toastError(data.message || "Failed to update profile");
        }
        return;
      }

      const avatarText = document.querySelector(".user-avatar-small span");
      if (avatarText) {
        avatarText.textContent = (fullName.charAt(0) || "A").toUpperCase();
      }

      if (typeof toastSuccess === "function") {
        toastSuccess(data.message || "Profile saved successfully");
      }
      closeAdminProfileModal();
    })
    .catch((error) => {
      console.error("Error updating admin profile:", error);
      if (typeof toastError === "function") {
        toastError("An error occurred while updating profile");
      }
    });
}
// ===================================
// THEME FUNCTIONALITY
// ===================================
function initTheme() {
  // Load saved theme
  const savedTheme = localStorage.getItem("dashboard-theme") || "light";
  document.documentElement.setAttribute("data-theme", savedTheme);
  // Update theme toggle UI
  updateThemeToggleUI(savedTheme);
}
function initThemeToggle() {
  if (!themeToggle) return;
  themeToggle.querySelectorAll(".theme-option").forEach((option) => {
    option.addEventListener("click", (e) => {
      e.stopPropagation();
      setTheme(option.getAttribute("data-theme"));
    });
  });
}
function setTheme(theme) {
  document.documentElement.setAttribute("data-theme", theme);
  localStorage.setItem("dashboard-theme", theme);
  updateThemeToggleUI(theme);
}
function updateThemeToggleUI(theme) {
  if (!themeToggle) return;
  themeToggle.querySelectorAll(".theme-option").forEach((option) => {
    option.classList.toggle("active", option.getAttribute("data-theme") === theme);
  });
}
// ===================================
// SEARCH FUNCTIONALITY
// ===================================
function initSearch() {
  mobileSearchBtn?.addEventListener("click", () => {
    searchContainer.classList.add("mobile-active");
    searchInput.focus();
  });
  searchClose?.addEventListener("click", () => {
    searchContainer.classList.remove("mobile-active");
    searchInput.value = "";
  });
}
// ===================================
// CHART INITIALIZATION
// ===================================
function initCharts() {
  initProgressChart();
  initCategoryChart();
}
function initProgressChart() {
  const ctx = document.getElementById("progressChart");
  if (!ctx) return;
  new Chart(ctx, {
    type: "line",
    data: {
      labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
      datasets: [
        {
          label: "Project Progress",
          data: [20, 35, 45, 60, 70, 85],
          borderColor: "#8b5cf6",
          backgroundColor: "rgba(139, 92, 246, 0.1)",
          borderWidth: 2,
          fill: true,
          tension: 0.4,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        y: {
          beginAtZero: true,
          max: 100,
          ticks: { callback: (value) => value + "%" },
        },
      },
    },
  });
}
function initCategoryChart() {
  const ctx = document.getElementById("categoryChart");
  if (!ctx) return;
  new Chart(ctx, {
    type: "doughnut",
    data: {
      labels: ["Frontend", "Backend", "Mobile", "DevOps"],
      datasets: [
        {
          data: [35, 25, 20, 20],
          backgroundColor: ["#8b5cf6", "#10b981", "#f59e0b", "#ef4444"],
          borderWidth: 0,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: "bottom",
          labels: {
            padding: 20,
            usePointStyle: true,
          },
        },
      },
    },
  });
}

window.openAdminProfileModal = openAdminProfileModal;
window.closeAdminProfileModal = closeAdminProfileModal;
window.submitAdminProfile = submitAdminProfile;