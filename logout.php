<?php
session_start(); // セッション開始

// セッション変数を全て削除
$_SESSION = array();

// セッションを破棄
session_destroy();

// ログインページにリダイレクト
header('Location: login.php'); 
exit;
?>