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
    padding:0px 0px 6px 0px;
    text-align: center;
  }
  .module_submit input{
  	cursor:pointer;
  }
</style>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
<form name="form_participle" method="get" action="<?php echo U(MODULE_NAME.'/SearchSphinx/participle');?>" >
	<tr>
    <td valign="top">
      <table width="100%" height="31" border="0" cellpadding="0" cellspacing="0">
          <tr>
              <td height="31">
               <div class="titlebt">分词测试</div>
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
                          <div class="l">分词内容：</div>
                          <div class="c">
                              <input name="words" type="text" class="input_w" value="<?php echo ($words); ?>" style="width:380px" onSubmit="return check_form();" autocomplete="off" />
                              <input type="submit" class="btn"  value="确认提交" />
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
 
 <div style=" margin-left:100px;">
	<?php echo ($outkeywords); ?>
 </div>