<?php
namespace Admin\Model;
use Think\Model;
class AccessModel extends Model{
	protected $tableName = 'access'; 
	//添加权限
	public function addAccessAll($data){
		return $this->addAll($data);
	}
	//删除旧的权限
	public function delAccessAllByRid($rid){
		return $this->where(array('role_id'=>$rid))->delete();
	}
	//获取权限列表
	public function getAccessByoRleid($rid){
		return $this->where(array('role_id'=>$rid))->getField('node_id',true);
	}
}