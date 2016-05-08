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
		<table width="100%" height="31" border="0" cellpadding="0" cellspacing="0" class="left_topbg" id="table2">
			<tr>
				<td height="50">
				<div class="titlebt"><?php echo ($configdata[0]['type']); ?></div>
				</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#F7F8F9">
		<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
			<tr>
				<td valign="top" colspan="4">
                  <div style="float: left; margin: 15px 0 5px 0;padding-bottom:5px;">
                    <input type="button" value="添加系统参数" class="btn" onClick="mdyDialog('<?php echo U(MODULE_NAME."/System/Add");?>','添加系统参数','420px','380px');" />
                  </div>
                </td>
			</tr>
			<tr>
				<td colspan="4">
				<table width="100%" border="0" cellpadding="0" cellspacing="0" class="content">
					<form method="post" action="<?php echo U(MODULE_NAME.'/System/save');?>" id="submitForm">
                    <input type="hidden" name="list" id="list" value="">
					<tr class="tr_title">
						<td width="25%">参数说明</td>
						<td width="55%">参数值</td>
						<td width="15%">键名</td>
                        <td width="5%">操作</td>
					</tr>
					<?php if(is_array($configdata)): $i = 0; $__LIST__ = $configdata;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><tr class="tr_content">
						<td class="td_right"><?php echo ($v["name"]); ?>:</td>
						<td class="td_left">
                          <?php switch($v["style"]): case "string": ?><input type="text" name="<?php echo ($v["nid"]); ?>" value="<?php echo ($v["value"]); ?>" style="margin:0 6px;width:97%;" class="input"><?php break;?>
                              <?php case "text": ?><textarea name="<?php echo ($v["nid"]); ?>" style="margin:0 6px;width:97%;height:80px;"><?php echo (stripslashes($v["value"])); ?></textarea><?php break;?>
                              <?php case "bool": ?><input type="radio" name="<?php echo ($v["nid"]); ?>" value="1" <?php if(($v["value"]) == "1"): ?>checked="checked"<?php endif; ?> /> 是
                              <input type="radio" name="<?php echo ($v["nid"]); ?>" value="0" <?php if(($v["value"]) == "0"): ?>checked="checked"<?php endif; ?> /> 否<?php break;?>
                              <?php default: ?>
                              <input  name="<?php echo ($v["nid"]); ?>" value="<?php echo (br2nl($v["value"])); ?>" size="15"><?php endswitch;?>
    
                        </td>
						<td><?php echo ($v["nid"]); ?></td>
                        <td>
                        <?php if(($v["status"]) == "1"): ?><a href="javascript:void(0)" onClick="mdyDialog('<?php echo U("Admin/System/Edit",array("id"=>$v["id"]));?>','修改：<?php echo ($v["name"]); ?>','420px','380px');">修改</a>
                        <?php else: ?>--<?php endif; ?>
                        </td>
					</tr><?php endforeach; endif; else: echo "" ;endif; ?>
					<tr>
						<td colspan="4" align="center">
							<input name="typeneme" type="hidden"  value="<?php echo ($configdata[0]['type']); ?>">
                            <input type="button" name="btn" class="btn"  value="提交配置" onClick="conFirmSubmit('#submitForm','确定修改数据？');" />
						</td>
					</tr>
					</form>
				</table>
					<script language="javascript">
                    function getlist(){
                        var gn=document.getElementsByTagName('input');
                        var ei=document.getElementById('list');
                        for(i=0;i<gn.length;i++){
                            if(/^con_/.test(gn.item(i).name)){
                                if(ei.value==''){
                                    ei.value=gn.item(i).name;
                                }else{
                                    ei.value=ei.value+','+gn.item(i).name;
                                }
                            }
                        }
                        gn=document.getElementsByTagName('textarea');
                        for(i=0;i<gn.length;i++){
                            if(/^con_/.test(gn.item(i).name)){
                                if(ei.value==''){
                                    ei.value=gn.item(i).name;
                                }else{
                                    ei.value=ei.value+','+gn.item(i).name;
                                }
                            }
                        }
                    }
                    getlist();
                    </script>
				</td>
			</tr>
			<tr><td height="40"></td></tr>
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