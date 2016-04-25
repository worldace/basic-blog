<?php
//======================================================
// ■カテゴリ一覧を表示する
// 
// http://127.0.0.1/basic-blog/?action=categorylist
// 呼び出し元: ../index.php
//======================================================


foreach(全カテゴリ() as $category => $count){
    $_category = rawurlencode($category);
    $設定['カテゴリ一覧'] .= "<li><a href=\"{$設定['URL']}?action=category&category=$_category\" class=\"js-category-name\" data-category-count=\"$count\">$category</a></li>\n";
}

//メニュー作成
$設定['メインメニュー'] = 部品("mainmenu");



?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title><?= $設定['ブログ名'] ?> カテゴリ一覧</title>
  <link href="<?= $設定['テンプレート'] ?>/base-blog.css" rel="stylesheet">
  <link rel="icon" href="<?= $設定['テンプレート'] ?>/favicon.png" type="image/png">

  <script src="<?= $設定['jQuery'] ?>"></script>
  <style>
.tagcloud {
    margin: 60px auto;
    width: 720px;
	padding: 0;
	list-style: none;
    text-align: center;
}
.tagcloud li {
	display: inline-block;
	margin: 7px 7px 7px 0;
	padding: 0;
}
.tagcloud a {
	display: inline-block;
	height: 34px;
	line-height: 34px;
	padding: 0 10px;
	background-color: #fff;
	border: 1px solid #aaa;
	border-radius: 3px;
	color: #333;
	font-size: 16px;
    letter-spacing: 0;
	text-decoration: none;
	transition: 0.2s;
}
.tagcloud a:hover {
	background-color: #3498db;
	border: 1px solid #3498db;
	color: #fff;
}

</style>
  <style><?= $設定['埋め込みCSS'] ?></style>

</head>
<body>


<header class="main-header">
<h1 class="main-title"><a href="<?= $設定['URL'] ?>"><?= $設定['ブログ名'] ?></a></h1>
<?= $設定['メインメニュー'] ?>
</header>

<article class="main-contents">
<ul class="tagcloud">
<?= $設定['カテゴリ一覧'] ?>
</ul>
</article>


<script src="<?= $設定['テンプレート'] ?>/blog.js" charset="utf-8"></script>
<script><?= $設定['埋め込みJavaScript'] ?></script>

</body>
</html>