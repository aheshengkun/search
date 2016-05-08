<?php
namespace Admin\Controller;
use Think\Controller;
class CustomerController extends CommonController{
	//用户列表
	public  function index(){
		$d = A('CheckInput');
		$search['searchoption'] = $d->in('搜索条件','searchoption','string','','0','60');
		$search['searchvalue'] = trim($d->in('条件值','searchvalue','string','','0','60'));
		$search['status'] = $d->in('用户状态','status','intval','','0','1');
		$search['p'] = $d->in('当前分页数','p','intval','1','0','11');

		$map      = "1=1 ";
		if (!empty($search['searchoption']) && ($search['searchoption']=='name' || $search['searchoption']=='nicename')) {
			$map .= " and a.".$search['searchoption']." =  '".$search['searchvalue']."'";
		}elseif(!empty($search['searchoption'])){
			$map .= " and a.".$search['searchoption']." = '".$search['searchvalue']."' ";
		}
		if ($search['status'] != '') {
			$map .= " and a.status = '".$search['status']."'";
		}

		$customer = M("customer");
		$count  = $customer->where($map)->count();// 查询满足要求的总记录数
		$page   = new \Think\Page($count,20);// 实例化分页类
		// $list = $customer->where($map)->order('reg_time desc')->limit($page->firstRow.','.$page->listRows)->select();
		$list = $customer->field('a.id,a.name,a.nicename,a.email,a.email_state,a.phone,a.phone_status,a.card_id,a.reg_time,a.status,a.type,b.customer_id,kefu_id')->alias('a')->join('left join '.C('DB_PREFIX').'customer_service as b ON a.id = b.customer_id')->where($map)->order('a.reg_time desc')->limit($page->firstRow.','.$page->listRows)->select();
		$userdb = M('user');
		//获取用户副表某些信息,新添加用户的专属客服
		foreach ($list as $key => $val) {
			// $list[$key]['sex'] = get_user_meta($val['id'],'sex','1');
			// $list[$key]['qq'] = get_user_meta($val['id'],'qq','1');
			//$list[$key]['area'] = area(get_user_meta($val['id'],'province','1'),'area','1').area(get_user_meta($val['id'],'city','1'),'area','1').area(get_user_meta($val['id'],'area','1'),'area','1');
			$area=get_user_meta($val['id'],'area','1');
			if(!empty($area)){
				$list[$key]['area'] = area($area,'area','1');
			}
			$list[$key]['kefu'] = $userdb->where(array('id'=>$val['kefu_id']))->getField('account');//用户的专属客服
		}
		foreach ($search as $key => $val) {
			$page->parameter[$key] = $val;
			//为下载XLS按钮分配分页条件
			$down .= "$key=" . $val. "&";
		}
		// p($list);
		$show   = $page->show();// 分页显示输出	
		$this->assign('userdata',$list);
		$this->assign('page',$show);// 赋值分页输出
		$this->assign("search", $search);//分配查询条件，查询后模板保留查询条件
		$this->assign('down', $down);
		$this->display();
	}
	
	//添加用户
	public function addUser(){
		$province  = M('area')->where("pid='0'")->select();  //省份
		//证件类型
		$card_type = M('data');
		$alltype = $card_type->alias('a')->join('left join '.C('DB_PREFIX').'data_type as b ON a.type_id = b.id')->where('b.nid = "card_type"')->order('a.id')->getField('a.id,a.value,a.name');
		$this->assign("province", $province);
		$this->assign("card_type", $alltype);
		$this->display();
	}
	//获取城市和地区
	public function Ajaxarea()
	{
		$d                = A('CheckInput');
		$Areaid           = $d->in('省份id','id','intval','','1','11');
		$list   = M('area')->where("pid='$Areaid'")->select();
		echo json_encode($list);
	}
	
	//添加客户
	public function addUserInsert(){
		$User = M("customer"); // 实例化User对象 
		// 手动进行令牌验证 
		if ($User->autoCheckToken($_POST)){
			$meta = array(
					'sex'       => '性别',
					'birthday'  => '生日',
					'province'  => '省份',
					'city'      => '城市',
					'area'      => '地区',
					'qq'        => 'QQ号码',
					'tel'       => '家庭电话',
					'address'   => '详细地址'
			);
			$d                = A('CheckInput');
			$data['name']     = $d->in('用户名','username','cnennumstr','','1','16');
			$data['password'] = md5($d->in('密码','password','string','','1','64'));
			$data['nicename'] = $d->in('真实姓名','realname','cnennumstr','','0','16');
			$data['email']    = $d->in('邮箱','email','email','','1','50');
			$data['phone']    = $d->in('手机号码','phone','numstr','','0','11');
			$data['card_type']= $d->in('证件类型','card_type','string','','0','4');
			$data['card_id']  = $d->in('证件号码','card_id','string','','0','20');
			$data['status']   = $d->in('状态','status','numstr','','0','2');
			$data['type']     = $d->in('账号类型','type','intval','','0','2');
			$data['reg_time'] = time();

			//验证用户名是否重复
			if($User->where(array('name'=>$data['name']))->getField('name') != ''){
				$this->error('用户名重复，请换一个');
			}
			//验证邮箱是否重复
			if($User->where(array('email'=>$data['email']))->getField('email') != ''){
				$this->error('邮箱重复，请换一个');
			}
			//验证手机号是否重复
			if($User->where(array('phone'=>$data['phone']))->getField('phone') != ''){
				$this->error('手机号重复，请换一个');
			}
			//验证证件号码是否重复
			if($User->where(array('card_id'=>$data['card_id']))->getField('card_id') != ''){
				$this->error('证件号码重复，请换一个');
			}

			$customer_id = $User->data($data)->add();
			
			/*if($customer_id){
				foreach ($meta as $k=>$v){
					update_user_meta($customer_id,$k,$d->in($v,$k,'string','','0','0'));
				}
				$this->success('用户注册成功');
			}*/
			if($customer_id){
				foreach ($meta as $k=>$v){
					update_user_meta($customer_id,$k,$d->in($v,$k,'string','','0','0'));
				}
				//$this->success('用户注册成功');
				//资金记录表添加记录
				$result_amount      =M('amount')->add(array('customer_id'=>$customer_id));
				if($result_amount){
					$this->success('添加用户成功！');
					M()->commit();          //提交事务
				}else{
					M()->rollback(); //事物回滚
					$this->error('添加用户失败！');
				}
			}else{
				M()->rollback(); //事物回滚
				$this->error('添加用户失败！');
			}
		}
	}
	
	//编辑客户
	function userEdit(){
		//表单提交
		$customer           = M("customer"); // 实例化User对象
		$d  = A('CheckInput');
		$id  = $d->in('用户id','user_id','intval','','1','11');
		/**
		* modify by zrh 2014-12-5 13:20:41 用户id改为在最开始统一获取，改为判断是否post提交，post提交里面再判断是否重复提交
		*/
		if(!IS_POST){
			//显示个人详细信息
			$area = M('area');
			$province  = $area->where("pid='0'")->select();  //省份
			//证件类型
			$card_type = M('data');
			$alltype = $card_type->alias('a')->join('left join '.C('DB_PREFIX').'data_type as b ON a.type_id = b.id')->where('b.nid = "card_type"')->order('a.id')->getField('a.id,a.value,a.name');
			$this->assign("province", $province);
			$this->assign("card_type", $alltype);

			//获取此用户id的数据
			$result = $customer->where(array('id'=>$id))->select();
			//modify by zrh 2014-12-30 11:42:29在此一次获取用户副表(customermeta)中所需数据
			$metalist = M('customermeta')->field('customer_id,meta_key,meta_value')->where(array('customer_id'=>$id))->select();

			//获取sex,qq,tel,address,area,province,city,birthday，并遍历加入到$result数组中
			foreach ($result as $k => $v) {
				foreach ($metalist as $key => $value) {
					if($value['meta_key'] == 'sex' && $value['customer_id'] == $v['id']){
						$result[$k]['sex'] = $value['meta_value'];
					}
					if($value['meta_key'] == 'qq' && $value['customer_id'] == $v['id']){
						$result[$k]['qq'] = $value['meta_value'];
					}
					if($value['meta_key'] == 'tel' && $value['customer_id'] == $v['id']){
						$result[$k]['tel'] = $value['meta_value'];
					}
					if($value['meta_key'] == 'address' && $value['customer_id'] == $v['id']){
						$result[$k]['address'] = $value['meta_value'];
					}
					if($value['meta_key'] == 'area' && $value['customer_id'] == $v['id']){
						$result[$k]['area'] = $value['meta_value'];
					}
					if($value['meta_key'] == 'province' && $value['customer_id'] == $v['id']){
						$result[$k]['province'] = $value['meta_value'];
					}
					if($value['meta_key'] == 'city' && $value['customer_id'] == $v['id']){
						$result[$k]['city'] = $value['meta_value'];
					}
					if($value['meta_key'] == 'birthday' && $value['customer_id'] == $v['id']){
						$result[$k]['birthday'] = $value['meta_value'];
					}
				}
			}
	        $city      = $area->where(array('pid'=>$result[0]['province']))->select(); //根据省份获取城市
	        $area      = $area->where(array('pid'=>$result[0]['city']))->select();     //根据城市获取地区  
	        $this->assign("info", $result[0]);
			$this->assign("city", $city);
			$this->assign("area", $area);
			$this->display();
		}else{
			if(!$customer->autoCheckToken($_POST)){
				$this->error('请勿重复提交',$_SERVER['HTTP_REFERER']);
			}else{
				$meta = array(
					'sex'       => $d->in('性别','sex','intval','','0','1'),
					'birthday'  => $d->in('生日','birthday','string','','0','16'),
					'province'  => $d->in('省份','province','intval','','0','10'),
					'city'      => $d->in('城市','city','intval','','0','10'),
					'area'      => $d->in('地区','area','intval','','0','10'),
					'qq'        => $d->in('QQ号码','qq','intval','','0','10'),
					'tel'       => $d->in('家庭电话','tel','string','','0','12'),
					'address'   => $d->in('详细地址','address','string','','0','150')
				);
				
				$password = $d->in('密码','password','string','','0','64');
				if($password){
					$data['password'] = md5($password);
				}
				
				$data['nicename'] = $d->in('真实姓名','realname','cnennumstr','','0','16');
				$data['email']    = $d->in('邮箱','email','email','','0','50');
				$data['phone']    = $d->in('手机号码','phone','numstr','','0','11');
				$data['card_type']= $d->in('证件类型','card_type','string','','0','4');
				$data['card_id']  = $d->in('证件号码','card_id','string','','0','20');
				$data['type']     = $d->in('账号类型','type','intval','','0','2');
				$data['status']   = $d->in('状态','status','numstr','','0','2');

				//获取原用户的信息，
				$userinfo = $customer->lock(true)->where(array('id'=>$id))->field('email,phone,card_id')->find();
				//验证邮箱是否重复
				$email = $customer->where(array('email'=>$data['email']))->getField('email');
				//如果根据提交邮箱查数据库不为空且值不为原值
				if($email != '' && $email !== $userinfo['email']){
					$this->error('邮箱重复，请换一个');
				}
				//验证手机号是否重复
				$phone = $customer->where(array('phone'=>$data['phone']))->getField('phone');
				if($phone != '' && $phone !== $userinfo['phone']){
					$this->error('手机号重复，请换一个');
				}
				//验证证件号码是否重复
				$cardid = $customer->where(array('card_id'=>$data['card_id']))->getField('card_id');
				if($cardid != '' && $cardid !== $userinfo['card_id']){
					$this->error('证件号码重复，请换一个');
				}

				//开启事务
				M()->startTrans();
				$sqlok = true;			//数据库操作成功标志

				$sqlok = $customer->where(array('id'=>$id))->save($data);
				if($sqlok !== false){
					$customermeta = M('customermeta');
					foreach ($meta as $key => $value) {
						if($sqlok === false){
							break;	//退出循环
						}
						$arr['meta_value'] = $value;
						$sqlok = $customermeta->where(array('customer_id'=>$id,'meta_key'=>$key))->save($arr);
					}
				}

				if($sqlok !== false){
					M()->commit();		//提交事务
					add_Log('编辑用户"id='.$id.'"的资料',1,$customer);//写入日志 add by：JZ
					$this->success('修改成功!',U(MODULE_NAME.'/Customer/index'));
				}else{
					M()->rollback();		//回滚事务
					add_Log('编辑用户"id='.$id.'"的资料',0,$customer);//写入日志 add by：JZ
					$this->error('修改失败!');
				}
			}
		}
	}

	//删除用户 
	//功能尚未完善,涉及到多个表,且一般不给删除用户,故注释禁用此功能. by hcf
	/*public function delUser(){
		$d  = A('CheckInput');
		$id = $d->in('用户id','user_id','intval','','1','11');
		$customer = M('customer');
		//开启事务，
		$customer->startTrans();
		$sqlok = true;			//数据库操作成功标志
		$sqlok = $customer->delete($id);
		if($sqlok){
			$customermeta = M('customermeta');
			//判断用户其他字段值表是否存在此用户数据
			if($customermeta->where(array('customer_id'=>$id))->select()){
				$sqlok = $customermeta->where(array('customer_id'=>$id))->delete();
			}else{
				//如果此表没有值直接提交
				$sqlok = true;
			}
		}

		if($sqlok){
			$customer->commit();		//提交事务
			$this->success('删除用户成功',U(MODULE_NAME.'/Customer/index'));
		}else{
			$customer->rollback();		//回滚事务
			$this->error('删除用户失败');
		}
	}*/
	
	//实名认证
	public function shiMingRenZheng(){
		$d = A('CheckInput');
		$search['searchoption'] = $d->in('搜索条件','searchoption','string','','0','60');
		$search['searchvalue'] = trim($d->in('条件值','searchvalue','string','','0','60'));
		$search['examine'] = $d->in('认证状态','examine','intval','','0','2');
		$search['p'] = $d->in('当前分页数','get.p','intval','1','0','11');

		$map      = "examine <> 0 ";
		if(!empty($search['searchoption']) && !empty($search['searchvalue'])){
			$map .= " and ".$search['searchoption']." = '".$search['searchvalue']."' ";
		}
		if ($search['examine'] != '') {
			$map .= " and examine = '".$search['examine']."'";
		}

		$customer = M("customer");
		$count  = $customer->where($map)->count();// 查询满足要求的总记录数
		$page   = new \Think\Page($count,20);// 实例化分页类

		$list = $customer->where($map)->order('examine_time DESC')->limit($page->firstRow.','.$page->listRows)->select();
		foreach ($search as $key => $val) {
			$page->parameter[$key] = $val;
		}

		$show       = $page->show();// 分页显示输出
		$this->assign("userlist", $list);
		$this->assign("page", $show);
		// $this->assign("realname", $result);
		$this->assign("search", $search);			//分配查询条件，查询后模板保留查询条件
		$this->display();
	}

	//add by zrh 2014-12-25 16:12:56 查看实名认证证件图片
	public function imgBox(){
		$d = A('CheckInput');
		$id = $d->in('用户id','id','intval','','1','11');
		$type = $d->in('查看正背面类型','type','string','','1','2');

		//将数据缓存60秒
		if(!$data = S('cardpic'.$id)){
			$data = M('customer')->where(array('id'=>$id))->field('card_pic1,card_pic2')->find();
			S('cardpic'.$id,$data,60);
		}

		// $data = M('customer')->where(array('id'=>$id))->field('card_pic1,card_pic2')->find();
		if($type == 'zm'){
			//正面图片
			$this->assign('cardpic',$data['card_pic1']);
		}else{
			//背面图片
			$this->assign('cardpic',$data['card_pic2']);
		}
		$this->display();
	}

	//实名验证操作
	public function UpdateAudit()
	{
		$d      = A('CheckInput');
		$userid          = $d->in('用户id','userid','intval','','1','11');
		if(IS_POST){
			$data['examine'] = $d->in('验证状态','status','intval','','1','2');
			//不通过认证时必须填写说明
			if($data['examine'] == '-1'){
				$data['explain'] = $d->in('说明原因','explain','string','','1','255');
			}else{
				$data['explain'] = $d->in('说明原因','explain','string','','0','255');
			}
			
			$customer        = M('customer');
			if ($customer->autoCheckToken($_POST)) {
				if($data['examine'] == '-1'){
					session('examine', -1);		//将登陆时session中的实名认证状态改为-1
				}
				$result          = $customer->where("id = '{$userid}'")->save($data);
				if($result){
					//实名验证积分活动
					M()->startTrans();//开启事务
					$creditsHandleReturn=creditsHandle($userid,'realname');
					if($creditsHandleReturn['status']){
						M()->commit();//事务提交
					}else{
						//失败时,写日志,方便排查错误
						add_Log('对用户id='.$userid.'进行实名认证审核失败,失败原因为:'.$creditsHandleReturn['message'],false);
						M()->rollback();//事务回滚
					}

					$this->success('数据保存成功');
				}else{
					$this->error('数据保存失败');
				}
			}else{
				$this->error('令牌验证错误');
			}
		}else{
			$this->assign('user_id',$userid);
			$this->display();
		}
	}
	
	//推广提成（功能暂未开发）
	public function invite(){
		$d                    = A('CheckInput');
		$data['name']         = $d->in('搜索的用户名','username','cnennumstr','','0','16');
		$data['invite_name']  = $d->in('搜索的额度范围','type','cnennumstr','','0','16');
		$data['real_status']  = $d->in('搜索的额度','amount','string','','0','16');
		
		$this->display();
		
	}
	
	//vip认证列表
	public  function vip(){
		$d = A('CheckInput');
		$search = array();
		$search['searchoption'] = $d->in('搜索条件','searchoption','string','','0','60');
		$search['searchvalue'] = trim($d->in('条件值','searchvalue','string','','0','60'));
		$search['vip_status'] = $d->in('VIP状态','vip_status','intval','','0','1');
		$search['p'] = $d->in('当前分页数','get.p','intval','1','0','11');

		$map      = "1=1 ";
		if (!empty($search['searchoption']) && !empty($search['searchvalue'])) {
			$map .= " and c.".$search['searchoption']." =  '".$search['searchvalue']."'";
		}
		if ($search['vip_status'] != '') {
			$map .= " and v.vip_status = '".$search['vip_status']."'";
		}
		$vip              = M('vip');
		// 查询满足要求的总记录数
		$count       = $vip->alias('v')->join('left join '.C('DB_PREFIX').'customer as c ON v.customer_id = c.id')->where($map)->count();
		$Page     = new \Think\Page($count,20);// 实例化分页类
		$list        = $vip->alias('v')->join('left join '.C('DB_PREFIX').'customer as c ON v.customer_id = c.id')->where($map)->order('v.addtime DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
		foreach ($search as $key => $val) {
			$Page->parameter[$key] = $val;
		}
		$show       = $Page->show();// 分页显示输出

		$this->assign("viplist", $list);
		$this->assign("page", $show);
		$this->assign("search", $search);		//保持搜索条件不消失
		$this->display();
	}

	//VIP 审核操作
	public function UpdateVip(){
		$d           = A('CheckInput');
		$vipid       = $d->in('vip用户id','vipid','intval','','1','11');
		$customer_id = $d->in('用户id','customer_id','intval','','1','11');

		if(IS_POST){
			$time = time();
			$str_sql 	 = '';
			$data['vip_status'] = $d->in('验证状态','status','intval','','1','2');
			$vip 				= M('Vip');
			$emptyMode 			= M();
			$emptyMode->startTrans();//开启事务

			if ($vip->autoCheckToken($_POST)) {
				//vip 收费金额
				$vip_money = M('system')->where(array('nid'=>'con_vip_money'))->getField('value');
				//扣除费用
				$amount = M('amount');          //用户资金表
				$amountid = $amount->where(array('customer_id'=>$customer_id))->getField('id');
	            //锁表查询
	            $amountdata = $amount->lock(true)->where(array('id'=>$amountid))->find();
	            
	            if($amountdata['cash']-$vip_money < 0){
	            	$emptyMode->rollback();
	            	$this->error('冻结金额不足以扣掉vip费用，请联系管理员');
	            }

				if($data['vip_status'] == 1){
					//审核通过
					$amdata['total']     = array('exp',"total-'{$vip_money}'");//总金额-vip费用
					$amdata['cash']      = array('exp',"cash-'{$vip_money}'");//冻结金额
				}else{
					//审核不通过，获取vip申请表数据
					$vipinfo = $vip->where(array('v_id'=>$vipid))->find();
					$amdata['cash']      = array('exp',"cash-'{$vip_money}'");//冻结金额
					$amdata['use_money'] = array('exp',"use_money+'{$vipinfo['from_use_money']}'");//可用的返还
					$amdata['received']  = array('exp',"received+'{$vipinfo['from_received']}'");//回款的返还
				}
				$amount_res = $amount->where(array('id'=>$amountid))->save($amdata);

				$str_sql    = $str_sql.$amount->_sql().';';//获取操作语句，仅供日志记录使用
				//添加资金记录
				$log['customer_id']  = $customer_id;
				$log['type']         = "vip";

				if($data['vip_status'] == 1){
					$log['money']     = $vip_money;		//操作金额
					$log['total']     = $amountdata['total']-$vip_money;		//总金额
					$log['cash']      = $amountdata['cash']-$vip_money;		//冻结金额
					$log['use_money'] = $amountdata['use_money'];//可用金额
					$log['received']  = $amountdata['received'];//回款金额
					$log['remark']    = "vip审核通过费用扣除";
				}else{
					//审核不通过
					$log['money']     = $vip_money;		//操作金额
					$log['total']     = $amountdata['total'];		//总金额
					$log['cash']      = $amountdata['cash']-$vip_money;		//冻结金额
					$log['use_money'] = $amountdata['use_money']+$vipinfo['from_use_money'];//可用金额
					$log['received']  = $amountdata['received']+$vipinfo['from_received'];//回款金额
					$log['remark']    = "vip审核不通过费用退还";
				}
				$log['collection']   = $amountdata['collection'];
				$log['to_user']      = "0";
				$log['addtime']      = $time;
				$log['addip']        = get_client_ip();
				$log['trade_no']     = $customer_id; //
				// $emptyMode->rollback(); 		//事务回滚
				// p($log);die;

				$amount_log = M('amount_log');
				$result_amount_log   = $amount_log->add($log);
				$str_sql    		 = $str_sql.$amount_log->_sql().';';//获取操作语句，仅供日志记录使用

				$data['verify_user'] = session('user_id');
				$data['verify_time'] = $time;
				$result          	 = $vip->where("v_id = '{$vipid}'")->save($data);
				$str_sql    		 = $str_sql.$vip->_sql().';';//获取操作语句，仅供日志记录使用

				if(($amount_res !== false) && ($result_amount_log !== false) && ($result !== false)){
					$emptyMode->commit();		    //提交事务
					$this->success('数据保存成功');
				}else{
					$emptyMode->rollback(); 		//事务回滚
					$this->error('数据保存失败');
				}
			}else{
				$this->error('令牌验证错误');
			}
			exit;	
		}else{
			$this->assign('vipid',$vipid);
			$this->assign('customer_id',$customer_id);
			$this->display();
		}
	}

	/*
	 * 礼品申请管理
	 */
	public function gift(){
		$d                   = A('CheckInput');
		$search['dotime1']   = $d->in('开始时间','dotime1','date','','0','10');
		$search['dotime2']   = $d->in('结束时间','dotime2','date','','0','10');
		$search['username']  = $d->in('用户名','username','cnennumstr','','0','16');
		$search['status']    = $d->in('状态','status','intval','','0','2');
		$search['p']    	 = $d->in('当前分页数','get.p','intval','1');	//当前分页数
		$map      = "1=1 ";
		if (isset($search['dotime1']) && $search['dotime1'] != "") {
			$map .= " and g.addtime >= '" . strtotime($search['dotime1']) . "'";
		}
		if (isset($search['dotime2']) && $search['dotime2'] != "") {
			$map .= " and g.addtime <= '" . strtotime($search['dotime2']) . "'";
		}
		if (!empty($search['username'])) {
			$map .= " and c.name LIKE  '%".$search['username']."%'";
		}
		if ($search['status'] != '') {
			$map .= " and g.status = '".$search['status']."'";
		}
		
		$gift  = M('gift');
		// $count = $gift->where('1=1')->count();// 查询满足要求的总记录数
		$count = $gift->alias('g')->join('Left JOIN __CUSTOMER__ c ON c.id = g.customer_id')->where($map)->count();// 查询满足要求的总记录数
		$Page  = new \Think\Page($count,20);// 实例化分页类
		// $list  = $gift->alias('g')->join('Left JOIN __CUSTOMER__ c ON c.id = g.customer_id')->where($map)->field('c.id,c.name,g.*')->order('addtime DESC')->limit($Page->firstRow.','.$Page->listRows)->select();

		//add by zrh 2014-12-24 10:45:55 增加显示申请礼品用户的专属客服
		$list  = $gift->alias('g')->join('left join '.C('DB_PREFIX').'customer as c ON c.id = g.customer_id')->join('left join '.C('DB_PREFIX').'customer_service as s ON s.customer_id = g.customer_id')->join('left join '.C('DB_PREFIX').'user as u ON u.id = s.kefu_id')->where($map)->field('c.name,g.*,u.account as kefu,u.nickname')->order('g.addtime DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
		foreach ($search as $key => $val) {
			$Page->parameter[$key] = urlencode($val);
			$down .= "$key=" .urlencode($val). "&";		//下载xls表格分页条件
		}

		$show  = $Page->show();// 分页显示输出// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$this->assign('list',$list);// 赋值数据集
		$this->assign('page',$show);// 赋值分页输出
		$this->assign('search',$search);
		$this->assign('down',$down);	//下载按钮分页条件
		$this->display();
	}

	//录入快递信息
	public function editGift(){
		if(!IS_POST){
			$customer_id = (int) $_GET['customer_id'];
			$gift        = M('gift');
			$list        = $gift->where("customer_id = '{$customer_id}'")->find();
			$this->assign('item',$list);
			$this->display();
		}else{
			$d                   = A('CheckInput');
			$data['express']     = $d->in('快递公司','express','string','','1','100');
			$data['express_no']  = $d->in('快递单号','express_no','string','','1','100');
			$data['status']      = 1;
			$customer_id         = $d->in('用户id','customer_id','intval','','1');
			$gift                = M('gift');
			$result              = $gift->where("customer_id = '{$customer_id}'")->save($data);
			add_Log('为用户"id='.$customer_id.'"录入快递信息',$result ,$gift);//写入日志 add by：JZ
			if($result){
				$this->success('数据提交成功');
			}else{
				$this->error('数据提交失败');
			}
		}
	}
	
	//ajax检查用户 by hcf
	public function checkUser(){
		if(!IS_AJAX) exit;
		$customer=M('customer');
		$d= A('CheckInput');
		$name= $d->in('用户名','username','string','','1','60');
		$result=$customer->lock(true)->where(array('name'=>$name))->find();
		if($result) echo "1";
		else echo "0";
	}

	//ajax检查证件号码 by zrh 2014-11-18 15:17:39
	public function checkCardId(){
		if(!IS_AJAX) exit;
		$customer=M('customer');
		$d= A('CheckInput');
		$card_id= $d->in('证件号码','card_id','string','','1','60');
		$result=$customer->lock(true)->where(array('card_id'=>$card_id))->getField('card_id');
		if($result) echo "1";
		else echo "0";
	}

	//ajax检查邮箱 by hcf
	public function checkEmail(){
		if(!IS_AJAX) exit;
		$customer=M('customer');
		$d= A('CheckInput');
		$name= $d->in('邮箱','email','string','','1','60');
		$result=$customer->lock(true)->where(array('email'=>$name))->find();
		if($result) echo "1";
		else echo "0";
	}	

	//ajax检查手机 by hcf
	public function checkPhone(){
		if(!IS_AJAX) exit;
		$customer=M('customer');
		$d= A('CheckInput');
		$name= $d->in('手机','phone','string','','1','60');
		$result=$customer->lock(true)->where(array('phone'=>$name))->find();
		if($result) echo "1";
		else echo "0";
	}

	//下载用户列表，导出数据为excel
	public function down(){
		$d = A('CheckInput');
		$search['searchoption'] = $d->in('搜索条件','searchoption','string','','0','60');
		$search['searchvalue'] = trim($d->in('条件值','searchvalue','string','','0','60'));
		$search['status'] = $d->in('用户状态','status','intval','','0','1');

		$map      = "1=1 ";
		if (!empty($search['searchoption']) && ($search['searchoption']=='name' || $search['searchoption']=='nicename')) {
			$map .= " and ".$search['searchoption']." LIKE  '%".$search['searchvalue']."%'";
		}elseif(!empty($search['searchoption'])){
			$map .= " and ".$search['searchoption']." = '".$search['searchvalue']."' ";
		}
		if ($search['status'] != '') {
			$map .= " and status = '".$search['status']."'";
		}

		$customer = M("customer");
		$count  = $customer->where($map)->count();// 查询满足要求的总记录数
		$page   = new \Think\Page($count,20);// 实例化分页类
		// $list = $customer->where($map)->order('reg_time desc')->limit($page->firstRow.','.$page->listRows)->select();

		$list = $customer->field('a.id,a.name,a.nicename,a.email,a.email_state,a.phone,a.phone_status,a.card_type,a.card_id,a.reg_time,a.status,a.examine,a.last_login_time,a.last_login_ip,b.customer_id,kefu_id')->alias('a')->join('left join '.C('DB_PREFIX').'customer_service as b ON a.id = b.customer_id')->where($map)->order('a.reg_time desc')->limit($page->firstRow.','.$page->listRows)->select();
		$userdb = M('user');
		//获取用户副表某些信息
		foreach ($list as $key => $val) {
			$list[$key]['sex'] = get_user_meta($val['id'],'sex','1');
			$list[$key]['qq'] = get_user_meta($val['id'],'qq','1');
			// $list[$key]['area'] = area($val['id'],'area','1');
			$list[$key]['area'] = area(get_user_meta($val['id'],'province','1'),'area','1').area(get_user_meta($val['id'],'city','1'),'area','1').area(get_user_meta($val['id'],'area','1'),'area','1');
			$list[$key]['kefu'] = $userdb->where(array('id'=>$val['kefu_id']))->getField('account');//用户的专属客服
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
		            }
	        	}
	        	if($k == 'reg_time'){
	        		$list[$key]['reg_time'] = date('Y-m-d H:i:s',$value['reg_time']);
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
        	}
        }
        $keynames = array(
            'id' => '序号',
            'name' => '用户名',
            'nicename' => '真实姓名',
            'kefu' => '专属客服',
            'sex' => '性别',
            'status' => '状态',
            'email' => '电子邮箱',
            'phone' => '手机号码',
            'area'=>'所在地',
            'card_type' => '证件类型',
            'card_id' => '证件号码',
            'examine' => '实名认证状态',
            'reg_time' => '注册时间',
            'last_login_time' => '最后登录时间',
            'last_login_ip'=>'最后登录IP'
        );
        add_Log('下载了"用户资金表"表',1,'数据id—'.$str_id);//写入日志 add by：JZ
        downXls($list, $keynames, '用户信息列表');
    }


    //下载礼品申请列表，导出数据为excel
    public function downGift(){
    	$d                   = A('CheckInput');
		$search['dotime1']   = $d->in('开始时间','dotime1','date','','0','10');
		$search['dotime2']   = $d->in('结束时间','dotime2','date','','0','10');
		$search['username']  = $d->in('用户名','username','cnennumstr','','0','16');
		$search['status']    = $d->in('状态','status','intval','','0','2');
		$map      = "1=1 ";
		if (isset($search['dotime1']) && $search['dotime1'] != "") {
			$map .= " and g.addtime >= '" . strtotime($search['dotime1']) . "'";
		}
		if (isset($search['dotime2']) && $search['dotime2'] != "") {
			$map .= " and g.addtime <= '" . strtotime($search['dotime2']) . "'";
		}
		if (!empty($search['username'])) {
			$map .= " and c.name LIKE  '%".$search['username']."%'";
		}
		if ($search['status'] != '') {
			$map .= " and g.status = '".$search['status']."'";
		}
		
		$gift  = M('gift');
		// $count = $gift->where('1=1')->count();// 查询满足要求的总记录数
		$count = $gift->alias('g')->join('Left JOIN __CUSTOMER__ c ON c.id = g.customer_id')->where($map)->count();// 查询满足要求的总记录数
		$Page  = new \Think\Page($count,20);// 实例化分页类
		$list  = $gift->alias('g')->join('Left JOIN __CUSTOMER__ c ON c.id = g.customer_id')->where($map)->field('c.id,c.name,g.*')->order('addtime DESC')->limit($Page->firstRow.','.$Page->listRows)->select();

		foreach ($list as $key => $value) {
        	foreach ($value as $k => $v) {
        		if($k=='id'){$str_id = $str_id.$v.';';}//被下载数据的id  仅供日志记录使用
	        	if($k == 'status'){
	        		switch ($value['status']) {
	        			case '0':
	        				$list[$key]['status'] = '未邮寄';
	        				break;
	        			case '1':
	        				$list[$key]['status'] = '已邮寄';
	        				break;
	        			default:
	        				$list[$key]['status'] = '错误状态';
	        				break;
	        		}
	        	}
	        	if($k == 'postage'){
	        		switch ($value['postage']) {
	        			case '0':
	        				$list[$key]['postage'] = '到付邮费';
	        				break;
	        			case '1':
	        				$list[$key]['postage'] = '免邮费';
	        				break;
	        		}
	        	}
	        	if($k == 'express_no'){
	        		$list[$key]['express_no'] = ' '.$value['express_no'];
	        	}
        	}
        }
        $keynames = array(
            'customer_id' => '用户id',
            'name' => '用户名',
            'gift_no' => '礼物编号',
            'address' => '邮寄地址',
            'zip_code' => '邮编',
            'recipient' => '收件人姓名',
            'phone' => '手机号码',
            'express' => '快递公司',
            'express_no' => '快递号',
            'postage' => '邮费',
            'status' => '状态',
            'addtime' => '添加时间',
            'addip' => '添加ip'
        );
        add_Log('下载了"礼品申请记录表"表',1,'数据id—'.$str_id);//写入日志 add by：JZ
        downXls($list, $keynames, '礼品申请记录表');
    }

    // 用户详细信息
    public function userinfo(){
    	$che= A('CheckInput');
		$id= $che->in('ID','id','intval','','1','0');
		$db=M('customer');
		// $list=$db->field("zb_customer.id,zb_customer.name as cu_name,email,phone,nicename,card_id,reg_time,zb_customer.status")
		// 		 ->where(array('zb_customer.id'=>$id))
		// 		 ->find();

		//modyfy by zrh 2014-12-29 15:57:07
		$list=$db->field('id,name as cu_name,email,phone,nicename,card_id,reg_time,status')
				 ->where(array('id'=>$id))
				 ->find();
		
		$list3 = get_user_meta($list['id']);
		$meta=array('sex','education','province','city','area');
		foreach($list3 as $k=>$y){
			if(in_array("$y[meta_key]",$meta)){
				$list[$y['meta_key']]=$y['meta_value'];
			}
			if($y['meta_key']=='area'){
				$area=area($y['meta_value'],'area','1');
			}
		}
		$list['area']=$area;
		$list['xueli']=$list['education'];
		$aa=M('data');
		$this->edu=$aa->where(array('id'=>$list['xueli']))->getField('name');
		
		// $list2=$db->field("zb_data.name,account,branch")
		// 		 ->join("left join zb_customer_bank ON zb_customer.id=zb_customer_bank.customer_id")
		// 		 ->join("left join zb_data ON zb_data.id=zb_customer_bank.bank")
		// 		 ->where(array('zb_customer.id'=>$id))
		// 		 ->select();

		//modyfy by zrh 2014-12-29 15:57:07
		$list2 = $db->alias('c')
			->join('left join '.C('DB_PREFIX').'customer_bank as cb ON c.id = cb.customer_id')
			->join('left join '.C('DB_PREFIX').'data as d ON d.id = cb.bank')
			->field('d.name,cb.account,cb.branch')
			->where(array('c.id'=>$id))->select();
		//dump($list2);die;
		$this->assign('arr',$list2);
		$this->assign('list',$list);
    	$this->display();
    }

    //查看此用户下的所有推荐人（下线）add by zrh 2014-12-3 17:31:55
    public function recommend(){
    	$d = A('CheckInput');
    	$customer = M("customer");
    	$search['id'] = $d->in('用户id','id','intval','','1','11');
    	$search['name'] = $d->in('用户名','name','string','','0','40');
    	if(empty($search['name'])){
    		$search['name'] = $customer->where(array('id'=>$search['id']))->getField('name');
    	}
		$search['searchoption'] = $d->in('搜索条件','searchoption','string','','0','60');
		$search['searchvalue'] = trim($d->in('条件值','searchvalue','string','','0','60'));
		$search['status'] = $d->in('用户状态','status','intval','','0','1');
		$search['p'] = $d->in('当前分页数','p','intval','1','0','11');
		$search['pg'] = $d->in('上级分页数','pg','intval','1','0','11');

		$map      = "1=1 AND `invite_id` = '".$search['id']."'";
		if (!empty($search['searchoption']) && ($search['searchoption']=='name' || $search['searchoption']=='nicename')) {
			$map .= " AND ".$search['searchoption']." LIKE  '%".$search['searchvalue']."%'";
		}elseif(!empty($search['searchoption'])){
			$map .= " AND ".$search['searchoption']." = '".$search['searchvalue']."' ";
		}

		$count  = $customer->where($map)->count();// 查询满足要求的总记录数
		$page   = new \Think\Page($count,2);// 实例化分页类
		//获取此用户下的所有推荐人
		$list = $customer->where($map)->field('id,name,nicename,email,phone,card_id,reg_time,type,status')->order('reg_time DESC')->limit($page->firstRow.','.$page->listRows)->select();
		if($list === false){
			$this->error('数据库查询出错');
		}
		if(empty($list)){
			if(IS_POST){
				$this->error('没有搜索到用户',U(MODULE_NAME.'/Customer/recommend',array('id'=>$search['id'],'p'=>$search['p'])));
			}else{
				$this->error('此用户下暂无推荐人',$_SERVER["HTTP_REFERER"]);
			}
			
		}else{
			//获取所有用户下的推荐人的id
			$idarr = array();
			foreach($list as $key=>$val){
				foreach ($val as $k => $v) {
					if($k == 'id'){
						$idarr[][$key] = $v;
					}
				}
			}
			$ids = arr2str($idarr);
			//获取所有推荐人的副表的数据
			$metalist = M('customermeta')->field('customer_id,meta_key,meta_value')->where("`customer_id` IN (".$ids.")")->select();
			//获取里面的地区数据
			$area = array();
			foreach ($metalist as $key => $value) {
				if($value['meta_key'] == 'province'){
					$area[$key]['province'] = $value['meta_value'];
				}
				if($value['meta_key'] == 'city'){
					$area[$key]['city'] = $value['meta_value'];
				}
				if($value['meta_key'] == 'area'){
					$area[$key]['area'] = $value['meta_value'];
				}
			}
			$areastr = arr2str($area);
			//根据所有推荐人地区id列表获取name数据
			$arealist = M('area')->field('id,pid,name')->where("`id` IN (".$areastr.")")->select();
			foreach ($arealist as $key => $value) {
				foreach ($metalist as $k => $v) {
					if($v['meta_key'] == 'province'){
						if($value['pid'] == 0 && $v['meta_value'] == $value['id']){
							//获取本身地区名称和所有下级名称
							$areaarr[] = array_merge($this->getChildsId($arealist,$value['id']),$v);
						}
					}else{
						continue;
					}
				}
			}

			foreach ($list as $key => $value) {
				foreach ($areaarr as $k => $v) {
					if($value['id'] == $v['customer_id']){
						//正则匹配去掉非中文字符
						$list[$key]['area'] = preg_replace("/[^\x{4e00}-\x{9fa5}]+/iu",' ',implode($v, ' ')); 
					}
				}
			}

			foreach ($search as $key => $val) {
				$page->parameter[$key] = $val;
				//为下载XLS按钮分配分页条件
				// $down .= "$key=" . $val. "&";
			}
			$show   = $page->show();// 分页显示输出	
			$this->assign('list',$list);
			$this->assign('page',$show);// 赋值分页输出
			$this->assign("search", $search);//分配查询条件，查询后模板保留查询条件
			// $this->assign('down', $down);
			$this->display();
		}
    }

    /**
	 * 传入父级的id号,获取所有子级name字段值
	 * @param $cate 所有要处理的结果集二维数组
	 * @param $id 父级id
	 * @return $arr 返回包含父级名称本身的二维数组
	 */
	function getChildsId($cate,$id){
		$arr = array();
		$parr =array();
		foreach($cate as $v){
			if($v['id'] == $id){
				$parr[] = $v['name'];
			}

			//如果父级id == 传入的父级id号，说明$v['pid']是$id的子级
			if($v['pid'] == $id){
				$arr[] = $v['name'];		//将子类id加入数组
				$arr = self::getChildsId($cate,$v['id']);
			}
		}
		return array_merge($parr,$arr);
	}

	/**
	 * 后台用户列表手工认证邮箱或手机
	 */
	public function authMethod(){
		$d = A('CheckInput');
    	$customer = M("customer");
    	$userid = $d->in('用户id','userid','intval','','1','11');
    	$type = $d->in('认证类型','type','string','','1','6');
    	switch ($type) {
    		case 'email':
    			if(!IS_POST){
    				$this->assign('user_id',$userid);
    				$this->assign('type',$type);
    				$this->display('authEmail');
    				exit();
    			}
    			$data['email'] = trim($d->in('认证的邮箱','email','string','','1','60'));
    			$data['email_state'] = '1';		//邮箱认证状态
		        $map['id'] = array('neq',$userid);
		        $map['email'] = $data['email'];
		        $unique = $customer->where($map)->getField('email');
		        if($unique){
		            $this->error('此邮箱已被其他用户占用！认证失败');
		            exit();
		        }
		        $res = $customer->where(array('id'=>$userid))->save($data);
		        add_Log('为用户"id='.$userid.'"手工认证邮箱'.$data['email'],$res ,$customer);//写入日志
		        if($res){
		        	//邮箱认证积分活动
					$creditsHandleReturn=creditsHandle($userid,'email');
		        	$this->success('邮箱认证成功');
		        }else{
		        	$this->error('邮箱认证失败');
		        }
    			break;
    		case 'phone':
    			if(!IS_POST){
    				$this->assign('user_id',$userid);
    				$this->assign('type',$type);
    				$this->display('authPhone');
    				exit();
    			}
    			$data['phone'] = trim($d->in('认证的手机','phone','intval','','1','11'));
    			$data['phone_status'] = '1';		//邮箱认证状态
		        $map['id'] = array('neq',$userid);
		        $map['phone'] = $data['phone'];
		        $unique = $customer->where($map)->getField('phone');
		        if($unique){
		            $this->error('此手机已被其他用户占用！认证失败');
		            exit();
		        }
		        $res = $customer->where(array('id'=>$userid))->save($data);
		        add_Log('为用户"id='.$userid.'"手工认证手机'.$data['phone'],$res ,$customer);//写入日志
		        if($res){
		        	//手机认证积分活动
					$creditsHandleReturn=creditsHandle($userid,'phone');
		        	$this->success('手机认证成功');
		        }else{
		        	$this->error('手机认证失败');
		        }
    			break;
    		default:
    			$this->error('非法操作！');
    			break;
    	}
	}

	/**
	 * 查询用户投标详情
	 */
	//每个用户的投资明细和总数,默认要显示按时间倒序排序的所有投资列表
	public function queryInvite(){
		$d             = A('CheckInput');
		$seach['name'] = $d->in('用户名','name','string','','0','30');			
		$seach['dotime1']  = $d->in('搜索年月时间','dotime1','string','','0','10');
		$years = substr($seach['dotime1'], 0,4);//投标年份
		$months = substr($seach['dotime1'], 5);//投标月份
		$seach['p']	   = $d->in('当前分页数','p','intval','1','0','11');
		$seach['customer_id'] = M('customer')->where(array('name'=>$seach['name']))->getField('id');

		$map      = "1 = 1 and a.customer_id = ".$seach['customer_id'];
		if(!empty($years) && !empty($months)){
			$maptime  = array();
			//获取指定年月开始和结束时间戳
			$maptime  = $this->getFristAndLast($years,$months);
			$map .= " and a.addtime >= '".$maptime['firstday']."'";
			$map .= " and a.addtime <= '".$maptime['lastday']."'";
		}
		$borrow_tender = M('borrow_tender');
		$info = $borrow_tender->alias('a')->join('left join '.C('DB_PREFIX').'borrow as b ON a.borrow_id = b.id')->join('left join '.C('DB_PREFIX').'customer as c ON c.id = a.customer_id')->where($map)->field('a.borrow_id,a.afew as tzafew,a.addtime as tbtime,a.interest,a.repay_status,a.cardstate,a.money,b.title,b.borrow_type,b.time_limit,b.end_time,b.apr,c.name')->group('a.id')->page($seach['p'].',20')->order('a.addtime desc')->select();
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
		$this->display('investInfo');
	}


	//下载客服专属用户月份投资详情列表
    public function downLoanList(){
		$d             = A('CheckInput');
		$seach['name'] = $d->in('用户名','name','string','','0','30');			
		$seach['dotime1']  = $d->in('搜索年月时间','dotime1','string','','0','10');
		$years = substr($seach['dotime1'], 0,4);//投标年份
		$months = substr($seach['dotime1'], 5);//投标月份
		// $seach['p']	   = $d->in('当前分页数','p','intval','1','0','11');
		$seach['customer_id'] = M('customer')->where(array('name'=>$seach['name']))->getField('id');

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
		$info = $borrow_tender->alias('a')->join('left join '.C('DB_PREFIX').'borrow as b ON a.borrow_id = b.id')->join('left join '.C('DB_PREFIX').'customer as c ON c.id = a.customer_id')->where($map)->field('a.id,a.borrow_id,a.afew as tzafew,a.addtime as tbtime,a.interest,a.repay_status,a.cardstate,a.money,b.title,b.borrow_type,b.time_limit,b.end_time,b.apr,c.name')->order('a.addtime desc')->select();

		foreach ($info as $key => $value) {
			$info[$key]['tbtime'] = date('Y-m-d H:i:s',$value['tbtime']);
			$info[$key]['end_time'] = date('Y-m-d H:i:s',$value['end_time']);
			$str_id = $str_id.$info[$key]['id'].';';//被下载数据的id  仅供日志记录使用
			switch ($value['borrow_type']) {
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
			switch ($value['repay_status']) {
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
			// switch ($value['cardstate']) {
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
        add_Log('下载了"用户('.$seach['name'].')'.$years.'年'.$months.'月份投资详情"表',1,'数据id—'.$str_id);//写入日志 add by：JZ
        downXls($info, $keynames, '用户('.$seach['name'].')'.$years.'年'.$months.'月份投资详情表');
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
}