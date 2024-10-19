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

// クエリパラメータから緯度経度を取得
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // POSTリクエスト以外の場合のみクエリパラメータから取得
    $latitude = isset($_GET['lat']) ? $_GET['lat'] : null;
    $longitude = isset($_GET['lng']) ? $_GET['lng'] : null;
}


// フォームが送信された場合の処理 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $spot_name = $_POST['spot_name'];
  $category = $_POST['category'];
  $tags = $_POST['tags'];
  $address = $_POST['address'];
  $latitude = $_POST['latitude'];
  $longitude = $_POST['longitude'];
  $comment = $_POST['comment'];
  $user_id = $_SESSION['user_id'];

  try {
      // スポット情報をデータベースに登録
      $sql = "INSERT INTO spots (user_id, spot_name, category, tags, address, latitude, longitude, comment) 
              VALUES (:user_id, :spot_name, :category, :tags, :address, :latitude, :longitude, :comment)";
      $stmt = $pdo->prepare($sql);
      $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
      $stmt->bindValue(':spot_name', $spot_name, PDO::PARAM_STR);
      $stmt->bindValue(':category', $category, PDO::PARAM_STR);
      $stmt->bindValue(':tags', $tags, PDO::PARAM_STR);
      $stmt->bindValue(':address', $address, PDO::PARAM_STR);
      $stmt->bindValue(':latitude', $latitude, PDO::PARAM_STR); 
      $stmt->bindValue(':longitude', $longitude, PDO::PARAM_STR); 
      $stmt->bindValue(':comment', $comment, PDO::PARAM_STR);

      if ($stmt->execute()) {
          // 登録成功

          // 登録したスポットのIDを取得
        $spot_id = $pdo->lastInsertId();

        // フィード投稿処理
        $feedContent = "{$spot_name}をお気に入りに登録しました！"; 
        $sql = "INSERT INTO feeds (user_id, content, spot_id) VALUES (:user_id, :content, :spot_id)"; // spot_id を追加
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':content', $feedContent, PDO::PARAM_STR);
        $stmt->bindValue(':spot_id', $spot_id, PDO::PARAM_INT); // spot_id をバインド
        $stmt->execute();

          header('Location: main.php'); 
          exit;
      } else {
          $error_message = "データベースエラーが発生しました。"; 
      }

  } catch (PDOException $e) {
      $error_message = "データベースエラー: " . $e->getMessage();
  }
}

?>

<!DOCTYPE html>
<html>
<head>
  <title>お気に入りスポット登録</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
  <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $google_maps_api_key; ?>&callback=initMap&libraries=places&v=weekly" async defer></script>
  <script src="script_register.js" defer></script> 
  <link rel="stylesheet" href="styles/style.css"> 
  <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
  <style>
    /* 地図とフォームのスタイル (必要であれば) */
    #map {
      height: 300px;
      width: 100%;
    }
  </style>
</head>
<body>

<h1>お気に入りスポット登録</h1>

<form id="spot-form" method="post" action="spot_register.php">
    <label for="spot_name">スポット名:</label><br>
    <input type="text" id="spot_name" name="spot_name" value="<?php echo isset($_GET['name']) ? htmlspecialchars($_GET['name']) : ''; ?>" required><br><br>
    
    <label for="category">カテゴリ:</label><br>
    <select id="category" name="category">
        <option value="restaurant">レストラン</option>
        <option value="tourist_spot">観光地</option>
        <option value="shop">お店</option>
        <option value="other">その他</option>
    </select><br><br>
    
    <label for="tags">タグ:</label><br>
    <input type="text" id="tags" name="tags"><br><br>
    
    <label for="address">住所:</label><br>
    <input type="text" id="address" name="address" value="<?php echo isset($_GET['address']) ? htmlspecialchars($_GET['address']) : ''; ?>" required><br><br>

    <input type="hidden" id="latitude" name="latitude" value="<?php echo $latitude; ?>">
    <input type="hidden" id="longitude" name="longitude" value="<?php echo $longitude; ?>">
    
    <label for="comment">コメント:</label><br>
    <textarea id="comment" name="comment"></textarea><br><br>
    
    <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
    
    <input type="submit" value="登録">
</form>

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