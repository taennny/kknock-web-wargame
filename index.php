<?php
session_start();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>게시판 선택</title>
  <style>
    * {
      box-sizing: border-box;
    }
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
    .nav-top {
      display: flex;
      flex-direction: column;
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
    .nav-bottom p {
      margin: 0 0 8px;
      color: #ccc;
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
  position: relative;
  background-color: white;
    }

    .main h1 {
     position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  font-size: 2rem;
  color: #ccc; /* 흐릿한 회색 */
  text-align: center;
    }

    .board-info {
      margin-bottom: 30px;
    }

    .board-info h2 {
      margin-bottom: 10px;
    }

    .board-info p {
      color: #555;
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
          <p>환영합니다, <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong>님!</p>
          <a href="/auth/logout.php">Logout</a>
        <?php else: ?>
          <a href="/auth/login.php">Login</a><br>
          <a href="/auth/register.php">Sign-up</a>
        <?php endif; ?>
      </div>
    </div>

    <div class="main">
      <h1>게시판을 선택하세요</h1>
    </div>
  </div>
</body>
</html>
