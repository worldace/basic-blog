<?php
//======================================================
// ■ライトインデックスを表示する (新着順の記事一覧)
// 
// http://127.0.0.1/basic-blog/?action=light
// 呼び出し元: ../index.php
//======================================================


if (!自然数なら($_GET['page'])){ $_GET['page'] = 1; }

//ブログテーブルから記事のデータを取得
$取得位置 = $設定['ライトインデックス記事表示件数'] * $_GET['page'] - $設定['ライトインデックス記事表示件数'];
$取得件数 = $設定['ライトインデックス記事表示件数'] + 1; //ページめくり用に1件多く

$検索結果 = データベース取得("select * from ブログ where 記事状態 = '公開' order by 記事ID desc limit $取得位置, $取得件数");


//部品作成
$設定['ページめくり']       = 部品("paging", $_GET['page'], $設定['ライトインデックス記事表示件数'], count($検索結果), "{$設定['URL']}?action=light&page=");
$設定['メインメニュー']     = 管理者なら() ? 部品("mainmenu_admin") : 部品("mainmenu_user");
$設定['ライトインデックス'] = 部品("lightindex", $検索結果);



?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title><?= $設定['ブログ名'] ?> ライトモード</title>
  <link href="<?= $設定['テンプレート'] ?>/base-blog.css" rel="stylesheet">
  <link rel="icon" href="<?= $設定['テンプレート'] ?>/favicon.png" type="image/png">
  <link rel="alternate" type="application/atom+xml" href="<?= $設定['URL'] ?>?action=feed">

  <meta property="og:url" content="<?= $設定['URL'] ?>">
  <meta property="og:site_name" content="<?= $設定['ブログ名'] ?>">
  <meta property="og:type" content="website">
  <meta property="og:image" content="<?= $設定['ベースURL'] ?><?= $設定['テンプレート'] ?>/<?= $設定['サイトアイキャッチ画像'] ?>">
  <meta property="og:locale" content="ja_JP">

  <script src="<?= $設定['jQuery'] ?>"></script>
  <style><?= $設定['埋め込みCSS'] ?></style>
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
<script><?= $設定['埋め込みJavaScript'] ?></script>

</body>
</html>