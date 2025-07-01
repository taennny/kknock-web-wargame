<?php
session_start();
require_once(__DIR__ . '/../db.php');

if (!isset($_SESSION['user_id'])) {
  header("Location: ../auth/login.php");
  exit();
}

$userId = $_SESSION['user_id'];
$postId = $_GET['id'] ?? null;

$stmt = $pdo->prepare("SELECT * FROM posts_board1 WHERE id = ?");
$stmt->execute([$postId]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post || $post['user_id'] != $userId) {
  echo "접근 권한이 없습니다.";
  exit();
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $title = trim($_POST["title"]);
  $content = trim($_POST["content"]);
  $deleteFile = isset($_POST["delete_file"]);
  $fileName = $post['file_name'];

  if ($deleteFile && $fileName) {
    $filePath = "../upload/board1/" . $fileName;
    if (file_exists($filePath)) unlink($filePath);
    $fileName = null;
  }

  if (!empty($_FILES["file"]["name"])) {
    $uploadDir = "../upload/board1/";
    $newFile = basename($_FILES["file"]["name"]);
    $targetPath = $uploadDir . $newFile;

    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetPath)) {
      if ($fileName && file_exists($uploadDir . $fileName)) {
        unlink($uploadDir . $fileName);
      }
      $fileName = $newFile;
    } else {
      $errors[] = "파일 업로드 실패";
    }
  }

  if (!$title || !$content) {
    $errors[] = "제목과 내용을 입력해주세요.";
  }

  if (empty($errors)) {
    $stmt = $pdo->prepare("UPDATE posts_board1 SET title = ?, content = ?, file_name = ? WHERE id = ?");
    $stmt->execute([$title, $content, $fileName, $postId]);
    header("Location: view.php?id=$postId");
    exit();
  }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>✏️ 게시글 수정</title>
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
      max-width: 800px;
      margin: auto;
    }
    h2 {
      margin-bottom: 20px;
    }
    form label {
      display: block;
      margin-top: 15px;
      font-weight: bold;
    }
    input[type="text"],
    textarea,
    input[type="file"] {
      width: 100%;
      padding: 8px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }
    .file-info {
      margin-top: 10px;
      font-size: 0.95rem;
      color: #555;
    }
    button[type="submit"] {
      margin-top: 20px;
      padding: 10px 16px;
      background-color: #3498db;
      border: none;
      border-radius: 4px;
      color: white;
      font-size: 1rem;
      cursor: pointer;
    }
    .error-list {
      margin-top: 20px;
      padding: 10px;
      border: 1px solid #e74c3c;
      background-color: #fdecea;
      color: #e74c3c;
      border-radius: 4px;
    }
    .error-list li {
      margin-left: 20px;
      list-style-type: disc;
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
      <div class="container">
        <h2>Edit Post</h2>

        <form method="post" enctype="multipart/form-data">
          <label for="title">Title</label>
          <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>" required>

          <label for="content">Content</label>
          <textarea name="content" rows="5" required><?= htmlspecialchars($post['content']) ?></textarea>

          <?php if ($post['file_name']): ?>
            <div class="file-info">
              현재 첨부파일: <strong><?= htmlspecialchars($post['file_name']) ?></strong><br>
              <label>
                <input type="checkbox" name="delete_file" value="1"> 첨부파일 삭제
              </label>
            </div>
          <?php endif; ?>

          <label for="file">새 파일 업로드</label>
          <input type="file" name="file">

          <button type="submit">Done</button>
        </form>

        <?php if (!empty($errors)): ?>
          <ul class="error-list">
            <?php foreach ($errors as $e): ?>
              <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>

        <a class="back-link" href="view.php?id=<?= $postId ?>">← Back</a>
      </div>
    </div>
  </div>
</body>
</html>
