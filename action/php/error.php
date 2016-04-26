<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title><?= $設定['ブログ名'] ?> エラー</title>
  <link href="<?= $設定['テンプレート'] ?>/base-admin.css" rel="stylesheet">
  <link href="<?= $設定['テンプレート'] ?>/blog.css" rel="stylesheet">
</head>
<body>

<header class="main-header">
<h1 class="main-title"><a href="<?= $設定['URL'] ?>"><?= $設定['ブログ名'] ?></a></h1>
</header>

<div class="contents"><p class="infobox-red"><?= $設定['エラー'] ?></p></div>

</body>
</html>
