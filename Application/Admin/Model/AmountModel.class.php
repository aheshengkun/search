<?php
namespace Admin\Model;
use Think\Model;
class AmountModel extends Model{
    function AccountList($map, $limit){
        $data = $this->Table("{$this->tablePrefix}amount as A")->join("left join {$this->tablePrefix}customer as U on A.customer_id=U.id")->field('U.name,U.nicename,A.id,A.customer_id,A.total,A.use_money,A.no_use_money,A.collection')->where($map)->order("A.id desc")->limit($limit)->select();
        return $data;
    }
}
?>