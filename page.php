<?php
$_get_page = $_GET["page"];
$_get_query = $_GET["q"];

$csql = "SELECT COUNT(*) as 'cnt' FROM articles WHERE title=:q"; // 総件数カウント用SQL
$ssql = "SELECT * FROM table WHERE data=:q LIMIT :start, 10 ORDER BY `id`"; // データ抽出用SQL

$dsn = "mysql:host=localhost; dbname=laravel_news; charset=utf8";
$user = 'root';
$password = 'root';
try {
    $dbh = new PDO($dsn,$user,$password);
  } catch (PDOException $e) {
    echo 'DB接続エラー！: ' . $e->getMessage();
}

// データ抽出用SQLを、プリペアドステートメントで実行
$ssth = $dbh->prepare($ssql);
$ssth->bindValue(":q", $_get_query);
$ssth->bindValue(":start", $_get_page * 10);
$ssth->execute();
$data = $ssth->fetchAll(PDO::FETCH_ASSOC);

// 総件数カウント用SQLを、プリペアドステートメントで実行
$csth = $dbh->prepare($csql);
$csth->bindValue(":q", $_get_query);
$csth->execute();
$total = $csth->fetchColumn(PDO::FETCH_ASSOC);

$pages = ceil($total / 10); // 総件数÷1ページに表示する件数 を切り上げたものが総ページ数
?>
<html>
<body>
<ul>
<?php
foreach($data as $row) {
    printf("<li>%s</li>\n", $row["column"]);
}
?>
</ul>

<?php
for($i=0; $i < $pages; $i++) {
    printf("<a href='?page=%d&q=%s'>%dページへ</a><br />\n", $i, $_get_query, $i);
}
?>

</body>
</html>