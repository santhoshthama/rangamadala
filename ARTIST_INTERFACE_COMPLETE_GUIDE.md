# Complete Artist Interface Guide
## Technical Documentation with Code Flow & Database Interactions

---

## 📋 TABLE OF CONTENTS
1. [Architecture Overview](#architecture-overview)
2. [Request Flow](#request-flow)
3. [Artist Dashboard - Complete Flow](#artist-dashboard-complete-flow)
4. [Create Drama - Complete Flow](#create-drama-complete-flow)
5. [Database Schema](#database-schema)
6. [File Structure](#file-structure)
7. [How to Modify Everything](#how-to-modify-everything)

---

## ARCHITECTURE OVERVIEW

### MVC Pattern Used
```
public/index.php 
    ↓
App.php (Router)
    ↓
Controller (e.g., Artistdashboard.php)
    ↓
Model (e.g., M_artist.php, M_drama.php)
    ↓
Database.php (PDO wrapper)
    ↓
MySQL Database
    ↓
View (e.g., artistdashboard.view.php)
    ↓
HTML sent to browser
```

### Core Files
- **Entry Point**: `public/index.php`
- **Router**: `app/core/App.php`
- **Database Handler**: `app/core/Database.php`
- **Controller Trait**: `app/core/Controller.php`
- **Config**: `app/core/config.php`

---

## REQUEST FLOW

### 1. How a URL Request Works

**URL**: `http://localhost/rangamadala/artistdashboard`

```
Step 1: public/index.php
├─ Includes: app/core/init.php
│   ├─ Loads: config.php (ROOT, DB_HOST, DB_NAME, etc.)
│   ├─ Starts: session_start()
│   ├─ Requires: App.php, Controller.php, Database.php, functions.php
│
Step 2: Creates $app = new App()
│
Step 3: $app->loadController()
├─ Splits URL: ['artistdashboard']
├─ Looks for: app/controllers/Artistdashboard.php
├─ File exists? Load it : Load _404.php
├─ Creates: new Artistdashboard()
├─ Calls: Artistdashboard->index() (default method)
│
Step 4: Controller executes
├─ Gets models (M_artist, M_drama)
├─ Queries database
├─ Prepares $data array
├─ Calls: $this->view('artistdashboard', $data)
│
Step 5: View renders
└─ Loads: app/views/artistdashboard.view.php
    └─ Displays HTML with data
```

### 2. How Controllers Load Models

**File**: `app/core/Controller.php`
```php
public function getModel($name)
{
    $modelFile = ucfirst($name) . ".php";
    if (file_exists("../app/models/" . $modelFile)) {
        require "../app/models/" . $modelFile;
        return new $name();
    }
    return null;
}
```

**Usage in Controller**:
```php
$artist_model = $this->getModel('M_artist');
// Loads: app/models/M_artist.php
// Returns: new M_artist() object
```

### 3. How Views Are Loaded

**File**: `app/core/Controller.php`
```php
public function view($name, $data=[])
{
    $filename = "../app/views/" . $name . ".view.php";
    if (file_exists($filename)) {
        require $filename;
    } else {
        require "../app/views/_404.view.php";
    }
}
```

**Usage in Controller**:
```php
$this->view('artistdashboard', $data);
// Loads: app/views/artistdashboard.view.php
// $data becomes available via extract($data)
```

---

## ARTIST DASHBOARD - COMPLETE FLOW

### URL: `/artistdashboard`

### FILE 1: app/controllers/Artistdashboard.php

```php
<?php
class Artistdashboard
{
    use Controller; // Gives access to view() and getModel()

    public function index()
    {
        // ============================================
        // STEP 1: AUTHENTICATION CHECK
        // ============================================
        if (!isset($_SESSION['user_id']) || 
            !isset($_SESSION['user_role']) || 
            $_SESSION['user_role'] !== 'artist') {
            header("Location: " . ROOT . "/login");
            exit;
        }
        // If not logged in or not an artist → redirect to login

        // ============================================
        // STEP 2: LOAD MODELS
        // ============================================
        $artist_model = $this->getModel('M_artist');
        // Loads: app/models/M_artist.php
        
        $drama_model = $this->getModel('M_drama');
        // Loads: app/models/M_drama.php
        
        $user_id = $_SESSION['user_id'];
        // Get logged-in user's ID from session

        // ============================================
        // STEP 3: FETCH DATA FROM DATABASE
        // ============================================
        
        // Get artist profile
        $data['user'] = $artist_model->get_artist_by_id($user_id);
        // DATABASE QUERY:
        // SELECT * FROM users WHERE id = ? AND role = 'artist'
        
        // Get dramas where user is director
        $data['dramas_as_director'] = $drama_model->get_dramas_by_director($user_id);
        // DATABASE QUERY:
        // SELECT d.*, c.name as category_name, 'active' as status
        // FROM dramas d
        // LEFT JOIN categories c ON d.category_id = c.id
        // WHERE d.creator_artist_id = ?
        // ORDER BY d.created_at DESC
        
        // Get dramas where user is production manager
        $data['dramas_as_manager'] = $drama_model->get_dramas_by_manager($user_id);
        // Currently returns [] (feature not implemented)
        
        // Get dramas where user is cast as actor
        $data['dramas_as_actor'] = $drama_model->get_dramas_by_actor($user_id);
        // Currently returns [] (feature not implemented)
        
        // Get pending role requests
        $data['role_requests'] = $artist_model->get_pending_role_requests($user_id);
        // Currently returns [] (tables don't exist yet)

        // ============================================
        // STEP 4: CALCULATE STATISTICS
        // ============================================
        $data['stats'] = [
            'total_dramas' => count($data['dramas_as_director']) + 
                             count($data['dramas_as_manager']) + 
                             count($data['dramas_as_actor']),
            'as_director' => count($data['dramas_as_director']),
            'as_manager' => count($data['dramas_as_manager']),
            'as_actor' => count($data['dramas_as_actor']),
            'pending_requests' => count($data['role_requests'])
        ];

        // ============================================
        // STEP 5: LOAD VIEW
        // ============================================
        $this->view('artistdashboard', $data);
        // Loads: app/views/artistdashboard.view.php
        // $data array is passed and extracted in view
    }
}
```

### FILE 2: app/models/M_artist.php

```php
<?php
class M_artist extends M_signup {
    private $db;

    public function __construct() {
        parent::__construct();
        $this->db = new Database(); // Create DB connection
    }

    // ============================================
    // METHOD: Get Artist by ID
    // ============================================
    public function get_artist_by_id($user_id) {
        try {
            // Prepare SQL query
            $this->db->query("SELECT * FROM users 
                             WHERE id = :user_id AND role = 'artist'");
            
            // Bind parameter (prevents SQL injection)
            $this->db->bind(':user_id', $user_id);
            
            // Execute and return single result
            return $this->db->single();
            // Returns object with: id, full_name, email, phone, 
            // role, nic_photo, created_at, etc.
            
        } catch (Exception $e) {
            error_log("Error in get_artist_by_id: " . $e->getMessage());
            return null;
        }
    }

    // Other methods: get_pending_role_requests(), respond_to_role_request()
}
```

### FILE 3: app/models/M_drama.php

```php
<?php
class M_drama {
    protected $db;

    public function __construct() {
        $this->db = new Database();
    }

    // ============================================
    // METHOD: Get Dramas by Director
    // ============================================
    public function get_dramas_by_director($user_id) {
        try {
            $this->db->query("SELECT d.*, c.name as category_name,
                             'active' as status
                             FROM dramas d
                             LEFT JOIN categories c ON d.category_id = c.id
                             WHERE d.creator_artist_id = :user_id
                             ORDER BY d.created_at DESC");
            
            $this->db->bind(':user_id', $user_id);
            
            return $this->db->resultSet();
            // Returns array of objects, each with:
            // id, title, description, category_id, venue, event_date,
            // event_time, duration, ticket_price, image, created_by,
            // creator_artist_id, created_at, updated_at, category_name, status
            
        } catch (Exception $e) {
            error_log("Error in get_dramas_by_director: " . $e->getMessage());
            return [];
        }
    }

    // ============================================
    // METHOD: Get Dramas by Manager
    // ============================================
    public function get_dramas_by_manager($user_id) {
        // TODO: Implement drama_managers table
        return [];
    }

    // ============================================
    // METHOD: Get Dramas by Actor
    // ============================================
    public function get_dramas_by_actor($user_id) {
        // TODO: Implement roles and role_assignments tables
        return [];
    }
}
```

### FILE 4: app/core/Database.php

```php
<?php
class Database {
    private $dbh;   // Database handler (PDO)
    private $stmt;  // Statement

    public function __construct() {
        // Create PDO connection
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME;
        $this->dbh = new PDO($dsn, DB_USER, DB_PASSWORD);
    }

    // ============================================
    // Prepare SQL query
    // ============================================
    public function query($sql){
        $this->stmt = $this->dbh->prepare($sql);
    }

    // ============================================
    // Bind values (prevents SQL injection)
    // ============================================
    public function bind($param, $value, $type = null) {
        $this->stmt->bindValue($param, $value, $type);
    }

    // ============================================
    // Execute the query
    // ============================================
    public function execute(){
        return $this->stmt->execute();
    }

    // ============================================
    // Get multiple results
    // ============================================
    public function resultSet(){
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_OBJ);
        // Returns array of objects
    }

    // ============================================
    // Get single result
    // ============================================
    public function single(){
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_OBJ);
        // Returns single object
    }

    // ============================================
    // Get last inserted ID
    // ============================================
    public function lastInsertId(){
        return $this->dbh->lastInsertId();
    }
}
```

### FILE 5: app/views/artistdashboard.view.php

```php
<?php 
// Extract data array for easier access
if(isset($data) && is_array($data)) {
    extract($data);
    // Now can use $user, $dramas_as_director, etc. directly
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Artist Dashboard</title>
    <style>/* CSS styles */</style>
</head>
<body>
    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <ul class="menu">
            <li><a href="<?=ROOT?>/artistdashboard">Dashboard</a></li>
            <li><a href="<?=ROOT?>/artistprofile">Profile</a></li>
            <li><a href="<?=ROOT?>/browseDramas">Browse Dramas</a></li>
            <li><a href="<?=ROOT?>/logout">Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main--content">
        <!-- Header with user info -->
        <div class="header--wrapper">
            <h1><?= esc($user->full_name) ?></h1>
            <!-- esc() function sanitizes output to prevent XSS -->
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3><?= $stats['total_dramas'] ?></h3>
                <p>Total Dramas</p>
            </div>
            <div class="stat-card">
                <h3><?= $stats['as_director'] ?></h3>
                <p>As Director</p>
            </div>
            <!-- More stat cards... -->
        </div>

        <!-- Dramas as Director Section -->
        <div id="director-tab">
            <h3>Dramas You're Directing</h3>
            
            <?php if (empty($dramas_as_director)): ?>
                <!-- No dramas message -->
                <div class="no-results">
                    <p>You haven't created any dramas.</p>
                    <button onclick="window.location.href='<?=ROOT?>/createDrama'">
                        Create Drama
                    </button>
                </div>
            <?php else: ?>
                <!-- Display dramas -->
                <?php foreach ($dramas_as_director as $drama): ?>
                    <div class="artist-card">
                        <h3><?= esc($drama->title) ?></h3>
                        <p><?= esc($drama->category_name) ?></p>
                        <p>Created: <?= date('M d, Y', strtotime($drama->created_at)) ?></p>
                        <button onclick="window.location.href='<?=ROOT?>/director/dashboard?drama_id=<?=$drama->id?>'">
                            Manage
                        </button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
```

---

## CREATE DRAMA - COMPLETE FLOW

### URL: `/createDrama`

### FILE 1: app/controllers/CreateDrama.php

```php
<?php
class CreateDrama
{
    use Controller;

    // ============================================
    // MAIN METHOD (called by router)
    // ============================================
    public function index()
    {
        // AUTHENTICATION CHECK
        if (!isset($_SESSION['user_id']) || 
            $_SESSION['user_role'] !== 'artist') {
            header("Location: " . ROOT . "/login");
            exit;
        }

        // Check HTTP method
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->createDrama(); // Handle form submission
        } else {
            $this->showForm();    // Display form
        }
    }

    // ============================================
    // SHOW FORM (GET request)
    // ============================================
    private function showForm()
    {
        $drama_model = $this->getModel('M_drama');
        
        // DATABASE QUERY: Get categories for dropdown
        $data['categories'] = $drama_model->getAllCategories();
        // SELECT * FROM categories ORDER BY name ASC
        
        // Initialize empty form data
        $data['form_data'] = [
            'title' => '',
            'description' => '',
            'category_id' => '',
            'venue' => '',
            'event_date' => '',
            'event_time' => '',
            'duration' => '',
            'ticket_price' => ''
        ];
        
        // Load view
        $this->view('create_drama', $data);
        // Loads: app/views/create_drama.view.php
    }

    // ============================================
    // CREATE DRAMA (POST request)
    // ============================================
    private function createDrama()
    {
        $drama_model = $this->getModel('M_drama');
        
        // ============================================
        // STEP 1: VALIDATE INPUT
        // ============================================
        $errors = [];
        
        if (empty($_POST['title'])) {
            $errors[] = "Drama title is required";
        }
        if (empty($_POST['description'])) {
            $errors[] = "Description is required";
        }
        if (empty($_POST['category_id'])) {
            $errors[] = "Category is required";
        }
        if (empty($_POST['venue'])) {
            $errors[] = "Venue is required";
        }
        if (empty($_POST['event_date'])) {
            $errors[] = "Event date is required";
        }
        if (empty($_POST['event_time'])) {
            $errors[] = "Event time is required";
        }
        
        // If validation fails
        if (!empty($errors)) {
            $_SESSION['message'] = implode(", ", $errors);
            $_SESSION['message_type'] = 'error';
            
            // Reload form with error message
            $data['categories'] = $drama_model->getAllCategories();
            $data['form_data'] = $_POST;
            $this->view('create_drama', $data);
            return;
        }
        
        // ============================================
        // STEP 2: HANDLE IMAGE UPLOAD
        // ============================================
        $image_name = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image_name = $this->handleImageUpload($_FILES['image']);
            // Validates file, saves to app/uploads/drama_images/
            // Returns: drama_1234567890_abc123.jpg or false
            
            if ($image_name === false) {
                $_SESSION['message'] = "Error uploading image";
                $_SESSION['message_type'] = 'error';
                
                $data['categories'] = $drama_model->getAllCategories();
                $data['form_data'] = $_POST;
                $this->view('create_drama', $data);
                return;
            }
        }
        
        // ============================================
        // STEP 3: PREPARE DATA FOR DATABASE
        // ============================================
        $drama_data = [
            'title' => trim($_POST['title']),
            'description' => trim($_POST['description']),
            'category_id' => (int)$_POST['category_id'],
            'venue' => trim($_POST['venue']),
            'event_date' => $_POST['event_date'],
            'event_time' => $_POST['event_time'],
            'duration' => !empty($_POST['duration']) ? (int)$_POST['duration'] : null,
            'ticket_price' => !empty($_POST['ticket_price']) ? (float)$_POST['ticket_price'] : null,
            'image' => $image_name,
            'created_by' => $_SESSION['user_id'] // Current artist's ID
        ];
        
        // ============================================
        // STEP 4: INSERT INTO DATABASE
        // ============================================
        if ($drama_model->createDrama($drama_data)) {
            // SUCCESS
            $_SESSION['message'] = "Drama created successfully! You are now the director.";
            $_SESSION['message_type'] = 'success';
            header("Location: " . ROOT . "/artistdashboard");
            exit;
        } else {
            // FAILURE
            $_SESSION['message'] = "Error creating drama. Please try again.";
            $_SESSION['message_type'] = 'error';
            
            $data['categories'] = $drama_model->getAllCategories();
            $data['form_data'] = $_POST;
            $this->view('create_drama', $data);
        }
    }
    
    // ============================================
    // HANDLE IMAGE UPLOAD
    // ============================================
    private function handleImageUpload($file)
    {
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        // Validate file type
        if (!in_array($file['type'], $allowed_types)) {
            return false;
        }
        
        // Validate file size
        if ($file['size'] > $max_size) {
            return false;
        }
        
        // Create directory if doesn't exist
        $upload_dir = 'app/uploads/drama_images/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'drama_' . time() . '_' . uniqid() . '.' . $extension;
        $filepath = $upload_dir . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return $filename; // Return just filename
        }
        
        return false;
    }
}
```

### FILE 2: app/models/M_drama.php (createDrama method)

```php
public function createDrama($data) {
    try {
        // ============================================
        // PREPARE INSERT QUERY
        // ============================================
        $this->db->query("INSERT INTO dramas 
            (title, description, category_id, venue, event_date, 
             event_time, duration, ticket_price, image, created_by, creator_artist_id) 
            VALUES 
            (:title, :description, :category_id, :venue, :event_date, 
             :event_time, :duration, :ticket_price, :image, :created_by, :creator_artist_id)");

        // ============================================
        // BIND PARAMETERS (prevents SQL injection)
        // ============================================
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':category_id', $data['category_id']);
        $this->db->bind(':venue', $data['venue']);
        $this->db->bind(':event_date', $data['event_date']);
        $this->db->bind(':event_time', $data['event_time']);
        $this->db->bind(':duration', $data['duration']);
        $this->db->bind(':ticket_price', $data['ticket_price']);
        $this->db->bind(':image', $data['image']);
        $this->db->bind(':created_by', $data['created_by']);
        $this->db->bind(':creator_artist_id', $data['created_by']);
        // ☝️ creator_artist_id = created_by (artist becomes director)

        // ============================================
        // EXECUTE QUERY
        // ============================================
        if ($this->db->execute()) {
            return $this->db->lastInsertId(); // Return new drama ID
        }
        return false;
        
    } catch (Exception $e) {
        error_log("Error in createDrama: " . $e->getMessage());
        return false;
    }
}
```

### FILE 3: app/views/create_drama.view.php

```php
<?php 
if(isset($data) && is_array($data)) {
    extract($data); // Get $categories, $form_data
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Drama</title>
    <style>/* Form styling */</style>
</head>
<body>
    <div class="container">
        <a href="<?=ROOT?>/artistdashboard">← Back to Dashboard</a>
        
        <h1>Create New Drama</h1>

        <!-- Display messages -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="message <?= $_SESSION['message_type'] ?>">
                <?= esc($_SESSION['message']) ?>
            </div>
            <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
        <?php endif; ?>

        <!-- Form -->
        <form action="<?=ROOT?>/createDrama" method="POST" enctype="multipart/form-data">
            
            <!-- Title -->
            <input type="text" name="title" 
                   placeholder="Drama Title" 
                   value="<?= esc($form_data['title']) ?>" 
                   required>
            
            <!-- Description -->
            <textarea name="description" 
                      placeholder="Description" 
                      required><?= esc($form_data['description']) ?></textarea>
            
            <!-- Category Dropdown -->
            <select name="category_id" required>
                <option value="">Select category</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category->id ?>"
                        <?= ($form_data['category_id'] == $category->id) ? 'selected' : '' ?>>
                        <?= esc($category->name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <!-- Venue -->
            <input type="text" name="venue" 
                   placeholder="Venue" 
                   value="<?= esc($form_data['venue']) ?>" 
                   required>
            
            <!-- Event Date -->
            <input type="date" name="event_date" 
                   value="<?= esc($form_data['event_date']) ?>" 
                   required>
            
            <!-- Event Time -->
            <input type="time" name="event_time" 
                   value="<?= esc($form_data['event_time']) ?>" 
                   required>
            
            <!-- Duration (optional) -->
            <input type="number" name="duration" 
                   placeholder="Duration (minutes)" 
                   value="<?= esc($form_data['duration']) ?>">
            
            <!-- Ticket Price (optional) -->
            <input type="number" name="ticket_price" 
                   placeholder="Ticket Price (LKR)" 
                   step="0.01"
                   value="<?= esc($form_data['ticket_price']) ?>">
            
            <!-- Image Upload -->
            <input type="file" name="image" accept="image/*">
            
            <!-- Buttons -->
            <button type="button" onclick="window.location.href='<?=ROOT?>/artistdashboard'">
                Cancel
            </button>
            <button type="submit">Create Drama</button>
        </form>
    </div>
</body>
</html>
```

---

## DATABASE SCHEMA

### Table: users
```sql
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('admin','artist','audience','service_provider') NOT NULL DEFAULT 'audience',
  `nic_photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
);
```

**Used for**: Storing all users (artists, audiences, admins, service providers)

**Artist-specific queries**:
- Get artist: `SELECT * FROM users WHERE id = ? AND role = 'artist'`

### Table: categories
```sql
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
);
```

**Used for**: Drama categories (Classical, Comedy, Musical, etc.)

**Queries**:
- Get all: `SELECT * FROM categories ORDER BY name ASC`

### Table: dramas
```sql
CREATE TABLE `dramas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text,
  `category_id` int(11) DEFAULT NULL,
  `venue` varchar(255) DEFAULT NULL,
  `event_date` date DEFAULT NULL,
  `event_time` time DEFAULT NULL,
  `duration` int(11) DEFAULT NULL COMMENT 'Duration in minutes',
  `ticket_price` decimal(10,2) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `creator_artist_id` int(11) DEFAULT NULL COMMENT 'The artist who is the director',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `created_by` (`created_by`),
  KEY `creator_artist_id` (`creator_artist_id`),
  FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  FOREIGN KEY (`creator_artist_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
);
```

**Key Fields**:
- `created_by`: General tracking (who created the record)
- `creator_artist_id`: Specific tracking (which artist is the director)
- Both set to the same value when artist creates a drama

**Artist-specific queries**:
- Get dramas by director: `SELECT * FROM dramas WHERE creator_artist_id = ?`
- Get all dramas: `SELECT d.*, c.name as category_name FROM dramas d LEFT JOIN categories c ON d.category_id = c.id`

---

## FILE STRUCTURE

```
rangamadala/
│
├── public/
│   ├── index.php              # Entry point (loads init.php, creates App)
│   ├── robots.txt
│   └── assets/
│       ├── CSS/               # Stylesheets
│       ├── images/            # Static images
│       └── JS/                # JavaScript files
│
├── app/
│   ├── core/
│   │   ├── init.php           # Initialization (config, session, includes)
│   │   ├── config.php         # Configuration (ROOT, DB_*, etc.)
│   │   ├── App.php            # Router (URL parsing, controller loading)
│   │   ├── Controller.php     # Controller trait (view, getModel methods)
│   │   ├── Database.php       # PDO wrapper (query, bind, execute, etc.)
│   │   └── functions.php      # Helper functions (esc, show, etc.)
│   │
│   ├── controllers/
│   │   ├── Home.php           # Homepage controller
│   │   ├── Login.php          # Login controller
│   │   ├── Signup.php         # Registration controller
│   │   ├── Artistdashboard.php         # Artist dashboard
│   │   ├── CreateDrama.php             # Create drama form & logic
│   │   ├── AudienceProfile.php         # Audience profile
│   │   └── _404.php           # 404 error controller
│   │
│   ├── models/
│   │   ├── M_login.php        # Login model
│   │   ├── M_signup.php       # Registration model
│   │   ├── M_artist.php       # Artist-specific queries
│   │   ├── M_drama.php        # Drama-related queries
│   │   ├── M_audience.php     # Audience-specific queries
│   │   └── user.php           # General user model
│   │
│   ├── views/
│   │   ├── home.view.php               # Homepage view
│   │   ├── login.view.php              # Login form view
│   │   ├── artistdashboard.view.php    # Artist dashboard view
│   │   ├── create_drama.view.php       # Create drama form view
│   │   ├── audience_profile.view.php   # Audience profile view
│   │   ├── _404.view.php               # 404 error page
│   │   └── includes/
│   │       ├── header.php              # Common header
│   │       └── footer.php              # Common footer
│   │
│   └── uploads/
│       ├── profile_images/             # User profile images
│       └── drama_images/               # Drama poster images
│
├── database_setup.sql          # Complete database schema
├── database_migration_creator_artist.sql  # Migration for creator_artist_id
└── ARTIST_INTERFACE_COMPLETE_GUIDE.md     # This file
```

---

## HOW TO MODIFY EVERYTHING

### 1. ADD A NEW FIELD TO ARTIST PROFILE

**Step 1: Update Database**
```sql
ALTER TABLE users 
ADD COLUMN bio TEXT AFTER phone;
```

**Step 2: Update View** (app/views/artistdashboard.view.php)
```php
<div class="bio-section">
    <h3>Biography</h3>
    <p><?= esc($user->bio) ?></p>
</div>
```

**Step 3: Update Edit Form** (if you have one)
```php
<textarea name="bio"><?= esc($user->bio) ?></textarea>
```

**Step 4: Update Controller** (to save changes)
```php
$artist_model->update_bio($_SESSION['user_id'], $_POST['bio']);
```

**Step 5: Add Model Method** (app/models/M_artist.php)
```php
public function update_bio($user_id, $bio) {
    $this->db->query("UPDATE users SET bio = :bio WHERE id = :user_id");
    $this->db->bind(':bio', $bio);
    $this->db->bind(':user_id', $user_id);
    return $this->db->execute();
}
```

---

### 2. ADD A NEW STATISTICS CARD

**Step 1: Calculate in Controller** (Artistdashboard.php)
```php
// Add to existing stats array
$data['stats']['upcoming_events'] = $drama_model->count_upcoming_events($user_id);
```

**Step 2: Add Model Method** (M_drama.php)
```php
public function count_upcoming_events($user_id) {
    $this->db->query("SELECT COUNT(*) as count 
                     FROM dramas 
                     WHERE creator_artist_id = :user_id 
                     AND event_date >= CURDATE()");
    $this->db->bind(':user_id', $user_id);
    $result = $this->db->single();
    return $result ? $result->count : 0;
}
```

**Step 3: Display in View** (artistdashboard.view.php)
```php
<div class="stat-card">
    <h3><?= $stats['upcoming_events'] ?></h3>
    <p>Upcoming Events</p>
</div>
```

---

### 3. ADD A NEW TAB TO DASHBOARD

**Step 1: Add Tab Button** (artistdashboard.view.php)
```php
<div class="tabs">
    <button class="tab-button" onclick="showTab('director-tab')">As Director</button>
    <button class="tab-button" onclick="showTab('manager-tab')">As Manager</button>
    <button class="tab-button" onclick="showTab('actor-tab')">As Actor</button>
    <button class="tab-button" onclick="showTab('awards-tab')">Awards</button> <!-- NEW -->
</div>
```

**Step 2: Add Tab Content**
```php
<div id="awards-tab" class="tab-content">
    <h3>Your Awards & Recognition</h3>
    <?php if (empty($awards)): ?>
        <p>No awards yet.</p>
    <?php else: ?>
        <?php foreach ($awards as $award): ?>
            <div class="award-card">
                <h4><?= esc($award->title) ?></h4>
                <p><?= esc($award->year) ?></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
```

**Step 3: Add JavaScript**
```javascript
function showTab(tabId) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    // Show selected tab
    document.getElementById(tabId).classList.add('active');
}
```

**Step 4: Fetch Data in Controller**
```php
$data['awards'] = $artist_model->get_awards($user_id);
```

**Step 5: Create Database Table**
```sql
CREATE TABLE artist_awards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    artist_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    year INT,
    description TEXT,
    FOREIGN KEY (artist_id) REFERENCES users(id)
);
```

**Step 6: Add Model Method**
```php
public function get_awards($user_id) {
    $this->db->query("SELECT * FROM artist_awards 
                     WHERE artist_id = :user_id 
                     ORDER BY year DESC");
    $this->db->bind(':user_id', $user_id);
    return $this->db->resultSet();
}
```

---

### 4. ADD SEARCH/FILTER TO DRAMAS LIST

**Step 1: Add Search Form** (artistdashboard.view.php)
```php
<form method="GET" action="<?=ROOT?>/artistdashboard">
    <input type="text" name="search" placeholder="Search dramas..." 
           value="<?= $_GET['search'] ?? '' ?>">
    <select name="category">
        <option value="">All Categories</option>
        <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat->id ?>" 
                <?= ($_GET['category'] ?? '') == $cat->id ? 'selected' : '' ?>>
                <?= esc($cat->name) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Search</button>
</form>
```

**Step 2: Update Controller** (Artistdashboard.php)
```php
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

$data['dramas_as_director'] = $drama_model->get_dramas_by_director_filtered(
    $user_id, $search, $category
);
```

**Step 3: Update Model Method** (M_drama.php)
```php
public function get_dramas_by_director_filtered($user_id, $search = '', $category = '') {
    $sql = "SELECT d.*, c.name as category_name
            FROM dramas d
            LEFT JOIN categories c ON d.category_id = c.id
            WHERE d.creator_artist_id = :user_id";
    
    if (!empty($search)) {
        $sql .= " AND (d.title LIKE :search OR d.description LIKE :search)";
    }
    
    if (!empty($category)) {
        $sql .= " AND d.category_id = :category";
    }
    
    $sql .= " ORDER BY d.created_at DESC";
    
    $this->db->query($sql);
    $this->db->bind(':user_id', $user_id);
    
    if (!empty($search)) {
        $this->db->bind(':search', '%' . $search . '%');
    }
    
    if (!empty($category)) {
        $this->db->bind(':category', $category);
    }
    
    return $this->db->resultSet();
}
```

---

### 5. ADD EDIT DRAMA FUNCTIONALITY

**Step 1: Create Controller** (app/controllers/EditDrama.php)
```php
<?php
class EditDrama {
    use Controller;

    public function index($drama_id = null) {
        // Authentication check
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'artist') {
            header("Location: " . ROOT . "/login");
            exit;
        }

        if (!$drama_id) {
            header("Location: " . ROOT . "/artistdashboard");
            exit;
        }

        $drama_model = $this->getModel('M_drama');
        
        // Verify ownership
        $drama = $drama_model->getDramaById($drama_id);
        if (!$drama || $drama->creator_artist_id != $_SESSION['user_id']) {
            $_SESSION['message'] = "You don't have permission to edit this drama.";
            $_SESSION['message_type'] = 'error';
            header("Location: " . ROOT . "/artistdashboard");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->updateDrama($drama_id);
        } else {
            $data['drama'] = $drama;
            $data['categories'] = $drama_model->getAllCategories();
            $this->view('edit_drama', $data);
        }
    }

    private function updateDrama($drama_id) {
        $drama_model = $this->getModel('M_drama');
        
        $update_data = [
            'drama_id' => $drama_id,
            'title' => trim($_POST['title']),
            'description' => trim($_POST['description']),
            'category_id' => (int)$_POST['category_id'],
            'venue' => trim($_POST['venue']),
            'event_date' => $_POST['event_date'],
            'event_time' => $_POST['event_time'],
            'duration' => !empty($_POST['duration']) ? (int)$_POST['duration'] : null,
            'ticket_price' => !empty($_POST['ticket_price']) ? (float)$_POST['ticket_price'] : null
        ];

        if ($drama_model->updateDrama($update_data)) {
            $_SESSION['message'] = "Drama updated successfully!";
            $_SESSION['message_type'] = 'success';
            header("Location: " . ROOT . "/artistdashboard");
            exit;
        } else {
            $_SESSION['message'] = "Error updating drama.";
            $_SESSION['message_type'] = 'error';
            header("Location: " . ROOT . "/editDrama/" . $drama_id);
            exit;
        }
    }
}
```

**Step 2: Add Model Method** (M_drama.php)
```php
public function updateDrama($data) {
    try {
        $this->db->query("UPDATE dramas 
                         SET title = :title,
                             description = :description,
                             category_id = :category_id,
                             venue = :venue,
                             event_date = :event_date,
                             event_time = :event_time,
                             duration = :duration,
                             ticket_price = :ticket_price
                         WHERE id = :drama_id");
        
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':category_id', $data['category_id']);
        $this->db->bind(':venue', $data['venue']);
        $this->db->bind(':event_date', $data['event_date']);
        $this->db->bind(':event_time', $data['event_time']);
        $this->db->bind(':duration', $data['duration']);
        $this->db->bind(':ticket_price', $data['ticket_price']);
        $this->db->bind(':drama_id', $data['drama_id']);
        
        return $this->db->execute();
    } catch (Exception $e) {
        error_log("Error in updateDrama: " . $e->getMessage());
        return false;
    }
}
```

**Step 3: Create View** (app/views/edit_drama.view.php)
- Copy create_drama.view.php
- Change form action to: `<?=ROOT?>/editDrama/<?=$drama->id?>`
- Pre-fill values with `$drama->field_name`

**Step 4: Add Edit Button** (artistdashboard.view.php)
```php
<button onclick="window.location.href='<?=ROOT?>/editDrama/<?=$drama->id?>'">
    <i class="fas fa-edit"></i> Edit
</button>
```

---

### 6. ADD DELETE DRAMA FUNCTIONALITY

**Step 1: Add Delete Button** (artistdashboard.view.php)
```php
<button onclick="confirmDelete(<?=$drama->id?>)" class="btn-danger">
    <i class="fas fa-trash"></i> Delete
</button>

<script>
function confirmDelete(dramaId) {
    if (confirm('Are you sure you want to delete this drama? This action cannot be undone.')) {
        window.location.href = '<?=ROOT?>/deleteDrama/' + dramaId;
    }
}
</script>
```

**Step 2: Create Controller** (app/controllers/DeleteDrama.php)
```php
<?php
class DeleteDrama {
    use Controller;

    public function index($drama_id = null) {
        // Authentication
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'artist') {
            header("Location: " . ROOT . "/login");
            exit;
        }

        if (!$drama_id) {
            header("Location: " . ROOT . "/artistdashboard");
            exit;
        }

        $drama_model = $this->getModel('M_drama');
        
        // Verify ownership
        $drama = $drama_model->getDramaById($drama_id);
        if (!$drama || $drama->creator_artist_id != $_SESSION['user_id']) {
            $_SESSION['message'] = "You don't have permission to delete this drama.";
            $_SESSION['message_type'] = 'error';
            header("Location: " . ROOT . "/artistdashboard");
            exit;
        }

        // Delete drama
        if ($drama_model->deleteDrama($drama_id)) {
            // Delete image file if exists
            if ($drama->image) {
                $image_path = 'app/uploads/drama_images/' . $drama->image;
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }

            $_SESSION['message'] = "Drama deleted successfully.";
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = "Error deleting drama.";
            $_SESSION['message_type'] = 'error';
        }

        header("Location: " . ROOT . "/artistdashboard");
        exit;
    }
}
```

**Step 3: Add Model Method** (M_drama.php)
```php
public function deleteDrama($drama_id) {
    try {
        $this->db->query("DELETE FROM dramas WHERE id = :drama_id");
        $this->db->bind(':drama_id', $drama_id);
        return $this->db->execute();
    } catch (Exception $e) {
        error_log("Error in deleteDrama: " . $e->getMessage());
        return false;
    }
}
```

---

### 7. CHANGE SIDEBAR MENU

**Location**: app/views/artistdashboard.view.php

**Find**:
```php
<ul class="menu">
    <li><a href="<?=ROOT?>/artistdashboard">Dashboard</a></li>
    <li><a href="<?=ROOT?>/artistprofile">Profile</a></li>
    <li><a href="<?=ROOT?>/browseDramas">Browse Dramas</a></li>
    <li><a href="<?=ROOT?>/logout">Logout</a></li>
</ul>
```

**Modify**:
```php
<ul class="menu">
    <li><a href="<?=ROOT?>/artistdashboard">
        <i class="fas fa-home"></i> Dashboard
    </a></li>
    <li><a href="<?=ROOT?>/artistprofile">
        <i class="fas fa-user"></i> Profile
    </a></li>
    <li><a href="<?=ROOT?>/myDramas">
        <i class="fas fa-theater-masks"></i> My Dramas
    </a></li>
    <li><a href="<?=ROOT?>/calendar">
        <i class="fas fa-calendar"></i> Calendar
    </a></li>
    <li><a href="<?=ROOT?>/messages">
        <i class="fas fa-envelope"></i> Messages
    </a></li>
    <li><a href="<?=ROOT?>/settings">
        <i class="fas fa-cog"></i> Settings
    </a></li>
    <li><a href="<?=ROOT?>/logout">
        <i class="fas fa-sign-out-alt"></i> Logout
    </a></li>
</ul>
```

---

### 8. CHANGE DASHBOARD COLORS/THEME

**Location**: app/views/artistdashboard.view.php (in `<style>` section)

**Find**:
```css
:root {
    --brand: #ba8e23;
    --brand-strong: #a0781e;
    --brand-soft: rgba(186, 142, 35, 0.12);
    /* ... */
}
```

**Change to** (example: blue theme):
```css
:root {
    --brand: #3498db;              /* Changed to blue */
    --brand-strong: #2980b9;       /* Darker blue */
    --brand-soft: rgba(52, 152, 219, 0.12);  /* Soft blue */
    --ink: #1f2933;
    --muted: #6b7280;
    --card: #ffffff;
    --bg: #f5f5f5;
    --border: #e0e0e0;
    --success: #28a745;
    --warning: #ffc107;
    --danger: #dc3545;
    --info: #17a2b8;
    /* ... */
}
```

---

### 9. ADD PAGINATION TO DRAMAS LIST

**Step 1: Update Controller** (Artistdashboard.php)
```php
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 9;
$offset = ($page - 1) * $per_page;

$data['dramas_as_director'] = $drama_model->get_dramas_by_director_paginated(
    $user_id, $per_page, $offset
);

$data['total_dramas'] = $drama_model->count_dramas_by_director($user_id);
$data['total_pages'] = ceil($data['total_dramas'] / $per_page);
$data['current_page'] = $page;
```

**Step 2: Add Model Methods** (M_drama.php)
```php
public function get_dramas_by_director_paginated($user_id, $limit, $offset) {
    $this->db->query("SELECT d.*, c.name as category_name
                     FROM dramas d
                     LEFT JOIN categories c ON d.category_id = c.id
                     WHERE d.creator_artist_id = :user_id
                     ORDER BY d.created_at DESC
                     LIMIT :limit OFFSET :offset");
    
    $this->db->bind(':user_id', $user_id);
    $this->db->bind(':limit', $limit, PDO::PARAM_INT);
    $this->db->bind(':offset', $offset, PDO::PARAM_INT);
    
    return $this->db->resultSet();
}

public function count_dramas_by_director($user_id) {
    $this->db->query("SELECT COUNT(*) as count 
                     FROM dramas 
                     WHERE creator_artist_id = :user_id");
    $this->db->bind(':user_id', $user_id);
    $result = $this->db->single();
    return $result ? $result->count : 0;
}
```

**Step 3: Add Pagination HTML** (artistdashboard.view.php)
```php
<?php if ($total_pages > 1): ?>
    <div class="pagination">
        <?php if ($current_page > 1): ?>
            <a href="?page=<?= $current_page - 1 ?>">← Previous</a>
        <?php endif; ?>
        
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?= $i ?>" 
               class="<?= $i == $current_page ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
        
        <?php if ($current_page < $total_pages): ?>
            <a href="?page=<?= $current_page + 1 ?>">Next →</a>
        <?php endif; ?>
    </div>
<?php endif; ?>
```

---

### 10. ADD ROLE MANAGEMENT (Manager, Actor)

**Step 1: Create Database Tables**
```sql
-- Table for production managers
CREATE TABLE drama_managers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    drama_id INT NOT NULL,
    artist_id INT NOT NULL,
    responsibilities TEXT,
    assigned_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'inactive') DEFAULT 'active',
    FOREIGN KEY (drama_id) REFERENCES dramas(id) ON DELETE CASCADE,
    FOREIGN KEY (artist_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table for roles (characters)
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    drama_id INT NOT NULL,
    role_name VARCHAR(100) NOT NULL,
    role_description TEXT,
    salary DECIMAL(10,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (drama_id) REFERENCES dramas(id) ON DELETE CASCADE
);

-- Table for role assignments
CREATE TABLE role_assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_id INT NOT NULL,
    artist_id INT NOT NULL,
    status ENUM('requested', 'accepted', 'rejected', 'removed') DEFAULT 'requested',
    requested_by ENUM('director', 'artist') DEFAULT 'director',
    request_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    response_date TIMESTAMP NULL,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (artist_id) REFERENCES users(id) ON DELETE CASCADE
);
```

**Step 2: Update Model Methods** (M_drama.php)
```php
public function get_dramas_by_manager($user_id) {
    $this->db->query("SELECT d.*, dm.responsibilities, dm.assigned_date,
                     u.full_name as director_name
                     FROM dramas d
                     INNER JOIN drama_managers dm ON d.id = dm.drama_id
                     INNER JOIN users u ON d.creator_artist_id = u.id
                     WHERE dm.artist_id = :user_id 
                     AND dm.status = 'active'
                     ORDER BY dm.assigned_date DESC");
    $this->db->bind(':user_id', $user_id);
    return $this->db->resultSet();
}

public function get_dramas_by_actor($user_id) {
    $this->db->query("SELECT d.*, r.role_name, ra.status as assignment_status,
                     u.full_name as director_name
                     FROM dramas d
                     INNER JOIN roles r ON d.id = r.drama_id
                     INNER JOIN role_assignments ra ON r.id = ra.role_id
                     INNER JOIN users u ON d.creator_artist_id = u.id
                     WHERE ra.artist_id = :user_id 
                     AND ra.status = 'accepted'
                     ORDER BY ra.response_date DESC");
    $this->db->bind(':user_id', $user_id);
    return $this->db->resultSet();
}
```

**Step 3: Update View** (artistdashboard.view.php)
- The "As Manager" and "As Actor" tabs will now display data
- No code changes needed (already implemented)

---

## SUMMARY

### Key Concepts:
1. **MVC Pattern**: Controller handles logic, Model handles database, View handles display
2. **Session Management**: `$_SESSION['user_id']`, `$_SESSION['user_role']` track logged-in user
3. **PDO Prepared Statements**: Prevent SQL injection via bind()
4. **Routing**: URL maps to Controller->method()
5. **Data Flow**: Controller → Model → Database → Model → Controller → View → Browser

### Common Tasks:
- **Add field**: Database → Model → Controller → View
- **Add page**: Create Controller → Create View → Add link
- **Add validation**: Update Controller's validation logic
- **Add query**: Add method in Model → Call from Controller
- **Change styling**: Update CSS in View file

### File Relationships:
```
Artistdashboard.php (controller)
    ↓ uses
M_artist.php (model) + M_drama.php (model)
    ↓ uses
Database.php (core)
    ↓ queries
MySQL Database
    ↓ returns data to
M_artist.php + M_drama.php
    ↓ returns to
Artistdashboard.php
    ↓ passes $data to
artistdashboard.view.php (view)
    ↓ displays
HTML in browser
```

---

## END OF GUIDE

This document contains everything about how the artist interface works, from URL to database and back. Use it as a reference when making modifications or adding new features.
