/* check browser version (document.compatMode == "CSS1Compat") */
var sys = {};
var navua = navigator.userAgent.toLowerCase();
var browser;
(browser = navua.match(/msie ([\d.]+)/)) ? sys.ie = browser[1] :
(browser = navua.match(/firefox\/([\d.]+)/)) ? sys.firefox = browser[1] :
(browser = navua.match(/chrome\/([\d.]+)/)) ? sys.chrome = browser[1] :
(browser = navua.match(/opera.([\d.]+)/)) ? sys.opera = browser[1] :
(browser = navua.match(/version\/([\d.]+).*safari/)) ? sys.safari = browser[1] : 0;





/* 屏幕遮罩 quitScreenMask()设置了单击或按esc退出遮罩 */
function addScreenMask()
{
	
	var screenMaskA = '<div id="screenMask" style=" position:absolute; z-index:10000; top:0; left:0; display:none; width:100%; background:#000; opacity:0.5;"></div>';
	var screenMaskB = '<iframe frameborder="0" scrolling="no" id="screenMask" style=" position:absolute; z-index:10000; top:0; left:0; display:none; width:100%; background:#000; filter:alpha(opacity=30);"></iframe>';
	var screenMask = (!sys.ie) ? screenMaskA : screenMaskB;

	$(document.body).prepend(screenMask);
	$("#screenMask").height($(document).height()).fadeIn("fast");
	
}

function removeScreenMask()
{
	$("#screenMask").fadeOut("fast",function(){
		$(this).remove();
	});
}

function quitScreenMask(fadeOutObj)
{
	$("#screenMask").bind("click", function(){
		removeScreenMask();
		$(fadeOutObj).fadeOut("fast");
		removeDialogbox();//临时加入
	});
	$(document.body).keydown(function(e){
		if (e.keyCode == 27)
		{
			removeScreenMask();
			$(fadeOutObj).fadeOut("fast");
			removeDialogbox();//临时加入
		}
	});
}


/* 对话框 内容嵌入到 .dialogboxContent */
function addDialogbox(objId,title,zIndex,objWidth)
{
	
	addScreenMask();
	var objIdInHTML = objId.replace(/#/,"");
	var sTop = (document.compatMode == "CSS1Compat") ? document.documentElement.scrollTop : document.body.scrollTop;

	//why document.body.scrollTop == 0 on chrome?
	if (sys.chrome){sTop = document.body.scrollTop;}

	var docBodyScrollTop = sTop + $(window).height()/7;
	var left = $(window).width()/2 - objWidth/2;
	$(document.body).prepend(
	'<div id='+objIdInHTML+' class="dialogbox skin" style="top:'+docBodyScrollTop+'px; left:'+left+'px; z-index:'+zIndex+'; width:'+objWidth+'px;">'+

	'<div class="dialogboxTitle clearfix">'+
	'<h2>'+title+'</h2>'+
	'<p><a class="closeDialogbox" href="javascript:void(0);" title="关闭对话框"><img class="sprite sprite_close" src="'+IMGPATH+'sprite.gif" alt="关闭对话框" /></a></p>'+
	'</div>'+

	'<div class="dialogboxContent"></div>'+
	'</div>'
	);

	$(objId).fadeIn("fast");

	$(objId+" .closeDialogbox").bind("click",function(){
		removeDialogbox(objId);
	});
}

function removeDialogbox(objId)
{
	$(objId).fadeOut("fast",function(){
		removeScreenMask();
		$(this).remove();
		$("html").removeClass("hidden");
	});
}