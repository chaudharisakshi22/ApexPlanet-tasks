<?php
require 'config.php';

// --- SEARCH HANDLING ---
$search_query = '';
$where = '';
if (isset($_GET['q']) && trim($_GET['q']) !== '') {
    $search_query = trim($_GET['q']);
    $where = "WHERE posts.title LIKE ? OR posts.content LIKE ?";
}

// --- PAGINATION SETTINGS ---
$posts_per_page = 5;
$page = isset($_GET['page']) && ctype_digit($_GET['page']) && $_GET['page'] > 0 ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $posts_per_page;

// --- COUNT TOTAL (possible search) ---
if ($where) {
    $count_stmt = $conn->prepare("SELECT COUNT(*) FROM posts $where");
    $search_like = "%" . $search_query . "%";
    $count_stmt->bind_param("ss", $search_like, $search_like);
    $count_stmt->execute();
    $count_stmt->bind_result($total_posts);
    $count_stmt->fetch();
    $count_stmt->close();
} else {
    $result = $conn->query("SELECT COUNT(*) AS cnt FROM posts");
    $total_posts = $result->fetch_assoc()['cnt'];
}

// --- FETCH POSTS (possible search, LIMIT & OFFSET) ---
if ($where) {
    $stmt = $conn->prepare(
        "SELECT posts.*, users.username 
         FROM posts 
           LEFT JOIN users ON posts.user_id = users.id 
         $where
         ORDER BY created_at DESC
         LIMIT $posts_per_page OFFSET $offset"
    );
    $stmt->bind_param("ss", $search_like, $search_like);
    $stmt->execute();
    $posts = $stmt->get_result();
} else {
    $posts = $conn->query(
        "SELECT posts.*, users.username 
         FROM posts 
           LEFT JOIN users ON posts.user_id = users.id 
         ORDER BY created_at DESC
         LIMIT $posts_per_page OFFSET $offset"
    );
}

// --- PAGINATION CALC ---
$total_pages = max(1, ceil($total_posts / $posts_per_page));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MyBlog - Modern CRUD App</title>
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
        margin-right: 4px;
        transition: color 0.15s;
    }
    .navbar-nav .nav-link.active, .navbar-nav .nav-link:hover {
        color: #ffd656 !important;
    }
    .header-title {
        color: #2a3142;
        font-size: 1.85rem;
        font-weight: 700;
        letter-spacing: 0.5px;
    }
    .search-box .input-group {
        box-shadow: 0 4px 12px #bce3fc1a;
        border-radius: 16px;
        overflow: hidden;
        background: #fff;
    }
    .search-box input {
        border: none !important;
        background: none;
    }
    .search-box button {
        background: #139be9;
        border: none;
        color: #fff;
        font-weight: 600;
        font-size: 1.03rem;
        border-radius: 0 12px 12px 0;
        padding-inline: 22px;
        transition: background .15s;
    }
    .search-box button:hover {
        background: #1968a6;
        color: #fff;
    }
    .blog-card {
        border: none;
        background: #fff;
        border-left: 6px solid #ffd656;
        box-shadow: 0 4px 24px #cbb99222;
        border-radius: 18px;
        transition: box-shadow 0.22s, transform 0.15s;
    }
    .blog-card:hover {
        box-shadow: 0 8px 32px #b6e2f955;
        transform: translateY(-2px) scale(1.012);
    }
    .card-title {
        color: #27639c;
        font-weight: 700;
    }
    .card-footer {
        background: transparent;
        border-top: none;
        padding-bottom: 10px;
    }
    .edit-btn, .delete-btn {
        border-radius: 14px;
        font-size: 0.97rem;
    }
    .edit-btn {
        color: #097883;
        border: 1px solid #42c6e4;
        background: #e2faff;
    }
    .edit-btn:hover {
        color: #fff;
        background: #17a2b8;
    }
    .delete-btn {
        color: #7a2424;
        border: 1px solid #fa8484;
        background: #ffeaea;
    }
    .delete-btn:hover {
        color: #fff;
        background: #e42a2a;
    }
    .pagination .page-link {
        color: #1b4b84;
        background: #f7fafd;
        border: none;
        border-radius: 16px;
        margin: 0 2px;
        transition: all 0.14s;
    }
    .pagination .active .page-link,
    .pagination .page-link:focus,
    .pagination .page-link:hover {
        background: #ffd656;
        color: #1b3547;
        font-weight: 700;
        border: none;
    }
    .badge {
        font-size: .98em;
        border-radius: 13px;
        padding: 0.35em 1em;
    }
    @media (max-width: 576px) {
        .header-title {font-size: 1.3rem;}
        .blog-card {margin-bottom: 26px;}
    }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg mb-4">
  <div class="container">
    <a class="navbar-brand" href="index.php"><i class="bi bi-journals me-2"></i>MyBlog</a>
    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navyNav"><span class="navbar-toggler-icon"></span></button>
    <div class="collapse navbar-collapse" id="navyNav">
      <ul class="navbar-nav ms-auto">
        <?php if (isset($_SESSION['user_id'])): ?>
            <li class="nav-item"><span class="nav-link">Hi, <?= htmlspecialchars($_SESSION['username']) ?></span></li>
            <li class="nav-item"><a class="nav-link" href="create_post.php"><i class="bi bi-plus-lg me-1"></i>New</a></li>
            <li class="nav-item"><a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right me-1"></i>Logout</a></li>
        <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="login.php"><i class="bi bi-box-arrow-in-right me-1"></i>Login</a></li>
            <li class="nav-item"><a class="nav-link" href="register.php"><i class="bi bi-person-plus me-1"></i>Register</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<div class="container py-3 pb-5">
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap">
      <div class="header-title mb-2"><i class="bi bi-list-task me-1"></i> Blog Posts</div>
    </div>
    <!-- SEARCH FORM -->
    <form method="get" class="mb-4 search-box mx-auto" style="max-width:520px;">
        <div class="input-group">
            <input type="text" class="form-control" name="q" value="<?= htmlspecialchars($search_query) ?>" placeholder="Search posts by title or content..." />
            <button class="btn" type="submit"><i class="bi bi-search"></i> Search</button>
        </div>
    </form>
    <?php if ($search_query): ?>
      <div class="mb-2 text-muted">
        Searching for: <strong><?= htmlspecialchars($search_query)?></strong>
        <a href="index.php" class="ms-2 badge bg-warning text-dark">Clear</a>
      </div>
    <?php endif; ?>
    <?php if ($posts->num_rows > 0): ?>
        <?php while ($row = $posts->fetch_assoc()): ?>
            <div class="card mb-4 blog-card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title"><?= htmlspecialchars($row['title']) ?></h4>
                    <p class="card-text"><?= nl2br(htmlspecialchars($row['content'])) ?></p>
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center border-0 text-muted small">
                    <span>
                        <i class="bi bi-person-fill"></i> <?= htmlspecialchars($row['username'] ?? 'Unknown') ?>
                        <span class="mx-2">&middot;</span>
                        <i class="bi bi-clock"></i> <?= date('M j, Y H:i', strtotime($row['created_at'])) ?>
                    </span>
                    <?php if (isset($_SESSION['user_id']) && $row['user_id'] == $_SESSION['user_id']): ?>
                        <span class="d-inline-flex gap-1">
                            <a href="edit_post.php?id=<?= $row['id'] ?>" class="edit-btn btn btn-sm"><i class="bi bi-pencil"></i> Edit</a>
                            <a href="?delete=<?= $row['id'] ?>&q=<?= urlencode($search_query) ?>&page=<?= $page ?>"
                               onclick="return confirm('Delete post?');"
                               class="delete-btn btn btn-sm"><i class="bi bi-trash"></i> Delete</a>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="alert alert-warning shadow-sm">No posts found.</div>
    <?php endif; ?>
    <!-- PAGINATION LINKS -->
    <?php if ($total_pages > 1): ?>
      <nav>
        <ul class="pagination justify-content-center mt-4">
            <?php $getQuery = $search_query ? "&q=" . urlencode($search_query) : ""; ?>
            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $page-1 ?><?= $getQuery ?>">&laquo;</a>
            </li>
            <?php
            $start = max(1, $page - 2); $end = min($total_pages, $page + 2);
            if ($start > 1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
            for ($i = $start; $i <= $end; $i++): ?>
                <li class="page-item <?= $i==$page ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?><?= $getQuery ?>"><?= $i ?></a>
                </li>
            <?php endfor;
            if ($end < $total_pages) echo '<li class="page-item disabled"><span class="page-link">...</span></li>'; ?>
            <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $page+1 ?><?= $getQuery ?>">&raquo;</a>
            </li>
        </ul>
      </nav>
    <?php endif; ?>
</div>
<!-- Bootstrap JS (for navbar collapse) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>