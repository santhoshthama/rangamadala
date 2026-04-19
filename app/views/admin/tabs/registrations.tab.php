<div class="dashboard-view" id="registrations">
  <div class="dashboard-table-container">
    <div class="dashboard-table-header">
      <h2 class="dashboard-table-title">Pending Registrations</h2>
      <div class="filter-buttons">
        <button class="btn btn-secondary filter-btn active" data-filter="all">All</button>
        <button class="btn btn-secondary filter-btn" data-filter="artist">Artists</button>
        <button class="btn btn-secondary filter-btn" data-filter="service_provider">Service Providers</button>
      </div>
    </div>

    <div id="registrationsTableContainer">
      <!-- Loading state -->
      <div class="loading-state" id="registrationsLoading">
        <span class="material-symbols-rounded spinning">progress_activity</span>
        <p>Loading registrations...</p>
      </div>

      <!-- Empty state -->
      <div class="empty-state" id="registrationsEmpty" style="display: none;">
        <div class="empty-state-icon">
          <span class="bx bx-task"></span>
        </div>
        <h3 class="empty-state-title">No Pending Registrations</h3>
        <p class="empty-state-description">All registration requests have been processed.</p>
      </div>

      <!-- Registrations table -->
      <table class="dashboard-table" id="registrationsTable" style="display: none;">
        <thead>
          <tr>
            <th>User Details</th>
            <th>Role</th>
            <th>Contact</th>
            <th>Registration Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="registrationsTableBody">
          <!-- Data will be loaded dynamically -->
        </tbody>
      </table>
    </div>
  </div>
</div>
