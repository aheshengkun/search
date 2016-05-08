$(document).ready(function(){
		//点击事件切换 全选取消状态
		$("#selectAll").click( function (){ $("#list").toggleCheckboxes(); });
		$(".tr_content").hover(
		  function(){
			  //$(this).find("td").addClass("hover");
			  $(this).find("td").css("background-color","#E8ECF0");
   //鼠标经过添加hover样式
		  },
		  function(){
			//$(this).find("td").removeClass("hover");   //鼠标离开移除hover样式
			  $(this).find("td").css("background-color","");
		  }
		);
		$(".tr_content").click(function(){
			$(this).find("input[type='checkbox']").attr("checked",function(){
				return !$(this).attr("checked");
			});
		});
		
		$('.option_checkbox').click(function(){
			$(this).attr("checked",function(){
				return !$(this).attr("checked");
			});
		});

});

  function Mdysearch(status){
	  $("#sta").val(status);
	  search.submit();
  }
