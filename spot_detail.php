<?php
session_start();

// ログイン状態の確認
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

// データベース接続設定を読み込む
require_once 'includes/db_connect.php';

// スポットIDを取得
$spot_id = isset($_GET['id']) ? $_GET['id'] : null;

// スポットIDを取得
$spot_id = isset($_GET['id']) ? $_GET['id'] : null;

if ($spot_id) {
    try {
        // スポット情報をデータベースから取得
        $sql = "SELECT * FROM spots WHERE spot_id = :spot_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':spot_id', $spot_id, PDO::PARAM_INT);
        $stmt->execute();
        $spot = $stmt->fetch(PDO::FETCH_ASSOC); 
    } catch (PDOException $e) {
        $error_message = "データベースエラー: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>スポット詳細</title>
</head>
<body>
  <h1><?php echo htmlspecialchars($spot['spot_name']); ?></h1>
  <p>住所: <?php echo htmlspecialchars($spot['address']); ?></p>
  <p>コメント: <?php echo htmlspecialchars($spot['comment']); ?></p>
  <p>登録ユーザー: <?php echo htmlspecialchars($spot['user_id']); ?></p> 

  <a href="main.php">メインに戻る</a>
</body>
</html>