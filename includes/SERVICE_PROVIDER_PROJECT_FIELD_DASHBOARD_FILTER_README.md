# Service Provider Project Field + Dashboard Filter Guide

This guide adds a new project field named project_category and shows it in the Service Provider Dashboard with filters.

You can copy each section exactly into the target file.

---

## 1) Database Migration

Create a SQL file (example: add_project_category_to_projects.sql) and run it.

~~~sql
ALTER TABLE projects
ADD COLUMN project_category VARCHAR(100) NULL AFTER project_name;
~~~

---

## 2) Model Updates

File: app/models/M_service_provider.php

### 2.1 Replace insertProject method

Find method:
- public function insertProject($provider_id, $year, $project_name, $services_provided, $description = '')

Replace with:

~~~php
public function insertProject($provider_id, $year, $project_name, $project_category, $services_provided, $description = '') {
    $this->db->query("INSERT INTO projects (provider_id, year, project_name, project_category, services_provided, description) 
                     VALUES (:provider_id, :year, :project_name, :project_category, :services_provided, :description)");
    $this->db->bind(':provider_id', $provider_id);
    $this->db->bind(':year', $year);
    $this->db->bind(':project_name', $project_name);
    $this->db->bind(':project_category', $project_category);
    $this->db->bind(':services_provided', $services_provided);
    $this->db->bind(':description', $description);
    return $this->db->execute();
}
~~~

### 2.2 Replace updateProject method

Find method:
- public function updateProject($project_id, $year, $project_name, $services_provided, $description = '')

Replace with:

~~~php
public function updateProject($project_id, $year, $project_name, $project_category, $services_provided, $description = '') {
    $this->db->query("UPDATE projects SET 
                     year = :year, 
                     project_name = :project_name,
                     project_category = :project_category,
                     services_provided = :services_provided,
                     description = :description
                     WHERE id = :id");
    $this->db->bind(':year', $year);
    $this->db->bind(':project_name', $project_name);
    $this->db->bind(':project_category', $project_category);
    $this->db->bind(':services_provided', $services_provided);
    $this->db->bind(':description', $description);
    $this->db->bind(':id', $project_id);
    return $this->db->execute();
}
~~~

### 2.3 Add new dashboard projects filter method

Add this new method near other project methods:

~~~php
public function getProjectsForDashboard($provider_id, $filters = [], $limit = 10) {
    $sql = "SELECT * FROM projects WHERE provider_id = :provider_id";

    if (!empty($filters['project_category'])) {
        $sql .= " AND project_category = :project_category";
    }

    if (!empty($filters['project_year'])) {
        $sql .= " AND year = :project_year";
    }

    if (!empty($filters['project_search'])) {
        $sql .= " AND (project_name LIKE :project_search OR services_provided LIKE :project_search OR description LIKE :project_search)";
    }

    $sql .= " ORDER BY year DESC, id DESC LIMIT :limit_count";

    $this->db->query($sql);
    $this->db->bind(':provider_id', (int)$provider_id);

    if (!empty($filters['project_category'])) {
        $this->db->bind(':project_category', $filters['project_category']);
    }

    if (!empty($filters['project_year'])) {
        $this->db->bind(':project_year', (int)$filters['project_year']);
    }

    if (!empty($filters['project_search'])) {
        $this->db->bind(':project_search', '%' . trim($filters['project_search']) . '%');
    }

    $this->db->bind(':limit_count', (int)$limit, PDO::PARAM_INT);

    return $this->db->resultSet();
}
~~~

---

## 3) Profile Controller Updates

File: app/controllers/ServiceProviderProfile.php

### 3.1 Update addProject action call

Inside addProject(), replace insertProject call with:

~~~php
$result = $model->insertProject(
    $provider_id,
    $_POST['year'],
    $_POST['project_name'],
    $_POST['project_category'] ?? null,
    $_POST['services_provided'],
    $_POST['description'] ?? ''
);
~~~

### 3.2 Update editProject action call

Inside editProject(), replace updateProject call with:

~~~php
$result = $model->updateProject(
    $project_id,
    $_POST['year'],
    $_POST['project_name'],
    $_POST['project_category'] ?? null,
    $_POST['services_provided'],
    $_POST['description'] ?? ''
);
~~~

---

## 4) Dashboard Controller Updates

File: app/controllers/ServiceProviderDashboard.php

### 4.1 Add new filter params

After trend parsing block, add:

~~~php
$projectFilters = [
    'project_category' => trim($_GET['project_category'] ?? ''),
    'project_year' => trim($_GET['project_year'] ?? ''),
    'project_search' => trim($_GET['project_search'] ?? ''),
];
~~~

### 4.2 Fetch filtered projects

After ongoing services/top clients queries, add:

~~~php
$dashboardProjects = $providerModel->getProjectsForDashboard($providerId, $projectFilters, 10);
~~~

### 4.3 Pass to view

In data array, add:

~~~php
'dashboard_projects' => $dashboardProjects,
'project_filters' => $projectFilters,
~~~

---

## 5) Add Project View Update

File: app/views/service_add_project.view.php

Add this block between Project Name and Services Provided fields:

~~~php
<div class="form-group">
    <label class="form-label">Project Category</label>
    <select name="project_category" class="form-input">
        <option value="">Select category</option>
        <option value="Theatre">Theatre</option>
        <option value="Concert">Concert</option>
        <option value="Corporate Event">Corporate Event</option>
        <option value="Festival">Festival</option>
        <option value="Other">Other</option>
    </select>
</div>
~~~

---

## 6) Edit Project View Update

File: app/views/service_edit_project.view.php

Add this block between Project Name and Services Provided fields:

~~~php
<div class="form-group">
    <label class="form-label">Project Category</label>
    <select name="project_category" class="form-input">
        <option value="" <?= empty($data['project']->project_category) ? 'selected' : '' ?>>Select category</option>
        <option value="Theatre" <?= (($data['project']->project_category ?? '') === 'Theatre') ? 'selected' : '' ?>>Theatre</option>
        <option value="Concert" <?= (($data['project']->project_category ?? '') === 'Concert') ? 'selected' : '' ?>>Concert</option>
        <option value="Corporate Event" <?= (($data['project']->project_category ?? '') === 'Corporate Event') ? 'selected' : '' ?>>Corporate Event</option>
        <option value="Festival" <?= (($data['project']->project_category ?? '') === 'Festival') ? 'selected' : '' ?>>Festival</option>
        <option value="Other" <?= (($data['project']->project_category ?? '') === 'Other') ? 'selected' : '' ?>>Other</option>
    </select>
</div>
~~~

---

## 7) Dashboard View Update (Filter + List)

File: app/views/service_provider_dashboard.view.php

Add this full block before the Ongoing Services section:

~~~php
<div class="chart-card" style="margin-top: 20px;">
    <div class="section-header">
        <h3>Recent Projects</h3>
    </div>

    <form method="GET" action="<?= ROOT ?>/ServiceProviderDashboard" style="margin-bottom: 16px; display: grid; grid-template-columns: repeat(4, minmax(120px, 1fr)); gap: 10px;">
        <input type="hidden" name="trend" value="<?= htmlspecialchars($trend_range ?? 'monthly') ?>">

        <select name="project_category" class="form-input">
            <option value="">All Categories</option>
            <?php $pf = $project_filters ?? []; ?>
            <?php $pc = $pf['project_category'] ?? ''; ?>
            <option value="Theatre" <?= $pc === 'Theatre' ? 'selected' : '' ?>>Theatre</option>
            <option value="Concert" <?= $pc === 'Concert' ? 'selected' : '' ?>>Concert</option>
            <option value="Corporate Event" <?= $pc === 'Corporate Event' ? 'selected' : '' ?>>Corporate Event</option>
            <option value="Festival" <?= $pc === 'Festival' ? 'selected' : '' ?>>Festival</option>
            <option value="Other" <?= $pc === 'Other' ? 'selected' : '' ?>>Other</option>
        </select>

        <input type="number" name="project_year" class="form-input" placeholder="Year"
               value="<?= htmlspecialchars($pf['project_year'] ?? '') ?>">

        <input type="text" name="project_search" class="form-input" placeholder="Search project..."
               value="<?= htmlspecialchars($pf['project_search'] ?? '') ?>">

        <div style="display:flex; gap:8px;">
            <button type="submit" class="btn">Apply</button>
            <a href="<?= ROOT ?>/ServiceProviderDashboard?trend=<?= urlencode($trend_range ?? 'monthly') ?>" class="btn btn-secondary" style="text-decoration:none; display:inline-flex; align-items:center;">Clear</a>
        </div>
    </form>

    <div class="activity-list">
        <?php if (!empty($dashboard_projects)): ?>
            <?php foreach ($dashboard_projects as $project): ?>
                <div class="activity-item">
                    <div class="activity-content">
                        <div class="activity-title">
                            <?= htmlspecialchars($project->project_name ?? 'Untitled Project') ?>
                            <?php if (!empty($project->project_category)): ?>
                                <span style="font-size: 12px; margin-left: 8px; padding: 2px 8px; border-radius: 12px; background:#f3f4f6; color:#374151;">
                                    <?= htmlspecialchars($project->project_category) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="activity-time">
                            Year: <?= htmlspecialchars($project->year ?? '-') ?>
                            <?php if (!empty($project->services_provided)): ?>
                                | Services: <?= htmlspecialchars($project->services_provided) ?>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($project->description)): ?>
                            <div style="margin-top:6px; color:#6b7280; font-size:13px;">
                                <?= nl2br(htmlspecialchars($project->description)) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="activity-item">
                <div class="activity-content">
                    <div class="activity-title">No projects found</div>
                    <div class="activity-time">Try changing your project filters.</div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
~~~

---

## 8) Optional: Show category in profile page projects

File: app/views/service_provider_profile.view.php

Inside the Recent Projects card (where project_name/year/services are displayed), add this block:

~~~php
<?php if (!empty($project->project_category)): ?>
    <div class="form-group">
        <label class="form-label">Project Category</label>
        <div class="form-input" style="background: #f8f9fa; cursor: default;">
            <?php echo htmlspecialchars($project->project_category); ?>
        </div>
    </div>
<?php endif; ?>
~~~

---

## 9) Quick Test Checklist

1. Add a new project with project_category.
2. Edit same project and change category.
3. Confirm value appears in profile project card.
4. Open Service Provider Dashboard.
5. Filter by category/year/search and verify results.
6. Clear filters and verify list resets.

---

## Notes

- Keep field name consistent everywhere: project_category.
- If you already use different categories, replace dropdown options accordingly.
- If your DB uses strict SQL mode and binds LIMIT with named params differently, adjust LIMIT handling to integer interpolation after validation.
