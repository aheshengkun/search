<?php

/**
 * 递归处理多维数组
 * Enter description here ...
 * @param $node
 * @param $pid
 */
function node_merge($node,$access=null,$pid = 0){
	$arr = array();
	foreach($node as $v){
		if(is_array($access)){
			$v['access'] = in_array($v['id'], $access)?1:0;
		}
		if($v['pid'] == $pid){
			$v['child'] = node_merge($node,$access,$v['id']);
			$arr[] = $v;
		}
	}
	return $arr;
}
/** 
*  采集图片php程序
*/ 
set_time_limit(0);
/**
* 写文件
* @param    string  $file   文件路径
* @param    string  $str    写入内容
* @param    char    $mode   写入模式
*/
function wfile($file,$str,$mode='w')
{
    $oldmask = @umask(0);
    $fp = @fopen($file,$mode);
    @flock($fp, 3);
    if(!$fp)
    {
        Return false;
    }
    else
    {
        @fwrite($fp,$str);
        @fclose($fp);
        @umask($oldmask);
        Return true;
    }
}

function savetofile($path_get,$path_save){
        @$hdl_read = fopen($path_get,'rb');
        if($hdl_read == false){
                echo("<span style='color:red'>$path_get can not get</span>");
                Return ;
        }
        if($hdl_read){
                @$hdl_write = fopen($path_save,'wb');
                if($hdl_write){
                        while(!feof($hdl_read)){
                                fwrite($hdl_write,fread($hdl_read,8192));
                        }
                        fclose($hdl_write);
                        fclose($hdl_read);
                        return 1;
                }else{
                        return 0;
                }
        }else{
                return -1;
        }
}

function getExt($path){
        $path = pathinfo($path);
        return strtolower($path['extension']);
}


/**
* 从文本中取得一维数组
* @param    string     $file_path    文本路径
*/
function getFileListData($file_path){
    $arr = @file($file_path);
    $data = array();
    if(is_array($arr) && !empty($arr)){
        foreach($arr as $val){
            $item = trim($val);
            if(!empty($item)){
                $data[] = $item;
            }
        }
    }
    Return $data;
}
/**
* URL是远程的完整图片地址，$url 为空则返回 false
* @param    string  $url   		图片的url路径
* @param    string  $filename   另存为的图片名字 
* @param    string  $save_url   写入路径
 */
function GrabImage($url, $filename="",$save_url){ 
	//$url 为空则返回 false; 
	if($url == ""){return false;} 
	$ext = strrchr($url, ".");//得到图片的扩展名 
	//if($ext != ".gif" && $ext != ".jpg" && $ext != ".bmp" && $ext != ".png"){echo "格式不支持！";return false;} 
	if($filename == ""){$filename = time().".png";}//以时间戳另起名 
	if($save_url == ""){
		//保持的路径(如果为空时路径为自动创建)
		$save_url = "uploads/shopsimages/".date("Y_m_d",time())."/";
		mkDirs($save_url);  //按日期建立文件夹
	}
	//开始捕捉 
	ob_start(); 
	readfile($url); 
	$img = ob_get_contents(); 
	ob_end_clean(); 
	$size = strlen($img);
	//dump($save_url.$filename);
	$filename_path = $save_url.$filename;
	$fp2 = fopen($filename_path,"a",$save_url); 
	fwrite($fp2,$img); 
	fclose($fp2); 
	return '/'.$filename_path;
}



/**
 * 下载xls文件
 * @param array $data 要生成xls的数据
 * @param arrat $keynames xls 各项的标题
 * @param string $name 生成xls 的文件名
 */
function downXls($data,$keynames,$name='down') { 
	$xlsdata[] = array_values($keynames); 
	$time = array('addtime','uploadtime','lastlogin','edittime','starttime','endtime','ordertime'); 
	foreach($data As $o) { 
		$line = array(); 
		foreach($keynames AS $k=>$v) { 
		  if(in_array($k,$time))$o[$k]=date("Y-m-d H:i:s",$o[$k]); 
		  $line[] = $o[$k]; 
		} 
		$xlsdata[] = $line; 
	} 
	import('Common.Org.Excel');
	$xls = new \Excel(); 
	$xls->addArray ($xlsdata); 
	$xls->generateXML ($name); 
}

//数据联动
function data_linkage($value,$nid){
	$linkage_type  = M('data_type');
	$linkage       = M('data');

	$type_id       = $linkage_type->where("nid='$nid'")->getField('id');
	if($nid=='borrow_use' || $nid=='nation' || $nid=='card_type')
		$name      = $linkage->where("id='$value' AND type_id='$type_id'")->getField('name');
	else
		$name      = $linkage->where("value='$value' AND type_id='$type_id'")->getField('name');
	return $name;
}

/**
* 添加后台操作日志和保存操作记录  
* @param    string  $action   当前操作说明
* @param    string  $result   当前操作结果
* @param    string  $sql   	  当前操作实例化的对象(如"M('user')" 或者 "$xxx" )或者需要保存的SQL语句（可多条，可选）
* 调用方式 add_Log('查看后台操作日志',$result,$user);
*/
function add_Log($action,$result,$sql='未记录sql'){
	$role_id 		= M('role_user')->where('user_id='.$_SESSION[C('USER_AUTH_KEY')])->getField('role_id');
	$role_name      = M('role')->where('id='.$role_id)->getField('name');
	if($role_name){
		$data['mess'] = $role_name.'【'.$_SESSION['AdminAccount'].'】'.$action;

	}else{
		$data['mess'] = '非法人员的操作';
	}
	$time = time();
	$data['user_id']  	= $_SESSION[C('USER_AUTH_KEY')];
	$data['query']     	= MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME;
	$data['url']		= __SELF__;
	$data['result'] 	= $result ? 1 : 0 ;
	$data['addtime'] 	= $time; 
	$data['addip']   	= get_client_ip();
	$id = M('User_log')->add($data);//写入日志表

	// // dump($sql);
	// // dump($p);exit;
	if ( is_object($sql) ) {
		$str_sql = $sql->_sql();
	} else {
		$str_sql = $sql;
	}
	$str_ = '<?php die(); ?>'."\r\n";//防下载语句
	$str = '';
	$str = $str.'日志ID:'.$id;
	$str = $str.'，操作者ID:'.$data['user_id'];
	$str = $str.'，操作者IP:'.$data['addip'];
	$str = $str.'，操作时间:'.gmdate('Y-m-d H:i:s',$time  + 3600 * 8);
	$str = $str.'，操作URL:'.$data['url'];
	$str = $str.'，操作动作:'.$data['query'];
	$str = $str.'，操作语句:'.$str_sql;
	$str = $str.'，操作结果:'.$data['result'].';'."\r\n";
	// echo $str;
	$catalog  = 'Log/'.date('Y', $time).'/'.date('m', $time);//文件目录
	$fileRoot = $catalog.'/'.date('d', $time).'.php';//文件路径
	Mk_Folder($catalog);//创建目录
	if ( !file_exists($fileRoot) ) {
		file_put_contents($fileRoot, $str_,FILE_APPEND);//文件第一次创建的时候写入防下载语句
	}
	file_put_contents($fileRoot, $str,FILE_APPEND);//写入记录文件
}
/**
* 创建 记录文件目录  
* @param    string  $Folder   文件目录字符串
*/
function Mk_Folder($Folder){
  if(!is_readable($Folder)){
	    Mk_Folder( dirname($Folder) );
	    if(!is_file($Folder)) mkdir($Folder,0777);
    }
}

/*HTML安全过滤*/
function htmtocode($content) {
	$content = str_replace('%','%&lrm;',$content);
	$content = str_replace("<", "&lt;", $content);
	$content = str_replace(">", "&gt;", $content);            
	$content = str_replace("\n", "<br/>", $content);
	$content = str_replace(" ", "&nbsp;", $content);
	$content = str_replace('"', "&quot;", $content);
	$content = str_replace("'", "&#039;", $content);
	$content = str_replace("$", "&#36;", $content);
	$content = str_replace('}','&rlm;}',$content);
	return $content;
}


/* 获取ip + 地址
 * $justcity 是否只获取所在地区名称 默认否
*/
function get_ip_dizhi($justcity=false,$ip=null){
	$opts = array(
		'http'=>array('method'=>"GET",'timeout'=>5,)
	);		
	$context = stream_context_create($opts); 
	

	if($ip){
		$ipmac = $ip;
	}else{
		// $ipmac=_get_ip();
		$ipmac=get_client_ip();
		if(strpos($ipmac,"127.0.0.") === true)return '';		
	}
	
	$url_ip='http://ip.taobao.com/service/getIpInfo.php?ip='.$ipmac;
	$str = @file_get_contents($url_ip, false, $context);
	if(!$str) return "";
	$json=json_decode($str,true);
	if($json['code']==0){
		
		$json['data']['region'] = addslashes(htmtocode($json['data']['region']));
		$json['data']['city'] = addslashes(htmtocode($json['data']['city']));
		
		// $ipaddr= $json['data']['region'].$json['data']['city'];//获取所在省，市
		$ipaddr= $json['data']['region'];//只获取所在省
		$ip= $ipaddr.','.$ipmac;
	}else{
		$ip="";
	}
	if($justcity){
		return $ipaddr;//只返回地区名称
	}else{
		return $ip;//返回地区名称+ip
	}
}

?>