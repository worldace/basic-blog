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
