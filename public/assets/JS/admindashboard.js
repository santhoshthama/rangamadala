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
let userTrendChartInstance = null;
let roleDistributionChartInstance = null;
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
  loadOverviewStats();
  loadOverviewDramaDetails();
  initCharts();
  initRegistrationsView();
  initAdminProfile();
  initOverviewDramaActions();
});

function loadOverviewStats() {
  fetch(ROOT + "/admindashboard/getOverviewStats")
    .then((response) => response.json())
    .then((data) => {
      if (!data.success || !data.stats) {
        return;
      }

      const stats = data.stats;
      const totalUsersEl = document.getElementById("statTotalUsers");
      const activeDramasEl = document.getElementById("statActiveDramas");
      const pendingUsersEl = document.getElementById("statPendingUserApprovals");
      const pendingDramaEl = document.getElementById("statPendingDramaApprovals");

      if (totalUsersEl) totalUsersEl.textContent = Number(stats.total_users || 0).toLocaleString();
      if (activeDramasEl) activeDramasEl.textContent = Number(stats.active_dramas || 0).toLocaleString();
      if (pendingUsersEl) pendingUsersEl.textContent = Number(stats.pending_user_approvals || 0).toLocaleString();
      if (pendingDramaEl) pendingDramaEl.textContent = Number(stats.pending_drama_approvals || 0).toLocaleString();
    })
    .catch((error) => {
      console.error("Error loading overview stats:", error);
    });
}

function initOverviewDramaActions() {
  const reviewBtn = document.getElementById("overviewDramaApprovalsBtn");
  if (!reviewBtn) return;

  reviewBtn.addEventListener("click", (e) => {
    e.preventDefault();
    switchView("drama-approvals");
  });
}

function escapeHtml(value) {
  return String(value ?? "")
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/\"/g, "&quot;")
    .replace(/'/g, "&#39;");
}

function formatDramaDate(value) {
  if (!value) return "N/A";
  const dt = new Date(value);
  if (Number.isNaN(dt.getTime())) return "N/A";

  return dt.toLocaleDateString("en-US", {
    month: "short",
    day: "numeric",
    year: "numeric",
  });
}

function getDramaStageBadge(stage) {
  switch (stage) {
    case "published":
      return '<span class="status-badge success">Published</span>';
    case "pending_approval":
      return '<span class="status-badge warning">Pending Approval</span>';
    default:
      return '<span class="status-badge info">In Progress</span>';
  }
}

function loadOverviewDramaDetails() {
  const tableBody = document.getElementById("overviewDramaTableBody");
  if (!tableBody) return;

  tableBody.innerHTML = '<tr><td colspan="5">Loading drama insights...</td></tr>';

  fetch(ROOT + "/admindashboard/getOverviewDramaDetails")
    .then((response) => response.json())
    .then((data) => {
      if (!data.success) {
        tableBody.innerHTML = '<tr><td colspan="6">Unable to load drama insights.</td></tr>';
        return;
      }

      const summary = data.summary || {};
      const pendingEl = document.getElementById("overviewDramaPending");
      const inProgressEl = document.getElementById("overviewDramaInProgress");
      const publishedEl = document.getElementById("overviewDramaPublished");
      const updatedEl = document.getElementById("overviewDramaUpdatedRecently");

      if (pendingEl) pendingEl.textContent = Number(summary.pending_approval || 0).toLocaleString();
      if (inProgressEl) inProgressEl.textContent = Number(summary.in_progress || 0).toLocaleString();
      if (publishedEl) publishedEl.textContent = Number(summary.published || 0).toLocaleString();
      if (updatedEl) updatedEl.textContent = Number(summary.updated_recently || 0).toLocaleString();

      const rows = Array.isArray(data.items) ? data.items : [];
      if (rows.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="6">No drama insights available yet.</td></tr>';
        return;
      }

      tableBody.innerHTML = "";
      rows.forEach((item) => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
          <td>
            <div class="project-title-cell">
              <div class="project-icon">
                <span class="bx bx-movie-play"></span>
              </div>
              <div class="project-info">
                <div class="project-title-text">${escapeHtml(item.drama_name || "Untitled drama")}</div>
              </div>
            </div>
          </td>
          <td>${getDramaStageBadge(item.stage)}</td>
          <td>${escapeHtml(item.producer_name || "Not specified")}</td>
          <td>${escapeHtml(item.producer_contact || "N/A")}</td>
          <td>${escapeHtml(formatDramaDate(item.activity_at))}</td>
          <td>${escapeHtml(item.insight || "No additional insight available.")}</td>
        `;
        tableBody.appendChild(tr);
      });
    })
    .catch((error) => {
      console.error("Error loading overview drama details:", error);
      tableBody.innerHTML = '<tr><td colspan="6">Unable to load drama insights right now.</td></tr>';
    });
}
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
    if (viewId === "overview") {
      loadOverviewStats();
      loadOverviewDramaDetails();
    }
  }
  // Close sidebar on mobile after navigation
  if (window.innerWidth <= 1024) closeSidebar();
}
function updatePageTitle(viewId) {
  const titles = {
    overview: "Overview",
    users: "User Management",
    registrations: "Registrations",
    "drama-approvals": "Drama Approvals",
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
  fetch(ROOT + "/admindashboard/getOverviewChartData")
    .then((response) => response.json())
    .then((data) => {
      if (!data.success || !data.charts) {
        return;
      }

      const trend = data.charts.registration_trend || { labels: [], values: [] };
      const role = data.charts.role_distribution || { labels: [], values: [] };

      renderUserTrendChart(trend.labels, trend.values);
      renderRoleDistributionChart(role.labels, role.values);
    })
    .catch((error) => {
      console.error("Error loading chart data:", error);
      renderUserTrendChart([], []);
      renderRoleDistributionChart([], []);
    });
}

function renderUserTrendChart(labels, values) {
  const ctx = document.getElementById("userTrendChart");
  if (!ctx) return;

  if (userTrendChartInstance) {
    userTrendChartInstance.destroy();
  }

  userTrendChartInstance = new Chart(ctx, {
    type: "line",
    data: {
      labels,
      datasets: [
        {
          label: "New Registrations",
          data: values,
          borderColor: "#ba8e23",
          backgroundColor: "rgba(186, 142, 35, 0.15)",
          borderWidth: 3,
          fill: true,
          tension: 0.35,
          pointRadius: 4,
          pointHoverRadius: 6,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            precision: 0,
          },
        },
      },
    },
  });
}

function renderRoleDistributionChart(labels, values) {
  const ctx = document.getElementById("roleDistributionChart");
  if (!ctx) return;

  if (roleDistributionChartInstance) {
    roleDistributionChartInstance.destroy();
  }

  roleDistributionChartInstance = new Chart(ctx, {
    type: "doughnut",
    data: {
      labels,
      datasets: [
        {
          data: values,
          backgroundColor: ["#ba8e23", "#10b981", "#3b82f6"],
          borderWidth: 0,
          hoverOffset: 8,
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
            padding: 16,
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
window.loadOverviewStats = loadOverviewStats;