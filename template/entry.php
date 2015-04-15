<?php
//======================================================
// ■記事部品
// 
// 呼び出し元: ../action/php/function.php 部品作成()
//======================================================


function _entry($entry) {
    global $設定;

    $entry['記事URL']      = 記事URL作成($entry['記事ID']);
    $entry['記事投稿時間'] = 日付変換($entry['記事投稿時間'], 5);
    $entry['ISO8601']      = 日付変換($entry['記事投稿時間'], 2);
    $entry['記事投稿者']   = $設定['管理者'];
    $entry['記事カテゴリ'] = rtrim($entry['記事カテゴリ']);
    $entry['記事ページビュー数'] = number_format($entry['記事ページビュー数']);

    if($entry['記事カテゴリ'] == ""){
        $entry['記事カテゴリ'] = $設定['カテゴリなし'];
    }
    if($entry['記事コメント数'] > 0){
        $entry['記事コメント数文字列'] = " <span class=\"comment{$entry['記事ID']}-sum comment-sum\">{$entry['記事コメント数']}</span>";
    }

    //カテゴリリンク作成
    foreach(explode("\n", $entry['記事カテゴリ']) as $category){
        $_category = urlencode($category);
        $entry['記事カテゴリリンク'] .= "<a href=\"{$設定['URL']}?action=category&category=$_category\" class=\"category-link js-category-name\">$category</a> ";
    }
    
    //管理者なら編集リンク作成
    if(管理者なら()) {
        $entry['管理用:記事複製リンク'] = "<li class=\"article-header-copylink\"><a href=\"{$設定['URL']}?action=entrypostform&id={$entry['記事ID']}\" target=\"_blank\">複製</a></li>";
        $entry['管理用:記事編集リンク'] = "<li class=\"article-header-editlink js-editlink\"><a href=\"{$設定['URL']}?action=entryeditform&id={$entry['記事ID']}\" target=\"_blank\">編集</a></li>";
    }

    $設定 += $entry;
    return テンプレート変換($設定['_entry_HTML'], $entry);
}





$設定['_entry_HTML']
=<<<───────────────────────────
<article id="article《記事ID》" class="article">
  <header class="article-header">
  <h1 class="article-title"><a href="《記事URL》">《記事タイトル》</a></h1>
  <ul class="article-header-list">
    <li class="article-header-date"><time datetime="《ISO8601》">《記事投稿時間》</time></li>
    <li class="article-header-category">《記事カテゴリリンク》</li>
    <li class="article-header-author">《記事投稿者》</li>
    《管理用:記事複製リンク》
    《管理用:記事編集リンク》
    <li class="article-header-pageview">《記事ページビュー数》</li>
    <li class="article-header-commentlink"><a href="《記事URL》#comments《記事ID》">コメント</a> 《記事コメント数文字列》</li>
  </ul>
  </header>
  <div id="contens《記事ID》" class="contents">
  《記事本文》
  </div>
</article>


───────────────────────────;


//CSSはblog.css