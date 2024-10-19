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
    <style>
        /* 地図のスタイル (必要であれば) */
        #map {
            height: 400px;
            width: 100%;
        }
    </style>
    <!-- spots データを script タグ内に埋め込む -->
    <script>
        const spots = <?php echo json_encode($spots); ?>; 
        const currentUserId = <?php echo $_SESSION['user_id']; ?>; // ログインユーザーIDを追加
    </script>
</head>
<body>
    <h1>メインページ</h1>


    <!-- 検索ボックスを追加 -->
    <input type="text" id="search-box" placeholder="お店の名前などを検索"> 

    
    <!-- 地図を表示する div --> 
    <div id="map"></div> 

    <p>ようこそ！</p>
    <a href="spot_register.php">スポット登録</a>
    <a href="logout.php">ログアウト</a>
</body>
</html>