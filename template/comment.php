<?php
//======================================================
// ■コメント部品
// 
// 呼び出し元: ../action/php/function.php 部品作成()
//======================================================



function _comment($検索結果, $i = 1){
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
            $comment['管理用:コメント削除リンク'] = "<a href=\"{$設定['URL']}?action=commentdelete\" class=\"comment-delete\">.</a>";
        }

        $html .= テンプレート変換($設定['_comment_HTML'], $comment);
    }
    return $html;
}

$設定['_comment_HTML']
=<<<━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
<article id="comment《記事ID》-《コメントID》" class="comment" data-comment_id="《コメントID》">
<header class="comment-header"><span class="comment-no">《コメント番号リンク》</span><span class="comment-name" data-comment-site="《コメントサイト》">《コメント名前》</span><time class="comment-date">《コメント投稿時間》</time>《管理用:コメント削除リンク》</header>
<p class="comment-body">《コメント本文》</p></article>


━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━;


$設定['_comment_CSS']
=<<<'━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━'
.comments {
    margin: 50px 0;
}
.comment {
    width: 70%;
    margin-bottom: 30px;
}
.comment-header {
    border-top-left-radius: 4px;
    border-top-right-radius: 4px;
    background-color: #f7f7f7;
    border: solid 1px #d0d0d0;
    border-bottom: #e5e5e5 1px solid;
    width: 100%;
    margin-bottom: 0;
    padding: 5px 18px;
    font-size: 16px;
}
.comment-body {
    width: 100%;
    margin-top: 0;
    padding: 18px;
    font-size: 16px;
    border-left: solid 1px #d0d0d0;
    border-right: solid 1px #d0d0d0;
    border-bottom: solid 1px #d0d0d0;
    border-bottom-left-radius: 4px;
    border-bottom-right-radius: 4px;
}
.comment-no,
.comment-no a,
.comment-no visited,
.comment-no hover{
    font-size: 16px;
    color: #777;
    font-family: Arial, Meiryo, sans-serif;
}
.comment-name{
    margin-left: 14px;
    margin-right: 14px;
}
.comment-date{
    font-size: 14px;
    color: #777;
    font-family: Arial, Meiryo, sans-serif;
}


/*** 削除関連 ***/
.comment-delete{
}
.comment-delete-selected {
    background-color: #ffeeee;
}



/*** レスポップアップ ***/
.comment-anker {
    position:relative;
    font-family: 'Courier New', Courier, Monaco, monospace;
}
.comment-popup {
    display: inline-block;
	font-size: 16px;
	font-family: Meiryo, sans-serif;
	letter-spacing: 1px;
	line-height: 1.4;
	white-space: pre;
    color: #fff;
	background-color: #000;
    opacity: 0.8;
    position: absolute;
    bottom: 32px;
    left: 8px;
    z-index: 100;
    padding: 10px;
    border-radius: 6px;
    max-width: 680px;
    /*max-width: 940px;*/
}

.comment-popup:after {
    width: 100%;
    content: "";
    display: block;
    position: absolute;
    left: 8px;
    bottom: -7px;
    border-bottom: 8px solid transparent;
    border-left: 8px solid #000;
}

.comment-popup-name {
    margin-left: 10px;
    margin-right: 10px;
}
.comment-popup-header {
    width: 100%;
}
.comment-popup-body {
    width: 100%;
    margin: 0;
    /*border-top: solid 1px #fff;*/
}
.comment-popup a{
    color: #fff;
}


━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━;




$設定['_comment_JavaScript']
=<<<'━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━'
$(function () {




//--- コメント削除 ---//
$('.comment-delete').click(function() {
    var a          = $(this);
    var comment    = $(this).closest('.comment');
    var comment_id = $(this).closest('.comment').attr('data-comment_id');
    var entry_id   = $(this).closest('.comments').attr('data-entry_id');

    //削除確認
    comment.addClass("comment-delete-selected");
    var flag = window.confirm("このコメントを削除しますか？");
    comment.removeClass("comment-delete-selected");
    if(flag == false){ return false; }

    //削除処理
    $.ajax({
        url : a.attr('href'),
        type: 'POST',
        data: {'entry_id': entry_id, 'comment_id': comment_id},
        success: function(response) {
            comment.fadeOut();
            //コメント数を減少させる
            var comment_sum = $(".comment" + entry_id + "-sum");
            var count       = parseInt(comment_sum.first().text()) - 1;
            comment_sum.text(count);
        },
        error: function(xhr) {
            alert(xhr.responseText);
        }
    });

    return false;
});


//--- レスポップアップ ---//
$(".comment-anker").on({
    mouseenter:function(){
        var 対象番号 = parseInt($(this).text().replace(">>", ""));
        var 対象本文 = $(this).closest('.comments').find("[data-comment_id=" + 対象番号 + "]").find(".comment-body").html();

        if(対象本文){
            $('<article class="comment-popup"><p class="comment-popup-body">' + 対象本文 + '</p></article>').appendTo(this).hide().fadeIn(200);
        }
    },
    mouseleave:function(){
        $(this).find(".comment-popup").remove();
    }
});

$('body').on('click', function(){
    $(".comment-popup").remove();
});

});


━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━;
