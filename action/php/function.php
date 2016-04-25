<?php
//======================================================
// ■共通して利用する関数群
// 
// 呼び出し元: ../../index.php
//======================================================


function 部品(){
    global $設定;
    static $読み込み記録;

    $args = func_get_args();
    $name = array_shift($args);

    include_once("{$設定['ディレクトリ']}/action/parts/{$name}.php");

    $html = call_user_func_array("${name}_parts", $args);
    
    if(!$読み込み記録[$name]){
        $設定['埋め込みCSS']        .= $css;
        $設定['埋め込みJavaScript'] .= $js;
        $読み込み記録[$name] = true;
    }

    return $html;
}


function テンプレート表示($file){
    global $設定;

    $設定['現在使用中のテンプレート'] .= $file;

    header("Content-Type: text/html; charset=UTF-8");
    print テンプレート変換(file_get_contents($file), $設定);
    exit;
}


function テキスト表示($str){
    header("Content-Type: text/plain; charset=UTF-8");
    print $str;
    exit;
}


function テンプレート変換($検索対象, $設定){
    return preg_replace_callback('/《([^》]+)》/u',

    function($match) use($設定){

        $名前 = $match[1];
        $最初の文字 = substr($名前, 0, 1);

        switch($最初の文字){
            case "&":
                $名前 = substr($名前, 1);
                return h($設定[$名前]);

            case "%":
                $名前 = substr($名前, 1);
                return rawurlencode($設定[$名前]);

            case "?":
                $名前 = substr($名前, 1);
                ob_start();
                eval($名前 . ";");
                return ob_get_clean();

            default:
                return $設定[$名前];
        }
    },

    $検索対象);
}


function リダイレクト($url){
    $url = str_replace(array("\r\n","\r","\n"), '', $url);
    header("Location: $url");
    exit;
}


function エラー($str){
    global $設定;

    if(Ajaxなら()){
        header("HTTP/1.0 400 Bad Request");
        header("Content-Type: text/plain; charset=UTF-8");
        print $str;
    }
    else {
        $設定['エラー'] = $str;
        テンプレート表示("{$設定['テンプレート']}/error.html");
    }
    exit;
}


function パスワードチェック(){
    global $設定;

    $sha1 = パスワードハッシュ();
    if($_COOKIE['p'] !== $sha1){
        $query = urlencode($_SERVER['QUERY_STRING']);
        リダイレクト("{$設定['URL']}?action=login&query=$query");
    }
    
    if(!preg_match("|^{$設定['URL']}|", $_SERVER['HTTP_REFERER'])){
        エラー("リファラが有効である必要があります");
    }

    setcookie('p', $sha1, $_SERVER['REQUEST_TIME']+60*60*24*$設定['管理者用クッキー有効日数']);
}


function パスワードハッシュ(){
    global $設定;
    static $hash;

    if (!isset($hash)) {
        $hash = sha1(str_repeat($設定['パスワード'], 2) . __FILE__);
    }
    return $hash;
}


function データベース接続() {
    global $設定;
    static $pdo;

    if (!isset($pdo)) {
        $pdo = new PDO($設定['DBドライバ'], $設定['DBユーザ'], $設定['DBパスワード'], array(
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => true,
            //PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
        ));
    }
    return $pdo;
}


/*
$bindvalue(配列:プレースホルダに割り当てる変数)は全て文字列型に変換されるので、$bindvalueには文字列型の変数のみ入れる。
数値型の場合はPHP側で値を検証して、$sql(SQL文)の中で展開させておくこと。(ToDo:独自の数値型プレースホルダーを作った方がよさそう。記号は@)
*/
function データベース実行($sql, $bindvalue = array()){
    $db = データベース接続();
    if(!$bindvalue){
        return $db->query($sql);
    }
    else{
        $stmt = $db->prepare($sql);
        $stmt -> execute($bindvalue);
        return $stmt;
    }
}


function データベース取得($sql, $bindvalue = array()){
    $db = データベース接続();
    if(!$bindvalue){
        return $db->query($sql)->fetchAll();
    }
    else{
        $stmt = $db->prepare($sql);
        $stmt -> execute($bindvalue);
        return $stmt->fetchAll();
    }
}


function データベース行取得($sql, $bindvalue = array()){
    $db = データベース接続();
    if(!$bindvalue){
        return $db->query($sql)->fetch();
    }
    else{
        $stmt = $db->prepare($sql);
        $stmt -> execute($bindvalue);
        return $stmt->fetch();
    }
}


function データベース列取得($sql, $bindvalue = array()){
    $db = データベース接続();
    if(!$bindvalue){
        return $db->query($sql)->fetchAll(PDO::FETCH_COLUMN);
    }
    else{
        $stmt = $db->prepare($sql);
        $stmt -> execute($bindvalue);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}


function データベースセル取得($sql, $bindvalue = array()){
    $db = データベース接続();
    if(!$bindvalue){
        return $db->query($sql)->fetchColumn();
    }
    else{
        $stmt = $db->prepare($sql);
        $stmt -> execute($bindvalue);
        return $stmt->fetchColumn();
    }
}


function データベース件数($sql, $bindvalue = array()){ //select count(列名) from テーブル名 where ...
    return データベースセル取得($sql, $bindvalue);
}


function データベース追加($sql, $bindvalue = array()){
    $db = データベース接続();
    if(!$bindvalue){
        $db->query($sql);
        return $db->lastInsertId();
    }
    else{
        $stmt = $db->prepare($sql);
        $stmt -> execute($bindvalue);
        return $db->lastInsertId();
    }
}


function データベース更新($sql, $bindvalue = array()){
    $db = データベース接続();
    if(!$bindvalue){
        return $db->exec($sql);
    }
    else{
        $stmt = $db->prepare($sql);
        $stmt -> execute($bindvalue);
        return $stmt->rowCount();
    }
}


function データベース削除($sql, $bindvalue = array()){
    return データベース更新($sql, $bindvalue);
}


function データベーステーブル作成($テーブル名, $テーブル定義, $PDOドライバ){
    //MySQLとSQLite両対応のテーブル作成
    //※$テーブル定義は「キー:列名」「値:型情報」の連想配列。MySQL互換であること

    foreach($テーブル定義 as $key => $value){
        $sql .= "$key $value,";
    }
    $sql = rtrim($sql, ',');

    //SQLiteの場合
    if(preg_match('/^sqlite/i', $PDOドライバ)){
       $sql = str_replace('auto_increment', 'autoincrement', $sql);
        データベース実行("create table IF NOT EXISTS $テーブル名 ($sql)");
    }
    //MySQLの場合
    else {
       $MySQL追加文 = " ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci";
       データベース実行("create table IF NOT EXISTS $テーブル名 ($sql) $MySQL追加文");
    }
}


function URL作成($querystring = false){
    if($_SERVER["HTTPS"] != 'on') {
        $scheme = "http://";
        if($_SERVER['SERVER_PORT'] != 80) { $port = ":" . $_SERVER['SERVER_PORT']; }
    }
    else {
        $scheme = "https://";
        if($_SERVER['SERVER_PORT'] != 443){ $port = ":" . $_SERVER['SERVER_PORT']; }
    }

    if($querystring === false){
        $request_uri = preg_replace("/\?.*$/", "", $_SERVER['REQUEST_URI']);
        $url = $scheme . $_SERVER["HTTP_HOST"] . $port . $request_uri;
    }
    else{
        $url = $scheme . $_SERVER["HTTP_HOST"] . $port . $_SERVER['REQUEST_URI'];
    }

    return $url;
}


function 記事URL作成($id, $encode = false){
    global $設定;

    $url = $設定['URL'] . "?action=entry&id=$id";

    if($encode == true){
        $url = $設定['URL'] . "?action=entry&amp;id=$id";
    }

    if($設定['URL書き換え'] == "○"){
        $url = $設定['ベースURL'] . $id;
    }

    return $url;
}

function 投稿文字列処理($str, $br = false){
    $str = trim($str); 
    $str = str_replace('>', '＞', $str); 
    $str = str_replace('<', '＜', $str);
    $str = str_replace('"', '”', $str);
    //$str = str_replace('&', '＆', $str);
    $str = str_replace("\0", "", $str);

    //改行処理
    if($br){ $str = str_replace(array("\r\n","\r","\n"), '<br>', $str); }
    else   { $str = str_replace(array("\r\n","\r","\n"), '', $str); }

    return $str;
}


function 日付変換($time = 0, $style = 0){

    if(!$time){ $time = $_SERVER['REQUEST_TIME']; }

    $曜日一覧 = array('日','月','火','水','木','金','土');
    $曜日 = $曜日一覧[date('w', $time)];

    switch($style){
        case 1  : return date("Y年n月j日({$曜日}) H:i", $time);
        case 2  : return date("c", $time);
        case 3  : return date("Y/m/d", $time);
        case 4  : return $曜日;
        case 5  : return date("Y年n月j日({$曜日})", $time);
        case 6  : return date("Y年n月j日", $time);
        default : return date("Y/m/d H:i", $time);
    }
}


function 経過時間($time = 0, $format = "Y/m/d H:i"){

    if(!$time){ $time = $_SERVER['REQUEST_TIME']; }

    $曜日一覧 = array('日','月','火','水','木','金','土');
    $曜日 = $曜日一覧[date('w', $time)];
    $format = str_replace('__', $曜日, $format);

    $時間差 = $_SERVER['REQUEST_TIME'] - $time;
    if($時間差 < 1){ $時間差 = 1; }
    switch($時間差){
        case $時間差 < 60     : return "{$時間差}秒前";
        case $時間差 < 3600   : return floor($時間差/60)   . "分前";
        case $時間差 < 86400  : return floor($時間差/3600) . "時間前";
        case $時間差 < 604800 : return floor($時間差/86400) . "日前";
        default: return date($format, $time);
    }
}


function GETなら(){
    if(strtolower($_SERVER['REQUEST_METHOD']) == 'get'){ return true; }
    else { return false; }
}


function POSTなら(){
    if(strtolower($_SERVER['REQUEST_METHOD']) == 'post'){ return true; }
    else { return false; }
}


function Ajaxなら(){
    if(strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){ return true; }
    else { return false; }
}


function 自然数なら($num){
    if (preg_match("/^[1-9][0-9]*$/", $num)){ return true; }
    else{ return false; }
}


function 管理者なら(){
    static $admin;

    if(!isset($admin)){
        if($_COOKIE['p'] and $_COOKIE['p'] === パスワードハッシュ()){ $admin = true; }
        else{ $admin = false; }
    }
    
    return $admin;
}


function h($str){
    return htmlspecialchars($str, ENT_COMPAT, "UTF-8");
}


function ファイル一覧取得($path = "./"){
    $ファイル一覧 = array();
    if(!is_dir($path)){ return array(); }

    $handle = opendir($path);
    while ($file = readdir($handle)){
        if(is_file("$path/$file")){
            $ファイル一覧[] = $file;
        }
    }
    closedir($handle);

    sort($ファイル一覧);
    return $ファイル一覧;
}


function ディレクトリ一覧取得($path = "./"){
    $ディレクトリ一覧 = array();
    if(!is_dir($path)){ return array(); }

    $handle = opendir($path);
    while ($dir = readdir($handle)){
        if($dir == "." or $dir == ".."){ continue; }
        if(is_dir("$path/$dir")){
            $ディレクトリ一覧[] = $dir;
        }
    }
    closedir($handle);

    sort($ディレクトリ一覧);
    return $ディレクトリ一覧;
}


function ディレクトリ作成($path, $name, $permission = 707){
    if(is_dir("$path/$name")){ return false; }

    mkdir("$path/$name");
    chmod("$path/$name", octdec($permission));
}


function バイト数を変換($byte){
    if($byte == 0){
        return '0 バイト';
    }
    if($byte < 1024){
        return '1 KB';
    }

    $KB = $byte / 1024;
    if($KB < 1024){
        return round($KB) .' KB';
    }

    $MB = $byte / 1024 /1024;
    if($MB < 100){
        return round($MB, 1) .' MB';
    }
    elseif($MB < 1024){
        return round($MB) .' MB';
    }

    $GB = $byte / 1024 /1024 /1024;
    if($GB < 100){
        return round($GB, 2) .' GB';
    }
    elseif($GB < 1024){
        return round($GB, 1) .' GB';
    }
    else{
        return round($GB) .' GB';
    }
}



function バイト数に変換($str){
    $str = strtoupper($str);
    if(preg_match("/^([\d|\.]+)([K|M|G])$/i", $str, $match)){
        switch($match[2]){
            case 'K': return floor($match[1]*1024);
            case 'M': return floor($match[1]*1024*1024);
            case 'G': return floor($match[1]*1024*1024*1024);
        }
    }
    else { return $str; }
}


function 画像リサイズ($フォーマット, $元ファイル, $新ファイル, $新W, $新H, $元X = 0, $元Y = 0, $元W = 0, $元H = 0){ //※GDが必要
    switch($フォーマット){
        case IMAGETYPE_GIF : return  GIFリサイズ($元ファイル, $新ファイル, $新W, $新H, $元X, $元Y, $元W, $元H);
        case IMAGETYPE_JPEG: return JPEGリサイズ($元ファイル, $新ファイル, $新W, $新H, $元X, $元Y, $元W, $元H);
        case IMAGETYPE_PNG : return  PNGリサイズ($元ファイル, $新ファイル, $新W, $新H, $元X, $元Y, $元W, $元H);
        default: return false;
    }
}


function JPEGリサイズ($元ファイル, $新ファイル, $新W, $新H, $元X = 0, $元Y = 0, $元W = 0, $元H = 0){ //※GDが必要
    $元画像 = @imagecreatefromjpeg($元ファイル);
    if(!$元画像){ return false; }

    $新画像 = imagecreatetruecolor($新W, $新H);

    if(!$元W){ $元W = imagesx($元画像); }
    if(!$元H){ $元H = imagesy($元画像); }

    imagecopyresampled($新画像, $元画像, 0, 0, $元X, $元Y, $新W, $新H, $元W, $元H);

    imagejpeg($新画像, $新ファイル, 90);

    imagedestroy($元画像);
    imagedestroy($新画像);

    return true;
}


function PNGリサイズ($元ファイル, $新ファイル, $新W, $新H, $元X = 0, $元Y = 0, $元W = 0, $元H = 0){ //※GDが必要
    $元画像  = @imagecreatefrompng($元ファイル);
    if(!$元画像){ return false; }

    $新画像  = imagecreatetruecolor($新W, $新H);

    if(!$元W){ $元W = imagesx($元画像); }
    if(!$元H){ $元H = imagesy($元画像); }

    imagealphablending($新画像, false); // アルファブレンディングをoffにする
    imagesavealpha($新画像, true);      // 完全なアルファチャネル情報を保存するフラグをonにする

    imagecopyresampled($新画像, $元画像, 0, 0, $元X, $元Y, $新W, $新H, $元W, $元H);

    imagepng($新画像, $新ファイル);

    imagedestroy($元画像);
    imagedestroy($新画像);

    return true;
}


function GIFリサイズ($元ファイル, $新ファイル, $新W, $新H, $元X = 0, $元Y = 0, $元W = 0, $元H = 0){ //※GDが必要
    $元画像 = @imagecreatefromgif($元ファイル);
    if(!$元画像){ return false; }

    $新画像 = imagecreatetruecolor($新W, $新H); //これで動く

    if(!$元W){ $元W = imagesx($元画像); }
    if(!$元H){ $元H = imagesy($元画像); }

    $alpha = imagecolortransparent($元画像); // 元画像から透過色を取得する
    imagefill($新画像, 0, 0, $alpha);        // その色でキャンバスを塗りつぶす
    imagecolortransparent($新画像, $alpha);  // 塗りつぶした色を透過色として指定する

    imagecopyresampled($新画像, $元画像, 0, 0, $元X, $元Y, $新W, $新H, $元W, $元H);

    imagegif($新画像, $新ファイル);

    imagedestroy($元画像);
    imagedestroy($新画像);

    return true;
}


function 画像リサイズ計算($imgW, $imgH, $limitW, $limitH){
    //画像サイズと制限サイズを受け取って、制限内に収まる画像サイズを返す(比率は維持する)

    //制限サイズからはみ出る量を計算。正ならはみ出る
    $overW  = $imgW - $limitW;
    $overH  = $imgH - $limitH;

    //横も縦もはみ出ない時
    if($overW <= 0 && $overH <= 0){
        $newW = $imgW;
        $newH = $imgH;
    }

    //横だけはみ出る時
    else if($overW > 0 && $overH <= 0){
        $newW = $limitW;
        $newH = $newW * $imgH / $imgW;
    }

    //縦だけはみ出る時
    else if($overW <= 0 && $overH > 0){
        $newH = $limitH;
        $newW = $newH * $imgW / $imgH;
    }

    //横も縦もはみ出る時
    else{
        if($overW > $overH){
            //横を画面内に収める
            $newW = $limitW;
            $newH = $newW * $imgH / $imgW;
            //まだ縦がはみ出る場合
            if($newH > $limitH){
                $beforeH = $newH;
                $newH = $limitH;
                $newW = $newH * $newW / $beforeH;
            }
        }
        else{
            //縦を制限内に収める
            $newH = $limitH;
            $newW = $newH * $imgW / $imgH;
            //まだ横がはみ出る場合
            if($newH > $limitH){
                $beforeW = $newW;
                $newW = $limitW;
                $newH = $newW * $newH / $beforeW;
            }
        }
    }
    return array(floor($newW), floor($newH));
}



function キャッシュ保存($name, $data){
    $tempfile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . get_current_user() . "-" . $name;
    file_put_contents($tempfile, serialize($data), LOCK_EX);
}


function キャッシュ削除($names){
    $temp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . get_current_user() . "-";

    if(is_scalar($names)){
        $names = array($names);
    }

    foreach($names as $name){
        @unlink($temp . $name);
    }
}


function キャッシュ取得($name){
    $tempfile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . get_current_user() . "-" . $name;
    if(!file_exists($tempfile)){
        return false;
    }
    else{
        return unserialize(file_get_contents($tempfile));
    }
}


function コメントが受付中なら($entry){
    global $設定;

    $経過日数 = ($_SERVER['REQUEST_TIME'] - $entry['記事投稿時間']) / 60 / 60 / 24;

    if($設定['コメント許可'] == '×' and $entry['記事コメント許可'] != '◎') {
        $設定['エラー'] = "このブログではコメントを受け付けていません";
        return false;
    }
    if($entry['記事コメント許可'] == '×'){
        $設定['エラー'] = "この記事ではコメントを受け付けていません";
        return false;
    }
    if($entry['記事コメント数'] >= $設定['1記事当たりのコメント最大数']){
        $設定['エラー'] = "この記事にはこれ以上コメントできません";
        return false;
    }
    if($経過日数 > $設定['コメント欄の有効期限'] and $設定['コメント欄の有効期限'] > -1){
        $設定['エラー'] = "この記事にはもうコメントできません";
        return false;
    }

    return true;
}


function コメントを表示するなら($entry){
    global $設定;

    if(管理者なら()){ return true; }


    $経過日数 = ($_SERVER['REQUEST_TIME'] - $entry['記事投稿時間']) / 60 / 60 / 24;

    if(!$entry['記事コメント数']){
        return false;
    }
    if($経過日数 > $設定['コメント欄の有効期限'] and $設定['コメント欄の有効期限'] > -1){
        if($設定['期限切れのコメント表示'] == '×'){
            return false;
        }
    }

    return true;
}

function 全カテゴリ(){
    global $設定;

    foreach(データベース列取得("select 記事カテゴリ from ブログ") as $カテゴリ欄){
        foreach(explode("\n", $カテゴリ欄) as $カテゴリ){
            if($カテゴリ == ''){ continue; }
            $全カテゴリ[] = $カテゴリ;
        }
    }
    if(!$全カテゴリ){ return array(); }

    $カテゴリ一覧 = array_count_values($全カテゴリ);//連想配列に変換(キー:カテゴリ名、値:出現回数)
    arsort($カテゴリ一覧); //値を大きい順に並び替える
    return $カテゴリ一覧;
}


function Ping送信(){
    global $設定;

    $blog = h($設定['ブログ名']);
    $url  = h($設定['URL']);

    $pingxml = "<?xml version=\"1.0\" encoding=\"utf-8\"?><methodCall><methodName>weblogUpdates.ping</methodName><params><param><value>$blog</value></param><param><value>$url</value></param></params></methodCall>";

    $header  = array("Content-Type: text/xml", "Content-Length: " . strlen($pingxml));
    $context = array("http" => array("method"=>"POST", "header"=>implode("\r\n", $header), "content"=>$pingxml, "timeout"=>3));

    foreach($設定['Ping送信先'] as $url){
        file_get_contents($url, false, stream_context_create($context));
    }
}


function アイキャッチ画像検索($body){
    global $設定;

    include_once($設定['actionディレクトリ'] . '/php/dom.php');

    $dom = str_get_html($body);

    $eyecatch = $dom->find('.eyecatch', 0);
    $img      = $dom->find('img', 0);

    if($eyecatch){
        $url = $eyecatch->getAttribute('src');
    }
    elseif($img){
        $url = $img->getAttribute('src');
    }

    if(preg_match("|^{$設定['ベースURL']}|", $url)){
        return $url;
    }
}


function サムネイル画像検索($eyecatch){
    global $設定;

    if(!$eyecatch){ return; }

    $eyecatch  = str_replace($設定['ベースURL'], "", $eyecatch);
    $thumbnail = preg_replace("/\.(\w+)$/", ".thumb.$1", $eyecatch);

    if(file_exists($thumbnail)){
        return $設定['ベースURL'] . $thumbnail;
    }
}


function ログイン失敗回数(){
    $日付 = date('YmdH', $_SERVER['REQUEST_TIME']);
    $記録 = キャッシュ取得("login-error");
    $回数 = 0;

    if($記録[0] == $日付){
        $回数 = $記録[1];
    }
    return $回数;
}


function ログイン失敗を記録する(){
    $日付 = date('YmdH', $_SERVER['REQUEST_TIME']);
    $記録 = キャッシュ取得("login-error");
    $回数 = 1;

    if($記録[0] == $日付){
        $回数 = $記録[1] + 1;
    }

    キャッシュ保存("login-error", array($日付, $回数));
    return $回数;
}



function 開発用の設定(){
    global $設定;

    //開発環境なら
    if($_SERVER['SERVER_SOFTWARE'] == 'PHP 7.0.0 Development Server'){
        error_reporting(E_ALL ^ E_NOTICE);
        ini_set('display_errors', 1);

        $設定['Ping送信'] = '×';

        if($設定['パスワード'] == ''){
            $設定['パスワード'] = "1";
        }
    }
}


