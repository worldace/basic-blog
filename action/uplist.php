<?php
//======================================================
// ■アップリストを表示する (管理用)
// 
// http://127.0.0.1/basic-blog/?action=uplist
// 呼び出し元: ../index.php
//======================================================


パスワードチェック();

$年   = $_GET['y'];
$今年 = date("Y", $_SERVER['REQUEST_TIME']);
$今日 = date("Ymd", $_SERVER['REQUEST_TIME']);


//$年の検証
if(!$年){ $年 = $今年; }
if(!自然数なら($年) or $年 < $設定['アップリスト最初の年'] or $年 > $今年){ エラー('不正なパラメータです。'); }


//アップロードしたファイルは「upload/年/月日/」に格納される
$ディレクトリ一覧 = ディレクトリ一覧取得("{$設定['アップロードディレクトリ']}/$年");


//カレンダーを作る
for($日 = 1; $日 <= 31; $日++){
    $表 .= "<tr>\n";

    for($月 = 1; $月 <= 12; $月++){
        //日自体が存在しない場合
        if($日 >= 28 and !checkdate($月, $日, $年)){
            $表 .= "<td class=\"nofile\">-</td>\n";
            continue;
        }

        $曜日 = date("D", mktime(0, 0, 0, $月, $日, $年));
        $月月 = sprintf("%02d", $月);
        $日日 = sprintf("%02d", $日);
        $本日 = '';

        if($今日 == "$年$月月$日日"){ $本日 = "today"; }

        //ファイルが存在する日
        if(in_array("$月月$日日", $ディレクトリ一覧, true)){
            $表 .= "<td class=\"$曜日 $本日\"><a href=\"{$設定['URL']}?action=upfilelist&y=$年&m=$月月&d=$日日\">{$月月}月{$日日}日</a></td>\n";
        }
        //ファイルが存在しない日
        else{
            $表 .= "<td class=\"nofile $曜日 $本日\">{$月月}月{$日日}日</td>\n";
        }
    }

    $表 .= "</tr>\n";
}


//ページめくり
//去年にリンク
if($年 > $設定['アップリスト最初の年']){
    $去年 = $年 - 1;
    $設定['去年リンク'] = "<a href=\"{$設定['URL']}?action=uplist&y=$去年\" rel=\"prev\" class=\"paging-leftlink\">←</a>";
}
//来年にリンク
if($年 < $今年){
    $来年 = $年 + 1;
    $設定['来年リンク'] = "<a href=\"{$設定['URL']}?action=uplist&y=$来年\" rel=\"next\" class=\"paging-rightlink\">→</a>";
}


//表示して終了
$設定['y'] = $年;
$設定['アップ表'] = $表;



?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title><?= $設定['ブログ名'] ?> アップリスト</title>
  <link href="<?= $設定['テンプレート'] ?>/upload.css" rel="stylesheet">
  <link rel="icon" href="<?= $設定['テンプレート'] ?>/favicon.png" type="image/png">
</head>
<body>


<header><a href="<?= $設定['URL'] ?>?action=uplist">アップリスト</a> / <a href="<?= $設定['URL'] ?>?action=uplist&y=<?= $設定['y'] ?>"><?= $設定['y'] ?>年</a></header>

<article class="main-contents">
<table id="uplist">
<caption><?= $設定['去年リンク'] ?> <?= $設定['y'] ?>年 <?= $設定['来年リンク'] ?></caption>
<tr><th>1月</th><th>2月</th><th>3月</th><th>4月</th><th>5月</th><th>6月</th><th>7月</th><th>8月</th><th>9月</th><th>10月</th><th>11月</th><th>12月</th></tr>
<?= $設定['アップ表'] ?>
</table>
</article>


</body>
</html>
