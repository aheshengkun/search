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
    padding:0px 0px 6px 0px;
    text-align: center;
  }
  .module_submit input{
  	cursor:pointer;
  }
</style>

<body>
<?php if(ACTION_NAME == "addType"): ?><table width="100%" border="0" cellpadding="0" cellspacing="0">
	<form name="form_user" method="post" action="<?php echo U(MODULE_NAME.'/Article/AddType');?>" autocomplete="off" id="submitForm">
  <tr>
    <td valign="top">
      <table width="100%" height="31" border="0" cellpadding="0" cellspacing="0">
          <tr>
              <td height="31">
               <div class="titlebt">添加分类</div>
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
                <table width="80%" border="0" cellpadding="0" cellspacing="0" class="content_fm">
                  <tr>
                      <td>
                        <div class="module_border">
                            <div class="l">上级分类：</div>
                            <div class="c">
                              <select name="parent_id" class="searchoption">
                                <option value="0">====顶级分类====</option>
                                <?php echo ($type["pidOption"]); ?>
                              </select>
                              <font color="#FF0000">*</font>
                            </div>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="module_border">
                            <div class="l">分类名称：</div>
                            <div class="c">
                                <input type="text" name="name" class="input_w" value="" />
                                <font color="#FF0000">*</font>
                            </div>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="module_border">
                            <div class="l">分类关键词：</div>
                            <div class="c">
                              <input type="text" name="slug" class="input_w" value="" />
                              <font color="#FF0000">*</font>
                            </div>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="module_border">
                            <div class="l">分类描述：</div>
                            <div class="c">
                              <input type="text" name="descript" class="input_w" value="" size="75"/>
                            </div>
                        </div>
                      </td>
                    </tr>

                    <tr>
                      <td>
                        <div class="module_border">
                            <div class="l">排序：</div>
                            <div class="c">
                                <input name="sort" type="text" value="0" class="input_w" />
                            </div>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="module_border">
                            <div class="l">菜单显示：</div>
                            <div class="c">
                                <input name="view" type="radio" value="1" checked="checked" /> 显示&nbsp;&nbsp;
                                <input name="view" type="radio" value="0" /> 不显示&nbsp;&nbsp;
                                <!-- <input name="view" type="radio" value="2" /> 侧导航&nbsp;&nbsp;
                                <input name="view" type="radio" value="3" /> 底导航&nbsp;&nbsp;
                                <input name="view" type="radio" value="4" /> 不固定 -->
                            </div>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="module_border">
                            <div class="l">分类类型：</div>
                            <div class="c">
                                <input name="is_page" type="radio" value="1" /> 单页&nbsp;&nbsp;
                                <input name="is_page" type="radio" value="0" checked="checked" /> 非单页&nbsp;&nbsp;
                                <input name="is_page" type="radio" value="2" /> 外部链接
                            </div>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="module_border">
                            <div class="l">外部链接url：</div>
                            <div class="c">
                                <input name="linkurl" type="text" value="" class="input_w" />
                                <font color="#FF0000">*格式为控制器/方法，如Ucenter/index,非外部链接请留空</font>
                            </div>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="module_border">
                          <div class="l">&nbsp;</div>
                          <div class="c">
                            <!-- <input type="submit" class="btn" value="确认提交" /> -->
                            <input type="button" name="btn" class="btn"  value="确认提交" onClick="check_formEdit();" />
                            <input type="reset" class="btn" name="reset" value="重置表单" />
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
<?php else: ?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <form name="form_user" method="post" action="<?php echo U(MODULE_NAME.'/Article/typeEdit');?>" id="submitForm" autocomplete="off" >
  <tr>
    <td valign="top">
      <table width="100%" height="31" border="0" cellpadding="0" cellspacing="0">
          <tr>
              <td height="31">
               <div class="titlebt">修改分类</div>
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
                <table width="80%" border="0" cellpadding="0" cellspacing="0" class="content_fm">
                  <tr>
                      <td>
                        <div class="module_border">
                            <div class="l">上级分类：</div>
                            <div class="c">
                              <select name="parent_id" id="pid" class="searchoption">
                                <option value="0">====顶级分类====</option>
                                <?php echo ($info["pidOption"]); ?>
                              </select>
                              <font color="#FF0000">*</font>
                            </div>
                        </div>
                      </td>
                      <script>
                        $("#pid option[value='<?php echo ($type["parent_id"]); ?>']").attr("selected","selected");
                      </script>
                    </tr>
                    <tr>
                      <td>
                        <div class="module_border">
                            <div class="l">分类名称：</div>
                            <div class="c">
                                <input type="text" name="name" class="input_w" value="<?php echo ($type["name"]); ?>" />
                                <font color="#FF0000">*</font>
                            </div>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="module_border">
                            <div class="l">分类关键词：</div>
                            <div class="c">
                              <input type="text" name="slug" class="input_w" value="<?php echo ($type["slug"]); ?>" />
                              <font color="#FF0000">*</font>
                            </div>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="module_border">
                            <div class="l">分类描述：</div>
                            <div class="c">
                              <input type="text" name="descript" class="input_w" value="<?php echo ($type["descript"]); ?>" />
                            </div>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="module_border">
                            <div class="l">排序：</div>
                            <div class="c">
                                <input name="sort" type="text" value="<?php echo ($type["sort"]); ?>" class="input_w" />
                            </div>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="module_border">
                            <div class="l">菜单显示：</div>
                            <div class="c">
                                <input name="view" type="radio" value="1" <?php if($type[view] == "1"): ?>checked="checked"<?php endif; ?> /> 显示&nbsp;&nbsp;
                                <input name="view" type="radio" value="0" <?php if($type[view] == "0"): ?>checked="checked"<?php endif; ?> /> 不显示&nbsp;&nbsp;
                                <!-- <input name="view" type="radio" value="2" <?php if($type[view] == "2"): ?>checked="checked"<?php endif; ?> /> 侧导航&nbsp;&nbsp;
                                <input name="view" type="radio" value="3" <?php if($type[view] == "3"): ?>checked="checked"<?php endif; ?> /> 底导航
                                <input name="view" type="radio" value="4" <?php if($type[view] == "4"): ?>checked="checked"<?php endif; ?> /> 不固定 -->
                            </div>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="module_border">
                            <div class="l">分类类型：</div>
                            <div class="c">
                                <input name="is_page" type="radio" value="1" <?php if($type[is_page] == "1"): ?>checked="checked"<?php endif; ?> /> 单页&nbsp;&nbsp;
                                <input name="is_page" type="radio" value="0" <?php if($type[is_page] == "0"): ?>checked="checked"<?php endif; ?> /> 非单页
                                <input name="is_page" type="radio" value="2" <?php if($type[is_page] == "2"): ?>checked="checked"<?php endif; ?> /> 外部链接
                            </div>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="module_border">
                            <div class="l">外部链接url：</div>
                            <div class="c">
                                <input name="linkurl" type="text" value="<?php echo ($type["linkurl"]); ?>" class="input_w" />
                                <font color="#FF0000">*格式为控制器/方法，如Ucenter/index,非外部链接请留空</font>
                            </div>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="module_border">
                          <div class="l">&nbsp;</div>
                          <div class="c">
                            <input type="hidden" name="id" value="<?php echo ($type["term_id"]); ?>">
                            <!-- <input type="submit" class="btn" value="确认修改" /> -->
                            <input type="button" name="btn" class="btn"  value="确认提交" onClick="check_formEdit();" />
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
 </table><?php endif; ?>
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
          var name  = $('input[name=name]');
          var slug  = $('input[name=slug]');
          //用于错误提示信息
          var errorMsg = '';

          //分类名称检查
          if (name.val() == '' || name.val().length > 50) {
            errorMsg += '分类名称不能为空且必须小于50位<br />';
          }
          //网站url检查
          if (slug.val().length == 0 || slug.val().length > 40) {
            errorMsg += '分类关键词不能为空且必须小于40位<br />';
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