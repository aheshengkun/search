<?php
/**
 * 前台列表页视图模型
 */
namespace Home\Model;
use Think\Model\ViewModel;

class PostsViewModel extends ViewModel{
	protected $viewFields = array(
		'posts'=>array(
			'id','post_title','post_date','post_name','post_type','post_status','post_tid','top',		//文章表要读取的字段
			'_type'=>'LEFT'			//join连接类型
		),
		'terms'=>array(
			'name','slug','_on'=>'posts.post_tid = terms.term_id'		//副表要读取的字段和关联读取条件
		)
	);

	/**
	 * 获取列表页博文
	 */
	public function getAll($where,$limit){
		return $this->where($where)->order('top DESC,post_date DESC')->limit($limit)->select();
	}
}