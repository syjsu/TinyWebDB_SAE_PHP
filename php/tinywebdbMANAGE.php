<?
define('MANAGEversion','201704162');
define('prefix','tinywebdb_');

@error_reporting(E_ALL &~ E_NOTICE);@set_time_limit(0);date_default_timezone_set('PRC');if(!isset($_SERVER['PHP_SELF'])||empty($_SERVER['PHP_SELF']))$_SERVER['PHP_SELF']=$_SERVER['SCRIPT_NAME'];header('Content-type:text/html;charset=utf-8');$kv=new SaeKV();$password=$kv->get('tinywebdbMANAGE_password');if($password==''&&$_REQUEST['a']!='init'){exit('<script>window.location.href="?a=init"</script>');}define('PWD',md5($password));session_start();
$cookiepwd=isset($_SESSION['tinywebdbmanage'])?$_SESSION['tinywebdbmanage']:'';$kv=new SaeKV();$kv->init();$a=isset($_REQUEST['a'])?$_REQUEST['a']:'index';if(isset($_REQUEST['k']))$k=($_REQUEST['k']);$v=isset($_REQUEST['v'])?($_REQUEST['v']):'';

function check_login(){global $cookiepwd;if($cookiepwd!=PWD)exit('<script>window.location.href="?a=index"</script>');}
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

if($_REQUEST['noecho']!='true'){
  ?><html><?
	?><head><title>TinyWebDB Manager</title><link rel="stylesheet" href="/script/manage.css"><link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous"><script src="/script/manage.js"></script></head><?
	?><body onload="setTimeout(CheckUpdate('aix.colintree.cn',<? echo MANAGEversion;?>),2000);setTimeout(CheckUpdate('www.source-space.cn',<? echo MANAGEversion;?>),2000);"><div id="body"><div id="header"><div id="title" class="text-center"><a href="/tinywebdb">TinyWebDB</a></div><div class="btn-group btn-group-justified"><a id="update_available" href="http://aix.colintree.cn/article/TinyWebDB_SAE_PHP-1" style="display:none;color:red;" class="btn btn-default">管理系统有更新！</a><a href="?a=all" class="btn btn-default">全部</a><a href="?a=set" class="btn btn-default">添加</a><a href="?a=backup" class="btn btn-default">备份/恢复</a><a href="?a=setting" class="btn btn-default">设置</a><a href="?a=logout" class="btn btn-default">退出</a></div></div><div id="main"><?
}
switch($a){
	case'init': require('init.php'); break;
	case'index': if($cookiepwd==PWD)exit('<script>window.location.href="?a=all"</script>');?><div class="panel panel-default"><div class="panel-heading"><h3 class="panel-title">请输入后台密码</h3></div><div class="panel-body"><form action="?a=login" method="post"><div class="input-group"><input autocomplete="off" type="password" class="form-control" name="passwd"><span class="input-group-btn"><input class="btn btn-default" type="submit" value="确定"></span></div></form></div></div><? break;
	case'set': check_login();if(isset($k)&&!empty($v)){$kv->set(prefix.$k,$v);?><div class="alert alert-success"><h3>设置成功</h3><p>Tag:<code><? echo htmlspecialchars($k); ?></code></p><p>Value:</p><pre style="word-break:break-word"><? echo htmlspecialchars($v); ?></pre></div><?}else{$v=$kv->get(prefix.$k);?><div class="panel panel-default"><div class="panel-heading"><h3 class="panel-title">添加/修改</h3></div><div class="panel-body text-center"><form action="?a=set" method="post"><p>Tag:<input type="text" class="form-control" name="k" value="<? echo $k; ?>"/></p><p>Value:</p><textarea rows="8" name="v" class="form-control"><? echo htmlspecialchars($v); ?></textarea><p><input type="submit" value="保存" class="btn btn-default"/></p></form></div></div><?} break;
	case'get': check_login();if(isset($k)){$v=$kv->get(prefix.$k);if($v!==FALSE){$view = isset($_REQUEST['view'])?$_REQUEST['view']:'';if($view=='json'&&is_string($view))$v=json_decode($v,1);?><div class="alert alert-success"><p><a href="?a=get&view=json&k=<? echo urlencode($k);?>" class="btn btn-default">Json解码</a></p><p>Key:<? echo htmlspecialchars($k); ?></p><p>Val:</p><pre><? echo htmlspecialchars(print_r($v,true)); ?></pre></div><?}else{echo '<dic class="alert alert-danger">',htmlspecialchars($k),' 不存在！</div>';}}else{?><div class="panel panel-default"><div class="panel-heading"><h3 class="panel-title">查看</h3></div><div class="panel-body text-center"><form action="?a=get" method="post"><div class="input-group"><input type="text" class="form-control" name="k"><span class="input-group-btn"><input class="btn btn-default" type="submit" value="查看"></span></div></form></div></div><?} break;
	case'del': check_login();if(isset($k)){$v=$kv->delete(prefix.$k);?><div class="alert alert-success"><? echo htmlspecialchars($k); ?> 已删除！</div><?}else{?><div class="panel panel-default"><div class="panel-heading"><h3 class="panel-title">删除</h3></div><div class="panel-body text-center"><form action="?a=del" method="post"><div class="input-group"><input type="text" class="form-control" name="k"/><span class="input-group-btn"><input class="btn btn-default" type="submit" value="删除" /></span></div></form></div></div><?} break;
	case'backup': check_login();require('backup.php'); break;
	case'setting': check_login();require('setting.php'); break;
	case'all': check_login();?><div class="panel panel-default"><div class="panel-heading"><h3 class="panel-title">全部</h3></div><div class="panel-body text-center"><table class="table table-hover"><thead><th>标签-值</th><th>操作</th></thead><tbody><? $ret=$kv->pkrget(prefix,100);while(true){foreach($ret as $k=>$v){$showTag=htmlspecialchars(substr($k,strlen(prefix)));$showValue=str_replace("\n",'',htmlspecialchars($v));echo'<tr><td>',$showTag,'<br><div class="tinywebdb-value">',$showValue,'</div></td><td><a href="?a=get&k=',urlencode_plus(substr($k,strlen(prefix))),'" class="btn btn-primary">查看</a><a href="?a=set&k=',urlencode_plus(substr($k,strlen(prefix))),'" class="btn btn-primary">修改</a><a href="?a=del&k=',urlencode_plus(substr($k,strlen(prefix))),'" onclick="return confirm(\'确认删除？\');" class="btn btn-danger">删除</a></td></tr>';}end($ret);$start_key=key($ret);$i=count($ret);if($i<100)break;$ret=$kv->pkrget(prefix,100,$start_key);}?></tbody></table></div></div><? break;
}
?><div style="width:100%;padding-left:10px;color:#bbb;">TinyWebDB MANAGE System By ColinTree @ <a target="_blank" style="color:#bbb;text-decoration:underline" href="http://www.colintree.cn">colintree.cn</a> VERSION: <? echo MANAGEversion;?></div>