<?php
//======================================================
// ■ログインフォームを表示する
// 
// http://127.0.0.1/basic-blog/?action=login
// 呼び出し元: ../index.php
//======================================================


//パスワードは設定している必要がある
if(!$設定['パスワード']){ エラー("パスワードが設定されていません<br>setting.phpでパスワードを設定してください。"); }


if($_GET['query']){ $設定['query'] = h($_GET['query']); }



?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="robots" content="noindex,nofollow,noarchive">
  <title><?= $設定['ブログ名'] ?> ログイン</title>
  <link href="<?= $設定['テンプレート'] ?>/base-admin.css" rel="stylesheet">
  <link href="<?= $設定['テンプレート'] ?>/blog.css" rel="stylesheet">
  <link rel="icon" href="<?= $設定['テンプレート'] ?>/favicon.png" type="image/png">

  <style>
    #login{ text-align: center;    margin-top: 180px; }
    #login input[name="password"]{ width: 220px; }
  </style>
  <script src="<?= $設定['jQuery'] ?>"></script>
</head>
<body>


<header class="main-header">
<h1 class="main-title"><?= $設定['ブログ名'] ?></h1>
</header>

<article class="main-contents">
<form id="login" action="<?= $設定['URL'] ?>?action=logincheck" method="POST" class="form-oneline">
<input type="password" name="password" value=""><input type="submit" name="submit" value="ログイン">
<input type="hidden" name="query" value="<?= $設定['query'] ?>">
</form>
</article>


<script>
$(function(){
    var password = $("#login input[name='password']");
    password.focus();

    $('#login').submit(function() {
        if(password.val() == ''){
            alert('パスワードを入力してください');
            password.focus();
            return false;
        }
    });
});
</script>

</body>
</html>