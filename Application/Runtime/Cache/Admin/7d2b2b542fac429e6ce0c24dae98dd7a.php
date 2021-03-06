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
<link rel="stylesheet" href="/searchsys/Public/admin/kindeditor/themes/default/default.css" />
<script type="text/javascript" src="/searchsys/Public/admin/kindeditor/kindeditor-all.js"></script>
<script type="text/javascript" src="/searchsys/Public/admin/kindeditor/lang/zh_CN.js"></script>
 

<style type="text/css">
  textarea.textarea{
    width: 830px;
    height: 100px;
    line-height: 22px;
    border: 1px solid #CCCCCC;
    overflow: auto;
    resize: none;
    font-size: 12px;
  }
  .module_border {
  	/*height: 30px;*/
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
  input.text{
    width: 300px;
    height: 25px;
    line-height: 25px;
    border: 1px solid #CCCCCC;
  }
  .v_center1{
    height:110px;line-height:104px;
  }
  .v_center2{
    height:405px;line-height:405px;
  }
  div.tip_mark{
    /*display: inline-block;*/
    color: red;
    height:114px;line-height:104px;
  }
</style>

<body>
<form name="form_user" method="post" action="<?php echo U(MODULE_NAME.'/SearchArticle/updateArticle');?>" autocomplete="off" id="submitForm" enctype="multipart/form-data">
  <table width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td valign="top">
        <table width="100%" height="31" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td height="31"><div class="titlebt">修改文章</div></td>
          </tr>
        </table>
      </td>
    </tr>
    <tr>
      <td valign="top" bgcolor="#F7F8F9">
        <table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
                <tr>
                  <td><div class="module_border">
                      <div class="l">文章标题：</div>
                      <div>
                        <input name="title" type="text" class="text"  value="<?php echo ($article["title"]); ?>" />
                        <font color="#FF0000">*</font> </div>
                    </div></td>
                </tr>
                <tr>
                  <td><div class="module_border">
                      <div class="l">URL</div>
                      <div>
                        <input name="url" type="text" class="text"  value="<?php echo ($article["url"]); ?>" />
                      </div>
                    </div></td>
                </tr>
                <tr>
                  <td><div class="module_border v_center1">
                      <div class="l v_center1">文章内容：</div>
                      <div>
                        <textarea name="content" class="textarea"><?php echo ($article["content"]); ?></textarea>
                      </div>
                    </div></td>
                </tr>
                <tr>
                  <td><div class="module_border">
                      <div class="l">发表时间：</div>
                      <div class="c">
                        <input type="text" name="dotime1" id="dotime1" value="<?php echo (date('Y-m-d H:i:s',$article["date_added"])); ?>" class="input" style="width:180px;" onClick="WdatePicker({skin:'whyGreen',startDate:'%y-%M-%d %H:00:00',dateFmt:'yyyy-MM-dd HH:mm:ss'});"/>
                      </div>
                    </div></td>
                </tr>
                
                <tr>
                  <td>
                    <div class="module_border">
                      <div class="l">&nbsp;</div>
                      <div class="c">
                      <input type="hidden" name="id" value="<?php echo ($article["id"]); ?>">
                      <input type="button" name="btn" class="btn"  value="确认修改" onClick="check_formEdit()" />
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
        </table>
      </td>
    </tr>
  </table>
</form>
<script type="text/javascript">
  function check_formEdit(){
    parent.$.layer({
      shade: [0.3, '#000'],
      area: ['300px','auto'],
      shadeClose: true,       //点击对话框外部即关闭
      dialog: {
        msg: '确认提交吗？',
        btns: 2,                    
        type: 4,
        btn: ['确定','取消'],
        yes: function(index){
          var title  = $('input[name=title]');   //文章标题
          var url  = $('input[name=url]');   //外部链接url
          var content  = $('textarea[name=content]');   //文章内容
          //用于错误提示信息
          var errorMsg = '';

          //文章标题检查
          if (title.val().length < 1 || title.val().length > 200) {
            errorMsg += '文章标题必须在1到100个字符之间<br />';
          }
          //外部链接url检查
          if (url.val().length > 500) {
            errorMsg += '外部链接url不能超过500个字符<br />';
          }
          //文章内容检查
          if (content.val().length > 2000) {
            errorMsg += '文章内容不能超过2000个字符<br />';
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
            $('#submitForm').submit();
            parent.layer.close(index);
            return true;
          }
        },
        no: function(){

        }
      }
    });
  }
</script>