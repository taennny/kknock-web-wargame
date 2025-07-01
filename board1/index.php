<?php
session_start();
require_once(__DIR__ . '/../db.php');

if (!isset($_SESSION['user_id'])) {
  header("Location: ../auth/login.php");
  exit();
}

$userId = $_SESSION['user_id'];
$search = $_GET['search'] ?? '';
$sort = ($_GET['sort'] ?? 'desc') === 'asc' ? 'ASC' : 'DESC';

$sql = "SELECT p.*, u.name FROM posts_board1 p
        JOIN users u ON p.user_id = u.id";
$params = [];

if ($search) {
  $sql .= " WHERE p.title LIKE ? OR u.name LIKE ?";
  $params[] = "%$search%";
  $params[] = "%$search%";
}

$sql .= " ORDER BY p.created_at $sort";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>📝 Board 1</title>
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
    .top-actions {
      margin-bottom: 20px;
    }
    .button {
      padding: 8px 12px;
      background-color: #3498db;
      color: white;
      border: none;
      border-radius: 4px;
      text-decoration: none;
    }
    form {
      margin-bottom: 20px;
    }
    input[type="text"], select {
      padding: 6px;
      margin-right: 8px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    th, td {
      padding: 10px;
      border-bottom: 1px solid #ddd;
      text-align: left;
    }
    th {
      background-color: #f0f0f0;
    }
    a.home-link {
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
      <div class="top-actions">
        <a class="button" href="create_post.php">Create Post</a>
      </div>

      <form method="get">
        <input type="text" name="search" placeholder="Search by title or author" value="<?= htmlspecialchars($search) ?>">
        <select name="sort">
          <option value="desc" <?= $sort === 'DESC' ? 'selected' : '' ?>>Sort by Newest</option>
          <option value="asc" <?= $sort === 'ASC' ? 'selected' : '' ?>>Sort by Oldest</option>
        </select>
        <button type="submit">Search</button>
      </form>

      <table>
        <tr>
          <th>Title</th>
          <th>Author</th>
          <th>Date</th>
          <th></th>
        </tr>

        <?php foreach ($posts as $post): ?>
          <tr>
            <td><a href="view.php?id=<?= $post['id'] ?>"><?= htmlspecialchars($post['title']) ?></a></td>
            <td><?= htmlspecialchars($post['name']) ?></td>
            <td><?= $post['created_at'] ?></td>
            <td>
              <?php if ($userId == $post['user_id']): ?>
                <a href="edit.php?id=<?= $post['id'] ?>"><button type="button">edit</button></a>
                <a href="delete.php?id=<?= $post['id'] ?>" onclick="return confirm('정말 삭제하시겠습니까?')"><button type="button">delete</button></a>
              <?php else: ?>
                -
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </table>
    </div>
  </div>
</body>
</html>
