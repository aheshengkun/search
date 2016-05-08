<?php
namespace Admin\Model;
use Think\Model;
class TypeModel extends Model{
	protected $tableName = 'terms'; 
	//添加分类
	public function addType($data){
		return $this->add($data);
	}
	//获取分类列表
	// public function getTypeAll(){
	// 	return $this->select();
	// }
	//修改分类
	public function editType($data){
		return $this->save($data);
	}
	//验证分类是否在用
	// public function checkRole($rid){
	// 	return $this->table('saoba_role_admin')->where(array('role_id'=>$rid))->count();
	// }
	//删除分类列表
	public function delType($id){
		return $this->where(array('term_id'=>$id))->delete();
	}
}