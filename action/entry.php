<?php
//======================================================
// ■記事を1件表示する
// 
// http://127.0.0.1/basic-blog/?action=entry&id=
// 呼び出し元: ../index.php
//======================================================


if(!自然数なら($_GET['id'])){ エラー('不正なIDです。'); }


//ブログテーブルから該当記事のデータを取得
$entry = データベース行取得("select * from ブログ where 記事ID = {$_GET['id']}");
$設定 += $entry;

if(!$entry['記事ID']){ エラー('その記事は存在しません。'); }


//非公開記事の場合
if($entry['記事状態'] != "公開"){
    if(!管理者なら()){
        エラー('この記事は公開されていません。');
    }
    $設定['管理用:記事状態の表示'] = "<div class=\"center\"><p class=\"infobox-green\">この記事は管理者だけに表示されます。(記事状態:{$entry['記事状態']})</p></div>";
}


//カウントアップ
if($設定['管理者のアクセスをカウントする'] == '○' or !管理者なら()){
    データベース更新("update ブログ set 記事ページビュー数 = 記事ページビュー数 + 1 where 記事ID = {$_GET['id']}");
}


//コメント一覧作成
if(コメントを表示するなら($entry)){
    $comment = データベース取得("select * from コメント where 記事ID = {$_GET['id']}");
    $設定['コメント'] = 部品("comment", $comment);
}

//コメントフォーム作成
if(コメントが受付中なら($entry)){
    $設定['コメントフォーム'] = 部品("commentform");
}

//アイキャッチ画像作成
if(!$設定['記事アイキャッチ画像']){
    $設定['記事アイキャッチ画像'] = "{$設定['ベースURL']}{$設定['テンプレート']}/{$設定['サイトアイキャッチ画像']}";
}

//前の記事と次の記事作成
$prev_entry = データベース取得("select * from ブログ where 記事ID < {$_GET['id']} order by 記事ID desc limit 1");
$next_entry = データベース取得("select * from ブログ where 記事ID > {$_GET['id']} order by 記事ID  asc limit 1");
$設定['前の記事'] = 部品("headline", $prev_entry, "←前の記事");
$設定['次の記事'] = 部品("headline", $next_entry, "次の記事→");


//記事とメニュー作成
$設定['記事URL']  = 記事URL作成($entry['記事ID']);
$設定['記事URL2'] = preg_replace("/^https*:\/\//", "", $設定['記事URL']);


$設定['メインメニュー']   = 管理者なら() ? 部品("mainmenu_admin") : 部品("mainmenu_user");
$設定['ソーシャルボタン'] = 部品("socialbutton");
$設定['記事'] = 部品("entry", $entry);



?>
<!DOCTYPE html>
<html lang="ja" id="entry">
<head>
  <meta charset="utf-8">
  <title><?= $設定['記事タイトル'] ?></title>
  <link href="<?= $設定['テンプレート'] ?>/base-blog.css" rel="stylesheet">
  <link href="<?= $設定['テンプレート'] ?>/blog.css" rel="stylesheet">
  <link rel="icon" href="<?= $設定['テンプレート'] ?>/favicon.png" type="image/png">
  <link rel="alternate" type="application/atom+xml" href="<?= $設定['URL'] ?>?action=feed">
  <link rel="canonical" href="<?= $設定['記事URL'] ?>">

  <meta property="og:url" content="<?= $設定['記事URL'] ?>">
  <meta property="og:site_name" content="<?= $設定['ブログ名'] ?>">
  <meta property="og:title" content="<?= $設定['記事タイトル'] ?>">
  <meta property="og:type" content="website">
  <meta property="og:image" content="<?= $設定['記事アイキャッチ画像'] ?>">
  <meta property="og:locale" content="ja_JP">

  <script src="<?= $設定['jQuery'] ?>"></script>
</head>
<body>


<header class="main-header">
<h1 class="main-title"><a href="<?= $設定['URL'] ?>"><?= $設定['ブログ名'] ?></a></h1>
<?= $設定['メインメニュー'] ?>
</header>

<article class="main-contents">
<?= $設定['管理用:記事状態の表示'] ?>
<?= $設定['記事'] ?>

<aside>
<?= $設定['ソーシャルボタン'] ?>
</aside>

<nav class="prevnext-headline" style="margin: 60px auto;">
<?= $設定['前の記事'] ?>
<?= $設定['次の記事'] ?>
</nav>


<aside>
<article id="comments<?= $設定['記事ID'] ?>" class="comments" data-entry_id="<?= $設定['記事ID'] ?>">
<?= $設定['コメント'] ?>
</article>

<?= $設定['コメントフォーム'] ?>
</aside>
</article>


<script src="<?= $設定['テンプレート'] ?>/blog.js" charset="utf-8"></script>
<script>$(function(){

!function 最近見た記事を記録する(){
    var url    = $("link[rel='canonical']").attr("href");
    var title  = $("meta[property='og:title']").attr("content");
    var latest = [];
    var recent = [];

    if(!url || !title || !localStorage){
        return;
    }

    if(localStorage.browsing_history){ //データ構造 [{url, title}]
        recent = JSON.parse(localStorage.browsing_history);
    }

    for(var i = 0; i < recent.length; i++){
        if(recent[i].url == url){
            continue;
        }
        latest.push(recent[i]);
    }

    latest.unshift({url: url, title: title});
    latest.splice(10);
    localStorage.browsing_history = JSON.stringify(latest);
}();


});</script>


</body>
</html>
