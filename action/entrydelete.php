<?php
//======================================================
// ■記事を1件削除する (管理用、POST)
// 
// http://127.0.0.1/blog/?action=entrydelete&entry_id=
// 呼び出し元: ../index.php
//======================================================


パスワードチェック();
if (!自然数なら($_POST['id'])){ エラー('不正なIDです。'); }

//削除処理
データベース削除("delete from ブログ where 記事ID = {$_POST['id']}");

//終了
テンプレート表示("{$設定['テンプレート']}/close.html");
