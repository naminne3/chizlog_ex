<?php
// データベース接続設定を読み込む
require_once 'includes/db_connect.php';

$error_message = ""; // エラーメッセージ

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // バリデーション (簡易版)
    if (empty($username) || empty($password)) {
        $error_message = "ユーザー名とパスワードを入力してください。";
    } else {
        try {
            // ユーザー情報をデータベースから取得
            $sql = "SELECT * FROM users WHERE username = :username";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':username', $username, PDO::PARAM_STR);
            $stmt->execute();

            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // パスワードの検証
                if (password_verify($password, $row['password'])) {
                    // ログイン成功!
                    session_start();
                    $_SESSION['user_id'] = $row['user_id'];
                    header('Location: main.php'); 
                    exit;
                } else {
                    // パスワードが間違っている場合
                    $error_message = "ユーザー名またはパスワードが間違っています。";
                }
            } else {
                // ユーザー情報が見つからない場合
                $error_message = "ユーザー名またはパスワードが間違っています。";
            }

        } catch (PDOException $e) {
            $error_message = "データベースエラー: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>ログイン</title>
</head>
<body>
    <h1>ログイン</h1>

    <?php if (isset($error_message)): ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>

    <form method="post" action="login.php"> 
        <label for="username">ユーザー名:</label><br>
        <input type="text" id="username" name="username" required><br><br>

        <label for="password">パスワード:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <input type="submit" value="ログイン">
    </form>
</body>
</html>