<?php
//■非公開記事

パスワードチェック();

if (!自然数なら($_GET['page'])){ $_GET['page'] = 1; }


//ブログテーブルから記事のデータを取得
$取得位置 = $設定['ライトインデックス記事表示件数'] * $_GET['page'] - $設定['ライトインデックス記事表示件数'];
$取得件数 = $設定['ライトインデックス記事表示件数'] + 1; //ページめくり用に1件多く

$検索結果 = データベース取得("select * from ブログ where 記事状態 != '公開' order by 記事ID desc limit $取得位置, $取得件数");


//ライトインデックス作成
$設定['ライトインデックス'] = 部品作成("lightindex", $検索結果);

//ページめくり作成
$設定['ページめくり'] = 部品作成("paging", $_GET['page'], $設定['ライトインデックス記事表示件数'], count($検索結果), "{$設定['URL']}?action=tool&tool=privatelist&page=");

//メニュー作成
$設定['メインメニュー'] = 部品作成("mainmenu");

//表示して終了
$設定['サブタイトル'] = "非公開記事";
テンプレート表示("{$設定['テンプレート']}/light.html");



