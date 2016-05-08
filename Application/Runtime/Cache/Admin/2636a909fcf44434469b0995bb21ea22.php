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
		<table width="100%" height="31" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td height="31">
				<div class="titlebt">操作日志</div>
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
                    <div style="float:left;">
                      <form method="post" id="search" name="search" action="<?php echo U(MODULE_NAME.'/System/sysLog');?>" autocomplete="off">
                      	真实姓名：
                        <input type="text" name="nickname" id="name" value="<?php echo ($search["nickname"]); ?>" class="input" style="width:120px;" />
						选择搜索条件：
                        <select name="searchoption" class="searchoption">
							<option value="">请选择搜索条件</option>
							<option value="query" <?php if($search['searchoption'] == 'query' ): ?>selected="selected"<?php endif; ?> >操作动作</option>
							<option value="url" <?php if($search['searchoption'] == 'url' ): ?>selected="selected"<?php endif; ?> >操作地址</option>
							<option value="mess" <?php if($search['searchoption'] == 'mess' ): ?>selected="selected"<?php endif; ?> >操作备注</option>
							<option value="addip" <?php if($search['searchoption'] == 'addip' ): ?>selected="selected"<?php endif; ?> >IP地址</option>
						</select>
						<input type="text" name="searchvalue" class="input" style="width:400px;" value="<?php echo ($search['searchvalue']); ?>" />
                        
                        <input type="button" value="搜索" class="btn" onclick="javascript:search.submit();" />
                      </form>
                    </div>
                </div>
				</td>
			</tr>
			<tr>
				<td>
                    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="content" style="table-layout:fixed;word-break:break-all">
                        <tr class="tr_title">
                            <td width="6%">ID</td>
                            <!-- <td width="5%" class="main_td">用户名</td> -->
                            <td width="6%">真实姓名</td>
                            <td width="16%">操作动作</td>
                            <td width="*">操作地址</td>
                            <td width="*">操作备注</th>
                            <td width="4%">状态</th>
                            <td width="7%">添加时间</th>
                            <td width="6%">IP</th>
                        </tr>
                        <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><tr class="tr_content">
                            <td align="center"><?php echo ($item["log_id"]); ?></td>
                            <!-- <td align="center"><?php echo ($item["account"]); ?></td> -->
                            <td align="center"><?php echo ($item["nickname"]); ?></td>
                            <td align="left"><?php echo ($item["query"]); ?></td>
                            <td align="left"><?php echo ($item["url"]); ?></td>
                            <td align="left"><?php echo ($item["mess"]); ?></td>
                            <td align="center"><?php if(($item["result"]) == "1"): ?><span class="green">成功</span><?php else: ?><span class="red">失败</span><?php endif; ?></td>
                            <td align="center" ><?php echo (date("Y-m-d H:i:s",$item["addtime"])); ?></td>
                            <td align="center" ><?php echo ($item["addip"]); ?></td>
                        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                    </table>
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
				<td class="left_txt">
                <img src="/searchsys/Public/admin/images/icon-mail2.gif" width="16" height="11">
				客户服务邮箱：<?php echo (C("sysconfig.services_email")); ?><br>
				<img src="/searchsys/Public/admin/images/icon-phone.gif" width="17" height="14">
				官方网站：<?php echo (C("sysconfig.website")); ?>
                </td>
			</tr>
		</table>
		</td>
	</tr>
</table>