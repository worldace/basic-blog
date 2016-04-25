<?php
//======================================================
// ■コメントフォーム部品
// 
// 呼び出し元: ../action/php/function.php 部品()
//======================================================



function _commentform(){
    global $設定;

    return テンプレート変換($設定['_commentform_HTML'], $設定);
}



$設定['_commentform_HTML']
=<<<━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
<form action="《URL》" method="POST" class="commentform" accept-charset="utf-8">
<div class="form-line commentform-line"><label>名前</label><input type="text" name="comment_name" value=""></div>
<div class="form-line commentform-line"><textarea name="comment_body"></textarea></div>
<div class="form-line commentform-line"><input type="submit" value="コメントする"></div>
<input type="hidden" name="entry_id" value="《記事ID》">
<input type="password" name="password" class="commentform-dummy"><input type="text" name="url" class="commentform-dummy">
</form>

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━;



$設定['_commentform_CSS']
=<<<'━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━'

.commentform{
    margin-top: 50px;
}
.commentform-line{
    width: 66%;
}
.commentform textarea{
    width: 100%;
    height: 180px;
}
.commentform input[type="text"]{
    width: 200px;
}
.commentform input[type="submit"]{
}
.commentform-dummy{
    display: none;
}

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━;



$設定['_commentform_JavaScript']
=<<<'━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━'
$(function () {

//--- 名前欄にクッキーセット ---//
$("input[name='comment_name']").val($.cookie("cn"));

//--- スパム対策 ---//
$(".commentform").attr('action', $(".commentform").attr('action') + '?action=commentpost');
$(".commentform-dummy").css({'display': 'none'});


//--- 入力チェック ---//
$('.commentform').submit(function() {
    var textarea = $(this).find("textarea");

    if(textarea.val() == ''){
        alert('コメントを入力してください');
        textarea.focus();
        return false;
    }
});

});

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━;
