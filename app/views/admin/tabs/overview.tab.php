<div class="dashboard-view active" id="overview">
  <!-- Stats Cards -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-card-header">
        <div class="stat-card-title">Total Users</div>
        <div class="stat-card-icon primary">
          <span class="bx bx-user"></span>
        </div>
      </div>
      <div class="stat-card-value" id="statTotalUsers">0</div>
      <div class="stat-card-change">
        <span class="bx bx-group"></span>
        <span>Registered non-admin users</span>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-card-header">
        <div class="stat-card-title">Active Dramas</div>
        <div class="stat-card-icon success">
          <span class="material-symbols-rounded">theater_comedy</span>
        </div>
      </div>
      <div class="stat-card-value" id="statActiveDramas">0</div>
      <div class="stat-card-change">
        <span class="material-symbols-rounded">event_available</span>
        <span>Currently active drama records</span>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-card-header">
        <div class="stat-card-title">Pending User Approvals</div>
        <div class="stat-card-icon warning">
          <span class="material-symbols-rounded">pending_actions</span>
        </div>
      </div>
      <div class="stat-card-value" id="statPendingUserApprovals">0</div>
      <div class="stat-card-change negative">
        <span class="material-symbols-rounded">schedule</span>
        <span>Awaiting approval</span>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-card-header">
        <div class="stat-card-title">Pending Drama Approvals</div>
        <div class="stat-card-icon info">
          <span class="bx bx-check-circle"></span>
        </div>
      </div>
      <div class="stat-card-value" id="statPendingDramaApprovals">0</div>
      <div class="stat-card-change negative">
        <span class="bx bx-hourglass"></span>
        <span>Waiting for admin review</span>
      </div>
    </div>
  </div>
  <!-- Charts -->
  <div class="chart-grid">
    <div class="chart-card">
      <div class="chart-card-header">
        <h3 class="chart-card-title">User Registration Trend</h3>
        <p class="chart-card-subtitle">New users over time</p>
      </div>
      <div class="chart-container">
        <canvas id="userTrendChart"></canvas>
      </div>
    </div>
    <div class="chart-card">
      <div class="chart-card-header">
        <h3 class="chart-card-title">User Distribution by Role</h3>
        <p class="chart-card-subtitle">Distribution across roles</p>
      </div>
      <div class="chart-container">
        <canvas id="roleDistributionChart"></canvas>
      </div>
    </div>
  </div>
  <!-- Drama Insights -->
  <div class="dashboard-table-container overview-drama-grid-section">
    <div class="dashboard-table-header">
      <h3 class="dashboard-table-title">Drama Pipeline & Ongoing Insights</h3>
      <a href="#" class="btn btn-primary" id="overviewDramaApprovalsBtn">Review Drama Approvals</a>
    </div>
    <div class="overview-drama-summary" id="overviewDramaSummary">
      <div class="overview-drama-summary-item">
        <span class="label">Pending Approval</span>
        <strong id="overviewDramaPending">0</strong>
      </div>
      <div class="overview-drama-summary-item">
        <span class="label">In Progress</span>
        <strong id="overviewDramaInProgress">0</strong>
      </div>
      <div class="overview-drama-summary-item">
        <span class="label">Published</span>
        <strong id="overviewDramaPublished">0</strong>
      </div>
      <div class="overview-drama-summary-item">
        <span class="label">Updated Last 14 Days</span>
        <strong id="overviewDramaUpdatedRecently">0</strong>
      </div>
    </div>
    <table class="dashboard-table">
      <thead>
        <tr>
          <th>Drama</th>
          <th>Stage</th>
          <th>Producer</th>
          <th>Producer Contact</th>
          <th>Last Update</th>
          <th>Insight</th>
        </tr>
      </thead>
      <tbody id="overviewDramaTableBody">
        <tr>
          <td colspan="6">Loading drama insights...</td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
