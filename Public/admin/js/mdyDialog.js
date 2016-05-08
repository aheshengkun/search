/*
* layer插件，加载页面层自定义函数
* var url 要加载的页面的地址
* var title 弹窗对话框的显示标题 如修改xxx，添加xxx
* var width 对话框的显示宽度 如'330px'
* var width 对话框的显示高度 如'360px'
* 使用示例：onclick="mdyDialog('{:U('Admin/Access/userEdit',array('id'=>$v['id']))}','修改：{$v["account"]}','330px','360px');"
*/
function mdyDialog(url,title,width,height,refresh){
  //refresh 设置弹窗关闭后是否刷新页面，默认为刷新页面。添加此参数则不刷新页面,可设置为'1'
  var refresh = arguments[4] ? 1 : 0;

	parent.$.layer({
	    type: 2,
	    title: title,
      border : [6, 0.3, '#000'],
      area: [width,height],
	    shade: [0.3, '#000'],
	    shadeClose: true,
	    moveType: 0,
	    // shift: 'top', //从左动画弹出
	    closeBtn: [0, true], 
     	iframe: {src : url},
     	//弹窗彻底关闭后执行的函数，这里要刷新页面
     	end: function(){
        //refresh == 0 表示要刷新页面
        if(refresh == 0){
          setTimeout(function(){
            window.location.reload();
            return false;
          },500);
        }
     	}
	});
}

/*
* layer插件，确认或取消对话框
* var url点击确认按钮跳转到的地址
* var tips 对话框提示信息字符串
* 使用示例：onClick="conFirm('{:U('Admin/Access/delUser',array('id'=>$v['id']))}','确定要删除吗?删除后将不可恢复');"
*/
function conFirm(url,tips){
	// parent.layer.confirm(tips,function(index){
	// 		window.location.href=url;
	// 		parent.layer.close(index);
	// 		// location.reload();
	// 	}
	// );
	
	//改写框架的集成方法，改为点击对话框外部即关闭
	parent.$.layer({
      shade: [0.3, '#000'],
      area: ['300px','auto'],
      shadeClose: true,       //点击对话框外部即关闭
      dialog: {
        msg: tips,
        btns: 2,                    
        type: 4,
        btn: ['确定','取消'],
        yes: function(index){
        	window.location.href=url;
        	parent.layer.close(index);
        },
        no: function(){

        }
      }
    });
}


/*
* layer插件，确认或取消提交表单对话框
* var formid 要提交表单的from标签id属性值
* var tips 对话框提示信息字符串
* 使用示例：onClick="conFirmSubmit('#submitForm','确定要提交吗？');"
*/
function conFirmSubmit(formid,tips){
	//改写框架的集成方法，改为点击对话框外部即关闭
	parent.$.layer({
      shade: [0.3, '#000'],
      area: ['300px','auto'],
      shadeClose: true,       //点击对话框外部即关闭
      dialog: {
        msg: tips,
        btns: 2,                    
        type: 4,
        btn: ['确定','取消'],
        yes: function(index){
        	$(formid).submit();
        	parent.layer.close(index);
        },
        no: function(){

        }
      }
    });
}