<?php
session_start();
require_once 'db_connect.php';

if (empty($_SESSION['admin_logged_in'])) {
  header('Location: admin_login.php');
  exit;
}

$success_msg = '';
$error_msg = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $brand_id = isset($_POST['brand_id']) ? (int)$_POST['brand_id'] : null;
    $model = trim($_POST['model'] ?? '');
    $category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : null;
    $year = !empty($_POST['year']) ? (int)$_POST['year'] : null;
    $daily_rate = isset($_POST['daily_rate']) ? (float)$_POST['daily_rate'] : 0.0;
    $status = $_POST['status'] ?? 'available';
    $plate_number = trim($_POST['plate_number'] ?? '');

    // Validation
    if (!$brand_id || empty($model) || !$category_id || $daily_rate <= 0) {
        $error_msg = "Please fill all required fields with valid values!";
    } else {
        // Insert vehicle
        try {
            $stmt = $pdo->prepare('INSERT INTO vehicles (brand_id, model, category_id, year, plate_number, daily_rate, status) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$brand_id, $model, $category_id, $year, $plate_number, $daily_rate, $status]);
            $vehicle_id = $pdo->lastInsertId();
            
            $success_msg = "Vehicle added successfully!";
            
            // Handle image upload if provided
            if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
                $check = getimagesize($_FILES['image_file']['tmp_name']);
                if ($check !== false) {
                    $uploadsDir = __DIR__ . '/img/uploads';
                    if (!is_dir($uploadsDir)) mkdir($uploadsDir, 0755, true);
                    $ext = pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION);
                    $filename = 'car_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
                    $target = $uploadsDir . '/' . $filename;
                    if (move_uploaded_file($_FILES['image_file']['tmp_name'], $target)) {
                        $url = 'img/uploads/' . $filename;
                        $ins = $pdo->prepare('INSERT INTO vehicle_images (vehicle_id, url, caption) VALUES (?, ?, ?)');
                        $ins->execute([$vehicle_id, $url, null]);
                    }
                }
            }
        } catch (Exception $e) {
            $error_msg = "Error adding vehicle: " . $e->getMessage();
        }
    }
}

// Get brands and categories
$brands = $pdo->query('SELECT id, name FROM brands ORDER BY name')->fetchAll();
$categories = $pdo->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin - Add New Car</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
  <link rel="stylesheet" href="index.css">
  <style>
    .add-car-container {
      max-width: 800px;
      margin: 100px auto 50px;
      padding: 40px;
      background: white;
      border-radius: 15px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }
    .add-car-container h2 {
      color: #1e3a5f;
      margin-bottom: 30px;
    }
    .form-section {
      margin-bottom: 30px;
    }
    .form-section h5 {
      color: #2c5aa0;
      border-bottom: 2px solid #ff3c00;
      padding-bottom: 10px;
      margin-bottom: 20px;
    }
  </style>
</head>
<body>

<?php include 'admin_menu.php'; ?>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
  <div class="container d-flex justify-content-between align-items-center">
    <a class="navbar-brand fw-bold text-danger fs-3" href="#">
      <img src="img/logo.png" alt="LM Cars" width="40" class="me-2">LM Cars
    </a>
    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
      <ul class="navbar-nav gap-3">
        <li class="nav-item"><a class="nav-link" href="home.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="OurCars.php">Our Cars</a></li>
        <li class="nav-item"><a class="nav-link" href="Aboutus.php">About Us</a></li>
      </ul>
    </div>
    <div class="d-none d-lg-flex align-items-center gap-2 text-danger fw-bold">
      <i class="ri-phone-fill fs-4"></i> Call Anytime: <span>0660169575</span>
    </div>
  </div>
</nav>

<div class="admin-content">
  <div class="add-car-container">
    <a href="admin_cars.php" class="btn btn-sm btn-outline-secondary mb-3">
      <i class="ri-arrow-left-line"></i> Back to Cars
    </a>

    <h2><i class="ri-car-add-line"></i> Add New Vehicle</h2>

    <!-- Success/Error Messages -->
    <?php if ($success_msg): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="ri-checkbox-circle-line"></i> <?= htmlspecialchars($success_msg); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <?php if ($error_msg): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="ri-error-warning-line"></i> <?= htmlspecialchars($error_msg); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
      <!-- Basic Information Section -->
      <div class="form-section">
        <h5><i class="ri-information-line"></i> Basic Information</h5>
        
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Brand *</label>
            <select name="brand_id" class="form-select form-select-lg" required>
              <option value="">-- Select Brand --</option>
              <?php foreach ($brands as $b): ?>
                <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label">Type (Model) *</label>
            <input type="text" name="model" class="form-control form-control-lg" placeholder="e.g., Clio, 3 Series, Model S" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Category *</label>
            <select name="category_id" class="form-select form-select-lg" required>
              <option value="">-- Select Category --</option>
              <?php foreach ($categories as $c): ?>
                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label">Year</label>
            <input type="number" name="year" class="form-control form-control-lg" placeholder="2024" min="1990" max="2030">
          </div>
        </div>
      </div>

      <!-- Pricing & Status Section -->
      <div class="form-section">
        <h5><i class="ri-price-tag-line"></i> Pricing & Status</h5>
        
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Daily Price (MAD) *</label>
            <input type="number" name="daily_rate" class="form-control form-control-lg" placeholder="e.g., 250.00" step="0.01" min="0" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Status *</label>
            <select name="status" class="form-select form-select-lg" required>
              <option value="available">Available</option>
              <option value="reserved">Reserved</option>
              <option value="rented">Rented</option>
              <option value="maintenance">Maintenance</option>
              <option value="unavailable">Unavailable</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Additional Information Section -->
      <div class="form-section">
        <h5><i class="ri-settings-line"></i> Additional Information</h5>
        
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Plate Number</label>
            <input type="text" name="plate_number" class="form-control form-control-lg" placeholder="e.g., AA-123-BB">
          </div>

          <div class="col-md-6">
            <label class="form-label">Photo</label>
            <input type="file" name="image_file" class="form-control form-control-lg" accept="image/*">
            <small class="text-muted">Upload a car image (PNG, JPG, etc.)</small>
          </div>
        </div>
      </div>

      <!-- Submit Button -->
      <div class="d-grid gap-2">
        <button type="submit" class="btn btn-success btn-lg">
          <i class="ri-check-double-line"></i> Add Vehicle
        </button>
      </div>
    </form>
  </div>
</div>

<footer class="bg-dark text-white text-center py-4 mt-5">
  <p class="mb-1">&copy; 2026 LM Cars. All Rights Reserved.</p>
  <small>Made with ❤️ in Morocco</small>
  <br>
  <small> Dev By Anass </small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
