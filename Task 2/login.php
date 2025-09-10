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
    <title>Login | Blog CRUD</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card mx-auto" style="max-width:400px;">
    <div class="card-body">
    <h2 class="mb-4 text-center">Login</h2>
    <?php if ($message): ?>
      <div class="alert alert-danger"><?= $message ?></div>
    <?php endif; ?>
    <form method="post" autocomplete="off">
        <div class="mb-3">
            <input class="form-control" name="username" placeholder="Username" required>
        </div>
        <div class="mb-3">
            <input type="password" class="form-control" name="password" placeholder="Password" required>
        </div>
        <button class="btn btn-primary w-100">Login</button>
    </form>
    <div class="mt-3"><a href="register.php">Register</a></div>
    </div>
    </div>
</div>
</body>
</html>