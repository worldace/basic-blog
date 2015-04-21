<?php
//======================================================
// ■アイキャッチ画像のサムネイルを作成する (管理用、POST、Ajax専用)
// 
// http://127.0.0.1/basic-blog/?action=thumbnailmake&url=
// 呼び出し元: ../index.php
//======================================================


パスワードチェック();
if (!Ajaxなら()){ exit; }

//パスを取得する
$元ファイル = str_replace($設定['ベースURL'], "", $_POST["url"]);
$新ファイル = preg_replace("/\.(\w+)$/", ".thumb.$1", $元ファイル);

//入力チェック
if(!$_POST["url"]){ エラー("URLが存在しません"); }
if(!file_exists($元ファイル)){ エラー("ファイルが存在しません"); }
if(!$_POST["w"]){ エラー("画像の横幅が不明です"); }
if(!$_POST["h"]){ エラー("画像の縦幅が不明です"); }


//画像フォーマットからサムネイル作成
list(, , $format) = getimagesize($元ファイル);
画像リサイズ($format, $元ファイル, $新ファイル, $設定['サムネイル横幅'], $設定['サムネイル縦幅'], $_POST["x"], $_POST["y"], $_POST["w"], $_POST["h"]);

if(!file_exists($新ファイル)){ エラー("サムネイルが作成できませんでした"); }


//出力して終了
テキスト表示($設定['ベースURL'] . $新ファイル);
