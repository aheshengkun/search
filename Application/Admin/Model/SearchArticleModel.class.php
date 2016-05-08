<?php
namespace Admin\Model;
use Think\Model;
class SearchArticleModel extends Model{
	protected $tableName = 'article'; 
	/*
	//添加角色
	public function addRole($data){
		return $this->add($data);
	}
	//获取角色列表
	public function getRoleAll(){
		return $this->select();
	}
	//验证角色是否在用
	public function checkRole($rid){
		return $this->table('saoba_role_admin')->where(array('role_id'=>$rid))->count();
	}
	//删除角色列表
	public function delRole($rid){
		return $this->where(array('id'=>$rid))->delete();
	}*/
}