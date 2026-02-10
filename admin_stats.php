<?php
session_start();
require_once 'db_connect.php';

if (empty($_SESSION['admin_logged_in'])) {
  header('Location: admin_login.php');
  exit;
}

$totalVehicles = $pdo->query('SELECT COUNT(*) FROM vehicles')->fetchColumn();
$available = $pdo->query("SELECT COUNT(*) FROM vehicles WHERE status='available'")->fetchColumn();
$rented = $pdo->query("SELECT COUNT(*) FROM vehicles WHERE status='rented'")->fetchColumn();
$reserved = $pdo->query("SELECT COUNT(*) FROM vehicles WHERE status='reserved'")->fetchColumn();
$totalCustomers = $pdo->query('SELECT COUNT(*) FROM customers')->fetchColumn();
$totalRentals = $pdo->query("SELECT COUNT(*) FROM vehicles WHERE status='rented'")->fetchColumn();
$totalRevenue = $pdo->query('SELECT IFNULL(SUM(amount),0) FROM payments WHERE status = "paid"')->fetchColumn();

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin - Statistics</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
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
    <h2>Admin — Statistics</h2>
  <div class="row mt-4 g-3">
    <div class="col-md-3">
      <div class="card p-3">
        <h6>Total Vehicles</h6>
        <h3><?= (int)$totalVehicles ?></h3>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card p-3">
        <h6>Available</h6>
        <h3><?= (int)$available ?></h3>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card p-3">
        <h6>Rented</h6>
        <h3><?= (int)$rented ?></h3>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card p-3">
        <h6>Reserved</h6>
        <h3><?= (int)$reserved ?></h3>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card p-3">
        <h6>Total Customers</h6>
        <h3><?= (int)$totalCustomers ?></h3>
      </div>
    </div>
    <div class="col-md-6 mt-3">
      <div class="card p-3">
        <h6>Total Rentals</h6>
        <h3><?= (int)$totalRentals ?></h3>
      </div>
    </div>
    <div class="col-md-6 mt-3">
      <div class="card p-3">
        <h6>Total Revenue (paid)</h6>
        <h3><?= number_format($totalRevenue,2) ?> MAD</h3>
      </div>
    </div>
  </div>
  <div class="mt-4">
    <a href="admin_cars.php" class="btn btn-outline-secondary">Back to Inventory</a>
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
