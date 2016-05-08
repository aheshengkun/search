<?php
/**
 * 文章内容页控制器
 */
namespace Home\Controller;
use Think\Controller;
//defined('MYPHP_FRAMEWORKS') or exit('Access Violation!');//防止非法访问
class NewContentController extends PublicController{
	/**
	 * 文章内容页视图
	 */
	public function index(){
		$id = (int) $_GET['id'];		//文章id
		$pid = (int) $_GET['pid'];		//父级id
		$db = M('posts');
		$d  = A('CheckInput');
    	$parent_id = $d->in('上级id','pid','intval','','0','8');

		$field = array('id','post_title','post_excerpt','post_name','post_content','guid','post_type','post_modified','post_tid');
		$content = $db->field($field)->where(array('id'=>$id,'post_status'=>'publish'))->find();	//获取文章
		$tid = $content['post_tid'];
		$terms = M('terms')->order('sort ASC')->select();		//获取所有分类
		$category = new \Org\Util\Category('', array('term_id', 'parent_id', 'name', 'fullname'));
		$parent = $category->getParents($terms, $tid);
		//增加顶级父id
        if($pid != 0){
        	foreach ($parent as $key => $val) {
	           $parent[$key]['pid'] = $pid;
	        }
        }
		// dump($parent);
		//上一篇
		$prev_page = $db->where("`post_tid` = '$tid' AND `id`>'$id' AND `post_status` = 'publish'")->field($field)->order('id ASC')->find();
		$prev = !$prev_page ? '没有了！' : '<a href="'.__ROOT__.'/NewContent/index/id/'.$prev_page["id"].'/pid/'.$pid.'.html" title="'.$prev_page["post_title"].'">'.$prev_page["post_title"].'</a>';
		
		//下一篇
		$next_page = $db->where("`post_tid` = '$tid' AND `id`<'$id' AND `post_status` = 'publish'")->field($field)->order('id DESC')->find();
		$next = !$next_page ? '没有了！' : '<a href="'.__ROOT__.'/NewContent/index/id/'.$next_page["id"].'/pid/'.$pid.'.html" title="'.$next_page["post_title"].'">'.$next_page["post_title"].'</a>';

		//内容页标题
		$seo['title'] =  !empty($content['post_title']) ? ' - '.$content['post_title'] : $seo['con_webname'];
		//内容页关键字
		$seo['keywords'] = !empty($content['post_name']) ? $content['post_name'] : $seo['con_webkeywords'];
		//内容页描述
		$seo['description'] = !empty($content['post_excerpt']) ? $content['post_excerpt'] : $seo['con_webdescription'];
		// p($content);
		$this->assign('post_tid', $tid);		//当前分类栏目id
		$this->assign('content', $content);
		$this->assign('parent', $parent);		//当前位置菜单
		$this->assign('prev_page',$prev);
		$this->assign('next_page',$next);
		$this->assign('SEO',$seo);		//内容页seo内容
		if($id == 18){
			$this->display('Tool/index');//网贷计算器页面
		}else{
			$this->display();
		}
	}
}