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

// ログインユーザーのお気に入りスポット情報を取得
try {
    $sql = "SELECT * FROM spots WHERE user_id = :user_id"; // ログインユーザーのuser_idを指定
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $favorites = $stmt->fetchAll(PDO::FETCH_ASSOC); 
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
    <p>ようこそ！</p>
    <a href="spot_register.php">スポット登録</a>
    <a href="logout.php">ログアウト</a><div></div>

    <input type="text" id="search-box" placeholder="お店の名前などを検索"> 

    <div id="map"></div> 





    <div id="favorite-list">
  <h2>お気に入りスポット</h2>
    <?php if (!empty($favorites)): ?>
        <ul>
            <?php foreach ($favorites as $favorite): ?>
                <li>
                    <a href="spot_detail.php?id=<?php echo $favorite['spot_id']; ?>">
                        <?php echo $favorite['spot_name']; ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>まだお気に入りは登録されていません。</p>
    <?php endif; ?>
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