$(function() {


var textarea = $("textarea");
var indexphp = $("link[rel='index']").attr("href");


//--- ドロップされた場合 ---//
$('.dropzone').on('drop', function(e){
    var uploadfile = e.originalEvent.dataTransfer.files;

    //ファイルのみ受け付ける
    if(uploadfile.length == 0){
        return true;
    }
    //ファイルサイズ確認
    if(ファイルサイズ確認(uploadfile) == false){
        return false;
    }
    //タブ1でのみ受け付ける
    var tab1 = $(".tab").children("ul").children("li").first();
    if(!tab1.hasClass("tab-selected")){
        return false;
    }

    var formData = new FormData();
    for(var i = 0; i < uploadfile.length; i++){
        formData.append('file[]', uploadfile[i]);
    }

    ajaxUpload(formData);

    return false;
})
.on('dragover', function(){
    return false;
});



//--- フォームから入力された場合 ---//
$('form[enctype="multipart/form-data"]').submit(function(){
    var input      = $(this).find('input[type="file"]');
    var uploadfile = input.prop('files');

    //入力チェック
    if(input.val() == ''){
        return false;
    }
    //ファイルサイズ確認
    if(ファイルサイズ確認(uploadfile) == false){
        return false;
    }

    var formData = new FormData(this);

    ajaxUpload(formData);

    return false;
});



//--- テキストエリアのフォーカスが外れた時のカーソル位置を記憶する ---//
textarea.on('blur', function() {
    var pos = $(this).selection('getPos');
    $(this).data("blurpos1", pos.start);
    $(this).data("blurpos2", pos.end);
});
//※insertText()で使用。要jquery.selection
//※IEではドロップした時にテキストエリアのカーソル位置が取得できない時がある。その時に使用する



//--- ファイルサイズ確認関数 ---//
function ファイルサイズ確認(files){
    //ファイルサイズ制限, $ファイルサイズ制限, POST制限, $POST制限の4つの変数をHTMLに貼り付けておくこと

    var recieved  = 0;

    //個別のファイルサイズを確認
    for(var i = 0; i < files.length; i++){
        if(files[i].size > ファイルサイズ制限){ 
            alert($ファイルサイズ制限 + "B以上のファイルはアップロードできません\n" + files[i].name);
            return false;
        }
        recieved += files[i].size;
    }

    //合計サイズの確認
    if(recieved > POST制限){
        alert("合計" + $POST制限 + "B以上は同時にアップロードできません");
        return false;
    }

    return true;
}



//--- 進捗付きアップロード ---//
function ajaxUpload(formData){
    $.ajaxQueue({
        url : indexphp + '?action=upload',
        type: 'POST',
        contentType: false,
        processData: false,
        data: formData,
        //進捗処理
        xhr : function(){ 
            var xhr = $.ajaxSettings.xhr();
            NProgress.start();
            if(xhr.upload){
                xhr.upload.addEventListener('progress', function(e){
                    NProgress.set(e.loaded/e.total); //0～1の間を取る
                }, false);
            }
            return xhr;
        },
        success: function(response) {
            NProgress.done();
            textarea.insertText(response + "\n");
        },
        error: function(xhr) {
            NProgress.done();
            alert(xhr.responseText);
        }
    });
}
//※要jquery.ajaxqueue


});
