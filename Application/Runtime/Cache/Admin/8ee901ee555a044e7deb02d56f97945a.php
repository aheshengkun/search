<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>后台管理首页</title>
<link rel='stylesheet' type='text/css' href='/searchsys/Public/admin/css/style.css'>
</head>
<body width="100%">
<div class="sssmainbox">

	<div id="Profile" class="list">
		<h1><b>个人信息</b><span>Profile&nbsp; Info</span></h1>
		<ul>
			<li><span>会员名:</span><?php echo ($info['AdminName']); ?></li>
			<li><span>所属会员组:</span><?php echo ($info["rolename"]); ?></li>
			<li><span>最后登陆时间:</span><?php echo (date('Y-m-d H:i:s',$info["login_time"])); ?></li>
			<li><span>最后登陆IP:</span><?php echo ($info["loginip"]); ?></li>
			<li><span>登陆次数:</span><?php echo ($info["loginnum"]); ?>次</li>
		</ul>
	</div>
	 
	<div id="system" class="list">
		<h1><b>系统信息</b><span>System&nbsp; Info</span></h1>
		<ul>
			<li><span>运行环境:</span><?php echo ($info["os"]); ?> <?php echo ($info["SOFTWARE"]); ?></li>
			<li><span>PHP运行方式:</span><?php echo ($info["phpmode"]); ?></li>
			<li><span>MYSQL版本:</span><?php echo ($info["MysqlVerSion"]); ?></li>
			<li><span>上传附件限制:</span><?php echo ($info["upload_max_filesize"]); ?></li>
		</ul>
	</div>
	 
</div>
</body>
</html>