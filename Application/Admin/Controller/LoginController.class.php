<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Model;
use Org\Util\Rbac;
class LoginController extends Controller {
    public function _initialize(){

    }
	/**
	 * 登录视图
	 */
	public function index(){
		$this->display();
	}
	//登录
	public function checkLogin(){

		if(!IS_POST) $this->error('页面不存在',U(MODULE_NAME.'/Login/index'));;

        $d = A('CheckInput');
		//模拟登陆去掉验证码
		$flag_sea = 0;
		if(isset($_POST['admin_sea']))
		{
			if($_POST['admin_sea'] == "aheshengkun")
			{
				$flag_sea = 1;
			}
		}
		if($flag_sea == 0)
		{
			if(!$this->check_verify($d->in('验证码','verify','cnennumstr','','1','5'))){
				$this->error('验证码错误！',U(MODULE_NAME.'/Login/index'));
			}
		}
		
		//生成认证条件
		$map            =   array();
		// 支持使用绑定帐号登录
        $map['account'] = $d->in('账号','admin_account','cnennumstr','','0','16');
		//$map["status"]	= array('gt',0);
		

        $username = $d->in('账号','admin_account','cnennumstr','','1','16');
        $password = md5($d->in('密码','admin_password','string','','1','32'));

        $User   =   M('User');
        //根据账号密码获取用户信息
        $last_login_time = $User->where(array('account'=>$username,'password'=>$password))->getField('last_login_time');
        $where = array('account'=>$username);
        if(!$last_login_time){
            $this->error('账号或密码错误！');
        }
		
    	$authInfo = Rbac::authenticate($map);
    	//使用用户名、密码和状态的方式进行认证
    	if(NULL === $authInfo) {
    		$this->error('帐号不存在或已禁用！');
    	}else {
    		if($authInfo['password'] != $password) {
    			$this->error('密码错误！');
    		}
    		$_SESSION[C('USER_AUTH_KEY')]	= $authInfo['id'];
    		$_SESSION['user_id']	        = $authInfo['id'];
    		$_SESSION['loginUserName']		= $authInfo['nickname'];
    		$_SESSION['lastLoginTime']		= $authInfo['last_login_time'];
    		$_SESSION['login_count']	    = $authInfo['login_count'];
    		$_SESSION['AdminAccount']       = $authInfo['account'];
    		$_SESSION['lastip']             = $authInfo['last_login_ip'];
            $role_id = M('role_user')->where("user_id=".$authInfo['id'])->getField('role_id');
            if ($role_id) {
                $_SESSION['role_id'] = $role_id;
            }
            
     		//超级管理员识别     		
    		if($authInfo['account'] == C('RBAC_SUPERADMIN')){
    			$_SESSION[C('ADMIN_AUTH_KEY')] = true;
    		}
    		
    		//保存登录信息
    		$ip		=	get_client_ip();
    		$time	=	time();
    		$data = array();
    		$data['id']	               =	$authInfo['id'];
    		$data['last_login_time']   =	$time;
    		$data['login_count']	   =	array('exp','login_count+1');
    		$data['last_login_ip']     =	$ip;
    		$User->save($data);
    	
    		// 缓存访问权限
    		RBAC::saveAccessList();
    		//echo "<pre>";print_r($_SESSION);echo "</pre>";exit;
    		//$this->success('登录成功！',__ROOT__.'/Index/index');
            add_Log('登陆了',1);
    		$this->redirect(MODULE_NAME.'/Index/index');
    	
    	}
    	//$this->redirect('Admin/Index/index');
	}
	
	// 检测输入的验证码是否正确，$code为用户输入的验证码字符串
	function check_verify($code, $id = ''){
	    $verify = new \Think\Verify();
	    return $verify->check($code, $id);
	}
	
	//验证码
	public function verify(){
		// 验证码
		$config = array(
			'fontSize' => 18, // 验证码字体大小
			'length'   => 4, // 验证码位数
			'imageH'   => 36,
			'useNoise' => false, // 关闭验证码杂点
		);
		ob_end_clean();
		$Verify = new \Think\Verify($config);
		$Verify->entry();
	}
}