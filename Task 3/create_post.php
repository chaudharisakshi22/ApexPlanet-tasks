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
    <title>New Post | MyBlog</title>
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
        .card-post {
            border: none;
            box-shadow: 0 4px 24px #cbb99222;
            border-radius: 22px;
            margin-top: 54px;
            max-width: 600px;
            background: #fff;
        }
        .card-post .form-label {
            font-weight: 500;
            color: #2a3142;
        }
        .btn-save {
            background: linear-gradient(90deg,#139be9,#ffd656);
            color: #233;
            font-weight: 600;
            box-shadow: 0 2px 12px #ffd65644;
            border: none;
            border-radius: 14px;
            font-size: 1.05em;
        }
        .btn-save:hover {
            background: #139be9;
            color: #fff;
        }
        @media (max-width: 480px) {
            .card-post {margin-top: 20px;}
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
  <div class="card card-post p-4">
    <div class="mb-2 text-center">
      <h2 class="fw-bold mb-1"><i class="bi bi-plus-lg me-1"></i> New Post</h2>
      <div class="text-muted">Share your thoughts with the world.</div>
    </div>
    <?php if ($message): ?>
      <div class="alert alert-danger"><?= $message ?></div>
    <?php endif; ?>
    <form method="post">
      <div class="mb-3">
        <label class="form-label">Title</label>
        <input name="title" class="form-control" required autofocus>
      </div>
      <div class="mb-3">
        <label class="form-label">Content</label>
        <textarea name="content" class="form-control" rows="6" required></textarea>
      </div>
      <div class="mb-3 d-flex gap-2">
        <a href="index.php" class="btn btn-outline-secondary flex-fill"><i class="bi bi-arrow-left"></i> Back</a>
        <button class="btn btn-save flex-fill"><i class="bi bi-save2 me-1"></i> Save Post</button>
      </div>
    </form>
  </div>
</div>
</body>
</html>