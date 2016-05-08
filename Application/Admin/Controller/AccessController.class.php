<?php
namespace Admin\Controller;
use Think\Controller;
class AccessController extends CommonController {
	/**
	 * 后台管理员列表
	 * Enter description here ...
	 */
    public function userList(){
    	// $db = D('UserRelation');
    	// $db = M('user');
    	// $admin = $db->getUserAll();
    	$admin   = M('user')->alias('a')->join('left join '.C('DB_PREFIX').'role_user as b ON a.id = b.user_id')->where("b.role_id <> '9'")->join('left join '.C('DB_PREFIX').'role as c ON b.role_id = c.id')->where("b.role_id <> '9'")->field('a.id,a.account,a.nickname,a.create_time,a.status,a.login_count,c.name,c.remark')->order('a.create_time')->select();
    	$this->assign('admin',$admin);
    	$this->display();
    }
	/**
	 * 删除后台管理员
	 * Enter description here ...
	 */
    public function delUser() {
    	$d       = A('CheckInput');
        $aid     = $d->in('管理员id','get.id','intval','','1','0');
    	// $aid  	= I('get.id','','intval');
        $db  	= D('UserRelation');
        $result = $db->delUser($aid);
		add_Log('删除了"id='.$aid.'"的管理员',$result,$db);//写入日志 
        if($result){
        	$this->success('删除成功！',U(MODULE_NAME.'/Access/userList'));
	    }else{
	    	$this->error('删除失败！',U(MODULE_NAME.'/Access/userList'));
	    }
    }
	/**
	 * 角色列表
	 * Enter description here ...
	 */
    public function roleList() {
        $db = D('Role');
        $data = $db->getRoleAll();
        $this->assign('role',$data);
        $this->display();
    }
	/**
	 * 删除角色
	 * Enter description here ...
	 */
    public function delRole() {
    	$d       = A('CheckInput');
        $rid     = $d->in('角色id','get.rid','intval','','1','0');
    	// $rid = I('get.rid','','intval');
        $db = D('Role');
        if($db->checkRole($rid)){
        	$this->error('删除失败，该角色正在使用！',U(MODULE_NAME.'/Access/roleList'));
        }else{
        	$result = $db->delRole($rid);
			add_Log('删除了"id='.$rid.'"的角色',$result,$db);//写入日志 
        	if($result){
        		$this->success('删除成功！',U(MODULE_NAME.'/Access/roleList'));
        	}else{
        		$this->error('数据异常，删除失败！',U(MODULE_NAME.'/Access/roleList'));
        	}
        }
    }
	/**
	 * 节点列表
	 * Enter description here ...
	 */
    public function nodeList() {
        $db = D('Node');
        $data = $db->nodeList();
        //$data = node_merge($data);
        //dump($data);
        $this->assign('node',$data);
        $this->display();
    }
	/**
	 * 删除节点
	 * Enter description here ...
	 */
    public function delNode() {
    	$d       = A('CheckInput');
        $nid     = $d->in('节点id','get.nid','intval','','1','0');
    	// $nid = I('get.nid','','intval');
        $db = D('Node');
        if($db->checkNode($nid)){
        	$this->error('删除失败，该节点正在使用！',U(MODULE_NAME.'/Access/nodeList'));
        }else{
        	if($db->checkChildNode($nid)){
        		$this->error('删除失败，请先删除子节点！',U(MODULE_NAME.'/Access/nodeList'));
        	}else{
        		$result = $db->delNode($nid);
				add_Log('删除了节点"id='.$nid.'"',$result,$db);//写入日志 
	        	if($result){
	        		$this->success('删除成功！',U(MODULE_NAME.'/Access/nodeList'));
	        	}else{
	        		$this->error('删除失败！',U(MODULE_NAME.'/Access/nodeList'));
	        	}
        	}
        }
    }
	/**
	 * 添加用户
	 * Enter description here ...
	 */
    public function userAdd() {
    	if(!IS_POST){
	    	//超级管理员只有一个(等于配置文件里的ID的时不允许选择) 传入 $info
	    	$info = array('id'=>C('RBAC_SUPPERADMIN_ID'));
	        $data = $this->getRole($info);
	        $this->assign('role',$data);
	        $this->display();
    	}else{
    		$time = time();
    	    $db = D('UserRelation');

	    	$d       = A('CheckInput');
	    	$data['account'] 		 = $d->in('用户名','username','cnennumstr','','1','16');
	    	$data['password'] 		 = md5($d->in('密码','password','string','','1','32'));
	    	$data['nickname'] 		 = $d->in('中文名','nickname','cnennumstr','','0','16');
	    	$data['status'] 		 = $d->in('状态','status','intval','','0','1');
	    	$data['create_time'] 	 = $time;
	    	$data['last_login_time'] = $time;
	    	$data['last_login_ip'] 	 = get_client_ip();
	    	$role_id 				 = $d->in('所属角色','role_id','intval','','1','2');
			//dump($data);die;

	    	// $data['account'] = I('post.username','','htmlspecialchars');
	    	// $data['password'] = I('post.password','','md5');
	    	// $data['nickname'] = I('post.nickname','','htmlspecialchars');
	    	// $data['status'] = I('post.status',1,'intval');
	    	// $data['create_time'] = $time;
	    	// $data['last_login_time'] = $time;
	    	// $data['last_login_ip'] = get_client_ip();
	    	// $role_id = I('post.role_id','','intval');

	    	$role = array();
	    	if($role_id == C('RBAC_SUPPERADMIN_ID'))$this->error('非法操作！');
	    	$uid = $db->addUser($data);
	    	//echo $db->getLastSql();exit;
			add_Log('添加了新管理员"'.$data['account'].'"',$uid,$db);//写入日志 
	    	if($uid){
		    	$role[]=array('role_id'=>$role_id,'user_id'=>$uid);
		    	M('role_user')->addAll($role);
		    	$this->success('添加成功！',U(MODULE_NAME.'/Access/userList'));
	    	}else{
	    		$this->error('添加失败！',U(MODULE_NAME.'/Access/userList'));
	    	}
    	}
    }
	/**
	 * 编辑用户
	 * Enter description here ...
	 */
    public function userEdit() {
    	$d     = A('CheckInput');
    	if(IS_POST){
    	    $db = D('UserRelation');
    	    $aid 	 	 = $d->in('管理员id','id','intval','','1','0');
    	    $password 	 = md5($d->in('密码','password','string','','','32'));
    	    // $aid = I('post.id',1,'intval');
    	    // $password =  I('post.password','','md5');
    	    $data['id'] = $aid;
    	    if($password){
    	    	$data['password'] = $password;
    	    }

	    	$data['account'] 		 = $d->in('用户名','username','cnennumstr','','1','16');
	    	$data['nickname'] 		 = $d->in('中文名','nickname','cnennumstr','','0','16');
	    	$data['status'] 		 = $d->in('状态','status','intval','','0','1');
	    	$data['update_time'] 	 = time();
	    	$data['last_login_ip'] 	 = get_client_ip();
	    	$role_id 				 = $d->in('所属角色','role_id','intval','','1','2');
	    	// $data['account'] = I('post.username','','htmlspecialchars');
	    	// $data['nickname'] = I('post.nickname','','htmlspecialchars');
	    	// $data['status'] = I('post.status',1,'intval');
	    	// $data['update_time'] = time();
	    	// $data['last_login_ip'] = get_client_ip();
	    	// $role_id = I('post.role_id','','intval');

	    	$role = array();
	    	if($role_id == C('RBAC_SUPPERADMIN_ID'))$this->error('非法操作！');
	    	$result = $db->editUser($data);
	    	// echo $db->getLastSql();exit;
	    	add_Log('编辑了管理员"'.$data['account'].'"的资料',$result,$db);//写入日志 
	    	if($result){
		    	$role[]=array('role_id'=>$role_id,'user_id'=>$aid);
		    	$db = M('role_user');
		    	$db->where(array('user_id'=>$aid))->delete();
		    	$db->addAll($role);
		    	$this->success('编辑成功！',U(MODULE_NAME.'/Access/userList'));
	    	}else{
	    		$this->error('编辑失败！',U(MODULE_NAME.'/Access/userList'));
	    	}
    	}else{
    	    $aid = $d->in('管理员id','get.id','intval','','1','0');
	    	// $aid = I('get.id',1,'intval');
	    	$db = M('role_user');
	    	$userdb = M('user');
	    	$user_admin = $db->where(array('user_id'=>$aid))->find();
	    	$user = $userdb->where(array('id'=>$aid))->find();
	    	if($user_admin){
	    		$info['pid'] = $user_admin['role_id'];
	    	}
	    	//超级管理员只有一个(等于配置文件里的ID的时不允许选择) 传入 $info
	    	$info['id'] = C('RBAC_SUPPERADMIN_ID');
	        $data = $this->getRole($info);
	        $this->assign('id',$aid);
	        $this->assign('user',$user);
	        $this->assign('role',$data);
	        $this->display();
    	}
    }
	/**
	 * 添加角色
	 * Enter description here ...
	 */
    public function roleAdd() {
    	if(!IS_POST){
	    	$this->assign("role", $this->getRole());
	        $this->display();
    	}else{
    	    $db 			= D('Role');
    		$d     			= A('CheckInput');
			$data['name'] 	= $d->in('角色名字','name','cnennumstr','','1','16');
	    	$data['pid'] 	= $d->in('父级组ID','pid','intval','','1','3');
	    	$data['status'] = $d->in('状态','status','intval','','1','2');
	    	$data['remark'] = $d->in('描述','remark','intval','','0','50');
	    	// $data['name'] 	= I('post.name','','htmlspecialchars');
	    	// $data['pid'] 	= I('post.pid','','intval');
	    	// $data['status'] = I('post.status','','htmlspecialchars');
	    	// $data['remark'] = I('post.remark','','htmlspecialchars');

	    	$result 		= $db->addRole($data);
			add_Log('添加了新角色"'.$data['name'].'"',$result,$db);//写入日志 
	    	if($result){
	    		$this->success('添加成功！',U(MODULE_NAME.'/Access/roleList'));
	    	}else{
	    		$this->error('添加失败！',U(MODULE_NAME.'/Access/roleList'));
	    	}
        }
    }
	/**
	 * 编辑/配置权限
	 * Enter description here ...
	 */
    public function accessEdit() {
    	$d   = A('CheckInput');
    	if(!IS_POST){
    		$rid = $d->in('id','rid','intval','','1','0');
    		// $rid = I('rid','0','intval');
	        $db = D('Node');
	        $node = $db->getNodeAll();
	        $db = D('Access');
	        $access = $db->getAccessByoRleid($rid);
	        $data = node_merge($node,$access);
	        $role = D('role');
	        $info = $role->where('id ='.$rid)->find();
	        $this->assign('info',$info);
	        $this->assign('node',$data);
	        $this->assign('rid',$rid);
	        $this->display();
    	}else{
    		$rid = $d->in('id','rid','intval','','1','0');
    	    // $rid = I('rid','0','intval');
	    	$db = D('Access');
	    	$data = array();
	    	foreach ($_POST['access'] as $v){
	    		$tmp = explode('_', $v);
	    		$data[] = array(
	    			'role_id' =>$rid,
	    			'node_id'=>$tmp[0],
	    			'level'=>$tmp[1]
	    			);
	    	}
	    	$db->delAccessAllByRid($rid);//删除旧权限
	    	$result = $db->addAccessAll($data);
			add_Log('编辑了角色"id='.$rid.'"的权限',$result,$db);//写入日志 
	    	if($result){
	    		$this->success('权限更新成功',U(MODULE_NAME.'/Access/roleList'));
	    	}else{
	    		$this->error('权限更新失败',U(MODULE_NAME.'/Access/roleList'));
	    	}
    	}
    }
	/**
	 * 添加节点
	 * Enter description here ...
	 */
    public function nodeAdd() {
    	if(!IS_POST){
	    	$this->assign("info", $this->getPid(array('level' => 1)));
	    	$this->display();
    	}else{
    	    $db = D('Node');
    	    $d   = A('CheckInput');
	    	$data['name']  	= $d->in('节点名称','name','cnennumstr','','1','20');
	    	$data['status'] = $d->in('状态','status','intval','','1','2');
	    	$data['title']  = $d->in('显示名','title','cnennumstr','','1','16');
	    	$data['sort'] 	= $d->in('排序','sort','intval','','0','3');
	    	$data['remark'] = $d->in('描述','remark','cnennumstr','','0','150');
	    	$data['pid'] 	= $d->in('父级节点','pid','intval','','1','0');
	    	$data['level'] 	= $d->in('类型','level','intval','','1','2');
	    	// $data['name']  	= I('post.name','','htmlspecialchars');
	    	// $data['status'] = I('post.status','','htmlspecialchars');
	    	// $data['title']  = I('post.title','','htmlspecialchars');
	    	// $data['sort'] 	= I('post.sort','1','intval');
	    	// $data['remark'] = I('post.remark','','htmlspecialchars');
	    	// $data['pid'] 	= I('post.pid','0','intval');
	    	// $data['level'] 	= I('post.level','0','intval');

	    	$result = $db->addNode($data);
			add_Log('添加了新节点"'.$data['name'].'"',$result,$db);//写入日志 
	    	if($result){
	    		$this->success('添加成功！',U(MODULE_NAME.'/Access/nodeList'));
	    	}else{
	    		$this->error('添加失败！',U(MODULE_NAME.'/Access/nodeList'));
	    	}
    	}
    }
    /**
     * 编辑节点
     * Enter description here ...
     */
    public function nodeEdit() {
    	$d   = A('CheckInput');
        if (IS_POST) {
            $db = D('Node');
    	    $data['id'] 	= $d->in('节点id','nid','intval','','1','0');
	    	$data['name']  	= $d->in('节点名称','name','cnennumstr','','1','16');
	    	$data['status'] = $d->in('状态','status','intval','','1','2');
	    	$data['title']  = $d->in('显示名','title','cnennumstr','','1','16');
	    	$data['sort'] 	= $d->in('排序','sort','intval','','0','3');
	    	$data['remark'] = $d->in('描述','remark','cnennumstr','','0','150');
	    	$data['pid'] 	= $d->in('父级节点','pid','intval','','1','0');
	    	$data['level'] 	= $d->in('类型','level','intval','','1','2');
      		//$data['id'] 	= I('post.nid','','intval');
	    	// $data['name'] 	= I('post.name','','htmlspecialchars');
	    	// $data['status'] = I('post.status','','htmlspecialchars');
	    	// $data['title'] 	= I('post.title','','htmlspecialchars');
	    	// $data['sort'] 	= I('post.sort','1','intval');
	    	// $data['remark'] = I('post.remark','','htmlspecialchars');
	    	// $data['pid'] 	= I('post.pid','0','intval');
	    	// $data['level'] 	= I('post.level','0','intval');
	    	$result = $db->editNode($data);
	    	add_Log('编辑了节点"'.$data['name'].'"',$result,$db);//写入日志 
	    	if($result){
	    		$this->success('编辑成功！',U(MODULE_NAME.'/Access/nodeList'));
	    	}else{
	    		$this->error('编辑失败！',U(MODULE_NAME.'/Access/nodeList'));
	    	}
        } else {
            $M = M("Node");
            $nid = $d->in('节点id','nid','intval','','1','0');
            // $nid = I('get.nid','','intval');
            $info = $M->where("id=".$nid)->find();
            if (empty($info['id'])) {
                $this->error("不存在该节点", U(MODULE_NAME.'/Access/nodeList'));
            }
            $this->assign("nodeInfo", $info);
            $this->assign("info", $this->getPid($info));
            $this->display();
        }
    }
    /**
     * 获取角色等级数据
     * Enter description here ...
     * @param $info
     */
    private function getRole($info = array()) {
       // import("Category");
        $cat = new \Org\Util\Category('Role', array('id', 'pid', 'name', 'fullname'));
        $list = $cat->getList();               //获取分类结构
        foreach ($list as $k => $v) {
            $disabled = $v['id'] == $info['id'] ? ' disabled="disabled"' : "";
            $selected = $v['id'] == $info['pid'] ? ' selected="selected"' : "";
            $info['pidOption'].='<option value="' . $v['id'] . '"' . $selected . $disabled . '>' . $v['fullname'] . '</option>';
        }
        return $info;
    }
    private function getPid($info) {
        $arr = array("请选择", "网站应用", "控制器", "方法");
        for ($i = 1; $i < 4; $i++) {
            $selected = $info['level'] == $i ? " selected='selected'" : "";
            $info['levelOption'].='<option value="' . $i . '" ' . $selected . '>' . $arr[$i] . '</option>';
        }
        $level = $info['level'] - 1;
        import("Category");
        $cat = new \Org\Util\Category('Node', array('id', 'pid', 'title', 'fullname'));
        $list = $cat->getList();               //获取分类结构
        $option = $level == 0 ? '<option value="0" level="-1">根节点</option>' : '<option value="0" disabled="disabled">根节点

</option>';
        foreach ($list as $k => $v) {
            $disabled = $v['level'] == $level ? "" : ' disabled="disabled"';
            $selected = $v['id'] != $info['pid'] ? "" : ' selected="selected"';
            $option.='<option value="' . $v['id'] . '"' . $disabled . $selected . '  level="' . $v['level'] . '">' . $v['fullname'] 

. '</option>';
        }
        $info['pidOption'] = $option;
        return $info;
    }
}