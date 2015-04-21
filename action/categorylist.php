<?php
//======================================================
// ■カテゴリ一覧を表示する
// 
// http://127.0.0.1/basic-blog/?action=categorylist
// 呼び出し元: ../index.php
//======================================================


foreach(全カテゴリ() as $category => $count){
    $_category = rawurlencode($category);
    $設定['カテゴリ一覧'] .= "<li><a href=\"{$設定['URL']}?action=category&category=$_category\" class=\"js-category-name\" data-category-count=\"$count\">$category</a></li>\n";
}

//メニュー作成
$設定['メインメニュー'] = 部品作成("mainmenu");

//結果を表示して終了
テンプレート表示("{$設定['テンプレート']}/categorylist.html");
