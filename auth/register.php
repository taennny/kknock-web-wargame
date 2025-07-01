<?php
session_start();
require_once('../db.php');

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $name = trim($_POST["name"]);
  $email = trim($_POST["email"]);
  $password = $_POST["password"];
  $passwordConfirm = $_POST["password_confirm"];

  if (!$name || !$email || !$password || !$passwordConfirm) {
    $errors[] = "모든 항목을 입력해주세요.";
  }

  if ($password !== $passwordConfirm) {
    $errors[] = "비밀번호가 일치하지 않습니다.";
  }

  if (empty($errors)) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    try {
      $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
      $stmt->execute([$name, $email, $hashedPassword]);
      header("Location: login.php");
      exit();
    } catch (PDOException $e) {
      if ($e->getCode() == 23000) {
        $errors[] = "이미 존재하는 이메일입니다.";
      } else {
        $errors[] = "회원가입 중 오류 발생: " . $e->getMessage();
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>Sign-up</title>
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
    form input,
    form button {
      display: block;
      width: 100%;
      margin-top: 10px;
    }
    input {
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
        <p>Already have an account?</p>
        <a href="login.php">Login</a>
      </div>
    </div>

    <div class="main">
      <div class="container">
        <h2>Sign Up</h2>

        <?php if (!empty($errors)): ?>
          <div class="error-list">
            <ul>
              <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <form method="post">
          <input type="text" name="name" placeholder="Name" required>
          <input type="email" name="email" placeholder="Email" required>
          <input type="password" name="password" placeholder="Password" required>
          <input type="password" name="password_confirm" placeholder="Confirm Password" required>
          <button type="submit">Sign Up</button>
        </form>
      </div>
    </div>
  </div>
</body>
</html>
