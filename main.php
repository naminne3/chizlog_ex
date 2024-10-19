<?php
session_start();

// ログイン状態の確認
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// データベース接続設定を読み込む
require_once 'includes/db_connect.php';

// APIキー設定を読み込む
require_once 'includes/api_keys.php'; 

// スポット情報をデータベースから取得 (全ユーザー分)
try {
    $sql = "SELECT * FROM spots"; // WHERE句を削除
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $spots = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "データベースエラー: " . $e->getMessage();
}

?>


<!DOCTYPE html>
<html>
<head>
    <title>メインページ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $google_maps_api_key; ?>&callback=initMap&libraries=places&v=weekly" async defer></script>
    <script src="script.js" defer></script> 
    <link rel="stylesheet" href="styles/style.css"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <style>
        #map {
            height: 400px;
            width: 100%;
        }
    </style>
    <script>
        // spots データとログインユーザーIDを定義
        const spots = <?php echo json_encode($spots); ?>; 
        const currentUserId = <?php echo $_SESSION['user_id']; ?>; 
          // ページ読み込み時と、履歴が変更された時に initMap() を実行
    window.addEventListener('popstate', initMap);
    </script>
</head>
<body>
    <h1>メインページ</h1>

    <input type="text" id="search-box" placeholder="お店の名前などを検索"> 

    <div id="map"></div> 

    <p>ようこそ！</p>
    <a href="spot_register.php">スポット登録</a>
    <a href="logout.php">ログアウト</a>


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