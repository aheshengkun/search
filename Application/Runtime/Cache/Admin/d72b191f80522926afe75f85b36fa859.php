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

<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td valign="top">
		<table width="100%" height="31" border="0" cellpadding="0"
			cellspacing="0" class="left_topbg" id="table2">
			<tr>
				<td height="31">
				<div class="titlebt">节点列表</div>
				</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#F7F8F9">
		<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
			<tr>
				<td valign="top" class="td_top">
				<div>
					<input name="addsystemvalue" type="button" value="添加节点" class="btn" onClick="javascript:window.location.href='<?php echo U(MODULE_NAME.'/Access/nodeAdd');?>'" />
				</div>
				</td>
			</tr>
			<tr>
				<td>
				<form method="post" id="list">
				<table width="100%" border="0" cellpadding="0" cellspacing="0" class="content">
					<tr class="tr_title">
						<td width="5%">ID</td>
						<td width="25%">节点结构</td>
						<td width="15%">名称</td>
						<td width="10%">显示名</td>
						<td width="10%">排序</td>
						<td width="20%">类型</td>
						<td width="10%">状态</td>
						<td width="20%">操作</td>
					</tr>
					<?php if(is_array($node)): foreach($node as $key=>$v): ?><tr class="tr_content">
						<td><?php echo ($v["id"]); ?></td>
						<td align="left"><?php echo ($v["fullname"]); ?></td>
						<td><?php echo ($v["name"]); ?></td>
						<td><?php echo ($v["title"]); ?></td>
						<td><?php echo ($v["sort"]); ?></td>
						<td><?php echo ($v["level"]); ?></td>
						<td><?php echo ($v["statusTxt"]); ?></td>
						<td align="center">
						<div style="width: 123px">
                          <a href="<?php echo U(MODULE_NAME.'/Access/nodeEdit',array('nid'=>$v['id']));?>">编辑</a> / 
                          <a href="javascript:void(0);" onClick="conFirm('<?php echo U(MODULE_NAME."/Access/delNode",array("nid"=>$v["id"]));?>','确定要删除吗?删除后将不可恢复');">删除</a>
                        </div>
						</td>
					</tr><?php endforeach; endif; ?>
				</table>
				</form>
				</td>
			</tr>
			<tr>
				<td height="20">
				<div class="page"><?php echo ($page); ?></div>
				</td>
			</tr>
			<tr>
				<td height="40"></td>
			</tr>
			<tr>
				<td width="51%" class="left_txt">
                <img src="/searchsys/Public/admin/images/icon-mail2.gif" width="16" height="11">
				客户服务邮箱：<?php echo (C("sysconfig.services_email")); ?><br>
				<img src="/searchsys/Public/admin/images/icon-phone.gif" width="17" height="14">
				官方网站：<?php echo (C("sysconfig.website")); ?></td>
			</tr>
		</table>
		</td>
	</tr>
</table>