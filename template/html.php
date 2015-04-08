<?php
//======================================================
// ■共通して利用するHTMLテンプレート＋HTML作成関数
// 
// 呼び出し元: ../index.php
//======================================================



/*─ ■記事■ ─*/

$設定['_記事']
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



function 記事のHTML作成($entry) {
    global $設定;

    $entry['記事URL']      = 記事URL作成($entry['記事ID']);
    $entry['記事URL2']     = preg_replace("/^https*:\/\//", "", $entry['記事URL']);
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
    return テンプレート変換($設定['_記事'], $entry);
}


/*─ ■ヘッドライン■ ─*/

$設定['_ヘッドライン']
=<<<───────────────────────────
<div class="headline-box">
<span class="headline-time">《記事投稿時間》</span><span class="headline-category js-category-name">《記事メインカテゴリ》</span>
<a href="《記事URL》"><img src="《記事サムネイル画像》" width="《サムネイル横幅》" height="《サムネイル縦幅》" class="headline-image"><h2 class="headline-title">《記事タイトル》</h2></a>
</div>

───────────────────────────;


function ヘッドラインのHTML作成($検索結果, $category = ""){
    global $設定;

    foreach($検索結果 as $entry){
        $i++;
        if($i > $設定['ヘッドライン記事表示件数']) { break; }

        if(!$entry['記事ID']){ return ""; }

        $entry['記事URL']      = 記事URL作成($entry['記事ID']);
        $entry['記事投稿時間'] = 日付変換($entry['記事投稿時間'], 6);

        if(!$entry['記事カテゴリ']){
            $entry['記事カテゴリ'] = $設定['カテゴリなし'];
        }

        if($category){
            $entry['記事メインカテゴリ'] = $category;
        }
        else{
            $entry['記事メインカテゴリ'] = preg_replace('/\s.*/', '', $entry['記事カテゴリ']); //カテゴリは最初の1つだけ表示する
        }
        
        if(!$entry['記事サムネイル画像']){
            $entry['記事サムネイル画像'] = $設定['URL'] . $設定['テンプレート'] . "/" . $設定['サイトアイキャッチサムネイル画像'];
        }
        $entry['サムネイル横幅'] = $設定['サムネイル横幅'];
        $entry['サムネイル縦幅'] = $設定['サムネイル縦幅'];

        $html .= テンプレート変換($設定['_ヘッドライン'], $entry);
    }
    
    return $html;
}



/*─ ■ライトインデックス■ ─*/

function ライトインデックスのHTML作成($検索結果){
    global $設定;

    foreach($検索結果 as $entry){
        $i++;
        if($i > $設定['ライトインデックス記事表示件数']) { break; }

        $entry['記事URL']            = 記事URL作成($entry['記事ID']);
        $entry['記事投稿者']         = $設定['管理者'];
        $entry['記事ページビュー数'] = number_format($entry['記事ページビュー数']);
        $entry['記事投稿時間']       = 日付変換($entry['記事投稿時間'], 3);

        if($entry['記事コメント最終時間']){
            $entry['記事コメント最終時間'] = 日付変換($entry['記事コメント最終時間'], 3);
        }
        if(!$entry['記事コメント数']){
            $entry['記事コメント数'] = '-';
        }
        if(!$entry['記事コメント最終時間']){
            $entry['記事コメント最終時間'] = '-';
        }


        //カテゴリリンク作成
        if(!$entry['記事カテゴリ']){
            $entry['記事カテゴリ'] = $設定['カテゴリなし'];
        }

        if($設定['カテゴリ']){ 
            $entry['記事メインカテゴリ'] = $設定['カテゴリ'];
        }
        else{
            $entry['記事メインカテゴリ'] = preg_replace('/\s.*/', '', $entry['記事カテゴリ']); //カテゴリは最初の1つだけ表示する
        }

        $_category = urlencode($entry['記事メインカテゴリ']);
        $entry['記事カテゴリリンク'] = "<a href=\"{$設定['URL']}?action=category&category=$_category\" title=\"{$entry['記事カテゴリ']}\" class=\"js-category-name\">{$entry['記事メインカテゴリ']}</a>";

        $html .= "<tr><td><a href=\"{$entry['記事URL']}\">{$entry['記事タイトル']}</a></td><td>{$entry['記事カテゴリリンク']}</td><td><time>{$entry['記事投稿時間']}</time></td><td>{$entry['記事ページビュー数']}</td><td>{$entry['記事コメント数']}</td><td><time>{$entry['記事コメント最終時間']}</time></td></tr>\n";
    }
    
    return $html;
}



/*─ ■メインメニュー■ ─*/


$設定['_メインメニュー']
=<<<───────────────────────────
<nav class="main-menu dropdown">
<button class="dropdown-button">メニュー<span class="dropdown-button-caret"></span></button>
<ul class="dropdown-menu dropdown-menu-right">
  <li><a href="《URL》?action=light">ライトモード</a></li>
  <li><a href="《URL》?action=categorylist">カテゴリ一覧</a></li>
  <li><a href="《URL》?action=search">記事検索</a></li>
  <li class="dropdown-submenu"><a>最近見た記事</a>
    <ul class="dropdown-menu browsing-history">
      <li><a>(なし)</a></li>
    </ul></li>
  <li><a href="《URL》">トップページ</a></li>
  <li class="dropdown-separate"></li>
  <li><a href="《URL》?action=login" rel="nofollow">ログイン</a></li>
</ul>
</nav>
───────────────────────────;


$設定['_管理用:メインメニュー']
=<<<───────────────────────────
<nav class="main-menu dropdown">
<button class="dropdown-button">メニュー<span class="dropdown-button-caret"></span></button>
<ul class="dropdown-menu dropdown-menu-right">
  <li><a href="《URL》?action=light">ライトモード</a></li>
  <li><a href="《URL》?action=categorylist">カテゴリ一覧</a></li>
  <li><a href="《URL》?action=search">記事検索</a></li>
  <li class="dropdown-submenu"><a>最近見た記事</a>
    <ul class="dropdown-menu browsing-history">
      <li><a>(なし)</a></li>
    </ul></li>
  <li><a href="《URL》">トップページ</a></li>
  <li class="dropdown-separate"></li>
  <li class="js-postlink"><a href="《URL》?action=entrypostform" target="_blank">新規投稿</a></li>
  <li><a href="《URL》?action=uplist" target="_blank">アップリスト</a></li>
  <li class="dropdown-submenu"><a>ツール</a>
      <ul class="dropdown-menu">
        《ツール一覧》
      <li class="dropdown-separate"></li>
        <li><a href="《テンプレート》/@design.html" target="_blank">デザイン見本</a></li>
        <li><a href="《ベースURL》readme.html" target="_blank">説明書</a></li>
    </ul></li>
  <li><a href="《URL》?action=login">ログイン</a></li>
  <li><a href="《URL》?action=logout">ログアウト</a></li>
</ul>
</nav>
───────────────────────────;


function メインメニューのHTML作成(){
    global $設定;

    if(管理者なら()){
        $設定['ツール一覧'] = ツール一覧のHTML作成();
        return テンプレート変換($設定['_管理用:メインメニュー'], $設定);
    }
    else{
        return テンプレート変換($設定['_メインメニュー'], $設定);
    }
}


function ツール一覧のHTML作成(){
    global $設定;

    $dir = $設定['actionディレクトリ'] . '/tool';

    foreach(ファイル一覧取得($dir) as $file){
        if(pathinfo($file, PATHINFO_EXTENSION) != "php"){ continue; }

        $contents = file("$dir/$file");

        if(preg_match('|■(.+)|u', $contents[1], $match)){ //ツール名はファイルの2行目に書く仕様
            $tool  = pathinfo($file, PATHINFO_FILENAME);
            $name  = rtrim($match[1]);
            $html .= "<li><a href=\"{$設定['URL']}?action=tool&tool=$tool\" target=\"_blank\">$name</a></li>";
        }
    }
    return $html;
}


/*─ ■コメント■ ─*/


$設定['_コメント']
=<<<───────────────────────────
<article id="comment《記事ID》-《コメントID》" class="comment" data-comment_id="《コメントID》">
<header class="comment-header"><span class="comment-no">《コメント番号リンク》</span><span class="comment-name" data-comment-site="《コメントサイト》">《コメント名前》</span><time class="comment-date">《コメント投稿時間》</time>《管理用:コメント削除リンク》</header>
<p class="comment-body">《コメント本文》</p></article>


───────────────────────────;


function コメントのHTML作成($検索結果, $i = 1){
    global $設定;

    foreach($検索結果 as $comment){
        $comment['コメント番号'] = $設定['コメント最大番号'] = $i;
        $i++;

        if($comment['コメント状態'] == '削除'){ continue; }

        //コメント番号
        if($_GET['action'] == 'comment'){
            $comment['コメント番号リンク'] = $comment['コメント番号'];
        }
        else{
            $comment['コメント番号リンク'] = "<a href=\"{$設定['URL']}?action=comment&entry_id={$comment['記事ID']}&comment_id={$comment['コメントID']}\" target=\"_blank\" rel=\"nofollow\">{$comment['コメント番号']}</a>";
        }

        //コメント名前
        if(!$comment['コメント名前']){
            $comment['コメント名前'] = $設定['コメント標準名'];
        }

        //コメント投稿時間
        $comment['コメント投稿時間'] = 経過時間($comment['コメント投稿時間']);
        //コメント本文
        $comment['コメント本文'] = preg_replace("/＞＞(\d+)/", "<a href=\"#comment{$comment['記事ID']}-$1\" class=\"comment-anker\">&gt;&gt;$1</a>", $comment['コメント本文']);

        if(管理者なら()){
            $comment['管理用:コメント削除リンク'] = "<a href=\"{$設定['URL']}?action=commentdelete&entry_id={$comment['記事ID']}&comment_id={$comment['コメントID']}\" class=\"comment-delete\">.</a>";
        }

        $html .= テンプレート変換($設定['_コメント'], $comment);
    }
    return $html;
}


/*─ ■コメントフォーム■ ─*/


$設定['_コメントフォーム']
=<<<───────────────────────────
<form action="《URL》" method="POST" id="comment-form" class="comment-form" accept-charset="utf-8">
<div class="form-line comment-form-line"><label>名前</label><input type="text" name="comment_name" value=""></div>
<div class="form-line comment-form-line"><textarea name="comment_body"></textarea></div>
<div class="form-line comment-form-line"><input type="submit" value="コメントする"></div>
<input type="hidden" name="entry_id" value="《記事ID》">
<input type="password" name="password" class="comment-form-dummy"><input type="text" name="url" class="comment-form-dummy">
</form>
───────────────────────────;

function コメントフォームのHTML作成(){
    global $設定;

    return テンプレート変換($設定['_コメントフォーム'], $設定);
}



/*─ ■ページめくり■ ─*/


function ページめくりのHTML作成($現在のページ番号, $表示件数, $DB取得件数, $リンク){

    if($現在のページ番号 > 1){
        $前のページ番号 = $現在のページ番号 - 1;
        $navi .= "<a href=\"{$リンク}{$前のページ番号}\" rel=\"prev\" class=\"paging-leftlink\">新しい記事へ</a> ";
    }
    if($DB取得件数 > $表示件数){
        $次のページ番号 = $現在のページ番号 + 1;
        $navi .= "<a href=\"{$リンク}{$次のページ番号}\" rel=\"next\" class=\"paging-rightlink\">過去の記事へ</a> ";
    }

    if($navi){
        return "<nav class=\"paging\">$navi</nav>";
    }
}



/*─ ■その他■ ─*/

$設定['_管理用:記事状態の表示']
=<<<───────────────────────────
<div class="center"><p class="infobox-green">この記事は管理者だけに表示されます。(記事状態:《記事状態》)</p></div>
───────────────────────────;


