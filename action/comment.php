<?php
//======================================================
// ■指定したコメントを1件表示する
// 
// http://127.0.0.1/basic-blog/?action=comment&entry_id=&comment_id=
// 呼び出し元: ../index.php
//======================================================


if (!自然数なら($_GET['entry_id']))  { エラー('不正なIDです。'); }
if (!自然数なら($_GET['comment_id'])){ エラー('不正なIDです。'); }


//ブログテーブルからデータを取得
$entry = データベース行取得("select * from ブログ where 記事ID = {$_GET['entry_id']} and 記事状態 = '公開'");
if(!$entry['記事ID']){ エラー('その記事は存在しません。'); }
$設定 += $entry;


//コメント表示の有効期限チェック
if(!コメントを表示するなら($entry)){
    エラー('このコメントはもう表示されません');
}


//コメントテーブルからデータを取得
foreach(データベース取得("select * from コメント where 記事ID = {$_GET['entry_id']}") as $line){
    $設定['コメント番号']++;
    if($line['コメントID'] == $_GET['comment_id']){
        $comment = $line;
        break;
    }
}
if(!$comment['コメントID']) { エラー('そのコメントは存在しません。'); }
if($comment['コメント状態'] != '公開') { エラー('そのコメントは表示できません。'); }
$設定 += $comment;


//HTML作成
$設定['記事URL'] = 記事URL作成($entry['記事ID']);

$設定['コメント'] = 部品作成("comment", array($comment), $設定['コメント番号']);

if(コメントが受付中なら($entry)){
    $設定['コメントフォーム'] = 部品作成("commentform");
}



?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title><?= $設定['記事タイトル'] ?></title>
  <link href="<?= $設定['テンプレート'] ?>/base-blog.css" rel="stylesheet">
  <link href="<?= $設定['テンプレート'] ?>/blog.css" rel="stylesheet">
  <link rel="icon" href="<?= $設定['テンプレート'] ?>/favicon.png" type="image/png">

  <script src="<?= $設定['jQuery'] ?>"></script>
  <style><?= $設定['埋め込みCSS'] ?></style>
</head>
<body>


<header class="main-header">
<h1 class="main-title"><a href="<?= $設定['URL'] ?>"><?= $設定['ブログ名'] ?></a></h1>
</header>


<article class="main-contents">

<header class="article-header">
<h1 class="article-title"><a href="<?= $設定['記事URL'] ?>"><?= $設定['記事タイトル'] ?></a></h1>
</header>

<article id="comments<?= $設定['記事ID'] ?>" class="comments" data-entry_id="<?= $設定['記事ID'] ?>">
<?= $設定['コメント'] ?>
</article>

<?= $設定['コメントフォーム'] ?>
</article>


<script src="<?= $設定['テンプレート'] ?>/blog.js" charset="utf-8"></script>
<script>
$(function(){
    var コメント番号 = $(".comment-no").text();
    if(!コメント番号){ return false; }
    $("textarea[name='comment_body']").val(">>" + コメント番号 + "\n");

});
</script>
<script><?= $設定['埋め込みJavaScript'] ?></script>
</body>
</html>