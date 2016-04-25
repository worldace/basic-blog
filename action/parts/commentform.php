<?php
//======================================================
// ■コメントフォーム部品
// 
// 呼び出し元: ../action/php/function.php 部品()
//======================================================



function commentform_parts(){
    global $設定;

    $template=<<<━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
<form action="《URL》" method="POST" class="commentform" accept-charset="utf-8">
<div class="form-line commentform-line"><label>名前</label><input type="text" name="comment_name" value=""></div>
<div class="form-line commentform-line"><textarea name="comment_body"></textarea></div>
<div class="form-line commentform-line"><input type="submit" value="コメントする"></div>
<input type="hidden" name="entry_id" value="《記事ID》">
<input type="password" name="password" class="commentform-dummy"><input type="text" name="url" class="commentform-dummy">
</form>
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━;

    return テンプレート変換($template, $設定);
}






$css=<<<'━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━'

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



$js=<<<'━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━'
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
