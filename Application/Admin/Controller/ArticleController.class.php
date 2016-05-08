<?php
namespace Admin\Controller;
use Think\Controller;
/**
 * 后台文章分类控制器
 */
class ArticleController extends CommonController{
	//文章分类列表
	public function type(){
		$cat = new \Org\Util\Category('Type', array('term_id', 'parent_id', 'name', 'fullname'));
        $cat->order('sort');
        $list = $cat->getList(NULL, 0, 'sort ASC');               //获取分类结构
		$this->assign('terms',$list);
		$this->display();
	}

	//分类排序
	public function typeSort(){
		if(!IS_POST){
			$this->error('页面不存在！');
		}
		$str_sql = '';
		$db      = M('terms');
		foreach($_POST as $id=>$sort){
			$id = intval($id);
			$sort = intval($sort);
			$db->where(array('term_id'=>$id))->setField('sort',$sort);
			$str_sql = $str_sql.$db->_sql().';';//获取操作语句，仅供日志记录使用
		}
		add_Log('修改了"文章分类"的排序',1,$str_sql);//写入日志 
		$this->redirect(MODULE_NAME.'/Article/type');
	}

	//添加文章分类
	public function addType() {
    	if(!IS_POST){
	    	$this->assign("type", $this->getCate());
	        $this->display('typeForm');
    	}else{
    	    $db = M('terms');
	    	$d  = A('CheckInput');
			$data['parent_id'] = $d->in('上级id','parent_id','intval','','1','8');
			$data['name']      = $d->in('分类名称','name','string','','1','50');
			$data['slug']     = $d->in('名称关键词缩写','slug','string','','1','40');
			$data['descript'] = $d->in('分类描述','descript','string','','0','255');
			$data['sort'] = $d->in('排序号','sort','intval','','1','8');
			$data['view'] = $d->in('是否显示','view','intval','','1','1');
			$data['is_page'] = $d->in('分类类型','is_page','intval','','1','1');
			if($data['is_page'] == '2'){
				$data['linkurl']   = $d->in('外部链接url','linkurl','string','','1','100');
			}
			$result = $db->add($data);
			add_Log('添加了"'.$data['name'].'"分类',$result,$db);//写入日志 
			if($result){
				$this->success('添加分类成功',U(MODULE_NAME.'/Article/type'));
			}else{
				$this->error('添加分类失败，请检查名称缩写是否重复');
			}
		}
    }

	//获取分类等级数据
	private function getCate($info = array()) {
        $cat = new \Org\Util\Category('Type', array('term_id', 'parent_id', 'name', 'fullname'));
        $list = $cat->getList();               //获取分类结构
        foreach ($list as $k => $v) {
            $info['pidOption'].='<option value="' . $v['term_id'] . '"' . '>' . $v['fullname'] . '</option>';
        }
        return $info;
    }

    //修改分类
    public function typeEdit(){
    	$d  = A('CheckInput');
    	$id = $d->in('分类id','id','intval','','1','8');
    	$db = M("terms");
		if(!IS_POST){
            $info = $db->where("term_id=$id")->find();
            if (empty($info['term_id'])) {
                $this->error("不存在此分类");
            }
            $this->assign("type", $info);
            $this->assign("info", $this->getCate($info));
            $this->display('typeForm');
    	}else{
			$data['parent_id'] = $d->in('上级id','parent_id','intval','','1','8');
			$data['name']      = $d->in('分类名称','name','string','','1','50');
			$data['slug']      = $d->in('名称关键词缩写','slug','string','','1','40');
			$data['descript']  = $d->in('分类描述','descript','string','','0','255');
			$data['sort']      = $d->in('排序','sort','intval','','1','8');
			$data['view'] 	   = $d->in('是否显示','view','intval','','1','1');
			$data['is_page']   = $d->in('是否单页分类','is_page','intval','','1','1');
			$data['linkurl']   = $d->in('外部链接url','linkurl','string','','0','100');

			$result = $db->where(array('term_id'=>$id))->save($data);
			add_Log('修改了"'.$data['name'].'"的分类信息',$result,$db);//写入日志 
			if($result){
				$this->success('修改分类成功',U(MODULE_NAME.'/Article/type'));
			}else{
				$this->error('修改分类失败,请检查名称缩写是否重复');
			}
		}
    }

    //删除分类
    public function delete(){
    	$d      = A('CheckInput');
		$id     = $d->in('分类id','id','intval','','1','8');
		$terms  = M('terms');
		$result = $terms->delete($id);
		add_Log('删除了"分类id='.$id.'"的分类',$result,$terms);//写入日志 
		if($result){
			$this->success('删除分类成功',U(MODULE_NAME.'/Article/type'));
		}else{
			$this->error('删除分类失败');
		}
    }
}