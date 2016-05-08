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

<script type="text/javascript" >  
	$(document).ready(function() {  
	    $(".stripe tr").mouseover(function() {
            $(this).addClass("over");  
        }).mouseout(function() {  
            $(this).removeClass("over");  
        });
	})

	function Mdysearch(status){
		$("#sta").val(status);
		search.submit();
	}

	//多选框处理，全选、反选、全不选
	var checkall=document.getElementsByName("ids[]");
	//全选
	function select(){
		for(var i=0; i<checkall.length; i++){
			checkall[i].checked=true;
		}
	}
	//反选
	function fanselect(){
		for(var i=0; i<checkall.length; i++){
			if(checkall[i].checked){
				checkall[i].checked=false;
			}else{
				checkall[i].checked=true;
			}
		}
	}
	//全不选
	function noselect(){
		for(var i=0; i<checkall.length; i++){
			checkall[i].checked=false;
		}
	}
</script>  

<style type="text/css">
	a.abtn{
		display: inline-block;
		height: 25px;
		line-height: 25px;
		border: 1px solid #357ebd;
		border-radius: 4px;
		padding: 2px;
		text-align: center;
		vertical-align: middle;
		background-color: #428bca;
		color: #fff;
	}
	.stripe tr{
		background: #fff;
	}
	.stripe tr.over td {  
	    background:#E8ECF0;  /*这个将是鼠标高亮行的背景色*/  
	}
	
</style>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td valign="top">
		<table width="100%" height="31" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td height="31">
				<div class="titlebt">所有文章</div>
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
                    <form method="get" id="search" name="search" action="<?php echo U(MODULE_NAME.'/ArticleContent/index');?>">
	                   	<div style="float:left;">
	                        文章标题：
	                        <input type="text" name="post_title" id="searchword" value="<?php echo ($condition['post_title']); ?>" class="input" />
	                        所属分类：
	                        <select name="post_tid" id="post_tid" class="searchoption">
	                          <option value="0">====请选择====</option>
	                          <?php echo ($type["pidOption"]); ?>
	                        </select>
	                        <script>$("#post_tid option[value='<?php echo ($condition["post_tid"]); ?>']").attr("selected","selected");</script>
	                        &nbsp;&nbsp;文章类型：
	                        <select name="post_type" id="post_type" class="searchoption">
	                          <option value="">==请选择==</option>
	                          <option value="post">非单页</option>
	                          <option value="page">单页</option>
	                        </select>
	                        <script>$("#post_type option[value='<?php echo ($condition["post_type"]); ?>']").attr("selected","selected");</script>
	                        <input name="post_status" type="hidden" value="" id="sta" />
	                        <input type="button" class="btn" value="搜索"  onclick="javascript:search.submit();" />
	                    </div>
                    
		                <div style="float:right;">
		                	<input type="button" value="所有文章" class="mdyButton btn" onClick="javascript:Mdysearch('');" />
		                	<input type="button" value="已发表" class="mdyButton btn" onClick="javascript:Mdysearch('publish');" />
		                	<input type="button" value="草稿" class="mdyButton btn" onClick="javascript:Mdysearch('draft');" />
		                </div>
	                </form>
                </div>
				</td>
			</tr>
			<form action="<?php echo U(MODULE_NAME.'/ArticleContent/articleHandle');?>" method="post" id="form">
			<tr>
				<td>
				<table width="100%" border="0" cellpadding="0" cellspacing="0" class="content stripe">
					<tr class="tr_title">
					  <td width="4%">选择</td>
                      <td width="6%">ID</td>
                      <td width="6%">排序</td>
                      <td width="6%">发表者</td>
                      <td width="35%">文章标题</td>
                      <td width="7%">所属分类</td>
                      <td width="6%">文章类型</td>
                      <td width="6%">文章状态</td>
                      <!-- <td width="4%">评论状态</td> -->
                      <td width="12%">发表时间</td>
                      <!-- <td >评论数</td> -->
                      <td width="8%">操作</td>
					</tr>
					<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><tr class="tr_content tr_content<?php echo ($item["id"]); ?>">
					  <td class="main_td1" align="center"><input type="checkbox" name="ids[]" value="<?php echo ($item["id"]); ?>" /></td>
					  <td class="main_td1" align="center"><?php echo ($item["id"]); ?></td>
					  <td class="main_td1" align="center" ><input type="text" size="4" name="<?php echo ($item["id"]); ?>" value="<?php echo ($item["menu_order"]); ?>"></td>
                      <td class="main_td1" align="center"><?php echo ($item["author"]); ?></td>
                      <td class="main_td1" align="center"><a href="<?php echo U('/NewContent/index',array('id'=>$item['id']));?>" title="<?php echo ($item["post_title"]); ?>" target="_blank"><?php echo (str_cut($item["post_title"],"25")); ?></a><?php if($item['top'] == 1): ?><span class="top">置顶</span><?php endif; ?></td>
                      <td class="main_td1" align="center"><?php echo ($item["tname"]); ?></td>
                      <td class="main_td1" align="center" >
                        <?php switch($item["post_type"]): case "post": ?>非单页<?php break;?>
                            <?php case "page": ?><span class="red">单页</span><?php break; endswitch;?>
                      </td>
                      <td class="main_td1" align="center" >
                         <?php switch($item["post_status"]): case "publish": ?>已发表<?php break;?>
                            <?php default: ?><span class="red">草稿</span><?php endswitch;?>
                      </td>
                      <!-- <td class="main_td1" align="center" >
                      	<?php switch($item["comment_status"]): case "open": ?>允许<?php break;?>
                            <?php default: ?><span class="red">不允许</span><?php endswitch;?>
                      </td> -->
                      <td class="main_td1" align="center" ><?php echo (date('Y-m-d H:i',$item["post_date"])); ?></td>
                      <!-- <td class="main_td1" align="center" ><?php echo ($item["comment_count"]); ?></td> -->
                       	<td>
                           	<a href="<?php echo U(MODULE_NAME.'/ArticleContent/updateArticle',array('id'=>$item['id']));?>">修改</a> /
                          	<a href="javascript:void(0);" onClick="conFirm('<?php echo U(MODULE_NAME."/ArticleContent/delArticle",array("id"=>$item["id"]));?>','确定要删除吗?删除后将不可恢复');">删除</a>
                      	</td>
					</tr><?php endforeach; endif; else: echo "" ;endif; ?>
				</table>
				</td>
			</tr>
			<tr>
				<td height="10"></td>
			</tr>
			<tr>
				<td colspan="10" align="left">
					<a href="javascript:select()" class="abtn">全选</a> <a href="javascript:fanselect()" class="abtn">反选</a> <a href="javascript:noselect()" class="abtn">全不选</a>&nbsp;&nbsp;
					<input type="hidden" name="delChecks" value="删除所选">
					<input type="button" onClick="conFirmSubmit('#form','确定删除选中的文章吗，删除后不可恢复！');" class="btn" value="删除所选" />
					<input type="submit" class="btn" value="排序" />
					<?php if($page): ?><div class="page"><?php echo ($page); ?></div><?php endif; ?>
				</td>
			</tr>
			</form>
			<tr>
				<td height="20"></td>
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
		</td>
	</tr>
</table>