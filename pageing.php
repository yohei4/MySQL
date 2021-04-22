<?php
$_get_page = !empty($_GET["page"]) ? $_GET["page"] : 1;

// 総件数カウント用SQLを、プリペアドステートメントで実行
$csql = 'SELECT COUNT(*) as cnt FROM articles'; //　総件数カウント用SQL
$csth = $dbh->prepare($csql);
$csth->execute();
$total = $csth->fetchColumn();
$pages = ceil($total / 10); // 総件数÷1ページに表示する件数 を切り上げたものが総ページ数
$prev = $_get_page - 1;
$next = $_get_page + 1;

// データ抽出用SQLを、プリペアドステートメントで実行
$sql = 'SELECT * FROM articles WHERE id LIMIT :start, 10';
$ssth = $dbh->prepare($sql);
$ssth->bindValue(":start", ($_get_page - 1) * 10, PDO::PARAM_INT);
$ssth->execute();
$data = $ssth->fetchAll(PDO::FETCH_ASSOC);
