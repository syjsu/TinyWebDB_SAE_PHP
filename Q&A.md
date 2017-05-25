<br>

## TinyWebDB_SAE_PHP (TSP)
[首页](/) - [下载页](下载页) - [安装方法](安装方法) - [使用手册](使用手册) - [意见反馈](意见反馈)
  
***
  
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link href="http://cdn.bootcss.com/alertify.js/0.3.11/alertify.default.min.css" rel="stylesheet">
<link href="http://cdn.bootcss.com/alertify.js/0.3.11/alertify.core.min.css" rel="stylesheet">
<script src="http://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
<script src="http://cdn.bootcss.com/alertify.js/0.3.11/alertify.min.js"></script>
<script>
$(document).ready(function(){
	$('form').submit(function(){
		if($('textarea').val()==''){
			alertify.error('内容为空');
			return false;
		}
		$('textarea').prop('disabled',true);
		$('input[type=submit]').prop('disabled',true);
		$.ajax({async:true, url:'http://colintreeDB.applinzi.com/php/tsp_feedback.php', method:'post', data:{'text':$('textarea').val()}})
		.done(function(response){if(response=='1'){$('input[type=submit]').next().text('保存成功，感谢您的反馈！').prop('disabled',true);}else{alertify.error('保存失败');$('textarea').prop('disabled',false);$('input[type=submit]').prop('disabled',false);}})
		.fail(function(){alertify.error('保存失败');$('textarea').prop('disabled',false);$('input[type=submit]').prop('disabled',false);});
		return false;
	});
});
</script>
<form action="http://colintreeDB.applinzi.com/php/tsp_feedback.php" method="post">
	<textarea name="text" placeholder="畅所欲言吧" style="width:100%;height:200px;resize:none"></textarea>
	<br>
	<input type="submit" value="提交"/>&nbsp;&nbsp;<span></span>
</form>
  
***
  
By Colintree @ colintree.cn (Email: 502470184@qq.com \|\| colinycl123@gmail.com)

<br>