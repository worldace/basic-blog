<?php
//======================================================
// ■アップロードされたファイルを保存する (管理用、POST)
// 
// http://127.0.0.1/basic-blog/?action=upload
// 呼び出し元: ../index.php
//======================================================


パスワードチェック();

//エラーチェック
if(!$_FILES['file']['tmp_name']){ エラー("アップロードされたファイルがありません"); }

for($i = 0; $i < count($_FILES['file']['error']); $i++){
    switch($_FILES['file']['error'][$i]){
        case UPLOAD_ERR_OK: break;
        case UPLOAD_ERR_INI_SIZE: エラー(ini_get('upload_max_filesize')."B以上のファイルはアップできません\n{$_FILES['file']['name'][$i]}"); break;
        default: エラー("アップロードできませんでした (エラー番号：{$_FILES['file']['error'][$i]})\n{$_FILES['file']['name'][$i]}");
    }
}


//ディレクトリ作成
$年 = date('Y', $_SERVER['REQUEST_TIME']);
$月 = date('m', $_SERVER['REQUEST_TIME']);
$日 = date('d', $_SERVER['REQUEST_TIME']);
$permission = decoct(fileperms($設定['アップロードディレクトリ']) & 0777);

ディレクトリ作成("{$設定['アップロードディレクトリ']}", "$年", $permission);
ディレクトリ作成("{$設定['アップロードディレクトリ']}/$年", "$月$日", $permission);

if(!is_dir("{$設定['アップロードディレクトリ']}/$年/$月$日")){ エラー('ディレクトリが作成できません'); }


//複数アップロード対応
for($i = 0; $i < count($_FILES['file']['tmp_name']); $i++){
    $UP = array();
    $UP['ディレクトリ'] = "{$設定['アップロードディレクトリ']}/$年/$月$日";
    $UP['拡張子']       = pathinfo($_FILES['file']['name'][$i], PATHINFO_EXTENSION);
    $UP['ファイル名']   = "{$_SERVER['REQUEST_TIME']}.{$UP['拡張子']}";
    $UP['パス']         = "{$UP['ディレクトリ']}/{$UP['ファイル名']}";
    $UP['URL']          = "{$設定['ベースURL']}{$UP['パス']}";

    //ファイル名被りを防止するためにカウントアップ
    $_SERVER['REQUEST_TIME']++;

    //移動
    move_uploaded_file($_FILES['file']['tmp_name'][$i], $UP['パス']);
    if(!is_file($UP['パス'])){ エラー('ファイルが作成できません'); }

    //レスポンス用のタグを作る
    if(アップロードファイルが画像なら()){
        if(画像サイズが制限オーバーなら()){
            if(GDで縮小するなら()){
                $ajaxresponse .= "<a href=\"{$UP['URL']}\" target=\"_blank\"><img src=\"{$設定['ベースURL']}{$UP['縮小画像のパス']}\" width=\"{$UP['縮小後の横幅']}\" height=\"{$UP['縮小後の縦幅']}\" class=\"{$UP['popup']}\"></a>";
            }
            else{ //GDで縮小しない場合
                $ajaxresponse .= "<a href=\"{$UP['URL']}\" target=\"_blank\"><img src=\"{$UP['URL']}\" width=\"{$UP['縮小後の横幅']}\" height=\"{$UP['縮小後の縦幅']}\" class=\"{$UP['popup']}\"></a>";
            }
        }
        else { //画像サイズが制限以内の場合
            $ajaxresponse .= "<img src=\"{$UP['URL']}\" width=\"{$UP['横幅']}\" height=\"{$UP['縦幅']}\">";
        }
    }
    else{ //アップロードファイルが画像以外の場合
        $ajaxresponse .= "<a href=\"{$UP['URL']}\" target=\"_blank\">{$UP['ファイル名']}</a>";
    }
}


//レスポンスを返して終了
if(Ajaxなら()){ 
    テキスト表示($ajaxresponse);
}
else{
    リダイレクト("{$設定['URL']}?action=upfilelist&y=$年&m=$月&d=$日");
}




function アップロードファイルが画像なら(){
    global $UP;

    //拡張子で判定
    if(preg_match("/^(png|jpeg|jpg|gif)$/i", $UP['拡張子'])){
        list($UP['横幅'], $UP['縦幅'], $UP['フォーマット']) = getimagesize($UP['パス']);
        //フォーマットでも判定
        if($UP['横幅'] and $UP['縦幅'] and $UP['フォーマット'] >= 1 and $UP['フォーマット'] <= 3){
            return true;
        }
    }

    return false;
}


function 画像サイズが制限オーバーなら(){
    global $UP;
    global $設定;

    list($UP['縮小後の横幅'], $UP['縮小後の縦幅']) = 画像リサイズ計算($UP['横幅'], $UP['縦幅'], $設定['アップロード画像の最大横幅'], $設定['アップロード画像の最大縦幅']);

    //制限オーバーの場合
    if($UP['横幅'] != $UP['縮小後の横幅'] or $UP['縦幅'] != $UP['縮小後の縦幅']){
        if($設定['アップロード画像が最大幅を超えたらポップアップ'] == '○'){
            $UP['popup'] = "popup";
        }
        return true;
    }

    return false;
}


function GDで縮小するなら(){
    global $UP;
    global $設定;

    //GDで縮小する場合
    if($設定['アップロード画像が最大幅を超えたら縮小処理'] == '○' and is_callable("imagecopyresampled")){
        $UP['縮小画像のパス'] = "{$UP['ディレクトリ']}/{$_SERVER['REQUEST_TIME']}_mini.{$UP['拡張子']}";

        return 画像リサイズ($UP['フォーマット'], $UP['パス'], $UP['縮小画像のパス'], $UP['縮小後の横幅'], $UP['縮小後の縦幅']);
    }

    return false;
}
