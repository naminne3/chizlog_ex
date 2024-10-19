<?php
session_start();

// ログイン状態の確認
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

// データベース接続設定を読み込む
require_once 'includes/db_connect.php';

// ユーザーIDを取得
$user_id = isset($_GET['id']) ? $_GET['id'] : null;

// ユーザー情報取得 (仮)
$user = [
    'username' => '仮のユーザー名',
];

?>
<!DOCTYPE html>
<html>
<head>
  <title>ユーザーページ</title>
  <link rel="stylesheet" href="styles/style.css">
</head>
<body>
  // ... (ナビゲーションバー) ...

  <h1><?php echo htmlspecialchars($user['username']); ?> さんのページ</h1> 

  <a href="main.php">メインに戻る</a>
<nav>
  <a href="main.php" <?php if (basename($_SERVER['PHP_SELF']) == 'main.php') echo 'class="active"'; ?>>
    <i class="fas fa-home"></i> メイン
  </a>
  <a href="feed.php" <?php if (basename($_SERVER['PHP_SELF']) == 'feed.php') echo 'class="active"'; ?>>
    <i class="fas fa-rss"></i> フィード
  </a>
  <a href="collecting.php" <?php if (basename($_SERVER['PHP_SELF']) == 'collecting.php') echo 'class="active"'; ?>>
    <i class="fas fa-trophy"></i> コレクション
  </a>
</nav>
</body>
</html>