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