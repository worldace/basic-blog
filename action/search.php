<?php
//======================================================
// ■検索して結果を表示する
// 
// http://127.0.0.1/basic-blog/?action=search&search=
// 呼び出し元: ../index.php
//======================================================


if(!自然数なら($_GET['page'])){ $_GET['page'] = 1; }

//メニュー作成
$設定['メインメニュー'] = 部品("mainmenu");

//検索ワードの正規化
$設定['検索ワード'] = 検索ワードの正規化($_GET['search']);

//検索ワードを入力してない場合は検索ページを表示する
if($設定['検索ワード'] == ''){
    goto end;
}


//SQL文を作成する
$placeholder = 検索用placeholder($設定['検索ワード'], $設定['DBドライバ']);
$bindvalue   = 検索用bindvalue($設定['検索ワード']);

$取得位置 = $設定['ライトインデックス記事表示件数'] * $_GET['page'] - $設定['ライトインデックス記事表示件数'];
$取得件数 = $設定['ライトインデックス記事表示件数'] + 1; //ページめくり用に1件多く

$SQL文 = "select * from ブログ where 記事状態 = '公開' and $placeholder order by 記事ID desc limit $取得位置, $取得件数";

$検索結果 = データベース取得($SQL文, $bindvalue);


//見つからなかったら終了
if(!$検索結果){
    $設定['検索情報'] = "「{$設定['検索ワード']}」は見つかりませんでした";
    goto end;
}

//検索結果を作成
$設定['ライトインデックス'] = 部品("lightindex", $検索結果);

//ページめくり作成
$_word = rawurlencode($設定['検索ワード']);
$設定['ページめくり'] = 部品("paging", $_GET['page'], $設定['ライトインデックス記事表示件数'], count($検索結果), "{$設定['URL']}?action=search&search=$_word&page=");



function 検索ワードの正規化($search){
    $search = str_replace(array("\r\n","\r","\n"), '', $search);
    $search = preg_replace("/[\s\t　]+/u", " ", $search);
    $search = trim($search);

    return $search;
}


function 検索用placeholder($search, $PDOドライバ){

    //単語数＝空白数＋1
    $単語数 = substr_count($search, ' ') + 1;

    //単語数分のプレースホルダーを確保。SQLite/MySQL
    if(preg_match('/^sqlite/i', $PDOドライバ)) {
        $placeholder = array_fill(0, $単語数, "(記事タイトル || 記事本文) like ?");
    }
    else {
        $placeholder = array_fill(0, $単語数, "concat(記事タイトル, 記事本文) like ?");
    }

    return implode(' and ', $placeholder);
}


function 検索用bindvalue($search){

    foreach(explode(' ', $search) as $word){
        $bindvalue[] = "%$word%";
    }
    return $bindvalue;
}


end:
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title><?= $設定['ブログ名'] ?> 検索</title>
  <link href="<?= $設定['テンプレート'] ?>/base-blog.css" rel="stylesheet">
  <link href="<?= $設定['テンプレート'] ?>/blog.css" rel="stylesheet">
  <link rel="icon" href="<?= $設定['テンプレート'] ?>/favicon.png" type="image/png">

  <style>
    .contents{ text-align: center;    margin-top: 60px; margin-bottom: 80px; }
    .infobox-green{ margin-top: 80px; }
    .infobox-green:empty{ display: none; }
    [name="search"]{ width: 240px; }
  </style>
  <script src="<?= $設定['jQuery'] ?>"></script>
  <style><?= $設定['埋め込みCSS'] ?></style>
</head>
<body>


<header class="main-header">
<h1 class="main-title"><a href="<?= $設定['URL'] ?>"><?= $設定['ブログ名'] ?></a></h1>
<?= $設定['メインメニュー'] ?>
</header>


<article class="main-contents">
<div class="contents">
<form action="<?= $設定['URL'] ?>" method="GET" id="search" class="form-oneline"><input type="hidden" name="action" value="search">
<input type="text" name="search" value="<?= h($設定['検索ワード']) ?>"><input type="submit" value="検索する">
</form>
<p class="infobox-green"><?= h($設定['検索情報']) ?></p>
</div>

<?= $設定['ライトインデックス'] ?>


<?= $設定['ページめくり'] ?>
</article>


<script src="<?= $設定['テンプレート'] ?>/blog.js" charset="utf-8"></script>
<script>
$(function(){
    var search = $("[name='search']");
    search.focus();
    search.val(search.val()); //キャレット位置を一番右に

    $('#search').submit(function() {
        if(search.val() == ''){
            $(".infobox-green").html("検索ワードを入力してください");
            search.focus();
            return false;
        }
    });

});
</script>
<script><?= $設定['埋め込みJavaScript'] ?></script>


</body>
</html>