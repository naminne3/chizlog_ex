<?php
session_start();

// ログイン状態の確認
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

// データベース接続設定を読み込む
require_once 'includes/db_connect.php';

// フィード投稿処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];

    try {
        // フィードをデータベースに登録
        $sql = "INSERT INTO feeds (user_id, content) VALUES (:user_id, :content)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':content', $content, PDO::PARAM_STR);

        if ($stmt->execute()) {
            // 登録成功
            header('Location: feed.php'); // 再読み込み
            exit;
        } else {
            $error_message = "データベースエラーが発生しました。";
        }

    } catch (PDOException $e) {
        $error_message = "データベースエラー: " . $e->getMessage();
    }
}


// フィード取得処理
try {
    $sql = "SELECT f.*, u.username, s.spot_name, s.category, s.address, s.comment 
            FROM feeds f
            INNER JOIN users u ON f.user_id = u.user_id
            LEFT JOIN spots s ON f.spot_id = s.spot_id 
            ORDER BY f.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $feeds = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "データベースエラー: " . $e->getMessage();
}


?>

<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
  <link rel="stylesheet" href="styles/style.css"> 
  <title>フィード</title>
</head>
<body>

  <nav>
    <a href="main.php" <?php if (basename($_SERVER['PHP_SELF']) == 'main.php') echo 'class="active"'; ?>>メイン</a>
    <a href="feed.php" <?php if (basename($_SERVER['PHP_SELF']) == 'feed.php') echo 'class="active"'; ?>>フィード</a>
    <a href="collecting.php" <?php if (basename($_SERVER['PHP_SELF']) == 'collecting.php') echo 'class="active"'; ?>>コレクション</a>
  </nav>

  <h1>フィード</h1>

  <!-- フィード投稿エリア -->
  <form method="post" action="feed.php">
    <textarea name="content" placeholder="つぶやきを入力"></textarea><br>
    <input type="submit" value="投稿">
  </form>

  <!-- フィード表示エリア -->
  <div id="feed-container">
    <?php foreach ($feeds as $feed): ?>
      <div class="feed-item">
        <p>
          <strong>
            <a href="user_page.php?id=<?php echo htmlspecialchars($feed['user_id']); ?>">
              <?php echo htmlspecialchars($feed['username']); ?> さん 
            </a>
          </strong>
        </p>
        <?php if (!empty($feed['spot_id'])): ?>
        <p>
          スポット: 
          <a href="spot_detail.php?id=<?php echo htmlspecialchars($feed['spot_id']); ?>">
            <?php echo htmlspecialchars($feed['spot_name']); ?>
          </a> 
          (<?php echo htmlspecialchars($feed['category']); ?>)
        </p>
        <p>住所: <?php echo htmlspecialchars($feed['address']); ?></p>
        <p>口コミ: <?php echo htmlspecialchars($feed['comment']); ?></p>
        <?php endif; ?>
        <p><?php echo htmlspecialchars($feed['content']); ?></p> 
        <p><?php echo htmlspecialchars($feed['created_at']); ?></p>
      </div>
    <?php endforeach; ?>
  </div>

</body>
</html>