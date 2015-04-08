<?php
//======================================================
// ■データベースのテーブル定義 SQLite/MySQL共用
// 
// 呼び出し元: ./install.php
//======================================================


/*
□SQLite
・primary keyは自動的にautoincremanetになる。現在の最大IDが割り当てられる (最大IDを削除した場合に重複する可能性がある)
・primary keyの後にautoincremanetをつけることもできる … 歴代の最大IDが割り当てられる (重複することはない)
・型は自動判別される。TEXT型のカラムにINTEGERのデータ型の値が格納された場合、TEXT型に変換
・create時、データ型が文字列「INT」を含む場合、INTEGER型となる
・create時、データ型が文字列「CHAR」「BLOB」「TEXT」のいずれかを含む場合、TEXT型となる

□MySQL
「autoincrement」ではなく「auto_increment」なので注意
*/

//テーブルの型情報(MySQL互換)
$設定['テーブル定義:ブログ'] = array(
"記事ID"               => "integer primary key auto_increment",
"記事評価"             => "varchar(32)",
"記事状態"             => "varchar(32) default '公開'",
"記事タイトル"         => "varchar(255)",
"記事カテゴリ"         => "varchar(255)",
"記事本文"             => "text",
"記事投稿時間"         => "integer",
"記事更新時間"         => "integer",
"記事アイキャッチ画像" => "varchar(255)",
"記事サムネイル画像"   => "varchar(255)",
"記事コメント許可"     => "varchar(32) default '○'",
"記事コメント数"       => "integer default 0",
"記事コメント最終時間" => "integer",
"記事ページビュー数"   => "integer default 0",
"記事関連"             => "text",
"記事備考"             => "text"
);


$設定['テーブル定義:コメント'] = array(
"コメントID"           => "integer primary key auto_increment",
"記事ID"               => "integer",
"コメント評価"         => "varchar(32)",
"コメント状態"         => "varchar(32) default '公開'",
"コメント名前"         => "varchar(64)",
"コメントURL"          => "varchar(255)",
"コメントメール"       => "varchar(128)",
"コメントIP"           => "varchar(255)",
"コメントサイト  "     => "varchar(32)",
"コメント本文"         => "text",
"コメント投稿時間"     => "integer",
"コメント備考"         => "text"
);


$設定['テーブル定義:履歴'] = array(
"履歴ID"               => "integer primary key auto_increment",
"記事ID"               => "integer",
"履歴本文"             => "text",
"履歴投稿時間"         => "integer",
"履歴備考"             => "text"
);


$設定['テーブル定義:状態'] = array(
"状態ID"               => "integer primary key auto_increment",
"状態名"               => "varchar(32)",
"状態内容"             => "text",
"状態備考"             => "text"
);

