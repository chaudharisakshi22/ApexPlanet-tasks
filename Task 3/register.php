<?php
require 'config.php';

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute(); $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $message = "Username already taken!";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?,?)");
        $stmt->bind_param("ss", $username, $password_hash);
        if ($stmt->execute()) {
            $message = "Registration successful! <a href='login.php'>Login here.</a>";
        } else {
            $message = "Registration failed.";
        }
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register | MyBlog</title>
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
      <h2 class="mb-1 fw-bold"><i class="bi bi-person-plus me-1"></i> Register</h2>
      <div class="text-muted">Create a new account. It's free!</div>
    </div>
    <?php if ($message): ?>
      <div class="alert alert-info"><?= $message ?></div>
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
      <button class="btn btn-auth w-100 mb-2">Register</button>
    </form>
    <div class="text-center auth-switch">
      Already have an account?
      <a href="login.php" class="fw-semibold text-primary">Login</a>
    </div>
  </div>
</div>
</body>
</html>