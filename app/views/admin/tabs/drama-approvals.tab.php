<div class="dashboard-view" id="drama-approvals">
  <div class="dashboard-table-container">
    <div class="dashboard-table-header">
      <h2 class="dashboard-table-title">Pending Drama Creation Requests</h2>
    </div>

    <div class="loading-state" id="dramaRequestsLoading">
      <span class="bx bx-loader-circle"></span>
      <p>Loading drama requests...</p>
    </div>

    <div class="empty-state" id="dramaRequestsEmpty" style="display: none;">
      <div class="empty-state-icon">
        <span class="bx bx-task"></span>
      </div>
      <h3 class="empty-state-title">No Pending Drama Requests</h3>
      <p class="empty-state-description">All drama creation requests have been processed.</p>
    </div>

    <table class="dashboard-table" id="dramaRequestsTable" style="display: none;">
      <thead>
        <tr>
          <th>Drama</th>
          <th>Artist</th>
          <th>Certificate No.</th>
          <th>Requested Date</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="dramaRequestsTableBody"></tbody>
    </table>
  </div>
</div>
