<?php
//======================================================
// ■RSSを出力する (Atom 1.0)
// 
// http://127.0.0.1/blog/?action=feed
// 呼び出し元: ../index.php
//======================================================



$最新記事 = データベース取得("select * from ブログ where 記事状態 = '公開' order by 記事ID desc limit {$設定['フィード出力件数']}");

foreach($最新記事 as $entry){
    
    $iso8601 = 日付変換($entry['記事投稿時間'], 2);
    if(!$設定['Atom更新時間']){ $設定['Atom更新時間'] = $iso8601; }
    $entry['記事タイトル'] = h($entry['記事タイトル']);
    $entry['記事URL']      = 記事URL作成($entry['記事ID'], true);

    $設定['Atomエントリ'] .= "<entry>\n";
    $設定['Atomエントリ'] .= "  <title>{$entry['記事タイトル']}</title>\n";
    $設定['Atomエントリ'] .= "  <link href=\"{$entry['記事URL']}\" />\n";
    $設定['Atomエントリ'] .= "  <content><![CDATA[{$entry['記事本文']}]]></content>\n";
    $設定['Atomエントリ'] .= "  <published>$iso8601</published>\n";
    $設定['Atomエントリ'] .= "  <id>{$entry['記事URL']}</id>\n";
    $設定['Atomエントリ'] .= "</entry>\n";
}


//表示して終了
header("Content-Type: application/atom+xml; charset=UTF-8");
テンプレート表示("{$設定['テンプレート']}/feed.html");
