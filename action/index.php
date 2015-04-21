<?php
//======================================================
// ■トップページを表示する
// 
// http://127.0.0.1/basic-blog/
// 呼び出し元: ../index.php
//======================================================


if (!自然数なら($_GET['page'])){ $_GET['page'] = 1; }


//ブログテーブルから記事のデータを取得
$取得位置 = $設定['ヘッドライン記事表示件数'] * $_GET['page'] - $設定['ヘッドライン記事表示件数'];
$取得件数 = $設定['ヘッドライン記事表示件数'] + 1; //ページめくり用に1件多く
$検索結果 = データベース取得("select * from ブログ where 記事状態 = '公開' order by 記事ID desc limit $取得位置, $取得件数");

//部品作成
$設定['ページめくり']   = 部品作成("paging", $_GET['page'], $設定['ヘッドライン記事表示件数'], count($検索結果), "{$設定['URL']}?page=");
$設定['メインメニュー'] = 部品作成("mainmenu");
$設定['ヘッドライン']   = 部品作成("headline", $検索結果);


//表示して終了
テンプレート表示("{$設定['テンプレート']}/index.html");

