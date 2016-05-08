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

<style type="text/css">
  .module_border {
    /*border-bottom: 1px solid #CCCCCC;*/
    height: 30px;
    line-height: 27px;
    text-align: left;
    width: 100%;
  }
  .module_border .l {
    float: left;
    padding: 4px 5px 0 0;
    text-align: right;
    width: 130px;
  }
  .module_border .c {
    float: left;
    padding: 4px 5px 3px 10px;
  }
  .main_right {
    padding: 5px 2px 0 5px;
    width: 82%;
  }
  .module_border .w {
    float: left;
    padding: 4px 5px 0 0;
    text-align: right;
    width: 200px;
  }
  .module_submit {
    /*border-bottom: 1px solid #CCCCCC;*/
    padding-bottom:6px;
    text-align: center;
  }
  .module_submit input{
  	cursor:pointer;
  }
</style>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
<form name="form_user" method="post" action="<?php echo U(MODULE_NAME.'/Access/roleAdd');?>" autocomplete="off" onSubmit="return check_form();">
	<tr>
    <td valign="top">
      <table width="100%" height="31" border="0" cellpadding="0" cellspacing="0">
          <tr>
              <td height="31">
               <div class="titlebt">添加角色</div>
              </td>
          </tr>
      </table>
    </td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#F7F8F9">
      <table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
              <td class="td_top" style="padding-left:20px;">
                <table width="70%" border="0" cellpadding="0" cellspacing="0" class="content_fm">
                    <tr>
                      <td>
                        <div class="module_border">
                            <div class="l">用户组名：</div>
                            <div class="c">
                                <input type="text" name="name" value="" class="input" />
                                <span class="red">*</span>
                            </div>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="module_border">
                            <div class="l">父级组ID：</div>
                            <div class="c">
                                <select name="pid" class="searchoption"><?php echo ($role["pidOption"]); ?></select>
                            </div>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="module_border">
                            <div class="l">状态：</div>
                            <div class="c">
                               <input type="radio" name="status" value="1" checked="checked">启用 
                                <input type="radio" name="status" value="0">禁用</td>
                            </div>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="module_border">
                            <div class="l">描述：</div>
                            <div class="c">
                                <input type="text" name="remark" value="" class="input_w" style="width:300px"/>
                            </div>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="module_border">
                          <div class="l">&nbsp;</div>
                          <div class="c">
                            <input type="submit" class="btn" value="确认提交" />
                            <input type="reset" class="btn" value="重置表单" />
                          </div>
                        </div>
                      </td>
                    </tr>
                </table>
              </td>
          </tr>
          <tr>
              <td height="40"></td>
          </tr>
      </table>
		</td>
	</tr> 
  </form>
 </table>
<script type="text/javascript">
function check_form(){
  var username  = $("input[name='name']");
  //用于错误提示信息
  var errorMsg = '';

  //用户名检查
  if(username.val().length < 2 || username.val().length > 15){
    errorMsg += '用户组名在2到15个字符之间<br />';
  }else if(isChinaOrNumbOrLett(username.val()) == false){
    errorMsg += '用户组名由汉字、字母、数字、下划线组成<br />';
  }

  if(errorMsg.length > 0){
    parent:parent.$.layer({
      shade: [0.3, '#000'],
      area: ['auto','auto'],
      shadeClose: true,       //点击对话框外部即关闭
      dialog: {
        msg: errorMsg,
        btns: 1,                    
        type: 8,
        btn: ['确定'],
        no: function(){

        }
      }
    });
    return false;
  }else{  
    return true;
  }
}

function isChinaOrNumbOrLett(s){ //判断是否是汉字、字母、数字组成 
  var regu = "^[0-9a-zA-Z\_\u4e00-\u9fa5]+$";
  var re = new RegExp(regu);
  if (re.test(s)){
    return true;
  }else{
    return false;
  } 
} 
</script>