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

    // 入力値のバリデーション (必要であれば)
    // ...

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
    $sql = "SELECT f.*, u.username 
            FROM feeds f 
            INNER JOIN users u ON f.user_id = u.user_id 
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
                <p><strong><?php echo htmlspecialchars($feed['username']); ?></strong></p>
                <p><?php echo htmlspecialchars($feed['content']); ?></p>
                <p><?php echo htmlspecialchars($feed['created_at']); ?></p>
            </div>
        <?php endforeach; ?>
    </div>

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