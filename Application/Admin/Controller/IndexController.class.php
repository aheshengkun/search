<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends CommonController {
    public function index(){
       	$this->assign('username',$_SESSION['AdminAccount']);
		$this->display();
    }
    //退出
	public function logout(){
		session_unset();
		session_destroy();
		$this->redirect(MODULE_NAME.'/Login/index');
	}
}