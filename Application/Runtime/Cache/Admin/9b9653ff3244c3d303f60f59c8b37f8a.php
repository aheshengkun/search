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
		search.submit();
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
                    <form method="get" id="search" name="search" action="<?php echo U(MODULE_NAME.'/SearchArticle/index');?>">
	                   	<div style="float:left;">
	                        文章标题：
	                        <input type="text" name="post_title" id="searchword" value="<?php echo ($condition['post_title']); ?>" class="input" />
	                        <script>$("#post_type option[value='<?php echo ($condition["post_type"]); ?>']").attr("selected","selected");</script>
	                        <input type="button" class="btn" value="搜索"  onclick="javascript:search.submit();" />
	                    </div>
                    
		                <div style="float:right;">
		                	<input type="button" value="所有文章" class="mdyButton btn" onClick="javascript:Mdysearch('');" />
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
                      <td width="10%">ID</td>     
                      <td width="35%">文章标题</td>
					  <td width="35%">URL</td>
                      <td width="12%">增加时间</td>
                      <td width="8%">操作</td>
					</tr>
					<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><tr class="tr_content tr_content<?php echo ($item["id"]); ?>">
					  <td class="main_td1" align="center"><?php echo ($item["id"]); ?></td>
					  <td class="main_td1" align="center"><?php echo ($item["title"]); ?></td>
                      <td class="main_td1" align="center"><?php echo ($item["url"]); ?></td>
					  <td class="main_td1" align="center"><?php echo ($item["date_added"]); ?></td>
                      <!--<td class="main_td1" align="center" ><?php echo (date('Y-m-d H:i',$item["post_date"])); ?></td>-->
						<td>
							<a href="<?php echo U(MODULE_NAME.'/SearchArticle/updateArticle',array('id'=>$item['id']));?>">修改</a> /
							<a href="javascript:void(0);" onClick="conFirm('<?php echo U(MODULE_NAME."/SearchArticle/delArticle",array("id"=>$item["id"]));?>','确定要删除吗?删除后将不可恢复');">删除</a>
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