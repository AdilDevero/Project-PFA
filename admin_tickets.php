<?php
session_start();
require_once 'db_connect.php';

if (empty($_SESSION['admin_logged_in'])) {
  header('Location: admin_login.php');
  exit;
}

// Create tickets table if it doesn't exist
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS tickets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(255) NOT NULL,
        phone VARCHAR(50) NOT NULL,
        vehicle_id INT NOT NULL,
        start_date DATE NOT NULL,
        end_date DATE NOT NULL,
        number_of_days INT NOT NULL,
        status ENUM('pending','confirmed','cancelled','completed') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE RESTRICT
    ) ENGINE=InnoDB");
} catch (Exception $e) {
    // Table already exists
}

// Handle status update
if (isset($_GET['update_status'])) {
    $ticket_id = (int)$_GET['update_status'];
    $new_status = $_GET['status'] ?? 'pending';
    $stmt = $pdo->prepare('UPDATE tickets SET status = ? WHERE id = ?');
    $stmt->execute([$new_status, $ticket_id]);
    header('Location: admin_tickets.php');
    exit;
}

// Handle delete
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $stmt = $pdo->prepare('DELETE FROM tickets WHERE id = ?');
    $stmt->execute([$delete_id]);
    header('Location: admin_tickets.php');
    exit;
}

// Get all tickets with vehicle info
$tickets = $pdo->query("SELECT t.id, t.full_name, t.phone, t.vehicle_id, t.start_date, t.end_date, 
                        t.number_of_days, t.status, t.created_at, v.model, b.name as brand, v.daily_rate
                        FROM tickets t
                        LEFT JOIN vehicles v ON t.vehicle_id = v.id
                        LEFT JOIN brands b ON v.brand_id = b.id
                        ORDER BY t.created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin - Reservations & Tickets</title>
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
      <i class="ri-ticket-2-line"></i> Reservations & Tickets Log
    </h2>

    <!-- Stats Cards -->
    <div class="row mb-4 g-3">
      <div class="col-md-3">
        <div class="card text-center p-3 bg-light">
          <h6>Total Reservations</h6>
          <h3 class="text-primary"><?= count($tickets) ?></h3>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-center p-3 bg-light">
          <h6>Pending</h6>
          <h3 class="text-warning"><?= count(array_filter($tickets, fn($t) => $t['status'] === 'pending')) ?></h3>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-center p-3 bg-light">
          <h6>Confirmed</h6>
          <h3 class="text-success"><?= count(array_filter($tickets, fn($t) => $t['status'] === 'confirmed')) ?></h3>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-center p-3 bg-light">
          <h6>Completed</h6>
          <h3 class="text-info"><?= count(array_filter($tickets, fn($t) => $t['status'] === 'completed')) ?></h3>
        </div>
      </div>
    </div>

    <!-- Reservations Table -->
    <div class="card shadow-sm">
      <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="ri-list-check-2"></i> All Reservations</h5>
      </div>
      <div class="card-body">
        <?php if (empty($tickets)): ?>
          <div class="alert alert-info">
            <i class="ri-info-line"></i> No reservations yet.
          </div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
              <thead class="table-light">
                <tr>
                  <th>ID</th>
                  <th>Customer Name</th>
                  <th>Phone</th>
                  <th>Car</th>
                  <th>Dates</th>
                  <th>Days</th>
                  <th>Status</th>
                  <th>Submitted</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($tickets as $ticket): ?>
                <tr>
                  <td>
                    <span class="badge bg-secondary"><?= $ticket['id'] ?></span>
                  </td>
                  <td>
                    <strong><?= htmlspecialchars($ticket['full_name']) ?></strong>
                  </td>
                  <td>
                    <a href="tel:<?= htmlspecialchars($ticket['phone']) ?>">
                      <i class="ri-phone-line"></i> <?= htmlspecialchars($ticket['phone']) ?>
                    </a>
                  </td>
                  <td>
                    <?= htmlspecialchars($ticket['brand'] . ' ' . $ticket['model']) ?>
                    <br>
                    <small class="text-muted"><?= number_format($ticket['daily_rate'], 2) ?> MAD/day</small>
                  </td>
                  <td>
                    <small>
                      <i class="ri-calendar-line"></i> 
                      <?= date('M d, Y', strtotime($ticket['start_date'])) ?> → 
                      <?= date('M d, Y', strtotime($ticket['end_date'])) ?>
                    </small>
                  </td>
                  <td>
                    <span class="badge bg-info"><?= $ticket['number_of_days'] ?> days</span>
                  </td>
                  <td>
                    <?php
                    $status_color = [
                      'pending' => 'warning',
                      'confirmed' => 'success',
                      'cancelled' => 'danger',
                      'completed' => 'secondary'
                    ];
                    $color = $status_color[$ticket['status']] ?? 'secondary';
                    ?>
                    <span class="badge bg-<?= $color; ?>"><?= ucfirst($ticket['status']) ?></span>
                  </td>
                  <td>
                    <small><?= date('M d, Y H:i', strtotime($ticket['created_at'])) ?></small>
                  </td>
                  <td>
                    <div class="btn-group btn-group-sm" role="group">
                      <a href="admin_tickets.php?update_status=<?= $ticket['id'] ?>&status=confirmed" 
                         class="btn btn-success" title="Confirm">
                        <i class="ri-check-line"></i>
                      </a>
                      <a href="admin_tickets.php?update_status=<?= $ticket['id'] ?>&status=cancelled" 
                         class="btn btn-warning" title="Cancel">
                        <i class="ri-close-line"></i>
                      </a>
                      <a href="admin_tickets.php?delete_id=<?= $ticket['id'] ?>" 
                         class="btn btn-danger" title="Delete"
                         onclick="return confirm('Delete this reservation?');">
                        <i class="ri-delete-bin-line"></i>
                      </a>
                    </div>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
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
  <small>Dev By Anass</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
