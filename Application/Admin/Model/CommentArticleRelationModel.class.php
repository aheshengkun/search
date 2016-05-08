<?php
/**
 * 文章评论关联关系模型
 */
namespace Admin\Model;
use Think\Model\RelationModel;
class CommentArticleRelationModel extends RelationModel{
	protected $tableName = 'comments';
	protected $_link = array(
		'customer'=>array(								//用户表关系
			'mapping_type'=>self::BELONGS_TO,			//反向一对多关系
			'mapping_name'=>'customer',
			'foreign_key'=>'user_id',					//由于没有中间表，无需定义关联外键和关联表
			'mapping_fields'=>'name',
			'as_fields'=>'name:commenter'				//用户名别名
		),
		'posts'=>array(									//文章表关系
			'mapping_type'=>self::BELONGS_TO,			//反向一对多关系
			'mapping_name'=>'posts',
			'foreign_key'=>'user_id',					//由于没有中间表，无需定义关联外键和关联表
			'mapping_fields'=>'post_title',
			'as_fields'=>'post_title:post_title'		//文章名别名
		)
	);
}