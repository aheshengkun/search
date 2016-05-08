<?php
namespace Admin\Model;
use Think\Model;
class AgentcodeModel extends Model{
	protected $tableName = "agentcode";
	//获取所有二维码
	public function getCodeAll(){
		//取删除状态为未删除的数据
		$fields = array(
			'saoba_agentcode.cid',
			'codetitle',
			'status',
			'cname',
			'lname',
			'agentname',
			'qrcodetype',
			'scanningnum',
			'add_time',
			'add_username',
			'codekey'
		);
		return $this
		->field($fields)
		//->join('think_userinfo ON think_user.uid = think_userinfo.user_id')
		->join('saoba_qrcode_type ON saoba_agentcode.tid = saoba_qrcode_type.cid')
		->join('saoba_city ON saoba_agentcode.regionid = saoba_city.lid')
		->join('saoba_agent ON saoba_agentcode.aid = saoba_agent.uid')
		->join('saoba_scanninginfo ON saoba_agentcode.cid = saoba_scanninginfo.code_id')
		->where(array('code_del'=>'0'))->select();//取删除状态为未删除的数据
	}
	
	public function getCodeAlls(){
		//取删除状态为已删除的数据
		$fields = array(
			'saoba_agentcode.cid',
			'codetitle',
			'status',
			'cname',
			'lname',
			'agentname',
			'qrcodetype',
			'scanningnum',
			'add_time',
			'add_username',
			'codekey'
		);
		return $this
		->field($fields)
		//->join('think_userinfo ON think_user.uid = think_userinfo.user_id')
		->join('saoba_qrcode_type ON saoba_agentcode.tid = saoba_qrcode_type.cid')
		->join('saoba_city ON saoba_agentcode.regionid = saoba_city.lid')
		->join('saoba_agent ON saoba_agentcode.aid = saoba_agent.uid')
		->join('saoba_scanninginfo ON saoba_agentcode.cid = saoba_scanninginfo.code_id')
		->where(array('code_del'=>'1'))->select();//取删除状态已删除的数据
	}

public function getCodeOnline(){
		//取删除状态为已删除的数据
		$fields = array(
			'saoba_agentcode.cid',
			'codetitle',
			'status',
			'cname',
			'lname',
			'agentname',
			'qrcodetype',
			'scanningnum',
			'add_time',
			'add_username',
			'codekey',
			'online'
		);
		return $this
		->field($fields)
		->join('saoba_qrcode_type ON saoba_agentcode.tid = saoba_qrcode_type.cid')
		->join('saoba_city ON saoba_agentcode.regionid = saoba_city.lid')
		->join('saoba_agent ON saoba_agentcode.aid = saoba_agent.uid')
		->join('saoba_scanninginfo ON saoba_agentcode.cid = saoba_scanninginfo.code_id')
		->where(array('code_del'=>'0'))->select();//取删除状态未删除的数据，并且提交了上线申请
	}
	//添加二维码
	public function addCode($data){
		return $this->add($data);
	}
	//编辑二维码
	public function editCode($data){
		return $this->save($data);
	}
	//删除二维码
	public function delCode($nid){
		return $this->where(array('id'=>$nid))->delete();
	}
	
	
	
}