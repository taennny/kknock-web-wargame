<?php
session_start();
require_once(__DIR__ . '/../db.php');

if (!isset($_SESSION['user_id'])) {
  header("Location: ../auth/login.php");
  exit();
}

$userId = $_SESSION['user_id'];
$id = $_GET['id'] ?? null;

if (!$id) {
  echo "잘못된 접근입니다.";
  exit();
}

// 게시글 불러오기
$stmt = $pdo->prepare("SELECT p.*, u.name FROM posts_board1 p 
                       JOIN users u ON p.user_id = u.id 
                       WHERE p.id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
  echo "게시글을 찾을 수 없습니다.";
  exit();
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>📄 게시글 보기</title>
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
    .card {
      background-color: #fff;
      border: 1px solid #ddd;
      border-radius: 8px;
      padding: 20px;
      max-width: 800px;
      margin: auto;
      box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    .card p {
      margin: 10px 0;
    }
    .meta {
      color: #777;
      font-size: 0.9rem;
    }
    .content {
      margin: 20px 0;
      padding: 10px;
      background-color: #f9f9f9;
      white-space: pre-wrap;
    }
    .file-download {
      margin: 10px 0;
    }
    .post-actions {
      margin: 15px 0;
    }
    .post-actions a {
      margin-right: 10px;
      color: #3498db;
      text-decoration: none;
    }
    .comment-form textarea {
      width: 100%;
      padding: 8px;
      margin-top: 10px;
      font-family: inherit;
    }
    .comment-form button {
      margin-top: 5px;
      padding: 6px 12px;
    }
    .comments {
      list-style: none;
      padding: 0;
    }
    .comments li {
      margin-top: 15px;
      padding: 10px;
      border-top: 1px solid #eee;
    }
    .comments small {
      display: block;
      color: #888;
      margin-top: 5px;
    }
    .back-link {
      display: inline-block;
      margin-top: 30px;
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
          <p>Welcome, <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong></p>
          <a href="/auth/logout.php">Logout</a>
        <?php else: ?>
          <a href="/auth/login.php">Login</a><br>
          <a href="/auth/register.php">Sign-up</a>
        <?php endif; ?>
      </div>
    </div>

    <div class="main">
      <div class="card">
        <p><strong>Title:</strong> <?= htmlspecialchars($post['title']) ?></p>
        <p class="meta"><strong>Author:</strong> <?= htmlspecialchars($post['name']) ?> | <strong>Date:</strong> <?= $post['created_at'] ?></p>

        <div class="content"><?= nl2br(htmlspecialchars($post['content'])) ?></div>

        <?php if ($post['file_name']): ?>
          <p class="file-download"><strong>첨부파일:</strong>
            <a href="../upload/board1/<?= urlencode($post['file_name']) ?>" download>
              <?= htmlspecialchars($post['file_name']) ?>
            </a>
          </p>
        <?php endif; ?>
        
        <hr>
        <h3>Comment</h3>

        <!-- 댓글 등록 -->
        <form method="post" action="comment_create.php" class="comment-form">
          <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
          <textarea name="content" rows="2" placeholder="Write Comment..." required></textarea><br>
          <button type="submit">Submit</button>
        </form>

        <!-- 댓글 목록 -->
        <?php
        $stmt = $pdo->prepare("SELECT c.*, u.name FROM comments_board1 c 
                               JOIN users u ON c.user_id = u.id 
                               WHERE c.post_id = ? ORDER BY c.created_at ASC");
        $stmt->execute([$post['id']]);
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <ul class="comments">
          <?php foreach ($comments as $comment): ?>
            <li>
              <strong><?= htmlspecialchars($comment['name']) ?>:</strong><br>
              <?= nl2br(htmlspecialchars($comment['content'])) ?>
              <small><?= $comment['created_at'] ?></small>

              <?php if ($userId == $comment['user_id']): ?>
                <a href="comment_edit.php?id=<?= $comment['id'] ?>&post_id=<?= $post['id'] ?>"><button type="button">edit</button></a>
                <a href="comment_delete.php?id=<?= $comment['id'] ?>&post_id=<?= $post['id'] ?>" onclick="return confirm('댓글을 삭제할까요?')"><button type="button">delete</button></a>
              <?php endif; ?>
            </li>
          <?php endforeach; ?>
        </ul>

        <a class="back-link" href="index.php">← Back</a>
      </div>
    </div>
  </div>
</body>
</html>
