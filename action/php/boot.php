<?php
//======================================================
// ■ブログの起動処理
// 
// 呼び出し元: ../../index.php
//======================================================


//エラーの方針
error_reporting(0);
ini_set('display_errors', 0);

//タイムゾーン
date_default_timezone_set('Asia/Tokyo');


//index.phpのURLを求める。index.phpがない場合とある場合がある
$設定['URL'] = URL作成();
//index.phpのディレクトリのURLを求める
$設定['ベースURL'] = preg_replace("/\/index\.php$/i", "/", $設定['URL']);

//URLにindex.phpを含む場合
if($設定['URL'] != $設定['ベースURL']){
    $設定["index.php"] = "index.php";
}
//URLを書き換える場合 (index.phpなし限定)
if($設定['URL書き換え'] == "○" and $_GET['action'] == "entry"){
    $設定['URL'] = $設定['ベースURL'] = preg_replace("/\/\d+$/", "/", $設定['URL']);
}

//ディレクトリの設定
$設定['ディレクトリ'] = getcwd();
$設定['actionディレクトリ'] = $設定['ディレクトリ'] . '/action';

//デフォルトアクション
if(!$_GET['action'] and GETなら()){
    $_GET['action'] = 'index';
}

//開発用の設定
開発用の設定();


//DBファイルがなければインストールへ
if(!file_exists($設定['DBファイル'])) {
    include_once($設定['actionディレクトリ'] . '/php/install.php');
}
