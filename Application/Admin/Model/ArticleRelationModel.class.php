<?php
/**
 * 文章关联关系模型
 */
namespace Admin\Model;
use Think\Model\RelationModel;
class ArticleRelationModel extends RelationModel{
	protected $tableName = 'posts';
	protected $_link = array(
		'terms'=>array(								//分类表关系
			'mapping_type'=>self::BELONGS_TO,		//反向一对多关系
			'mapping_name'=>'terms',
			'foreign_key'=>'post_tid',				//由于没有中间表，无需定义关联外键和关联表
			'mapping_fields'=>'name',
			'as_fields'=>'name:tname'				//分类名别名
		),
		'user'=>array(								//用户表关系
			'mapping_type'=>self::BELONGS_TO,		//反向一对多关系
			'mapping_name'=>'user',
			'foreign_key'=>'post_author',			//由于没有中间表，无需定义关联外键和关联表
			'mapping_fields'=>'account',
			'as_fields'=>'account:author'			//用户名别名
		)
	);

	/**
	 * 获取文章或回收站中文章
	 * $type 表示文章的状态，0表示未删除到回收站的，1表示删除到回收站的
	 */
	public function getArticles($field, $where){
		//获取关联关系表数据
		return $this->field($field)->where($where)->relation(true)->select();
	}
}