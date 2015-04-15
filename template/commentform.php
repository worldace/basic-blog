<?php
//======================================================
// ■コメントフォーム部品
// 
// 呼び出し元: ../action/php/function.php 部品作成()
//======================================================



function _commentform(){
    global $設定;

    return テンプレート変換($設定['_コメントフォーム'], $設定);
}



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



$設定['_commentform_CSS']
=<<<'───────────────────────────'

.comment-form{
    margin-top: 50px;
}
.comment-form-line{
    width: 66%;
}
.comment-form textarea{
    width: 100%;
    height: 180px;
}
.comment-form input[type="text"]{
    width: 200px;
}
.comment-form input[type="submit"]{
}
.comment-form-dummy{
    display: none;
}
───────────────────────────;


$設定['_commentform_JavaScript']
=<<<'───────────────────────────'
$(function () {

//--- 名前欄にクッキーセット ---//
$("input[name='comment_name']").val($.cookie("cn"));

//--- スパム対策 ---//
$(".comment-form").attr('action', $(".comment-form").attr('action') + '?action=commentpost');
$(".comment-form-dummy").css({'display': 'none'});


//--- 入力チェック ---//
$('.comment-form').submit(function() {
    var textarea = $(this).find("textarea");

    if(textarea.val() == ''){
        alert('コメントを入力してください');
        textarea.focus();
        return false;
    }
});

});

───────────────────────────;
