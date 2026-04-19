<div class="dashboard-view" id="users">
  <div class="dashboard-table-container">
    <div class="dashboard-table-header">
      <h2 class="dashboard-table-title">User Management</h2>
      <div class="header-actions">
        <div class="filter-buttons">
          <button class="btn btn-secondary filter-btn active" data-filter="all" data-target="users">All</button>
          <button class="btn btn-secondary filter-btn" data-filter="artist" data-target="users">Artists</button>
          <button class="btn btn-secondary filter-btn" data-filter="audience" data-target="users">Audience</button>
          <button class="btn btn-secondary filter-btn" data-filter="service_provider" data-target="users">Service Providers</button>
        </div>
        <button class="btn btn-primary" onclick="showAddUserModal()">
          <span class="material-symbols-rounded">add</span>
          Add New User
        </button>
      </div>
    </div>

    <div id="usersTableContainer">
      <!-- Loading state -->
      <div class="loading-state" id="usersLoading">
        <span class="material-symbols-rounded spinning">progress_activity</span>
        <p>Loading users...</p>
      </div>

      <!-- Empty state -->
      <div class="empty-state" id="usersEmpty" style="display: none;">
        <div class="empty-state-icon">
          <span class="bx bx-user"></span>
        </div>
        <h3 class="empty-state-title">No Users Found</h3>
        <p class="empty-state-description">There are no users in the system yet. Add a new user to get started.</p>
        <button class="btn btn-primary" style="margin-top: 20px;" onclick="showAddUserModal()">
          <span class="bx bx-plus"></span>
          Add New User
        </button>
      </div>

      <!-- Users table -->
      <table class="dashboard-table" id="usersTable" style="display: none;">
        <thead>
          <tr>
            <th>User Details</th>
            <th>Role</th>
            <th>Contact</th>
            <th>NIC Number</th>
            <th>Status</th>
            <th>Joined Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="usersTableBody">
          <!-- Data will be loaded dynamically -->
        </tbody>
      </table>
    </div>
  </div>
</div>
