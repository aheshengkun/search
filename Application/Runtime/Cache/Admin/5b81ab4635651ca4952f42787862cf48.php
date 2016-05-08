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
				<td height="31"><div class="titlebt3">用户列表</div></td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#F7F8F9">
		<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
			<tr>
				<td valign="top" class="td_top">
				<div><input name="addsystemvalue" type="button" value="添加管理者" class="btn" onClick="javascript:window.location.href='<?php echo U(MODULE_NAME.'/Access/userAdd');?>'" /></div>
				</td>
			</tr>
			<tr>
				<td>
				<table width="100%" border="0" cellpadding="0" cellspacing="0" class="content">
					<tr class="tr_title">
                      <td width="5%" class="main_td">ID</td>
                      <td width="*" class="main_td">管理员名</td>
                      <td width="" class="main_td">添加时间</td>
                      <td width="" class="main_td">状态</td>
                      <td width="" class="main_td">管理员类型</td>
                      <td width="" class="main_td">登录次数</td>
                      <td width="" class="main_td">操作</td>
					</tr>
					<?php if(is_array($admin)): foreach($admin as $key=>$v): ?><tr class="tr_content">
                        <td class="main_td1" align="center"><?php echo ($v["id"]); ?></td>
                        <td class="main_td1" align="center"><?php echo ($v["account"]); ?></td>
                        <td class="main_td1" align="center"><?php echo (date('Y-m-d H:i:s',$v["create_time"])); ?></td>
                        <td class="main_td1" align="center"><?php if($v["status"]): ?>开启<?php else: ?>关闭<?php endif; ?></td>
                        <td class="main_td1" align="center">
							<?php if($v["account"] == C("RBAC_SUPERADMIN")): ?>超级管理员
							<?php else: echo ($v["name"]); ?>(<?php echo ($v["remark"]); ?>)<?php endif; ?>
                        </td>
                        <td class="main_td1" align="center"><?php echo ($v["login_count"]); ?></td>
                        <td class="main_td1" align="center">
                        
                        <?php if($v["account"] == C("RBAC_SUPERADMIN")): ?>--
						<?php else: ?>
							<a href="<?php echo U(MODULE_NAME.'/Access/userEdit',array('id'=>$v['id']));?>">编辑</a>
                          	<?php if(($search["type_id"]) != "1"): ?>/<a href="javascript:void(0);" onClick="conFirm('<?php echo U(MODULE_NAME."/Access/delUser",array("id"=>$v["id"]));?>','确定要删除吗?删除后将不可恢复');">删除</a><?php endif; endif; ?>
                        </td>
					</tr><?php endforeach; endif; ?>
				</table>
				</td>
			</tr>
			<tr>
				<td height="20">
				<div class="page"><?php echo ($page); ?></div>
				</td>
			</tr>
			<!--<tr>
				<td height="20"><a href="/searchsys/Admin/Access/down/<?php echo ($down); ?>" class="downxls"></a></td>
			</tr>-->
			<tr>
				<td height="40"></td>
			</tr>
		</table>
		</td>
	</tr>
</table>