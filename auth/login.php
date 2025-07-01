<?php
session_start();
require_once('../db.php');

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email']);
  $password = $_POST['password'];

  if (!$email || !$password) {
    $errors[] = "이메일과 비밀번호를 모두 입력해주세요.";
  } else {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['user_name'] = $user['name'];
      header("Location: ../index.php");
      exit();
    } else {
      $errors[] = "이메일 또는 비밀번호가 올바르지 않습니다.";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
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
      max-width: 400px;
      margin: auto;
    }
    h2 {
      margin-bottom: 20px;
    }
    form input[type="email"],
    form input[type="password"],
    form button {
      display: block;
      width: 100%;
      margin-top: 10px;
    }
    input[type="email"],
    input[type="password"] {
      padding: 10px;
      font-size: 1rem;
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
        <p>Not registered?</p>
        <a href="register.php">Sign-up</a>
      </div>
    </div>

    <div class="main">
      <div class="container">
        <h2>Login</h2>

        <?php if (!empty($errors)): ?>
          <div class="error-list">
            <ul>
              <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <form method="post" action="">
          <input type="email" name="email" placeholder="Email" required>
          <input type="password" name="password" placeholder="Password" required>
          <button type="submit">Login</button>
        </form>
      </div>
    </div>
  </div>
</body>
</html>
