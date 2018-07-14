<?php
namespace app\common\model;
use think\Model;
use app\common\model\OrdersGoods;
use app\common\model\Distribution;
use app\common\model\GoodsDistribution;
use think\Db;

class CreditLog extends Model
{
    protected $table = 'credit_log';
    protected $pk = 'credit_log_id';
    protected $autoWriteTimestamp = 'datetime';

    /**
     * 函数名：addCreditLog
     * 函数说明：
     * @param int $user_id 用户id
     * @param int $create_by 创建人id
     * @param int $credit_num 积分变化数目
     * @param int $change_type 积分变化类型：1|增加,2|减少
     * @param string $remark 备注
     * @return bool
     * author：liu_sd
     */
    public function addCreditLog($user_id,$order_id,$create_by,$credit_num,$change_type,$remark='')
    {
        $data = array();
        $data['user_id'] = $user_id;
        $data['credit'] = $credit_num;
        $data['change_type'] = $change_type;
        $data['remark'] = $remark;
        $data['create_by'] = $create_by;
        $data['order_id'] = $order_id;
        $rs = $this->save($data);
        if ($rs===false)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

}