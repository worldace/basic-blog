!function($){

//テキストエリアのカーソル位置に文字列を挿入するjQueryプラグイン ※要jquery.selection
$.fn.insertText = function(str){
    var p1 = this.selection('getPos').start;
    var p2 = this.selection('getPos').end;
    var margin = 0;

    //IEの場合は特別に対策する必要がある
    var browser = window.navigator.userAgent.toLowerCase();
    if (browser.indexOf("msie") >= 0 || browser.indexOf("trident") >= 0) {
        //ドロップ時にカーソル位置が上手く取得できない。blur時のカーソル位置を記憶しておいて、それを使うと上手くいく
        if(!p1 && this.data("blurpos1") > 0){
            p1 = this.data("blurpos1");
            p2 = this.data("blurpos2");
        }
        //追加文字列の末尾が改行の時、カーソル位置が2大きい (カーソル位置が改行&&一つ前が改行以外の時は0)
        if(/\n$/.test(str)){
            margin = -2;
            if(this.val().charAt(p2) === "\n" && this.val().charAt(p2-1) !== "\n"){ margin = 0; }
        }
    }

    //カーソル前後の文字列を取得
    var before_str = this.selection('setPos', {start:0, end:p1}).selection('get');
    var after_str  = this.selection('setPos', {start:p2, end:this.val().length}).selection('get');

    //追加文字列の長さ＝テキストエリアの内容を追加文字列に変更し、カーソル位置を一番最後に
    this.html(str).selection('setPos', {start:this.val().length, end:this.val().length});
    var p3 = this.selection('getPos').end;

    //文字列を全て貼り付けて、カーソルを移動
    this.html(before_str + str + after_str).selection('setPos', {start:p1+p3+margin, end:p1+p3+margin});

    return this;
};
}(jQuery);




$(function() {


var title       = $("input[name='title']");
var category    = $("input[name='category']");
var textarea    = $("textarea[name='body']");
var textarea_ex = 270;
var entry_id    = $("input[name='id']").val();
var indexphp    = $("link[rel='index']").attr("href");


!function 初期化処理(){
    //テキストエリアの縦幅調整
    textarea.css({height: $(window).height() - textarea_ex});
    $('body').css({visibility: 'visible'});

    //entrypostの初期処理
    if($('#entrypost').length){
        title.focus();
    }

    //entryeditの初期処理
    if($('#entryedit').length){
        textarea.focus();

        //履歴のバージョン名を「最新版」「初版」に変更する
        var vtd = $("#version-list td");
        if(vtd.length > 1){
            vtd.first().text("最新版");
            if (vtd.last().prev().text() == "第1版"){
                vtd.last().prev().text("初版");
            }
        }

        //settingの初期値を選択する
        selectSelected($("#setting-entry-status"));
    }
}();



//テキストエリアの縦幅調整
$(window).resize(function() {
    textarea.css({height: $(window).height() - textarea_ex});
});



//入力チェック
$('#entrypost, #entryedit').submit(function() {
    if(title.val() == ''){
        alert('タイトルを入力してください');
        title.focus();
        return false;
    }
    if(textarea.val() == ''){
        alert('本文を入力してください');
        textarea.focus();
        return false;
    }

    //settingの値を取得して本formに追加する
    $("input[name='entry-status']").val($("#setting-entry-status").val());
});



//プレビュータブを開く
$("#tab-preview").click(function(){
    iframePreview($("#preview-iframe"), textarea.val());
});



//サムネイルタブを開く
$("#tab-thumbnail").click(function(){
    //これを消さないとtextareの内容が反映されない
    $(".jcrop-holder").remove();

    //プレビューを消す
    $("#thumbnail-preview-wrapper").empty();

    //テキストエリアの内容をDOMに変換
    var textarea_dom = $($.parseHTML("<div>" + textarea.val() + "</div>"));

    //サムネイル候補を探す
    var thumb_url = textarea_dom.find(".eyecatch").attr("src");
    if(!thumb_url){ thumb_url = textarea_dom.find("img").attr("src"); }
    if(!thumb_url){ return true; }

    //サムネイルの存在確認
    var new_thumb_url = thumb_url.replace(/\.(\w+)$/, ".thumb.$1");
    $.ajax({
        url: new_thumb_url,
        type: "HEAD",
        success: function(){
           $("#thumbnail-exists").text("(作成済)");
           $("#thumbnail-preview-wrapper")
           .append($("<h3></h3>", {text:"ヘッドラインプレビュー"}))
           .append($("<img>", {src: new_thumb_url + "?" + $.now(), "class":"headline-image"}))
           .append($("<h2></h2>", {"class":"headline-title", text:title.val()}));
        },
        error: function(){
            $("#thumbnail-exists").text("(未作成)");
        }
    });

    var section   = $("#tab-thumbnail-section");
    var saved_url = section.attr("data-url");
    var saved_x   = parseInt(section.attr("data-x"));
    var saved_y   = parseInt(section.attr("data-y"));
    var saved_w   = parseInt(section.attr("data-w"));
    var saved_h   = parseInt(section.attr("data-h"));

    var setting_w = parseInt($("#thumbnail-image-wrapper").attr("data-w"));
    var setting_h = parseInt($("#thumbnail-image-wrapper").attr("data-h"));
    if(!setting_w || !setting_h){
        alert("サムネイルのサイズが設定されていません(setting.php)");
        return true;
    }

    var jcrop_setting = {
        aspectRatio: setting_w/setting_h,
        //minSize: [setting_w, setting_h],
        onSelect: function(e){
            section.attr("data-url", thumb_url);
            section.attr("data-x", e.x);
            section.attr("data-y", e.y);
            section.attr("data-w", e.w);
            section.attr("data-h", e.h);
        },
        onRelease: function(e){
            section.attr("data-url", "");
            section.attr("data-x", "");
            section.attr("data-y", "");
            section.attr("data-w", "");
            section.attr("data-h", "");
        }
    };

    if(saved_url == thumb_url){
        $.extend(jcrop_setting, {setSelect: [saved_x, saved_y, saved_x+saved_w, saved_y+saved_h]});
    }

    var imgtag = $("<img>", {src:thumb_url, id:"thumbnail-jcrop"});
    $("#thumbnail-image-wrapper").html(imgtag);
    $('#thumbnail-jcrop').Jcrop(jcrop_setting);
});



//サムネイルを作成する
$("#thumbnail-make").click(function(){
    var section = $("#tab-thumbnail-section");
    var img     = $("#thumbnail-jcrop");

    if(img.length == 0){
        alert("本文中に画像が存在しません");
        return false;
    }

    //APIが動かないので別方法で情報取得
    var saved_url = section.attr("data-url");
    var saved_x   = parseInt(section.attr("data-x"));
    var saved_y   = parseInt(section.attr("data-y"));
    var saved_w   = parseInt(section.attr("data-w"));
    var saved_h   = parseInt(section.attr("data-h"));

    if(!saved_x && !saved_w){
        alert("範囲を選択してください");
        return false;
    }

    $.ajax({
        url: indexphp + "?action=thumbnailmake",
        data: {url:saved_url, x:saved_x, y:saved_y, w:saved_w, h:saved_h},
        type: "POST",
        success: function(response){
            $("#thumbnail-exists").text("(作成済)");
            $("#thumbnail-preview-wrapper").empty()
            .append($("<h3></h3>", {text:"ヘッドラインプレビュー"}))
            .append($("<img>", {src: response + "?" + $.now(), "class":"headline-image"}))
            .append($("<h2></h2>", {"class":"headline-title", text:title.val()}));
            alert("サムネイルを作成しました\n記事を更新するとこのサムネイルが有効になります");
        },
        error: function(xhr){
            alert(xhr.responseText);
        }
    });

    return false;
});



//記事削除確認
$('#entrydelete').submit(function() {
    return confirm("「" + title.val() + "」を削除しますか？");
});



/*
//投稿プレビュー(submitボタンの上で右クリック) *休止中*
$("#entrypost input[type='submit'], #entryedit input[type='submit']").on('contextmenu', function() {
    var preview_form = $(this).parents('form').clone();

    preview_form.attr({'action':'?action=preview', 'target':'_blank', 'id':'preview'});
    preview_form.hide().appendTo('body');
    preview_form.find("input[type='submit']").click();
    preview_form.remove();

    return false;
});
*/



//履歴1。バージョンを選択したらiframeに該当コンテンツを表示する
$(".version-tr").click(function() {
    var td     = $(this).find("td:first-child");
    var vid    = td.attr("data-vid");
    var vname  = td.text();

    $(".version-tr").removeClass("version-selected");
    $(this).addClass("version-selected");

    $.ajax({
        url : indexphp + '?action=versionload',
        type: 'POST',
        data: {"vid": vid, "escape": 0},
        dataType: 'text',
        success: function(response) {
            $("#version-restore").attr("data-vid", vid);
            $("#version-restore").attr("data-vname", vname);

            iframePreview($("#version-iframe"), response);
        },
        error: function(xhr, error) {
            $("#version-list tr").removeClass("version-selected");
        }
    });

    return false;
});


//履歴2。復元ボタンを押したら、テキストエリアに表示する
$("#version-restore").click(function() {
    var vid   = $(this).attr("data-vid");
    var vname = $(this).attr("data-vname");

    if(!vid){
        alert("復元するバージョンを選択してください");
        return false;
    }

    if(confirm(vname + "から復元しますか？") == false){
        return false;
     }

    $.ajax({
        url : indexphp + '?action=versionload',
        type: 'POST',
        data: {"vid": vid, "escape": 1},
        dataType: 'text',
        success: function(response){
            $(".tab > ul > li:first-child").dblclick();
            textarea.empty().append(response).focus();
            alert("記事を" + vname + "から復元しました\n更新ボタンを押すと記事に反映されます");
        },
        error: function() {
            alert("復元に失敗しました");
        }
    });
    return false;
});


//タブ
$(".tab > ul > li").click(function() {
    if($(this).hasClass("tab-selected")){ return false; }

    $(this).closest(".tab").children("section").hide().eq($(this).index()).fadeIn('fast');
    $(this).addClass('tab-selected').siblings().removeClass('tab-selected');
});

$(".tab > ul > li").dblclick(function() {
    if($(this).hasClass("tab-selected")){ return false; }

    $(this).closest(".tab").children("section").hide().eq($(this).index()).show(0);
    $(this).addClass('tab-selected').siblings().removeClass('tab-selected');
});


//テキストエリア内のショートカット
$(textarea).keydown(function(e){

    //Markdown(Ctrl＋↓)
    if(e.ctrlKey && e.keyCode == 40){
        if(!confirm("Markdown形式をHTMLに変換しますか？")){
            return false;
        }

        //現在のカーソル位置
        var p1 = $(this).selection('getPos').start;
        var p2 = $(this).selection('getPos').end;
        
        //選択中なら
        if(p1 < p2){
            var md = $(this).selection('get');
            $(this).insertText(marked(md));
        }
        //選択していないなら
        else{
            var md = $(this).val();
            $(this).html(marked(md));
        }
        return false;
    }

    //タグを閉じる(Ctrl＋/)
    else if(e.ctrlKey && e.keyCode == 191){
        //現在のカーソル位置
        var p1 = $(this).selection('getPos').start;
        var p2 = $(this).selection('getPos').end;
        //現在のカーソルより前の文字列を取得
        var before_str = $(this).selection('setPos', {start:0, end:p1}).selection('get');
        //カーソル位置を最初に戻す
        $(this).selection('setPos', {start:p1, end:p2});

        var close_tag = closetagSearch(before_str);
        if(close_tag){
            $(this).insertText(close_tag);
            return false;
        }
    }
});



//候補表示(カテゴリ欄で使用)
!function 候補表示(){
    var onmouse;

    $(".candidate-dropdown").dblclick(function() {
        $(this).next(".candidate-list").css("width", $(this).outerWidth()).show();
    });

    $('.candidate-dropdown, .candidate-list').hover(
        function(){ onmouse = true;  },
        function(){ onmouse = false; }
    );

    $(document).click(function() {
        if (onmouse == false) {
            $('.candidate-list').slideUp(80);
        }
    });

    $(".candidate-list li").click(function() {
        var add   = $(this).text();
        var input = $(this).parent().prev(".candidate-dropdown");
        var value = input.val();

        if(value){
            input.val(value + " " + add);
        }
        else {
            input.val(add);
        }
    });


    $(window).resize(function() {
        $(".candidate-dropdown").each(function(){
            $(this).next(".candidate-list").css("width", $(this).outerWidth());
        });
    });

//※下記のようなHTMLを用意しておくこと
//<input type="text" class="candidate-dropdown" autocomplete="off">
//<ul class="candidate-list" style="display:none; position:absolute; ...詳しくはpost.cssを"><li>候補1</li><li>候補2</li></ul>
}();



//optionタグにselected属性を付与する
//第1引数: 親のselectタグを選択したjQueryオブジェクト。※data-selected属性に初期値を入れておく
function selectSelected(select){
    var str = select.attr("data-selected");
    if(str === undefined || str == ""){
        return false;
    }

    select.find("option").each(function(){
        if($(this).val() == str){
            $(this).prop("selected", true);
        }
    });
}



//iframeにプレビューを表示する
//第1引数: iframeタグを選択したjQueryオブジェクト
//第2引数: iframe内に貼り付ける内容(.contents)
function iframePreview(iframe, contents){
    var iframe_doc  = iframe[0].contentWindow.document;
    var iframe_body = $("body", iframe_doc);

    $(".contents", iframe_doc).html(contents);

    $("*", iframe_doc).on("load", function(){
        iframe.attr("height", iframe_body.outerHeight(true)+100);
    });
}



//文字列から閉じてないタグを探す
function closetagSearch(text){
    //終了タグが存在しないタグ
    var exclude_tag = ["br","wbr","hr","img","col","base","link","meta","input","keygen","area","param","embed","source","track","command"];

    //ループ内で使う変数
    var word;               //現在の文字
    var tag_name;           //タグ名候補
    var in_tag = false;     //タグ内かどうか
    var in_comment = false; //コメント内かどうか
    var closetag = {};      //終了タグの出現回数を記録しておく
    var insert_tag;         //挿入するタグ

    //改行除去
    text = text.replace(/[\n\r]/g, "");

    //1文字ずつループする
    for (var i = 0; i < text.length; i++) {
    	//後ろから1文字を取り出す
    	word = text.charAt(text.length - i - 1);

    	//タグ内なら
    	if (in_tag === true){
    		if(word === '<'){
    			in_tag = false;
    			//開始タグなら(XMLも考慮した正規表現)
    			if(tag_name.match(/^[a-zA-Z_\u007F-\uFFFF][a-zA-Z_\u007F-\uFFFF0-9\-\.\:]*$/)){
    				//終了タグが存在しないタグならスキップする
    				if(in_array(tag_name.toLowerCase(), exclude_tag)){ continue; }

    				//終了タグが出現済みならば
    				if(tag_name in closetag && closetag[tag_name] > 0){
    					closetag[tag_name]--;
    				}
    				//終了タグが未出現ならば
    				else {
    					insert_tag = '</' + tag_name + '>';
    					break;
    				}
    			}
    			//終了タグなら
    			else if(tag_name.indexOf('/') === 0){
    				tag_name = tag_name.slice(1);
    				if(closetag[tag_name] === undefined){ closetag[tag_name] = 0; }
    				closetag[tag_name]++;
    			}

    			continue;
    		}
    		else if(word === ' '){
    			tag_name = '';
    			continue;
    		}
    	}

    	//コメント内なら
    	else if (in_comment === true){
    		//コメント開始タグならば
    		if(word === '<' && tag_name.indexOf('!--') === 0){
    			in_comment = false;
    			continue;
    		}
    	}

    	//タグ外なら
    	else {
    		if(word === '>'){
    			tag_name = '';
    			if(text.charAt(text.length - i - 2) + text.charAt(text.length - i - 3) === '--'){
    				in_comment = true;
    				continue;
    			}
    			else {
    				in_tag = true;
    				continue;
    			}
    		}
    		//コメント開始タグならば
    		else if(word === '<' && tag_name.indexOf('!--') === 0){
    			insert_tag = '-->';
    			break;
    		}
    	}

    	tag_name = word + tag_name;

    }
    
    return insert_tag;
}

//PHPのin_array()と同等。closetagSeach()で使う
function in_array(value, array){
	for(var i = 0; i < array.length; i++){
		if(array[i] === value) { return true; }
	}
	return false;
}


});
