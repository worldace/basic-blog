<?php
//======================================================
// ■記事の編集フォームを表示する (管理用)
// 
// http://127.0.0.1/basic-blog/?action=entryeditform&id=
// 呼び出し元: ../index.php
//======================================================


パスワードチェック();
if (!自然数なら($_GET['id'])){ エラー('不正なIDです。'); }

//データ取得
$entry = データベース行取得("select * from ブログ where 記事ID = {$_GET['id']}");

if (!$entry['記事ID']){ エラー("IDが存在しません"); }

$entry['記事カテゴリ'] = rtrim($entry['記事カテゴリ']); //末尾の改行削除
$entry['記事カテゴリ'] = str_replace("\n", " ", $entry['記事カテゴリ']); //カテゴリは改行区切り→空白区切りに変換
$設定 += $entry;


//履歴一覧を作る
$履歴一覧 = データベース取得("select * from 履歴 where 記事ID = {$_GET['id']}");
    
foreach($履歴一覧 as $version){
    $i++;
    $date = 日付変換($version['履歴投稿時間']);
    $設定['履歴一覧'] = "<tr class=\"version-tr\"><td data-vid=\"{$version['履歴ID']}\" data-version=\"{$i}\">第{$i}版</td><td>$date</td></tr>\n" . $設定['履歴一覧'];
}


//カテゴリ候補を作る
foreach(全カテゴリ() as $category => $count){
    $category = h($category);
    $設定['カテゴリ候補'] .= "<li>$category</li>\n";
}


//アップ可能な最大サイズ
$設定['$ファイルサイズ制限'] = ini_get('upload_max_filesize');
$設定['ファイルサイズ制限']  = バイト数に変換($設定['$ファイルサイズ制限']);
$設定['$POST制限'] = ini_get('post_max_size');
$設定['POST制限']  = バイト数に変換($設定['$POST制限']);


$設定['記事URL'] = 記事URL作成($entry['記事ID']);



?>
<!DOCTYPE html>
<html lang="ja" class="dropzone">
<head>
  <meta charset="utf-8">
  <title><?= $設定['ブログ名'] ?> 記事の編集</title>
  <link href="<?= $設定['テンプレート'] ?>/base-admin.css" rel="stylesheet">
  <link href="<?= $設定['テンプレート'] ?>/blog.css" rel="stylesheet">
  <link href="<?= $設定['テンプレート'] ?>/post.css" rel="stylesheet">
  <link rel="icon" href="<?= $設定['テンプレート'] ?>/favicon.png" type="image/png">
  <link rel="index" href="<?= $設定['URL'] ?>">

  <script src="<?= $設定['jQuery'] ?>"></script>
</head>
<body>


<header class="main-header">
<h1 class="main-title"><?= $設定['ブログ名'] ?></h1>
</header>


<article class="main-contents">
<div class="tab">
<ul>
  <li class="tab-selected" id="tab-main">記事編集</li>
  <li id="tab-preview">プレビュー</li>
  <li id="tab-thumbnail">サムネイル</li>
  <li id="tab-version">履歴</li>
  <li id="tab-setting">設定</li>
  <li id="tab-entrydelete">削除</li>
</ul>
<section id="tab-main-section" class="tab-selected">
<form id="entryedit" action="<?= $設定['URL'] ?>?action=entryedit" method="POST">
<div class="form-line"><label for="title">タイトル</label><input type="text" id="title" name="title" value="<?= h($設定['記事タイトル']) ?>"></div>
<div class="form-line"><label for="category">カテゴリ</label><input type="text" id="category" class="candidate-dropdown" name="category" value="<?= h($設定['記事カテゴリ']) ?>" autocomplete="off"><ul class="candidate-list"><?= $設定['カテゴリ候補'] ?></ul></div>
<div class="form-line"><textarea name="body" spellcheck="false"><?= h($設定['記事本文']) ?></textarea></div>
<div class="form-line"><input type="submit" name="submit" value="記事を更新する"></div>
<input type="hidden" name="id" value="<?= $設定['記事ID'] ?>">
<input type="hidden" name="entry-status" value="">
</form></section>

<section id="tab-preview-section">
<iframe id="preview-iframe" src="<?= $設定['テンプレート'] ?>/preview.html" width="980" height="0" frameborder="0" scrolling="no"></iframe>
</section>

<section id="tab-thumbnail-section">
<div id="thumbnail-image-wrapper" data-w="<?= $設定['サムネイル横幅'] ?>" data-h="<?= $設定['サムネイル縦幅'] ?>"></div>
<button id="thumbnail-make" class="submit">サムネイルを作成する <span id="thumbnail-exists"></span></button>
<div id="thumbnail-preview-wrapper"></div>
</section>

<section id="tab-version-section">
<div><button id="version-restore" class="submit" data-vid="">記事を復元する</button></div>
<table id="version-list">
<tr><th>バージョン</th><th>投稿日時</th></tr>
<?= $設定['履歴一覧'] ?>
</table>
<iframe id="version-iframe" src="<?= $設定['テンプレート'] ?>/preview.html" width="980" height="0" frameborder="0" scrolling="no"></iframe>
</section>

<section id="tab-setting-section">
<table id="setting">
<tr><th>記事のURL</th><td><a href="<?= $設定['記事URL'] ?>" target="_blank" title="新しいウインドウで開く"><?= $設定['記事URL'] ?></a></td></tr>
<tr><th>記事の公開</th><td><select data-selected="<?= $設定['記事状態'] ?>" id="setting-entry-status"><option value="公開">公開する</option><option value="非公開">非公開にする</option></select></td></tr>
</table>
</section>

<section id="tab-entrydelete-section">
<form id="entrydelete" action="<?= $設定['URL'] ?>?action=entrydelete" method="POST">
<input type="submit" name="submit" value="記事を削除する">
<input type="hidden" name="id" value="<?= $設定['記事ID'] ?>">
</form></section>


</div>
</article>


<link rel="stylesheet" href="<?= $設定['テンプレート'] ?>/jquery.jcrop.css">
<link rel="stylesheet" href="<?= $設定['テンプレート'] ?>/jquery.nprogress.css">
<script src="<?= $設定['テンプレート'] ?>/jquery.selection.js" charset="utf-8"></script>
<script src="<?= $設定['テンプレート'] ?>/jquery.ajaxqueue.js" charset="utf-8"></script>
<script src="<?= $設定['テンプレート'] ?>/jquery.nprogress.js" charset="utf-8"></script>
<script src="<?= $設定['テンプレート'] ?>/jquery.jcrop.js" charset="utf-8"></script>
<script src="<?= $設定['テンプレート'] ?>/marked.js" charset="utf-8"></script>
<script src="<?= $設定['テンプレート'] ?>/post.js" charset="utf-8"></script>

<script>
var ファイルサイズ制限  = <?= $設定['ファイルサイズ制限'] ?>;
var $ファイルサイズ制限 = "<?= $設定['$ファイルサイズ制限'] ?>";
var POST制限  = <?= $設定['POST制限'] ?>;
var $POST制限 = "<?= $設定['$POST制限'] ?>";
</script>
<script src="<?= $設定['テンプレート'] ?>/dndupload.js" charset="utf-8"></script>

</body>
</html>