<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); exit();
}
$post_id = (int)$_GET['id'];
$uid = $_SESSION['user_id'];

// Fetch the post to edit
$stmt = $conn->prepare("SELECT title, content FROM posts WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $post_id, $uid);
$stmt->execute();
$stmt->bind_result($title, $content);
if (!$stmt->fetch()) {
    // Not found or not authorized
    echo "<!DOCTYPE html><html><head><link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css'><body>
    <div class='container mt-5'><div class='alert alert-danger'>Post not found or access denied. <a href='index.php'>Back</a></div></div></body></html>";
    exit();
}
$stmt->close();

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_title = trim($_POST['title']);
    $new_content = trim($_POST['content']);
    $stmt = $conn->prepare("UPDATE posts SET title=?, content=? WHERE id=? AND user_id=?");
    $stmt->bind_param("ssii", $new_title, $new_content, $post_id, $uid);
    if ($stmt->execute()) {
        header("Location: index.php");
        exit();
    } else {
        $message = "Update failed.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Post | MyBlog</title>
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
      <h2 class="fw-bold mb-1"><i class="bi bi-pencil-square me-1"></i> Edit Post</h2>
      <div class="text-muted">Modify your post below and save changes.</div>
    </div>
    <?php if ($message): ?>
      <div class="alert alert-danger"><?= $message ?></div>
    <?php endif; ?>
    <form method="post">
      <div class="mb-3">
        <label class="form-label">Title</label>
        <input name="title" class="form-control" value="<?= htmlspecialchars($title) ?>" required autofocus>
      </div>
      <div class="mb-3">
        <label class="form-label">Content</label>
        <textarea name="content" class="form-control" rows="6" required><?= htmlspecialchars($content) ?></textarea>
      </div>
      <div class="mb-3 d-flex gap-2">
        <a href="index.php" class="btn btn-outline-secondary flex-fill"><i class="bi bi-arrow-left"></i> Back</a>
        <button class="btn btn-save flex-fill"><i class="bi bi-pencil"></i> Update Post</button>
      </div>
    </form>
  </div>
</div>
</body>
</html>