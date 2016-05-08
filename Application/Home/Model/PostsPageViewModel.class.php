<?php
/**
 * 前台单页视图模型
 */
namespace Home\Model;
use Think\Model\ViewModel;

class PostsPageViewModel extends ViewModel{
	protected $viewFields = array(
		'posts'=>array(
			'id','post_title','face_img','post_excerpt','post_content','post_date','post_name','post_type','post_status','post_tid',		//文章表要读取的字段
			'_type'=>'LEFT'			//join连接类型
		),
		'terms'=>array(
			'name','_on'=>'posts.post_tid = terms.term_id'		//副表要读取的字段和关联读取条件
		)
	);

	/**
	 * 获取单页文章
	 */
	public function getPage($where){
		return $this->where($where)->find();
	}
}