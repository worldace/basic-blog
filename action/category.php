<?php
//======================================================
// ■指定したカテゴリに属する記事一覧を表示する
// 
// http://127.0.0.1/basic-blog/?action=category&category=
// 呼び出し元: ../index.php
//======================================================


if(!自然数なら($_GET['page'])){ $_GET['page'] = 1; }
if(!$_GET['category']){ エラー('カテゴリを選択してください'); }

$設定['カテゴリ'] = $_GET['category'] = 投稿文字列処理($_GET['category']);


//SQL文を作成
$取得位置 = $設定['ライトインデックス記事表示件数'] * $_GET['page'] - $設定['ライトインデックス記事表示件数'];
$取得件数 = $設定['ライトインデックス記事表示件数'] + 1; //ページめくり用に1件多く

if($_GET['category'] === $設定['カテゴリなし']){
    $検索結果 = データベース取得("select * from ブログ where 記事状態 = '公開' and 記事カテゴリ = '' order by 記事ID desc limit $取得位置, $取得件数");
}
else{
    $検索結果 = データベース取得("select * from ブログ where 記事状態 = '公開' and 記事カテゴリ like ? order by 記事ID desc limit $取得位置, $取得件数", array("%{$_GET['category']}%"));
}


//検索結果のHTMLを作成する
$設定['ライトインデックス'] = 部品("lightindex", $検索結果);

//ページめくり作成
$_category = rawurlencode($_GET['category']);
$設定['ページめくり']   = 部品("paging", $_GET['page'], $設定['ライトインデックス記事表示件数'], count($検索結果), "{$設定['URL']}?action=category&category=$_category&page=");

//メニュー作成
$設定['メインメニュー'] = 管理者なら() ? 部品("mainmenu_admin") : 部品("mainmenu_user");



?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title><?= $設定['ブログ名'] ?> <?= $設定['カテゴリ'] ?></title>
  <link href="<?= $設定['テンプレート'] ?>/base-blog.css" rel="stylesheet">
  <link rel="icon" href="<?= $設定['テンプレート'] ?>/favicon.png" type="image/png">
  <link rel="alternate" type="application/atom+xml" href="<?= $設定['URL'] ?>?action=feed">

  <meta property="og:url" content="<?= $設定['URL'] ?>">
  <meta property="og:site_name" content="<?= $設定['ブログ名'] ?>">
  <meta property="og:type" content="website">
  <meta property="og:image" content="<?= $設定['ベースURL'] ?><?= $設定['テンプレート'] ?>/<?= $設定['サイトアイキャッチ画像'] ?>">
  <meta property="og:locale" content="ja_JP">

  <script src="<?= $設定['jQuery'] ?>"></script>
</head>
<body>


<header class="main-header">
<h1 class="main-title"><a href="<?= $設定['URL'] ?>"><?= $設定['ブログ名'] ?></a></h1>
<?= $設定['メインメニュー'] ?>
</header>

<article class="main-contents">

<?= $設定['ライトインデックス'] ?>


<?= $設定['ページめくり'] ?>
</article>


<script src="<?= $設定['テンプレート'] ?>/blog.js" charset="utf-8"></script>

</body>
</html>


