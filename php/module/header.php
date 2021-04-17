<?php
//MySQLに接続する
$dsn = "mysql:host=localhost; dbname=laravel_news; charset=utf8";
$user = 'root';
$password = 'root';
try {
    $dbh = new PDO($dsn,$user,$password);
    //【レコードの全読み込み用】sql作成
    $sql = 'SELECT * FROM articles';
    //sqlの実行後、$articlesに代入
    $articles = $dbh->query($sql);
  } catch (PDOException $e) {
    echo 'DB接続エラー！: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel News</title>

    <!-- css -->
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <header>
        <p><a href="../../front-page.php">Laraver News</a></p>
    </header>