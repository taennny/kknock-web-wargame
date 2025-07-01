<?php
session_start();
require_once(__DIR__ . '/../db.php');

$id = $_GET['id'] ?? null;
$postId = $_GET['post_id'] ?? null;

$stmt = $pdo->prepare("SELECT * FROM comments_board1 WHERE id = ?");
$stmt->execute([$id]);
$comment = $stmt->fetch(PDO::FETCH_ASSOC);

if ($comment && $comment['user_id'] == $_SESSION['user_id']) {
  $stmt = $pdo->prepare("DELETE FROM comments_board1 WHERE id = ?");
  $stmt->execute([$id]);
}

header("Location: view.php?id=$postId");
exit();
