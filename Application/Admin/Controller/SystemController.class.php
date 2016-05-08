<?php
namespace Admin\Controller;
use Think\Controller;
class SystemController extends CommonController {
    public function index(){
		$CK=A('CheckInput');
		$type=urldecode($CK->in('配置类别','get.type','string','','0','60'));
		$data=M('system')->where(array('type'=>$type))->select();
		$this->assign('configdata',$data);
		$this->display();
    }
	public function save(){
		$CK  		= A('CheckInput');
		$list 		= $CK->in('参数列表','post.list','htcontent','','1','0');
		$typeneme 	= $CK->in('配置类别','typeneme','string','','0','30');
		$list 		= split(',',$list);
		$list 		= array_flip(array_flip($list));
		$modle 		= M('system');
		$str_sql 	= '';
		foreach($list as $key=>$str){
			$where['nid'] 	= $str;
			$data['value']	= $CK->in('参数值',$str,'htcontent');
			if( ($modle->where($where)->data($data)->save()) === false ){
				$str_sql = $str_sql.$modle->_sql().';';//执行到中间失败时也记录之前成功的sql
				add_Log('编辑了'.$typeneme,0,$str_sql);//写入日志 
				$this->error('提交失败,请重试！');
			}
			$str_sql = $str_sql.$modle->_sql().';';//要保存的sql语句
		}
		add_Log('编辑了“'.$typeneme.'”',1,$str_sql);//写入日志 
		$this->saveConfig();//保存配置为文件格式
		$this->success('提交成功！');
	}

	public function add(){
		if(!IS_POST){
			$typelist=M('system')->field('type')->group('type')->select();
			$this->assign('typelist',$typelist);
			$this->display();
		}else{
			$CK=A('CheckInput');
			$data['type']   = $CK->in('所属分类','type','cnennumstr','','1','16');
			$data['nid']    = $CK->in('变量名','nid','ennumstr','','1','32');
			$data['name']   = $CK->in('参数名','name','cnennumstr','','1','32');
			$data['style']  = $CK->in('显示类别','style','enstr','','1','16');
			$data['status'] = $CK->in('状态','status','bool','1','1','4');
			switch($data['style']){
				case 'string':
				case 'text':
				case 'pic':
					$data['value']=$CK->in('参数值','value1','cnennumstr');
					break;
				case 'bool':
				default:
					$data['value']=$CK->in('参数值','value2','bool');			
			}
			$result = M('system')->add($data);
			add_Log('添加了系统参数，键名为"'.$data['nid'].'"',$result,M('system'));//写入日志 
			if( $result ){
				$this->saveConfig();//保存配置为文件格式
				$this->success('提交成功！');
			}else{
				$this->error('提交失败,请重试！');
			}
		}
	}
	public function edit(){
		if(!IS_POST){
			$CK=A('CheckInput');
			$where['id']=$CK->in('id','id','intval','','1','16');
			
			$modle=M('system');
			$data=$modle->where($where)->limit(1)->select();
			$typelist=$modle->field('type')->group('type')->select();
			$this->assign('typelist',$typelist);
			$this->assign('data',$data[0]);
			$this->display();
		}else{
			$CK=A('CheckInput');
			$where['id'] 		= $CK->in('编辑id','id','intval','','1','16');
			$data['type'] 		= $CK->in('所属分类','type','cnennumstr','','1','16');
			$data['nid'] 		= $CK->in('变量名','nid','ennumstr','','1','32');
			$data['name'] 		= $CK->in('参数名','name','cnennumstr','','1','32');
			$data['style'] 		= $CK->in('显示类别','style','enstr','','1','16');
			$data['status'] 	= $CK->in('状态','status','bool','1','1','4');
			switch($data['style']){
				case 'string':
				case 'text':
				case 'pic':
					$data['value']=$CK->in('参数值','value1','cnennumstr');
					break;
				case 'bool':
				default:
					$data['value']=$CK->in('参数值','value2','bool');			
			}
			$result = M('system')->where($where)->data($data)->save();
			add_Log('修改了系统参数，键名为"'.$data['nid'].'"',$result,M('system'));//写入日志 
			if( $result ){
				$this->saveConfig();//保存配置为文件格式
				$this->success('提交成功！');
			}else{
				$this->error('提交失败,请重试！');
			}
		}
	}
	
	//后台操作日志 
	public function sysLog(){
		$d = A('CheckInput');
		$data['nickname']  = trim($d->in('真实姓名','nickname','string','','0','16'));
		$data['searchoption'] = $d->in('搜索条件','searchoption','string','','0','60');
		//将分页按钮GET传入条件值数据的'/'替换为竖线'|',解决分页按钮传参错误。
		$data['searchvalue'] = str_replace('/','|',trim($d->in('条件值','searchvalue','string','','0','255')));
		$data['p'] = $d->in('当前分页数','p','intval','1','0','11');

		$map = "1=1 ";
		if(!empty($data['nickname'])){
			$map .= "and p2.nickname = '".$data['nickname']."' ";//真实姓名
		}
		if(!empty($data['searchoption']) && !empty($data['searchvalue'])){
			//条件值加入查询条件时将竖线'|'又替换为斜杠，
			$map .= "and p1.".$data['searchoption']." = '".str_replace('|','/',$data['searchvalue'])."'";
		}

		// if (IS_POST) {
		// 	$CK                = A('CheckInput');
		// 	$data['nickname']  = trim($CK->in('真实姓名','nickname','string','','0','16'));
		// 	$data['query']     = trim($CK->in('处理信息','query','string','','0','100'));
		// 	$data['query'] 	   = str_replace('/', '_', $data['query']);
		// 	if ( empty($data['nickname']) && empty($data['query']) ) {
		// 		$this->error('请填写搜索条件！');
		// 	}
		// 	// dump($data);die;
		// 	$map1 = "p2.nickname = '".$data['nickname']."'";//真实姓名
		// 	$map2 = "p1.mess LIKE '%".$data['query']."%' or p1.url LIKE '%".$data['query']."%' or p1.query LIKE '%".$data['query']."%' or p1.addip LIKE '%".$data['query']."%'";//关键字
		// 	if( empty($data['query']) ){
		// 		$map = $map2;
		// 	}else if( empty($data['nickname']) ){
		// 		$map = $map2;
		// 	}else{
		// 		$map = $map1.' and '.$map2;
		// 	}
			
		// }

		$user_log = M('User_log');
		$count    = $user_log->alias('p1')->join(C('DB_PREFIX')."user as p2 on p1.user_id = p2.id")->where($map)->count();
		$Page     = new \Think\Page($count,20);
		$limit 	  = $Page->firstRow . ',' . $Page->listRows;
		$list     = $user_log->alias('p1')->join(C('DB_PREFIX')."user as p2 on p1.user_id = p2.id")->field("p1.*,p2.account,p2.nickname")->where($map)->order("p1.addtime desc")->limit($limit)->select();
		foreach ($data as $key => $val) {
			$Page->parameter[$key] = (string) $val;
		}
		$show  = $Page->show();
		
		$this->assign("search", $data);
		$this->assign("list", $list);
		$this->assign("page", $show);
		$this->display();
	}

	/**
	 * 清除缓存
	 * @access public
	 * @return null
	 * @author hcf 20150123
	 */
	public function clearCache(){
		import('Common.Org.Dir');
		$handler=new \Dir();
		$handler->delDir(LOG_PATH);// 应用日志目录
		$handler->delDir(TEMP_PATH);// 应用缓存目录
		$handler->delDir(DATA_PATH);// 应用数据目录
		$handler->delDir(CACHE_PATH);// 应用模板缓存目录
		$this->success('删除缓存成功!',U('Index/index'));
	}

	/**
	 * 保存系统配置为文件格式
	 * @access private
	 * @return array
	 * @author hcf 20150123
	 */
	private function saveConfig(){
		$config=M()->table(C('DB_PREFIX').'system')->field('nid,name,value')->select();
		if(empty($config)){
			$return['message']='系统配置记录为空';
			$return['status']=false;
			return $return;
		}

		$file=CONF_PATH.'sysConfig.php';
		$content="<?php\r\n";
        $content.="\$sysconfig=array(\r\n";
        foreach($config as $k=>$v){
            $content.="\t'".$v['nid']."'=>'".$v['value']."',//".$v['name']."\r\n";
        }
        unset($config);
        $content.= ");\r\n";
        $content.= "\r\n?>";
		$_s=file_put_contents($file, $content);

		if($_s){
			$return['message']='保存系统配置文件成功!';
			$return['status']=true;
		}else{
			$return['message']='保存系统配置文件失败!请检查是否有写权限?请在'.CONF_PATH.'目录下手动建sysConfig.php并赋予写权限!';
			$return['status']=false;
		}
		add_Log($return['message'],$return['status']);
		return $return;
	}
}