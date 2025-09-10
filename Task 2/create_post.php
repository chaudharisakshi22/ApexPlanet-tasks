<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); exit();
}
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $uid = $_SESSION['user_id'];
    $stmt = $conn->prepare("INSERT INTO posts (title, content, user_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $title, $content, $uid);
    if ($stmt->execute()) {
        header("Location: index.php");
        exit();
    } else {
        $message = "Failed to create post.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Post | Blog CRUD</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5" style="max-width:600px;">
    <div class="card">
    <div class="card-body">
    <h2 class="mb-4">Create New Post</h2>
    <?php if ($message): ?>
      <div class="alert alert-danger"><?= $message ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="mb-3">
            <input name="title" class="form-control" placeholder="Title" required>
        </div>
        <div class="mb-3">
            <textarea name="content" class="form-control" placeholder="Content" rows="6" required></textarea>
        </div>
        <div class="mb-3 d-flex">
            <a href="index.php" class="btn btn-link me-auto">Back</a>
            <button class="btn btn-success ms-auto">Save Post</button>
        </div>
    </form>
    </div>
    </div>
</div>
</body>
</html>