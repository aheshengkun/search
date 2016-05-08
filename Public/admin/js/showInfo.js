function showInfoBy(id,page,size){
  //分页每次调用都把之前的层关闭，防止叠加层
  layer.closeAll();
  $.ajax({
    async:false,
    type: "POST",
    url: "{:U('Loan/single')}",
    data:{"id":id,'page':page,'size':size},
    success: function(msg){
      var len=msg.length-1;
      if(len==0){
        // alert('此标尚未有人投标!');
        parent.$.layer({
          shade: [0.3, '#000'],
          area: ['auto','auto'],
          shadeClose: true,       //点击对话框外部即关闭
          dialog: {
            msg: '此标尚未有人投标!',
            btns: 1,                    
            type: 8,
            btn: ['确定'],
            no: function(){

            }
          }
        });
        return false;
      }
      var count=Math.ceil(msg[len].count/size);
      var str='';
      var addtime='';
      var total='';
      var n=null;
      var pstr='';
      //包含样式表文件
      // str = '<link type="text/css" rel="stylesheet" href="__PUBLIC__/admin/css/skin.css" />';
      // str += '<style type="text/css">.pageing a{text-decoration:none;color:#666; padding:0 15px; margin: 0 3px;height:30px; line-height:30px; border:1px solid #428BCA; display:inline-block;border-radius:3px; -moz-border-radius:3px; -webkit-border-radius:3px;}.pageing a:hover{color: #428BCA;}div.pageing{width:auto; margin:auto; height:auto; padding:10px 0 10px; text-align:right; font-size:14px;}</style>';
      str +='<div class="titlebt"><span class="red">标题:</span>&nbsp;'+msg[0].title+'&nbsp;&nbsp;&nbsp;<span class="red">借款人:</span>&nbsp;'+msg[0].user+'</div><table width="650" border="0" cellpadding="3" cellspacing="1" bgcolor="#CCCCCC" class="content"><tr class="tr_title"><th>序号</th><th>投标人</th><th>投标金额</th><th>年利率</th><th>预计收益(元)</th><th>管理费(元)</th><th>共计(元)</th><th>投标时间</th></tr>';
      
      for(var i=0;i<len;i++){
        n=i+1+(page-1)*size;
        addtime=timeToDay(msg[i].addtime);
        total=msg[i].repayment_account-msg[i].management_expense;
        total=total.toFixed(2);
        str+='<tr><td align="center">'+n+'</td><td align="center">'+msg[i].name+'</td><td align="center">'+msg[i].money+'</td><td align="center">'+msg[i].apr+'%</td><td align="center">'+msg[i].interest+'</td><td align="center">'+msg[i].management_expense+'</td><td align="center">'+total+'</td><td align="center">'+addtime+'</td></tr>';
      }
      str+='</table>';

      if(count>1){
        for(var j=0;j<count;j++){
          pstr+='<a class="num" style="cursor:pointer" onclick="showInfo('+id+','+(j+1)+','+size+')">'+(j+1)+'</a>';
        }
      }
      pstr+='<a class="num" href="#">共有 <font size="3" color="red">'+msg[len].count+'</font> 条记录</a>';
      str+='<div class="pageing">'+pstr+'</div>';
      // art.dialog({
      //   id:'show',
      //   content:str,
      //   lock:true,
      //   style:'succeed noClose',
      //   title:'借款标详情'
      // });
      
      $.layer({
        type: 1,
        title: '借款标详情',
        shade: [0.3, '#000'],
        area: ['auto','auto'],
        shadeClose: true,       //点击对话框外部即关闭
        page: {
          html: str
        }
      });
    }
  });
}