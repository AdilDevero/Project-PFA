<?php
session_start();
require_once 'db_connect.php';

if (empty($_SESSION['admin_logged_in'])) {
  header('Location: admin_login.php');
  exit;
}

$stmt = $pdo->query("SELECT v.id,v.model,v.year,v.plate_number,v.color,v.seats,v.transmission,v.fuel_type,v.daily_rate,v.status,v.mileage,COALESCE(v.quantity,1) AS quantity, b.name AS brand, c.name AS category, o.name AS office
FROM vehicles v
LEFT JOIN brands b ON v.brand_id = b.id
LEFT JOIN categories c ON v.category_id = c.id
LEFT JOIN offices o ON v.office_id = o.id");
$vehicles = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin - Manage Cars</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
  <link rel="stylesheet" href="index.css">
</head>
<body>

<?php include 'admin_menu.php'; ?>

<!-- Navbar (copied from site pages) -->
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
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div class="d-flex align-items-center gap-3">
        <button class="btn btn-outline-secondary" onclick="toggleSidebar()" title="Show/Hide Menu" style="font-size: 20px; width: 45px; height: 45px; padding: 0; display: flex; align-items: center; justify-content: center;">
          ☰
        </button>
        <h2 style="margin: 0;">Admin — Car Inventory</h2>
      </div>
      <div class="d-flex gap-2">
        <a href="admin_add_car.php" class="btn btn-success">
          <i class="ri-car-add-line"></i> Add New Car
        </a>
        <a href="admin_stats.php" class="btn btn-outline-primary">
          <i class="ri-bar-chart-line"></i> View Statistics
        </a>
      </div>
    </div>

  <div class="table-responsive">
    <table class="table table-striped table-bordered align-middle">
      <thead class="table-light">
        <tr>
          <th>ID</th>
          <th>Brand</th>
          <th>Model</th>
          <th>Category</th>
          <th>Plate</th>
          <th>Year</th>
          <th>Rate</th>
          <th>Quantity</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($vehicles as $v): ?>
        <tr>
          <td><?= htmlspecialchars($v['id']) ?></td>
          <td><?= htmlspecialchars($v['brand'] ?? '') ?></td>
          <td><?= htmlspecialchars($v['model']) ?></td>
          <td><?= htmlspecialchars($v['category'] ?? '') ?></td>
          <td><?= htmlspecialchars($v['plate_number'] ?? '') ?></td>
          <td><?= htmlspecialchars($v['year'] ?? '') ?></td>
          <td><?= number_format($v['daily_rate'],2) ?> MAD</td>
          <td>
            <form action="admin_actions.php" method="post" class="d-flex gap-2">
              <input type="hidden" name="action" value="update_quantity">
              <input type="hidden" name="vehicle_id" value="<?= htmlspecialchars($v['id']) ?>">
              <input type="number" name="quantity" value="<?= htmlspecialchars($v['quantity']) ?>" min="0" class="form-control form-control-sm" style="width:90px;">
              <button class="btn btn-sm btn-primary" type="submit">Save</button>
            </form>
          </td>
          <td>
            <form action="admin_actions.php" method="post" class="d-flex gap-2">
              <input type="hidden" name="action" value="update_status">
              <input type="hidden" name="vehicle_id" value="<?= htmlspecialchars($v['id']) ?>">
              <select name="status" class="form-select form-select-sm">
                <?php foreach (['available','reserved','rented','maintenance','unavailable'] as $s): ?>
                  <option value="<?= $s ?>" <?= $v['status']==$s? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                <?php endforeach; ?>
              </select>
              <button class="btn btn-sm btn-secondary" type="submit">Update</button>
            </form>
          </td>
          <td>
            <form action="admin_actions.php" method="post" onsubmit="return confirm('Delete vehicle?');">
              <input type="hidden" name="action" value="delete_vehicle">
              <input type="hidden" name="vehicle_id" value="<?= htmlspecialchars($v['id']) ?>">
              <button class="btn btn-sm btn-danger">Delete</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  </div>
</div>

<footer class="bg-dark text-white text-center py-4 mt-5">
  <p class="mb-1">&copy; 2026 LM Cars. All Rights Reserved.</p>
  <small>Made with ❤️ in Morocco</small>
  <br>
  <small> Dev By Anass </small>
</footer>

</body>
</html>
