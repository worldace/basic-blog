<?php
//======================================================
// ■ヘッドライン部品
// 
// 呼び出し元: ../action/php/function.php 部品()
//======================================================


function headline_parts($検索結果, $category = ""){
    global $設定;

    $template=<<<━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
<div class="headline-box">
<span class="headline-time">《記事投稿時間》</span><span class="headline-category js-category-name">《記事メインカテゴリ》</span>
<a href="《記事URL》"><img src="《記事サムネイル画像》" width="《サムネイル横幅》" height="《サムネイル縦幅》" class="headline-image"><h2 class="headline-title">《記事タイトル》</h2></a>
</div>

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━;

    foreach($検索結果 as $entry){
        $i++;
        if($i > $設定['ヘッドライン記事表示件数']) { break; }

        if(!$entry['記事ID']){ return ""; }

        $entry['記事URL']      = 記事URL作成($entry['記事ID']);
        $entry['記事投稿時間'] = 日付変換($entry['記事投稿時間'], 6);

        if(!$entry['記事カテゴリ']){
            $entry['記事カテゴリ'] = $設定['カテゴリなし'];
        }

        if($category){
            $entry['記事メインカテゴリ'] = $category;
        }
        else{
            $entry['記事メインカテゴリ'] = preg_replace('/\s.*/', '', $entry['記事カテゴリ']); //カテゴリは最初の1つだけ表示する
        }
        
        if(!$entry['記事サムネイル画像']){
            $entry['記事サムネイル画像'] = $設定['URL'] . $設定['テンプレート'] . "/" . $設定['サイトアイキャッチサムネイル画像'];
        }
        $entry['サムネイル横幅'] = $設定['サムネイル横幅'];
        $entry['サムネイル縦幅'] = $設定['サムネイル縦幅'];

        $html .= テンプレート変換($template, $entry);
    }
    
    return $html;
}




$css=<<<'━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━'
.headline-box{
    width: 480px;
    display: inline-block;
    margin: 0 5px 35px 0;
    padding: 0;
}
.headline-image{
    margin: 0;
    padding: 0;
    border: 0;
    vertical-align: middle;
    z-index: 2;
}
.headline-title{
    overflow: hidden;
    width: 480px;
    height: 66px;
    background-color: #000;
    color: #fff;
    margin: 0;
    padding: 4px 5px 2px 5px;
    border: 0;
    font-size: 24px;
    line-height: 30px;
    text-align:left;
}
.headline-category{
    display: inline-block;
    border-left: solid 1px #555;
    border-top: solid 1px #555;
    border-right: solid 1px #555;
    border-bottom: solid 1px #555;
    margin-bottom: -1px;
    float: right;
    padding: 3px 14px;
    border-radius: 5px 5px 0 0;
    font-size: 16px;
}
.headline-time{
    font-size: 18px;
    color: #aaa;
}


━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━;
