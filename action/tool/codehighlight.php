<?php
//■コードハイライト
// 
// http://127.0.0.1/basic-blog/?action=tool&tool=codehighlight
// 呼び出し元: ../tool.php
//======================================================


パスワードチェック();

$dir = $設定['actionディレクトリ'] . "/tool/codehighlight";

//GETなら表示して終了
if (GETなら()){ テンプレート表示("$dir/codehighlight.html"); }

//Ajax専用
if (!Ajaxなら()){ exit; }


// GeSHi https://github.com/GeSHi/geshi-1.0/tree/master/src/geshi
// ※言語を追加する場合は↑から言語ファイルをダウンロードし「codehighlight」の中に入れ、テンプレートファイルの<option>に言語を加える
include("$dir/geshi.php");

$geshi = new GeSHi($_POST['before'], $_POST['lang']);


$geshi->set_language_path($dir);//言語ファイルのディレクトリ
$geshi->set_header_type(GESHI_HEADER_NONE);//<pre>などのヘッダは不要
$geshi->enable_keyword_links(false);//勝手にリンクを貼らない
$geshi->enable_classes();//クラスを使う
$geshi->set_tab_width(4);//タブは4つの空白に変換
$geshi->set_symbols_highlighting(false);//記号類はハイライトしない
$geshi->set_numbers_highlighting(false);//数字類はハイライトしない
$geshi->set_escape_characters_highlighting(false);//エスケープ類はハイライトしない

$result = $geshi->parse_code();

//余計なものを削除
$result = str_replace('<br />', '',  $result);
$result = str_replace('&nbsp;', ' ', $result);


//レスポンスを作成
$ajaxresponse .= "<pre><span class=\"pre-title\">{$_POST['lang']}</span>\n";
$ajaxresponse .= $result;
$ajaxresponse .= "\n</pre>\n";


//表示して終了
テキスト表示($ajaxresponse);
