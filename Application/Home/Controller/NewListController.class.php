<?php
/**
 * 文章列表页/单页控制器
 */
namespace Home\Controller;
use Think\Controller;
//defined('MYPHP_FRAMEWORKS') or exit('Access Violation!');//防止非法访问
class NewListController extends PublicController{

	public function index(){
		// $d = A('CheckInput');
		$tid = (int) $_GET['tid'];		//获取要查看列表页的分类id
		$pid = (int) $_GET['pid'];		//父级id
		$nowpage = (int) $_GET['p'] ? $_GET['p'] : 1;		//当前分页数

		$db = M('terms');
		$terms = $db->order('sort ASC')->select();		//获取所有分类
		$category = new \Org\Util\Category('', array('term_id', 'parent_id', 'name', 'fullname'));
        $tids = $category->getChildsId($terms,$tid);		//获取所有子类id
		$tids[] = $tid;		//将父类id放入子类id数组
		$tids = implode($tids,',');			//转换为字符串
		//获取文章类型,用于判断是否单页
		$info = $db->field('is_page')->find($tid);
		if($info['is_page'] == '1'){		//单页分类
			$where = array('post_tid'=>array('IN',$tids),'post_status'=>'publish');
			$content = D('PostsPageView')->getPage($where);
			$post_tid = $content['post_tid'];
			$parent = $category->getParents($terms, $post_tid);		//获取当前位置父级类别
			if(count($parent) > 1){
				array_pop($parent);		//删除最后一个自身分类
			}
			//增加顶级父id
            foreach ($parent as $key => $val) { 
               $parent[$key]['pid'] = $pid;
            }

            //单页标题
			$seo['title'] =  !empty($content['post_title']) ? ' - '.$content['post_title'] : $seo['con_webname'];
			//单页关键字
			$seo['keywords'] = !empty($content['post_name']) ? $content['post_name'] : $seo['con_webkeywords'];
			//单页描述
			$seo['description'] = !empty($content['post_excerpt']) ? $content['post_excerpt'] : $seo['con_webdescription'];
			// p($content);
			$this->assign('post_tid', $post_tid);		//当前分类栏目id
			$this->assign('content', $content);
			$this->assign('parent', $parent);
			$this->assign('SEO',$seo);					//内容页seo内容

			$this->display('page');

		}else{
			$parent = $category->getParents($terms, $tid);		//获取当前位置父级类别
			//增加顶级父id
            foreach ($parent as $key => $val) { 
               $parent[$key]['pid'] = $pid;
            }
			$where = array('post_tid'=>array('IN',$tids),'post_status'=>'publish');
			$count = M('posts')->where($where)->count();
		    $Page  = new \Think\Page($count,8);    //分页数
		     			
			$Page->setConfig('prev', "上一页");//上一页
			$Page->setConfig('next', '下一页');//下一页
			$Page->setConfig('first', '首页');//第一页
			$Page->setConfig('last', "末页");//最后一页
			$Page->setConfig ( 'theme', '%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%' );		
			$show       = $Page->show();// 分页显示输出

			$limit = $Page->firstRow.','.$Page->listRows;
			$list = D('PostsView')->getAll($where,$limit);
			//如果是单页分类中的列表页,且内容页有上一页和下一页
			$pagetype = $db->where(array('term_id'=>$list[0]['post_tid']))->getField('is_page');
			if($pagetype == 0 && $list[0]['post_type'] == 'post'){
				foreach ($list as $key => $val) {
					$list[$key]['pid'] = $pid;
				}
			}
			//判断有多个父类
			if(count($parent) > 1){
				$this->assign('have_left',1);
			}

			//列表页标题
			$seo['title'] =  !empty($list[0]['name']) ? ' - '.$list[0]['name'] : $seo['con_webname'];
			//列表页关键字
			$seo['keywords'] = !empty($list[0]['slug']) ? $list[0]['slug'] : $seo['con_webkeywords'];
			//列表页描述
			$seo['description'] = !empty($list[0]['descript']) ? $list[0]['descript'] : $seo['con_webdescription'];

			$this->assign('post_tid', $tid);		//当前分类栏目id
			$this->assign('parent', $parent);		//当前位置菜单
			$this->assign('list', $list);          // 赋值文章列表
		    $this->assign('page', $show);          // 赋值分页输出
		    $this->assign('p', $nowpage);          // 赋值分页输出
		    $this->assign('SEO',$seo);			   //列表页seo内容

			$this->display();
		}
	}
}