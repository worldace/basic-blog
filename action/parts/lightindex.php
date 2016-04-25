<?php
//======================================================
// ■ライトインデックス部品
// 
// 呼び出し元: ../action/php/function.php 部品()
//======================================================


function lightindex_parts($検索結果){
    global $設定;

    foreach($検索結果 as $entry){
        $i++;
        if($i > $設定['ライトインデックス記事表示件数']) { break; }

        $entry['記事URL']            = 記事URL作成($entry['記事ID']);
        $entry['記事投稿者']         = $設定['管理者'];
        $entry['記事ページビュー数'] = number_format($entry['記事ページビュー数']);
        $entry['記事投稿時間']       = 日付変換($entry['記事投稿時間'], 3);

        if($entry['記事コメント最終時間']){
            $entry['記事コメント最終時間'] = 日付変換($entry['記事コメント最終時間'], 3);
        }
        if(!$entry['記事コメント数']){
            $entry['記事コメント数'] = '-';
        }
        if(!$entry['記事コメント最終時間']){
            $entry['記事コメント最終時間'] = '-';
        }


        //カテゴリリンク作成
        if(!$entry['記事カテゴリ']){
            $entry['記事カテゴリ'] = $設定['カテゴリなし'];
        }

        if($設定['カテゴリ']){ 
            $entry['記事メインカテゴリ'] = $設定['カテゴリ'];
        }
        else{
            $entry['記事メインカテゴリ'] = preg_replace('/\s.*/', '', $entry['記事カテゴリ']); //カテゴリは最初の1つだけ表示する
        }

        $_category = urlencode($entry['記事メインカテゴリ']);
        $entry['記事カテゴリリンク'] = "<a href=\"{$設定['URL']}?action=category&category=$_category\" title=\"{$entry['記事カテゴリ']}\" class=\"js-category-name\">{$entry['記事メインカテゴリ']}</a>";

        $html .= "<tr><td><a href=\"{$entry['記事URL']}\">{$entry['記事タイトル']}</a></td><td>{$entry['記事カテゴリリンク']}</td><td><time>{$entry['記事投稿時間']}</time></td><td>{$entry['記事ページビュー数']}</td><td>{$entry['記事コメント数']}</td><td><time>{$entry['記事コメント最終時間']}</time></td></tr>\n";
    }
    
    if($html){
        return 
            "<table class=\"lightindex sortable\">" .
            "<tr><th>タイトル</th><th>カテゴリ</th><th>投稿日</th><th title=\"アクセス数\">PV</th><th>レス</th><th>最終レス</th></tr>" .
            $html .
            "</table>";
    }
}


$css=<<<'━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━'
.lightindex{
    width: 100%;
    table-layout: fixed;
    margin: 50px auto;
    border-collapse: collapse
}
.lightindex th{
    font-size: 16px;
    padding-bottom: 10px;
}
.lightindex td{
    border-bottom: solid 1px #eee;
    font-size: 14px;
    padding: 3px 5px;
    text-align: center;
    overflow: hidden;
    text-overflow: '';
    white-space: nowrap;
}
.lightindex tr th:nth-child(1){
    width: 50%;
}
.lightindex tr th:nth-child(2){
    width: 15%;
}
.lightindex tr th:nth-child(3){
    width: 11%;
}
.lightindex tr th:nth-child(4){
    width: 7%;
}
.lightindex tr th:nth-child(5){
    width: 5%;
}
.lightindex tr th:nth-child(6){
    width: 11%;
}

.lightindex tr td:first-child{
    text-align: left;
}
.lightindex tr td:first-child a{
    display: block;
}

.sortable th{
    cursor: pointer;
    -ms-user-select: none;
    user-select: none;
}


━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━;



