<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no,initial-scale=1">
<meta name="format-detection" content="telephone=no">
<link rel="stylesheet" href="/searchsys/Public/home/default/css/common.css" />
<link rel="stylesheet" href="/searchsys/Public/home/default/css/search.css" />
<title>搜一搜</title>
</head>
<body class="mybody">
<div class="content">
<div class="logo">搜一搜</div>
<form action="<?php echo U(MODULE_NAME.'/index/search');?>" method="get">
<span class="bg s_ipt_wr">
<div id="kw_tip" style="display: none;" unselectable="on" onselectstart="return false;" class="s_ipt_tip"></div>
<input id="kw" name="words" class="s_ipt" value="<?php echo ($words); ?>" maxlength="100" autocomplete="off">
</span>
<input type="submit" id="su" value="搜一下" class="s_btn">
</form>
<div class="nums"><?php echo ($search_info); ?></div>

<?php if(is_array($list)): foreach($list as $key=>$items): ?><div class="result c-container ">
	<h3 class="t">
	<a  href="" target="_blank"><?php echo ($items['title']); ?></a>
	</h3>
	<div class="c-abstract"><?php echo ($items['content']); ?></div>
	<div class="f13"><span class="g"></span></div><br/>
</div><?php endforeach; endif; ?>
<div class="Pageing"><?php echo ($page); ?></div>
</div> 

</body>
</html>