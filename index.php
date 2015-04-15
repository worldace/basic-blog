<?php
//======================================================
// ■Basic Blog
$設定['バージョン'] = '1.01';
//======================================================


include('setting.php');
include('action/php/function.php');
include('action/php/boot.php');


switch($_GET['action']){
    case 'index'         : include('action/index.php');         break;
    case 'entry'         : include('action/entry.php');         break;
    case 'light'         : include('action/light.php');         break;
    case 'category'      : include('action/category.php');      break;
    case 'categorylist'  : include('action/categorylist.php');  break;
    case 'search'        : include('action/search.php');        break;
    case 'comment'       : include('action/comment.php');       break;
    case 'commentpost'   : include('action/commentpost.php');   break;
    case 'commentdelete' : include('action/commentdelete.php'); break;
    case 'entrypost'     : include('action/entrypost.php');     break;
    case 'entrypostform' : include('action/entrypostform.php'); break;
    case 'entryedit'     : include('action/entryedit.php');     break;
    case 'entryeditform' : include('action/entryeditform.php'); break;
    case 'entrydelete'   : include('action/entrydelete.php');   break;
    case 'versionload'   : include('action/versionload.php');   break;
    case 'upload'        : include('action/upload.php');        break;
    case 'uplist'        : include('action/uplist.php');        break;
    case 'upfilelist'    : include('action/upfilelist.php');    break;
    case 'upfiledelete'  : include('action/upfiledelete.php');  break;
    case 'thumbnailmake' : include('action/thumbnailmake.php'); break;
    case 'tool'          : include('action/tool.php');          break;
    case 'login'         : include('action/login.php');         break;
    case 'logout'        : include('action/logout.php');        break;
    case 'feed'          : include('action/feed.php');          break;
    default              : include('action/index.php');
}
