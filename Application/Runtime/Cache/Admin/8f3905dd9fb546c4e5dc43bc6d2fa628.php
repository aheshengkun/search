<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
  <title>弹窗内容页面</title>
  <link type="text/css" rel="stylesheet" href="/searchsys/Public/admin/css/skin.css"  />
  <script language="javascript" src="/searchsys/Public/admin/js/jquery-1.8.3.min.js"></script>
  <style>
  	body,form,table{
  		margin: 0;padding: 0;background: #fff;
  		font: 14px 'Microsoft Yahei',arial,SimSun,Verdana,sans-serif;
  	}
  	.content td{
    	background: #fff;
  	}
</style>
</head>
<body>

<form name="form1" id="ajaxForm" method="post" action="<?php echo U(MODULE_NAME.'/System/edit');?>" autocomplete="off">
  <table width="100%" border="0" cellpadding="0" cellspacing="0" class="content_wd">
      <tr>
        <td align="right" width="95">参数说明：</td>
        <td align="left">
          <input type="text" class="input" name="name" value="<?php echo ($data["name"]); ?>" />
        </td>
      </tr>
      <tr>
        <td align="right">变量名：</td>
        <td align="left">
          <input type="text" class="input" name="nid" value="<?php echo ($data["nid"]); ?>"/>&nbsp;&nbsp;请在前面加con_
        </td>
      </tr>
      <tr>
        <td align="right">所属分类：</td>
        <td align="left">
          <select name="type" class="searchoption">
            <?php if(is_array($typelist)): $i = 0; $__LIST__ = $typelist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><option value="<?php echo ($v["type"]); ?>" <?php if(($data["type"] == $v['type'])): ?>selected<?php endif; ?> ><?php echo ($v["type"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
          </select>
        </td>
      </tr>
      <tr>
        <td align="right">参数类型：</td>
        <td align="left">
          <input type="radio" value="string" name="style" checked="checked" onclick="change(0)" <?php if(($data["style"]) == "string"): ?>checked="checked"<?php endif; ?> /> 文本/数字 
          <input type="radio" value="bool" name="style" onclick="change(1)" <?php if(($data["style"]) == "bool"): ?>checked="checked"<?php endif; ?> /> 布尔（Y/N）
          <input type="radio" value="text" name="style" onclick="change(0)" <?php if(($data["style"]) == "text"): ?>checked="checked"<?php endif; ?> /> 多行
          <div style="display:none"><input type="radio" value="pic" name="style" onclick="change(0)" <?php if(($data["style"]) == "pic"): ?>checked="checked"<?php endif; ?> />图片</div>
        </td>
      </tr>
      <tr>
        <td align="right">参数值：</td>
        <td align="left">
          <div id="text_v" <?php if(($data["style"]) == "bool"): ?>style="display:none"<?php endif; ?> align="left" >
            <?php if(($data["style"] == 'string') OR ($data["style"] == 'pic') OR ($data["style"] == 'bool') ): ?><input type="text" class="input" align="absmiddle" name="value1" size="38" value="<?php echo (stripslashes($data["value"])); ?>"/><?php endif; ?>
            <?php if(($data["style"]) == "text"): ?><textarea name="value1" style="width:280px;height:46px;font-size:12px;"><?php echo (stripslashes($data["value"])); ?></textarea><?php endif; ?>
          </div>
          <div id="option_v" <?php if(($data["style"] == 'string') OR ($data["style"] == 'text') OR ($data["style"] == 'pic')): ?>style="display:none"<?php endif; ?> >
            <input type="radio" value="1" name="value2" checked="checked" <?php if(($data["value"]) == "1"): ?>checked="checked"<?php endif; ?> /> 是
            <input type="radio" value="0" name="value2" <?php if(($data["value"]) == "0"): ?>checked="checked"<?php endif; ?> /> 否 
          </div>
          <div style="clear:both;"></div>
        </td>
      </tr>
      <tr>
        <td align="right">状态：</td>
        <td align="left">
          <input type="radio" value="0" name="status" checked="checked" <?php if(($sys["value"]) == "0"): ?>checked="checked"<?php endif; ?> /> 系统 
          <input type="radio" value="1" name="status" <?php if(($sys["status"] == 1) OR ($sys["status"] == '')): ?>checked="checked"<?php endif; ?> /> 自定义
        </td>
      </tr>
      <tr>
        <div  style="margin-top:10px\9;height:0;line-height:0;">
          <td align="center" colspan="2" height="0">
            <input type="hidden" value="<?php echo ($data["id"]); ?>" name="id" />
            <!-- <input type="submit" id="sub" class="btn" value="提交" />  -->
            <input type="button" id="sub" class="btn" value="提交" />
            <input type="reset" class="btn" value="重置" />
          </td>
        </div>
      </tr>
  </table>           
</form>
<script type="text/javascript">
  function change(val){
    if (val==0){
  	  document.getElementById("text_v").style.display ="";
  	  document.getElementById("option_v").style.display ="none";
    }else{
  	  document.getElementById("text_v").style.display ="none";
  	  document.getElementById("option_v").style.display ="";
    }
  }

  var ajaxCallUrl = "<?php echo U(MODULE_NAME.'/System/edit');?>";
  var index = parent.layer.getFrameIndex(window.name);    //当前弹窗层的索引
  $('#sub').on('click',function(){
    var name = $("input[name='name']");
    var nid = $("input[name='nid']");
    if(name.val() == ''){
      name.focus();
      parent.layer.msg('参数说明不能为空');
      return false;
    }
    if(nid.val() == ''){
      nid.focus();
      parent.layer.msg('变量名不能为空');
      return false;
    }

    //ajax提交
    $.ajax({
      type: 'POST',
      async: false,
      url:ajaxCallUrl,
      data:$('#ajaxForm').serialize(),// 要提交的表单 
      success: function(msg) {
        //注意ajax提交后由父页面层被彻底关闭后执行的回调函数end刷新页面。 
        if(msg.status == 1){
          //提交成功
          parent.layer.msg(msg.info,1,{type:9});
          parent.layer.close(index);
        }else{
          //提交失败,则不关闭弹窗
          parent.layer.msg(msg.info,1);
          // parent.layer.close(index);
        }
      }
    });
  });
</script>