$(function () {


$("input[name='comment_name']").val($.cookie("cn"));
$(".comment-form").attr('action', $(".comment-form").attr('action') + '?action=commentpost');
$(".comment-form-dummy").css({'display': 'none'});


//入力チェック
$('.comment-form').submit(function() {
    var form     = $(this);
    var submit   = form.find("input[type='submit']");
    var textarea = form.find("textarea");

    if(textarea.val() == ''){
        alert('コメントを入力してください');
        textarea.focus();
        return false;
    }
});


//コメント削除
$('.comment-delete').click(function() {
    var a       = $(this);
    var comment = $(this).closest('.comment');
    var id      = $(this).closest('.comments').attr('data-entry_id');

    comment.addClass("comment-delete-selected");
    var flag = window.confirm("このコメントを削除しますか？");
    comment.removeClass("comment-delete-selected");
    if(flag == false){ return false; }

    $.ajax({
        url : a.attr('href'),
        type: 'GET',
        success: function(response) {
            comment.fadeOut();
            //コメント数を減少させる
            var comment_sum = parseInt($(".comment" + id + "-sum").first().text()) - 1;
            $(".comment" + id + "-sum").text(comment_sum);
        },
        error: function(xhr) {
            alert(xhr.responseText);
        }
    });

    return false;
});


//レスポップアップ
$(".comment-anker").on({
    mouseenter:function(){
        var 番号     = parseInt($(this).text().replace(">>", ""));
        var コメント = $(this).closest('.comments').find("[data-comment_id=" + 番号 + "]");
        var 名前     = コメント.find(".comment-name").text();
        var 時間     = コメント.find(".comment-date").text();
        var 本文     = コメント.find(".comment-body").html();
        
        番号 = '<span class="comment-popup-no">' + 番号 + '.</span> ';
        名前 = '<span class="comment-popup-name">' + 名前 + '</span> ';
        時間 = '<span class="comment-popup-date">' + 時間 + '</span> ';
        
        if(本文){
            /*$('<article class="comment-popup"><header class="comment-popup-header">' + 番号 + 名前 + 時間 + '</header><p class="comment-popup-body">' + 本文 + '</p></article>').appendTo(this).hide().fadeIn(200);*/
            $('<article class="comment-popup"><p class="comment-popup-body">' + 本文 + '</p></article>').appendTo(this).hide().fadeIn(200);
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
