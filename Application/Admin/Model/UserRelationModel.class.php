<?php
namespace Admin\Model;
use Think\Model\RelationModel;
/**
 * 用户与角色关联模型
 * Enter description here ...
 * @author Alex
 */
class UserRelationModel extends RelationModel{
	//定义主表
	protected $tableName = 'user';
	//定义主表
	protected $_link = array(
			'role'=>array(
					'mapping_type'=>self::MANY_TO_MANY,
					'class_name' => 'role',
					'foreign_key'=>'user_id',
					'relation_key'=>'role_id',
					'relation_table'=>'zb_role_user',
					'mapping_fields'=>'id,name,remark'
				)
		);
	
	//添加用户
	public function addUser($data){
		return $this->add($data);
	}
	//删除用户
	public function delUser($id){
		$flg = false;
		if($this->table("{$this->tablePrefix}role_user")->where(array('user_id'=>$id))->delete()){
			$flg = $this->where(array('id'=>$id))->delete();
		}
		return $flg;
	}
	//修改用户
	public function editUser($data){
		return $this->save($data);
	}
	//获取角色列表
	public function getUserAll(){
		$fields =array(
			'id','account','nickname','last_login_time','last_login_ip','login_count','status'
		);
		return $this->field($fields)->relation('role')->select();
	}
	//获取角色列表
	public function getUserById($id){
		$fields =array(
			'id'
		);
		return $this->where(array('id'=>$aid))->field($fields)->relation('role')->select();
	}
}