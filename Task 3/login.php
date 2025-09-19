<?php
require 'config.php';

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($uid, $password_hash);
    if ($stmt->fetch() && password_verify($password, $password_hash)) {
        $_SESSION['user_id'] = $uid;
        $_SESSION['username'] = $username;
        header("Location: index.php");
        exit();
    } else {
        $message = "Invalid credentials.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | MyBlog</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(114deg, #f6fafd 0%, #f5f2ef 100%);
            min-height: 100vh;
            font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
        }
        .navbar {
            background: linear-gradient(90deg, #1b4b84, #139be9 70%, #ffd656 100%);
            box-shadow: 0 1px 8px #b6e2f91a;
        }
        .navbar-brand {
            font-weight: bold;
            color: #ffd656 !important;
            letter-spacing: 1px;
            font-size: 1.45rem;
        }
        .navbar-nav .nav-link {
            color: #fff !important;
            font-weight: 500;
        }
        .navbar-nav .nav-link.active, .navbar-nav .nav-link:hover {
            color: #ffd656 !important;
        }
        .card-auth {
            border: none;
            box-shadow: 0 4px 24px #cbb99222;
            border-radius: 20px;
            margin-top: 60px;
            max-width: 420px;
            background: #fff;
        }
        .card-auth .form-label {
            font-weight: 500;
            color: #2a3142;
        }
        .btn-auth {
            background: linear-gradient(90deg,#139be9,#ffd656);
            color: #233;
            font-weight: 600;
            box-shadow: 0 2px 12px #ffd65633;
            border: none;
            border-radius: 14px;
            font-size: 1.05em;
        }
        .btn-auth:hover {
            background: #139be9;
            color: #fff;
        }
        .auth-switch {
            font-size: 0.98em;
        }
        @media (max-width: 480px) {
            .card-auth {margin-top: 30px;}
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg mb-4">
  <div class="container">
    <a class="navbar-brand" href="index.php"><i class="bi bi-journals me-2"></i>MyBlog</a>
  </div>
</nav>
<div class="container d-flex flex-column align-items-center">
  <div class="card card-auth p-4">
    <div class="mb-3 text-center">
      <h2 class="mb-1 fw-bold"><i class="bi bi-box-arrow-in-right me-1"></i> Login</h2>
      <div class="text-muted">Welcome back. Please sign in.</div>
    </div>
    <?php if ($message): ?>
      <div class="alert alert-danger"><?= $message ?></div>
    <?php endif; ?>
    <form method="post" autocomplete="off">
      <div class="mb-3">
        <label class="form-label">Username</label>
        <input class="form-control" name="username" required autofocus>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" class="form-control" name="password" required>
      </div>
      <button class="btn btn-auth w-100 mb-2">Login</button>
    </form>
    <div class="text-center auth-switch">
      Don't have an account?
      <a href="register.php" class="fw-semibold text-primary">Register</a>
    </div>
  </div>
</div>
</body>
</html>