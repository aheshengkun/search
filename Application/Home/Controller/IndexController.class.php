<?php
namespace Home\Controller;
use Think\Controller;
use Org\Util\SphinxClient;
class IndexController extends Controller {
	var $index = "*";
	var $host = "localhost";
    var $port = 9312;
    var $timeout = 1;
    var $pagenum = 1;
    public function index(){
        $this->display();
    }

    private function setSysValue(){
    	$type = "搜索设置";
    	$sysconf=M('system')->where(array('type'=>$type))->select();
       		foreach ($sysconf as $key=>$val){
                if($val['nid'] == 'con_host'){   //主机名称
                    $this->host = $val['value'];
                }
                if($val['nid'] == 'con_port'){   //主机端口号
                    $this->port = $val['value'];
                }
                if($val['nid'] == 'con_timeout'){    //连接超时时间
                   $this->timeout = $val['value'];
                }
                if($val['nid'] == 'con_pagenum'){    //一页最多显示条数
                   $this->pagenum = $val['value'];
                }
            } 
    }

    public function search(){
    	$words = "";
    	$search_info = "";
    	if(isset($_GET["words"]) && !empty($_GET["words"])){
    		$words = urldecode($_GET["words"]);
    		$this->setSysValue();
    		$sp = new SphinxClient ();
       		$sp->SphinxClient();
       		$sp->SetServer ( $this->host, $this->port );
			$sp->SetConnectTimeout ( $this->timeout );
			//分页
			if(!$rs = $sp->Query( $words, $this->index )){
				die ("Error:". $sp->GetLastError());
				exit(0);
			}

			$count = $rs["total"];
			$Page  = new \Think\Page($count,$this->pagenum);    //分页数
 
			$Page->setConfig('prev', "上一页");//上一页
			$Page->setConfig('next', '下一页');//下一页
			$Page->setConfig('first', '首页');//第一页
			$Page->setConfig('last', "末页");//最后一页
			$Page->setConfig ( 'theme', '%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%' );		
			$show       = $Page->show();// 分页显示输出
			$sp->setLimits($Page->firstRow, $Page->listRows);
			$rs = $sp->Query ( $words, $this->index );
			$strs = array_keys( $rs["matches"]);
			$st = null;

			//提取key
			foreach ($strs as $key => $value) {
				$st .= "," . $value;
			}
			//得到所有文章的id
			$article_id = trim($st, ",");
			//查询mysql数据库
			$article = M("article");
			$data["id"] = array("in", $article_id);
			$search_info = "关键字为 【".$words."】 共有 ".$rs["total"]."条记录，用时".$rs["time"]."秒";
			$rows = $article->where($data)->select();
			//关键字高亮
			$opts = array();
			$opts["before_match"] = '<font color="red">';
			$opts["after_match"] = '</font>';
			$opts["chunk_separator"] = "";
			//$opts["limit"] = 128;
			
			
			foreach ($rows as $key => $value) {
				$title[] = $rows[$key]["title"];
				$content[] = $rows[$key]["content"];
				//$content[] = preg_replace('/\[[\/]?(b|img|url|color|s|hr|p|list|i|align|email|u|font|code|hide|table|tr|td|th|attach|list|indent|float).*\]/', strip_tags($rows[$key]["content"]) );
			}

			$title = $sp->BuildExcerpts($title,"mysql", $words,$opts);
			$content = $sp->BuildExcerpts($content,"mysql", $words,$opts);
			
			foreach ($rows as $k => $v) {
				$rows[$k]["title"] = $title[$k];
				$rows[$k]["content"] = $content[$k];
			}
			$this->assign("search_info", $search_info);
			$this->assign("words", $words);
			$this->assign("page", $show);
			$this->assign("list", $rows);
    	}
    	
    	$this->display();
    }
}