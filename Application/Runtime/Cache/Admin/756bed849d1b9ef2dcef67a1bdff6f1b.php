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
<script type="text/javascript">
    KindEditor.ready(function(K) {
      var editor1 = K.create('textarea[name="post_content"]', {
        uploadJson : '/searchsys/Admin/ArticleContent/upload',
        allowFileManager : false,
        afterCreate : function() {
          var self = this;
          K.ctrl(document, 13, function() {
            self.sync();
            K('form[name=form_user]')[0].submit();
          });
          K.ctrl(self.edit.doc, 13, function() {
            self.sync();
            K('form[name=form_user]')[0].submit();
          });
        },
		afterBlur: function(){this.sync();}
      });
      //prettyPrint();
    });



  $(function () {
    $('#post_tid').change(function(){
      var brtype = $(this).children('option:selected').attr('class');//根据class值判断是否单页分类
      var htmlTxt = '';
      switch(brtype){
        case '0':
          htmlTxt += '<option value="post">非单页</option>';break;
        case '1':
          htmlTxt += '<option value="page">单页</option>';break;
        default:
          htmlTxt += '<option value="post">非单页</option>';
          htmlTxt += '<option value="page">单页</option>';break;
      }
      $("#post_type").html(htmlTxt);
    })
  })

</script>
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

<form name="form_user" method="post" action="<?php echo U(MODULE_NAME.'/ArticleContent/addArticle');?>"  autocomplete="off" id="submitForm" enctype="multipart/form-data">
  <table width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td valign="top">
        <table width="100%" height="31" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td height="31"><div class="titlebt">添加文章</div></td>
          </tr>
        </table>
      </td>
    </tr>
    <tr>
      <td valign="top" bgcolor="#F7F8F9">
        <table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td class="td_top">
              <table width="100%" border="0" cellpadding="0" cellspacing="0" class="content_fm">
                <tr>
                  <td><div class="module_border">
                      <div class="l">文章所属分类：</div>
                      <div>
                        <select name="post_tid" class="searchoption" id="post_tid">
                          <option value="" class="">====请选择====</option>
                          <?php echo ($type["pidOption"]); ?>
                        </select>
                        <font color="#FF0000">*外部链接分类下不能添加文章</font>
                      </div>
                    </div></td>
                </tr>                
                <tr>
                  <td><div class="module_border">
                      <div class="l">文章类型：</div>
                      <div>
                        <select name="post_type" class="searchoption" id="post_type">
                          <option value="post">非单页</option>
                          <option value="page">单页</option>
                        </select>
                        <font color="#FF0000">*</font> 
                      </div>
                    </div></td>
                </tr>
                <tr>
                  <td><div class="module_border">
                      <div class="l">文章封面图片：</div>
                      <div>
                        <input type="file" name="face_img" id="face_img" />
                      </div>
                    </div></td>
                </tr>
                <tr>
                  <td><div class="module_border">
                      <div class="l">是否置顶：</div>
                      <div class="c">
                        <label><input name="top" type="radio" value="1" /> 置顶</label>&nbsp;&nbsp;
                        <label><input name="top" type="radio" value="0" checked="checked" /> 不置顶</label>
                      </div>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td><div class="module_border">
                      <div class="l">文章标题：</div>
                      <div>
                        <input name="post_title" type="text" class="text"  value="" />
                        <font color="#FF0000">*</font> </div>
                    </div></td>
                </tr>
                <tr>
                  <td><div class="module_border">
                      <div class="l">文章关键字：</div>
                      <div>
                        <input name="post_name" type="text" class="text"  value="" />
                      </div>
                    </div></td>
                </tr>                 
                <tr>
                  <td><div class="module_border v_center1">
                      <div class="l v_center1">文章摘要：</div>
                      <div>
                        <textarea name="post_excerpt" class="textarea"></textarea>
                      </div>
                    </div></td>
                </tr>
                <tr>
                  <td><div class="module_border" style="height:410px;">
                      <div class="l v_center2">文章内容：</div>
                      <div> 
                        <textarea name="post_content" id="ArcContent" style="width:830px;height:400px;visibility:hidden;">
                        </textarea>
                      </div>
                    </div></td>
                </tr>
                <tr>
                  <td><div class="module_border">
                      <div class="l">是否发表：</div>
                      <div class="c">
                        <input name="post_status" type="radio" value="publish" checked /> 发表&nbsp;&nbsp;
                        <input name="post_status" type="radio" value="draft" /> 作为草稿
                      </div>
                    </div></td>
                </tr>

                <tr>
                  <td><div class="module_border">
                      <div class="l">发表时间：</div>
                      <div class="c">
                        <input type="text" name="dotime1" id="dotime1" value="" class="input" style="width:180px;" onClick="WdatePicker({skin:'whyGreen',startDate:'%y-%M-%d %H:00:00',dateFmt:'yyyy-MM-dd HH:mm:ss'});"/>
                      </div>
                    </div></td>
                </tr>

                <tr>
                  <td>
                    <div class="module_border">
                      <div class="l">&nbsp;</div>
                      <div class="c">
                      <!-- <input type="submit" class="btn" value="确认提交" /> -->
                      <input type="button" name="btn" class="btn"  value="确认提交" onClick="check_formEdit()" />
                      <input type="reset"  class="btn" name="reset" value="重置表单" />
                      </div>
                    </div>
                  </td>
                </tr>
              </table></td>
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
          var post_tid  = $('select[name=post_tid]');      //文章所属分类
          var post_title  = $('input[name=post_title]');   //文章标题
          var post_name  = $('input[name=post_name]');   //文章关键字
          var post_excerpt  = $('textarea[name=post_excerpt]');   //文章摘要
          // var post_content  = $('textarea[name=post_content]');   //文章内容
          //用于错误提示信息
          var errorMsg = '';

          //文章所属分类检查
          if (post_tid.val() == '') {
            errorMsg += '文章所属分类必须选择<br />';
          }
          //文章标题检查
          if (post_title.val().length < 1 || post_title.val().length > 100) {
            errorMsg += '文章标题必须在1到100个字符之间<br />';
          }
          //文章关键字检查
          if (post_name.val().length > 100) {
            errorMsg += '文章关键字不能超过100个字符<br />';
          }
          //文章摘要检查
          if (post_excerpt.val().length > 350) {
            errorMsg += '文章摘要不能超过350个字符<br />';
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