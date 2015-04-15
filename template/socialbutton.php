<?php
//======================================================
// ■ソーシャルボタン部品
// 
// 呼び出し元: ../action/php/function.php 部品作成()
//======================================================

function _socialbutton(){
    global $設定;

    return テンプレート変換($設定['_ソーシャルボタン'], $設定);
}


$設定['_ソーシャルボタン']
=<<<───────────────────────────
<ul class="socialbutton-list">
  <li><a href="https://www.facebook.com/sharer.php?u=《%記事URL》&t=《%記事タイトル》" target="_blank"><img src="《テンプレート》/facebook-like.png" width="69" height="20"></a><a class="socialbutton-balloon facebook-like-count">0</a></li>
  <li><a href="http://b.hatena.ne.jp/add?mode=confirm&url=《%記事URL》&title=《%記事タイトル》" target="_blank"><img src="《テンプレート》/hatena-bookmark.png" width="80" height="20"></a><a href="http://b.hatena.ne.jp/entry/《記事URL2》" class="socialbutton-balloon hatena-bookmark-count" target="_blank">0</a></li>
  <li><a href="https://twitter.com/share?url=《%記事URL》&text=《%記事タイトル》" target="_blank"><img src="《テンプレート》/twitter-tweet.png" width="61" height="20"></a><a href="https://twitter.com/search?q=《%記事URL2》" class="socialbutton-balloon twitter-tweet-count" target="_blank">0</a></li>
</ul>

───────────────────────────;


$設定['_socialbutton_CSS']
=<<<'───────────────────────────'
.socialbutton-list{
    margin-top: 45px;
    padding-left: 0;
    list-style-type: none;
}
.socialbutton-list > li{
    font-family: sans-serif !important;
    display: inline-block !important;
    padding-right: 6px !important;
    vertical-align: top !important;
}
iframe.twitter-share-button {
    width: 110px !important;
    text-align: left !important;
}

.socialbutton-balloon{
    background-color: #fff;
    border: solid 1px #ccc;
    height: 20px;
    line-height: 18px;
    display: inline;
    float: right;
    margin-left: 6px;
    font-size: 11px;
    text-align: center;
    padding: 0 5px;
    border-radius: 2px;
    font-family: Helvetica, Arial, "hiragino kaku gothic pro", meiryo, "ms pgothic", sans-serif;
    position: relative;
    box-sizing: border-box;
}
.socialbutton-balloon:after,
.socialbutton-balloon:before {
    right: 100%;
    top: 50%;
    border: solid transparent;
    content: "";
    height: 0;
    width: 0;
    position: absolute;
}
.socialbutton-balloon:after {
    border-right-color: #fff;
    border-width: 4px;
    top: 5px;
}
.socialbutton-balloon:before {
    border-right-color: #ccc;
    border-width: 5px;
    top: 4px;
}
.socialbutton-balloon:link,
.socialbutton-balloon:visited,
.socialbutton-balloon:hover{
    color: #000;
    text-decoration: none;
}

───────────────────────────;


$設定['_socialbutton_JavaScript']
=<<<'───────────────────────────'
$(function () {

var url = $("link[rel='canonical']").attr('href');

var facebook_api = 'http://graph.facebook.com/?id=' + encodeURI(url);
$.getJSON(facebook_api + '&callback=?', function(json){
    var count = 0;
    if(json.shares){ count = json.shares; }
    $('.facebook-like-count').text(count);
});

var hatena_api = 'http://api.b.st-hatena.com/entry.count?url=' + encodeURI(url);
$.getJSON(hatena_api + '&callback=?', function(json){
    var count = 0;
    if(json){ count = json; }
    $('.hatena-bookmark-count').text(count);
});

var twitter_api = 'http://cdn.api.twitter.com/1/urls/count.json?url=' + encodeURI(url);
$.getJSON(twitter_api + '&callback=?', function(json){
    var count = 0;
    if(json.count){ count = json.count; }
    $('.twitter-tweet-count').text(count);
});


});

───────────────────────────;
