<?php
namespace app\common\model;
use think\Model;
use app\common\model\OrdersGoods;
use app\common\model\Distribution;
use app\common\model\GoodsDistribution;
use app\common\model\WithdrawLog;
use app\common\model\GoodsSpu;
use think\Db;

class CommissionDetail extends Model
{
    protected $table = 'shop_commission_details';
    protected $pk = 'id';
    protected $autoWriteTimestamp = 'datetime';

    /**
     * 函数名：getCommissionList
     * 函数说明：按照订单统计某个分销商的佣金明细
     * @param int $distributor_id 分销商id
     * @return mixed
     * author：liu_sd
     */
    public function getCommissionList($distributor_id,$status=null)
    {
        $orders_goods = new OrdersGoods();
        $where = array();
        $where['scd.distributor_id'] = $distributor_id;
        switch($status) {
            case 1;
                $where['o.status'] = 1;
                break;
            case 2;
                $where['o.status'] = array('in',[2,4]);
                break;
            case 3;
                $where['o.status'] = 3;
                break;
            case 5;
                $where['o.status'] = 5;
                break;
            default:
                $where['o.status'] = array('in',[0,1,2,3,4,5,6,7,8]);
                break;
        }

        $com_list = $this->alias('scd')
            ->join('orders o','scd.order_id=o.order_id')
            ->where($where)
            ->where("delete_time",NULL)
            ->field('scd.goods_spu_id,scd.money,scd.create_time,scd.level as commission_level,o.order_sn,o.user_id,o.order_id,o.total_amount,o.true_amount,o.status')
            ->order('scd.order_id','desc')
            ->select();
     //    var_dump($com_list);exit;
        $com_data = array();
        $commission_count = 0;
        if (!empty($com_list) && is_array($com_list))
        {
            foreach($com_list as $key => $value)
            {
                $order_id = $value['order_id'];
                $money = $value['money'];
                $commission_count += $money;
                if (!isset($com_data[$order_id]))
                {
                    $com_data[$order_id] = array();
                }
                if (isset($com_data[$order_id]['money']))
                {
                    $com_data[$order_id]['money'] += $money;
                }
                else
                {
                    $com_data[$order_id]['money'] = $money;
                }
                $agent_name=db('user')->field('nickname,avatar')->where('user_id',$value['user_id'])->find();
                $com_data[$order_id]['agent_name'] = $agent_name['nickname'];
                $com_data[$order_id]['avatar'] = $agent_name['avatar'];
                $com_data[$order_id]['order_id'] = $order_id;
              //  $com_data[$order_id]['create_time'] = $value['create_time'];
                $com_data[$order_id]['create_time'] = date('Y-m-d', strtotime($value['create_time']));
                $com_data[$order_id]['order_sn'] = $value['order_sn'];
                $com_data[$order_id]['status'] = $value['status'];
                $goods = $orders_goods
                    ->alias('og')
                    ->join('goods_spu gs','gs.goods_spu_id=og.goods_spu_id','left')
                    ->join('goods g','g.goods_id=gs.goods_id')
                    ->where(array('og.order_id'=>$order_id,'og.goods_spu_id'=>$value['goods_spu_id']))
                    ->field('og.*,g.name,gs.original_price,gs.present_price')
                    ->find();
                if(isset($goods->url))
                {
                    $goods->picture = $goods->url;
                }
                if ($goods) $goods = $goods->toArray();
                if(is_array($goods) && !empty($goods)){
                    $goods['money'] = $money;
                }
                $com_data[$order_id]['goods'][] = !empty($goods)?$goods:"";
                if (!empty($com_data[$order_id]['goods']))
                {
                    $com_data[$order_id]['goods_count'] = array_sum(array_column($com_data[$order_id]['goods'],'number'));
                }
                else
                {
                    $com_data[$order_id]['goods_count'] = 0;
                }
            }

        }
        $com_data['commission_count'] = $commission_count;
        return $com_data;
//        $com_list = $this->alias('scd')
//            ->join('orders o','scd.order_id=o.order_id')
//            ->where($where)
//            ->field('sum(scd.money) as commission,max(scd.create_time) as create_time,max(scd.level) as commission_level,o.order_sn,o.order_id,o.total_amount,o.true_amount,o.status')
//            ->group('scd.order_id')
////            ->select();
//        foreach ($com_list as $key=>&$value)
//        {
//            $value = $value->toArray();
//            $value['goods_list'] = $orders_goods
//                ->alias('og')
//                ->join('goods_spu gs','gs.goods_spu_id=og.goods_spu_id','left')
//                ->join('goods g','g.goods_id=gs.goods_id')
//                ->where(array('og.order_id'=>$value['order_id']))->field('og.*,g.name,g.picture,gs.original_price,gs.present_price')->select();
//        }
//        unset($value);
//        return $com_list;
    }


    /**
     * 函数名：countCommission
     * 函数说明：统计分销订单数量
     * @param $distributor_id
     * @return int|string
     * author：liu_sd
     */
    public function countCommission($distributor_id)
    {
        $orders_goods = new OrdersGoods();
        $where = array();
        $where['scd.distributor_id'] = $distributor_id;
        $where['scd.is_deleted'] = 'N';
        $where['o.status'] = array('in',[0,1,2,3,4,5,6,7,8]);
        $where['o.delete_time'] = null;
        $com_count= $this->alias('scd')
            ->join('orders o','scd.order_id=o.order_id')
            ->where($where)
            ->field('sum(scd.money) as commission,max(scd.create_time) as create_time,max(scd.level) as commission_level,o.order_sn,o.order_id,o.total_amount,o.true_amount,o.status')
            ->group('scd.order_id')
            ->count();
        return $com_count;
    }



    /**
     * 函数名：setCommission
     * 函数说明：设置三级分佣明细
     * @param int $order_id 订单id
     * @param int $distributor_id 分销商id
     * @return:
     * author：liu_sd
     */
    public function setCommission($order_id,$distributor_id)
    {
        if (!$distributor_id)
        {
            return true;
        }
        $order_goods = new OrdersGoods();
        $distribution = new Distribution();
        $order_goods_list = $order_goods->where(array('order_id'=>$order_id))->select();
        if (is_array($order_goods_list) && !empty($order_goods_list))
        {
            $superior = $distribution->getThreeSuperior($distributor_id);//两个上级分销商信息以及他自己的分销商信息
//            Db::startTrans();//事务开始
//            try {
            foreach ($order_goods_list as $value)
            {
                $goods_spu_id = $value->goods_spu_id;
                $goods_num = $value->number;
                if (!empty($superior[0])) $this->setOneCommission($order_id,$goods_spu_id,$goods_num,$distributor_id,1);
                if (!empty($superior[1])) $this->setOneCommission($order_id,$goods_spu_id,$goods_num,$superior[1]['distributor_id'],2);
                if (!empty($superior[2])) $this->setOneCommission($order_id,$goods_spu_id,$goods_num,$superior[2]['distributor_id'],3);
            }
//                // 提交事务
//                Db::commit();
//                return true;
//            } catch (\Exception $e) {
//                // 回滚事务
//                Db::rollback();
//                return false;
//            }

        }
    }


    /**
     * 函数名：setOneCommission
     * 函数说明：设置某一级分佣
     * @param int $order_id 订单id
     * @param int $goods_id 商品id
     * @param int $goods_num 商品数量
     * @param int $distributor_id 分销商id
     * @param int $level 几级分销商
     * author：liu_sd
     */
    public function setOneCommission($order_id,$goods_spu_id,$goods_num,$distributor_id,$level)
    {
        $goods_distribution = new GoodsDistribution();
        $distribution = new Distribution();
        if (!($distribution->ditributorStatus($distributor_id)))
        {
            return false; //若成为分销商还在审核或未通过
        }
        $goods_spu = new GoodsSpu();
        $goods_id = $goods_spu->where(['goods_spu_id'=>$goods_spu_id])->value('goods_id');
        $distribution_data = $goods_distribution->where(array('goods_id'=>$goods_id))->find();
        if ($distribution_data)
        {
            switch($level){
                case 1:
                    $money = ($distribution_data->one_commission/100)*$distribution_data->commission_money*$goods_num;
                    break;
                case 2:
                    $money = ($distribution_data->two_commission/100)*$distribution_data->commission_money*$goods_num;
                    break;
                case 3:
                    $money = ($distribution_data->three_commission/100)*$distribution_data->commission_money*$goods_num;
                    break;
                default:
                    $money = 0;
                    break;
            }
            $data = array();
            $data['distributor_id'] = $distributor_id;
            $data['order_id'] = $order_id;
            $data['goods_spu_id'] = $goods_spu_id;
            $data['level'] = $level;
            $data['money'] = $money;
            $data['create_time'] = date('Y-m-d H:i:s');
            $this->insert($data);
        }
    }


    /**
     * 函数名：getWithdrawCommission
     * 函数说明：获取某个分销商可提现的佣金订单
     * @param $distributor_id
     * author：liu_sd
     */
    public function getWithdrawCommission($distributor_id)
    {
        $now = time();
        $before_7 = $now - 7*60*60*12;
        $where = array();
        $where['scd.distributor_id'] = $distributor_id;
        $where['scd.status'] = 1;
        $where['scd.is_deleted'] = 'N';
        $where['o.status'] = 5;
        $where['o.update_time'] = ['<',$before_7];
        $where['o.delete_time'] = null;

        $money = $this->alias('scd')
            ->join('orders o','scd.order_id=o.order_id','left')
            ->where($where)
            ->field('sum(scd.money) as money,scd.order_id')
            ->group('scd.order_id')
            ->select();
        return $money;
    }

    /**
     * 函数名：getTotalCommission
     * 函数说明：获取某个分销商累计佣金
     * @param $distributor_id
     * author：liu_sd
     */
    public function getTotalCommission($distributor_id)
    {
        $total_commission = $this->where(['distributor_id'=>$distributor_id,'is_deleted'=>'N'])->sum('money');
        return $total_commission;
    }

    /**
     * 函数名：getCanWithdraw
     * 函数说明：获取某个分销商可提现佣金
     * @param $distributor_id
     * author：liu_sd
     */
    public function getCanWithdraw($distributor_id)
    {
        $now = time();
        $before_7 = $now - 7*60*60*12;
        $where = array();
        $where['scd.distributor_id'] = $distributor_id;
        $where['scd.status'] = 1;
        $where['scd.is_deleted'] = 'N';
        $where['o.status'] = 5;
        $where['o.update_time'] = ['<',$before_7];
        $where['o.delete_time'] = null;

        $money = $this->alias('scd')
            ->join('orders o','scd.order_id=o.order_id','left')
            ->where($where)
            ->sum('scd.money');
        return $money;
    }

    /**
     * 函数名：getApplyWithdraw
     * 函数说明：获取某个分销商已经申请提现佣金
     * @param $distributor_id
     * author：liu_sd
     */
    public function getApplyWithdraw($distributor_id)
    {
        $where = array();
        $where['scd.distributor_id'] = $distributor_id;
        $where['dwl.distributor_id'] = $distributor_id;
        $where['scd.is_deleted'] = 'N';
        $where['dwl.is_deleted'] = 'N';
        $where['scd.status'] = 2;
        $where['dwl.status'] = 1;
        $apply_withdraw = $this->alias('scd')
            ->join('distribution_withdraw_log dwl','dwl.order_id=scd.order_id','left')
            ->where($where)
            ->sum('scd.money');
        return $apply_withdraw;
    }

    /**
     * 函数名：getFreezeWithdraw
     * 函数说明：获取某个分销商冻结佣金
     * @param $distributor_id
     * author：liu_sd
     */
    public function getFreezeWithdraw($distributor_id)
    {
        $now = time();
        $before_7 = $now - 7*60*60*12;
        $where = array();
        $where['scd.distributor_id'] = $distributor_id;
        $where['scd.status'] = 1;
        $where['scd.is_deleted'] = 'N';
        $where['o.status'] = 5;
        $where['o.update_time'] = ['between',[$before_7,$now]];
        $where['o.delete_time'] = null;
        $money_1 = $this->alias('scd')
            ->join('orders o','scd.order_id=o.order_id','left')
            ->where($where)
            ->sum('scd.money');

        $where = array();
        $where['scd.distributor_id'] = $distributor_id;
        $where['scd.status'] = 1;
        $where['scd.is_deleted'] = 'N';
        $where['o.status'] = 6;
        $where['o.delete_time'] = null;
        $money_2 = $this->alias('scd')
            ->join('orders o','scd.order_id=o.order_id','left')
            ->where($where)
            ->sum('scd.money');
        $money = $money_1 + $money_2;
//
//        $where1 = array();
//        $where1['o.status'] = 5;
//        $where1['o.update_time'] = ['between',[$before_7,$now]];
//
//        $where_add = array();
//        $where_add['o.status'] = 6;
//        $where_add['_complex'] = $where1;
//        $where_add['_logic'] = 'or';
//
//        $where_base['_complex'] = $where_add;
//        $where_base['_logic'] = 'and';
//        $money = $this->alias('scd')
//            ->fetchSql()
//            ->join('orders o','scd.order_id=o.order_id','left')
//            ->where($where_base)
//            ->where('scd.status',0)
//            ->where('scd.is_deleted','N')
//            ->where(function ($query) { $query->where('o.status',6);})
//            ->whereor(function ($query) {$query->where('o.status',5)->where('o.update_time','between',"''$before_7,$now''");})
//            ->sum('scd.money');
        return $money;
    }

    /**
     * 函数名：getInvalidCommission
     * 函数说明：获取某个分销商无效佣金
     * @param $distributor_id
     * author：liu_sd
     */
    public function getInvalidCommission($distributor_id)
    {
        $invalid_commission = $this
            ->alias('scd')
            ->join('distribution_withdraw_log dwl','dwl.order_id=scd.order_id and scd.distributor_id=dwl.distributor_id','left')
            ->where('scd.distributor_id',$distributor_id)
            ->where('scd.is_deleted','N')
            ->where(function($query) {
                $query->where('scd.status',3)->whereor(function($query) {
                    $query->where('scd.status',2)->where('dwl.status',3);
                });
            })
            ->sum('money');
        return $invalid_commission;

    }


    /**
     * 函数名：getWaitPayment
     * 函数说明：获取某个分销商待打款佣金
     * @param $distributor_id
     * author：liu_sd
     */
    public function getWaitPayment($distributor_id)
    {
        $withdraw_log = new WithdrawLog();
        $where = array();
        $where['distributor_id'] = $distributor_id;
        $where['status'] = 2;
        $where['pay_status'] = 1;
        $where['is_deleted'] = 'N';
        $wait_payment = $withdraw_log->where($where)->sum('withdraw');
        return $wait_payment;
    }

    /**
     * 函数名：getTotalWithdraw
     * 函数说明：获取某个分销商的累计提现佣金
     * @param $distributor_id
     * author：liu_sd
     */
    public function getTotalWithdraw($distributor_id)
    {
        $withdraw_log = new WithdrawLog();
        $where = array();
        $where['distributor_id'] = $distributor_id;
        $where['status'] = 2;
        $where['pay_status'] = 3;
        $where['is_deleted'] = 'N';
        $wait_payment = $withdraw_log->where($where)->sum('withdraw');
        return $wait_payment;
    }


    /**
     * 函数名：getUnliquidatedCommission
     * 函数说明：获取未结算佣金
     * @param $distributor_id
     * @return float|int
     * author：liu_sd
     */
    public function getUnliquidatedCommission($distributor_id)
    {
//        $now = time();
//        $before_7 = $now - 7*60*60*12;
        $where = array();
        $where['scd.distributor_id'] = $distributor_id;
        $where['scd.status'] = 1;
        $where['scd.is_deleted'] = 'N';
        $where['o.status'] = array('in',[1,2,3,4,8]);
        $where['o.delete_time'] = null;
//        $where['o.update_time'] = ['<',$before_7];

        $money = $this->alias('scd')
            ->join('orders o','scd.order_id=o.order_id','left')
            ->where($where)
            ->sum('scd.money');
        return $money;
    }

    /**
     * 函数名：setInvalidCommission
     * 函数说明：把某些订单的佣金设为无效
     * @param int $order_id 订单id
     * author：liu_sd
     */
    public function setInvalidCommission($order_id)
    {
        $update = array();
        $update['status'] = 3;
        $update['update_time'] = date('Y-m-d H:i:s');
//        Db::startTrans();//事务开始
//        try {
//            $this->save($update,['order_id'=>$order_id]);
        $data = $this->where(['order_id'=>$order_id])->find();
        if ($data)
        {
            $res = $this->where(['order_id'=>$order_id])->update($update);
            if ($res===false)
            {
                return false;
            }
            else
            {
                return true;
            }
        }
        return true;
//            // 提交事务
//            Db::commit();
//            return true;
//        } catch (\Exception $e) {
//            // 回滚事务
//            Db::rollback();
//            return false;
//        }
    }


    /**
     * 函数名：delCommissionDetail
     * 函数说明：把某笔订单的分佣明细删除
     * @param $order_id
     * @return bool
     * author：liu_sd
     */
    public function delCommissionDetail($order_id)
    {
        $where = array();
        $where['order_id'] = $order_id;
        $data = $this->where($where)->find();
        if($data)
        {
            $res = $this->where($where)->update(['is_deleted'=>'Y']);
            if($res===false)
            {
                return false;
            }
            else
            {
                return true;
            }
        }
        return true;

    }

}