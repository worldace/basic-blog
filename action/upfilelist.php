<?php
//======================================================
// ■指定日にアップロードされたファイルの一覧を表示する (管理用)
// 
// http://127.0.0.1/blog/?action=upfilelist&y=&m=&d=
// 呼び出し元: ../index.php
//======================================================


パスワードチェック();

//日付が正当かチェック
if(!checkdate($_GET['m'], $_GET['d'], $_GET['y'])){ エラー('不正なパラメータです。'); }

//引数の日にアップされたファイルが入ってるディレクトリ
$ディレクトリ = "{$設定['アップロードディレクトリ']}/{$_GET['y']}/{$_GET['m']}{$_GET['d']}";

//ファイル一覧ゲット
$ファイル一覧 = ファイル一覧取得($ディレクトリ);
if(!$ファイル一覧){ エラー('その日にアップロードされたファイルはありません。'); }


//表を作る
foreach($ファイル一覧 as $ファイル名){
    $投稿日時 = 日付変換(filemtime("$ディレクトリ/$ファイル名"));
    $サイズ   = バイト数を変換(filesize("$ディレクトリ/$ファイル名"));
    $削除     = "<form action=\"{$設定['URL']}?action=upfiledelete\" method=\"POST\"><input type=\"hidden\" name=\"y\" value=\"{$_GET['y']}\"><input type=\"hidden\" name=\"m\" value=\"{$_GET['m']}\"><input type=\"hidden\" name=\"d\" value=\"{$_GET['d']}\"><input type=\"hidden\" name=\"filename\" value=\"{$ファイル名}\"><input class=\"filedelete\" type=\"image\" name=\"submit\" src=\"{$設定['テンプレート']}/delete.png\" width=\"16\" height=\"16\"></form>";
    $設定['アップファイル表'] .= "<tr><td>$投稿日時</td><td><a href=\"$ディレクトリ/$ファイル名\" target=\"_blank\">$ファイル名</a></td><td>$サイズ</td><td>$削除</td></tr>\n";
}


//表示して終了
$設定['曜日'] = 日付変換(mktime(0, 0, 0, $_GET['m'], $_GET['d'], $_GET['y']), 4);

$設定['y'] = $_GET['y'];
$設定['m'] = $_GET['m'];
$設定['d'] = $_GET['d'];

テンプレート表示("{$設定['テンプレート']}/upfilelist.html");

