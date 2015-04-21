<?php
//======================================================
// ■コメントを記録する (POST)
// 
// http://127.0.0.1/basic-blog/?action=commentpost
// 呼び出し元: ../index.php
//======================================================


if (!自然数なら($_POST['entry_id'])){ エラー('不正なIDです'); }

//引っ掛け
if ($_POST['password'] or $_POST['url']){ エラー('投稿できません'); }


//ブログテーブルから該当記事データを取得
$entry = データベース行取得("select * from ブログ where 記事ID = {$_POST['entry_id']}");
if(!$entry['記事ID']){ エラー('その記事は存在しません。'); }


//コメントを受け付けているか？
if(!コメントが受付中なら($entry)) { エラー($設定['エラー']); }


//入力をチェック＆整形する

$_POST['comment_name']  = 投稿文字列処理($_POST['comment_name']);
$_POST['comment_mail']  = 投稿文字列処理($_POST['comment_mail']);
$_POST['comment_url']   = 投稿文字列処理($_POST['comment_url']);
$_POST['comment_site']  = 投稿文字列処理($_POST['comment_site']);
$_POST['comment_body']  = 投稿文字列処理($_POST['comment_body'] ,true);

if(strlen($_POST['comment_name']) > $設定['コメント名前欄の制限'])  { エラー('名前欄が大きすぎます'); }
if(strlen($_POST['comment_mail']) > $設定['コメントメール欄の制限']){ エラー('メール欄が大きすぎます'); }
if(strlen($_POST['comment_url'])  > $設定['コメントURL欄の制限'] )  { エラー('URL欄が大きすぎます'); }
if(strlen($_POST['comment_site']) > $設定['コメントサイト欄の制限']){ エラー('サイト欄が大きすぎます'); }
if(strlen($_POST['comment_body']) > $設定['コメント本文欄の制限']  ){ エラー('本文欄が大きすぎます'); }

if(!$_SERVER['REMOTE_HOST']) { $_SERVER['REMOTE_HOST'] = gethostbyaddr($_SERVER["REMOTE_ADDR"]); }


//二重投稿チェック
foreach(データベース取得("select * from コメント order by コメントID desc limit 10") as $最近のコメント){
    if($最近のコメント['コメント本文'] == $_POST['comment_body'] and $最近のコメント['コメントIP'] == $_SERVER['REMOTE_HOST']){
        エラー('二重投稿です');
    }
}


//コメントテーブルに追加
$コメントID = データベース追加("insert into コメント (記事ID, コメント投稿時間, コメント名前, コメントURL, コメントメール, コメントサイト, コメントIP, コメント本文) values ({$_POST['entry_id']}, {$_SERVER['REQUEST_TIME']}, ?, ?, ?, ?, ?, ?)", 
array($_POST['comment_name'], $_POST['comment_url'], $_POST['comment_mail'], $_POST['comment_site'], $_SERVER['REMOTE_HOST'], $_POST['comment_body']));

//ブログテーブルを更新
データベース更新("update ブログ set 記事コメント数 = 記事コメント数 + 1, 記事コメント最終時間 = {$_SERVER['REQUEST_TIME']} where 記事ID = {$_POST['entry_id']}");



//レスポンスを返して終了
setcookie("cn", $_POST['comment_name'],  $_SERVER['REQUEST_TIME']+60*60*24*$設定['ユーザ用クッキー有効日数']);
setcookie("cs", $_POST['comment_site'],  $_SERVER['REQUEST_TIME']+60*60*24*$設定['ユーザ用クッキー有効日数']);

リダイレクト(記事URL作成($_POST['entry_id']) . "#comment{$_POST['entry_id']}-$コメントID");

