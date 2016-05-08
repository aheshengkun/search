<?php
namespace Admin\Controller;
use Think\Controller;
/*
 * 后台客服管理控制器
 */
class KefuController extends CommonController{
	//客服列表
	public  function index(){
		$user = M("user");
		$d = A('CheckInput');
		//保存搜索条件
		$condition = array();
		$condition['account'] = trim($d->in('客服名','account','cnennumstr','','0','16'));
		$condition['nickname'] = trim($d->in('真实姓名','nickname','cnennumstr','','0','16'));
		$condition['email'] = trim($d->in('邮箱','email','email','','0','50'));
		$condition['p'] = $d->in('当前分页数','p','intval','1','0','11');
		//组合条件
		$wherelist = array();
		if(!empty($condition['account'])){
			$wherelist[] = "a.account = '{$condition['account']}'";
		}
		if(!empty($condition['nickname'])){
		    $wherelist[] = "a.nickname = '{$condition['nickname']}'";
	    }
	    if(!empty($condition['email'])){
			$wherelist[] = "a.email = '{$condition['email']}'";
	   	}
		//组装存在的查询条件
		if(count($wherelist) > 0){
			$where = implode(' AND ' , $wherelist); 
		}
		$where 	  = isset($where) ? $where." AND b.role_id = '3'" : "b.role_id = '3'";

		$count  = $user->alias('a')->join('left join '.C('DB_PREFIX').'role_user as b ON a.id = b.user_id')->where($where)->count();// 查询满足要求的总记录数
		$page   = new \Think\Page($count,20);// 实例化分页类

		$result   = $user->alias('a')->join('left join '.C('DB_PREFIX').'role_user as b ON a.id = b.user_id')->where($where)->field('a.*')->limit($page->firstRow.','.$page->listRows)->order('a.create_time')->select();

		//在此一次获取客服副表中所需数据，去掉原来的模板中获取
		//获取当前页所有客服的id
		$idarr = array();
		foreach($result as $key=>$val){
			foreach ($val as $k => $v) {
				if($k == 'id'){
					$idarr[][$key] = $v;
				}
			}
		}

		//分页保持查询条件
		foreach ($condition as $key => $val) {
			$page->parameter[$key] = $val;
		}
		$show   = $page->show();// 分页显示输出	
		$this->assign('kefudata',$result);
		$this->assign('page',$show);			//分配分页
		$this->assign('condition',$condition);	//保持查询条件不消失
		$this->display();
	}

	//修改客服状态（已通过/未审核）
	public function setState(){
		$db = M("user");
		$d = A('CheckInput');
		$id = $d->in('客服id','user_id','intval','','1','11');
		$type = $d->in('审核类型','type','string','','1','6');
		if(isset($id)){
			if(!$res = $db->field('id,status')->find($id)) $this->error('不存在此客服！');
			if($type == 'ok'){
				$result = $db->where(array('id'=>$id))->setField('status','1');
				add_Log('修改客服"id='.$id.'"的状态为-已通过',$result,$db);//写入日志 add by：JZ
				if($result){
					redirect($_SERVER["HTTP_REFERER"]);		//跳转到上个页面
				}else{
					$this->error('修改失败！');
				}
			}elseif($type == 'cancel'){
				$result = $db->where(array('id'=>$id))->setField('status','0');
				add_Log('修改客服"id='.$id.'"的状态为-未审核',$result,$db);//写入日志 add by：JZ
				if($result){
					redirect($_SERVER["HTTP_REFERER"]);		//跳转到上个页面
				}else{
					$this->error('修改失败！');
				}
			}else{
				$this->error('非法操作！');
			}
		}else{
			$this->error('非法操作！');
		}
	}
	
	//添加客服页面
	public function addKefu(){
		$this->display();
	}
	//获取城市和地区
	public function Ajaxarea()
	{
		$d      = A('CheckInput');
		$Areaid = $d->in('省份id','id','intval','','1','11');
		$list   = M('area')->where("pid='$Areaid'")->select();
		echo json_encode($list);
	}
	
	//添加客服表单数据
	public function addKefuInsert(){
		$db =  M('user');
		$str_sql = '';
		// 手动进行令牌验证 
		if (!$db->autoCheckToken($_POST)){
			$this->error('请不要重复提交',U(MODULE_NAME.'/Kefu/addKefu'));
		}

		$sqlok = true;			//语句是否执行成功的标记
		$db->startTrans();		//开启事务
		$time                   = time();
		$d                		= A('CheckInput');
		$data['account']     	= $d->in('客服名','username','cnennumstr','','1','16');
		$data['password'] 		= md5($d->in('密码','password','string','','1','30'));
		$password1 				= md5($d->in('重复密码','password1','string','','1','30'));
		if($data['password'] !== $password1){
			$this->error('两次密码输入不一致',U(MODULE_NAME.'/Kefu/addKefu'));
		}
		$data['nickname'] 		= $d->in('真实姓名','realname','cnennumstr','','1','16');
		$data['create_time'] 	= $time;
		$data['last_login_time']= $time;
    	$data['last_login_ip'] 	= get_client_ip();
		$data['remark'] 		= $d->in('备注说明','remark','string','','0','60');
		$role_id 				= $d->in('所属角色id','role_id','numstr','','1','2');
    	if($role_id == C('RBAC_SUPPERADMIN_ID')){
    		$this->error('非法操作！');
    	}
    	if($sqlok !== false){
			$uid = $db->add($data);
			$str_sql = $str_sql.$db->_sql().';';//获取操作语句，仅供日志记录使用
		}
    	if(($sqlok = $uid) !== false){
	    	$role      = array('role_id'=>$role_id,'user_id'=>$uid);
	    	$role_user = M('role_user');
	    	$sqlok     = $role_user->add($role);		//保存到角色和用户中间表
	    	$str_sql   = $str_sql.$role_user->_sql().';';//获取操作语句，仅供日志记录使用
	    }
		if($sqlok !== false){
			$db->commit();		//提交事务
			add_Log('添加新客服"'.$data['account'].'"',1,$str_sql);//写入日志
			$this->success('客服添加成功',U(MODULE_NAME.'/Kefu/index'));
		}else{
			$db->rollback();
			add_Log('添加新客服"'.$data['account'].'"',0,$str_sql);//写入日志
			//$this->error('客服添加失败',U(MODULE_NAME.'/Kefu/index'));
			redirect(U(MODULE_NAME.'/Kefu/index'), 2, '客服添加失败!');
		}
	}
	
	//修改客服
	public function kefuEdit(){
		$user = M("user");
		$user->startTrans();		//开启事务
		$sqlok = true;				//事务是否执行成功的标志
		$d  = A('CheckInput');
		$id  = $d->in('客服id','user_id','intval','','1','11');
		if(IS_POST){
			$str_sql = '';
			//如果填写密码
			if(!empty($_POST['password'])){
				$data['password'] = md5($d->in('密码','password','string','','1','30'));
				$password1 		  = md5($d->in('重复密码','password1','string','','1','30'));
				if($data['password'] !== $password1){
					$this->error('两次密码输入不一致',U(MODULE_NAME.'/Kefu/kefuEdit'));
				}
			}
			$data['account']     	= $d->in('客服名','username','string','','1','60');
			$data['nickname'] 		= $d->in('真实姓名','realname','string','','0','20');
			$data['update_time'] 	= time();		//修改时间
			$data['remark'] 		= $d->in('备注说明','remark','string','','0','60');
			if($sqlok !== false){
				$sqlok = $user->where(array('id'=>$id))->data($data)->save();
				$str_sql = $str_sql.$user->_sql().';';//获取操作语句，仅供日志记录使用
			}

			if($sqlok !== false){
				$user->commit();		//提交事务
				add_Log('修改客服"'.$data['account'].'"账号信息',1,$str_sql);//写入日志 add by：JZ
				$this->success('客服修改成功',U(MODULE_NAME.'/Kefu/index'));
			}else{
				$user->rollback();
				add_Log('修改客服"'.$data['account'].'"账号信息',0,$str_sql);//写入日志 add by：JZ
				$this->error('客服修改失败',U(MODULE_NAME.'/Kefu/kefuEdit'));
			}
		}else{
			$res   = M('user')->alias('a')->join('left join '.C('DB_PREFIX').'usermeta as b ON a.id = b.user_id')->where('a.id = "'.$id.'" OR b.user_id = "'.$id.'" ')->field('a.*,b.meta_key,b.meta_value')->order('a.id')->select();
			foreach ($res as $k => $v){
				$result = $v;
				$result2[$v['meta_key']] = $v['meta_value'];
			}
			$this->assign("info", $result);			//主表信息
			$this->assign("infometa", $result2);	//副表信息
			$this->display();
		}
	}
	
	//删除客服?是否同时删除客户服务表 '.C('DB_PREFIX').'customer_service中对应数据待定！
	public function kefuDelete(){
		$user 	  = M('user');
		$usermeta = M('usermeta');
		$str_sql  = '';
		$user->startTrans();		//开启事务
		$sqlok    = true;				//判断事务是否成功的标志
		$d  	  = A('CheckInput');
		$id       = $d->in('客服id','user_id','intval','','1','11');
		//获取此客服头像地址,无需再添加任何前缀地址
		$photo   = $usermeta->where(array('user_id'=>$id,'meta_key'=>'photo'))->find();

		if($sqlok !== false){
			$sqlok = $user->where(array('id'=>$id))->delete();
			$str_sql = $str_sql.$user->_sql().';';//获取操作语句，仅供日志记录使用
		}
		//删除客服其他字段表的数据
		if($sqlok !== false){
			$sqlok = $usermeta->where(array('user_id'=>$id))->delete();
			$str_sql = $str_sql.$usermeta->_sql().';';//获取操作语句，仅供日志记录使用
		}
		//同时删除角色——用户表对应记录
		if($sqlok !== false){
			$role_user = M('role_user');
			$sqlok     = $role_user->where(array('user_id'=>$id))->delete();
			$str_sql   = $str_sql.$role_user->_sql().';';//获取操作语句，仅供日志记录使用
		}
		//同时删除客户服务表customer_service此客服所有记录
		if($sqlok !== false){
			$customer_service = M('customer_service');
			$sqlok     = $customer_service->where(array('kefu_id'=>$id))->delete();
			$str_sql   = $str_sql.$customer_service->_sql().';';//获取操作语句，仅供日志记录使用
		}
		
		if($sqlok !== false){
			$user->commit();		//提交事务
			//同时删除此客服的头像照片
			if(file_exists($photo['meta_value'])){
				@unlink($photo['meta_value']);
			}
			add_Log('删除客服"id='.$id.'"的账号',1,$str_sql);//写入日志 add by：JZ
			$this->success('删除客服成功',U(MODULE_NAME.'/Kefu/index'));
		}else{
			$user->rollback();
			add_Log('删除客服"id='.$id.'"的账号',0,$str_sql);//写入日志 add by：JZ
			$this->error('删除客服失败');
		}
	}


	//查看专属用户信息
	public function kefuByCustomer(){
		$d  	  = A('CheckInput');
		$id       = $d->in('客服id','user_id','intval','','1','11');
		//获取客服名
		$search['name'] 	  =  urldecode($d->in('客服名','name','string','','1','100'));
		//限制只能查看本客服下的用户，其他客服不能查看其他人的,其他角色除外
		if(session('role_id') == '9'){
			//如果session中的用户id不是传入的id,并且排除一个经理id=59,经理可查看其他人的
			if(session('user_id') != $id && session('user_id') != '59'){
				$this->error('对不起，您不能查看其他客服的专属用户');
				exit();
			}
		}
		$search['searchoption'] = $d->in('搜索条件','searchoption','string','','0','60');
		$search['searchvalue'] = trim($d->in('条件值','searchvalue','string','','0','60'));
		$search['dotime1']  = $d->in('开始时间','dotime1','date','','0','10');
		$search['dotime2'] = $d->in('结束时间','dotime2','date','','0','10');
		$search['user_id'] = $id;
		$search['p'] = $d->in('当前分页数','p','intval','1','0','11');
		$search['pg'] = $d->in('上级分页','pg','intval','1','0','11');

		$map = "a.kefu_id = '".$id."'";
		if (!empty($search['searchoption']) && ($search['searchoption']=='name' || $search['searchoption']=='nicename')) {
			$map .= " and b.".$search['searchoption']." LIKE  '%".$search['searchvalue']."%'";
		}elseif(!empty($search['searchoption'])){
			$map .= " and b.".$search['searchoption']." = '".$search['searchvalue']."' ";
		}
		if (!empty($search['dotime1'])) {
			$map .= " and a.kefu_addtime >= '" . strtotime($search['dotime1']) . "'";
		}
		if (!empty($search['dotime2'])) {
			$map .= " and a.kefu_addtime <= '" . strtotime($search['dotime2']) . "'";
		}

		$db = M('customer_service');
		$count  = $db->alias('a')->join('left join '.C('DB_PREFIX').'customer as b ON a.customer_id = b.id')->where($map)->count();// 查询满足要求的总记录数

		//增加列表下面显示用户总数，不随分页改变
		$total = $db->alias('a')->join('left join '.C('DB_PREFIX').'customer as b ON a.customer_id = b.id')->where("a.kefu_id = '".$id."'")->count();// 不随分页改变的用户总数
		$page   = new \Think\Page($count,20);// 实例化分页类
		//获取此客服下的所有用户列表
		$list = $db->alias('a')->join('left join '.C('DB_PREFIX').'customer as b ON a.customer_id = b.id')->where($map)->field('a.kefu_addtime,b.id,b.name,b.nicename,b.email,b.phone,b.card_id,b.type,b.status')->order('a.kefu_addtime DESC')->limit($page->firstRow.','.$page->listRows)->select();
		//获取所有投资用户id
		$arrcid = M('customer_service')->where(array('kefu_id'=>$id))->field('customer_id')->select();
		$customer_ids = arr2str($arrcid);
		$usermeta = M('customermeta')->where("customer_id IN(".$customer_ids.")")->select();

		//获取用户副表某些信息
		foreach ($list as $key => $val) {
			$list[$key]['page'] = $search['p'];
			foreach ($usermeta as $k => $v) {
				if($v['meta_key'] == 'area' && $val['id'] == $v['customer_id']){
					if($v['meta_value'] != ''){
						$list[$key]["areas"] = area($v['meta_value']);
					}
				}
			}
		}

		foreach ($search as $key => $val) {
			$page->parameter[$key] = $val;
			//为下载XLS按钮分配分页条件
			$down .= "$key=" . $val. "&";
		}
		$show   = $page->show();// 分页显示输出	
		$this->assign('customerdata',$list);
		$this->assign('user_id',$id);//专属客服id
		$this->assign('page',$show);// 赋值分页输出
		$this->assign("search", $search);//分配查询条件，查询后模板保留查询条件
		$this->assign('down', $down);
		$this->assign('total', $total);// 用户总数
		$this->display();
	}

	//搜索查看专属客户投资统计信息
	public function searchInvestInfo(){
		$d  	  = A('CheckInput');
		$id       = $d->in('客服id','user_id','intval','','1','11');
		//获取客服名
		$name 	  =  urldecode($d->in('客服名','name','string','','1','100'));
		$search['dotime1']  = $d->in('开始时间','dotime1','date','','0','10');
		$search['dotime2'] = $d->in('结束时间','dotime2','date','','0','10');
		$search['user_id'] = $id;
		// $search['p'] = $d->in('当前分页数','p','intval','1','0','11');
		$search['pg'] = $d->in('上级分页','p','intval','1','0','11');

		if (!empty($search['dotime1'])) {
			$map .= " and addtime >= '" . strtotime($search['dotime1']) . "'";
		}
		if (!empty($search['dotime2'])) {
			$map .= " and addtime <= '" . strtotime($search['dotime2']) . "'";
		}

		//获取所有投资用户id
		$arrcid = M('customer_service')->where(array('kefu_id'=>$id))->field('customer_id')->select();
		$customer_ids = arr2str($arrcid);	//转为逗号分隔的字符串

		$borrow_tender = M('borrow_tender');
		$result = $borrow_tender->where("customer_id IN(".$customer_ids.")")->field('id,money')->select();
		//遍历获取用户总投资额
		$totalmoney = 0;
		foreach ($result as $value) {
			$totalmoney += $value['money'];
		}

		//搜索得到的投资额,
		$searchsum = $borrow_tender->where("customer_id IN(".$customer_ids.")".$map)->field('money')->sum('money');
		//搜索得到的用户数，只有投资了的才包含进来
		$searchtotal  = $borrow_tender->where("customer_id IN(".$customer_ids.")".$map)->field('id')->group('customer_id')->select();
		$searchtotal = count($searchtotal);

		$this->assign('pg', $search['pg']);		//上级页面的分页数
		$this->assign('user_id',$id);//专属客服id
		$this->assign('kefuname',$name);	//当前客服名
		$this->assign("search", $search);//分配查询条件，查询后模板保留查询条件
		$this->assign('total', count($arrcid));			//总专属用户数
		$this->assign('totalmoney', $totalmoney);		//用户投资总额
		$this->assign('searchtotal', $searchtotal);		//搜索得到的用户数
		$this->assign('searchmoney', $searchsum);		//搜索得到的投资额
		$this->display();
	}

	//每个用户的投资明细和总数,默认要显示按时间倒序排序的所有投资列表
	public function investInfo(){
		$d             = A('CheckInput');
		$seach['customer_id'] = $d->in('用户id','customer_id','intval','','1','11');
		$seach['user_id'] = $d->in('客服id','user_id','intval','','0','11');
		//获取客服名
		$seach['name'] 	  =  urldecode($d->in('客服名','name','string','','0','100'));
		if(empty($seach['name'])){
			$seach['name'] = M('user')->where(array('id'=>$seach['user_id']))->getField('account');
		}
		$seach['dotime1']  = $d->in('搜索年月时间','dotime1','string','','0','10');
		$years = substr($seach['dotime1'], 0,4);//投标年份
		$months = substr($seach['dotime1'], 5);//投标月份
		$seach['p']	   = $d->in('当前分页数','p','intval','1','0','11');
		$seach['pg']   = $d->in('上级分页','pg','intval','1','0','11');

		$map      = "1 = 1 and a.customer_id = ".$seach['customer_id'];
		if(!empty($years) && !empty($months)){
			$maptime  = array();
			//获取指定年月开始和结束时间戳
			$maptime  = $this->getFristAndLast($years,$months);
			$map .= " and a.addtime >= '".$maptime['firstday']."'";
			$map .= " and a.addtime <= '".$maptime['lastday']."'";
		}

		$borrow_tender = M('borrow_tender');
		$info = $borrow_tender->alias('a')->join('left join '.C('DB_PREFIX').'borrow as b ON a.borrow_id = b.id')->join('left join '.C('DB_PREFIX').'customer as c ON c.id = a.customer_id')->where($map)->field('a.borrow_id,a.afew as tzafew,a.addtime as tbtime,a.interest,a.maturities,a.repay_status,a.cardstate,a.money,b.title,b.borrow_type,b.time_limit,b.end_time,b.apr,c.name')->group('a.id')->page($seach['p'].',20')->order('a.addtime desc')->select();
		$count         = $borrow_tender ->alias('a')->join('left join '.C('DB_PREFIX').'borrow as b ON a.borrow_id = b.id')->join('left join '.C('DB_PREFIX').'customer as c ON c.id = a.customer_id')->where($map)->count();//记录总数

		$borrowtotal = $borrow_tender->alias('a')->where($map)->sum('a.money');
		$borrowtotal = $borrowtotal == '' ? 0 : $borrowtotal;

		$Page          = new \Think\Page($count,20);// 实例化分页类
		foreach ($seach as $key => $val) {
			$Page->parameter[$key] = $val;
			//为下载XLS按钮分配分页条件
			$down .= "$key=" . $val. "&";
		}
		
		$show          = $Page->show();// 分页显示输出
		$this->assign('page',$show);// 赋值分页输出
		$this->assign('info',$info);// 赋值数据集
		$this->assign('down',$down);
		$this->assign('years',$years);		//投标年份
		$this->assign('months',$months);		//投标月份
		$this->assign('seach',$seach);		//所有查询参数或搜索条件
		$this->assign('total',$count);// 月份投标总数
		$this->assign('borrowtotal',$borrowtotal);// 月份投标总额
		$this->display();
	}

	/**
	* 获取指定月份的第一天开始和最后一天结束的时间戳
	* @param int $y 年份 $m 月份
	* @return array(本月开始时间，本月结束时间)
	*/
	private function getFristAndLast($y="",$m=""){
		if($y=="") $y=date("Y");
		if($m=="") $m=date("m");
		$m=sprintf("%02d",intval($m));
		$y=str_pad(intval($y),4,"0",STR_PAD_RIGHT);
		$m>12 || $m<1 ? $m=1 : $m=$m;
		$firstday=strtotime($y.$m."01000000");
		$firstdaystr=date("Y-m-01",$firstday);
		$lastday = strtotime(date('Y-m-d 23:59:59', strtotime("$firstdaystr +1 month -1 day")));
		return array("firstday"=>$firstday,"lastday"=>$lastday);
	}

	//下载专属客服-用户表，导出数据为excel
	public function down(){
		$d  	  = A('CheckInput');
		$id       = $d->in('客服id','user_id','intval','','1','11');
		//获取客服名
		$search['name'] 	  =  urldecode($d->in('客服名','name','string','','1','100'));
		//限制只能查看本客服下的用户，其他客服不能查看其他人的,其他角色除外
		// if(session('role_id') == '9'){
		// 	if(session('user_id') != $id){
		// 		$this->error('对不起，您不能查看其他客服的专属用户');
		// 		exit();
		// 	}
		// }
		$search['searchoption'] = $d->in('搜索条件','searchoption','string','','0','60');
		$search['searchvalue'] = trim($d->in('条件值','searchvalue','string','','0','60'));
		$search['dotime1']  = $d->in('开始时间','dotime1','date','','0','10');
		$search['dotime2'] = $d->in('结束时间','dotime2','date','','0','10');
		$search['user_id'] = $id;
		$search['p'] = $d->in('当前分页数','p','intval','1','0','11');
		// $search['pg'] = $d->in('上级分页','pg','intval','1','0','11');

		$map = "a.kefu_id = '".$id."'";
		if (!empty($search['searchoption']) && ($search['searchoption']=='name' || $search['searchoption']=='nicename')) {
			$map .= " and b.".$search['searchoption']." LIKE  '%".$search['searchvalue']."%'";
		}elseif(!empty($search['searchoption'])){
			$map .= " and b.".$search['searchoption']." = '".$search['searchvalue']."' ";
		}
		if (!empty($search['dotime1'])) {
			$map .= " and a.kefu_addtime >= '" . strtotime($search['dotime1']) . "'";
		}
		if (!empty($search['dotime2'])) {
			$map .= " and a.kefu_addtime <= '" . strtotime($search['dotime2']) . "'";
		}

		$db = M('customer_service');
		$count  = $db->alias('a')->join('left join '.C('DB_PREFIX').'customer as b ON a.customer_id = b.id')->where($map)->count();// 查询满足要求的总记录数
		$page   = new \Think\Page($count,20);// 实例化分页类
		//获取此客服下的所有用户列表
		$list = $db->alias('a')->join('left join '.C('DB_PREFIX').'customer as b ON a.customer_id = b.id')->where($map)->field('a.kefu_addtime,b.id,b.name,b.nicename,b.email,b.phone,b.card_id,b.card_type,b.examine,b.type,b.status,b.last_login_time,b.last_login_ip')->order('a.kefu_addtime DESC')->limit($page->firstRow.','.$page->listRows)->select();
		//获取所有投资用户id
		$arrcid = M('customer_service')->where(array('kefu_id'=>$id))->field('customer_id')->select();
		$customer_ids = arr2str($arrcid);
		$usermeta = M('customermeta')->where("customer_id IN(".$customer_ids.")")->select();

		//获取用户副表某些信息
		foreach ($list as $key => $val) {
			// $list[$key]['page'] = $search['p'];
			$list[$key]['sex'] = get_user_meta($val['id'],'sex','1');
			foreach ($usermeta as $k => $v) {
				if($v['meta_key'] == 'area' && $val['id'] == $v['customer_id']){
					if($v['meta_value'] != ''){
						$list[$key]["area"] = area($v['meta_value']);
					}
				}
			}
		}

        foreach ($list as $key => $value) {
        	foreach ($value as $k => $v) {
        		if($k=='id'){$str_id = $str_id.$v.';';}//被下载数据的id  仅供日志记录使用
        		if($k == 'sex'){
		            switch ($value['sex']) {
		            	case '0':
		            		$list[$key]['sex'] = '女';
		            		break;
		            	case '1':
		            		$list[$key]['sex'] = '男';
		            		break;
		            	default:
		            		$list[$key]['sex'] = '未设置';
		            		break;
		            }
	        	}
	        	if($k == 'kefu_addtime'){
	        		$list[$key]['kefu_addtime'] = date('Y-m-d H:i:s',$value['kefu_addtime']);
	        	}
	        	if($k == 'last_login_time'){
	        		$list[$key]['last_login_time'] = date('Y-m-d H:i:s',$value['last_login_time']);
	        	}
	        	if($k == 'card_id'){
	        		$list[$key]['card_id'] = empty($value['card_id']) ? '无' : " ".$value['card_id'];
	        	}
	        	if($k == 'card_type'){
	        		switch ($value['card_type']) {
	        			case '385':
	        				$list[$key]['card_type'] = '身份证';
	        				break;
	        			case '386':
	        				$list[$key]['card_type'] = '军官证';
	        				break;
	        			case '387':
	        				$list[$key]['card_type'] = '台胞证';
	        				break;
	        			default:
	        				$list[$key]['card_type'] = '无';
	        				break;
	        		}
	        	}
	        	if($k == 'examine'){
	        		switch ($value['examine']) {
	        			case '0':
	        				$list[$key]['examine'] = '未申请';
	        				break;
	        			case '1':
	        				$list[$key]['examine'] = '审核通过';
	        				break;
	        			case '2':
	        				$list[$key]['examine'] = '审核中';
	        				break;
	        			case '-1':
	        				$list[$key]['examine'] = '审核不通过';
	        				break;
	        			default:
	        				$list[$key]['examine'] = '错误状态';
	        				break;
	        		}
	        	}
	        	if($k == 'status'){
	        		switch ($value['status']) {
	        			case '1':
	        				$list[$key]['status'] = '启用';
	        				break;
	        			case '0':
	        				$list[$key]['status'] = '禁用';
	        				break;
	        			default:
	        				$list[$key]['status'] = '错误状态';
	        				break;
	        		}
	        	}
	        	if($k == 'type'){
	        		switch ($value['type']) {
	        			case '0':
	        				$list[$key]['type'] = '未知类型';
	        				break;
	        			case '1':
	        				$list[$key]['type'] = '正式账号';
	        				break;
	        			case '2':
	        				$list[$key]['type'] = '测试账号';
	        				break;
	        			default:
	        				$list[$key]['type'] = '未知类型';
	        				break;
	        		}
	        	}
        	}
        }

        $keynames = array(
            'id' => '序号',
            'name' => '用户名',
            'nicename' => '真实姓名',
            'sex' => '性别',
            'email' => '电子邮箱',
            'phone' => '手机号码',
            'area'=>'所在地',
            'card_type' => '证件类型',
            'card_id' => '证件号码',
            'examine' => '实名认证状态',
            'type' => '账号类型',
            'status' => '状态',
            'kefu_addtime' => '客服添加时间',
            'last_login_time' => '最后登录时间',
            'last_login_ip'=>'最后登录IP'
        );
        add_Log('下载了"专属客服('.urldecode($search['name']).')的用户"表',1,'数据id—'.$str_id);//写入日志 add by：JZ
        downXls($list, $keynames, '专属客服('.urldecode($search['name']).')-用户表');
    }

    //下载客服专属用户月份投资详情列表
    public function downLoanList(){
		$d             = A('CheckInput');
		$seach['customer_id'] = $d->in('用户id','customer_id','intval','','1','11');
		$seach['user_id'] = $d->in('客服id','user_id','intval','','0','11');
		//获取客服名
		$seach['name'] 	  =  urldecode($d->in('客服名','name','string','','0','100'));
		if(empty($seach['name'])){
			$seach['name'] = M('user')->where(array('id'=>$seach['user_id']))->getField('account');
		}
		$seach['dotime1']  = $d->in('搜索年月时间','dotime1','string','','0','10');
		$years = substr($seach['dotime1'], 0,4);//投标年份
		$months = substr($seach['dotime1'], 5);//投标月份
		$seach['p']	   = $d->in('当前分页数','p','intval','1','0','11');
		// $seach['pg']   = $d->in('上级分页','pg','intval','1','0','11');

		$map      = "1 = 1 and a.customer_id = ".$seach['customer_id'];
		if(!empty($years) && !empty($months)){
			$maptime  = array();
			//获取指定年月开始和结束时间戳
			$maptime  = $this->getFristAndLast($years,$months);
			$map .= " and a.addtime >= '".$maptime['firstday']."'";
			$map .= " and a.addtime <= '".$maptime['lastday']."'";
		}

		$borrow_tender = M('borrow_tender');
		// $count         = $borrow_tender ->alias('a')->join('left join '.C('DB_PREFIX').'borrow as b ON a.borrow_id = b.id')->join('left join '.C('DB_PREFIX').'customer as c ON c.id = a.customer_id')->where($map)->count();//记录总数
		// $Page          = new \Think\Page($count,20);// 实例化分页类 
		// $info = $borrow_tender->alias('a')->join('left join '.C('DB_PREFIX').'borrow as b ON a.borrow_id = b.id')->join('left join '.C('DB_PREFIX').'customer as c ON c.id = a.customer_id')->where($map)->field('a.id,a.borrow_id,a.afew as tzafew,a.addtime as tbtime,a.interest,a.maturities,a.repay_status,a.cardstate,a.borrow_type,a.money,b.title,b.time_limit,b.apr,c.name')->group('a.id')->page($seach['p'].',20')->order('a.addtime desc')->select();

		$info = $borrow_tender->alias('a')->join('left join '.C('DB_PREFIX').'borrow as b ON a.borrow_id = b.id')->join('left join '.C('DB_PREFIX').'customer as c ON c.id = a.customer_id')->where($map)->field('a.id,a.borrow_id,a.money,a.addtime as tbtime,a.interest,a.repay_status,a.cardstate,a.money,b.title,b.borrow_type,b.time_limit,b.end_time,b.apr,c.name')->order('a.addtime desc')->select();

		foreach ($info as $key => $value) {
			$info[$key]['tbtime'] = date('Y-m-d H:i:s',$value['tbtime']);
			$info[$key]['end_time'] = date('Y-m-d H:i:s',$value['end_time']);
			$str_id = $str_id.$info[$key]['id'].';';//被下载数据的id  仅供日志记录使用
			switch ($info[$key]['borrow_type']) {
				case '1':
					$info[$key]['borrow_type'] = '流转标';
					$info[$key]['time_limit'] = $value['time_limit'].'个月';
					break;
				case '2':
					$info[$key]['borrow_type'] = '给力标(月)';
					$info[$key]['time_limit'] = $value['time_limit'].'个月';
					break;
				case '3':
					$info[$key]['borrow_type'] = '秒标';
					$info[$key]['time_limit'] = $value['time_limit'].'个月';
					break;
				case '4':
					$info[$key]['borrow_type'] = '给力标(天)';
					$info[$key]['time_limit'] = $value['time_limit'].'天';
					break;
				case '5':
					$info[$key]['borrow_type'] = '净值标';
					$info[$key]['time_limit'] = $value['time_limit'].'个月';
					break;
				case '6':
					$info[$key]['borrow_type'] = '信用标';
					$info[$key]['time_limit'] = $value['time_limit'].'个月';
					break;
				case '7':
					$info[$key]['borrow_type'] = '抵押标';
					$info[$key]['time_limit'] = $value['time_limit'].'个月';
					break;
				case '8':
					$info[$key]['borrow_type'] = '分期金融';
					$info[$key]['time_limit'] = $value['time_limit'].'个月';
					break;
				default:
					$info[$key]['borrow_type'] = '无';
					$info[$key]['time_limit'] = $value['time_limit'].'个月';
					break;
			}			
			switch ($info[$key]['repay_status']) {
				case '0':
					$info[$key]['repay_status'] = '未还';
					break;
				case '1':
					$info[$key]['repay_status'] = '已还';
					break;
				default:
					$info[$key]['repay_status'] = '未知状态';
					break;
			}
			// switch ($info[$key]['cardstate']) {
			// 	case '0':
			// 		$info[$key]['cardstate'] = '未发卡';
			// 		break;
			// 	case '1':
			// 		$info[$key]['cardstate'] = '邮寄途中';
			// 		break;
			// 	case '2':
			// 		$info[$key]['cardstate'] = '已收到';
			// 		break;
			// 	default:
			// 		$info[$key]['cardstate'] = '未收到';
			// 		break;
			// }
		}
		$keynames = array(
            'borrow_id' => '标ID',
            'name' => '投标人',
            'borrow_type' => '标类型',
            'title' => '借款标题',
            'money'=>'投资金额',
            // 'tzafew' => '投资份数',
            'apr' => '利率',
            'time_limit' => '借款期限',
            'interest' => '预期收益',
            'tbtime' => '投资时间',
            'end_time' => '到期时间',
            'repay_status' => '还款状态'
            // 'cardstate' => '投资卡状态' 
        );
        add_Log('下载了"专属客服'.urldecode($seach['name']).'的用户'.$years.'年'.$months.'月份投资详情"表',1,'数据id—'.$str_id);//写入日志 add by：JZ
        downXls($info, $keynames, '专属客服('.urldecode($seach['name']).')-用户'.$years.'年'.$months.'月份投资详情表');
    }
}