<?php
namespace Admin\Controller;
use Think\Controller;
/**
 * 文章内容管理控制器
 */
class ArticleContentController extends CommonController{
	//文章列表
	public function index(){
		$d = A('CheckInput');
		$condition = array();
		$condition['post_title']  = $d->in('文章标题','post_title','cnennumstr','','0','100');
		$condition['post_tid']    = $d->in('所属分类','post_tid','intval','0','0','100');
		$condition['post_type']   = $d->in('文章类型','post_type','cnennumstr','','0','18');
		$condition['post_status'] = $d->in('文章状态','post_status','cnennumstr','','0','18');//发表或草稿
		//组合条件
		 $wherelist = array();
		if(!empty($condition['post_title'])){
			$wherelist[] = "post_title = '{$condition['post_title']}'";
		}
		if(($condition['post_tid'])!='0'){
		    $wherelist[] = "post_tid = '{$condition['post_tid']}'";
	    }
	    if(!empty($condition['post_type'])){
			$wherelist[] = "post_type = '{$condition['post_type']}'";
	   	}
	   	if(!empty($condition['post_status'])){
			$wherelist[] = "post_status = '{$condition['post_status']}'";
	   	}
		//组装存在的查询条件
		if(count($wherelist) > 0){
			$where = implode(' AND ' , $wherelist); 
		}
		$where = isset($where) ? $where : '';
		//使用关联模型，以便获取文章所属的分类名称
		$db = D('ArticleRelation');
		$field = array('id','post_author','menu_order','post_author','post_title','post_type','post_status','comment_status','post_date','comment_count','post_tid','top');		//要查询的字段
		
		$count  = $db->where($where)->count();
		$Page   = new \Think\Page($count,20);		//分页数

		//分页跳转的时候保证查询条件
		foreach($condition as $key=>$val) {
			$Page->parameter[$key] = $val;
		}
		$show   = $Page->show();

		$list 	= $db->where($where)->field($field)->order('id ASC')->relation(true)->limit($Page->firstRow.','.$Page->listRows)->order('top DESC,id DESC')->select();
		$this->assign("type", $this->getCate());		//文章分类下拉框
		$this->assign('list',$list);					// 赋值数据集
		$this->assign('page',$show);					// 赋值分页输出
		$this->assign('condition',$condition);			//保持查询条件不消失
		$this->display();
	}

	//添加文章/显示文章列表
	public function addArticle(){
		if(!IS_POST){
			$this->assign("type", $this->getCate());		//文章分类下拉框
			$this->display();
		}else{
			$d = A('CheckInput');
			$data['post_tid'] 		= $d->in('文章所属分类id','post_tid','intval','','1','8');
			$data['post_type']		= $d->in('文章类型','post_type','enstr','','1','4');
			$data['face_img']		= $d->in('文章封面图片','file.face_img','uploadFile','','0','0');//新增的上传文章封面图片
			$data['post_title'] 	= $d->in('文章标题','post_title','string','','1','100');
			$data['post_name'] 		= $d->in('文章缩略名','post_name','string','','0','100');
			$data['post_excerpt'] 	= $d->in('文章摘要','post_excerpt','string','','0','250');
			$data['post_content'] 	= $d->in('文章内容','post_content','htcontent','','1','0');
			$data['post_status']    = $d->in('文章发表状态','post_status','enstr','','1','10');
			if(empty($_POST['dotime1'])){
				$data['post_date'] 		= NOW_TIME;	//发表时间
			}else{
				$data['post_date'] 		= strtotime($_POST['dotime1']);//发表时间
			}
			$data['post_author'] 	= session('user_id');	//对应作者id
			//新添加的文章置顶属性 2015-6-16
			$data['top'] = $d->in('是否置顶','top','string','','0','1');

			$result = M('posts')->add($data);
			add_Log('添加了"'.$data['post_title'].'"文章',$result,M('posts'));//写入日志 
			if($result){
				$this->success('添加文章成功',U(MODULE_NAME.'/ArticleContent/addArticle'));
			}else{
				//删除已经上传存储在硬盘中的文章封面图片
				if(file_exists($data['face_img'])){
					@unlink($data['face_img']);
				}
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
			$this->assign("type", $this->getCate());		//文章所属分类下拉框
			$article = M('posts')->where(array('id'=>$id))->find();
			$this->assign("article", $article);
			$this->display('updateArticle');
		}else{
			$data['id'] = $id;
			$data['post_tid'] = $d->in('文章所属分类id','post_tid','intval','','1','8');
			$data['post_type'] = $d->in('文章类型','post_type','enstr','','1','4');
			$data['post_title'] = $d->in('文章标题','post_title','string','','1','100');
			$data['post_name'] = $d->in('文章缩略名','post_name','string','','0','100');
			$data['post_excerpt'] = $d->in('文章摘要','post_excerpt','string','','0','350');
			$data['post_content'] = $d->in('文章内容','post_content','htcontent','','1','0');
			$data['post_status']    = $d->in('文章发表状态','post_status','enstr','','1','10');
			if(!empty($_POST['dotime1'])){
				$data['post_date'] 		= strtotime($_POST['dotime1']);//发表时间
			}
			$data['post_modified'] = NOW_TIME;	//修改时间
			$data['post_author'] = session('user_id');	//对应作者id
			//新添加的文章置顶属性 2015-6-16
			$data['top'] = $d->in('是否置顶','top','string','','0','1');

			$posts = M('posts');
			//如果选择上传图片文件
			if(!empty($_FILES['face_img']['name'])){
				$res = $posts->field('face_img')->where(array('id'=>$id))->find();
				$data['face_img'] = $d->in('文章封面图片','file.face_img','uploadFile','','0','0');//新增的上传文章封面图片  
			}

			$result = $posts->save($data);

			add_Log('修改了文章"'.$data['post_title'].'"的信息',$result,M('posts'));//写入日志 
			if($result){
				//删除已经上传存储在硬盘中的文章封面图片
				if(file_exists($res['face_img'])){
					@unlink($res['face_img']);
				}
				$this->success('修改文章成功',U(MODULE_NAME.'/ArticleContent/index'));
			}else{
				$this->error('修改文章失败');
			}
		}
	}

	//删除文章
    public function delArticle(){
    	$d  	= A('CheckInput');
		$id     = $d->in('文章id','id','intval','','1','8');
		$posts  = M('posts');
		$res    = $posts->field('face_img')->find($id);//获取封面图片地址
		$result = $posts->delete($id);
		add_Log('删除"id='.$id.'"的文章',$result,$posts);//写入日志 
		if($result){
			//删除已经上传存储在硬盘中的文章封面图片
			if(file_exists($res['face_img'])){
				@unlink($res['face_img']);
			}
			$this->success('删除文章成功',U(MODULE_NAME.'/ArticleContent/index'));
		}else{
			$this->error('删除文章失败');
		}
    }

    /**
	 * 编辑器使用框架文件上传类，上传图片
	 */
	public function upload(){
		$config = array(
	        'mimes'         =>  array(), //允许上传的文件MiMe类型
	        'maxSize'       =>  3145728, //上传的文件大小限制 (0-不做限制)
	        'exts'          =>  array('jpg','jpeg','png','gif'), //允许上传的文件后缀
	        'autoSub'       =>  true, //自动子目录保存文件
	        'subName'       =>  array('date', 'Y-m'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
	        'rootPath'      =>  './uploads/images/', //保存根路径
	        'savePath'      =>  '', //保存路径
	        'saveName'      =>  array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
	        'saveExt'       =>  '', //文件保存后缀，空则使用原后缀
	        'replace'       =>  false, //存在同名是否覆盖
	        'hash'          =>  true, //是否生成hash编码
	        'callback'      =>  false, //检测文件是否存在回调，如果存在返回文件信息数组
	        'driver'        =>  '', // 文件上传驱动
	        'driverConfig'  =>  array(), // 上传驱动配置
	    );
		
		$upload = new \Think\Upload($config);// 实例化上传类
		
		//取得最后一次错误信息
        $uploaderror = $upload->getError();
		//如果上传成功
		if(!!$info = $upload->upload()){
			$image = new \Think\Image();
			// //打开图片
			// $image->open('./uploads/images/'.$info['imgFile']['savepath'].$info['imgFile']['savename']);
			// // 按照原图的比例生成一个最大为150*150的缩略图并保存前缀为thumb的新图
			// $image->thumb(150, 150)->save('./uploads/images/'.$info['imgFile']['savepath'].'thumb_'.$info['imgFile']['savename']);
			// $image->open('/.uploads/'.$info['imgFile']['savepath'].$info['imgFile']['savename']);
			// //将图片裁剪为440x440并保存为corp.jpg
			// $image->crop(440, 440)->save('./uploads/'.$info['imgFile']['savepath'].'crop_'.$info['imgFile']['savename']);
			// // 给裁剪后的图片添加图片水印（水印文件位于./logo.png），位置为右下角，保存为water.gif
			// $image->water('./Public/Data/logo.png')->save('./uploads/images/'.$info['imgFile']['savepath'].$info['imgFile']['savename']);

			//判断编辑器上传图片时是否选择缩放图片
			if($_POST['wid'] != '0'){
				$image->open('./uploads/images/'.$info['imgFile']['savepath'].$info['imgFile']['savename']);
	            $width  = $image->width(); // 返回图片的宽度
	            $height = $image->height(); // 返回图片的高度
	            if($width > $_POST['wid']){//上传的图片宽度大于要限制的宽度才缩放
		            $bili   = $_POST['wid']/$width; //获取宽的倍数
		            $new_height = $height*$bili;
		            $image->thumb($_POST['wid'], $new_height,\Think\Image::IMAGE_THUMB_FIXED)->save('./uploads/images/'.$info['imgFile']['savepath'].$info['imgFile']['savename']);
		        }
	        }
			//判断编辑器上传图片是否选择添加水印，给原图添加水印并保存为原名（需要重新打开原图）
			if($_POST['iswater'] == '1'){
				$image->open('./uploads/images/'.$info['imgFile']['savepath'].$info['imgFile']['savename'])->water('./Public/Data/logo.png')->save('./uploads/images/'.$info['imgFile']['savepath'].$info['imgFile']['savename']);
			}
			$data = array('error' => 0, 'url' => __ROOT__.'/uploads/images/'.$info['imgFile']['savepath'].$info['imgFile']['savename']);
			exit(json_encode($data));
		}else{
			$data = array('error' => 1, 'message' => $uploaderror);
			exit(json_encode($data));
		}
	}
}