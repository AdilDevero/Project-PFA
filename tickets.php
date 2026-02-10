<?php
require_once 'db_connect.php';

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

// Handle form submission
$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $vehicle_id = isset($_POST['vehicle_id']) ? (int)$_POST['vehicle_id'] : 0;
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';

    // Validation
    if (empty($full_name) || empty($phone) || $vehicle_id <= 0 || empty($start_date) || empty($end_date)) {
        $error_msg = "All fields are required!";
    } else if (strtotime($end_date) <= strtotime($start_date)) {
        $error_msg = "End date must be after start date!";
    } else {
        // Calculate number of days
        $start = new DateTime($start_date);
        $end = new DateTime($end_date);
        $interval = $start->diff($end);
        $number_of_days = $interval->days + 1;

        // Insert into database
        $stmt = $pdo->prepare('INSERT INTO tickets (full_name, phone, vehicle_id, start_date, end_date, number_of_days) VALUES (?, ?, ?, ?, ?, ?)');
        try {
            $stmt->execute([$full_name, $phone, $vehicle_id, $start_date, $end_date, $number_of_days]);
            $success_msg = "Reservation submitted successfully! Our team will contact you soon.";
        } catch (Exception $e) {
            $error_msg = "Error submitting reservation. Please try again.";
        }
    }
}

// Get available cars
$cars = $pdo->query("SELECT v.id, v.model, b.name as brand, c.name as category, v.daily_rate
                     FROM vehicles v
                     LEFT JOIN brands b ON v.brand_id = b.id
                     LEFT JOIN categories c ON v.category_id = c.id
                     WHERE v.status = 'available'
                     ORDER BY b.name, v.model")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Book a Car - Reservation</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="index.css">
    <style>
        .reservation-form-container {
            max-width: 600px;
            margin: 100px auto 50px;
            padding: 30px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        .reservation-form-container h2 {
            color: #1e3a5f;
            margin-bottom: 30px;
            text-align: center;
        }
        .form-label {
            font-weight: 600;
            color: #333;
        }
        .btn-book {
            background-color: #ff3c00;
            color: white;
            padding: 12px 30px;
            font-weight: 600;
            border: none;
            border-radius: 30px;
            width: 100%;
            transition: all 0.3s ease;
        }
        .btn-book:hover {
            background-color: #e63000;
            transform: translateY(-2px);
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #1e3a5f;
            text-decoration: none;
            font-weight: 600;
        }
        .back-link:hover {
            color: #ff3c00;
        }
    </style>
</head>
<body>

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
                <li class="nav-item"><a class="nav-link btn btn-outline-danger text-dark px-3 rounded-pill" href="home.php">Home</a></li>
                <li class="nav-item"><a class="nav-link btn btn-outline-danger text-dark px-3 rounded-pill" href="OurCars.php">Our Cars</a></li>
                <li class="nav-item"><a class="nav-link btn btn-outline-danger text-dark px-3 rounded-pill" href="Aboutus.php">About Us</a></li>
            </ul>
        </div>
        <div class="d-none d-lg-flex align-items-center gap-2 text-danger fw-bold">
            <i class="ri-phone-fill fs-4"></i> Call Anytime: <span>0660169575</span>
        </div>
    </div>
</nav>

<div class="container">
    <div class="reservation-form-container">
        <a href="OurCars.php" class="back-link"><i class="ri-arrow-left-line"></i> Back to Cars</a>
        
        <h2><i class="ri-calendar-booking-line"></i> Book Your Car</h2>

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

        <form method="POST" action="tickets.php" class="needs-validation">
            <div class="mb-3">
                <label for="full_name" class="form-label">Full Name *</label>
                <input type="text" class="form-control form-control-lg" id="full_name" name="full_name" placeholder="Enter your full name" required>
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Phone Number *</label>
                <input type="tel" class="form-control form-control-lg" id="phone" name="phone" placeholder="e.g., +1234567890" required>
            </div>

            <div class="mb-3">
                <label for="vehicle_id" class="form-label">Select Car *</label>
                <select class="form-select form-select-lg" id="vehicle_id" name="vehicle_id" required>
                    <option value="">-- Choose a car --</option>
                    <?php foreach ($cars as $car): ?>
                        <option value="<?= $car['id'] ?>">
                            <?= htmlspecialchars($car['brand'] . ' ' . $car['model']) ?> 
                            (<?= htmlspecialchars($car['category']) ?>) - 
                            <?= number_format($car['daily_rate'], 2) ?> MAD/day
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (empty($cars)): ?>
                    <small class="text-warning"><i class="ri-alert-line"></i> No available cars at the moment</small>
                <?php endif; ?>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="start_date" class="form-label">Start Date *</label>
                    <input type="date" class="form-control form-control-lg" id="start_date" name="start_date" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="end_date" class="form-label">End Date *</label>
                    <input type="date" class="form-control form-control-lg" id="end_date" name="end_date" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="number_of_days" class="form-label">Number of Days</label>
                <input type="number" class="form-control form-control-lg" id="number_of_days" readonly placeholder="Auto-calculated">
            </div>

            <button type="submit" class="btn btn-book">
                <i class="ri-check-line"></i> Confirm Reservation
            </button>
        </form>
    </div>
</div>

<footer class="bg-dark text-white text-center py-4 mt-5">
    <p class="mb-1">&copy; 2026 LM Cars. All Rights Reserved.</p>
    <small>Made with ❤️ in Morocco</small>
    <br>
    <small>Dev By Anass</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Auto-calculate number of days
    document.getElementById('start_date').addEventListener('change', calculateDays);
    document.getElementById('end_date').addEventListener('change', calculateDays);

    function calculateDays() {
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        
        if (startDate && endDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);
            const diffTime = end - start;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
            
            document.getElementById('number_of_days').value = diffDays > 0 ? diffDays : '';
        }
    }
</script>
</body>
</html>
