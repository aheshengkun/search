<?php
namespace Admin\Model;
use Think\Model;
class AmountLogModel extends Model{
    protected $tableName='amount_log';
    const ERROR = '操作有误,请不要乱操作';
    function LogList($map, $limit){
        $data = $this->Table("{$this->tablePrefix}amount_log as AL")->join("{$this->tablePrefix}customer as U ON AL.customer_id = U.id")->join("left join {$this->tablePrefix}customer as TU ON AL.to_user = TU.id")->field('U.name,TU.name as to_username,AL.id,AL.customer_id,AL.type,AL.total,AL.money,AL.use_money,AL.no_use_money,AL.collection,AL.to_user,AL.addtime,AL.remark')->where($map)->order("AL.addtime desc,AL.id desc")->limit($limit)->select();
        return $data;
    }
    
    function LogListCount($map){
        $data = $this->Table("{$this->tablePrefix}amount_log as AL")->join("left join {$this->tablePrefix}customer as U ON AL.customer_id = U.id")->where($map)->count();
        return $data;
    }
}
?>