<?
define('MANAGEversion','201705091');
require_once('class/db.php');

@error_reporting(E_ALL &~ E_NOTICE);
@set_time_limit(0);
date_default_timezone_set('PRC');
if(!isset($_SERVER['PHP_SELF'])||empty($_SERVER['PHP_SELF']))$_SERVER['PHP_SELF']=$_SERVER['SCRIPT_NAME'];
header('Content-type:text/html;charset=utf-8');
$password=setting::get('password');if($password==''&&$_REQUEST['a']!='init'){exit('<script>window.location.href="?a=init"</script>');}define('PWD',md5($password));
session_start();
$cookiepwd=isset($_SESSION['tinywebdbmanage'])?$_SESSION['tinywebdbmanage']:'';
$a=isset($_REQUEST['a'])?$_REQUEST['a']:'index';if(isset($_REQUEST['k']))$k=$_REQUEST['k'];if(isset($_REQUEST['v']))$v=$_REQUEST['v'];

function check_login($noecho=false){global $cookiepwd;if($cookiepwd!=PWD){if($noecho===false){exit('<script>window.location.href="?a=index"</script>');}else{return false;}}return true;}
function urlencode_plus($s){
    $s=str_replace('%','%25',$s);
    $s=str_replace('+','%2B',$s);
    $s=str_replace(' ','%20',$s);
    $s=str_replace('/','%2F',$s);
    $s=str_replace('?','%3F',$s);
    $s=str_replace('#','%23',$s);
    $s=str_replace('&','%26',$s);
    $s=str_replace('=','%3D',$s);
    return $s;
}

if($a=='login'){$passwd=md5($_POST['passwd']);if($passwd==PWD){$_SESSION['tinywebdbmanage']=$passwd;exit('<script>window.location.href="?a=all"</script>');}else exit('<script>alert("密码错误");history.go(-1);</script>');}
elseif($a=='logout'){$_SESSION['tinywebdbmanage']='';echo'<script>window.location.href="?a=index";</script>';}

if($_REQUEST['noecho']!=='true'){
  ?><html>
    <head>
        <title>TinyWebDB Manager</title>
        <link href="//cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
        <link href="//cdn.bootcss.com/alertify.js/0.3.11/alertify.default.min.css" rel="stylesheet">
        <link href="//cdn.bootcss.com/alertify.js/0.3.11/alertify.core.min.css" rel="stylesheet">
        <link href="//cdn.bootcss.com/bootstrap-fileinput/4.3.9/css/fileinput.min.css" rel="stylesheet">
        <link href="/script/manage.css" rel="stylesheet">
        <script src="//cdn.bootcss.com/jquery/3.2.1/jquery.js"></script>
        <script src="//cdn.bootcss.com/alertify.js/0.3.11/alertify.min.js"></script>
        <script src="//cdn.bootcss.com/bootstrap-fileinput/4.3.9/js/fileinput.min.js"></script>
        <script src="//cdn.bootcss.com/bootstrap-fileinput/4.3.9/js/locales/zh.js"></script>
        <script src="/script/manage.js"></script>
    </head>
    <body onload="setTimeout(CheckUpdate('aix.colintree.cn',<? echo MANAGEversion;?>),2000);setTimeout(CheckUpdate('www.source-space.cn',<? echo MANAGEversion;?>),2000);">
        <div id="body">
            <div id="header"><div id="title" class="text-center"><a href="/tinywebdb">TinyWebDB</a></div><div class="btn-group btn-group-justified"><a id="update_available" href="http://aix.colintree.cn/article/TinyWebDB_SAE_PHP-1" style="display:none;color:red;" class="btn btn-default">管理系统有更新！</a><a href="?a=all" class="btn btn-default">全部标签</a><a href="?a=backup" class="btn btn-default">备份/恢复</a><a href="?a=file" class="btn btn-default">文件目录</a><a href="?a=setting" class="btn btn-default">设置</a><a href="?a=logout" class="btn btn-default">退出</a></div></div>
            <div id="main">
                <div id="all_processing_background" style="display:none"><div class="panel panel-default"><div class="panel-body text-center"><h4 id="all_processing_msg1">MSG1</h4><h4 id="all_processing_msg2" style="margin:0">MSG2</h4></div></div></div>
                <div id="tag_show_background" style="display:none"><div class="panel panel-default"><div class="panel-heading"><h3 class="panel-title">查看标签</h3><div><span class="glyphicon glyphicon-remove"></span></div></div><div class="panel-body"><p>标签：<code>TAG</code></p><p>值：<button button-task="show-tag-json" class="btn btn-info">查看原值</button></p><pre class="line-number"></pre></div></div></div>
                <div id="tag_edit_background" style="display:none"><div class="panel panel-default"><div class="panel-heading"><h3 class="panel-title">编辑标签</h3><div><span class="glyphicon glyphicon-remove"></span></div></div><div class="panel-body"><p>标签：<input type="text" class="form-control"><input type="hidden"></p><p>值：<textarea class="form-control"></textarea></p><p><button button-task="edit-tag-submit" class="btn btn-primary" style="font-weight:bold;width:100%;margin:0">保存</button></p></div></div></div>
                <div id="myconfirm_background" style="display:none"><div class="panel panel-default"><div class="panel-heading"><h3 class="panel-title" id="myconfirm_title">TITLE</h3></div><div class="panel-body text-center"><div id="myconfirm_msg">MSG</div><div id="myconfirm_buttonbox"><button id="myconfirm_ok" class="btn btn-primary">OK</button><button id="myconfirm_cancel" class="btn btn-default">CANCEL</button></div></div></div></div><?
}
switch($a){
	case'init': require('init.php'); break;
	case'index': if($cookiepwd==PWD)exit('<script>window.location.href="?a=all"</script>');?><div class="panel panel-default"><div class="panel-heading"><h3 class="panel-title">请输入后台密码</h3></div><div class="panel-body"><form action="?a=login" method="post"><div class="input-group"><input autocomplete="off" autofocus type="password" class="form-control" name="passwd"><span class="input-group-btn"><input class="btn btn-default" type="submit" value="确定"></span></div></form></div></div><? break;
    case'apiset': header('Content-Type: application/json');if( check_login(true) ){exit(json_encode(['status'=>db::set($_REQUEST['tag'],$_REQUEST['value'])!==false]));}else{exit(json_encode(['status'=>false,'msg'=>'储存失败：请登录']));} break;
    case'apiget':
    case'apiget-json': header('Content-Type: application/json'); if( check_login(true) ){$apiget_val2=$apiget_val=db::get($_REQUEST['tag']);if($a=='apiget-json' && is_string($apiget_val)){$apiget_val=print_r(json_decode($apiget_val,true),true);if($apiget_val==NULL){$apiget_val=$apiget_val2.'';}}else{$apiget_val.='';}exit(json_encode(['tag'=>$_REQUEST['tag'],'value'=>$apiget_val]));}else{exit(json_encode(['tag'=>$_REQUEST['tag'],'value'=>'获取失败：请登录']));} break;
	case'backup': check_login(); require('backup.php'); break;
	case'setting': check_login(); require('setting.php'); break;
	case'all': check_login(); ?><div class="panel panel-default"><div class="panel-heading"><h3 class="panel-title" style="display:inline-block">标签 - <? $all_category=explode('#',setting::get('all_categorylist'));if(isset($_REQUEST['category'])){array_unshift($all_category,$_REQUEST['category']);}$all_category=array_unique($all_category);if(!is_array($all_category) || empty($all_category)){$all_category=[''];} ?><select id="all_category"><? foreach($all_category as $category){echo'<option value="',htmlspecialchars($category),'">',($category==''?'显示所有':$category),'</option>';} ?></select></h3><form method="get" id="all_search_form"><div class="input-group"><input autocomplete="off" type="text" placeholder="搜索" class="form-control" name="keyword"><input type="hidden" name="a" value="search"><span class="input-group-btn"><button class="btn btn-default" type="submit"><span class="glyphicon glyphicon-search"></span></button></span></div></form></div><div class="panel-body text-center"><div id="all_toolbar_frame" class="panel panel-default text-center" style="display:none">操作：<button id="all_toolbar_selectall" class="btn btn-primary">全选</button><button id="all_toolbar_selectnone" style="display:none" class="btn btn-primary">全不选</button><button id="all_toolbar_delete" class="btn btn-danger">删除</button></div><table class="table table-hover"><thead><th></th><th>标签-值</th><th>操作</th></thead><tbody><tr><td></td><td><input type="text" class="form-control" placeholder="标签名" id="new_tag"></td><td><button class="btn btn-primary" button-task="new-tag" style="margin-top:0">新建</button></td></tr><? foreach(db::getall($_REQUEST['category']) as $k=>$v){$showTag=htmlspecialchars($k);$showValue=substr(str_replace("\n",'',htmlspecialchars($v)),0,120);echo'<tr>', '<td><input type="checkbox" tinywebdb_key="',$showTag,'"></td>', '<td>',$showTag,'<br><div class="tinywebdb-value">',$showValue,'</div></td>', '<td><button tinywebdb_key="',$showTag,'" class="btn btn-primary" button-task="show-tag">查看</button><button tinywebdb_key="',$showTag,'" class="btn btn-primary" button-task="edit-tag">修改</button><button tinywebdb_key="',$showTag,'" class="btn btn-danger" button-task="delete-tag">删除</button></td>', '</tr>';}?></tbody></table></div></div><? break;
    case'search': check_login(); ?><form method="get" id="search_form"><div class="input-group"><input autocomplete="off" type="text" class="form-control" name="keyword" value="<? echo htmlspecialchars($_GET['keyword']); ?>"><input type="hidden" name="a" value="search"><span class="input-group-btn"><input class="btn btn-default" type="submit" value="搜索"></span></div><div class="panel panel-default"><div class="panel-heading"><div id="search_filter_controler"><b>搜索条件</b><span></span></div><div><div class="checkbox"><label><input type="checkbox" name="ignorecase"<? echo($_GET['ignorecase']=='on'?' checked':'');?>>忽略大小写</label></div><label class="radio-inline"><input type="radio" name="range" value="tag"<? echo($_GET['range']!='value'&&$_GET['range']!='both'?' checked':'');?>>标签</label><label class="radio-inline"><input type="radio" name="range" value="value"<? echo($_GET['range']=='value'?' checked':'');?>>值</label><label class="radio-inline"><input type="radio" name="range" value="both"<? echo($_GET['range']=='both'?' checked':'');?>>标签或值</label></div></div></div></form><? $search_rst=$_GET['keyword']=='' ? db::getall() : db::search($_GET['keyword'],$_GET['ignorecase']=='on',$_GET['range']!='value',$_GET['range']=='value'||$_GET['range']=='both'); ?><div class="panel panel-default"><div class="panel-heading"><h3 class="panel-title">搜索结果<? if(!empty($search_rst)){echo' 找到',count($search_rst)+0,'条结果';} ?></h3></div><div class="panel-body text-center"><div id="all_toolbar_frame" class="panel panel-default text-center" style="display:none">操作：<button id="all_toolbar_selectall" class="btn btn-primary">全选</button><button id="all_toolbar_selectnone" style="display:none" class="btn btn-primary">全不选</button><button id="all_toolbar_delete" class="btn btn-danger">删除</button></div><table class="table table-hover"><? if(!empty($search_rst)){?><thead><th></th><th>标签-值</th><th>操作</th></thead><tbody><? foreach($search_rst as $k=>$v){$showTag=htmlspecialchars($k);$showValue=substr(str_replace("\n",'',htmlspecialchars($v)),0,120);echo'<tr>', '<td><input type="checkbox" tinywebdb_key="',$showTag,'"></td>', '<td>',$showTag,'<br><div class="tinywebdb-value">',$showValue,'</div></td>', '<td><button tinywebdb_key="',$showTag,'" class="btn btn-primary" button-task="show-tag">查看</button><button tinywebdb_key="',$showTag,'" class="btn btn-primary" button-task="edit-tag">修改</button><button tinywebdb_key="',$showTag,'" class="btn btn-danger" button-task="delete-tag">删除</button></td>', '</tr>';}?></tbody><?}else{echo'<tr><td colspan="3" class="text-center" style="border:0">没有找到 ',($_GET['range']=='both'?'标签和值':($_GET['range']=='value'?'值':'标签')),' 中包含关键字的结果</td></tr>';}?></table></div></div><? break;
    case'apimdelete': if(check_login(true)){if(isset($_POST['tags'])){db::mdelete($_POST['tags']);}exit('finish');}else{http_response_code(401);exit('删除失败：请登录');} break;
    case'file': check_login(); require('file.php'); break;
    case'file-upload': if(check_login(true)){$f_tag='files';if(!empty($_FILES[$f_tag])){$data=file_get_contents($_FILES[$f_tag]['tmp_name']);if(kvfile::save($_REQUEST['dir'].$_FILES[$f_tag]['name'],$data)!==false){exit('{}');}else{http_response_code(401);exit('{"error":"上传失败：储存错误"}');}}else{http_response_code(401);exit('{"error":"上传失败：数据为空"}');}}else{http_response_code(401);exit('{"error":"上传失败：请登录"}');} break;
    case'file-delete': if(check_login(true)){kvfile::del($_POST['filepath']);exit('finish');}else{http_response_code(401);exit('删除失败：请登录');} break;
}
if($_REQUEST['noecho']!=='true'){?><div style="width:100%;padding-left:10px;color:#bbb;">TinyWebDB MANAGE System By ColinTree @ <a target="_blank" style="color:#bbb;text-decoration:underline" href="http://www.colintree.cn">colintree.cn</a> VERSION: <? echo MANAGEversion;?></div><?}