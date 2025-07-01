<?php
session_start();
require_once(__DIR__ . '/../db.php');

$commentId = $_GET['id'] ?? null;
$postId = $_GET['post_id'] ?? null;

if (!$commentId || !$postId || !isset($_SESSION['user_id'])) {
  echo "잘못된 접근입니다.";
  exit();
}

$stmt = $pdo->prepare("SELECT * FROM comments_board2 WHERE id = ?");
$stmt->execute([$commentId]);
$comment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$comment || $comment['user_id'] != $_SESSION['user_id']) {
  echo "접근 권한이 없습니다.";
  exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $newContent = trim($_POST['content'] ?? '');
  if ($newContent) {
    $stmt = $pdo->prepare("UPDATE comments_board2 SET content = ? WHERE id = ?");
    $stmt->execute([$newContent, $commentId]);
    header("Location: view.php?id=$postId");
    exit();
  }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>✏️ 댓글 수정</title>
  <style>
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #f5f5f5;
    }
    .layout {
      display: flex;
      height: 100vh;
    }
    .sidebar {
      width: 220px;
      background-color: #2c3e50;
      color: white;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      padding: 30px 20px;
    }
    .nav-top h2 {
      font-size: 1.5rem;
      margin-bottom: 30px;
    }
    .nav-top a {
      display: block;
      margin: 10px 0;
      color: white;
      text-decoration: none;
      font-size: 1rem;
    }
    .nav-top a:hover {
      text-decoration: underline;
    }
    .nav-bottom {
      font-size: 0.9rem;
      margin-top: 20px;
    }
    .nav-bottom a {
      color: white;
      text-decoration: none;
    }
    .nav-bottom a:hover {
      text-decoration: underline;
    }
    .main {
      flex: 1;
      background-color: white;
      padding: 40px;
      overflow-y: auto;
    }
    .container {
      max-width: 600px;
      margin: auto;
    }
    h3 {
      margin-bottom: 20px;
    }
    form textarea {
      width: 100%;
      padding: 10px;
      font-size: 1rem;
      border: 1px solid #ccc;
      border-radius: 4px;
      resize: vertical;
    }
    form button {
      margin-top: 10px;
      padding: 8px 14px;
      background-color: #3498db;
      color: white;
      border: none;
      border-radius: 4px;
      font-size: 1rem;
      cursor: pointer;
    }
    .back-link {
      display: inline-block;
      margin-top: 20px;
    }
    .back-link a {
      color: #3498db;
      text-decoration: none;
    }
  </style>
</head>
<body>
  <div class="layout">
    <div class="sidebar">
      <div class="nav-top">
        <h2>BOARD</h2>
        <a href="/board1/index.php">BOARD 1</a>
        <a href="/board2/index.php">BOARD 2</a>
      </div>
      <div class="nav-bottom">
        <?php if (isset($_SESSION['user_id'])): ?>
          <p>환영합니다, <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong></p>
          <a href="/auth/logout.php">Logout</a>
        <?php else: ?>
          <a href="/auth/login.php">Login</a><br>
          <a href="/auth/register.php">Sign-up</a>
        <?php endif; ?>
      </div>
    </div>

    <div class="main">
      <div class="container">
        <h3>Edit Comment</h3>
        <form method="post">
          <textarea name="content" rows="3" required><?= htmlspecialchars($comment['content']) ?></textarea><br>
          <button type="submit">Done</button>
        </form>
        <div class="back-link">
          <a href="view.php?id=<?= $postId ?>">← Back</a>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
