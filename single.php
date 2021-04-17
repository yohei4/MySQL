<?php 
//header.phpの読み込み
include './php/module/header.php';

//データの受け渡しができているかを確認
$article = !empty($_GET) ? $_GET : NULL;

$sql = "SELECT * FROM comment WHERE parent_id=$article[id]";

$comment = $dbh->query($sql);

if($_SERVER['REQUEST_METHOD'] === "POST") {

    //記事のコメントのエラー検知
    if(!empty($_POST["comment"])) {
        $text = $_POST["comment"];
        if(strlen($text) > 50) {
            $error_msg = "コメントは50文字以内です。";
        } else {
            $sql = "INSERT INTO comment (text, parent_id, my_id)" . "\n";
            $sql .= "VALUES ( :text, :article_id, :comment_id)";

            // SQL実行
            $stmt = $dbh->prepare($sql); //詳細版
            $stmt->bindValue(':text', $text, PDO::PARAM_STR); 
            $stmt->bindValue(':article_id', $article['id'], PDO::PARAM_INT);
            $stmt->bindValue(':comment_id', NULL, PDO::PARAM_INT);
            $stmt->execute();
            // $dbh->query($sql); //簡易版
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }
    } else {
        $error_msg = "コメントが見入力です。";
    };
    //削除機能
    if(!empty($_POST['delete'])) {
        //選択した記事のIDを取得
        $id = !empty($_POST['id']) ? $_POST['id'] : NULL;
        $sql = "DELETE FROM comment WHERE my_id=:comment_id";
        $stmt = $dbh->prepare($sql); //詳細版
        $stmt->bindValue(':comment_id', $id, PDO::PARAM_INT);
        $stmt->execute();
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    };
};

?>
<main>
    <article>
        <div class="aritcle-title">
            <h3><?php echo $article['title']; ?></h3>
        </div>
        <div class="article-body">
            <p><?php echo $article['text']; ?></p>
        </div>
    </article>
    <hr/>
    <div class="error_msg" style="padding: 0;"><p class="msg"><?= $error_msg; ?></p></div>
        <section class="comment-posts">
            <div class="comment-form">
                <form action="" method="POST">
                    <div class="input-body">
                        <textarea name="comment" class="comment-body" id="comment" cols="30" rows="10"></textarea>
                        <input type="submit" class="comment-btn" value="コメントを書く">    
                    </div>
                </form>
            </div>
            <ul class="comment-list">
<!-- ファイルから1行ずつデータを取得する -->
<?php if(isset($comment)) :?>
    <?php foreach ($comment as $row) : ?>
<li class="comment-item">
    <form action="" method="POST" class="comment">
    <input name="mode" type="hidden" value="DELETE">
    <input name="id" type="hidden" value="<?= $row['my_id']; ?>">
        <div class="input-body">
            <div class="input-data comment-body"><p><?= $row['text']; ?></p></div>
            <input type="submit" name="delete" class="comment-btn" value="コメントを消す">
        </div>
    </form>
</li>
    <?php endforeach; ?>
<?php endif; ?>
            </ul>
        </section>
    </main>
</body>
</html>