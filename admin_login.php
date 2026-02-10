<?php
session_start();
require_once 'db_connect.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';
    // TODO: replace with secure credential storage
    $validUser = 'admin';
    $validPass = 'password123';
    if ($user === $validUser && $pass === $validPass) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: admin_stats.php');
        exit;
    } else {
        $error = 'Invalid credentials';
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Login</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="index.css">
</head>
<body>
<div class="container" style="margin-top:140px; max-width:420px;">
  <div class="card p-4 shadow">
    <h4 class="mb-3">Staff Login</h4>
    <?php if ($error): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post">
      <div class="mb-3">
        <label class="form-label">Username</label>
        <input name="username" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <div class="d-flex justify-content-between">
        <button class="btn btn-danger">Sign in</button>
        <a class="btn btn-link" href="home.php">Back</a>
      </div>
    </form>
    <div class="mt-3 text-muted small">Default: admin / password123 — change in <code>admin_login.php</code></div>
  </div>
</div>
</body>
</html>
