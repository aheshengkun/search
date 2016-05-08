<?php
namespace Admin\Controller;
use Think\Controller;
/**
 * 文章内容管理控制器
 */
class SearchArticleController extends CommonController{
	//文章列表
	public function index(){
		$d = A('CheckInput');
		$condition = array();
		$condition['title']  = $d->in('文章标题','title','cnennumstr','','0','100');
		//组合条件
		$wherelist = array();
		if(!empty($condition['title'])){
			$wherelist[] = "title = '{$condition['title']}'";
		}
		//组装存在的查询条件
		if(count($wherelist) > 0){
			$where = implode(' AND ' , $wherelist); 
		}
		$where = isset($where) ? $where : '';
		//使用关联模型，以便获取文章所属的分类名称
		$db = M('article');
		$field = array('id','title','url','date_added');		//要查询的字段
		
		$count  = $db->where($where)->count();
		$Page   = new \Think\Page($count,20);		//分页数

		//分页跳转的时候保证查询条件
		foreach($condition as $key=>$val) {
			$Page->parameter[$key] = $val;
		}
		$show   = $Page->show();

		$list 	= $db->where($where)->field($field)->order('id ASC')->limit($Page->firstRow.','.$Page->listRows)->select();

		$this->assign('list',$list);					// 赋值数据集
		$this->assign('page',$show);					// 赋值分页输出
		$this->assign('condition',$condition);			//保持查询条件不消失
		$this->display();
	}

	//添加文章/显示文章列表
	public function addArticle(){
		if(!IS_POST){
			$this->display();
		}else{
			$d = A('CheckInput');
 
			$data['title'] 	= $d->in('文章标题','title','string','','1','200');
			$data['url'] 		= $d->in('外部链接url','url','string','','0','500');
			//$data['content'] 	= $d->in('文章内容','content','string','','0','2000');
			$data['content'] = $_POST['content'];
			
			if(empty($_POST['dotime1'])){
				$data['date_added'] 		= date('Y-m-d H:i:s');	//发表时间
			}else{
				$data['date_added'] 		= $_POST['dotime1'];//发表时间
			}
			$result = M('article')->add($data);
			 
			if($result){
				$this->success('添加文章成功',U(MODULE_NAME.'/SearchArticle/addArticle'));
			}else{
				$this->error('添加文章失败');
			}
		}
	}

	//获取分类等级数据
	private function getCate($info = array()) {
        $cat = new \Org\Util\Category('Type', array('term_id', 'parent_id', 'name', 'fullname'));
        $cat->order('sort');					//排序显示
        $list = $cat->getList();               //获取分类结构

        foreach ($list as $k => $v) {
        	//外部链接类型的分类不可在其下面添加文章 
        	if($v['is_page'] != '2'){
        		$info['pidOption'].='<option value="'.$v['term_id'].'" class="'.$v['is_page'].'">' . $v['fullname'] . '</option>';
        	}
        }
        return $info;
    }

    //文章排序或批量删除
    public function articleHandle(){
		if(!IS_POST){
			$this->error('页面不存在！');
		}
		$db = M('posts');
		$str_sql = '';
		if(isset($_POST['ids']) && $_POST['delChecks'] == '删除所选'){
			foreach ($_POST['ids'] as $id) {
				$res = $db->field('face_img')->find($id);//获取文章封面图片地址
				$sqlok = $db->where(array('id'=>$id))->delete();
				if($sqlok !== false){
					//删除已经上传存储在硬盘中的文章封面图片
					if(file_exists($res['face_img'])){
						@unlink($res['face_img']);
					}
				}
				$str_sql = $str_sql.$db->_sql().';';//获取操作语句，仅供日志记录使用
			}
			add_Log('批量删除了文章',1,$str_sql);//写入日志 
			$this->success('批量删除成功！', U(MODULE_NAME.'/ArticleContent/index'));
		}else{
			foreach($_POST as $id=>$sort){
				$sort = intval($sort);
				$db->where(array('id'=>$id))->setField('menu_order',$sort);
			}
			add_Log('修改了文章排序',1,'未记录sql');//写入日志 
			$this->redirect(MODULE_NAME.'/ArticleContent/index');
		}
	}

	//修改文章视图/表单数据处理
	public function updateArticle(){
		$d  = A('CheckInput');
		$id = $d->in('文章id','id','intval','','1','8');
		if(!IS_POST){
			$article = M('article')->where(array('id'=>$id))->find();
			$this->assign("article", $article);
			$this->display('updateArticle');
		}else{
			$data['id'] = $id;
			$data['title'] 	= $d->in('文章标题','title','string','','1','200');
			$data['url'] 		= $d->in('外部链接url','url','string','','0','500');
			//$data['content'] 	= $d->in('文章内容','content','string','','0','2000');
			$data['content'] = $_POST['content'];
			if(!empty($_POST['dotime1'])){
				$data['date_added'] 		= $_POST['dotime1'];	//发表时间
			}
			
			$posts = M('article');
			$result = $posts->save($data);
			if($result){	 
				$this->success('修改文章成功',U(MODULE_NAME.'/SearchArticle/index'));
			}else{
				$this->error('修改文章失败');
			}
		}
	}

	//删除文章
    public function delArticle(){
    	$d  	= A('CheckInput');
		$id     = $d->in('文章id','id','intval','','1','8');
		$posts  = M('article');
		$result = $posts->delete($id);
		if($result){
			$this->success('删除文章成功',U(MODULE_NAME.'/SearchArticle/index'));
		}else{
			$this->error('删除文章失败');
		}
    }

}