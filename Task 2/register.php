<?php
require 'config.php';

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Check if username already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
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
    <title>Register | Blog CRUD</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card mx-auto" style="max-width:400px;">
    <div class="card-body">
    <h2 class="mb-4 text-center">Register</h2>
    <?php if ($message): ?>
      <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>
    <form method="post" autocomplete="off">
        <div class="mb-3">
            <input class="form-control" name="username" placeholder="Username" required>
        </div>
        <div class="mb-3">
            <input type="password" class="form-control" name="password" placeholder="Password" required>
        </div>
        <button class="btn btn-success w-100">Register</button>
    </form>
    <div class="mt-3"><a href="login.php">Login</a></div>
    </div>
    </div>
</div>
</body>
</html>