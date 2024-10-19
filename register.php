<?php
// データベース接続設定を読み込む
require_once 'includes/db_connect.php';

$error_message = ""; // エラーメッセージ

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // バリデーション
    if (empty($username) || empty($email) || empty($password)) {
        $error_message = "全ての項目を入力してください。";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "正しいメールアドレスを入力してください。";
    } else if (strlen($password) < 8) {
        $error_message = "パスワードは8文字以上で入力してください。"; 
    } else {
        // パスワードのハッシュ化
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            // ユーザー登録
            $sql = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR); 

            if ($stmt->execute()) {
                // 登録成功
                header('Location: register_success.php'); 
                exit;
            } else {
                $error_message = "データベースエラーが発生しました。";
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
<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>ユーザー登録</title>
</head>
<body>
    <h1>ユーザー登録</h1>

    <?php if (isset($error_message)): ?>
        <p style="color: red;"><?php echo $error_message; ?></p> 
    <?php endif; ?> 

    <form method="post" action="register.php">
        <label for="username">ユーザー名:</label><br>
        <input type="text" id="username" name="username" required><br><br>

        <label for="email">メールアドレス:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <label for="password">パスワード:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <input type="submit" value="登録">
    </form> 

</body>
</html>