<?php if (!defined('THINK_PATH')) exit();?><link type="text/css" rel="stylesheet" href="/searchsys/Public/admin/css/skin.css" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv='Refresh' content='<?php echo ($waitSecond); ?>;URL=<?php echo ($jumpUrl); ?>'>
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
	background-color: #EEF2FB;
}
-->
</style>
<body>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td valign="top">
		<table width="100%" height="31" border="0" cellpadding="0"
			cellspacing="0" class="left_topbg" id="table2">
			<tr>
				<td height="31">
				<div class="titlebt">页面提示</div>
				</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#F7F8F9">
		<table width="98%" border="0" align="center" cellpadding="0"
			cellspacing="0">
			<tr style="height: 60px;">
				<td colspan="2" valign="top">&nbsp;</td>
				<td>&nbsp;</td>
				<td valign="top">&nbsp;</td>
			</tr>
			<tr>
				<td width="25%">&nbsp;</td>
				<td width="40%" valign="top">
				<table width="100%" height="144" border="0" cellpadding="0"
					cellspacing="0" class="line_table">
					<tr>
						<td width="7%" height="27" background="/searchsys/Public/admin/images/news-title-bg.gif"><img src="/searchsys/Public/admin/images/news-title-bg.gif" width="2" height="27"></td>
						<td width="93%" background="/searchsys/Public/admin/images/news-title-bg.gif" class="left_bt2"><?php echo ($msgTitle); ?></td>
					</tr>
					<tr>
						<td height="60" colspan="2" style="text-align: center; font-size: 16px;"><?php echo ($error); ?></td>
					</tr>
					<tr>
						<td height="40" colspan="2" style="text-align: center; font-size: 14px;">
                        系统将在 <span style="color: blue; font-weight: bold" id="time"><?php echo ($waitSecond); ?></span>
                        秒后自动关闭，如果不想等待,直接点击<A id="href" href="<?php echo ($jumpUrl); ?>" style="font-size: 15px; color: blue;">这里</A> 关闭</td>
					</tr>
				</table>
				</td>
			</tr>
			<script language="javascript">
			  (function(){
			  var wait = document.getElementById('time'),href = document.getElementById('href').href;
			  var interval = setInterval(function(){
				  var time = --wait.innerHTML;
				  if(time == 0) {
					  location.href = href;
					  clearInterval(interval);
				  };
			  }, 1000);
			  })();
	        </script>
			<tr style="height: 180px;">
				<td colspan="2">&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</table>
		</td>
	</tr>
</table>
</body>