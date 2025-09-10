<?php
require 'config.php';

if (isset($_GET['delete']) && isset($_SESSION['user_id'])) {
    $id = (int)$_GET['delete'];
    $uid = $_SESSION['user_id'];
    $stmt = $conn->prepare("DELETE FROM posts WHERE id=? AND user_id=?");
    $stmt->bind_param("ii", $id, $uid);
    $stmt->execute();
    header("Location: index.php");
    exit();
}
$posts = $conn->query(
    "SELECT posts.*, users.username FROM posts LEFT JOIN users ON posts.user_id = users.id ORDER BY created_at DESC"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Blog CRUD</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="index.php">MyBlog</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <?php if (isset($_SESSION['user_id'])): ?>
            <li class="nav-item"><span class="nav-link">Welcome, <?= htmlspecialchars($_SESSION['username']) ?></span></li>
            <li class="nav-item"><a class="nav-link" href="create_post.php">New Post</a></li>
            <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
        <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
            <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<div class="container py-5">
    <h2 class="mb-4">Blog Posts</h2>
    <?php 
    while($row = $posts->fetch_assoc()): ?>
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <h4 class="card-title"><?= htmlspecialchars($row['title']) ?></h4>
                <p class="card-text"><?= nl2br(htmlspecialchars($row['content'])) ?></p>
            </div>
            <div class="card-footer text-muted d-flex justify-content-between align-items-center">
                <span>By <?= htmlspecialchars($row['username'] ?? 'Unknown') ?> | <?= $row['created_at'] ?></span>
                <?php if (isset($_SESSION['user_id']) && $row['user_id'] == $_SESSION['user_id']): ?>
                  <span>
                    <a href="edit_post.php?id=<?= $row['id'] ?>" class="btn btn-outline-primary btn-sm">Edit</a>
                    <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete post?');" class="btn btn-outline-danger btn-sm">Delete</a>
                  </span>
                <?php endif; ?>
            </div>
        </div>
    <?php endwhile; ?>
</div>
</body>
</html>