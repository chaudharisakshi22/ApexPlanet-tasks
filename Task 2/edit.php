<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); exit();
}
$post_id = (int)$_GET['id'];
$uid = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT title, content FROM posts WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $post_id, $uid);
$stmt->execute();
$stmt->bind_result($title, $content);
if (!$stmt->fetch()) {
    echo "<div class='alert alert-danger'>Post not found or access denied. <a href='index.php'>Back</a></div>";
    exit();
}
$stmt->close();

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
    <title>Edit Post | Blog CRUD</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5" style="max-width:600px;">
    <div class="card">
    <div class="card-body">
    <h2 class="mb-4">Edit Post</h2>
    <?php if (!empty($message)): ?>
      <div class="alert alert-danger"><?= $message ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="mb-3">
            <input name="title" class="form-control" value="<?= htmlspecialchars($title) ?>" required>
        </div>
        <div class="mb-3">
            <textarea name="content" class="form-control" rows="6" required><?= htmlspecialchars($content) ?></textarea>
        </div>
        <div class="mb-3 d-flex">
            <a href="index.php" class="btn btn-link me-auto">Back</a>
            <button class="btn btn-primary ms-auto">Update Post</button>
        </div>
    </form>
    </div>
    </div>
</div>
</body>
</html>