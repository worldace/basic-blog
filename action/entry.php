<?php
//======================================================
// ■記事を1件表示する
// 
// http://127.0.0.1/basic-blog/?action=entry&id=
// 呼び出し元: ../index.php
//======================================================


if(!自然数なら($_GET['id'])){ エラー('不正なIDです。'); }


//ブログテーブルから該当記事のデータを取得
$entry = データベース行取得("select * from ブログ where 記事ID = {$_GET['id']}");
$設定 += $entry;

if(!$entry['記事ID']){ エラー('その記事は存在しません。'); }


//非公開記事の場合
if($entry['記事状態'] != "公開"){
    if(!管理者なら()){
        エラー('この記事は公開されていません。');
    }
    $設定['管理用:記事状態の表示'] = "<div class=\"center\"><p class=\"infobox-green\">この記事は管理者だけに表示されます。(記事状態:{$entry['記事状態']})</p></div>";
}


//カウントアップ
if($設定['管理者のアクセスをカウントする'] == '○' or !管理者なら()){
    データベース更新("update ブログ set 記事ページビュー数 = 記事ページビュー数 + 1 where 記事ID = {$_GET['id']}");
}


//コメント一覧作成
if(コメントを表示するなら($entry)){
    $comment = データベース取得("select * from コメント where 記事ID = {$_GET['id']}");
    $設定['コメント'] = 部品作成("comment", $comment);
}

//コメントフォーム作成
if(コメントが受付中なら($entry)){
    $設定['コメントフォーム'] = 部品作成("commentform");
}

//アイキャッチ画像作成
if(!$設定['記事アイキャッチ画像']){
    $設定['記事アイキャッチ画像'] = "{$設定['ベースURL']}{$設定['テンプレート']}/{$設定['サイトアイキャッチ画像']}";
}

//前の記事と次の記事作成
$prev_entry = データベース取得("select * from ブログ where 記事ID < {$_GET['id']} order by 記事ID desc limit 1");
$next_entry = データベース取得("select * from ブログ where 記事ID > {$_GET['id']} order by 記事ID  asc limit 1");
$設定['前の記事'] = 部品作成("headline", $prev_entry, "←前の記事");
$設定['次の記事'] = 部品作成("headline", $next_entry, "次の記事→");


//記事とメニュー作成
$設定['記事URL']  = 記事URL作成($entry['記事ID']);
$設定['記事URL2'] = preg_replace("/^https*:\/\//", "", $設定['記事URL']);


$設定['メインメニュー']   = 部品作成("mainmenu");
$設定['ソーシャルボタン'] = 部品作成("socialbutton");
$設定['記事'] = 部品作成("entry", $entry);


//表示して終了
テンプレート表示("{$設定['テンプレート']}/entry.html");
