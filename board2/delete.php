<?php
session_start();
require_once(__DIR__ . '/../db.php');

if (!isset($_SESSION['user_id'])) {
  header("Location: ../auth/login.php");
  exit();
}

$userId = $_SESSION['user_id'];
$postId = $_GET['id'] ?? null;

if (!$postId) {
  echo "잘못된 접근입니다.";
  exit();
}

// 1. 게시글 정보 가져오기
$stmt = $pdo->prepare("SELECT * FROM posts_board2 WHERE id = ?");
$stmt->execute([$postId]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
  echo "게시글이 존재하지 않습니다.";
  exit();
}

// 2. 본인 확인
if ($post['user_id'] != $userId) {
  echo "본인 글만 삭제할 수 있습니다.";
  exit();
}

// 3. 첨부파일이 있다면 서버에서 삭제
if (!empty($post['file_name'])) {
  $filePath = "../upload/board2/" . $post['file_name'];
  if (file_exists($filePath)) {
    unlink($filePath); // 파일 삭제
  }
}

// 4. DB에서 게시글 삭제
$stmt = $pdo->prepare("DELETE FROM posts_board2 WHERE id = ?");
$stmt->execute([$postId]);

// 5. 목록 페이지로 이동
header("Location: /board2/index.php");
exit();
