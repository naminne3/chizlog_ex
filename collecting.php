<?php
session_start();

// ログイン状態の確認
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

// データベース接続設定を読み込む
require_once 'includes/db_connect.php';

// バッジ情報取得処理
try {
    // ユーザーが獲得したバッジ情報を取得するSQL
    $sql = "SELECT b.* 
            FROM badges b
            INNER JOIN user_badges ub ON b.badge_id = ub.badge_id
            WHERE ub.user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $badges = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "データベースエラー: " . $e->getMessage();
}

// 塗り絵情報取得処理
try {
    // ユーザーが訪問した国の情報を取得するSQL
    $sql = "SELECT DISTINCT country 
            FROM spots 
            WHERE user_id = :user_id AND visited = 1"; 
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $visitedCountries = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $error_message = "データベースエラー: " . $e->getMessage();
}

?>

<?php
// ... (PHPコードはそのまま) ...
?>

<!DOCTYPE html>
<html>
<head>
  <title>コレクティングページ</title>
  <link rel="stylesheet" href="styles/style.css"> 
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const tabs = document.querySelectorAll('.tabs a');
      const tabContents = document.querySelectorAll('.tab-content');

      tabs.forEach(tab => {
        tab.addEventListener('click', (event) => {
          event.preventDefault(); 

          // アクティブなタブとコンテンツを非アクティブにする
          tabs.forEach(t => t.classList.remove('active'));
          tabContents.forEach(c => c.classList.remove('active'));

          // クリックされたタブと対応するコンテンツをアクティブにする
          const target = event.target.getAttribute('href');
          event.target.classList.add('active');
          document.querySelector(target).classList.add('active');
        });
      });
    }); 
  </script>
</head>
<body>
  <nav>
    <a href="main.php" <?php if (basename($_SERVER['PHP_SELF']) == 'main.php') echo 'class="active"'; ?>>メイン</a>
    <a href="feed.php" <?php if (basename($_SERVER['PHP_SELF']) == 'feed.php') echo 'class="active"'; ?>>フィード</a>
    <a href="collecting.php" <?php if (basename($_SERVER['PHP_SELF']) == 'collecting.php') echo 'class="active"'; ?>>コレクション</a>
  </nav>

  <h1>コレクティングページ</h1>

  <div class="tabs">
    <a href="#badges" class="active">バッジ収集</a>
    <a href="#coloring">塗り絵</a>
  </div>

  <div id="badges" class="tab-content active">
    <h2>獲得バッジ</h2>
    <div id="badge-container">
        <img src="img/1.jpg" alt="バッジ画像"> 
    </div>
  </div>

  <div id="coloring" class="tab-content">
    <h2>塗り絵</h2>
    <div id="coloring-container">
        <img src="img/2.jpg" alt="世界地図画像"> 
    </div>
  </div>
</body>
</html>