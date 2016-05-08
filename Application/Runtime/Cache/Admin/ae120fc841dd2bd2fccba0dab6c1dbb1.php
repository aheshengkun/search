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
				<div class="titlebt">分类列表</div>
				</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#F7F8F9">
		<form method="post" id="list" action="<?php echo U(MODULE_NAME.'/Article/typeSort');?>">
		<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
			<tr>
				<td valign="top" class="td_top">
				<div>
				<input type="button" value="添加分类" class="btn <?php if(($search["vip_status"]) == "2"): ?>buttonOK<?php endif; ?>" onClick="location.href='<?php echo U(MODULE_NAME."/Article/addType");?>'" />
				</div>
				</td>
			</tr>
			<tr>
				<td>
				<table width="100%" border="0" cellpadding="0" cellspacing="0" class="content">
					<tr class="tr_title">
						<td width="10%">ID</td>
						<td width="10%">排序</td>
						<td width="25%">分类名称</td>
						<td width="25%">名称缩写</td>
						<td width="10%">菜单显示</td>
						<td width="10%">分类类型</td>
						<td width="20%">操作</td>
					</tr>
					<?php if(is_array($terms)): foreach($terms as $key=>$v): ?><tr class="tr_content">
						<td><?php echo ($v["term_id"]); ?></td>
						<td><input type="text" size="4" name="<?php echo ($v["term_id"]); ?>" value="<?php echo ($v["sort"]); ?>"></td>
						<td><?php echo ($v["fullname"]); ?></td>
						<td><?php echo ($v["slug"]); ?></td>
						<td>
							<?php if($v['view'] == 0): ?><span class="red">不显示</span>
							<?php elseif($v['view'] == 1): ?>
								<span class="green">显示</span>
							<?php else: ?>
								<span class="red">--</span><?php endif; ?>
						</td>
						<td>
							<?php if($v['is_page'] == 0): ?><span class="green">非单页</span><?php elseif($v['is_page'] == 1): ?><span class="red">单页</span><?php elseif($v['is_page'] == 2): ?><span class="blue">外部链接</span><?php else: ?><span class="red">错误类型</span><?php endif; ?>
						</td>
						<td align="center">
						<div style="width: 123px">
                          <a href="<?php echo U(MODULE_NAME.'/Article/typeEdit',array('id'=>$v['term_id']),'');?>">修改</a> / 
                          <!--<a href="javascript:void(0);" onClick="javascript:if(confirm('确定要删除吗?删除后将不可恢复')) location.href='<?php echo U(MODULE_NAME.'/Article/delete',array('id'=>$v['term_id']),'');?>'">删除</a>-->
                          <a href="javascript:void(0);" onClick="conFirm('<?php echo U(MODULE_NAME."/Article/delete",array("id"=>$v["term_id"]));?>','确定要删除吗?删除后将不可恢复');">删除</a>
                        </div>
						</td>
					</tr><?php endforeach; endif; ?>
				</table>
				</td>
			</tr>
			<tr>
				<td height="10"></td>
			</tr>
			<tr>
              <td colspan="5">
                <div class="module_submit border_b" >
                    <input type="submit" class="btn" value="排序" />
                </div>
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
		</form>
		</td>
	</tr>
</table>