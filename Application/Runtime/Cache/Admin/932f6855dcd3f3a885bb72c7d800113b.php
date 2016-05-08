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
<script language="javascript" src="/searchsys/Public/admin/timepicker/WdatePicker.js"></script>

<style type="text/css">
  .module_border {
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
  	padding: 10px 0px;
  	text-align: center;
  }
  .module_submit input {
  	cursor: pointer;
  }
  .remark_textarea{
    width: 280px;
    height: 70px;
    overflow: auto;
    font-size: 12px;
    border: 1px solid #ccc;
  }
</style>
<script type="text/javascript">
$(document).ready(function (){
	  $("#province").change(function(){
		  var province = $(this).val();
		  var count = 0;
		  $.ajax({
			url:"<?php echo U(MODULE_NAME.'/Customer/Ajaxarea');?>",
			dataType:'json',
			data:"id="+province,
			success:function(json){
				$("#city option").each(function(){
				  $(this).remove();
				});
				$("#area option").each(function(){
				  $(this).remove();
				});
				$("#city").html("<option value=''>请选择</option>");
				$("#area").html("<option value=''>请选择</option>");
				$(json).each(function(){
				  $("<option value='"+json[count].id+"'>"+json[count].name+"</option>").appendTo("#city");
				  count++;
				});
		    }
		  });
	  });
	  $("#city").change(function(){
		  var province = $(this).val();
		  var count = 0;
		  $.ajax({
			  url:"<?php echo U(MODULE_NAME.'/Customer/Ajaxarea');?>",
			  dataType:'json',
			  data:"id="+province,
			  success:function(json){
				  $("#area option").each(function(){
					$(this).remove();
				  });
				  $(json).each(function(){
					$("<option value='"+json[count].id+"'>"+json[count].name+"</option>").appendTo("#area");
					count++;
				  });
				  if(count>0){
					$("#area").show();
				  }else{
					$("#area").hide();
				  }
				}
			  });
		  });
	  $("#area").change(function(){
	  });
  }); 
</script>

<form name="form_user" method="post" action="<?php echo U(MODULE_NAME.'/Kefu/addKefuInsert');?>" onSubmit="return check_form();" autocomplete="off" enctype="multipart/form-data" >
  <table width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td valign="top">
        <table width="100%" height="31" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td height="31"><div class="titlebt">添加客服</div></td>
          </tr>
        </table>
      </td>
    </tr>
    <tr>
      <td valign="top" bgcolor="#F7F8F9">
        <table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td class="td_top">
              <table width="70%" border="0" cellpadding="0" cellspacing="0" class="content_fm">
                <tr>
                  <td><div class="module_border">
                      <div class="l">客服名：</div>
                      <div class="c">
                        <input name="username" type="text"  value="" class="input_w" />
                        <font color="#FF0000">*</font> </div>
                    </div></td>
                </tr>
                <tr>
                  <td><div class="module_border">
                      <div class="l">登录密码：</div>
                      <div class="c">
                        <input name="password" type="password" class="input_w" />
                        <font color="#FF0000">*</font> </div>
                    </div></td>
                </tr>
                <tr>
                  <td><div class="module_border">
                      <div class="l">确认密码：</div>
                      <div class="c">
                        <input name="password1" type="password" class="input_w" />
                        <font color="#FF0000">*</font> </div>
                    </div></td>
                </tr>
                <tr>
                  <td><div class="module_border">
                      <div class="l">真实姓名：</div>
                      <div class="c">
                        <input name="realname" type="text" value="" class="input_w" />
                        <font color="#FF0000">*</font>
                      </div>
                    </div></td>
                </tr>
                <tr>
                  <td><div class="module_border">
                      <div class="l">手机：</div>
                      <div class="c">
                        <input name="phone" type="text" value="" class="input_w" />
                      </div>
                    </div></td>
                </tr>
                <tr>
                  <td><div class="module_border">
                      <div class="l">备注说明：</div>
                      <div class="c">
                        <textarea name="remark" class="remark_textarea"></textarea>
                      </div>
                    </div></td>
                </tr>
                <tr>
                  <td>
                    <div class="module_border">
                      <div class="l">&nbsp;</div>
                      <div class="c">
                      <input type="hidden" name="role_id" value="9"><!--value=9表示客服类型角色-->
                      <input type="submit" class="btn" value="确认提交" />
                      <input type="reset"  class="btn" name="reset" value="重置表单" />
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
        </table></td>
    </tr>
  </table>
</form>
<script type="text/javascript">
function check_form(){
  var username  = $("input[name='username']");
  var realname  = $("input[name='realname']");    //真实姓名
  var password  = $("input[name='password']");
  var password1 = $("input[name='password1']");
  //用于错误提示信息
  var errorMsg = '';

  //客服名检查
  if (username.val().length < 2 || username.val().length > 15) {
    errorMsg += '客服名长度在2到15个字符之间<br />';
  }else if(isChinaOrNumbOrLett(username.val()) == false){
    errorMsg += '客服名由汉字、字母、数字、下划线组成<br />';
  }

  //密码检查
  if (password.val().length <= 4) {
    errorMsg += '密码必须填写且大于4位<br />';
  }else if(password.val() !== password1.val()){
    errorMsg += '确认密码不一致<br />';
  }

  //真实姓名检查
  if (realname.val().length < 2 || realname.val().length > 15) {
    errorMsg += '真实姓名长度在2到15个字符之间<br />';
  }else if(isChinaOrNumbOrLett(realname.val()) == false){
    errorMsg += '真实姓名不能有特殊符号<br />';
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

function isEmail(str){ 
  var myReg = /^[-_A-Za-z0-9]+@([_A-Za-z0-9]+\.)+[A-Za-z0-9]{2,3}$/; 
  if(myReg.test(str)) return true; 
  return false; 
}

function isQQ(str){
  var myReg = /^[1-9][0-9]{4,9}$/;
  if(myReg.test(str)){
    return true;
  }else{
    return false;
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