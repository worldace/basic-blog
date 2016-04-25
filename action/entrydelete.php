<?php
//======================================================
// ■記事を1件削除する (管理用、POST)
// 
// http://127.0.0.1/basic-blog/?action=entrydelete
// 呼び出し元: ../index.php
//======================================================


パスワードチェック();
if (!自然数なら($_POST['id'])){ エラー('不正なIDです。'); }

//削除処理
データベース削除("delete from ブログ where 記事ID = {$_POST['id']}");


?>
<!DOCTYPE html>
<html><body>
<script>
window.onload = function(){
    if(window.opener.closed){
        location.href = '<?= $設定['URL'] ?>';
    }
    else{
        window.opener.location.href = '<?= $設定['URL'] ?>';
        setTimeout(function(){ window.close(); }, 0);
    }
};
</script>
</body></html>