<?php
namespace Admin\Model;
use Think\Model;
class NodeModel extends Model{
	protected $tableName = 'node'; 
	//添加节点
	public function addNode($data){
		return $this->add($data);
	}
	//编辑节点
	public function editNode($data){
		return $this->save($data);
	}
	//删除节点
	public function delNode($nid){
		return $this->where(array('id'=>$nid))->delete();
	}
	//验证节点是否在用
	public function checkNode($nid){
		return $this->table('saoba_access')->where(array('node_id'=>$nid))->count();
	}
	//验证节点是否有子节点
	public function checkChildNode($nid){
		return $this->where(array('pid'=>$nid))->count();
	}
	//获取节点列表
	public function getNodeAll(){
		$fiend = array(
			'id',
			'name',
			'title',
			'pid'
		);
		return $this->field($fiend)->order('sort')->select();
	}
    public function nodeList() {
        $cat = new \Org\Util\Category('Node', array('id', 'pid', 'title', 'fullname'));
        $temp = $cat->getList();               //获取分类结构
        $level = array("1" => "网站应用(GROUP_NAME)", "2" => "控制器(MODEL_NAME)", "3" => "方法(ACTION_NAME)");
        foreach ($temp as $k => $v) {
            $temp[$k]['statusTxt'] = $v['status'] == 1 ? "启用" : "禁用";
            //$temp[$k]['chStatusTxt'] = $v['status'] == 0 ? "启用" : "禁用";
            $temp[$k]['level'] = $level[$v['level']];
            $list[$v['id']] = $temp[$k];
        }
        unset($temp);
        return $list;
    }
}