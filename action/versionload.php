<?php
//======================================================
// ■指定した履歴を1件出力する (管理用、Ajax専用、POST)
// 
// http://127.0.0.1/basic-blog/?action=versionload
// 呼び出し元: ../index.php
//======================================================


パスワードチェック();

if (!Ajaxなら()){ exit; }
if (!自然数なら($_POST['vid'])){ exit; }

//データベースから取得
$version = データベース行取得("select * from 履歴 where 履歴ID = {$_POST['vid']}");

//引数によりエスケープ
if($_POST['escape'] == 1){ $version['履歴本文'] = h($version['履歴本文']); }

//結果を出力
テキスト表示($version['履歴本文']);