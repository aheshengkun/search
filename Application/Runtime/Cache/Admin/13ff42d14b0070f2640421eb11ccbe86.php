<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<title>后台右侧主体内容区公共头部，</title>
	<link type="text/css" rel="stylesheet" href="/searchsys/Public/admin/css/skin.css"  />
	<script language="javascript" src="/searchsys/Public/admin/js/jquery-1.8.3.min.js"></script>
	<script language="javascript" src="/searchsys/Public/admin/layer/layer.min.js"></script>
	<?php if(CONTROLLER_NAME != 'Comment' && CONTROLLER_NAME != 'ArticleContent'): ?><script language="javascript" src="/searchsys/Public/admin/js/checkboxes.js"></script><?php endif; ?>
	<script language="javascript" src="/searchsys/Public/admin/js/mdyDialog.js"></script>
</head>
<body>
<link rel='stylesheet' type='text/css' href='/searchsys/Public/admin/css/style.css'>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td valign="top">
		<table width="100%" height="31" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td height="31">
				<div class="titlebt">Searchd服务状态</div>
				</td>
			</tr>
		</table>
		</td>
	</tr>
</table>   
    <div id="system" class="list">
		<ul>
        <?php if($result == 1): if(is_array($status)): foreach($status as $key=>$v): ?><li><?php echo ($v[0]); ?>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo ($v[1]); ?></li><?php endforeach; endif; ?>	
        <?php else: ?>
        	<li>searchd服务没有启动</li><?php endif; ?>
		</ul>
	</div>