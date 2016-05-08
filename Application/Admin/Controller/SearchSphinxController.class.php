<?php
namespace Admin\Controller;
use Think\Controller;
use Org\Util\SphinxClient;
class SearchSphinxController extends CommonController {
  
    public function index(){
       	$cl = new SphinxClient ();
       	$cl->SphinxClient();
		    $status = $cl->Status();
		    $result = 0;	//表示 searchd 服务没有启动
		    if($status){
			     $result = 1;	//表示 searchd 服务启动了
		    }
		    $this->assign('result',$result);
		    $this->assign('status',$status);
		    $this->display();
    }

    /**
    * 分词测试
    * Enter description here ...
    */
    public function participle()
    {
    	  $cl = new SphinxClient ();
       	$cl->SphinxClient();
       	$words = "";  //分词内容
       	$outkeywords = "";  //分词后的内容结果
       	$keywords = array();
       	if(isset($_GET["words"])){
       		$words = urldecode($_GET["words"]);  
       		$keywords = $cl->BuildKeywords ( $words, "mysql", false ); //获取分词结果
       		foreach($keywords as $k=>$keyword){
				//$outkeywords = $outkeywords."tokenized = ".mb_convert_encoding($keyword['tokenized'], "GBK", "UTF-8")."<br/>"; 
				//$outkeywords = $outkeywords."normalized = ".mb_convert_encoding($keyword['normalized'], "GBK", "UTF-8")."<br/>";
				  $outkeywords = $outkeywords."tokenized = ".$keyword['tokenized']."<br/>"; 
				  $outkeywords = $outkeywords."normalized = ".$keyword['normalized']."<br/>";
				  $outkeywords = $outkeywords."******************************"."<br/>";
			    } 
       	}       
       	$this->assign('words',$words);	
       	$this->assign('outkeywords',$outkeywords);	
    	  $this->display();
    }

    /**
    * 搜索设置
    * Enter description here ...
    */
    public function searchSet(){
      $this->display();
    }
}