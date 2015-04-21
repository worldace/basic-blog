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


//表示して終了
$設定['記事URL'] = 記事URL作成($entry['記事ID']);
テンプレート表示("{$設定['テンプレート']}/entryeditform.html");
