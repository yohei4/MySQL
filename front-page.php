<?php

//header.phpの読み込み
include './php/module/header.php';

//ページング用
include './pageing.php';

//methodがPOSTならば実行
if($_SERVER['REQUEST_METHOD'] === "POST") {

    //modeを確認
    $mode = !empty($_POST['mode']) ? $_POST['mode'] : NULL;

    if($mode === "POST") {
        //titleの値が取得できれば代入
        $title = !empty($_POST["title"]) ? $_POST["title"] : NULL;
        //bodyの値が取得できれば代入
        $body = !empty($_POST["body"]) ? $_POST["body"] : NULL;
        
        //タイトルと記事の文字数取得
        $title_length = strlen($title);
        $body_length = strlen($body);

        //タイトルのエラー検知
        if($title === NULL || $title_length > 30) {
            $error_title_msg = "タイトルは必須かつ３０文字以内です。";
            $error_title = false;
        } else {
            $error_title = true;
        };

        //記事のエラー検知
        if($body === NULL) {
            $error_body_msg = "記事は必須項目です。";
            $error_body = false;
        } else {
            $error_body = true;
        };

        if($error_title === true && $error_body === true) {
        //$error_titleと$error_bodyがtrueだったら
            //sqlの作成
            $sql = "INSERT INTO articles (title, text)" . "\n";
            $sql .= "VALUES ( :title, :body)";
            // SQL実行
            $stmt = $dbh->prepare($sql); //詳細版
            $stmt->bindValue(':title', $title, PDO::PARAM_STR);
            $stmt->bindValue(':body', $body, PDO::PARAM_STR);
            $stmt->execute();
            //$dbh->query($sql); //簡易版
            //リダイレクト
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }
    }
    //削除機能
    elseif($mode === 'DELETE') {
        if(!empty($_POST['delete'])) {
            //選択した記事のIDを取得
            $id = !empty($_POST['id']) ? $_POST['id'] : NULL;
            //sql作成(コメントに共通のIDがあれば消す)
            $sql = <<< EOD
            DELETE articles, comment
            FROM articles
            LEFT OUTER JOIN comment
            ON articles.id = comment.parent_id
            WHERE articles.id = :article_id
            EOD;
            //sqlの実行
            $stmt = $dbh->prepare($sql); //詳細版
            $stmt->bindValue(':article_id', $id, PDO::PARAM_INT);
            $stmt->execute();
            //リダイレクト
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }
    };
}
?>
    <main>
        <section class="form-post">
            <div class="form-title">
                <h1>
                    さぁ、最新のニュースをシェアしましょう
                </h1>
            </div>
                <div class="form-group">
                    <form action="" method="POST" onsubmit="return post_alert()">
                        <input type="hidden" name="mode" value="POST">
                        <div class="input-title">
                            <label for="title">タイトル：</label>
                            <input name="title" id="title" type="text">
                        </div>
                        <div class="error_msg"><p class="msg"><?= $error_title_msg;?></p></div>
                        <div class="input-body">
                            <label for="body">記事：</label>
                            <textarea name="body" id="body" cols="30" rows="10"></textarea>
                        </div>
                        <div class="error_msg"><p class="msg"><?= $error_body_msg;?></p></div>
                        <div class="input-submit">
                            <button type="submit" value="投稿">投稿</button>
                        </div>
                    </form>
                </div>
        </section>
        <section class="posts">
            <ul class="article-list">

<!-- $aritlclesから1行ずつデータを取得する -->
<?php if(isset($data)) :?>
    <?php foreach ($data as $row) : ?>
        <?php if(!($row['title'] === "" && $row['body'] === "")) :?>
            <li>
            <article>
                <div class="aritcle-title">
                    <h3><?php echo $row['title'];?></h3>
                </div>
                <div class="article-body">
                    <p><?php echo $row['text']?></p>
                </div>
                <div class="airticle-link">
                    <p><a href="./single.php?id=<?= $row['id']; ?>">記事全文•コメントを見る</a></p>
                </div>
                <form action="" method="POST" class="comment">
                    <input name="mode" type="hidden" value="DELETE">
                    <input name="id" type="hidden" value="<?= $row['id']; ?>">
                    <div class="delete-btn">
                        <input type="submit" name="delete" class="comment-btn" value="記事を消す">
                    </div>
                </form>
                </form>
            </article>
        </li>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>
            </ul>
            <nav>
            <ul class="page-list">
                <li class="page-item">
                    <span><a href="./front-page.php?page=<?php echo $prev; ?>">&lsaquo;</a></span>
                </li>
                <?php for($i = 1; $i <= $pages; $i++) :?>
                <li class="page-item">
                    <span><a href="./front-page.php?page=<?php echo $i; ?>"><?= $i; ?></a></span>
                </li>
                <?php endfor; ?>
                <li class="page-item">
                    <span><a href="./front-page.php?page=<?php echo $next; ?>">&rsaquo;</a></span>
                </li>
            </ul>
        </nav>
        </section>
    </main>
    <script src="./js/ alert.js"></script>
</body>
</html>