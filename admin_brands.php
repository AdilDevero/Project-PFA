<?php
session_start();
require_once 'db_connect.php';

if (empty($_SESSION['admin_logged_in'])) {
  header('Location: admin_login.php');
  exit;
}

// Handle add brand
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_brand') {
  $brand_name = trim($_POST['brand_name'] ?? '');
  if (!empty($brand_name)) {
    $stmt = $pdo->prepare('INSERT INTO brands (name) VALUES (?)');
    $stmt->execute([$brand_name]);
    $success_msg = "Brand added successfully!";
  } else {
    $error_msg = "Brand name cannot be empty!";
  }
}

// Handle delete brand
if (isset($_GET['delete_id'])) {
  $delete_id = (int)$_GET['delete_id'];
  $stmt = $pdo->prepare('DELETE FROM brands WHERE id = ?');
  $stmt->execute([$delete_id]);
  header('Location: admin_brands.php');
  exit;
}

// Get all brands
$brands = $pdo->query('SELECT id, name FROM brands ORDER BY name ASC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin - Manage Brands</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
  <link rel="stylesheet" href="index.css">
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
  <div class="container" style="margin-top:120px;">
    <h2 class="mb-4">
      <i class="ri-trademark-line"></i> Manage Car Brands
    </h2>

    <!-- Success/Error Messages -->
    <?php if (isset($success_msg)): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="ri-checkbox-circle-line"></i> <?= htmlspecialchars($success_msg); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <?php if (isset($error_msg)): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="ri-error-warning-line"></i> <?= htmlspecialchars($error_msg); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <!-- Add New Brand Form -->
    <div class="card mb-4 shadow-sm">
      <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="ri-add-circle-line"></i> Add New Brand</h5>
      </div>
      <div class="card-body">
        <form action="admin_brands.php" method="post" class="row g-3">
          <input type="hidden" name="action" value="add_brand">
          <div class="col-md-8">
            <label class="form-label">Brand Name</label>
            <input type="text" name="brand_name" class="form-control form-control-lg" placeholder="e.g., Toyota, BMW, Mercedes..." required>
          </div>
          <div class="col-md-4">
            <label class="form-label">&nbsp;</label>
            <button type="submit" class="btn btn-success btn-lg w-100">
              <i class="ri-add-line"></i> Add Brand
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Brands List -->
    <div class="card shadow-sm">
      <div class="card-header bg-secondary text-white">
        <h5 class="mb-0"><i class="ri-list-2"></i> Existing Brands</h5>
      </div>
      <div class="card-body">
        <?php if (empty($brands)): ?>
          <div class="alert alert-info">
            <i class="ri-info-line"></i> No brands found. Add the first brand to get started!
          </div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
              <thead class="table-light">
                <tr>
                  <th style="width: 60px;">ID</th>
                  <th>Brand Name</th>
                  <th style="width: 150px;" class="text-center">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($brands as $brand): ?>
                <tr>
                  <td>
                    <span class="badge bg-secondary"><?= htmlspecialchars($brand['id']) ?></span>
                  </td>
                  <td>
                    <strong><?= htmlspecialchars($brand['name']) ?></strong>
                  </td>
                  <td class="text-center">
                    <a href="admin_brands.php?delete_id=<?= $brand['id'] ?>" 
                       class="btn btn-sm btn-danger"
                       onclick="return confirm('Are you sure you want to delete this brand?');">
                      <i class="ri-delete-bin-line"></i> Delete
                    </a>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <div class="mt-3 text-muted">
            <small><i class="ri-information-line"></i> Total brands: <strong><?= count($brands) ?></strong></small>
          </div>
        <?php endif; ?>
      </div>
    </div>
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
