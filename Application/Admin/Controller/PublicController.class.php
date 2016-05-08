<?php
namespace Admin\Controller;
use Think\Controller;
class PublicController extends CommonController {
    //后台首页统计数据
    public function main(){
        $admin                        = $_SESSION['AdminAccount'];
        $infos['AdminName']           = $admin;
        $infos['os']                  = PHP_OS;
        $infos['SOFTWARE']            = $_SERVER["SERVER_SOFTWARE"];
        $infos['sykj']                = round((@disk_free_space(".") / (1024 * 1024)), 2) . 'M';
        $infos['upload_max_filesize'] = ini_get('upload_max_filesize');
        $infos['max_execution_time']  = ini_get('max_execution_time') . '秒';
        $infos['phpmode']             = php_sapi_name();
        $infos['login_time']          = session('lastLoginTime');
        $infos['lasttime']            = session('lastLoginTime');
        $infos['loginip']             = session('lastip');
        $infos['loginnum']            = session('login_count');
		
        $rolename                     = M('role')->where("id=".$_SESSION['role_id'])->getField('name');
        $infos['rolename']			  = $rolename;
		$model                        = M();
        $MysqlVerSion                 = $model->query("select VERSION()");
        $infos['MysqlVerSion']        = $MysqlVerSion[0]['version()'];
        $this->assign('info', $infos);
        $this->display();
    } 
}