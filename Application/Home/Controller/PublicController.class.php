<?php
namespace Home\Controller;
use Think\Controller;
/**
 * 前台页面分配数据用公共控制器
 */
//defined('MYPHP_FRAMEWORKS') or exit('Access Violation!');//防止非法访问
class PublicController extends Controller{
	//自动运行的方法
	public function _initialize(){
    	//判断用户是否登陆
    	$this->assign('userIsLogin',session('?customer_id'));
    	$this->User_username = session('name');
    	
    	//顶部导航菜单，获取子下拉菜单，已经改为手工在模板添加
        // $categorys = M('terms')->where("`view`='1' OR `view`='4'")->field('term_id,parent_id,name,is_page,linkurl')->order('sort ASC')->select();
        // $cat = new \Org\Util\Category('Type', array('term_id', 'parent_id', 'name', 'fullname'));
        // $cats = $cat->unlimitedForLayer($categorys, 0);     //获取顶级分类
        // $this->assign('cats', $cats);

        //友情链接
        //$this->logolink = M('links')->where(array('linktype'=>'2','status'=>'1'))->field('id,linktype,webname,weburl,logo')->limit('9')->order('listorder ASC,id')->select();

        //底部导航菜单，已经改为手工在模板添加
        // $bottomMenu = M('terms')->where(array('view'=>'3','parent_id'=>'0'))->limit('5')->order('sort')->field('term_id,parent_id,name,is_page,linkurl')->select();
        // $this->assign('bottomMenu',$bottomMenu);
        //根据传入的父id获取子级
        $d  = A('CheckInput');
    	$parent_id = $d->in('上级id','pid','intval','','0','8');
    	$terms    = M('terms');
    	$category = new \Org\Util\Category('', array('term_id', 'parent_id', 'name', 'fullname'));
        $termsarr = $terms->where(array('view'=>'1'))->field('term_id,parent_id,name,linkurl,is_page')->select();
        $leftMenu = $category->unlimitedForLayer($termsarr);//递归组合菜单及子菜单
        // p($leftMenu);
		
		//判断当前菜单是不是单页 by lhq 20150128
		//public function page
		
		
        // p($leftMenu);die;
    	
        /*  if(empty($parent_id)){
         	//如果是单页
         	//$id = (int) $_GET['id'];
         	$id = $d->in('文章','id','intval');
         	if($id){
         		$allterms = $terms->order('sort ASC')->select();
         		$content  = M('posts')->field($field)->where(array('id'=>$id,'post_status'=>'publish'))->find();	//获取文章
         		$parent   = $category->getParents($allterms, $content['post_tid']);        		
         		$leftMenu = $category->getChilds($allterms,$parent[0]['term_id']);     //获取所有子类
         	}else {
         		$leftMenu = $terms->where(array('view'=>'1','parent_id'=>'0'))->order('sort')->field('term_id,parent_id,name,is_page')->select();
         	}
           
         }else{
             $menu = $terms->where(array('view'=>'1'))->order('sort')->field('term_id,parent_id,name,is_page')->select();
             //左侧导航菜单,根据父级id获取所有子级
             $leftMenu = $category->getChilds($menu,$parent_id);     //获取所有子类
           
             if(!empty($leftMenu)){
                 $term = array();
                foreach ($leftMenu as $key => $value) {
                     array_push ($term,$value['parent_id']);
                 }
                 foreach ($leftMenu as $key => $val) { 
                    if (in_array($val['term_id'],$term)){           
                        $leftMenu[$key]['is_title'] = 0;                
                     }else{
                         $leftMenu[$key]['is_title'] = 1;
                    }
                   $leftMenu[$key]['top_parent'] = $this->getparent($val['parent_id'],$menu);
                 }
             }else{
                 //没有子级时获取所有左侧菜单
                $leftMenu = $terms->where(array('view'=>'1','parent_id'=>'0'))->order('sort')->field('term_id,parent_id,name,is_page')->select();  
             } 
        }    */
    	$this->assign('leftMenu',$leftMenu); 
    	
    	
    	/*
    	 * 客服列表
    	*/
        /*
        $info = S('kefulist');
        if($info==false){
        	$user = M('user');
        	$UserInfo = $user->alias('a')->join('left join '.C('DB_PREFIX').'usermeta as b ON a.id = b.user_id')->field('a.id,a.account,a.status,b.*')->where('a.status = "1" and (b.meta_key = "photo" or b.meta_key = "qq")')->order('a.create_time')->select();
        	//重组数组
        	$info = array();
        	foreach ($UserInfo as $key => $value) {
        		$info[$value['id']][$value['meta_key']] = $value['meta_value'];
        		$info[$value['id']]['status']           = $value['status'];
        		$info[$value['id']]['user_id']          = $value['user_id'];
        		$info[$value['id']]['account']          = $value['account'];
        	}
            // shuffle($info);
            S('kefulist',$info,360);
        }
        $info = shuffle_twoarr($info);//调用随机打乱二维数组函数
    	$this->assign("kefu",$info);

        //后台设置的网站SEO信息
        if(!$seo = S('zhaobao_seo')){
            $seo      = M('system')->select();
            foreach ($seo as $key=>$val){
                if($val['nid'] == 'con_webname'){   //网站名称
                    $seo['con_webname'] = $val['value'];
                }
                if($val['nid'] == 'con_webkeywords'){   //网站关键词
                    $seo['con_webkeywords'] = $val['value'];
                }
                if($val['nid'] == 'con_webdescription'){    //网站描述
                    $seo['con_webdescription'] = $val['value'];
                }
            }
            S('zhaobao_seo',$seo,3600*24);     //设置缓存24h
        }
        */
        $this->assign('CON_SEO',$seo);   //分配网站全局seo内容
	}

	//ajax获取城市和地区
	public function ajaxArea(){
		$d                = A('CheckInput');
		$Areaid           = $d->in('省份id','id','intval','',1,11);
		$list   = M('area')->where("pid='$Areaid'")->select();
		echo json_encode($list);
	}
	
	//获取顶级父类
	public function getparent($catid,$cats){
		foreach ($cats as $key => $val){			
			if($val['term_id'] == $catid){
				$parent_id = $val['parent_id'];
			}			
		}
		if($parent_id){
			foreach ($cats as $key => $val){
				if($val['term_id'] == $parent_id){
					if($val['parent_id']){
						$this->getparent();
					}else {
						$return = $val['term_id'];
					}
				}
			}
		}
		return $return;
	}
	
/* 	public function update_time(){
		$bt=M('borrow_tender');
		$data2=$bt->where('borrow_id =11')->select();
		$sqlok=true;
		foreach ($data2 as $key => $v) {
			if($sqlok===false) break;
			$v['addtime']-=2678400;
			$where = "`id` = '".$v['id']."'";	
			$sqlok = $bt->where($where)->save(array('addtime'=>$v['addtime']));
		}
		
	} */
}