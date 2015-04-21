<?php
//======================================================
// ■アップロードされたファイルを1つ削除する (管理用、POST)
// 
// http://127.0.0.1/basic-blog/?action=upfiledelete
// 呼び出し元: ../index.php
//======================================================


パスワードチェック();
$ディレクトリ = "{$設定['アップロードディレクトリ']}/{$_POST['y']}/{$_POST['m']}{$_POST['d']}/";
$ファイルパス = "$ディレクトリ/{$_POST['filename']}";

//引数チェック
if(!checkdate($_POST['m'], $_POST['d'], $_POST['y'])){ エラー('日付が不正です'); }
if(preg_match("/\//", $_POST['filename']) or preg_match("/^\.\./", $_POST['filename'])){ エラー('ファイル名が不正です'); }
if(!is_file($ファイルパス)){ エラー('ファイルが存在しません'); }


//削除処理
$削除成功 = unlink($ファイルパス);
if(!$削除成功){ エラー('ファイルを削除できませんでした'); }


//ディレクトリのファイル調査
if(ファイル一覧取得($ディレクトリ)){
    //ファイルがあれば元のページに
    リダイレクト("{$設定['URL']}?action=upfilelist&y={$_POST['y']}&m={$_POST['m']}&d={$_POST['d']}");
}
else {
    //ファイルがなければディレクトリ削除
    rmdir($ディレクトリ);
    リダイレクト("{$設定['URL']}?action=uplist&y={$_POST['y']}");
}
