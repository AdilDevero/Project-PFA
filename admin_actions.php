<?php
session_start();
require_once 'db_connect.php';

if (empty($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin_cars.php');
    exit;
}

$action = $_POST['action'] ?? '';
$vehicle_id = isset($_POST['vehicle_id']) ? (int)$_POST['vehicle_id'] : 0;

// Ensure `quantity` column exists — add it if missing to avoid SQL errors
try {
    $hasQuantity = (int) $pdo->query("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='vehicles' AND COLUMN_NAME='quantity'")->fetchColumn();
    if (!$hasQuantity) {
        // add column with default value
        $pdo->exec("ALTER TABLE vehicles ADD COLUMN quantity INT DEFAULT 1");
    }
} catch (Exception $e) {
    // ignore — if DB user cannot alter schema, we'll continue and avoid failing later
    $hasQuantity = 0;
}

if ($action === 'update_quantity') {
    $qty = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
    if ($hasQuantity) {
        $stmt = $pdo->prepare('UPDATE vehicles SET quantity = ? WHERE id = ?');
        $stmt->execute([$qty, $vehicle_id]);
    }
}
elseif ($action === 'update_status') {
    $status = $_POST['status'] ?? 'available';
    $stmt = $pdo->prepare('UPDATE vehicles SET status = ? WHERE id = ?');
    $stmt->execute([$status, $vehicle_id]);
}
elseif ($action === 'delete_vehicle') {
    $stmt = $pdo->prepare('DELETE FROM vehicles WHERE id = ?');
    $stmt->execute([$vehicle_id]);
}
elseif ($action === 'add_vehicle') {
    $brand_id = isset($_POST['brand_id']) ? (int)$_POST['brand_id'] : null;
    $model = $_POST['model'] ?? '';
    $category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : null;
    $year = !empty($_POST['year']) ? (int)$_POST['year'] : null;
    $plate = $_POST['plate_number'] ?? null;
    $daily = isset($_POST['daily_rate']) ? (float)$_POST['daily_rate'] : 0.0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    $office_id = !empty($_POST['office_id']) ? (int)$_POST['office_id'] : null;

    if ($hasQuantity) {
        $stmt = $pdo->prepare('INSERT INTO vehicles (brand_id, model, category_id, year, plate_number, daily_rate, quantity, office_id) VALUES (?,?,?,?,?,?,?,?)');
        $stmt->execute([$brand_id, $model, $category_id, $year, $plate, $daily, $quantity, $office_id]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO vehicles (brand_id, model, category_id, year, plate_number, daily_rate, office_id) VALUES (?,?,?,?,?,?,?)');
        $stmt->execute([$brand_id, $model, $category_id, $year, $plate, $daily, $office_id]);
        // If quantity column couldn't be added, we silently continue — quantity will be unavailable.
    }
    $vehicleId = $pdo->lastInsertId();

    // Handle uploaded image
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
                $ins = $pdo->prepare('INSERT INTO vehicle_images (vehicle_id, url, caption) VALUES (?,?,?)');
                $ins->execute([$vehicleId, $url, null]);
            }
        }
    }
}

header('Location: admin_stats.php');
exit;
