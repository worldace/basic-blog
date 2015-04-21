<?php
//======================================================
// ■編集した記事を記録する (管理用、POST)
// 
// http://127.0.0.1/basic-blog/?action=entryedit
// 呼び出し元: ../index.php
//======================================================


パスワードチェック();

if (!自然数なら($_POST['id'])){ エラー('不正なIDです。'); }

//入力の検証と整形
$_POST['title'] = 投稿文字列処理($_POST['title']);

if($_POST['category']){
    $_POST['category']  = 投稿文字列処理($_POST['category']);
    $_POST['category']  = implode("\n", array_unique(preg_split("/[\s\t　]+/u", $_POST['category']))); //カテゴリは重複を除去し改行区切りに変換
    $_POST['category'] .= "\n"; //最後に改行を付け加える
}

if (!$_POST['title']){ エラー("タイトルを入力してください"); }
if (!$_POST['body']) { エラー("本文を入力してください"); }

$アイキャッチ画像 = アイキャッチ画像検索($_POST['body']);
$サムネイル画像   = サムネイル画像検索($アイキャッチ画像);

//ブログテーブルを更新
データベース更新("update ブログ set 記事タイトル = ?, 記事本文 = ?, 記事カテゴリ = ?, 記事アイキャッチ画像 = ?, 記事サムネイル画像 = ?, 記事状態 = ?, 記事更新時間 = {$_SERVER['REQUEST_TIME']} where 記事ID = {$_POST['id']}",
array($_POST['title'], $_POST['body'], $_POST['category'], $アイキャッチ画像, $サムネイル画像, $_POST['entry-status']));

//履歴テーブルに追加
データベース追加("insert into 履歴 (記事ID, 履歴投稿時間, 履歴本文) values ({$_POST['id']}, {$_SERVER['REQUEST_TIME']}, ?)",
array($_POST['body']));


//テンプレート変数をセット
$設定['記事ID']  = $_POST['id'];
$設定['記事URL'] = 記事URL作成($設定['記事ID']);

//終了
テンプレート表示("{$設定['テンプレート']}/refresh.html");

