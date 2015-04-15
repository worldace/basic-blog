<?php
//======================================================
// ■メインメニュー部品
// 
// 呼び出し元: ../action/php/function.php 部品作成()
//======================================================


function _mainmenu(){
    global $設定;

    if(管理者なら()){
        $設定['ツール一覧'] = _ツール一覧作成();
        $html = テンプレート変換($設定['_管理用メインメニュー'], $設定);
    }
    else{
        $html = テンプレート変換($設定['_メインメニュー'], $設定);
    }

    return $html;
}


function _ツール一覧作成(){
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


$設定['_メインメニュー']
=<<<───────────────────────────
<nav class="mainmenu dropdown">
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



$設定['_管理用メインメニュー']
=<<<───────────────────────────
<nav class="mainmenu dropdown">
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



$設定['_mainmenu_CSS']
=<<<'───────────────────────────'
.mainmenu {
    text-align: right;
    position: relative;
}
.dropdown-button {
    cursor: pointer;
    padding: 4px 12px;
    margin-bottom: 0;
    font-size: 14px;
    line-height: 20px;
    color: #333333;
    text-align: center;
    text-shadow: 0 1px 1px rgba(255, 255, 255, 0.75);
    vertical-align: middle;
    background-color: #f5f5f5;
    background: linear-gradient(to bottom, #fff 0%, #fff 66%, #f5f5f5 66%, #f5f5f5 100%);
    border: 1px solid #cccccc;
    border-radius: 3px;
}
.dropdown-button-caret {
    display: inline-block;
    width: 0;
    height: 0;
    vertical-align: top;
    border-top: 4px solid #000000;
    border-right: 4px solid transparent;
    border-left: 4px solid transparent;
    margin-left: 5px;
    margin-top: 8px;
}
.dropdown-menu {
    position: absolute;
    left: 0;
    z-index: 3;
    display: none;
    min-width: 160px;
    padding: 5px 0;
    margin: 0;
    list-style: none;
    background-color: #fff;
    border: 1px solid #999;
    border-radius: 3px;
    box-shadow: 2px 2px 1px rgba(50,50,50,0.1);
}
.dropdown-menu-right {
    left: auto;
    right: 0; /*右寄せ*/
}
.dropdown-menu > li{
    font-family: 'MS PGothic',sans-serif;
    font-size: 16px;
}
.dropdown-separate{
    height: 2px;
    margin: 9px 1px;
    overflow: hidden;
    background-color: #e5e5e5;
    border-bottom: 1px solid #fff;
}
.dropdown-menu > li > a {
    display: block;
    padding: 3px 20px;
    font-weight: normal;
    line-height: 20px;
    color: #333333;
    white-space: nowrap;
    text-align: left;
}
.dropdown-menu > li > a:hover,
.dropdown-submenu:hover > a,
.dropdown-submenu:focus > a{
    color: #fff;
    text-decoration: none;
    background-color: #0081c2;
    background-image: linear-gradient(to bottom, #0088cc, #0077b3);
}

.dropdown-submenu {
    position: relative;
}
.dropdown-submenu > .dropdown-menu {
    top: 0;
    left: 100%;
    margin-top: -6px;
    margin-left: -1px;
    border-radius: 6px;
}
.dropdown-submenu:hover > .dropdown-menu {
    display: block;
}
.dropdown-submenu > a:after {/* キャレット */
    display: block;
    float: right;
    width: 0;
    height: 0;
    margin-top: 5px;
    margin-right: -10px;
    border-color: transparent;
    border-left-color: #333;
    border-style: solid;
    border-width: 5px 5px 5px 5px;
    content: " ";
}
.dropdown-submenu:hover > a:after {/* キャレット */
    border-left-color: #fff;
}
.dropdown-submenu-dropleft {
    float: none;
}
.dropdown-submenu-dropleft > .dropdown-menu {
    left: -100%;
    /*margin-left: 10px;*/
    border-radius: 6px;
}
.dropdown-submenu-dropleft > a:after {/* キャレット方向:左矢印 */
    border-left-color: transparent;
    border-right-color: #333;
}
.dropdown-submenu-dropleft:hover > a:after {/* キャレット方向:左矢印 */
    border-left-color: transparent;
    border-right-color: #fff;
}

───────────────────────────;




$設定['_mainmenu_JavaScript']
=<<<'───────────────────────────'
$(function () {

var onmouse;

$('.dropdown-button').click(function() {
    $(this).parents(".dropdown").children('.dropdown-menu').slideToggle(150);
});

$('.dropdown-menu a').click(function() {
    //hrefがない場合はなにもせずに終了
    if($(this).attr('href') == null){ return false; }

    $(this).parents(".dropdown").children('.dropdown-menu').slideUp(0);
});

$('.dropdown-button, .dropdown-menu').hover(
    function(){ onmouse = true;  },
    function(){ onmouse = false; }
);

$(document).click(function() {
    if (onmouse == false) {
        $('.dropdown-menu').slideUp(80);
    }
});

$(".dropdown-submenu > .dropdown-menu").on({
    mouseenter:function(){
        $(document).scrollLeft($(document).width() - $(window).width());
    },
    mouseleave:function(){
        $(document).scrollLeft(0);
    }
});


!function 最近見た記事を作成する(){
    var html = "";

    if(!localStorage || !localStorage.browsing_history){
        return;
    }

    var recent = JSON.parse(localStorage.browsing_history);

    for(var i = 0; i < recent.length; i++){
        if(!recent[i].url || !recent[i].title){
            continue;
        }
        html += '<li title="' + recent[i].title + '"><a href="' + recent[i].url + '">' + recent[i].title + '</a></li>\n';
    }

    if(html){
        $(".browsing-history").html(html);
    }
}();



});

───────────────────────────;

