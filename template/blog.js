//jQueryCookie https://github.com/carhartl/jquery-cookie (C) Klaus Hartl / MIT license
!function(e){"function"==typeof define&&define.amd?define(["jquery"],e):e("object"==typeof exports?require("jquery"):jQuery)}(function(e){function n(e){return u.raw?e:encodeURIComponent(e)}function o(e){return u.raw?e:decodeURIComponent(e)}function i(e){return n(u.json?JSON.stringify(e):String(e))}function r(e){0===e.indexOf('"')&&(e=e.slice(1,-1).replace(/\\"/g,'"').replace(/\\\\/g,"\\"));try{return e=decodeURIComponent(e.replace(c," ")),u.json?JSON.parse(e):e}catch(n){}}function t(n,o){var i=u.raw?n:r(n);return e.isFunction(o)?o(i):i}var c=/\+/g,u=e.cookie=function(r,c,a){if(arguments.length>1&&!e.isFunction(c)){if(a=e.extend({},u.defaults,a),"number"==typeof a.expires){var f=a.expires,s=a.expires=new Date;s.setTime(+s+864e5*f)}return document.cookie=[n(r),"=",i(c),a.expires?"; expires="+a.expires.toUTCString():"",a.path?"; path="+a.path:"",a.domain?"; domain="+a.domain:"",a.secure?"; secure":""].join("")}for(var d=r?void 0:{},p=document.cookie?document.cookie.split("; "):[],m=0,x=p.length;x>m;m++){var l=p[m].split("="),g=o(l.shift()),k=l.join("=");if(r&&r===g){d=t(k,c);break}r||void 0===(k=t(k))||(d[g]=k)}return d};u.defaults={},e.removeCookie=function(n,o){return void 0===e.cookie(n)?!1:(e.cookie(n,"",e.extend({},o,{expires:-1})),!e.cookie(n))}});

//TINY SORT http://tinysort.sjeiti.com/ (C) Ron Valstar / MIT license
(function(e){var a=false,g=null,f=parseFloat,b=/(\d+\.?\d*)$/g;e.tinysort={id:"TinySort",version:"1.2.18",copyright:"Copyright (c) 2008-2012 Ron Valstar",uri:"http://tinysort.sjeiti.com/",licenced:{MIT:"http://www.opensource.org/licenses/mit-license.php",GPL:"http://www.gnu.org/licenses/gpl.html"},defaults:{order:"asc",attr:g,data:g,useVal:a,place:"start",returns:a,cases:a,forceStrings:a,sortFunction:g}};e.fn.extend({tinysort:function(m,h){if(m&&typeof(m)!="string"){h=m;m=g}var n=e.extend({},e.tinysort.defaults,h),s,B=this,x=e(this).length,C={},p=!(!m||m==""),q=!(n.attr===g||n.attr==""),w=n.data!==g,j=p&&m[0]==":",k=j?B.filter(m):B,r=n.sortFunction,v=n.order=="asc"?1:-1,l=[];if(!r){r=n.order=="rand"?function(){return Math.random()<0.5?1:-1}:function(F,E){var i=!n.cases?d(F.s):F.s,K=!n.cases?d(E.s):E.s;if(!n.forceStrings){var H=i.match(b),G=K.match(b);if(H&&G){var J=i.substr(0,i.length-H[0].length),I=K.substr(0,K.length-G[0].length);if(J==I){i=f(H[0]);K=f(G[0])}}}return v*(i<K?-1:(i>K?1:0))}}B.each(function(G,H){var I=e(H),E=p?(j?k.filter(H):I.find(m)):I,J=w?E.data(n.data):(q?E.attr(n.attr):(n.useVal?E.val():E.text())),F=I.parent();if(!C[F]){C[F]={s:[],n:[]}}if(E.length>0){C[F].s.push({s:J,e:I,n:G})}else{C[F].n.push({e:I,n:G})}});for(s in C){C[s].s.sort(r)}for(s in C){var y=C[s],A=[],D=x,u=[0,0],z;switch(n.place){case"first":e.each(y.s,function(E,F){D=Math.min(D,F.n)});break;case"org":e.each(y.s,function(E,F){A.push(F.n)});break;case"end":D=y.n.length;break;default:D=0}for(z=0;z<x;z++){var o=c(A,z)?!a:z>=D&&z<D+y.s.length,t=(o?y.s:y.n)[u[o?0:1]].e;t.parent().append(t);if(o||!n.returns){l.push(t.get(0))}u[o?0:1]++}}return B.pushStack(l)}});function d(h){return h&&h.toLowerCase?h.toLowerCase():h}function c(j,m){for(var k=0,h=j.length;k<h;k++){if(j[k]==m){return !a}}return a}e.fn.TinySort=e.fn.Tinysort=e.fn.tsort=e.fn.tinysort})(jQuery);



$(function () {


//--- 初期化処理 ---//
!function 初期化処理(){
    //カテゴリ名からアンダーバーを取る
    $(".js-category-name").text(function(i, text){
        return text.replace("_", " ");
    });

}();





//--- 投稿フォームのショートカット ---//
$("body").keydown(function(e){
    if(!$.cookie("p")){
        return true;
    }
    
    //Ctrl+スペースを押した場合
    if(e.ctrlKey && e.keyCode == 32){
        var editlink = $(".js-editlink").find('a');
        var postlink = $(".js-postlink").find('a');

        if(e.shiftKey && postlink.length > 0){
            postlink[0].click();
            $('.main-menu ul').hide();
        }
        else if (editlink.length > 0){
            editlink[0].click();
        }
        else if (postlink.length > 0) {
            postlink[0].click();
            $('.main-menu ul').hide();
        }

        return false;
    }
    return true;
});



//--- preコードダウンローダ ---//
$('.pre-title').click(function() {
    var filename = $(this).text();
    var contents = $(this).parent("pre").contents(":not('.pre-title')").text().trim();
    var blob     = new Blob([contents], {"type" : "text/plain"});
    var bloburl  = window.URL.createObjectURL(blob) || window.webkitURL.createObjectURL(blob);
    
    if(!/\.\w+$/.test(filename)){
        filename = filename + '.txt';
    }

    if (window.navigator.msSaveOrOpenBlob) {
        window.navigator.msSaveOrOpenBlob(blob, filename);
    }
    else {
         var a = $('<a></a>', {href: bloburl, download: filename})[0];
         $(a).hide().appendTo(document.body);
         a.click();
         $(a).remove();
    }
});


//--- テーブルソート(TINY SORT使用) ---//
var aAsc = [];
$('table.sortable').each(function(){
    $(this).find('th').each(function(index){$(this).attr('rel', index);});
    $(this).find('td').each(function(){$(this).attr('value', $(this).text());});
});
$(document).on('click', 'table.sortable th', function(e){
    // update arrow icon
    $(this).parents('table.sortable').find('span.arrow').remove();
    $(this).append('<span class="arrow"></span>');

    // sort direction
    var nr = $(this).attr('rel');
    //aAsc[nr] = aAsc[nr]=='asc'?'desc':'asc';
    aAsc[nr] = aAsc[nr]=='desc'?'asc':'desc';
    if(aAsc[nr] == 'desc'){ $(this).find('span.arrow').addClass('up'); }

    // sort rows
    var rows = $(this).parents('table.sortable').find('tr').slice(1);
    rows.tsort('td:eq('+nr+')',{order:aAsc[nr],attr:'value'});

    // fix row classes
    rows.removeClass('alt first last');
    var table = $(this).parents('table.sortable');
    table.find('tr:even').addClass('alt');
    table.find('tr:first').addClass('first');
    table.find('tr:last').addClass('last');
});



//--- 画像ポップアップ ---//
$("<div></div>", {id: "overlay417"}).hide().appendTo("body");

//popupクラスならポップアップさせる
$(".popup").click(function(e) {

    //イベントが発生した要素からURLを取得する
    var url = $(e.target).parent("a").attr("href") || $(e.target).attr("href") || $(e.target).attr("src");
    if(!url){
        return false;
    }


    //対象画像を読み込んで、サイズを計算してからポップアップする
    var preload = new Image();
    preload.onload = function() {
        //ウインドウのサイズ
        var windowW = $(window).width();
        var windowH = $(window).height();

        //表示大サイズ
        var displayW = $(e.target).width();
        var displayH = $(e.target).height();

        //原寸大サイズ
        var imgW = preload.width;
        var imgH = preload.height;

        //原寸大で表示した時のはみ出る量を計算。正ならはみ出る
        var margin = 80;
        var overW  = imgW - windowW + margin;
        var overH  = imgH - windowH + margin;


        //横も縦もはみ出ない時
        if(overW <= 0 && overH <= 0){
            popupW = imgW;
            popupH = imgH;
        }

        //横だけはみ出る時
        else if(overW > 0 && overH <= 0){
            popupW = windowW - margin;
            popupH = popupW * imgH / imgW;
        }

        //縦だけはみ出る時
        else if(overW <= 0 && overH > 0){
            popupH = windowH - margin;
            popupW = popupH * imgW / imgH;
        }

        //横も縦もはみ出る時
        else{
            if(overW > overH) {
                //横を画面内に収める
                popupW = windowW - margin;
                popupH = popupW * imgH / imgW;
                //まだ縦がはみ出る場合
                if(popupH > (windowH - margin)){
                    var beforeH = popupH;
                    popupH = windowH - margin;
                    popupW = popupH * popupW / beforeH;
                }
            }
            else{
                //縦を画面内に収める
                popupH = windowH - margin;
                popupW = popupH * imgW / imgH;
                //まだ横がはみ出る場合
                if(popupH > (windowH - margin)){
                    var beforeW = popupW;
                    popupW = windowW - margin;
                    popupH = popupW * popupH / beforeW;
                }
            }
        }

        //原寸大よりポップアップの方が大きい場合は、原寸大で表示する(拡大はしない)
        if(popupW > imgW || popupH > imgH){
            popupW = imgW;
            popupH = imgH;
        }

        //表示上よりポップアップの方が小さい場合は、新しいウインドウを開いて終了
        if(popupW < displayW || popupH < displayH){ 
            window.open(url);
            return false;
        }

        //ポップアップの表示位置を決める
        var popupL = (windowW/2) - (popupW/2) + $(window).scrollLeft();
        var popupT = (windowH/2) - (popupH/2) + $(window).scrollTop();

        //ポップアップの表示位置とサイズを整数にする
        popupL = Math.round(popupL);
        popupT = Math.round(popupT);
        popupW = Math.round(popupW);
        popupH = Math.round(popupH);

        //デバッグ用
        //console.log('元画像:' + imgW + '*' + imgH + ', 表示上:' + displayW + '*' + displayH + ', ウインドウ:' + windowW + '*' + windowH + ', ポップアップ画像:' + popupW + '*' + popupH + ', ポップアップ位置:' + popupL + '*' + popupT);

        //レイヤー内を空にする→CSSを適用→imgタグ追加→表示
        $("#overlay417").empty().css({
            'height'    : $(document).height(),
            'position'  : 'absolute',
            'top'       : '0',
            'left'      : '0',
            'width'     : '100%',
            'padding'   : '0',
            'margin'    : '0',
            'z-index'   : '100',
            'cursor'    : 'pointer',
            'background-color': 'rgba(255,255,255,0.4)'
        })
        .append($("<img>", {src: url, width: popupW, height: popupH}))
        .fadeIn('fast');

        //レイヤー内画像にCSSを適用
        $("#overlay417 img").css({
            'left'       : popupL,
            'top'        : popupT,
            'position'   : 'absolute',
            'display'    : 'inline-block',
            'border'     : '7px solid white',
            'box-sizing' : 'content-box',
            'box-shadow' : '0px 0px 10px rgba(0, 0, 0, 0.3)'
        });
    };
    preload.src = url;
    return false;
});


$("#overlay417").click(function() {
    $("#overlay417").hide();
});

});

