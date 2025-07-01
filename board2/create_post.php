<?php
session_start();
require_once(__DIR__ . '/../db.php');

// 로그인 안한 사용자는 접근 차단
if (!isset($_SESSION['user_id'])) {
  header("Location: ../auth/login.php");
  exit();
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $title = trim($_POST["title"]);
  $content = trim($_POST["content"]);
  $userId = $_SESSION["user_id"];

  // 파일 업로드
  $fileName = null;
  if (!empty($_FILES["file"]["name"])) {
    $uploadDir = "../upload/board2/";
    $originalName = basename($_FILES["file"]["name"]);
    $targetPath = $uploadDir . $originalName;

    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetPath)) {
      $fileName = $originalName;
    } else {
      $errors[] = "파일 업로드 실패";
    }
  }

  if (!$title || !$content) {
    $errors[] = "제목과 내용을 입력해주세요.";
  }

  if (empty($errors)) {
    $stmt = $pdo->prepare("INSERT INTO posts_board2 (user_id, title, content, file_name) VALUES (?, ?, ?, ?)");
    $stmt->execute([$userId, $title, $content, $fileName]);
    header("Location: index.php");
    exit();
  }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>게시글 작성 - Board2</title>
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
    form label,
    form input[type="text"],
    form textarea,
    form input[type="file"],
    form button {
      display: block;
      width: 100%;
      margin-top: 10px;
    }
    input[type="text"],
    textarea,
    input[type="file"] {
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }
    button[type="submit"] {
      padding: 10px 16px;
      margin-top: 20px;
      background-color: #3498db;
      border: none;
      border-radius: 4px;
      color: white;
      font-size: 1rem;
      cursor: pointer;
    }
    .errors {
      margin-bottom: 20px;
      padding: 10px;
      border: 1px solid #e74c3c;
      background-color: #fdecea;
      color: #e74c3c;
      border-radius: 4px;
    }
    .errors ul {
      margin: 0;
      padding-left: 20px;
    }
    .back-link {
      margin-top: 30px;
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
        <h2>Create Post (Board 2)</h2>

        <?php if (!empty($errors)): ?>
          <div class="errors">
            <ul>
              <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
          <input type="text" name="title" placeholder="Title" required>
          <textarea name="content" placeholder="Content" rows="6" required></textarea>
          <input type="file" name="file">
          <button type="submit">Done</button>
        </form>

        <div class="back-link">
          <a href="index.php">← Back</a>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
