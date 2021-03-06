<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link type="text/css" rel="stylesheet" href="/searchsys/Public/admin/css/style.css" />
<script type="text/javascript" src="/searchsys/Public/admin/js/jquery-1.8.3.min.js"></script>
<script src="/searchsys/Public/admin/layer/layer.min.js"></script>
<script type="text/javascript" src="/searchsys/Public/admin/js/jquery.artDialog.js"></script>
<script type="text/javascript" src="/searchsys/Public/admin/js/iframeTools.js"></script>
<title>欢迎使Search后台管理系统</title>
</head>
<body style="background:#E2E9EA">
<div id="header" class="header">
	<div class="logo">
		<a href="<?php echo U('Home/Index/index');?>" target="_blank"><img src="/searchsys/Public/admin/images/logo.png" width="200" ></a>
	</div>
	<div class="nav nav_1">
        <a href="<?php echo U('Home/NewList/index');?>" target="_blank">帮助中心</a><i>|</i> &nbsp;&nbsp;
	</div>
	<div class="nav nav_2">
		欢迎你！<i>|</i> [<?php echo ($username); ?>]  <i>|</i>
        [<a href="<?php echo U(MODULE_NAME.'/Index/logout');?>" target="_top">退出</a>]<i>|</i>
        <a href="<?php echo U('Home/Index/index');?>" target="_blank">网站首页</a>
	</div>
	<div class="topmenu">
		<ul>
			<li id="menu_1"><span><a href="javascript:void(0);" onclick="sethighlight(1);" class="active" title="后台首页">后台首页</a></span></li>
			<li id="menu_2"><span><a href="javascript:void(0);" onclick="sethighlight(2);" title="搜索模块">搜索模块</a></span></li>
			<li id="menu_3"><span><a href="javascript:void(0);" onclick="sethighlight(3);" title="权限管理">权限管理</a></span></li>
            <li id="menu_4"><span><a href="javascript:void(0);" onclick="sethighlight(4);">文章模块</a></span></li>
            <li id="menu_5"><span><a href="javascript:void(0);" onclick="sethighlight(5);">工具模块</a></span></li>
		</ul>
	</div>
</div>
<div id="Main_content">
	<div id="MainBox">
		<div class="main_box">
			<iframe name="main" id="Main" src="<?php echo U(MODULE_NAME.'/Public/main');?>" frameborder="false" scrolling="auto" width="100%" height="auto" allowtransparency="true">
            
			</iframe>
		</div>
	</div>
	<div id="leftMenuBox">
		<div id="leftMenu">
			<dl id="nav_1">
				<dt>后台首页</dt>
				<dd id="nav_11"><span onclick="javascript:gourl('11','<?php echo U(MODULE_NAME.'/Public/main');?>')"><a href="<?php echo U(MODULE_NAME.'/Public/main');?>" target="main">后台首页</a></span></dd>
			</dl>
			<dl id="nav_2">
				<dt>搜索模块</dt>
                <dd id="nav_21"><span onclick="javascript:gourl('21','<?php echo U(MODULE_NAME.'/SearchSphinx/Index');?>')"><a href="<?php echo U(MODULE_NAME.'/SearchSphinx/Index');?>" target="main">Searchd服务状态</a></span></dd>
				<dd id="nav_22"><span onclick="javascript:gourl('22','<?php echo U(MODULE_NAME.'/SearchSphinx/participle');?>')"><a href="<?php echo U(MODULE_NAME.'/SearchSphinx/participle');?>" target="main">分词测试</a></span></dd>
            	<dd id="nav_23"><span onclick="javascript:gourl('23','<?php echo U(MODULE_NAME.'/System/index',array('type'=>'搜索设置'));?>')"><a href="<?php echo U(MODULE_NAME.'/System/index',array('type'=>'搜索设置'));?>" target="main">搜索配置</a></span></dd>
				<dd id="nav_24"><span onclick="javascript:gourl('24','<?php echo U(MODULE_NAME.'/SearchArticle/index');?>');"><a href="<?php echo U(MODULE_NAME.'/SearchArticle/index');?>" target="main">搜索文章</a></span></dd>
				<dd id="nav_25"><span onclick="javascript:gourl('25','<?php echo U(MODULE_NAME.'/SearchArticle/addArticle');?>');"><a href="<?php echo U(MODULE_NAME.'/SearchArticle/addArticle');?>" target="main">添加搜索文章</a></span></dd>
            </dl>
			<dl id="nav_3">
				<dt>权限管理</dt>
				<dd id="nav_31"><span onclick="javascript:gourl('31','<?php echo U(MODULE_NAME.'/Access/userList');?>')"><a href="<?php echo U(MODULE_NAME.'/Access/userList');?>" target="main">管理人员</a></span></dd>
				<dd id="nav_32"><span onclick="javascript:gourl('32','<?php echo U(MODULE_NAME.'/Access/roleList');?>')"><a href="<?php echo U(MODULE_NAME.'/Access/roleList');?>" target="main">角色列表</a></span></dd>
                <dd id="nav_33"><span onclick="javascript:gourl('33','<?php echo U(MODULE_NAME.'/Access/nodeList');?>')"><a href="<?php echo U(MODULE_NAME.'/Access/nodeList');?>" target="main">节点列表</a></span></dd>
                <dd id="nav_34"><span onclick="javascript:gourl('34','<?php echo U(MODULE_NAME.'/Access/userAdd');?>')"><a href="<?php echo U(MODULE_NAME.'/Access/userAdd');?>" target="main">添加用户</a></span></dd>
                <dd id="nav_35"><span onclick="javascript:gourl('35','<?php echo U(MODULE_NAME.'/Access/roleAdd');?>')"><a href="<?php echo U(MODULE_NAME.'/Access/roleAdd');?>" target="main">添加角色</a></span></dd>
                <dd id="nav_36"><span onclick="javascript:gourl('36','<?php echo U(MODULE_NAME.'/Access/nodeAdd');?>')"><a href="<?php echo U(MODULE_NAME.'/Access/nodeAdd');?>" target="main">添加节点</a></span></dd>
			</dl>
			<dl id="nav_4">
				<dt>文章模块</dt>
				<dd id="nav_41"><span onclick="javascript:gourl('41','<?php echo U(MODULE_NAME.'/Article/type');?>')"><a href="<?php echo U(MODULE_NAME.'/Article/type');?>" target="main">文章分类</a></span></dd>
                <dd id="nav_42"><span onclick="javascript:gourl('42','<?php echo U(MODULE_NAME.'/ArticleContent/index');?>');"><a href="<?php echo U(MODULE_NAME.'/ArticleContent/index');?>" target="main">文章内容</a></span></dd>
				<dd id="nav_43"><span onclick="javascript:gourl('43','<?php echo U(MODULE_NAME.'/ArticleContent/addArticle');?>');"><a href="<?php echo U(MODULE_NAME.'/ArticleContent/addArticle');?>" target="main">添加文章</a></span></dd>
                <dd id="nav_44"><span onclick="javascript:gourl('44','<?php echo U(MODULE_NAME.'/ArticleContent/index');?>?post_type=page');"><a href="<?php echo U(MODULE_NAME.'/ArticleContent/index',array('post_type'=>'page'));?>" target="main">单页管理</a></span></dd>
            </dl>
            <dl id="nav_5">
				<dt>工具模块</dt>
                <dd id="nav_51"><span onclick="javascript:gourl('51','<?php echo U(MODULE_NAME.'/System/sysLog');?>')"><a href="<?php echo U(MODULE_NAME.'/System/sysLog');?>" target="main">后台操作日志</a></span></dd>
			</dl>
		</div>
		<div id="Main_drop">
			<a href="javascript:toggleMenu(1);" class="on"><img src="/searchsys/Public/admin/images/admin_barclose.gif" width="11" height="60" border="0"/></a><a href="javascript:toggleMenu(0);" class="off" style="display:none;"><img src="/searchsys/Public/admin/images/admin_baropen.gif" width="11" height="60" border="0"/></a>
		</div>
	</div>
</div>
<!-- <div id="footer" class="footer">
	Powered by <a href="http://www.weixingkeji.cn" target="_blank">广发财富</a>&nbsp;v1.0 Copyright 2012-2014  <span id="run"></span>
</div> -->
<script language="JavaScript">if(!Array.prototype.map)
Array.prototype.map = function(fn,scope) {
  var result = [],ri = 0;
  for (var i = 0,n = this.length; i < n; i++){
	if(i in this){
	  result[ri++]  = fn.call(scope ,this[i],i,this);
	}
  }
return result;
};
var getWindowWH = function(){
	  return ["Height","Width"].map(function(name){
	  return window["inner"+name] ||
		document.compatMode === "CSS1Compat" && document.documentElement[ "client" + name ] || document.body[ "client" + name ]
	});
}
window.onload = function (){
	if(!+"\v1" && !document.querySelector) { //IE6 IE7
	 document.body.onresize = resize;
	} else { 
	  window.onresize = resize;
	}
	function resize() {
		wSize();
		return false;
	}
}
function wSize(){
	var str=getWindowWH();
	var strs= new Array();
	strs=str.toString().split(","); //字符串分割
	var h = strs[0] - 95-30;
	$('#leftMenu').height(h);
	$('#Main').height(h); 
}
wSize();
function sethighlight(n) {
	 $('.topmenu li span').removeClass('current');
	 $('#menu_'+n+' span').addClass('current');
	 $('#leftMenu dl').hide();
	 $('#nav_'+n).show();
	 $('#leftMenu dl dd').removeClass('on');
	 $('#nav_'+n+' dd').eq(0).addClass('on');
	 url = $('#nav_'+n+' dd a').eq(0).attr('href');
	 window.main.location.href= url;
}
sethighlight(1);
function gourl(n,url){
	$('#leftMenu dl dd').removeClass('on');
	$('#nav_'+n).addClass('on');
	window.main.location.href=url;
}
function gocacheurl(){
	mainurl = window.main.location.href;
	window.main.location.href= "/index.php?g=Admin&m=Index&a=cache&forward="+encodeURIComponent(mainurl);
}
function toggleMenu(doit){
	if(doit==1){
		$('#Main_drop a.on').hide();
		$('#Main_drop a.off').show();
		$('#MainBox .main_box').css('margin-left','24px');
		$('#leftMenu').hide();
	}else{
		$('#Main_drop a.off').hide();
		$('#Main_drop a.on').show();
		$('#leftMenu').show();
		$('#MainBox .main_box').css('margin-left','224px');
	}
}	
</script>
</body>
</html>