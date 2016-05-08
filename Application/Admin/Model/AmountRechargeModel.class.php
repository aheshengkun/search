<?php
namespace Admin\Model;
use Think\Model;
class AmountRechargeModel extends Model{
    protected $tableName='amount_recharge';

    public function AccountList($map, $limit){
        $data = $this->Table("{$this->tablePrefix}amount_recharge as AR")->join("left join {$this->tablePrefix}customer as U on AR.customer_id=U.id")->join("left join {$this->tablePrefix}payment as P on AR.payment=P.id")->field('AR.money-AR.fee as total,AR.verify_remark as remark,U.name,P.name as payment_name,AR.id,AR.trade_no,AR.type,AR.payment,AR.money,AR.fee,AR.addtime,AR.verify_time,AR.status')->where($map)->order("AR.id desc")->limit($limit)->select();
        return $data;
    }

    public function ListCount($map){
        $data = $this->Table("{$this->tablePrefix}$this->tableName as AR")->join("left join {$this->tablePrefix}customer as U on AR.customer_id=U.id")->join("left join {$this->tablePrefix}payment as P on AR.payment=P.id")->where($map)->count();
        return $data;
    }
}
?>