<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<link href="favicon.ico" rel="shortcut icon" />
    <title><?php echo ($CON_SEO['con_webname']); if($SEO['title'] != ''): echo ($SEO['title']); endif; ?></title>
    <meta name="keywords" content="<?php if($SEO["keywords"] != ""): echo ($SEO['keywords']); else: echo ($CON_SEO['con_webkeywords']); endif; ?>" />
    <meta name="description" content="<?php if($SEO["description"] != ""): echo ($SEO['description']); else: echo ($CON_SEO['con_webdescription']); endif; ?>" />
	<link rel="stylesheet" href="/searchsys/Public/home/default/css/common.css" />
	<script type="text/javascript" src="/searchsys/Public/home/default/js/jquery-1.8.3.min.js"></script>

	<?php if(CONTROLLER_NAME == 'Ucenter'): ?><link rel="stylesheet" href="/searchsys/Public/home/default/css/user.css" />
		<script type="text/javascript" src="/searchsys/Public/home/default/js/area/areaSelect.js"></script><?php endif; ?>
	
	<script type="text/javascript" src="/searchsys/Public/home/default/js/SuperSlide.js"></script>
	<script type="text/javascript" src="/searchsys/Public/home/default/js/commont.js"></script>
	<script type="text/javascript" src="/searchsys/Public/home/default/js/switch.js"></script>
	<script type="text/javascript" src="/searchsys/Public/common/layer/layer.min.js"></script>
</head>
<body>
	
<!-- /头部 -->
		<!-- headerWrap end -->
		<div class="mainBox webAuto clearfix">
			<!--left bar-->
			<div class="leftMenu fl">
	<ul>
		<div class="SiteNav">
			<p>网站导航</p>
			<p>Site navigation</p>
		</div>
		<?php if(is_array($leftMenu)): foreach($leftMenu as $key=>$val): if($val['linkurl'] != ''): ?><li><a href="<?php echo U($val['linkurl']);?>" <?php if($val[term_id] == "$post_tid"): ?>class="subhover"<?php endif; ?> ><?php echo ($val["name"]); ?></a></li>
			<?php else: ?>
				<?php if($val['term_id'] == 9): ?><li><a href="<?php echo U('NewContent/index',array('id'=>$val['term_id'],'pid'=>0));?>" <?php if($val[term_id] == "$post_tid"): ?>class="subhover"<?php endif; ?> ><?php echo ($val["name"]); ?></a></li>
				<?php else: ?>
					<li><a href="<?php echo U('NewList/index',array('tid'=>$val['term_id'],'pid'=>0));?>" <?php if($val[term_id] == "$post_tid"): ?>class="subhover"<?php endif; ?> ><?php echo ($val["name"]); ?></a></li><?php endif; endif; endforeach; endif; ?>
	</ul>
</div>
			<!---left bar结束-->
			<div class="rightContent fl">
				<div class="title"><?php echo ($list[0]['name']); ?></div>
				<?php if(($duli == 1)): ?><div class="ArticleContent">
						<p><?php echo ($content); ?></p>
					</div>
				<?php else: ?>
					<div class="listing">
						<div class="titleLine">
							<div class="w570 fl">标题</div>
							<div class="w174 fl" style="margin-left:23px">发布时间</div>
						</div>
						<ul class="cleafix">
							<?php if(is_array($list)): foreach($list as $key=>$v): ?><li>
									<span class="tigs fl">[<?php echo ($v["name"]); ?>]</span>
									<span class="fl" style="padding-left:10px;">
										<a href="<?php echo U('NewContent/index',array('id'=>$v['id'],'pid'=>$v['pid']));?>" title="<?php echo ($v["post_title"]); ?>"><?php echo (str_cut($v["post_title"],"28")); ?></a> <?php if($v['top'] == 1): ?><span class="top">置顶</span><?php endif; ?>
									</span>
									<span class="time fl"><?php echo (date('Y-m-d',$v['post_date'])); ?></php></span>
								</li><?php endforeach; endif; ?>
						</ul>
						<div class="Pageing"><?php echo ($page); ?></div>
					</div><?php endif; ?>
			</div>
			<!-- itemAttr end -->
		</div>
		<!-- mainBox end -->
		<!-- 底部 -->
		
		<!-- /底部 -->
	</body>
</html>