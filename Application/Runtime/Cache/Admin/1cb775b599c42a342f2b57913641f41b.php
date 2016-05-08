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
				<div class="titlebt">客服列表</div>
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
                    <form method="post" id="search" name="search" action="<?php echo U(MODULE_NAME.'/Kefu/index');?>" autocomplete="off" >
                      <div style="float:left;">
                        客服名：
                        <input type="text" name="account" id="account" value="<?php echo ($condition['account']); ?>" class="input" />
                        真实姓名：
                        <input type="text" name="nickname" id="nickname" value="<?php echo ($condition['nickname']); ?>" class="input" />
                        手机：
                        <input type="text" name="telphone" id="telphone" value="<?php echo ($condition['telphone']); ?>" class="input" />
                        <input type="button" value="搜索" class="btn" onClick="javascript:search.submit();" />
                      </div>
                      <div style="float:right;">
                        <input type="button" value="添加客服" class="btn" onClick="javascript:window.location.href='<?php echo U(MODULE_NAME.'/Kefu/addKefu');?>'"/>
                      </div>
                    </form>
                </div>
				</td>
			</tr>
			<tr>
				<td>
				<table width="100%" border="0" cellpadding="0" cellspacing="0" class="content">
					<tr class="tr_title">
                        <td width="" class="main_td">ID</td>
                        <td width="*" class="main_td">客服名</td>
                        <td width="*" class="main_td">真实姓名</td>
                        <td width="*" class="main_td">手机</td>
                        <td width="" class="main_td">添加时间</td>
                        <td width="" class="main_td">操作</td>
					</tr>
					<?php if(is_array($kefudata)): $i = 0; $__LIST__ = $kefudata;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><tr class="tr_content">
                          <td class="main_td1" align="center"><?php echo ($item["id"]); ?></td>
                          <td class="main_td1" align="center"><?php echo ($item["account"]); ?></td>
                          <td class="main_td1" align="center"><?php echo ($item["nickname"]); ?></td>
                          <td class="main_td1" align="center"><?php echo ($item["email"]); ?></td>
                          <td class="main_td1" align="center"><?php echo (date("Y-m-d H:i:s",$item["create_time"])); ?></td>
                          <td class="main_td1" align="center">
                            <a href="<?php echo U(MODULE_NAME.'/Kefu/kefuEdit',array('user_id'=>$item['id']));?>">修改</a> / <a href="javascript:conFirm('<?php echo U(MODULE_NAME."/Kefu/kefuDelete",array("user_id"=>$item["id"]));?>','确定要删除吗?删除后将不可恢复');">删除</a>
                            / <a href="<?php echo U(MODULE_NAME.'/Kefu/kefuByCustomer',array('user_id'=>$item['id'],'pg'=>$item['page'],'name'=>$item['account']));?>">查看专属用户</a>
                          </td>
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
				官方网站：<?php echo (C("sysconfig.website")); ?></td>
			</tr>
		</table>
		</td>
	</tr>
</table>
<script>
function dele(url){
	parent.layer.confirm('确定要删除吗?删除后将不可恢复',function(index){
		location.href=url;
		parent.layer.close(index);
		});
	}
</script>