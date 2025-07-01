<?php
session_start();
require_once(__DIR__ . '/../db.php');

if (!isset($_SESSION['user_id'])) {
  header("Location: ../auth/login.php");
  exit();
}

$postId = $_POST['post_id'] ?? null;
$content = trim($_POST['content'] ?? '');

if ($postId && $content) {
  $stmt = $pdo->prepare("INSERT INTO comments_board1 (post_id, user_id, content) VALUES (?, ?, ?)");
  $stmt->execute([$postId, $_SESSION['user_id'], $content]);
}

header("Location: view.php?id=$postId");
exit();
